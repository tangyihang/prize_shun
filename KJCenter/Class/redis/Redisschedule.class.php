<?php
/**
 * @描述 竞彩相关赛程Redis存储
 * @author WYE
 * @date 2016.07
 * */
include "Redisbase.class.php";
class Redisschedule extends Redisbase{
    /*
    * 存储比赛数据
    * $keyname 键名
	* $data 操作数据
    * $database 操作库
	* $datatype 存储数据类型
	* $expiretime 有效期 单位s
    */
	function setgameinfo($keyname, $data, $database, $datatype, $expiretime){
		$this->selectDB($database);
		if($datatype == "String"){
			//字符串
			$this->redis->set($keyname,serialize($data));
			$this->redis->expire($keyname,$expiretime);
			
		}else if($datatype == "Hashes"){
			;//哈希值
			$this->redis->hmset($keyname,$data);
			
		}else if($datatype == "Lists"){
			;//列表
			$this->redis->lpush($keyname,serialize($data));
		}else if($datatype == "Sets"){
			;//集合
			
		}
		return true;
	}
	
	function getgameinfo($keyname, $database, $datatype, $hashkeys){
		$this->selectDB($source);
		if($datatype == "String"){
			return $this->redis->get($keyname);
		}else if($datatype == "Hashes"){
			;//哈希值
			return $this->redis->hmget($keyname, $hashkeys);
		}else if($datatype == "Lists"){
			;//列表;
			return $this->redis->lrange($keyname,0,-1);
		}else if($datatype == "Sets"){
			//集合
			;
		}
		
	}
	/*
	*$type 竞彩为FB 篮彩BK
	*/
	function setSchedule($arr,$type){
		if($type=='FB'){
		   $this->redis->select(4);
		}
		if($type=='BK'){
		   $this->redis->select(5);
		}
		
		$curmatchlist=$type."_cur_list";
		$curmatch=array();
		foreach($arr as $val){
			$idx=$val['lotttime']."_".$val['ballid'];
			if($this->redis->exists($idx)){
			   $temp=array();
			   $temp=unserialize($this->redis->get($idx));
			   if($temp['h_status']){
				   $val['h_status']=$temp['h_status']; //华阳平台控制的状态  
			   }
			   $this->redis->set($idx,serialize($val));
			}else{
			   $this->redis->set($idx,serialize($val));	
			}
			if($val['status']=='Selling'){
				$curmatch[] = $idx;
			}
		}
		error_log(json_encode($curmatch).chr(13).chr(10),3,ROOT.'/Log/redis_test'.date('YmdH').'.log');
		//当前赛事与redis库不一致时更新数据
		$datamatch=array();
                $datamatch = unserialize($this->redis->get($curmatchlist));
		if($curmatch && !$this->checkmatch($curmatch,$datamatch)){
			  $c=array_merge($curmatch,$datamatch);
	          $curmatch=array_unique($c);
			  $newcurmatch=array();
			  foreach($curmatch as $val){
				 $temp=unserialize($this->redis->get($val));
                 if(strtotime($temp['gamestarttime'])<time()){
					 continue;
				 }
                 $newcurmatch[]=$val;				 
			  }
			  $this->redis->set($curmatchlist,serialize($newcurmatch));
              error_log(date("Y-m-d H:i:s",time())." update redis curmatch: ".json_encode($newcurmatch).chr(13).chr(10),3,ROOT.'/Log/redis_curlist.log');		   
		}
		
	}
	function checkmatch($a=array(),$b=array()){
		//比较2个数组是否一致 一致返回true
		//if(!array_diff($a,$b) && !array_diff($b,$a)){
		//	return true;
		//}
		return false;	
	}
	/*
	*
	*获取当前赛事
	*
	*/
	function getCurSchedule($type){
		$this->redis->select(4);
		$curmatchlist=unserialize($this->redis->get($type.'_cur_list'));
		$matchlist=array();
		foreach($curmatchlist as $val){
			$matchlist[] =unserialize($this->redis->get($val));
		}
		return $matchlist;
	}
	/*
	*
	*获取当前赛事
	*
	*/
	function getCurScheduleLc($type){
		$this->redis->select(5);
		$curmatchlist=unserialize($this->redis->get($type.'_cur_list'));
		$matchlist=array();
		foreach($curmatchlist as $val){
			$matchlist[] =unserialize($this->redis->get($val));
		}
		error_log(json_encode($matchlist).chr(13).chr(10),3,'redis.log');
		return $matchlist;
	}
	/*
	*
	*获取当前赛事
	*
	*/
	function getHisSchedule($keyname){
		$arr =$this->redis->keys($keyname."*");
	    return $arr;
	}
	/*
	*
	*获取当前赛事
	*
	*/
	function delSchedule(){
	 ;
	}
	/*
	*
	*更新平台赛事状态
	*
	*/
	function updateSchedule($key,$status){
		$this->redis->select(4);
		$arr=unserialize($this->redis->get($key));
		$arr['h_status']=$status;
		$this->redis->set($key,serialize($arr));
	}
	
}
?>
