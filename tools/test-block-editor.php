<?php
/** Current Gutenberg persistence and role checks. Never runs against the normal DB.
 * php tools/test-block-editor.php /absolute/path/to/disposable/test.sqlite
 * Requires a separate SQLite WordPress copy with theme/plugins and roles already installed.
 */
if ( PHP_SAPI !== 'cli' ) exit;
$database = realpath( $argv[1] ?? '' );
$production = realpath( dirname(__DIR__) . '/wordpress/wp-content/database/.ht.sqlite' );
if ( ! $database || $database === $production || basename($database) !== 'test.sqlite' ) {
	fwrite(STDERR, "Specify a separate existing test.sqlite database.\n"); exit(1);
}
define('DB_DIR', dirname($database)); define('DB_FILE', basename($database));
define('REST_REQUEST',true);
$_SERVER['HTTP_HOST']='5gtech.test'; $_SERVER['REQUEST_URI']='/wp-json/wp/v2/pages';
require dirname(__DIR__).'/wordpress/wp-load.php';
require_once ABSPATH.'wp-admin/includes/user.php';
remove_all_filters('pre_wp_mail');
add_filter('pre_wp_mail', '__return_true');
$checks=0; $posts=[]; $users=[];
function block_check($ok,$message){global $checks; ++$checks; if(!$ok)throw new RuntimeException($message);}
// Keep test variables outside WordPress' global template scope.
(static function() use (&$checks, &$posts, &$users) {
try {
	$registry=WP_Block_Type_Registry::get_instance();
	foreach(glob(dirname(__DIR__).'/wordpress/wp-content/plugins/5gtech-core/build/*/block.json') as $file){
		$metadata=json_decode(file_get_contents($file),true);
		block_check($registry->is_registered($metadata['name']), 'Unregistered: '.$metadata['name']);
	}
	foreach(['g5_content_editor','g5_hr_editor'] as $role){
		$id=wp_insert_user(['user_login'=>'block_test_'.wp_generate_password(10,false),'user_pass'=>wp_generate_password(30),'role'=>$role]);
		block_check(!is_wp_error($id),'Create test editor'); $users[]=$id; wp_set_current_user($id);
		block_check(!current_user_can('manage_options'),'Editor must not manage WordPress settings');
		block_check(!current_user_can('install_plugins'),'Editor must not install plugins');
		g5tech_editor_preview_globals();
		$scripts = wp_scripts()->registered['wp-block-editor']->extra['before'] ?? [];
		$globals = end($scripts);
		block_check(str_contains($globals, 'heroStats'), 'Editor receives real global stats');
		block_check(str_contains($globals, 'post-new.php?post_type=g5_team') === ('g5_hr_editor' === $role), 'Team action follows role permissions');
		block_check(str_contains($globals, 'admin.php?page=g5tech-settings') === ('g5_content_editor' === $role), 'Settings action follows role permissions');
		foreach(['lt','en','de'] as $language){
			$page_id=wp_insert_post(['post_type'=>'page','post_status'=>'draft','post_title'=>'Temporary editor test','post_author'=>$id],true);
			block_check(!is_wp_error($page_id),'Create test page');$posts[]=$page_id;pll_set_post_language($page_id,$language);
			block_check(pll_get_post_language($page_id)===$language,'Initial test language assignment');
			block_check(current_user_can('edit_post',$page_id),'Editor can edit page');
			$title='Editor saved '.$language.' – žą & "quote"';
			$content=serialize_block(['blockName'=>'g5tech/page-hero','attrs'=>['title'=>$title,'lead'=>'Saved via WordPress REST'],'innerBlocks'=>[],'innerHTML'=>'','innerContent'=>[]]);
			$request=new WP_REST_Request('POST','/wp/v2/pages/'.$page_id);$request->set_param('content',$content);$request->set_param('lang',$language);
			$response=rest_do_request($request);
			block_check($response->get_status()===200,'REST saves block for '.$role.'/'.$language);
			$saved=get_post_field('post_content',$page_id);
			block_check(html_entity_decode(parse_blocks($saved)[0]['attrs']['title'],ENT_QUOTES,'UTF-8')===$title,'Block attributes round-trip '.$role.'/'.$language);
			block_check(str_contains(html_entity_decode(do_blocks($saved),ENT_QUOTES,'UTF-8'),$title),'Saved heading renders');
			block_check(pll_get_post_language($page_id)===$language,'Saving retains language: expected '.$language.', terms '.wp_json_encode(wp_get_object_terms($page_id,'language',['fields'=>'slugs'])));
		}
	}
	$settings = g5tech_settings();
	$home_page = get_page_by_path('pagrindinis');
	foreach(['lt','en','de'] as $language){
		$home_id = pll_get_post($home_page->ID, $language);
		block_check($home_id && str_contains(g5tech_page_editor_url('pagrindinis', $language), 'post=' . $home_id . '&action=edit'), 'Homepage editor link uses '.$language.' translation');
	}
	$sanitized = g5tech_sanitize_settings($settings);
	foreach($settings as $key=>$value){
		if(str_starts_with($key,'home_')) block_check($sanitized[$key] === $value,'Retired setting preserved: '.$key);
	}
	echo 'PASS: '.$checks." block-editor checks\n";
} finally {
	wp_set_current_user(0);
	foreach($posts as $id)wp_delete_post($id,true);
	foreach($users as $id)wp_delete_user($id);
}
})();
