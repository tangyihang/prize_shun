<?php
ini_set('display_errors',0);
define('ROOT',dirname(__FILE__));

include_once ROOT.'/Config.php';
include_once ROOT.'/Class/mysql.class.php';
include_once ROOT.'/Lib/Resultbasketball.class.php';
$DB = new MySQL(DB_HOST, DB_USER, DB_PWD, DB_NAME);
$lotttime=$_REQUEST['lotttime'];
$ballid=$_REQUEST['ballid'];
$sport=$_REQUEST['sport'];

if($sport=='fb'){
	if($ballid){
	   $sql="select * from tab_lottery_result where lotttime='".$lotttime."' and ballid='".$ballid."' and status != '4' group by lotttime,ballid,half_score,full_score having count(*)>2";	
	}else{
	   $sql="select * from tab_lottery_result where lotttime='".$lotttime."' and status != '4' group by lotttime,ballid,half_score,full_score having count(*)>2";
	}
    $result=$DB->query($sql);
    echo json_encode($result);
	exit;
}
if($sport=='bk'){
	if($ballid){
        $sql="select * from tab_lottery_result_lancai where lotttime='".$lotttime."' and ballid='".$ballid."' and status != '4' group by lotttime,ballid,first_score,two_score,three_score,four_score,add_score,full_score having count(*)>2";
	}else{
	    $sql="select * from tab_lottery_result_lancai where lotttime='".$lotttime."' and status != '4' group by lotttime,ballid,first_score,two_score,three_score,four_score,add_score,full_score having count(*)>2";
		
	}
	$result=$DB->query($sql);
	echo json_encode($result);
	exit;
}
echo "error request";



