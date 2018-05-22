<?php
define('ROOT',dirname(__FILE__));
include_once ROOT.'/Config.php';
include_once ROOT.'/Class/mysql.class.php';
include_once ROOT.'/Lib/Lancai.class.php';
include_once ROOT.'/Lib/function.common.php';
ini_set('pcre.backtrack_limit', 1000000); //抓取大数据时需要开启此功能

$url='http://info.sporttery.cn/basketball/match_list.php';
$logfile=ROOT."/Log/";
$Lc=new Lancai($url);
$Jc->logdir = $logfile;
$DB = new MySQL(DB_HOST, DB_USER, DB_PWD, DB_NAME);
$Lc->DB = $DB;
$Lc->run();
