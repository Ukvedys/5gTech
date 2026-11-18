<?php
/** CLI-only: import the 17 approved legacy career listings into WP job fields.
 * php tools/migrate-legacy-jobs.php /path/to/old-careers.html [--apply]
 * Default: prepares deploy/content/legacy-jobs.json for review, without DB writes.
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$root = dirname(__DIR__);
$input = $argv[1] ?? '';
if (!is_file($input)) { throw new RuntimeException('Supply the downloaded legacy careers HTML.'); }
$_SERVER['HTTP_HOST'] = '5gtech.test';
$_SERVER['REQUEST_URI'] = '/';
require $root . '/wordpress/wp-load.php';
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML(file_get_contents($input));
$xpath = new DOMXPath($dom);
$clean = static function ($node) {
    $text = preg_replace('/\s+/u', ' ', trim($node->textContent));
    return strtr($text, ['sprendimams.Darbas' => 'sprendimams. Darbas', 'nuodojant' => 'naudojant', 'peleidimo' => 'paleidimo', 'komopensaciją' => 'kompensaciją', 'įgudžių' => 'įgūdžių']);
};
$slugs = ['telekomunikaciju-specialistas-pradinis-lietuva','telekomunikaciju-specialistas-savarankiskas-lietuva','telekomunikaciju-inzinierius-koordinuojantis-lietuva','telekomunikaciju-inzinierius-vadovaujantis-lietuva','telekomunikaciju-specialistas','telekomunikaciju-specialistas-savarankiskas-europa','telekomunikaciju-inzinierius-koordinuojantis-europa','telekomunikaciju-inzinierius-vadovaujantis-europa','mobiliojo-rysio-integracijos-inzinierius','saules-elektriniu-montuotojas-pradinis','saules-elektriniu-montuotojas-savarankiskas','saules-elektriniu-montuotojas-koordinuojantis','telekomunikaciju-projektu-vykdytojas','telekomunikaciju-projektu-vadovas','saules-elektriniu-projektu-vykdytojas','saules-elektriniu-projektu-vadovas','administracijos-ir-personalo-koordinatorius'];
$jobs = [];
for ($i=1; $i<=17; $i++) {
    $region = $xpath->query('//*[@id="elementor-tab-content-808'.$i.'"]')->item(0);
    $heading = $xpath->query('//*[@id="elementor-tab-title-808'.$i.'"]')->item(0);
    if (!$region || !$heading) { throw new RuntimeException('Missing source listing '.$i); }
    $originalTitle = $clean($heading);
    $title = trim(explode('|', $originalTitle)[0]);
    $title = trim(preg_replace('/\s+Vilnius, Lietuva$/u', '', $title));
    preg_match('/\((pradinis|savarankiškas|koordinuojantis|vadovaujantis) lygis\)/u', $originalTitle, $level);
    preg_match('/\|\s*([\d ][\d –+]+) EUR/u', $originalTitle, $salary);
    $sections = []; $section = 'INTRO';
    foreach ($xpath->query('.//h5|.//p|.//li', $region) as $node) {
        $text = $clean($node);
        if ($node->nodeName === 'h5') { $section = rtrim($text, " ?!: "); continue; }
        if ($text === '' || $xpath->query('.//a', $node)->length) { continue; }
        $sections[$section][$node->nodeName][] = $text;
    }
    $responsibilities=[]; $requirements=[]; $offer=[]; $intro=[];
    foreach ($sections as $label=>$parts) {
        $lines = $parts['li'] ?? [];
        if (in_array($label, ['KAS TAVĘS LAUKIA','KĄ VEIKSI','KĄ VEIKSI PAS MUS','PAGRINDINĖS ATSAKOMYBĖS'], true)) {
            $responsibilities = array_merge($responsibilities, $lines);
            $intro = array_merge($intro, $parts['p'] ?? []);
        } elseif (in_array($label, ['REIKALAVIMAI','KO MES IEŠKOME','KO TIKIMĖS','MUMS SVARBU, KAD TU'], true)) {
            $requirements = array_merge($requirements, $lines);
        } elseif ($label === 'PRIVALUMAI') {
            $requirements = array_merge($requirements, array_map(static fn($line) => 'Privalumas: '.$line, $lines));
        } elseif (in_array($label, ['KODĖL VERTA RINKTIS MUS','KĄ GAUSI','KĄ GAUSI MAINAIS'], true)) {
            $offer = array_merge($offer, $lines);
        }
    }
    $intro = array_merge($sections['INTRO']['p'] ?? [], $intro);
    $intro = array_map(static fn($line) => str_replace('kasdienį projekto eigą', 'kasdienę projekto eigą', preg_replace('/visoje Lietuvoje$/u', 'visoje Lietuvoje.', $line)), $intro);
    if ($i===17) {
        // Keep the role-specific introduction; omit duplicated general recruiting slogans.
        $intro = ['Ieškome komandos narės (-io), kuri (-is) padėtų užtikrinti administracinę tvarką, prisidėtų prie personalo atrankų ir palaikytų sklandų komandos darbą.'];
    }
    $meta = [
        'g5_job_group' => $i<=4 ? 'lithuania' : ($i<=12 ? 'europe' : 'office'),
        'g5_job_level' => isset($level[1]) ? mb_strtoupper(mb_substr($level[1],0,1)).mb_substr($level[1],1).' lygis' : '',
        'g5_job_location' => $i<=4 ? 'Lietuva' : ($i<=9 ? 'Vokietija, Švedija, Norvegija ir kt.' : ($i<=12 ? ($i===12 ? 'Europa' : 'Vokietija') : ($i===17 ? 'Vilnius, Meistrų 8A' : 'Vilnius · hibridinis darbas'))),
        'g5_job_salary' => isset($salary[1]) ? trim($salary[1]).' €' : '',
        'g5_job_rotation' => $i>=5 && $i<=12 ? '6–8 sav.' : '',
        // The legacy listing says B is an advantage for installers, not mandatory.
        'g5_job_driving' => $i===9 ? 'B' : '',
        'g5_job_summary' => implode(' ', $intro),
        'g5_job_responsibilities' => implode("\n", $responsibilities),
        'g5_job_requirements' => implode("\n", $requirements),
        'g5_job_offer' => implode("\n", $offer),
        'g5_job_expires' => '', 'g5_job_active' => '1',
    ];
    foreach (['g5_job_summary','g5_job_responsibilities','g5_job_requirements','g5_job_offer'] as $key) {
        if (!$meta[$key]) { throw new RuntimeException("Empty $key for source $i"); }
    }
    if ($i>=5 && $i<=12 && !$meta['g5_job_salary']) { throw new RuntimeException('Missing salary '.$i); }
    $jobs[] = ['source_id'=>$i, 'source_title'=>$originalTitle, 'slug'=>$slugs[$i-1], 'title'=>$title, 'menu_order'=>$i, 'meta'=>$meta];
}
$document = ['source'=>'https://5gtech.lt/karjera/', 'retrieved'=>'2026-09-08', 'language'=>'lt', 'jobs'=>$jobs];
file_put_contents($root.'/deploy/content/legacy-jobs.json', json_encode($document, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)."\n");
if (in_array('--apply', $argv, true)) {
    if (home_url() !== 'http://5gtech.test') { throw new RuntimeException('Only the local WordPress may be updated by this tool.'); }
    foreach ($jobs as $job) {
        $existing = get_page_by_path($job['slug'], OBJECT, 'g5_job');
        $id = wp_insert_post(wp_slash(['ID'=>$existing ? $existing->ID : 0, 'post_type'=>'g5_job','post_status'=>'publish','post_title'=>$job['title'],'post_name'=>$job['slug'],'menu_order'=>$job['menu_order']]), true);
        if (is_wp_error($id)) { throw new RuntimeException($id->get_error_message()); }
        foreach ($job['meta'] as $key=>$value) { update_post_meta($id, $key, wp_slash($value)); }
        if (function_exists('pll_set_post_language')) { pll_set_post_language($id, 'lt'); }
        echo "$id {$job['slug']}\n";
    }
}
echo 'Prepared '.count($jobs)." source-backed job listings.\n";
