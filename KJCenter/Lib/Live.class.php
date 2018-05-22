<?php
include_once 'base.class.php';
class Live extends Base{
  public $filename;
  public $iscachefile=0; //
  public $cachefliepath='';
  public $datasource=array();
  public $liveID=1;
  function __construct($iscachefile=0,$cachefliepath='',$lotterytype='jc'){
       $this->iscachefile=$iscachefile;
       $this->cachefliepath=$cachefliepath;
       $this->lotterytype=$lotterytype;
  }
  
  function run(){
     $html=$this->getData($this->filename);
     if($this->lotterytype=='lc'){
        $this->datasource=$this->makehtml2($html);
     }else{
        $this->datasource=$this->makehtml($html,$this->liveID);
     } 
  }
  function set($filename)
  {
     $this->filename=$filename;
  }
  function cachedata(){
  ;
  }
  //同步文件
  function sendcachedata(){
    ;
  }
  //
  function tojson(){
    ;
  }
  //
  function toxml($config){
  	//print_r($this->datasource);
  	$str='';
  	$transactiontype=$this->liveID!=6 ? '255001' :'255001';
  	foreach($this->datasource as $val)
  	{
     $str.='<element><liveID>'.$this->liveID.'</liveID>';
  	  foreach($val as $key=>$val2){
  	     $str.='<'.$key.'>'.$val2.'</'.$key.'>';
  	  }
  	  $str.='</element>';
  	}
  	
  	$ielementxml='<body><elements>'.$str.'</elements></body>';
  	$timestamp=date('YmdHis',time());
  	//$messengerid=date('YmdHis',time()).uniqid();
  	$headerxml='<header><transactiontype>'.$transactiontype.'</transactiontype><agenterid>'.$config['agenterid'].'</agenterid></header>';
  	
  	$xml='<?xml version="1.0" encoding="utf-8"?><message version="1.0">'.$headerxml.$ielementxml.'</message>';
    return $xml;
  }
  //
  function send($config,$xml){
  	  $data=$xml;
  	  $url=$config['url'];
      $curl = curl_init();
	  curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
	  curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
	  curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
	  curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
	  curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
	  
	 //设置文件读取并提交的cookie路径
	  $tmpInfo = curl_exec($curl); // 执行操作
	  
	  if (curl_errno($curl)) {
	     echo 'Errno'.curl_error($curl);//捕抓异常
	  }
	  curl_close($curl); // 关闭CURL会话
	  return $tmpInfo; // 返回数据
  }
}