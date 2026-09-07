<?php
/** Read-only HTTP regression checks against actual Polylang translations. No emails sent. */
$_SERVER['HTTP_HOST'] = '5gtech.test';
$_SERVER['REQUEST_URI'] = '/';
require dirname(__DIR__) . '/wordpress/wp-load.php';
$base = rtrim($argv[1] ?? home_url(), '/');
$checks = 0; $errors = [];
function g5_verify($condition, $message) { global $checks,$errors; $checks++; if(!$condition)$errors[]=$message; }
function g5_http($url, $post = null, $language = 'lt') {
	$h=curl_init($url);
	curl_setopt_array($h,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,CURLOPT_TIMEOUT=>20,CURLOPT_MAXREDIRS=>5,CURLOPT_FOLLOWLOCATION=>$post===null,CURLOPT_HTTPHEADER=>['Accept-Language: '.$language,'Cookie: pll_language='.$language]]);
	if($post!==null)curl_setopt_array($h,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>http_build_query($post)]);
	$raw=curl_exec($h);$info=curl_getinfo($h);curl_close($h);
	return ['status'=>$info['http_code'],'headers'=>substr((string)$raw,0,$info['header_size']),'body'=>substr((string)$raw,$info['header_size'])];
}
$posts=get_posts(['post_type'=>['page','post','g5_service','g5_project','g5_team','g5_job'],'post_status'=>'publish','posts_per_page'=>-1,'suppress_filters'=>true,'lang'=>'']);
foreach($posts as $p){
	$language=pll_get_post_language($p->ID)?:'lt';
	$url=$base.parse_url(get_permalink($p),PHP_URL_PATH);
	$r=g5_http($url,null,$language);
	g5_verify($r['status']===200,$url.' HTTP '.$r['status']);
	if(!$r['body'])continue;
	$d=new DOMDocument();@$d->loadHTML($r['body']);$x=new DOMXPath($d);
	$expected=['lt'=>'lt-LT','en'=>'en-US','de'=>'de-DE'][$language];
	g5_verify($d->documentElement->getAttribute('lang')===$expected,$url.' HTML lang');
	g5_verify($x->query('//h1')->length===1,$url.' requires exactly one H1');
	$seo_id=$p->ID;
	if($p->post_type==='g5_team' && !(bool)get_post_meta($p->ID,'g5_team_show_profile',true))$seo_id=get_page_by_path('apie-mus')->ID;
	$translations=pll_get_post_translations($seo_id);
	foreach(count($translations)>1?$translations:[] as $lang=>$id){
		$path=parse_url(get_permalink($id),PHP_URL_PATH);
		$nodes=$x->query('//link[@hreflang="'.$lang.'"]');
		g5_verify($nodes->length===1 && parse_url($nodes->item(0)->getAttribute('href'),PHP_URL_PATH)===$path,$url.' hreflang '.$lang);
	}
	$switchers=$x->query('//nav[contains(@class,"g5-language-switcher")]');
	g5_verify($switchers->length===2,$url.' header/footer language switchers');
	foreach($switchers as $switcher){
		$links=$x->query('.//a[@href]',$switcher);
		g5_verify($links->length===2,$url.' two alternative languages');
	}
	g5_verify(!preg_match('/Vardas Pavardė|Lorem ipsum|Uncategorized/u',$d->textContent),$url.' unfinished copy');
}
foreach(['lt','en','de'] as $lang){
	foreach(['contact'=>'kontaktai','application'=>'kandidatuoti'] as $kind=>$slug){
		$lt=get_page_by_path($slug);$id=pll_get_post($lt->ID,$lang);
		$path=parse_url(get_permalink($id),PHP_URL_PATH);$url=$base.$path;
		$r=g5_http($url,null,$lang);$d=new DOMDocument();@$d->loadHTML($r['body']);$x=new DOMXPath($d);
		$field=$x->query('//input[@name="g5tech_form_language"]');
		g5_verify($field->length===1 && $field->item(0)->getAttribute('value')===$lang,$url.' explicit form language');
		$nonce=$x->query('//input[@name="g5tech_nonce"]');
		g5_verify($nonce->length===1,$url.' form nonce exists');
		if($kind==='contact') {
			g5_verify($x->query('//div[contains(@class,"info-card")]//a[starts-with(@href,"mailto:")]')->length===3,$url.' three direct contact cards');
		}
		foreach(['security'=>'invalid','required'=>$nonce->length?$nonce->item(0)->getAttribute('value'):''] as $status=>$token){
			// Missing required fields prevent mail transport from being invoked.
			$r=g5_http($base.'/wp-admin/admin-post.php',['action'=>'g5tech_'.$kind,'g5tech_form_language'=>$lang,'g5tech_nonce'=>$token],$lang);
			g5_verify($r['status']===302 && str_contains($r['headers'],'Location: '.$url.'?forma='.$status),$url.' POST '.$status.' keeps language');
		}
	}
	$r=g5_http($base.($lang==='lt'?'':'/'.$lang).'/missing-regression-page/',null,$lang);
	g5_verify($r['status']===404,'404 '.$lang);
}
echo ($errors?'FAIL':'PASS').': '.$checks.' checks; '.count($errors).' failures'.PHP_EOL;
foreach($errors as $error)echo ' - '.$error.PHP_EOL;
exit($errors?1:0);
