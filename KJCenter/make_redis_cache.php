<?php
/*
*拼接当前赛事功能，节约前端中间件拼接时间
*/
define('ROOT',dirname(__FILE__));
include_once ROOT.'/Config3.php';
$start=microtime_float();
$redis=new redis();
$con=$redis->connect($_RC['HOST'],$_RC['PORT']);
try{
	$con=$redis->connect($_RC['HOST'],$_RC['PORT']);
	if($con){
		$auth=$redis->auth($_RC['PWD']);
		if($auth){
			$redis->select(4);
			$arr=unserialize($redis->get('FB_cur_list'));
			$replies=array();
			$pipe = $redis->multi(Redis::PIPELINE);
			foreach($arr as $v){
			   $pipe->get($v);
			}
			$replies = $pipe->exec();
			$resArr=array();
			foreach($replies as $val){
				$temp=array();
				$temp=unserialize($val);
				$idx=$temp['lotttime']."_".$temp['ballid'];
			    if(date('Y-m-d H:i:s',time()) >date('Y-m-d H:i:s',strtotime($temp['gameendtime']))){
				   $temp['status']=-1;
			    }
				$resArr[]=$temp;
			}
			//比赛缓存
		    $redis->set('FB_match_cache',serialize($resArr));
		    echo "FB_match_cache set sucess \n";
		   $matchArr=unserialize($redis->get("FB_cur_list"));
		   $spArr=array();
		   $lotteryidArr=array('209','210','211','212','213');
		   $redis->select(6);
		   foreach($lotteryidArr as $lotid){
			   foreach($matchArr as $val){
				 $idx=$lotid.'_'.$val;
				 $lrange=$redis->lrange($idx,0,0);
				 $tep=array();
				 if(isset($lrange[0])){
				    $tep=unserialize($lrange[0]);
					 unset($tep['s_code']);
					 unset($tep['m_id']);
					 unset($tep['m_num']);
					 unset($tep['date']);
					 unset($tep['time']);
					 unset($tep['lotttime']);
					 unset($tep['ballid']);
					 unset($tep['p_id']);
					 unset($tep['addtime']);
					 $newSp=array();
					 $newSp=$tep;
					 $newSp['single']=$tep['single'];
					 $newSp['p_status']=$tep['p_status'];
					 $spArr[$lotid][$val]=$newSp;
				 }
			   }
		   }
		   $redis->select(4);
		   $redis->set('FB_cursp_cache',serialize($spArr));
		   echo "FB_cursp_cache set sucess \n";
			//
		}else{
		   echo "auth no \n";		   
		}
	   
	}else{
	   echo "connect redis fail,please check config file"; 
	}
}catch(Exception $e){
   echo "程序异常 \n";
}
$end=microtime_float();
$total=$end-$start;
echo "time:".$total."s\n";

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
	

