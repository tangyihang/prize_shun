<?php
/*
@author PaulHE
287568970@qq.com
*/
class Livebflc {
	public $httpUrl;
	public $charset = 'GBK';
	public $formatData  = array();
	public $opts = array('http'=>array('method'=>"GET",'timeout'=>10)); //超时10
	public $DB;
    
	function __construct($url='')
	{
		$this->url=$url;
	}
	/*
	*
	*/
	function fromOkooo($datestr=''){
	    if($datestr==''){
    		$url="http://www.okooo.com/jingcailanqiu/livecenter/";
    	}else{
    		$url="http://www.okooo.com/jingcailanqiu/livecenter/".$datestr;//查询历史开奖数据
    	}
    	$ch=curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	$html = curl_exec($ch);
    	curl_close($ch);
 
    	if($this->charset != 'UTF-8'){
			   $html = mb_convert_encoding($html, 'UTF-8','gb2312');
		  }
	
    	$parten='/<table.*>(.*)<\/table>/iUs';
    	preg_match_all($parten,trim($html),$match);
    	//print_r($match[0][0]);
    	
    	//print_r($match);
    	$parten='/<tr.*>(.*)<\/tr>/iUs';
    	$parten_td = "/<td.*>(.*)<\/td>/iUs";
		$parten_str = "'<[\/\!]*?[^<>]*?>'si";
		$parten_div="/<div.*>(.*)<\/div>/iUs";
		$parten_font="/<font.*>(.*)[+,-]{1}.*<\/font>/iUs";
		  
    	preg_match_all($parten,trim($match[0][0]),$match2);
    
    	$newresult=array();
    	$lantemplist=array();
    	$lantemplist2=array();
    	$i=0;
    	$n=0;
    	foreach($match2[0] as $val)
    	{
    	  preg_match_all($parten_td, $val, $matchtd);
    	  
    	  if(count($matchtd[0])==11 || count($matchtd[0])==16){
    	  	if(count($matchtd[0])==16){
    	  	 $lantemplist[$i]=$matchtd[0];
    	  	 $i++;
    	  	}
    	   if(count($matchtd[0])==11){
    	  	 $lantemplist2[$n]=$matchtd[0];
    	  	 $n++;
    	  	}
    	  }
    	}
    	$n=0;
    	$newlist=array();
    	for($i=0;$i<count($lantemplist);$i++)
    	{
    	  
		  $newlist[$i]['s_code']="BK"; 
		  $newlist[$i]['starttime']= date("Y-m-d H:i:s",strtotime(date("Y")."-".strip_tags($lantemplist2[$i][0])));
    	  $info=$this->ballidto($this->strformat($lantemplist[$i][0]),$newlist[$i]['starttime']);
		  $newlist[$i]['b_date'] = $info[0];   //$this->get_date_by_week();
    	  $newlist[$i]['m_num'] = $info[1];
		  $newlist[$i]['m_id']='';
		  $newlist[$i]['l_cn']=trim(strip_tags($lantemplist[$i][1]));
		  $newlist[$i]['h_cn']=str_replace("&nbsp;","",trim(strip_tags($lantemplist2[$i][1])));
		  $newlist[$i]['a_cn']=str_replace("&nbsp;","",trim(strip_tags($lantemplist[$i][3])));
		  
    	  $newlist[$i]['one']=intval($this->strformat($lantemplist[$i][4])).":".intval($this->strformat($lantemplist2[$i][2]));
    	  $newlist[$i]['two']=intval($this->strformat($lantemplist[$i][5])).":".intval($this->strformat($lantemplist2[$i][3]));
    	  $newlist[$i]['three']=intval($this->strformat($lantemplist[$i][6])).":".intval($this->strformat($lantemplist2[$i][4]));
    	  $newlist[$i]['four']=intval($this->strformat($lantemplist[$i][7])).":".intval($this->strformat($lantemplist2[$i][5]));
		  $hadd_score=$this->strformat($lantemplist[$i][8]) ? $this->strformat($lantemplist[$i][8]) : 0;
		  $vadd_score=$this->strformat($lantemplist2[$i][6]) ? $this->strformat($lantemplist2[$i][6]) :0;
		
    	  $newlist[$i]['add']=intval($hadd_score).":".intval($vadd_score);
		  
		  $newlist[$i]['full']=intval($this->strformat($lantemplist[$i][9])).":".intval($this->strformat($lantemplist2[$i][7]));
    	  $newlist[$i]['status']=strip_tags($lantemplist[$i][2]);
		  $this->saveLiveDB($newlist[$i]);
    	}
	}
	//抓取天气与红牌
	function fromjcw_wether_hongpai(){
		$url="http://i.sporttery.cn/api/match_live/get_match_list?callback=?&_=1467170033422";
		$ch=curl_init();
		
		$header = array(
				'Referer:http://info.sporttery.cn/livescore/fb_livescore.html', 
				'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.89 Safari/537.36', 
				);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT,5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$html = curl_exec($ch);
		$temp=explode("=",str_replace(';','',$html));
		$arr=json_decode(trim($temp[1]),true);
		
		$newArr=array();
		foreach($arr as $val){
			$newArr['s_code']='FB';
			$newArr['m_num']=$val['sort_num'];
			
			$week=preg_replace("@(\d{1})\d{3}@",'\\1',$val['sort_num']);
			$weekArr=array('1'=>'周一','2'=>'周二','3'=>'周三','4'=>'周四','5'=>'周五','6'=>'周六','7'=>'周日');
			$weekcn=$weekArr[$week];
			
			list($lotttime,$ballid)=$this->ballidto($weekcn.$val['sort_num']);
			
			$newArr['b_date']=$lotttime;
			$newArr['weather']=$val['weather'];
			$newArr['weather_city']=$val['weather_city'];
			$newArr['goalline']=$val['goalline'];
			$newArr['h_rc']=$val['h_rc'];
			$newArr['a_rc']=$val['a_rc'];
			$newArr['h_yc']=$val['h_yc'];
			$newArr['a_yc']=$val['a_yc'];
			
			$newArr['starttime']=date('Y-m-d H:i:s',strtotime($val['date_cn']." ".$val['time_cn'])); 
			$this->saveLivePlayedDB2($newArr); 
		}
	}
	/*
	*保存天气
	*/
	function saveLivePlayedDB2($data){
		if($data['m_num']=='' || $data['starttime']==''){  //starttime
			return false;
		}else{
			$arr=array(
				'weather' => $data['weather'],
				'weather_city' => $data['weather_city'],
				'goalline' => $data['goalline'],
				'h_rc' => $data['h_rc'],
				'a_rc' => $data['a_rc'],
				'h_yc' => $data['h_yc'],
				'a_yc' => $data['a_yc'],
			);

			$sql="select * from bk_result_org where m_num='".$data['m_num']."' and b_date='".$data['b_date']."'";
			$num =$this->DB->execute($sql);
			if(!$num)
			{	
				$this->frombet365();
			}
			$res=$this->DB->data($arr)->where("m_num='".$data['m_num']."' and b_date='".$data['b_date']."'")->table("fb_result_org")->update();
		}   
	}
	/*
	*保存比分直播数据
	**/
	function saveLiveDB($data){
		if($data['s_code']=='' || $data['m_num']=='' || $data['status']=='' ){
			return false;
			exit;
		}else{
			$arr=array(
				's_code' => $data['s_code'],
				'm_id' => $data['m_id'],
				'm_num' => $data['m_num'],
				'b_date' => $data['b_date'],
				'l_cn' => $data['l_cn'],
				'h_cn' => $data['h_cn'],
				'a_cn' => $data['a_cn'],
				'bg_color' => isset($data['bg_color']) ? $data['bg_color']: "",
				'starttime' => $data['starttime'],
				'status' => $data['status'],				
				'one' => $data['one'],
				'two' => $data['two'],
				'three' => $data['three'],
				'four' => $data['four'],
				'add' => $data['add'],
				'full' => $data['full'],
				'createtime' => date('Y-m-d H:i:s',time())
			);
			$sql="select * from bk_result_org where m_num='".$arr['m_num']."' and b_date='".$arr['b_date']."'";
			echo "sql:".$sql."\n";
			$num =$this->DB->execute($sql);
			echo $num."\n";
			if(!$num)
			{	
				$this->DB->data($arr)->table("bk_result_org")->add();
			}else{
				$this->DB->data($arr)->where("m_num='".$arr['m_num']."' and b_date='".$arr['b_date']."'")->table("bk_result_org")->update();
			}
			print_r($this->DB->error());
		}
	}
	/*
	* 
	* */
	function strformat($str){
		$str=str_replace("\n","",strip_tags($str));
		$str=str_replace("\r","",$str);
		$str=str_replace("  ","",$str);
		return trim($str);
	}
	
