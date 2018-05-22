<?php
ini_set('display_errors',0);
define('ROOT',dirname(__FILE__));
include_once ROOT.'/Config.php';
include_once ROOT.'/Class/mysql.class.php';
include_once ROOT.'/Lib/GetSP.class.php';
$time_start = microtime_float();
$DB = new MySQL(DB_HOST, DB_USER, DB_PWD, DB_NAME);
$ResultObj = new GetSP();
$ResultObj->DB = $DB;
$sport=$_REQUEST['sport'];
$lotteryid=$_REQUEST['lotteryid'];
//
$lotteryarr=array();
$lotteryarr[]=$_REQUEST['lotteryid'];
if($sport=='fb'){
   $result = $ResultObj->zucai($lotteryarr);	
}
if($sport=="bk"){
   $result = $ResultObj->lancaisp($lotteryarr);	
}
echo json_encode($result);
$time_end = microtime_float();
$time = $time_end - $time_start;

error_log("Did something in $time seconds ".chr(13).chr(10),3,ROOT.'/Log/interface_sp.'.date('YmdH').".log"); 

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
