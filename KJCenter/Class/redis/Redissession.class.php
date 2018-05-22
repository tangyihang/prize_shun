<?php
/**
  *Redis��ȡ
  *PaulHE
**/
include "Redisbase.class.php";
class Redissession extends Redisbase{
   /*
   *����session
   *$expiretime ��Ч�� ��λs
   *$username �û���
   *$source ��Դ��ַ{ios,android,touch} 
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
   *��ȡsession
   */
   function getSession($sessionname,$source){
	   $this->selectDB($source);
	   return unserialize($this->redis->get($sessionname));
   }
   /*
   *���session
   */
   function delSession($sessionname){
	  return $this->redis->del($sessionname);
   }
   /*
   *У��session
   */
   function authSession($sessionname){
	   if($this->redis->ttl($sessionname)==-2 || $this->redis->ttl($sessionname)==-1){
		   return false;
	   }
	   return true;
   }
}
?>