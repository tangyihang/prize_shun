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
echo "--------------\n 开始抓取胜平负玩法SP值 \n";
$ResultObj->had();
echo "胜平负玩法 get sp data success,please wait 2s ... \n";
sleep(2);
echo "--------------\n开始抓取让球胜平负玩法SP值 \n";
$ResultObj->hhad();
echo "让球胜平负玩法 get sp data success,please wait 2s ... \n";
sleep(2);
echo "--------------\n开始抓取总进球玩法SP值 \n";
$ResultObj->ttg();
echo "总进球玩法 get sp data success,please wait 2s ... \n";
sleep(2);
echo "--------------\n开始抓取比分玩法SP值 \n";
$ResultObj->crs();
echo "比分玩法 get sp data success,please wait 2s ... \n";
sleep(2);
echo "--------------\n开始抓取半全场玩法SP值 \n";
$ResultObj->hafu();
echo "半全场玩法 get sp data success,please wait 2s ... \n";

$time_end = microtime_float();
$time = $time_end - $time_start;

echo "抓取时间总耗时： $time seconds \n";
echo "-------------------------------------------------------------\n";

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