	function get_date_by_week($thisWeek,$gamestarttime=''){
	   $retdate = '';
       $weekint = '';
		if(is_numeric($thisWeek)){
			$weekint = $thisWeek;
		} else {
			$weekint = getIntWeek($thisWeek);
		}
		// 1或7均为周日
		if($weekint == 7) $weekint = 0;
		$curweek = date('w');
		$curtime=time();
		//$curtime=strtotime('2016-12-18 13:00:00');
		//$curweek = 0;
		$datenum=ceil((strtotime($gamestarttime)-$curtime)/(3600*24)-1);
		$num=0;
		if($datenum > 0){
			if($curweek > $weekint){
				$num=$weekint+7-$curweek;
			}else{
				$num=$weekint-$curweek;
			}
			$retdate = date('Ymd', ($curtime + $num * 24 * 3600));
			if($datenum>6){
		       $retdate=date('Ymd',strtotime($retdate)+intval($datenum/7)*3600*24*7);
	         }
			 
			if(strtotime($retdate) > strtotime($gamestarttime) && date('w', strtotime($retdate)) == $weekint){
			   $retdate = date('Ymd',strtotime($retdate)-7*3600*24);
		    } 
			 
		}else{
		    if($curweek == 0) $curweek = 7;
			if($curweek==7 && $weekint==0){
				$num=0;
			}else{
				if($curweek >= $weekint){
					$num=$curweek-$weekint;
				}else{
					$num=$curweek+(7-$weekint);
				}
			}
			$retdate = date('Ymd', ($curtime - $num * 24 * 3600));
		}
		// 反验证生成是否正确，如果不正确返回false
		$newweek = date('w', strtotime($retdate));
		
		if($newweek != $weekint){
			return false;
		}
		return $retdate;
}

