<?php
ini_set('display_errors',0);
define('ROOT',dirname(__FILE__));
include_once ROOT.'/Config.php';
include_once ROOT.'/Class/mysql.class.php';
include_once ROOT.'/Lib/Resultbasketball.class.php';
$DB = new MySQL(DB_HOST, DB_USER, DB_PWD, DB_NAME);

$lotttime=date('Y-m-d',time()-24*3600);
$sport=$_REQUEST['sport'];

if($sport=='fb'){
	$sql="select * from tab_lottery_result where lotttime='".$lotttime."' and status != '4' group by lotttime,ballid,half_score,full_score having count(*)<3";
    $result=$DB->query($sql);
	$newArr=array();
	foreach($result as $val){
		if(time()-strtotime($val['addtime']) > 600){
			$temp[]=$val['ballid'];
		}
	}
	$temp=array_unique($temp);
	foreach($temp as $key=>$val){
		$newArr[$key]['lotttime']=$lotttime;
		$newArr[$key]['ballid']=$val;
	}
	if(count($newArr)==0){
		echo "no error data";
		exit;
	}
    echo json_encode($newArr);
	exit;
}
if($sport=='bk'){
	
	$sql="select * from tab_lottery_result_lancai where lotttime='".$lotttime."' and status != '4' group by lotttime,ballid,first_score,two_score,three_score,four_score,add_score,full_score having count(*)<3";
	$result=$DB->query($sql);
	$newArr=array();
	foreach($result as $val){
		if(time() - strtotime($val['addtime']) > 600){
			$temp[]=$val['ballid'];
		}
	}
	$temp=array_unique($temp);
	foreach($temp as $key=>$val){
		$newArr[$key]['lotttime']=$lotttime;
		$newArr[$key]['ballid']=$val;
	}
	if(count($newArr)==0){
		echo "no error data";
		exit;
	}
    echo json_encode($newArr);
	exit;
}
echo "error request";



