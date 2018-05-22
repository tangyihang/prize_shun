<?php
ini_set('display_errors',"Off");
define('ROOT',dirname(__FILE__));
include_once ROOT.'/Config.php';
include_once ROOT.'/Class/mysql.class.php';
include_once ROOT.'/Lib/Resultbasketball.class.php';
$DB = new MySQL(DB_HOST, DB_USER, DB_PWD, DB_NAME);
$ResultObj = new Resultbasketball();
$ResultObj->DB = $DB;
$ResultObj->httpUrl="http://info.sporttery.cn/basketball/match_result.php?start_date=2015-12-21&end_date=2015-12-21";
$ResultObj->fromjcw();
$ResultObj->from500();
$ResultObj->from163();


