<?php
ini_set('display_errors',"ON");
define('ROOT',dirname(__FILE__));
include_once ROOT.'/Config3.php';
include_once ROOT.'/Class/mysql.class.php';
include_once ROOT.'/Lib/Jingcairedis.class.php';
include_once ROOT.'/Lib/function.common.php';
ini_set('pcre.backtrack_limit', 1000000); //抓取大数据时需要开启此功能
$time_start = microtime_float();
$url='http://info.sporttery.cn/football/match_list.php';
$logfile=ROOT."/Log/";
echo "-------------------------------------------------------------\n";
echo "抓取官方赛程初始化 ...\n";
$Jc=new Jingcairedis($url);
$Jc->logdir = $logfile;
$DB = new MySQL(DB_HOST, DB_USER, DB_PWD, DB_NAME,3306,'latin1');
$Jc->DB = $DB;
$Jc->run();
$time_end = microtime_float();
$time = $time_end - $time_start;

echo "抓取时间总耗时： $time seconds \n";
echo "-------------------------------------------------------------\n";

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}