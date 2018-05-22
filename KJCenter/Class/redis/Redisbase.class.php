<?php
/**
  *Redis读取
  *PaulHE
**/
class Redisbase{
   public $HOST='locahost';
   public $PORT="6379";
   public $PWD="123456";
   public $redis;
   /*
   *初始化
   */
   function __construct($host="localhost",$pwd='',$port='6379'){
	  $this->HOST=$host;
	  $this->PWD=$pwd;
	  $this->PORT=$port;
	  $this->connectRedis();  
   }
    /*
   *连接redis
   */
   function connectRedis(){
	   $this->redis = new Redis();
	   $con=$this->redis->connect($this->HOST,$this->PORT);
	   if(!$con){
		   return false;
	   }else{
		 $auth=$this->redis->auth($this->PWD);
         if(!$auth){
			 return false;
		 }else{
			 return true;
		 }		 
	   }
   }
   /*
   *选择数据库
   */
   function selectDB($source=''){
		if($source=='ios'){
			$this->redis->select(0);
			return true;
		}
		if($source=='android'){
			$this->redis->select(1);
			return true;		 
		}
		if($source=='touch'){
			$this->redis->select(2);
			return true;		 
		}
		if($source=='match'){
			$this->redis->select(0);
			return true;		 
		}
		$this->redis->select(0);
   }
   /*
   *生成日志
   */
   function writelog($str){
	   $filename=date('Ymdh').".log";
	   error_log($logstr.chr(13).chr(10),3,$filename);
   }
   /*
   *
   */
   function randStr($len=6,$format='ALL') { 
	 switch($format)
	 { 
		 case 'ALL':
		 $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~'; break;
		 case 'CHAR':
		 $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~'; break;
		 case 'NUMBER':
		 $chars='0123456789'; break;
		 default :
		 $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~'; 
		 break;
	 }
	 mt_srand((double)microtime()*1000000*getmypid()); 
	 $password="";
	 while(strlen($password)<$len)
		$password.=substr($chars,(mt_rand()%strlen($chars)),1);
	 return $password;
  }
}
?>