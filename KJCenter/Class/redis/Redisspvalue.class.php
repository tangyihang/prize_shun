<?php
/**
 * @���� ����SPֵRedis�洢
 * @author PaulHE
 * @date 2016.07
 * */
include "Redisbase.class.php";
class Redisspvalue extends Redisbase{
    
	public function setSp($keyname,$data,$expiretime='3600',$datatype="String"){
		$this->redis->select(6);
		foreach($data as $key=>$val){
		  $idx=$keyname."_".$val['lotttime']."_".$val['ballid'];
		  if($this->isExist($idx,$val)){
			 $val['addtime']=date('Y-m-d H:i:s',time());
			 $this->redis->lpush($idx,serialize($val));  
		 } 	
		}
		//$this->redis->set($keyname,serialize($data));
		//print_r($data);
		//$this->redis->hmset($keyname,$data);
	}
	/*
	*�жϵ�ǰץȡ��������redis���ݿ���ĵ�һ���Ƿ�һ�£������һ��list�����һ������
	*/
	function isExist($key,$val){
		if(!$this->redis->exists($key)){
			return true;  
		}
		$arr=unserialize($this->redis->lGet($key,0));
		unset($arr['addtime']);
		if(serialize($arr)==serialize($val)){
			error_log('22222'.chr(13).chr(10),3,'122333.log');
			return false;
		}else{
		    return true;
		}
		
	}
	/*
	*��ȡ���µ���spֵ
	*/
	function getSp($keyname){
		$this->redis->select(5);
	}
	/*
	*��ȡ����spvalue
	*/
	function getSpAll($keyname){
		$this->redis->set();
	}
	function delSp($keyname){
		
		$this->redis->del($keyname);
	}
}
?>