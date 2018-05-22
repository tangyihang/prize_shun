<?php
ini_set('display_errors',0);
define('ROOT',dirname(__FILE__));
include_once ROOT.'/Config2.php';
include_once ROOT.'/Class/mysql.class.php';
include_once ROOT.'/Lib/GrabSp.class.php';
include_once ROOT.'/Lib/function.common.php';
$time_start = microtime_float();
$DB = new MySQL(DB_HOST, DB_USER, DB_PWD, DB_NAME);
echo "-------------------------------------------------------------\n";
echo "抓取官方SP值初始化 ...\n";
$ResultObj = new GrabSp();
$ResultObj->DB = $DB;
//
echo "--------------\n 开始抓取让分胜负玩法SP值 \n";
$ResultObj->hdc();
echo "获取竞彩篮球让分胜负的SP值(hdc) get sp data success,please wait 1s ... \n";
sleep(1);
echo "--------------\n开始抓取胜负玩法SP值 \n";
$ResultObj->mnl();
echo "获取竞彩篮球胜负的SP值(mnl) get sp data success,please wait 1s ... \n";
sleep(1);
echo "--------------\n开始抓取大小分玩法SP值 \n";
$ResultObj->hilo();
echo "获取竞彩篮球大小分的SP值(hilo) get sp data success,please wait 1s ... \n";
sleep(1);
echo "--------------\n开始抓取胜分差玩法SP值 \n";
$ResultObj->wnm();
echo "获取竞彩篮球胜分差的SP值(wnm) get sp data success,please wait 1s ... \n";
echo "--------------\n开始抓取半全场玩法SP值 \n";
//
//$ResultObj->zucaisp();

$time_end = microtime_float();
$time = $time_end - $time_start;

echo "抓取时间总耗时： $time seconds \n";
echo "-------------------------------------------------------------\n";

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
