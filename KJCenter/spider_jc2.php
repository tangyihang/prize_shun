<?php
ini_set('display_errors',"ON");
define('ROOT',dirname(__FILE__));
include_once ROOT.'/Config3.php';
include_once ROOT.'/Class/mysql.class.php';
include_once ROOT.'/Lib/Jingcai2.class.php';
include_once ROOT.'/Lib/function.common.php';
ini_set('pcre.backtrack_limit', 1000000); //抓取大数据时需要开启此功能
$url='http://info.sporttery.cn/football/match_list.php';
$logfile=ROOT."/Log/";
$Jc=new Jingcai2($url);
$Jc->logdir = $logfile;
$DB = new MySQL(DB_HOST, DB_USER, DB_PWD, DB_NAME);
$Jc->DB = $DB;
$Jc->run();
