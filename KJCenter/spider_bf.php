<?php
$time_start = microtime_float();
ini_set('display_errors',"ON");
define('ROOT',dirname(__FILE__));
include_once ROOT.'/Config4.php';
include_once ROOT.'/Class/mysql.class.php';
include_once ROOT.'/Lib/Livebf.class.php';
include_once ROOT.'/Lib/function.common.php';
ini_set('pcre.backtrack_limit', 1000000); //抓取大数据时需要开启此功能
$url='http://info.sporttery.cn/football/match_list.php';
$logfile=ROOT."/Log/";
echo "初始化抓取比分类\n";
$Bf=new Livebf($url);
$Bf->logdir = $logfile;
$DB = new MySQL(DB_HOST, DB_USER, DB_PWD, DB_NAME,3306,'latin1');
$Bf->DB = $DB;
echo "开始从500彩票抓取竞彩足球即时比分\n";
$Bf->from500();
$datetime=date("Y-m-d",time()-12*3600);
$Bf->from500($datetime);

echo "抓取结束\n";
echo "please wait 5s \n";
sleep(5);
echo "开始从500彩票抓取竞彩篮球即时比分\n";
$Bf->from500_lancai();

$time_end = microtime_float();
$time = $time_end - $time_start;
echo "程序结束";
echo "时间总耗时： $time seconds \n";
echo "-------------------------------------------------------------\n";

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}