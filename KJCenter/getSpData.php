<?php
ini_set('display_errors',0);
define('ROOT',dirname(__FILE__));
include_once ROOT.'/Config.php';
include_once ROOT.'/Class/mysql.class.php';
include_once ROOT.'/Lib/CollectSp.class.php';
$time_start = microtime_float();
$DB = new MySQL(DB_HOST, DB_USER, DB_PWD, DB_NAME);
$ResultObj = new CollectSp();
$ResultObj->DB = $DB;
//
$ResultObj->lancaisp();
//
$ResultObj->zucaisp();

$time_end = microtime_float();
$time = $time_end - $time_start;

echo "Did something in $time seconds ";

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
