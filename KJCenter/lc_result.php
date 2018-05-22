<?php
ini_set('display_errors',0);
define('ROOT',dirname(__FILE__));
include_once ROOT.'/Config.php';
include_once ROOT.'/Class/mysql.class.php';
include_once ROOT.'/Lib/Resultbasketball.class.php';
$time_start = microtime_float();
$DB = new MySQL(DB_HOST, DB_USER, DB_PWD, DB_NAME);
$ResultObj = new Resultbasketball();
$ResultObj->DB = $DB;
$start_date='';
$end_date='';
if($start_date && $end_date){
	$ResultObj->httpUrl="http://info.sporttery.cn/basketball/match_result.php?start_date=$start_date&end_date=$end_date";
}else{
	$ResultObj->httpUrl="http://info.sporttery.cn/basketball/match_result.php";
}

//$ResultObj->fromjcw();
$datestr=date('Y-m-d',time()-24*3600);
$ResultObj->from500();
sleep(2);
$ResultObj->from163();
sleep(2);
$ResultObj->fromOkooo();
sleep(2);
$ResultObj->from500($datestr);
sleep(2);
$ResultObj->from163($datestr);
sleep(2);
$ResultObj->fromOkooo($datestr);

$time_end = microtime_float();

$time = $time_end - $time_start;

echo "Did something in $time seconds ";

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
