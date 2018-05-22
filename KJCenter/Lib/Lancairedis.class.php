<?php
/**
 * 竞彩篮球对阵抓取
 * @author HXD 2015-03-21 上线使用
 */
include_once ROOT.'/Lib/Base.class.php';
class Lancairedis extends Base{
   public $url;
   public $charset = 'GBK';
   public $formatData  = array();
   function __construct($url)
   {
     $this->url=$url;
   }
   function run(){
	    global $_RC;
	    include_once ROOT.'/Class/redis/Redisschedule.class.php';
        $this->gethtmldata();
        $val2=array();
        $arrweek=array('周一'=>1,'周二'=>2,'周三'=>3,'周四'=>4,'周五'=>5,'周六'=>6,'周日'=>7);
		$newarr=array();
        foreach($this->formatData as $value){
           $val2['cz_type']='12';
           $tempintweek=getIntWeek(substr($value['ball'], 0, -3));
		   $tempdate = get_date_by_week($tempintweek,$value['gamestarttime']);
		   if(!$tempdate){
			   continue;
		   }
		   $val2['lotttime']=$tempdate;
		   $cnweek=trim(preg_replace("@(.*)(\d{3})@","\\1",$value['ball']));
		   $val2['lottweek']=($tempintweek == '0') ? '7' : $tempintweek;
		   $val2['gamestarttime']=$value['gamestarttime'];
		   $val2['gameendtime'] = $this->makeEndTime($tempintweek,$value['gamestarttime']);
		   /*if(strtotime($val2['gameendtime']) > strtotime("2017-01-26 23:50:00") && strtotime($val2['gameendtime']) < strtotime("2017-02-23 00:00:00")){
			  $val2['gameendtime']= date("Y-m-d H:i:s",strtotime("2017-01-26 23:50:00"));
		   }*/
		   
		   $val2['gamename']=$value['gamename'];
		   $val2['hteam']=$this->getLcteamShortname($value['hteam']);
		   $val2['vteam']=$this->getLcteamShortname($value['vteam']);
		   $val2['status']=$value['status'];
		   $val2['ballid']=preg_replace('@.*(\d{3})@','\\1',$value['ball']);
		   
		   $val2['status_single']='214|'.$value['214'].'^215|'.$value['215'].'^216|'.$value['216'].'^217|'.$value['217'];
		   $val2['status_single']=strip_tags($val2['status_single']);
		   $val2['h_status']=0;
		   $val2['mid']=$value['mid'];
		   $newarr[]=$val2;
        }
	   print(iconv('gbk','UTF-8',"----保存redis----")."\n");
	   $redis=new Redisschedule($_RC['HOST'],$_RC['PWD']);
	   
	  error_log(json_encode($newarr).chr(13).chr(10),3,ROOT.'/Log/redis_data'.date('YmdH').'.log');
	  $redis->setSchedule($newarr,"BK");
	  print(iconv('gbk','UTF-8',"----保存redis完成----")."\n");
   }
   //
   function gethtmldata(){
       $html=file_get_contents($this->url);
      if($this->charset != 'UTF-8'){
	       $html = mb_convert_encoding($html, 'UTF-8','gb2312');
	  }
	  //echo $html;
		//$parten='/<table width=\"100%\".*>(.*)<\/table>/iUs';
		$parten='/<tr windex=\"\d+\" lindex=\"\d+\".*>(.*)<\/tr>/iUs';
    	preg_match_all($parten,trim($html),$match_html);
		
		//$parten='/<table width=\"100%\".*>(.*)<\/table>/iUs';
		//print_r($match_html);
    	//preg_match_all($parten,trim($match_html[0][1]),$match);
		//print_r($match);
		//exit;
    	$parten='/<tr.*>(.*)<\/tr>/iUs';
    	$parten_td = "/<td.*>(.*)<\/td>/iUs";
    	$parten_span="/<span.*>(.*)<\/span>/iUs";
    	$parten_a="/<a.*>(.*)<\/a>/iUs";
		$parten_str = "'<[\/\!]*?[^<>]*?>'si";
		$parten_div="/<div.*>(.*)<\/div>/iUs";
		$parten_font="/<font.*>(.*)[+,-]{1}.*<\/font>/iUs";
    	//preg_match_all($parten,$match[0][0],$match2);
    	$lancaiarr=array();
    	$i=0;
		//print_r($match2);
    	foreach($match_html[0] as $key=>$val){
    		preg_match_all($parten_td, $val, $matchtd);
    		if(count($matchtd[0])==11){
    		    $temp=$matchtd[0];
    		    $lancaiarr[$i]['ball']=trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[0]));
    		    $lancaiarr[$i]['gamename']=trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[1]));
				$teamstr=trim(strip_tags($temp[2]));
				$teamstr=str_replace("\n","",$teamstr);
				$teamstr=str_replace(" ","",$teamstr);
				$teamnamearr=explode('^',str_replace("VS","^",$teamstr));
				$lancaiarr[$i]['vteam'] = trim($teamnamearr[0]);
				$lancaiarr[$i]['hteam'] = trim($teamnamearr[1]);
				
    		    $lancaiarr[$i]['gamestarttime']=date('YmdHis',strtotime(trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[3]))));
    		    //$lancaiarr[$i]['status']=trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[4]));
				$midarr=array();
				preg_match_all("/m=\d+/is", $temp[2], $midarr);
			    $lancaiarr[$i]['mid']=preg_replace('@^m=(\d+)$@','\\1',$midarr[0][0]);
				
				$status='';
				if(iconv('UTF-8','gbk//IGNORE',trim(strip_tags($temp[5])))=="已开售"){
					$status='Selling';
				}
				if(iconv('UTF-8','gbk//IGNORE',trim(strip_tags($temp[5])))=="停售"){
					$status='Close';
				}
				$lancaiarr[$i]['status']=str_replace("\n","",strip_tags($status));
				
    		    $temp[6]=str_replace("\n","",str_replace("u-kong","u-closed",$temp[6]));
    		    $temp[7]=str_replace("\n","",str_replace("u-kong","u-closed",$temp[7]));
    		    $temp[8]=str_replace("\n","",str_replace("u-kong","u-closed",$temp[8]));
    		    $temp[9]=str_replace("\n","",str_replace("u-kong","u-closed",$temp[9]));
				
    		    $lancaiarr[$i]['216']=str_replace("cir","kong",strip_tags(preg_replace('@.*<div.?class=\"u-(.*)\"></div>.*@','\\1',$temp[6])));
    		    $lancaiarr[$i]['214']=str_replace("cir","kong",strip_tags(preg_replace('@.*<div.?class=\"u-(.*)\"></div>.*@','\\1',$temp[7])));
    		    $lancaiarr[$i]['215']=str_replace("cir","kong",strip_tags(preg_replace('@.*<div.?class=\"u-(.*)\"></div>.*@','\\1',$temp[8])));
    		    $lancaiarr[$i]['217']=str_replace("cir","kong",strip_tags(preg_replace('@.*<div.?class=\"u-(.*)\"></div>.*@','\\1',$temp[9])));
				
				
    		    $i++;
    		}
    	}
		$this->formatData=$lancaiarr;
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
   //
   function getLcteamShortname($teamname){
	   $teamArr=array(
	   '洛杉矶湖人'=>'湖人','底特律活塞'=>'活塞','华盛顿奇才'=>'奇才','多伦多猛龙'=>'猛龙','克利夫兰骑士'=>'骑士','达拉斯小牛'=>'小牛','圣安东尼奥马刺'=>'马刺','明尼苏达森林狼'=>'森林狼',
	   '印第安那步行者'=>'步行者','亚特兰大老鹰'=>'老鹰','芝加哥公牛'=>'公牛','波士顿凯尔特人'=>'凯尔特人','犹他爵士'=>'爵士','亚特兰大梦想'=>'梦想','休斯敦火箭'=>'火箭','孟菲斯灰熊'=>'灰熊',
	   '新奥尔良鹈鹕'=>'鹈鹕','俄克拉荷马城雷霆'=>'雷霆','洛杉矶快船'=>'快船','金州勇士'=>'勇士','丹佛掘金'=>'掘金','萨克拉门托国王'=>'国王','波特兰开拓者'=>'开拓者','菲尼克斯太阳'=>'太阳',
	   '密尔沃基雄鹿'=>'雄鹿','迈阿密热火'=>'热火','夏洛特黄蜂'=>'黄蜂','皇家马德里'=>'皇马','莫斯科中央陆军'=>'中央陆军','布鲁克林篮网'=>'篮网','纽约尼克斯'=>'尼克斯','奥兰多魔术'=>'魔术',
	   '华盛顿神秘人'=>'神秘人','纽约自由'=>'自由','印第安纳狂热'=>'狂热','塔尔萨震动'=>'震动','康涅狄格太阳'=>'太阳','达拉斯飞马'=>'飞马','圣安东尼奥银星'=>'银星','西雅图风暴'=>'风暴',
	   '菲尼克斯水星'=>'水星','明尼苏达天猫'=>'天猫','洛杉矶火花'=>'火花','芝加哥天空'=>'天空','费城76人'=>'76人',
	   );
	   //print_r($teamname);
	   foreach($teamArr as $key=>$val){
		   if(iconv("GBK","UTF-8",$key) == $teamname){
			  $resteamname = iconv("GBK","UTF-8",$val);
		   }
	   }
	   
	   if(isset($resteamname))
	   {
		   return $resteamname;  
	   }else{
		   return mb_substr($teamname,0,4,'utf-8');
	   }
   }
   
   //生成投注截止时间 ,周一到周五9：00~23:59:59 周六日：9：00~1:00
   function makeEndTime($week,$gamestarttime){
	   global $_LC;
	   $startimestamp=strtotime($gamestarttime);
	   $endtime='';
	   if(in_array($week,array(1,4,5))){
		    if(date('His',$startimestamp) > date('His',strtotime($_LC['starttime'])) 
				&& date('His',$startimestamp) <= date('His',strtotime($_LC['endtime'])+$_LC['tqtime']-1)){
				$endtime=date('Y-m-d H:i:s',strtotime($gamestarttime)-$_LC['tqtime']);
			}else{
				$tmptime=date('Y-m-d',$startimestamp-3600*24);
				$endtime=date('Y-m-d H:i:s',strtotime($tmptime." ".$_LC['endtime']));
			}
	   }
	   if(in_array($week,array(2,3))){
		    if(date('His',$startimestamp) > date('His',strtotime($_LC['starttime'])-3600) 
				&& date('His',$startimestamp) <= date('His',strtotime($_LC['endtime'])+$_LC['tqtime']-1)){
				$endtime=date('Y-m-d H:i:s',strtotime($gamestarttime)-$_LC['tqtime']);
			}else{
				$tmptime=date('Y-m-d',$startimestamp-3600*24);
				$endtime=date('Y-m-d H:i:s',strtotime($tmptime." ".$_LC['endtime']));
			}
	   }
	   if(in_array($week,array(6,7))){
		    if(date('His',$startimestamp) > date('His',strtotime($_LC['starttime'])) 
				&& date('His',$startimestamp) <= date('His',strtotime($_LC['endtime'])+$_LC['tqtime']-1)){
				$endtime=date('Y-m-d H:i:s',strtotime($gamestarttime)-$_LC['tqtime']);
			}else{
				if(date('His',$startimestamp)<=date('His',strtotime($_LC['endtime'])+$_LC['tqtime']+$_LC['addtime']-1)){
					$endtime=date('Y-m-d H:i:s',strtotime($gamestarttime)-$_LC['tqtime']);
				}else{
					$tmptime=date('Y-m-d',$startimestamp-3600*24);
					$endtime=date('Y-m-d H:i:s',strtotime($tmptime." ".$_LC['endtime'])+$_LC['addtime']);
				}
			}
	   }
	   return $endtime;
   }
   
}
?>
