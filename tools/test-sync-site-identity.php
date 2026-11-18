<?php
/** Pure reference tests; never loads WordPress or changes a database. */
require dirname(__DIR__) . '/deploy/sync-helpers.php';
$checks = 0;
$assert = static function ($condition) use (&$checks) {
	++$checks;
	if (!$condition) throw new RuntimeException('Site identity check failed: ' . $checks);
};
$mapped = g5sync_options(['site_logo'=>9, 'site_icon'=>10], [9=>701, 10=>702]);
$assert($mapped === ['site_logo'=>701, 'site_icon'=>702]);
$assert(g5sync_options(['site_logo'=>0, 'site_icon'=>0], []) === ['site_logo'=>0, 'site_icon'=>0]);
$assert(g5sync_options(['blogname'=>'5G TECH'], []) === ['blogname'=>'5G TECH']);
foreach (['site_logo', 'site_icon'] as $key) {
	$rejected = false;
	try { g5sync_options([$key=>99], [9=>701]); }
	catch (RuntimeException $error) { $rejected = true; }
	$assert($rejected);
}
echo "PASS: $checks site identity checks\n";
