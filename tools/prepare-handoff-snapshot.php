<?php
/** One-time schema upgrade: preserve repository copy, use DB only to resolve source IDs. */
if ( PHP_SAPI !== 'cli' || ! in_array( '--write', $argv, true ) ) exit( 'CLI --write required.' );
$_SERVER['HTTP_HOST'] = '5gtech.test';
$_SERVER['REQUEST_URI'] = '/';
require dirname(__DIR__) . '/wordpress/wp-load.php';
if ( wp_get_environment_type() !== 'local' ) throw new RuntimeException('Snapshot preparation is local-only.');
require dirname(__DIR__) . '/deploy/polylang-shared.php';
$file = dirname(__DIR__) . '/deploy/content/snapshot.json';
$data = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
$local=[];
foreach(get_posts(['post_type'=>['page','post','g5_team','g5_service','g5_project','g5_job','g5_faq','g5_partner','g5_module'],'post_status'=>['publish','draft','private'],'posts_per_page'=>-1,'lang'=>'','suppress_filters'=>true]) as $p) $local[$p->post_type.'|'.$p->post_name]=$p->ID;
$sources=[];
foreach($data['posts'] as $p)if($p['lang']==='lt')foreach((array)$p['translations'] as $slug)$sources[$p['type'].'|'.$slug]=$p;
foreach($data['posts'] as &$p){
 $key=$p['type'].'|'.$p['slug'];
 if(!isset($local[$key]))throw new RuntimeException('Cannot resolve source ID: '.$key);
 $p['source_id']=$p['source_id']??$local[$key];
 if($p['type']==='post' && $p['lang']!=='lt' && isset($sources[$key]) && ($p['terms']['category'][0]['slug']??'')==='uncategorized'){
  $p['terms']['category']=array_map(static function($term)use($p){$term['name']=g5pll_t($term['name'],$p['lang']);$term['slug']=sanitize_title($term['name']).'-'.$p['lang'];return $term;},$sources[$key]['terms']['category']??[]);
 }
 // Only replace single legacy page wrappers; never overwrite already authored translated blocks.
 if($p['lang']!=='lt' && isset($sources[$key]) && preg_match('~^\s*<!-- wp:g5tech/[a-z-]+-page\s*/-->\s*$~',$p['content']) && !preg_match('~^\s*<!-- wp:g5tech/[a-z-]+-page\s*/-->\s*$~',$sources[$key]['content'])){
  $p['content']=g5pll_content($sources[$key]['content'],$p['lang']);
  echo 'Migrated translated blocks: '.$key."\n";
 }
}
unset($p);
$data['schema_version']=2;
$data['generated']=gmdate('c');
file_put_contents($file.'.tmp',wp_json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
rename($file.'.tmp',$file);
