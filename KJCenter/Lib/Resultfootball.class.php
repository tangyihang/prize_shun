<?php
/*
@author PaulHE
287568970@qq.com
*/
include_once ROOT.'/Lib/Base.class.php';
class Resultfootball extends Base{
    public $httpUrl;
    public $charset = 'GBK';
   	public $formatData  = array();
	public $DB;
   function __construct($url='')
   {
     $this->url=$url;
   }
   /*
   *www.500.com
   */
   //
    public function from500($datestr=''){
      $result_arr=array('胜'=>3,'平'=>1,'负'=>0);
      $date_arr=array();
      if($datestr==''){
         $url="http://live.500.com/";
      }else{
         $url="http://live.500.com/?e=".$datestr;//查询历史开奖数据
      }
	  
      $ch=curl_init();
      $header = array(
           'Referer:http://live.500.com/lq.php?c=jc', 
           'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.89 Safari/537.36', 
      );
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_ENCODING, "gzip");
      $html = curl_exec($ch);
      curl_close($ch);
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
              var_dump($temp);
              if(preg_match('/完/',$temp[4])){
              
  
              $ballid_tmp=$this->ballidto($temp[0]);
               
              $newresult[$i]['lotteryid']='';
              $newresult[$i]['source']='500wan';
              $newresult[$i]['half_score']=preg_replace('/.*(\d+).*(\d+)/','\\1:\\2',$temp[8]);
              $newresult[$i]['full_score']=str_replace('-',':',preg_replace('/(\d+)(.*)(\d+)/','\\1-\\3',$temp[6]));
              $newresult[$i]['match_starttime']=date('Y-m-d H:i',strtotime(date('Y')."-".$temp[3]));
              $newresult[$i]['lotttime']=$ballid_tmp[0];
              $newresult[$i]['ballid']=$ballid_tmp[1];
        
              $newresult[$i]['result']=$result_arr[$temp[10]];
              $newresult[$i]['status']=-1;
              $i++;
              } 
          }
      }
      foreach($newresult as $val){
        $this->saveToDB($val);
      }
    }
   /*
   *
   *caipiao.163.com
   *
   */
   function from163($datestr=''){
    	if($datestr==''){
    		$url="http://live.caipiao.163.com/jcbf/";
    	}else{
    		$url="http://live.caipiao.163.com/jcbf/?date=".$datestr;
    	}
	  $refer="http://live.caipiao.163.com/";
	  $HTTP_CONNECTION="keep-alive";
	  $opt=array('http'=>array('header'=>"Referer: $refer,HTTP_CONNECTION:$HTTP_CONNECTION,HTTP_CACHE_CONTROL:'max-age=0'"));
      $context=stream_context_create($opt);
    	$html = file_get_contents($url);
        if($html){
    	$partentime='/<a class=\"imitateSelect\" .*>(\d{4}-\d{2}-\d{2})<\/a><i><\/i>/iUs';
		preg_match_all($partentime,trim($html),$pmatch);
		$starttime=$pmatch[1][0];
    	$parten='/<dl.*>(.*)<\/dl>/iUs';
    	preg_match_all($parten,trim($html),$match);
    	
    	$parten='/<dd.*>(.*)<\/dd>/iUs';
    	$parten_span = "/<span.*>(.*)<\/span>/iUs";
		  $parten_str = "'<[\/\!]*?[^<>]*?>'si";
		  $parten_div="/<div.*>(.*)<\/div>/iUs";
		  $parten_font="/<font.*>(.*)[+,-]{1}.*<\/font>/iUs";
		  
    	preg_match_all($parten,trim($match[0][0]),$match2);
        $newresult=array(); 
        $i=0;		
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
    		 if(preg_match('/已结束/',$temp[4])|| preg_match('/点球结束/',$temp[4]) || preg_match('/加时结束/',$temp[4])){
				
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
					  $newresult[$i]['result']='';
					  $newresult[$i]['status']=-1;
					  $i++;
    		  }
    	}
		if($newresult){
			foreach($newresult as $val){
				  $this->saveToDB($val);
			}
		}
		}
    
   }
   /*
   *
   *www.sporttery.cn
   *
   */
   function fromjcw(){
		// 
		if($this->httpUrl == ''){
			echo 1;
		}
		//
		$opts = array('http'=> array('method' => 'GET', 'timeout' => 10));
		$context = stream_context_create($opts);
		$httpContent = file_get_contents($this->httpUrl, false, $context);
		if(!$httpContent){
			return ;
		}
		// 
		if($this->charset != 'UTF-8'){
			$httpContent = mb_convert_encoding($httpContent, 'UTF-8', $this->charset);
		}
		$trs_preg = "/<tr.*>(.*)<\/tr>/iUs";
		$trarr = array();
		preg_match_all($trs_preg, $httpContent, $trarr);
		$tds_preg = "/<td.*>(.*)<\/td>/iUs";
		$td_list = array();
	
		foreach($trarr[1] as $tr)
		{
			preg_match_all($tds_preg, $tr, $tds);
                        
			if(count($tds[1]) == 9 ||  count($tds[1])==10){
				if(count($tds[1])==9)
				{
                                    
			
					$tds[1][1]=strip_tags($tds[1][1]);
					$tdStr4=$tds[1][4];
					$tdStr5=$tds[1][5];
					$tdStr6=$tds[1][6];
					$tds[1][4]=strip_tags($tdStr4);
					$tds[1][5]=strip_tags($tdStr5);
					$tds[1][6]=strip_tags($tdStr6);
				    $td_list[] = $tds[1];
				}else{
					$td_list[] = $tds[1];
	     			}
                              
            
                                    
			}
		}
            
		foreach($td_list as $val){
			if($val[6] && preg_match("@已完成@",$val[6])){

		         list($lotttime,$ballid)=$this->ballidto($val[1]);	
			 $iarr=array(
			    'lotteryid'=>'',
				'lotttime' => $lotttime,
				'ballid' =>  $ballid,
				'source' => 'fromjcw',
				'half_score' => $val[4],
				'full_score' => $val[5],
				'addtime' => date('Y-m-d H:i:s',time()),
				'status' => -1,
				'result'=>'',
				'match_starttime'=>''
			 );
	      		
			 $this->saveToDB($iarr);
			 }
		}	
   }
   //okooo
   public function fromOkooonet($datestr=''){
    	$date_arr=array();
    	if($datestr==''){
    	   $url="http://www.okooo.com/livecenter/jingcai/";
        }else{
      	   $url="http://www.okooo.com/livecenter/jingcai/?date=".$datestr;//查询历史开奖数据
        }
      $refer="http://www.okooo.com/livecenter/jingcai/";
	  $HTTP_CONNECTION="keep-alive";
      $opt=array('http'=>array('header'=>"Referer: $refer,HTTP_CONNECTION:$HTTP_CONNECTION,HTTP_CACHE_CONTROL:'max-age=0'"));
      $context=stream_context_create($opt);
      $html = file_get_contents($url);
    	
    	if($this->charset != 'UTF-8'){
			   $html = mb_convert_encoding($html, 'UTF-8','gb2312');
		  }
	
    	$parten='/<table.*>(.*)<\/table>/iUs';
    	preg_match_all($parten,trim($html),$match);
  
    	$parten='/<tr.*>(.*)<\/tr>/iUs';
    	$parten_td = "/<td.*>(.*)<\/td>/iUs";
		$parten_str = "'<[\/\!]*?[^<>]*?>'si";
		$parten_div="/<div.*>(.*)<\/div>/iUs";
		$parten_font="/<font.*>(.*)[+,-]{1}.*<\/font>/iUs";
		  
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
    		  	  if(preg_match('/^完$/',$temp[3])){
			      $half_score = str_replace('-',':',preg_replace('/.*(\d+-\d+)/','\\1',$temp[6]));
				  $full_score = str_replace('-',':',$temp[5]);
				  if(!preg_match("@^\d+:\d+$@",$half_score) || !preg_match("@\d+:\d+@",$full_score)){
					  continue;
				  }		  
    		  	  $newresult[$i]['lotteryid']='';
    		  	  $newresult[$i]['source']='fromOkooo';
    		  	  $newresult[$i]['half_score'] = $half_score;
    		  	  $newresult[$i]['full_score'] = $full_score;
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
    }
   /*
   *
   */
   public function saveToDB($data){
          if($data['lotttime']=='' || $data['ballid']=='' || $data['status']=='' ){
                return false;
                exit;
            }else{
             $arr=array(
				'lotteryid' => $data['lotteryid']?$data['lotteryid']:0,
				'lotttime' => $data['lotttime'],
				'ballid' =>  $data['ballid'],
				'source' => $data['source'],       
				'half_score' => $data['half_score'],
				'full_score' => $data['full_score'],
				'match_starttime' => $data['match_starttime'],
				'result' => $data['result'],
				'addtime' => date('Y-m-d H:i:s',time()),
				'status' => $data['status']
            );
            $sql="select * from tab_lottery_result where lotttime='".$arr['lotttime']."' and ballid='".$arr['ballid']."' and source='".$arr['source']."'";
			$num =$this->DB->execute($sql);
	
		    if(!$num)
			{	
		       $res = $this->DB->data($arr)->table("tab_lottery_result")->add();
			}
        }
  }
    /*
     * 
     * */
    function strformat($str){
       
       $str=str_replace("\n","",strip_tags($str));
       $str=str_replace("\r","",$str);
       $str=str_replace("  ","",$str);
       $str = trim($str);
       return $str;
    }
    /*
    */
     public function ballidto($str){
  	$cnweekarr=array(0=>'周日',1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五',6=>'周六');
  	$cnweek=preg_replace('@\d+@','',$str);

  	
  	foreach($cnweekarr as $key=> $val){
  	  if(trim($val) == trim($cnweek)){
  	     $week=$key;	
  	  }	
  	}
 
  	//
    if(date('w',time())-$week >=0){
       $days=date('w',time())-$week;
    }
    if(date('w',time())-$week <0){
       $days=date('w',time())+7-$week;
    }
    if($week==0){
  		$week=7;
  	}
  	$ballid=$week.preg_replace('@^.*(\d{3})$@','\\1',$str);
    $lotttime=date('Y-m-d',time()-$days*24*3600);
   
    return array($lotttime,$ballid);
  }
}
