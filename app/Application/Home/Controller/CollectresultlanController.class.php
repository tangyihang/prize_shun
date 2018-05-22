<?php
namespace Home\Controller;
use Home\Controller\BaseController;
class CollectresultlanController extends BaseController {
	public function _initialize(){
		;
	}
    public function index()
    {
      ;
    }
    /*
    *抓取篮彩开奖数据来源官方公布的数据
    *http://info.sporttery.cn/basketball/match_result.php
    */
    public function fromJingcaioffice(){
    	
    	$starttime=microtime(get_as_float);
    	$url="http://info.sporttery.cn/basketball/match_result.php";
    	$html = file_get_contents($url);
    	$pattern='/A\[\d+\]=".*"/';
    	preg_match_all($pattern, $html, $matches);
    	
    	echo microtime(get_as_float)-$starttime;
    }
    
    /*
    *抓取篮彩开奖数据来源官方公布的数据 比分直播的
    *http://info.sporttery.cn/livescore/bk_livescore.html
    */
    
    public function fromJingcainet(){
    	$model=D('Resultlan');
    	$starttime=microtime(get_as_float);
    	$url="http://info.sporttery.cn/livescore/iframe/bk_realtime.php?type=bkdata";
    	$lotteryxml = file_get_contents($url);
    	$xmlobj = simplexml_load_string ( $lotteryxml );
    	if ($xmlobj) {
	     $xml = easy_xml_to_array ( $xmlobj );
      }
      $matches=$xml['m']['h'];
    	$newresult=array();
    	foreach($matches as $key => $val)
    	{
    		$newresult[]=explode('^',$val);
    	}
    	foreach($newresult as $val){
      	$idata=array();
      	$idata['lotteryid']='';
      	$idata['ballid']=$val[2];
      	$idata['source']='jincainet';
      	$idata['first_score']=$val[14].":".$val[15];
      	$idata['two_score']=$val[16].":".$val[17];
      	$idata['three_score']=$val[18].":".$val[19];
      	$idata['four_score']=$val[20].":".$val[21];
      	$idata['match_starttime']=date('Y-m-d H:i',strtotime($val[10]." ".$val[11]));
      	$idata['lotttime']=date('Y-m-d',strtotime($idata['match_starttime'])-12*3600);
      	$idata['status']=$val[28];
      	if($idata['status']=='-1'){
      		 $this->saveToDB($model,$idata,'basketball');
        }
      }
    	echo microtime(get_as_float)-$starttime;
    }
    
     /*
    *抓取篮彩开奖数据来源网易彩票 比分直播的
    *http://info.sporttery.cn/livescore/bk_livescore.html
    */
    
    public function from163(){
    	$model=D('Resultlan');
    	$starttime=microtime(get_as_float);
    	$datestr=$_GET['date'];
    	if($datestr==''){
    		$url="http://live.caipiao.163.com/basketball";
    	}else{
    		$url="http://live.caipiao.163.com/basketball?date=".$datestr;//查询历史开奖数据
    	}
    	$html = file_get_contents($url);
    	
    	$parten='/<section class=\"liveItem\".*>(.*)<\/section>/iUs';
    	$partendiv="/<div.*>.*<\/div>/iUs";
    	$partentable="/<tbody.*>.*<\/tbody>/iUs";
    	$partentd="/<td.*>.*<\/td>/iUs";
    	$fparten="@比赛结束@";
    	preg_match_all($parten, $html, $matches);
    	$newresult=array();
    	foreach($matches[0] as $key => $val)
    	{

    		preg_match_all($partendiv, $val, $match1);
    		preg_match_all($partentable, $val, $match2);
    		preg_match_all($partentd, $match2[0][0], $tdmatch);
    		$macth['ballid']=preg_replace('@.*<em>(.*)<\/em>.*@','\\1',$match1[0][0]);
    		$macth['status']=preg_replace('@.*<span.*?>(.*)<\/span>.*@','\\1',$match1[0][0]);
    		 
    		$macth['first_score']=strip_tags($tdmatch[0][1]).":".strip_tags($tdmatch[0][13]);
    		$macth['two_score']=strip_tags($tdmatch[0][2]).":".strip_tags($tdmatch[0][14]);
    		$macth['three_score']=strip_tags($tdmatch[0][3]).":".strip_tags($tdmatch[0][15]);
    		$macth['four_score']=strip_tags($tdmatch[0][4]).":".strip_tags($tdmatch[0][16]);
    		$macth['add_score']=strip_tags($tdmatch[0][5]).":".strip_tags($tdmatch[0][17]);
			$macth['add_score']=str_replace('-','0',$macth['add_score']);
			$macth['full_score']=strip_tags($tdmatch[0][6]).":".strip_tags($tdmatch[0][18]);
    		$newresult[]=$macth;
    	}
    	foreach($newresult as $val){
    		 
    		$idata=array();
    		list($lotttime,$ballid)=$this->ballidto($val['ballid']);
    		$idata['lotteryid']='';
    		$idata['lotttime'] = $lotttime;
    		$idata['ballid'] = $ballid;
    		$idata['source'] = 'from163';
    		 
    		$idata['first_score']=$val['first_score'];
    		$idata['two_score']=$val['two_score'];
    		$idata['three_score']=$val['three_score'];
    		$idata['four_score']=$val['four_score'];
			$idata['add_score']=$val['add_score'];
			$idata['full_score']=$val['full_score'];
    		$idata['match_starttime']='';
    		
    		if(preg_match($fparten,$val['status'])){
    			$idata['status']='-1';
    		}
    	
    		if($idata['status']=='-1'){
    			$this->saveToDB($model,$idata,'basketball');
    		}
    	}
    	echo microtime(get_as_float)-$starttime;
    }
    
