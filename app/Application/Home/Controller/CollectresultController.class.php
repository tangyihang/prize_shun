<?php
namespace Home\Controller;
use Think\Controller;
class CollectresultController extends Controller {
	public function _initialize(){
		;
	}
    public function index()
    {
        
    }
    
    public function fromJincainet(){
    	$starttime=microtime(get_as_float);
    	$url="http://info.sporttery.cn/livescore/iframe/realtime.php?type=fbdata";
    	$html = file_get_contents($url);
    	$pattern='/A\[\d+\]=".*"/';
    	preg_match_all($pattern, $html, $matches);
    	$newresult=array();
    	foreach($matches[0] as $key => $val)
    	{
    		$val=preg_replace('@A\[\d+\]="(.*)"@','\\1',$val);
    		$newresult[]=explode('^',$val);
    	}
    	
      foreach($newresult as $val){
      	
      	$idata=array();
      	$idata['lotteryid']='';
      	$idata['lotttime']=$val[51];
      	$idata['ballid']=$val[51];
      	$idata['source']='jincainet';
      	$idata['half_score']=$val[16].":".$val[17];
      	$idata['full_score']=$val[14].":".$val[15];
      	$idata['match_starttime']=date('Y-m-d H:i',strtotime(date('Y',strtotime($val[50]))."-".$val[36]." ".$val[11]));
      	$idata['lotttime']=date('Y-m-d',strtotime($idata['match_starttime'])-12*3600);
      	if($val[14] > $val[15])
      	{
      	    $idata['result']=3;
        }
        if($val[14] == $val[15])
      	{
      	    $idata['result']=1;
        }
        if($val[14] < $val[15])
      	{
      	    $idata['result']=0;
        }
      	$idata['status']=$val[13];
      	
      	if($idata['status']=='-1'){
      	   $this->saveToDB($idata);
        }
      }
    	echo microtime(get_as_float)-$starttime;
    }
    //from okooo sporttery.cn
    public function fromOkooonet(){
    	$datestr=$_GET['date'];
    	$starttime=microtime(get_as_float);
    	$date_arr=array();
    	if($datestr==''){
    	   $url="http://www.okooo.com/livecenter/jingcai/";
      }else{
      	 $url="http://www.okooo.com/livecenter/jingcai/?date=".$datestr;//查询历史开奖数据
      }
      
      $opt=array('http'=>array('header'=>"Referer: $refer,HTTP_CONNECTION:$HTTP_CONNECTION,HTTP_CACHE_CONTROL:'max-age=0'"));
      $context=stream_context_create($opt);
      $html = file_get_contents($url);
    	
    	if($this->charset != 'UTF-8'){
			   $html = mb_convert_encoding($html, 'UTF-8','gb2312');
		  }
	
    	$parten='/<table.*>(.*)<\/table>/iUs';
    	preg_match_all($parten,trim($html),$match);
    	//print_r($match);
    	$parten='/<tr.*>(.*)<\/tr>/iUs';
    	$parten_td = "/<td.*>(.*)<\/td>/iUs";
		  $parten_str = "'<[\/\!]*?[^<>]*?>'si";
		  $parten_div="/<div.*>(.*)<\/div>/iUs";
		  $parten_font="/<font.*>(.*)[+,-]{1}.*<\/font>/iUs";
		  
    	//print_r($match[0][0]);
    	preg_match_all($parten,trim($match[0][0]),$match2);
    	//print_r($match2[0]);
    	$newresult=array();
    	$i=0;
    	foreach($match2[0] as $val){
    		 
    		  preg_match_all($parten_td, $val, $matchtd);
    		  if(count($matchtd[0])==13){
    		  	  $temp=array();
    		  	  foreach($matchtd[0] as $key=> $val2){
    		  	  	   $str=strip_tags($val2);
    		  	  	    $str=str_replace("\n","",$str);
    		  	  	    $str=str_replace("\r","",$str);
    		  	  	    $str=str_replace("  ","",$str);
    		  	  	    $str = trim($str); 
    		  	       $temp[$key]= $str;
    		  	  }
    		  	  if(preg_match('/完/',$temp[3])){
    		  	  $newresult[$i]['lotteryid']='';
    		  	  $newresult[$i]['source']='fromOkooo';
    		  	  $newresult[$i]['half_score']=str_replace('-',':',preg_replace('/.*(\d+-\d+)/','\\1',$temp[6]));
    		  	  $newresult[$i]['full_score']=str_replace('-',':',$temp[5]);
    		  	  $newresult[$i]['match_starttime']=date('Y-m-d H:i',strtotime(date('Y')."-".$temp[2]));
    		  	  $newresult[$i]['lotttime']=date('Y-m-d',strtotime($newresult[$i]['match_starttime'])-12*3600);
    		  	  $week=date('w',strtotime($newresult[$i]['lotttime']));
    		  	  if($week==0){
    		  	    $week=7;
    		  	  }
    		  	  $newresult[$i]['ballid']=$week.$temp[0];
    		  	  $newresult[$i]['result']=$temp[9];
    		  	  $newresult[$i]['status']=-1;
    		  	  $i++;
    		  	  } 
    		  }
    	}
    	print_r($newresult);
    	foreach($newresult as $val){
    		 $this->saveToDB($val);
    	}
    
    	//var_dump($newresult);
    	echo microtime(get_as_float)-$starttime;
    } 
    //
    public function from500(){
    	$result_arr=array('胜'=>3,'平'=>1,'负'=>0);
    	$datestr=$_GET['date'];
    	$starttime=microtime(get_as_float);
    	$date_arr=array();
    	if($datestr==''){
    	   $url="http://live.500.com/";
      }else{
      	 $url="http://live.500.com/?e=".$datestr;//查询历史开奖数据
      }
      
      $refer="http://live.500.com/";
			$UserAgent = "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36";
			$opt=array('http'=>array('header'=>"Referer: $refer,User-Agent:$UserAgent"));
			$context=stream_context_create($opt);
		
    	$html = file_get_contents($url,false, $context);

    	if($this->charset != 'UTF-8'){
			   $html = mb_convert_encoding($html, 'UTF-8','gb2312');
		  }
    	$parten='/<tbody.*>(.*)<\/tbody>/iUs';
    	preg_match_all($parten,trim($html),$match);
    	$parten='/<tr.*>(.*)<\/tr>/iUs';
    	$parten_td = "/<td.*>(.*)<\/td>/iUs";
		  $parten_str = "'<[\/\!]*?[^<>]*?>'si";
		  $parten_div="/<div.*>(.*)<\/div>/iUs";
		  $parten_font="/<font.*>(.*)[+,-]{1}.*<\/font>/iUs";
		  
    	//print_r($match[0][0]);
    	preg_match_all($parten,trim($match[0][0]),$match2);
    	$newresult=array();
    	$i=0;
    
    	
    	foreach($match2[0] as $val){
    		 
    		  preg_match_all($parten_td, $val, $matchtd);
    		  if(count($matchtd[0])==13){
    		  	  $temp=array();
    		  	  foreach($matchtd[0] as $key=> $val2){
    		  	  	   $str=strip_tags($val2);
    		  	  	    $str=str_replace("\n","",$str);
    		  	  	    $str=str_replace("\r","",$str);
    		  	  	    $str=str_replace("  ","",$str);
    		  	  	    $str = trim($str); 
    		  	       $temp[$key]= $str;
    		  	  }
    		  	  if(preg_match('/完/',$temp[4])){
    		  	  $ballid_tmp=$this->ballidto($temp[0]);	
    		  	  $newresult[$i]['lotteryid']='';
    		  	  $newresult[$i]['source']='500wan';
    		  	  $newresult[$i]['half_score']=preg_replace('/.*(\d+).*(\d+)/','\\1:\\2',$temp[8]);
    		  	  $newresult[$i]['full_score']=str_replace('-',':',preg_replace('/(\d+)(.*)(\d+)/','\\1-\\3',$temp[6]));
    		  	  $newresult[$i]['match_starttime']=date('Y-m-d H:i',strtotime(date('Y')."-".$temp[3]));
    		  	  $newresult[$i]['lotttime']=$ballid_tmp[0];
				  $newresult[$i]['ballid']=$ballid_tmp[1];
				  //$newresult[$i]['lotttime']=date('Y-m-d',strtotime($newresult[$i]['match_starttime'])-12*3600);
    		  	  //$week=date('w',strtotime($newresult[$i]['lotttime']));
    		  	  //if($week==0){
    		  	  //  $week=7;
    		  	  //}
    		  	  //$newresult[$i]['ballid']=$week.preg_replace('/.*(\d{3})/','\1',$temp[0]);
    		  	  $newresult[$i]['result']=$result_arr[$temp[10]];
    		  	  $newresult[$i]['status']=-1;
    		  	  $i++;
    		  	  } 
    		  }
    	}
   
    	foreach($newresult as $val){
    		$this->saveToDB($val);
    	}
    
    	//var_dump($newresult);
    	echo microtime(get_as_float)-$starttime;
    }
    /**
    *数据来源网易彩票比分直播
    *
    */
    public function from163(){
    	 $datestr=$_GET['date'];
    	$starttime=microtime(get_as_float);
    	$date_arr=array();
    	if($datestr==''){
    	   $url="http://live.caipiao.163.com/jcbf/";
      }else{
      	 $url="http://live.caipiao.163.com/jcbf/?date=".$datestr;//查询历史开奖数据
      }
      
      echo $url;
      
      $opt=array('http'=>array('header'=>"Referer: $refer,HTTP_CONNECTION:$HTTP_CONNECTION,HTTP_CACHE_CONTROL:'max-age=0'"));
      $context=stream_context_create($opt);
    	$html = file_get_contents($url);
    	
    	$partentime='/<a class=\"imitateSelect\" .*>(\d{4}-\d{2}-\d{2})<\/a><i><\/i>/iUs';
		  preg_match_all($partentime,trim($html),$pmatch);
		 // print_r($pmatch);
		  //exit;
		$starttime=$pmatch[1][0];
    	//echo $starttime;
    	$parten='/<dl.*>(.*)<\/dl>/iUs';
    	preg_match_all($parten,trim($html),$match);
    	
    	$parten='/<dd.*>(.*)<\/dd>/iUs';
    	$parten_span = "/<span.*>(.*)<\/span>/iUs";
		  $parten_str = "'<[\/\!]*?[^<>]*?>'si";
		  $parten_div="/<div.*>(.*)<\/div>/iUs";
		  $parten_font="/<font.*>(.*)[+,-]{1}.*<\/font>/iUs";
		  
    	preg_match_all($parten,trim($match[0][0]),$match2);
    	//print_r($match2[0]);
    	
    	foreach($match2[0] as $val){
    		 preg_match_all($parten_span, $val, $matchtd);
    		 $temp=array();
    		  	  foreach($matchtd[0] as $key=> $val2){
    		  	  	   $str=strip_tags($val2);
    		  	  	    $str=str_replace("\n","",$str);
    		  	  	    $str=str_replace("\r","",$str);
    		  	  	    $str=str_replace("  ","",$str);
    		  	  	    $str = trim($str); 
    		  	       $temp[$key]= $str;
    		  	  }
    		  //print_r($temp[2]);  
    		  if(preg_match('/已结束/',$temp[4])|| preg_match('/点球结束/',$temp[4]) || preg_match('/加时结束/',$temp[4])){
			    //echo $starttime;
				
			    $week=date('w',$starttime);
					  if($week==0){
						$week=7;
					  }
				$ballid_tmp=$this->ballidto($temp[0]);
				
					  $newresult[$i]['lotteryid']='';
					  $newresult[$i]['source']='from163';
					  $newresult[$i]['half_score']=$temp[8];
					  $newresult[$i]['full_score']=$temp[6];
					  $newresult[$i]['lotttime']=$ballid_tmp[0];
					  $timenum=intval(date('H',strtotime($temp[2])));
					  
					  if($timenum>12){
						$newresult[$i]['match_starttime']=date('Y-m-d H:i',strtotime($starttime." ".$temp[2]));
					  }else{
						$newresult[$i]['match_starttime']=date('Y-m-d H:i',24*3600+strtotime($starttime." ".$temp[2]));;
					  }
					  $newresult[$i]['ballid']=$ballid_tmp[1];
					  $newresult[$i]['result']=$result_arr[$temp[10]];
					  $newresult[$i]['status']=-1;
					  $i++;
    		  }
    	}	
    	foreach($newresult as $val){
    		//print_r($val);
    		$this->saveToDB($val);
    	}
    } 
    
     
    /*
    *
    */ 
     public function saveToDB($data){
     	 
       $Result=D('Result');
       if($data['lotttime']=='' || $data['ballid']=='' || $data['status']=='' ){
          return false;
          exit;
       }else{
             $ielement=array(
				          'lotteryid' => $data['lotteryid'],
				          'lotttime' => $data['lotttime'],
				          'ballid' =>  $data['ballid'],
				          'source' => $data['source'],
				          'half_score' => $data['half_score'],
				          'full_score' => $data['full_score'],
				          'match_starttime' => $data['match_starttime'],
				          'result' => $data['result'],
				          'addtime' => date('Y-m-d H:i:s',time()),
				          'status' => $data['status'],
           );
		  
            if($Result->add($ielement)){
               echo "success<br>";
            }else{
               echo "error<br>";
            }
       }
    }
	
  /*
  * 方法功能
  *把 周三001 转换转换成 3001 并推算出当前比赛的对阵时间
  *如：当前 3001 当天时间是2014-11-07 周五 即推算出3001的对阵时间为2014-11-05
  */
  
  public function ballidto($str){
  	$cnweekarr=array(0=>'周日',1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五',6=>'周六');
  	$cnweek=preg_replace('@\d+@','',$str);

  	
  	foreach($cnweekarr as $key=> $val){
  	  if($val==$cnweek){
  	     $week=$key;	
  	  }	
  	}
  	$ballid=$week.preg_replace('@^.*(\d{3})$@','\\1',$str);
  	//这里只能推算已经结束的比赛
    if(date('w',time())-$week >=0){
       $days=date('w',time())-$week;
    }
    if(date('w',time())-$week <0){
       $days=date('w',time())+7-$week;
    }
    $lotttime=date('Y-m-d',time()-$days*24*3600);
    if($week==0){
  		$week=7;
  	}
  	$ballid=$week.preg_replace('@^.*(\d{3})$@','\\1',$str);
    return array($lotttime,$ballid);
  }
}
