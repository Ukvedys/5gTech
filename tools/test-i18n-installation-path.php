<?php
/** Read-only URL regression checks; no database values are changed. */
$_SERVER['HTTP_HOST'] = '5gtech.test';
$_SERVER['REQUEST_URI'] = '/';
require dirname(__DIR__) . '/wordpress/wp-load.php';
$checks = 0;
$errors = [];
foreach (['', '/new-site', '/preview/site'] as $base) {
    $home = 'https://5gtech.test' . $base;
    $override = static fn() => $home;
    add_filter('pre_option_home', $override);
    foreach (['lt' => '/paslaugos/', 'en' => '/en/services/', 'de' => '/de/leistungen/'] as $language => $expected) {
        foreach (['/paslaugos/', '/en/services/', '/de/leistungen/'] as $path) {
            foreach ([$home . $path, $base . $path] as $input) {
                $absolute = str_starts_with($input, 'https://');
                $want = ($absolute ? 'https://5gtech.test' : '') . $base . $expected . '?x=1#details';
                $got = g5tech_i18n_localize_url($input . '?x=1#details', $language);
                $checks++;
                if ($got !== $want) $errors[] = "$input ($language): $got != $want";
            }
        }
    }
    foreach (['#contact', 'mailto:info@5gtech.lt', 'tel:+37068777155', 'https://example.org/services/', $home . '/wp-admin/admin-post.php', $home . '/wp-json/', $home . '/wp-content/file.css?ver=1', $home . '/document.pdf'] as $url) {
        $checks++;
        if (g5tech_i18n_localize_url($url, 'en') !== $url) $errors[] = "Technical/external URL changed: $url";
    }
    foreach (['/paslaugos/' => $base . '/paslaugos/', $base . '/kontaktai/' => $base . '/kontaktai/', '#team' => '#team', '//example.org/path/' => '//example.org/path/', 'https://example.org/' => 'https://example.org/'] as $url => $want) {
        $block = ['blockName' => 'core/navigation-link', 'attrs' => ['url' => $url]];
        $checks++;
        if (g5tech_navigation_installation_path($block)['attrs']['url'] !== $want) $errors[] = "Navigation URL: $url";
    }
    remove_filter('pre_option_home', $override);
}
echo ($errors ? 'FAIL' : 'PASS') . ": $checks checks; " . count($errors) . " failures\n";
foreach ($errors as $error) echo " - $error\n";
exit($errors ? 1 : 0);
