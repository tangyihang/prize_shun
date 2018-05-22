<?php

header("Content-type: text/html; charset=GBK");
define('LIB_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR.'Lib'.DIRECTORY_SEPARATOR);
define('CLASS_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR.'Class'.DIRECTORY_SEPARATOR);
define('CACHE_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR);
require_once 'Config.php';
require_once LIB_ROOT.'lotteryResult.class.php';
require_once LIB_ROOT.'cache/functions.php';


$lotteryResult = new lotteryResult();
$lotteryResult->initDB();

/**
 * 彩乐乐普通数字彩抓取开奖信息写入缓存
 * 彩乐乐抓取功能暂停使用 2015.03
 */
/*
$cailele = array(
	'1' => 'http://www.cailele.com/static/ssq/newlyopenlist.xml',
	'2' => 'http://www.cailele.com/static/3d/newlyopenlist.xml',
	'3' => 'http://www.cailele.com/static/qlc/newlyopenlist.xml',
	'281' => 'http://www.cailele.com/static/dlt/newlyopenlist.xml',
	'283' => 'http://www.cailele.com/static/ps/newlyopenlist.xml',
	'284' => 'http://www.cailele.com/static/pw/newlyopenlist.xml',
	'282' => 'http://www.cailele.com/static/qxc/newlyopenlist.xml'
	);

foreach($cailele as $k=>$v){
	$lotteryResult->runCailele($k, $v, 'cailele');
}
*/

 
/**
 * 彩乐乐单独抓取传统足彩赛果
 */
/*$cailele_zc = array(
	'324' => 'http://www.cailele.com/static/sfc14/openAward_15041.xml',
	'441' => 'http://www.cailele.com/static/sfc14/openAward_15041.xml',
	'326' => 'http://www.cailele.com/static/fbbqc/openAward_15041.xml',
	'325' => 'http://www.cailele.com/static/jq4/openAward_15041.xml'
	);
foreach($cailele_zc as $k=>$v){
	//$lotteryResult->run($k, $v, 'cailele');
}
*/





