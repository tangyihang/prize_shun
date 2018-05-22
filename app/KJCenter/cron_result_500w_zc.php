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
 * 500w传统足彩抓取赛果
 */
$info_500w = array(
	'324' => 'http://live.500.com/zucai.php',
	'441' => 'http://live.500.com/zucai.php',
	'326' => 'http://live.500.com/6chang.php',
	'325' => 'http://live.500.com/4chang.php'
);
foreach($info_500w as $k=>$v){
	if(empty($_GET['date'])){
		$result = $lotteryResult->getzcissue($k);
		foreach($result as $key=>$v2){
			$lotteryResult->runZC($k, $v, $v2['lotteryissue']);
		}
	}else{
		$lotteryResult->runZC($k, $v, $_GET['date']);
	}
}

//http://newm.198tc.com/KJCenter/cron_result_500w_zc.php
//http://newm.198tc.com/KJCenter/cron_result_500w_zc.php?date=15044

