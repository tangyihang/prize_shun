<?php
include_once ROOT.'/Lib/Base.class.php';
class Jingcairedis extends Base{
    public $url;
    public $charset = 'GBK';
   	public $formatData  = array();
	public $DB;
   function __construct($url='')
   {
     $this->url=$url;
   }
    function run($lottid=''){
		global $_RC;
	    include_once ROOT.'/Class/redis/Redisschedule.class.php';
        $this->getJsondata();
		$val2=array();
		$filename=date("YmdH",time()).".log";
		$this->writeLog($filename,json_encode($this->formatData));
		$newarr=array();
	   print(iconv('gbk','UTF-8',"----保存redis----")."\n");
	   $redis=new Redisschedule($_RC['HOST'],$_RC['PWD']);
	   $redis->setSchedule($this->formatData,"FB");
	   print(iconv('gbk','UTF-8',"----保存redis完成----")."\n");
   }
   function getStatusString($val){
	   $str_status="";
	   if($val['p_status']=="Selling"){
			   $str_status= $val['single']==1 ? "dan" : "kong";
		   }else{
			   $str_status="closed";
	   }
	   return $str_status;
   }
   //
   public function getHtmlContent_cookie($url){
		$cookie_file = dirname(__FILE__).'/cookie.txt';
		$ch = curl_init($url); //初始化
		curl_setopt($ch, CURLOPT_HEADER, 0); //不返回header部分
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //返回字符串，而非直接输出
		curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies
		curl_exec($ch);
		curl_close($ch);
		//使用上面保存的cookies再次访问
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //使用上面获取的cookies
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
    //从竞彩网的计算器中抓取json数据;这样做的好处是出现跨周赛事也不会出现多余的赛事录入
   function getJsondata(){
	   $url="http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&poolcode[]=hhad&poolcode[]=had&poolcode[]=ttg&poolcode[]=crs&poolcode[]=crs&poolcode[]=hafu&_=1472093264651";
	  
	   $jsonstr=$this->getHtmlContent_cookie($url);
	   $jsonArr=json_decode($jsonstr,true);
	   $newarr=$jsonArr['data'];
	   foreach($newarr as $v){
		   $val=array();
		   $val['m_id']=$v['id'];
		   $val['gamename']=$v['l_cn'];
		   $val['short_gamename']=$v['l_cn_abbr'];
		   $val['hteam']=$v['h_cn'];
		   $val['short_hteam']=$v['h_cn_abbr'];
		   $val['vteam']=$v['a_cn'];
		   $val['short_vteam']=$v['a_cn_abbr'];
		   $val['status']=$v['status'];
		   $val['lotttime']=date('Ymd',strtotime($v['b_date']));
		   $tempintweek=getIntWeek(substr($v['num'], 0, -3));
		   $val['lottweek']= ($tempintweek == '0') ? '7' : $tempintweek;
		   $val['ballid']=preg_replace('@.*(\d{3})@','\\1',$v['num']);
		   $val['gamestarttime']=date('Y-m-d H:i:s',strtotime($v['date'] ." ".$v['time']));	
           $val['gameendtime'] = $this->makeEndTime($tempintweek,$val['gamestarttime']);
  
           /*if(strtotime($val['gameendtime']) > strtotime("2017-01-26 23:50:00") && strtotime($val['gameendtime']) < strtotime("2017-02-03 00:00:00")){
			  $val['gameendtime']= date("Y-m-d H:i:s",strtotime("2017-01-26 23:50:00"));
		   }*/
		   $v['had']['single']=isset($v['had']['single']) ? $v['had']['single'] :0;
		   $v['had']['p_status']=isset($v['had']['p_status']) ? $v['had']['p_status'] :0;
		   
		   $val['isconcede']=intval($v['hhad']['fixedodds']);
		   $val['had_status']=array('single'=>$v['had']['single'],'p_status'=>$v['had']['p_status']);
		   $val['hhad_status']=array('single'=>$v['hhad']['single'],'p_status'=>$v['hhad']['p_status']);
		   $val['hafu_status']=array('single'=>$v['hafu']['single'],'p_status'=>$v['hafu']['p_status']);
		   $val['ttg_status']=array('single'=>$v['ttg']['single'],'p_status'=>$v['ttg']['p_status']);
		   $val['crs_status']=array('single'=>$v['crs']['single'],'p_status'=>$v['crs']['p_status']);
		   $val['h_status']=0;
		   $jingcaiarr[]=$val;
	   }
	   $this->formatData=$jingcaiarr;
   }
   //从竞彩网的受注赛程中抓取赛程数据;
   function gethtmldata(){
       $html=file_get_contents($this->url);
      if($this->charset != 'UTF-8'){
	       $html = mb_convert_encoding($html, 'UTF-8','gb2312');
	  }
        $parten='/<div class=\"match_list\">.*<table width=\"100%\".*>(.*)<\/table>/iUs';
    	preg_match_all($parten,trim($html),$match_html);
		$parten='/<table width=\"100%\".*>(.*)<\/table>/iUs';
    	preg_match_all($parten,trim($match_html[0][1]),$match);
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
    	foreach($match2[0] as $key=>$val){
    		preg_match_all($parten_td, $val, $matchtd);
    		if(count($matchtd[0])==12){
    		    $temp=$matchtd[0];
    		    $jingcaiarr[$i]['ball']=trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[0]));
    		    //$jingcaiarr[$i]['gamename']=$temp[1];//trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[1]));
    		    
    		    $jingcaiarr[$i]['gamename']=strip_tags($temp[1]);
				$teamstr=trim(strip_tags($temp[2]));
				$teamstr=str_replace("\n","",$teamstr);
				$teamstr=str_replace(" ","",$teamstr);
				$teamnamearr=explode('^',str_replace("VS","^",$teamstr));
				$jingcaiarr[$i]['hteam']=$teamnamearr[0];
				$jingcaiarr[$i]['vteam']=$teamnamearr[1];
				
    		    $jingcaiarr[$i]['gamestarttime']=date('YmdHis',strtotime(trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[3]))));
    		    //$jingcaiarr[$i]['status']=trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[5]));
				
