<?php
/**
 * @描述 缓存相关数据存储
 * @author WYE
 * @date 2016.07
 * */
include "Redisbase.class.php";
class Rediskaijiang extends Redisbase{
	
	/*
    * 更新操作 存储缓存数据
    * $keyname 键名
	* $data 操作数据
	* $database 数据表
	* $expiretime 有效期 单位s
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
    * 查询操作 存储缓存数据
    * $keyname 键名
	* $database 数据表
    */
	function getcomes($keyname, $database){
		$this->redis->select($database);
		return $this->redis->get($keyname);
	}
	
	/*
    * 删除操作 存储缓存数据
    * $keyname 键名
	* $database 数据表
    */
	function delcomes($keyname, $database){
		$this->redis->select($database);
		return $this->redis->del($keyname);
	}
	
	
}
?>