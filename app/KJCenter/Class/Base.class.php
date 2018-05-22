<?php
class Base{
	public $charset='utf-8';
	public $lotterytype='jc';
	public $logdir="";
	function getData($filename){
		return file_get_contents($filename);
	}
	//
	function makehtml2($html){
       
    	if($this->charset != 'UTF-8'){
	       $html = mb_convert_encoding($html, 'UTF-8','gb2312');
		}
	
    	$parten='/<table.*>(.*)<\/table>/iUs';
    	preg_match_all($parten,trim($html),$match);
    	$parten='/<tr.*>(.*)<\/tr>/iUs';
    	$parten_td = "/<td.*>(.*)<\/td>/iUs";
    	$parten_span="/<span.*>(.*)<\/span>/iUs";
    	$parten_a="/<a.*>(.*)<\/a>/iUs";
		$parten_str = "'<[\/\!]*?[^<>]*?>'si";
		$parten_div="/<div.*>(.*)<\/div>/iUs";
		$parten_font="/<font.*>(.*)[+,-]{1}.*<\/font>/iUs";
		  
    	preg_match_all($parten,trim($match[0][0]),$match2);
    	$i=0;
    	$lancaiarr=array();
    	foreach($match2[0] as $key=>$val){
    	  preg_match_all($parten_td, $val, $matchtd);
    	  if(count($matchtd[0])==17){
    	  	   $temp=$matchtd[0];
    	  	   $key2=$key+1;
    	  	   preg_match_all($parten_td, $match2[0][$key2], $matchtd3);
    	  	   $temp=array_merge($temp,$matchtd3[0]);
    	  	  $temp2=array();
    	      foreach($temp as $key3=> $val2){
    		  	  	  if($key3==3 || $key3==18){
    		  	  	    $str=$val2;
    		  	  	  }else{
    		  	  	    $str=strip_tags($val2);
    		  	  	  }
    		  	  	    $str=str_replace("\n","",$str);
    		  	  	    $str=str_replace("\r","",$str);
    		  	  	    $str=str_replace("  ","",$str);
    		  	  	    $str = trim($str); 
    		  	        $temp2[$key3]= $str;
    		  	  }
    		 
    		  $newresult[$i]['ballid']=preg_replace('@.*<span>(.*)<\/span>.*@', '\\1', $temp2[0]);
    	      $newresult[$i]['gamename']=$temp2[1];
    	      $newresult[$i]['status']=$temp2[2];
    	      $newresult[$i]['hteam']=preg_replace('@.*<a.*>(.*)<span.*>.*@','\\1',$temp2[18]);
    	      $newresult[$i]['vteam']=preg_replace('@.*<a.*>(.*)<span.*>.*@','\\1',$temp2[3]);
    	      $newresult[$i]['hteamlevel']=preg_replace('@.*<span.*>(.*)<\/span>.*@','\\1', $temp2[18]);
    	      $newresult[$i]['vteamlevel']=preg_replace('@.*<span.*>(.*)<\/span>.*@','\\1', $temp2[3]);
    	      $newresult[$i]['Homeone']=$temp2[19];
    	      $newresult[$i]['Hometwo']=$temp2[20];
    	      $newresult[$i]['Homethree']=$temp2[21];
    	      $newresult[$i]['Homefour']=$temp2[22];
    	      $newresult[$i]['Guestone']=$temp2[4];
    	      $newresult[$i]['Guesttwo']=$temp2[5];
    	      $newresult[$i]['Guestthree']=$temp2[6];
    	      $newresult[$i]['Guestfour']=$temp2[7];
    	      $newresult[$i]['Homeadd']=$temp2[23];
    	      $newresult[$i]['Guestadd']=$temp2[8];
    	      
    	      $newresult[$i]['Homescore']=$temp2[25];
    	      $newresult[$i]['Guestscore']=$temp2[10];
			}
			$i++;
    	}
    	//print_r($newresult);
        return $newresult;
    	
	}  
	function makehtml($html,$liveID){ 
    	if($this->charset != 'UTF-8'){
	       $html = mb_convert_encoding($html, 'UTF-8','gb2312');
		}
		  if($liveID==3){
			   $parten_aissue="/<a id=\"select_qihao\".*>(.*)<\/a>/iUs";
			   preg_match_all($parten_aissue,trim($html),$htmlmatch);
			   $issue=preg_replace('@(\d+).*@','\\1',$htmlmatch[1][0]);
		  }
		  error_log($issue.chr(13).chr(10),3,'abcd.log');
    	$parten='/<table.*>(.*)<\/table>/iUs';
    	preg_match_all($parten,trim($html),$match);
    	$parten='/<tr.*>(.*)<\/tr>/iUs';
    	$parten_td = "/<td.*>(.*)<\/td>/iUs";
    	$parten_span="/<span.*>(.*)<\/span>/iUs";
    	$parten_a="/<a.*>(.*)<\/a>/iUs";
		$parten_str = "'<[\/\!]*?[^<>]*?>'si";
		$parten_div="/<div.*>(.*)<\/div>/iUs";
		$parten_font="/<font.*>(.*)[+,-]{1}.*<\/font>/iUs";
		  
    	preg_match_all($parten,trim($match[0][0]),$match2);
    	$i=0;
    	foreach($match2[0] as $val){
    	  preg_match_all($parten_td, $val, $matchtd);
    	  if(count($matchtd[0])==13){
    	         $temp=array();
    		  	  foreach($matchtd[0] as $key=> $val2){
    		  	  	  if($key==7 || $key==4 ||$key==6 ||$key==8){
    		  	  	    $str=$val2;
    		  	  	  }else{
    		  	  	    $str=strip_tags($val2);
    		  	  	  }
    		  	  	    $str=str_replace("\n","",$str);
    		  	  	    $str=str_replace("\r","",$str);
    		  	  	    $str=str_replace("  ","",$str);
    		  	  	    $str = trim($str); 
    		  	        $temp[$key]= $str;
    		  	  }
    		  	 //print_r($temp);
    		  	 
    		  	 $newresult[$i]['Ballid']=$temp[0];
    		  	
    		  	 $newresult[$i]['Lotteryissue']=isset($issue) ? $issue :20000;
    		  	 $newresult[$i]['Sclassname']=$temp[1];
    		  	 $newresult[$i]['Color']=$this->getbgcolor($temp[1]);
    		  	 $newresult[$i]['matchtime']=date('Y-m-d H:i:s',strtotime(date('Y').'-'.$temp[2]));
    		  	 $newresult[$i]['Lotttime']=date('Ymd',strtotime(date('Y').'-'.$temp[2])-12*60*60);
				  $week=date('w',strtotime($newresult[$i]['Lotttime']));
    		  	  if($week==0){
    		  	    $week=7;
    		  	  }
				 $newresult[$i]['Lotteryweek']=$week;
    		  	 
    		  	 preg_match_all($parten_span, $temp[4], $matchspan);
    		  	 preg_match_all($parten_a, $temp[4], $matcha);
    		  	 
    		  	 $newresult[$i]['Homename']=$matcha[1][0];
    		  	 preg_match_all($parten_span, $temp[6], $vmatchspan);
    		  	 preg_match_all($parten_a, $temp[6], $vmatcha);
    		  	 $newresult[$i]['Guestname']=$vmatcha[1][0];
    		  	 
    		  	 $newresult[$i]['Homeorder']=preg_replace('@\[(.*)\]@','\\1',$matchspan[1][1]);
    		  	 $newresult[$i]['Letgoal']=intval(preg_replace('@\((.*)\)@','\\1',$matchspan[1][2]));
    		  	 $newresult[$i]['Guestorder']=preg_replace('@\[(.*)\]@','\\1',$vmatchspan[1][1]);
    		  	 
    		  	
    		  	 $newresult[$i]['Status']=$this->getstatus($temp[3]);
    		  	 if($this->getstatus($temp[3])==1 || $this->getstatus($temp[3])==3)
    		  	 {
    		  	 	 echo intval($temp[3]);
    		  	 	 $newresult[$i]['matchMin']=intval($temp[3]);
    		  	 }
    		  	 $scorehalf=explode('-', $vmatchspan[1][2]);
    		  	 $scorefull=explode('-', $temp[5]);
    		  	 $newresult[$i]['Homescore']=$scorefull[0];
    		  	 $newresult[$i]['Guestscore']=$scorefull[1];
    		  	 $newresult[$i]['Homehalfscore']=$scorehalf[0];
    		  	 $newresult[$i]['Guesthalfscore']=$scorehalf[1];
    		  	
    		  	 //$newresult[$i]['halfscore']=$vmatchspan[1][2];
    		  	 //$newresult[$i]['fullscore']=$temp[5];
    		  	 //$newresult[$i]['weather']=
    		  	 $temp[7]=preg_replace('@<td.*>(.*)<\/td>@iUs','\1',$temp[7]);
    		  	 $temp[7]=strlen($temp[7])>10 ? $temp[7]:'';
    		  	 $newresult[$i]['Weather']=trim(preg_replace('@<img src=.* title=.* alt=\"(.*)\" \/>@iUs','\\1',$temp[7])); 
    		  	 switch ($newresult[$i]['Weather']){
    		  	 	 case "多云":
    		  	     $newresult[$i]['WeatherICON']=4; 
    		  	     break;
    		  	     case "晴":
    		  	     $newresult[$i]['WeatherICON']=1; 
    		  	     break;
    		  	 	 default:
    		  	 	 $newresult[$i]['WeatherICON']=''; 
    		  	 }
    		  	 //$newresult[$i]['weathericon']=$weathericon;
    		  	 //print_r($temp[8]);
    		  	 $temp[8]=preg_replace('@<td.*>(.*)<\/td>@iUs','\1',$temp[8]);
    		  	 
    		  	 preg_match_all($parten_span, $temp[8], $spspan);
    		  	 
    		  	 $newresult[$i]['WinSp']=$spspan[1][0];	 	
    		  	 $newresult[$i]['FlatSp']=$spspan[1][1];	
    		  	 $newresult[$i]['FailSp']=$spspan[1][2];
    		  	 
    	  }
    	  $i++;
    	  //if($i==2){
    	  //  break;
    	  //}
    	}
    	//print_r($newresult);
    	return  $newresult; 	
	}
	/*比分状态  比赛状态 0:未开,1:
          上半场,2:中场,3:下半场,4,加时，-11:待定,-12:腰斩,-13:中断,-14:推迟,-1:完场，-10取消
	*/
	function getstatus($str)
	{
		$status=0;
		if($str=='未')
		{
			$status=0;
		}
		if($str=='完')
		{
			$status=-1;
		}
		if($str=='中')
		{
			$status=2;
		}
		if($str=='加')
		{
			$status=4;
		}
		if(intval($str)>0 && intval($str) <= 45)
		{
			$status=1;
		}
		if(intval($str)>45 && intval($str) < 90)
		{
   	     $status=3;
		}
		return $status;
	}
	//
	function getbgcolor($str)
	{
		$bgcolor='#339966';
		$arr=array('澳超'=>'#3F1C10','土超'=>'#9F4F87','阿甲'=>'#7F4E27','德乙'=>'#DB31EE','意甲'=>'#0066FF',
			'墨联'=>'#339966','西甲'=>'#006633','西乙'=>'#438E0B','葡甲'=>'#DB31EE','荷甲'=>'#FF6699',
			'足总杯'=>'#EF0012',
			'瑞士超'=>'#1BA578',
			'马来超'=>'#000000',
			'法甲'=>'#663333',
			'英乙'=>'#DF9999',
			'希腊超'=>'#40146F',
			'比甲'=>'#FC9B0A',
			'比乙'=>'#7B2121',
			'奥甲'=>'#2F3FD2',
			'苏超'=>'#57A87B', 
			'德甲'=>'#990099',
			'荷乙'=>'#EF5D0E',
		);
		if(isset($arr[$str]))
		{
			$bgcolor=$arr[$str];
		}  
		return $bgcolor;
      
	}
	//写入日志文件
	function writeLog($filename,$content)
	{
	    error_log($content.chr(13).chr(10),3,$this->logdir.$filename);
	}
}
