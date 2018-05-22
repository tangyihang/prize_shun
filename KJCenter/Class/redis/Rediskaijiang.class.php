<?php
/**
 * @���� ����������ݴ洢
 * @author WYE
 * @date 2016.07
 * */
include "Redisbase.class.php";
class Rediskaijiang extends Redisbase{
	
	/*
    * ���²��� �洢��������
    * $keyname ����
	* $data ��������
	* $database ���ݱ�
	* $expiretime ��Ч�� ��λs
    */
	function setcomes($keyname, $data, $database, $expiretime){
		$this->redis->select($database);
		$salt = $this->redis->set($keyname,serialize($data));
		$salt2 = $this->redis->expire($keyname,$expiretime);
		if($salt && $salt2){
			return true;
		}else{
			return false;
		}
	}
	
	/*
    * ��ѯ���� �洢��������
    * $keyname ����
	* $database ���ݱ�
    */
	function getcomes($keyname, $database){
		$this->redis->select($database);
		return $this->redis->get($keyname);
	}
	
	/*
    * ɾ������ �洢��������
    * $keyname ����
	* $database ���ݱ�
    */
	function delcomes($keyname, $database){
		$this->redis->select($database);
		return $this->redis->del($keyname);
	}
	
	
}
?>