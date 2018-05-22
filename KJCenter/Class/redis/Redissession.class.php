<?php
/**
  *Redis读取
  *PaulHE
**/
include "Redisbase.class.php";
class Redissession extends Redisbase{
   /*
   *设置session
   *$expiretime 有效期 单位s
   *$username 用户名
   *$source 来源地址{ios,android,touch} 
   */
   function setSession($expiretime,$username,$source){
	   $sessionid = md5(time().$username.time().$this->randStr(6));
	   $sessionname="session_".md5($username);
	   $data=array('name'=>$username,'lastlogintime'=>date('Ymdhis',time()),'sessionname'=>$sessionid);
	   $this->selectDB($source);
	   if(!$this->redis->exists($sessionname)){
		   $this->redis->set($sessionname,serialize($data));
		   $this->redis->expire($sessionname,$expiretime);
		   return $sessionname;
	   }else{
		   $this->setSession($expiretime,$username,$source);
	   }
   }
   /*
   *获取session
   */
   function getSession($sessionname,$source){
	   $this->selectDB($source);
	   return unserialize($this->redis->get($sessionname));
   }
   /*
   *清除session
   */
   function delSession($sessionname){
	  return $this->redis->del($sessionname);
   }
   /*
   *校验session
   */
   function authSession($sessionname){
	   if($this->redis->ttl($sessionname)==-2 || $this->redis->ttl($sessionname)==-1){
		   return false;
	   }
	   return true;
   }
}
?>