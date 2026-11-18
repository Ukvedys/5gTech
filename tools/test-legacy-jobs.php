<?php
/** Read-only migration verification. Never submits a form or sends email. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$base = rtrim($argv[1] ?? 'http://5gtech.test','/');
$jobs = json_decode(file_get_contents(dirname(__DIR__).'/deploy/content/legacy-jobs.json'),true,512,JSON_THROW_ON_ERROR)['jobs'];
$errors=[]; $checks=0;
function verify_job($ok,$label) { global $checks,$errors; $checks++; if (!$ok) $errors[]=$label; }
function read_job_page($url) {
    $delay = max(0, min(5000, (int) getenv('G5_HTTP_DELAY_MS')));
    if ($delay) usleep($delay * 1000);
    $c=curl_init($url);
    curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_TIMEOUT=>20,CURLOPT_HTTPHEADER=>['Cookie: pll_language=lt']]);
    $html=curl_exec($c);$code=curl_getinfo($c,CURLINFO_RESPONSE_CODE);curl_close($c);
    verify_job($code===200,$url.' HTTP '.$code);
    $d=new DOMDocument(); @$d->loadHTML($html ?: '<html></html>');
    return [$d,new DOMXPath($d)];
}
[$list,$x]=read_job_page($base.'/karjera/');
verify_job($x->query('//*[@id="positions"]//a')->length===17,'Exactly 17 LT job links');
foreach($jobs as $job) {
    $path='/karjera/'.$job['slug'].'/';
    verify_job($x->query('//*[@id="positions"]//a[@href="'.$base.$path.'"]')->length===1,'Listed once '.$path);
    [$d,$detail]=read_job_page($base.$path);
    $h1=$detail->query('//h1');
    verify_job($h1->length===1 && trim($h1->item(0)->textContent)===$job['title'],'Title '.$path);
    foreach($job['meta'] as $key=>$value) {
        if (!$value || in_array($key,['g5_job_group','g5_job_active','g5_job_expires'],true)) continue;
        foreach(explode("\n",$value) as $line) verify_job(str_contains($d->textContent,trim($line)),$path.' missing '.$key.': '.$line);
    }
    $cta=$detail->query('//a[contains(@href,"/kandidatuoti/?pozicija=")]');
    verify_job($cta->length===2,'Two application CTAs '.$path);
    if ($cta->length) {
        [$form,$fx]=read_job_page($cta->item(0)->getAttribute('href'));
        $selected=$fx->query('//select//option[@selected]');
        $matches=false;foreach($selected as $option) if(str_contains($option->textContent,$job['title'])) $matches=true;
        verify_job($matches,'Correct job preselected '.$path);
    }
    verify_job(!preg_match('/porn-dumps|arab-porn|pornichka|<script/i',strip_tags($d->textContent)),'No unrelated source content '.$path);
}
echo ($errors?'FAIL':'PASS').": $checks job migration checks; ".count($errors)." failures\n";
foreach($errors as $error) echo $error."\n";
exit($errors?1:0);