     /*
    *抓取篮彩开奖数据来源500wan彩票 比分直播的
    *http://live.500.com/lq.php?c=jc&e=2014-11-06
    */
    public function from500(){
    	
    	$model=D('Resultlan');
    	$starttime=microtime(get_as_float);
        $datestr=$_GET['date'];
    	if($datestr==''){
    		$url="http://live.500.com/lq.php";
    	}else{
    		$url="http://live.500.com/lq.php?c=jc&e=".$datestr;//查询历史开奖数据
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
    	$html = curl_exec($ch);
    	curl_close($ch);
    	$pattern='/matchList=\[.*\];/';
    	preg_match_all($pattern, $html, $matches);
    	
    	$jsondata=preg_replace('@matchList=(.*);@','\\1',$matches[0][0]);
    	$jsondata=iconv('gbk','utf-8',$jsondata);
   
    	$matchlist=json_decode(preg_replace('/,\s*([\]}])/m', '$1', $jsondata));
    	$newresult=array();
    	
    	foreach($matchlist as $key => $val)
    	{
			//print_r($val);
			//31-22-22-42/9-13  判断是否有加时
			$htmp=explode('/',$val[22]);
			$vtmp=explode('/',$val[23]);
    		$temp1=explode('-',$htmp[0]);
    		$temp2=explode('-',$vtmp[0]);
			$addtemp1=explode('-',$htmp[1]);
			$addtemp2=explode('-',$vtmp[1]);
			$hadd_score=0;
			$vadd_score=0;
			for($i=0;$i<count($addtemp1);$i++){
				$hadd_score+=$addtemp1[$i];
				$vadd_score+=$addtemp2[$i];
			}
			$temp1[]=$hadd_score;
			$temp2[]=$vadd_score;
    		list($lotttime,$ballid)=$this->ballidto($val[26]);
    	  $macth['ballid']=$ballid;
          $status=$val[0]==11 ? '-1':'1';
    	  $macth['status']=$status;
    	  $macth['source'] = '500wan';
    	  $macth['first_score']=$temp1[0].":".$temp2[0];
      	$macth['two_score']=$temp1[1].":".$temp2[1];
      	$macth['three_score']=$temp1[2].":".$temp2[2];
      	$macth['four_score']=$temp1[3].":".$temp2[3];
		$macth['add_score']=$temp1[4].":".$temp2[4];
		$htotal=0;
		$vtotal=0;
		for($i=0;$i<count($temp1);$i++){
				$htotal+=$temp1[$i];
				$vtotal+=$temp2[$i];
			}
		$macth['full_score']=$htotal.":".$vtotal;
      	$macth['match_starttime']=date('Y-m-d H:i',strtotime($val[3]." ".$val[4]));
      	$macth['lotttime']=$lotttime;
      	if($macth['status']==-1){
    		$newresult[]=$macth;
    	 }
    	}
    	foreach($newresult as $val){
    		$val['lotteryid']='';
    		if($val['status']=='-1'){
      	       $this->saveToDB($model,$val,'basketball');
            }
    	}
    }
 /*
    *抓取篮彩开奖数据来源500wan彩票 比分直播的
    *http://live.500.com/lq.php?c=jc&e=2014-11-06
    */
    public function fromOkooo(){
    	
    	$model=D('Resultlan');
    	$starttime=microtime(get_as_float);
    	$datestr=$_GET['date'];
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
		  
    	//print_r($match[0][0]);
    	preg_match_all($parten,trim($match[0][0]),$match2);
    	//print_r($match2);
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
		  
    	  $info=$this->ballidto($this->strformat($lantemplist[$i][0]));
    	  $newlist[$i]['lotttime']=$info[0];
    	  $newlist[$i]['ballid']=$info[1];
    	  $newlist[$i]['first_score']=$this->strformat($lantemplist[$i][4]).":".$this->strformat($lantemplist2[$i][2]);
    	  $newlist[$i]['two_score']=$this->strformat($lantemplist[$i][5]).":".$this->strformat($lantemplist2[$i][3]);
    	  $newlist[$i]['three_score']=$this->strformat($lantemplist[$i][6]).":".$this->strformat($lantemplist2[$i][4]);
    	  $newlist[$i]['four_score']=$this->strformat($lantemplist[$i][7]).":".$this->strformat($lantemplist2[$i][5]);
		  $hadd_score=$this->strformat($lantemplist[$i][8]) ? $this->strformat($lantemplist[$i][8]) : 0;
		  $vadd_score=$this->strformat($lantemplist2[$i][6]) ? $this->strformat($lantemplist2[$i][6]) :0;
		
    	  $newlist[$i]['add_score']=$hadd_score.":".$vadd_score;
		  
		  $newlist[$i]['full_score']=$this->strformat($lantemplist[$i][9]).":".$this->strformat($lantemplist2[$i][7]);
    	  $newlist[$i]['status']=iconv('UTF-8','GBK',$this->strformat($lantemplist[$i][2]));
    	  $fparten="@".iconv('UTF-8','GBK',"完")."@";
    	 
    	 if(preg_match($fparten,$this->strformat($newlist[$i]['status']))){
      	   $newlist[$i]['status']='-1';
      	 }else{
      	 	$newlist[$i]['status']='1';
      	 }
      	  $newlist[$i]['source']='fromOkooo';
    	}
    	print_r($newlist);
       foreach($newlist as $val){
    		$val['lotteryid']='';
    		if($val['status']=='-1'){
      	       $this->saveToDB($model,$val,'basketball');
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
  	  if($val==$cnweek){
  	     $week=$key;	
  	  }	
  	}
  
  	//这里只能推算已经结束的比赛
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
