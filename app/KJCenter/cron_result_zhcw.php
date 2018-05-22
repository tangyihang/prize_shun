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
 * 中彩网抓取双色球,3D,大乐透开奖号码及中奖详情
 */
$source_zhcw = array(
		'1' => 'http://kj.zhcw.com/kjData/2012/zhcw_ssq_index_last30.js',
		'2' => 'http://kj.zhcw.com/kjData/2012/zhcw_3d_index_last30.js',
		'3' => 'http://kj.zhcw.com/kjData/2012/zhcw_qlc_index_last30.js'
		);
foreach($source_zhcw as $k=>$v){
	$lotteryResult->runZhcw($k, $v, 'zhcw');
}


//http://newm.198tc.com/KJCenter/cron_result_zhcw.php