	/*
	*/
	public function ballidto($str,$gamestarttime){
	
		$cnweekarr=array(0=>'周日',1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五',6=>'周六');
		$cnweek=preg_replace('@\d+@','',$str);
		foreach($cnweekarr as $key=> $val){
			if(trim($val) == trim($cnweek)){
				$week=$key;	
			}	
		}
		//
		//if(date('w',time())-$week >=0){
		//	$days=date('w',time())-$week;
		//}
		//if(date('w',time())-$week <0){
		//	$days=date('w',time())+7-$week;
		//}
		if($week==0){
			$week=7;
		}
		$ballid=$week.preg_replace('@^.*(\d{3})$@','\\1',$str);
		$lotttime = date("Y-m-d",strtotime($this->get_date_by_week($week,$gamestarttime)));
		//$lotttime=date('Y-m-d',time()-$days*24*3600);
		return array($lotttime,$ballid);
	}

	/**
	抓取比赛进度时间,为前端校时使用
	*/
	function getGameTimeing($datestr=''){
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

			$minute=preg_replace('@[\x80-\xff]@','\\1',$temp[4]);
			if($minute>0){
				$ballid_tmp=$this->ballidto($temp[0]);
				$newresult['ballid']=$ballid_tmp[1];
				$newresult['b_date']=$ballid_tmp[0];
				$timenum=intval(date('H',strtotime($temp[2])));
				if($timenum>12){
					$newresult['starttime']=date('Y-m-d H:i:s',strtotime($starttime." ".$temp[2]));
				}else{
					$newresult['starttime']=date('Y-m-d H:i:s',24*3600+strtotime($starttime." ".$temp[2]));
				}
				$newresult['minute']=$minute;
				$this->saveTimetoDB($newresult); 
			}
		}
	}
	/*
	*更新比赛时间
	*/
	function saveTimetoDB($data){

		if($data['b_date']=='' || $data['minute']=='' ||$data['ballid']==''){
			return false;           
		}
		$arr=array('minute'=>$data['minute']); 
		$res=  $this->DB->data($arr)->where("b_date='".$data['b_date']."' and m_num='".$data['ballid']."'")->table("fb_result_org")->update();
		print($res."\n");      
	}	  
}
