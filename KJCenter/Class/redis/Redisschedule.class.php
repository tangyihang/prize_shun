<?php
/**
 * @���� �����������Redis�洢
 * @author WYE
 * @date 2016.07
 * */
include "Redisbase.class.php";
class Redisschedule extends Redisbase{
    /*
    * �洢��������
    * $keyname ����
	* $data ��������
    * $database ������
	* $datatype �洢��������
	* $expiretime ��Ч�� ��λs
    */
	function setgameinfo($keyname, $data, $database, $datatype, $expiretime){
		$this->selectDB($database);
		if($datatype == "String"){
			//�ַ���
			$this->redis->set($keyname,serialize($data));
			$this->redis->expire($keyname,$expiretime);
			
		}else if($datatype == "Hashes"){
			;//��ϣֵ
			$this->redis->hmset($keyname,$data);
			
		}else if($datatype == "Lists"){
			;//�б�
			$this->redis->lpush($keyname,serialize($data));
		}else if($datatype == "Sets"){
			;//����
			
		}
		return true;
	}
	
	function getgameinfo($keyname, $database, $datatype, $hashkeys){
		$this->selectDB($source);
		if($datatype == "String"){
			return $this->redis->get($keyname);
		}else if($datatype == "Hashes"){
			;//��ϣֵ
			return $this->redis->hmget($keyname, $hashkeys);
		}else if($datatype == "Lists"){
			;//�б�;
			return $this->redis->lrange($keyname,0,-1);
		}else if($datatype == "Sets"){
			//����
			;
		}
		
	}
	/*
	*$type ����ΪFB ����BK
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
				   $val['h_status']=$temp['h_status']; //����ƽ̨���Ƶ�״̬  
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
		//��ǰ������redis�ⲻһ��ʱ��������
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
		//�Ƚ�2�������Ƿ�һ�� һ�·���true
		//if(!array_diff($a,$b) && !array_diff($b,$a)){
		//	return true;
		//}
		return false;	
	}
	/*
	*
	*��ȡ��ǰ����
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
	*��ȡ��ǰ����
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
	*��ȡ��ǰ����
	*
	*/
	function getHisSchedule($keyname){
		$arr =$this->redis->keys($keyname."*");
	    return $arr;
	}
	/*
	*
	*��ȡ��ǰ����
	*
	*/
	function delSchedule(){
	 ;
	}
	/*
	*
	*����ƽ̨����״̬
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
