<?php
ini_set('display_errors',0);
define('ROOT',dirname(__FILE__));
include_once ROOT.'/Config.php';
include_once ROOT.'/Class/mysql.class.php';
include_once ROOT.'/Lib/Resultfootball.class.php';
$time_start = microtime_float();
$DB = new MySQL(DB_HOST, DB_USER, DB_PWD, DB_NAME);
$ResultObj = new Resultfootball();
$ResultObj->DB = $DB;
$start_date='';
$end_date='';
$page=5;
if($start_date && $end_date){
	$ResultObj->httpUrl="http://info.sporttery.cn/football/match_result.php?start_date=$start_date&end_date=$end_date&page=$page";
}else{
	$ResultObj->httpUrl="http://info.sporttery.cn/football/match_result.php";
}

//暂停竞彩官网 $ResultObj->fromjcw();

$datastr=date('Y-m-d',time()-24*3600);

$ResultObj->from500(); //获取当天
sleep(2);
$ResultObj->from500($datastr); //获取上一天
sleep(1);
$ResultObj->from163();
sleep(2);
$ResultObj->from163($datastr);
sleep(2);
$ResultObj->fromOkooonet();
sleep(2);
$ResultObj->fromOkooonet($datastr);

$time_end = microtime_float();

$time = $time_end - $time_start;

echo "Did something in $time seconds ";

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
