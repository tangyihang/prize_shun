<?php
header("Content-type: text/html; charset=GBK");
define('LIB_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR.'Lib'.DIRECTORY_SEPARATOR);
define('CLASS_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR.'Class'.DIRECTORY_SEPARATOR);
define('CACHE_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR);
define('HOME_PATH',substr(dirname(__FILE__),0,'-9').'./Application/Home');
require_once 'Config.php';
require_once LIB_ROOT.'lotteryResult.class.php';
require_once LIB_ROOT.'cache/functions.php';

$lotteryResult = new lotteryResult();
$lotteryResult->initDB();

/**
 * 中国体彩网抓取功能
 * 大乐透，七星彩，排列三，排列五开奖号码
 * 传统足彩中奖详情
 */

$lottery_url = 'http://www.lottery.gov.cn/lottery/draws/Global.aspx';
$lotteryResult->runGov($lottery_url, 'lotterygov');


//足彩中奖详情地址---暂停使用
/*
$source_gov = array(
	'324' => 'http://www.lottery.gov.cn/lottery/draws/SFC.aspx',	
	'326' => 'http://www.lottery.gov.cn/lottery/draws/BQC.aspx',
	'325' => 'http://www.lottery.gov.cn/lottery/draws/JQC.aspx'
		);
foreach($source_gov as $k=>$v){
	//$lotteryResult->runGov_zc($k,$v,'lotterygov');
}
*/


//http://newm.198tc.com/KJCenter/cron_result_lottery_gov.php


