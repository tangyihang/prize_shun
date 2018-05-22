<?php
include_once ROOT.'/Lib/Base.class.php';
class Jingcai2 extends Base{
    public $url;
    public $charset = 'GBK';
   	public $formatData  = array();
	public $opts = array('http'=>array('method'=>"GET",'timeout'=>10)); //超时10
	public $DB;
   function __construct($url='')
   {
     $this->url=$url;
   }
    function run($lottid=''){
		
		
		
		
		
		
		die("dddd");
		
		
		
		
		
		
		
		
		
		
		
        $this->gethtmldata();
		$val2=array();
		$filename=date("YmdH",time()).".log";
		$this->writeLog($filename,json_encode($this->formatData));
		echo "match total:".count($this->formatData)."\n";
		$sellNum=0;
		foreach ($this->formatData as $value) {
		   $val2['s_code']='FB';
                   print_r($value);
		   $tempintweek=getIntWeek(substr($value['ball'], 0, -3));
		   $tempdate = get_date_by_week($tempintweek,date('Y-m-d H:i:s',strtotime($value['gamestarttime'])));
		 if($tempdate=='20170119'){
                  $tempdate='20170126';
                 }  
                 if(time()-strtotime($tempdate) >3*3600*24){
                        continue;
                   }

                   $week=($tempintweek == '0') ? '7' : $tempintweek;
				   
				error_reporting(0)   ;
			 echo  "<hr>".$week.chr(13).chr(10)."<hr>";   
				   
				   
		   $val2['num']=$week.preg_replace('@.*(\d{3})@','\\1',$value['ball']);
		   $val2['date']=date('Y-m-d',strtotime($value['gamestarttime']));
		   $val2['time']=date('H:i:s',strtotime($value['gamestarttime']));
		   $val2['b_date'] = date('Y-m-d',strtotime($tempdate));
		   $val2['l_code'] = '';
		   $val2['l_id'] = '';
		   $val2['l_cn'] = $value['gamename'];
		   $val2['h_cn'] = $value['hteam'];
		   $val2['a_cn'] = $value['vteam'];
		   $val2['color'] = $value['bgcolor'];
		   $val2['id'] = $value['mid'];
		  if($value['status']=='Selling' || $value['status']=='Close')
		   { 
	           $val2['status'] = $value['status'];
			   print_r($val2);
	           $this->saveToDb($val2);
			   $sellNum++;
			   echo $sellNum;
		   }
		}
		echo "Selling match total:".$sellNum."\n";
   }
    //
   function gethtmldata(){
       $html=file_get_contents($this->url,false, stream_context_create($this->opts));
       if($this->charset != 'UTF-8'){
	       $html = mb_convert_encoding($html, 'UTF-8','gb2312');
	   }
        /*$parten='/<div class=\"match_list\">.*<table width=\"100%\".*>(.*)<\/table>/iUs';
    	preg_match_all($parten,trim($html),$match_html);
		$parten='/<table width=\"100%\".*>(.*)<\/table>/iUs';
    	preg_match_all($parten,trim($match_html[0][1]),$match);
		*/
		$parten='/<tr windex=\"\d+\" lindex=\"\d+\".*>(.*)<\/tr>/iUs';
    	preg_match_all($parten,trim($html),$match_html);
		
    	$parten='/<tr.*>(.*)<\/tr>/iUs';
    	$parten_td = "/<td.*>(.*)<\/td>/iUs";
    	$parten_span="/<span.*>(.*)<\/span>/iUs";
    	$parten_a="/<a.*>(.*)<\/a>/iUs";
		$parten_str = "'<[\/\!]*?[^<>]*?>'si";
		$parten_div="/<div.*>(.*)<\/div>/iUs";
		$parten_font="/<font.*>(.*)[+,-]{1}.*<\/font>/iUs";
		
    	preg_match_all($parten,$match[0][0],$match2);
    	$jingcaiarr=array();
    	$i=0;
    	foreach($match_html[0] as $key=>$val){
			
    		preg_match_all($parten_td, $val, $matchtd);
			
    		if(count($matchtd[0])==12){
				
    		    $temp=$matchtd[0];
				
    		    $jingcaiarr[$i]['ball']=trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[0]));
    		    //$jingcaiarr[$i]['gamename']=$temp[1];//trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[1]));
    		    $jingcaiarr[$i]['s_code']='fb';
    		    $jingcaiarr[$i]['gamename']=strip_tags($temp[1]);
				$jingcaiarr[$i]['bgcolor']=preg_replace('@.*bgcolor=\"#([0-9a-fA-F]{6}).*@','\\1',$temp[1]);
				
				$teamstr=trim(strip_tags($temp[2]));
				$teamstr=str_replace("\n","",$teamstr);
				$teamstr=str_replace(" ","",$teamstr);
				$teamnamearr=explode('^',str_replace("VS","^",$teamstr));
				$jingcaiarr[$i]['hteam']=$teamnamearr[0];
				$jingcaiarr[$i]['vteam']=$teamnamearr[1];
				preg_match_all("/m=\d+/is", $temp[4], $midarr);
				
				if(preg_replace('@^m=(\d+)$@','\\1',$midarr[0][0])==preg_replace('@^m=(\d+)$@','\\1',$midarr[0][1])){
					$jingcaiarr[$i]['mid']=preg_replace('@^m=(\d+)$@','\\1',$midarr[0][0]);
				}
				
				
    		    $jingcaiarr[$i]['gamestarttime']=date('YmdHis',strtotime(trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[3]))));
			    $status='';
				if(iconv('UTF-8','gbk//IGNORE',trim(strip_tags($temp[5])))=="已开售"){
					$status='Selling';
				}
				if(iconv('UTF-8','gbk//IGNORE',trim(strip_tags($temp[5])))=="停售"){
					$status='Close';
				}
				
				$jingcaiarr[$i]['status'] = $status;
    		    $i++;
    		}
    	}
		$this->formatData=$jingcaiarr;
   }
   function saveToDb($arr)
   {
      $filename="m_".date("YmdH",time()).".log";
	  $this->writeLog($filename,json_encode($arr));
		
	  $sql="select * from fb_betting where b_date='".$arr['b_date']."' and num='".$arr['num']."'";
	 
      $num =$this->DB->execute($sql);
      error_log($sql.chr(13).chr(10),3,'sql0806.log');
      error_log('num='.$num.chr(13).chr(10),3,'sql0806.log');
	  
      if($num>0){
      	$where=array(
	      	 'b_date'=>$arr['b_date'],
	      	 'num'=>$arr['num'],
      	);
      	$sql="update fb_betting set date='".$arr['date']."',time='".$arr['time']."',status='".$arr['status']."' WHERE status!='Final' and b_date='".$arr['b_date']."' and num='".$arr['num']."' and index_show=0";
        error_log($sql.chr(13).chr(10),3,'sql0806.log');
        $res =$this->DB->execute($sql);
	    if($res){
			echo "update success! \n";
		}
      }else{
   	    $res=$this->DB->data($arr)->table("fb_betting")->add();
        if($res){
			echo "new add match data to DBbase success \n";
		}else{
			echo $this->DB->error();
			echo "error";
		}
	  }
   }
   function updateGameStatus(){
	   $sql="update fb_betting set status='Final' where status!='Reback' and date='".date('Y-m-d',time())."' and time<'".date('H:i:s',time())."'";
       $res =$this->DB->execute($sql);
       if($res){
		   echo "update status ".$res."success!";
	   }else{
		   echo "no update status ";
	   }	   
   }
}
