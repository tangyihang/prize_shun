<?php
/**
  *Redis╤ах║
  *PaulHE
**/
class Rediscache{
   function saveData($name,$arr){
	   $this->redis->hmset($name,$arr);
   }
   function saveMysql(){
	   ;
   }
   function saveOracle(){
	   ;
   }
  
}
?>