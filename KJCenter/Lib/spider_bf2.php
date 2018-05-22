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
echo "开始从bet365彩票抓取竞彩足球即时比分\n";
//$Bf->frombet365();
//$Bf->fromjcw_wether_hongpai();

$datestr=date('Y-m-d',time()-24*3600);
$Bf->frombet365($datestr);

//for($i=0;$i<5;$i++){
//   $Bf->get_match_updated();
//   echo "please wait 8s \n"; 
 //  echo "第".($i+1)."次更新,抓取结束 please wait 8s \n"; 
 //  sleep(5);   
//}

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