				$status='';
				if(iconv('UTF-8','gbk//IGNORE',trim(strip_tags($temp[5])))=="已开售"){
					$status='Selling';
				}
				if(iconv('UTF-8','gbk//IGNORE',trim(strip_tags($temp[5])))=="停售"){
					$status='Close';
				}
				$jingcaiarr[$i]['status'] = $status;
				
				
				
    		    $temp[5]=str_replace("\n","",$temp[5]);
    		    $temp[6]=str_replace("\n","",$temp[6]);
    		    $temp[7]=str_replace("\n","",$temp[7]);
    		    $temp[8]=str_replace("\n","",$temp[8]);
				$temp[9]=str_replace("\n","",$temp[9]);
				$temp[10]=str_replace("\n","",$temp[10]);
			  
    		    $jingcaiarr[$i]['209']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[6]));
    		    $jingcaiarr[$i]['210']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[7]));
    		    $jingcaiarr[$i]['211']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[8]));
    		    $jingcaiarr[$i]['212']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[9]));
				$jingcaiarr[$i]['213']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[10]));
    		    $i++;
    		}
    	}
		$this->formatData=$jingcaiarr;
   }
   function saveToDb($arr)
   {
	  $sql="select * from tab_sport_lottery_info where cz_type=".$arr['cz_type']." and lotttime=".$arr['lotttime']." and ballid=".$arr['ballid'];
      $num =$this->DB->execute($sql);
      if($num>0){
		$where=array(
	      	 'cz_type'=>$arr['cz_type'],
	      	 'lotttime'=>$arr['lotttime'],
	      	 'ballid'=>$arr['ballid'],
      	);
      	$sql="update tab_sport_lottery_info set status_single='".$arr['status_single']."' WHERE cz_type=".$arr['cz_type']." and lotttime=".$arr['lotttime']." and ballid=".$arr['ballid'];
        $num =$this->DB->execute($sql);
      }else{
   	    $this->DB->data($arr)->table("tab_sport_lottery_info")->add();
      }
   }
   //生成投注截止时间 ,周一到周五9：00~23:59:59 周六日：9：00~1:00
   function makeEndTime($week,$gamestarttime){
	   global $_JC;
	   $startimestamp=strtotime($gamestarttime);
	   $endtime='';
	   if(in_array($week,array(1,2,3,4,5))){
		    if(date('His',$startimestamp) > date('His',strtotime($_JC['starttime'])) 
				&& date('His',$startimestamp) <= date('His',strtotime($_JC['endtime'])+$_JC['tqtime']-1)){
				$endtime=date('Y-m-d H:i:s',strtotime($gamestarttime)-$_JC['tqtime']);
			}else{
				$tmptime=date('Y-m-d',$startimestamp-3600*24);
				$endtime=date('Y-m-d H:i:s',strtotime($tmptime." ".$_JC['endtime']));
			}
	   }
	   if(in_array($week,array(6,7))){
		    if(date('His',$startimestamp) > date('His',strtotime($_JC['starttime'])) 
				&& date('His',$startimestamp) <= date('His',strtotime($_JC['endtime'])+$_JC['tqtime']-1)){
				$endtime=date('Y-m-d H:i:s',strtotime($gamestarttime)-$_JC['tqtime']);
			}else{
				if(date('His',$startimestamp)<=date('His',strtotime($_JC['endtime'])+$_JC['tqtime']+$_JC['addtime']-1)){
					$endtime=date('Y-m-d H:i:s',strtotime($gamestarttime)-$_JC['tqtime']);
				}else{
					$tmptime=date('Y-m-d',$startimestamp-3600*24);
					$endtime=date('Y-m-d H:i:s',strtotime($tmptime." ".$_JC['endtime'])+$_JC['addtime']);
				}
			}
	   }
	   return $endtime;
   }
}