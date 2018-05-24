<?php
/*
抓取竞彩足球赛果
@author PaulHE
287568970@qq.com
*/
include_once ROOT.'/Lib/Base.class.php';
class Resultbasketball extends Base{
    public $httpUrl;
    public $charset = 'GBK';
   	public $formatData  = array();
	public $DB;
   function __construct($url='')
   {
     $this->url=$url;
   }
   /*
   *从500wan抓取
   *www.500.com
   *比赛结束后开始抓取入库
   */
   function from500($datestr=''){
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
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
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
		  $temp1=preg_split('@[-\/]@',$val[22]);
    	  $temp2=preg_split('@[-\/]@',$val[23]);
    		list($lotttime,$ballid)=$this->ballidto($val[26]);
    	  $macth['ballid']=$ballid;
          $status=$val[0]==11 ? '-1':'1';
    	  $macth['status']=$status;
    	  $macth['source'] = '500wan';
		  
		  $macth['first_score']=intval($temp1[0]).":".intval($temp2[0]);
      	  $macth['two_score']=intval($temp1[1]).":".intval($temp2[1]);
		  $macth['three_score']=intval($temp1[2]).":".intval($temp2[2]);
		  $macth['four_score']=intval($temp1[3]).":".intval($temp2[3]);
		  if($temp2==5){
			  $macth['add_score']=$temp1[4].":".$temp2[4];
		  }else{
			  $macth['add_score']="0:0";
		  }
		  $macth['full_score']=array_sum($temp1).":".array_sum($temp2); 
    	
      	  $macth['match_starttime']=date('Y-m-d H:i',strtotime($val[3]." ".$val[4]));
      	  $macth['lotttime']=$lotttime;
		  $macth['result']='';
		  $macth['lotteryid']='';
		  
      	if($macth['status']==-1){
    		$newresult[]=$macth;
    	 }
    	}
    	foreach($newresult as $val){
    		$val['lotteryid']='';
    		if($val['status']=='-1'){
				//比赛只有2场的情况
				if(($val['two_score']=='0:0' && $val['four_score']=='0:0') || ($val['two_score']==':' && $val['four_score']==':')){
					$val['two_score']=$val['three_score'];
					$val['three_score']='0:0';
					$val['four_score']='0:0';
				}
                //print_r($val);
				
      	       $this->saveToDB($val);
            }
    	}
   }
   /*
   *从163抓取
   *caipiao.163.com
   *比赛结束后开始抓取入库
   */
   function from163($datestr=''){
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
			//print_r($tdmatch);
			
    		$macth['ballid']=preg_replace('@.*<em>(.*)<\/em>.*@','\\1',$match1[0][0]);
    		$macth['status']=preg_replace('@.*<span.*?>(.*)<\/span>.*@','\\1',$match1[0][0]);
    		 
    		$macth['first_score'] = intval(strip_tags($tdmatch[0][1])).":".intval(strip_tags($tdmatch[0][13]));
    		$macth['two_score'] = intval(strip_tags($tdmatch[0][2])).":".intval(strip_tags($tdmatch[0][14]));
    		$macth['three_score'] = intval(strip_tags($tdmatch[0][3])).":".intval(strip_tags($tdmatch[0][15]));
    		$macth['four_score'] = intval(strip_tags($tdmatch[0][4])).":".intval(strip_tags($tdmatch[0][16]));
    		$macth['add_score'] = intval(strip_tags($tdmatch[0][5])).":".intval(strip_tags($tdmatch[0][17]));
			$macth['full_score'] = intval(strip_tags($tdmatch[0][6])).":".intval(strip_tags($tdmatch[0][18]));
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
			$idata['result']='';


    		if(preg_match($fparten, $val['status'])){
    			$idata['status']='-1';
				
				//比赛只有2场的情况
				if($idata['two_score']==':' && $idata['four_score']==':'){
					$idata['two_score']=$idata['three_score'];
					$idata['three_score']='0:0';
					$idata['four_score']='0:0';
				}
				$this->saveToDB($idata);
    		}
    	}
    
   }
   //
   public function fromOkooo($datestr='')
   {
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
    	  $info=$this->ballidto($this->strformat($lantemplist[$i][0]));
    	  $newlist[$i]['lotttime']=$this->strformat($info[0]);
    	  $newlist[$i]['ballid']=$this->strformat($info[1]);
    	  $newlist[$i]['first_score']=intval($this->strformat($lantemplist[$i][4])).":".intval($this->strformat($lantemplist2[$i][2]));
    	  $newlist[$i]['two_score']=intval($this->strformat($lantemplist[$i][5])).":".intval($this->strformat($lantemplist2[$i][3]));
    	  $newlist[$i]['three_score']=intval($this->strformat($lantemplist[$i][6])).":".intval($this->strformat($lantemplist2[$i][4]));
    	  $newlist[$i]['four_score']=intval($this->strformat($lantemplist[$i][7])).":".intval($this->strformat($lantemplist2[$i][5]));
		  $hadd_score=$this->strformat($lantemplist[$i][8]) ? $this->strformat($lantemplist[$i][8]) : 0;
		  $vadd_score=$this->strformat($lantemplist2[$i][6]) ? $this->strformat($lantemplist2[$i][6]) :0;
		
    	  $newlist[$i]['add_score']=intval($hadd_score).":".intval($vadd_score);
		  
		  $newlist[$i]['full_score']=intval($this->strformat($lantemplist[$i][9])).":".intval($this->strformat($lantemplist2[$i][7]));
    	  $newlist[$i]['status']=$this->strformat($lantemplist[$i][2]);
    	  $fparten="@^完$@";
    	 if(preg_match($fparten,$this->strformat($newlist[$i]['status']))){
      	   $newlist[$i]['status']='-1';
      	 }else{
      	 	$newlist[$i]['status']='1';
      	 }
      	  $newlist[$i]['source']='fromOkooo';
    	}
       foreach($newlist as $val){
    		$val['lotteryid']='';
			$val['match_starttime']='';
			$val['result']='';
    		if($val['status']=='-1'){
      	       $this->saveToDB($val);
            }
    	}
    
    }
   
   
   /*
   *从竞彩网官网抓取
   *www.sporttery.cn
   *官网的公布的数据一般比赛结束后2小时或者更晚
   */
   function fromjcw(){
		// 检测目标地址是否为空
		if($this->httpUrl == ''){
			echo 1;
		}
		// 获取目标http所有内容
		$opts = array('http'=> array('method' => 'GET', 'timeout' => 10));
		$context = stream_context_create($opts);
		$httpContent = file_get_contents($this->httpUrl, false, $context);
		if(!$httpContent){
			return ;
		}
		// 目标编码不是UTF-8则要转换
		if($this->charset != 'UTF-8'){
			$httpContent = mb_convert_encoding($httpContent, 'UTF-8', $this->charset);
		}
		$trs_preg = "/<tr.*>(.*)<\/tr>/iUs";
		$trarr = array();
		preg_match_all($trs_preg, $httpContent, $trarr);
		$tds_preg = "/<td.*>(.*)<\/td>/iUs";
		$td_list = array();
		//print_r($trarr[1]);
		foreach($trarr[1] as $tr)
		{
			preg_match_all($tds_preg, $tr, $tds);
			if(count($tds[1]) == 11 || count($tds[1])==10){
				if(count($tds[1])==10)
				{
					$tds[1][10]=$tds[1][8];
					$tds[1][9]=strip_tags($tds[1][7]);
					$tds[1][7]='';
					$tdStr4=$tds[1][4];
					$tdStr5=$tds[1][5];
					$tdStr6=$tds[1][6];
					$tds[1][4]=preg_replace('@(.*)<span class=\"l10\"><\/span>(.*)@','\\1',$tdStr4);
					$tds[1][5]=preg_replace('@(.*)<span class=\"l10\"><\/span>(.*)@','\\2',$tdStr4);
					$tds[1][6]=preg_replace('@(.*)<span class=\"l10\"><\/span>(.*)@','\\1',$tdStr5);
					$tds[1][7]=preg_replace('@(.*)<span class=\"l10\"><\/span>(.*)@','\\2',$tdStr5);
					$tds[1][8]=strip_tags($tdStr6);
				    $td_list[] = $tds[1];
				}else{
					$td_list[] = $tds[1];
				}
			}
		}
		foreach($td_list as $val){
			if($val[9] && preg_match("@已完成@",iconv('UTF-8','GBK',$val[10]))){
		     list($lotttime,$ballid)=$this->ballidto(iconv('UTF-8','GBK',$val[1]));
			 $add_score=!empty($val[8]) ? $val[8] : "0:0";
			
			 $iarr=array(
			    'lotteryid'=>'',
				'lotttime' => $lotttime,
				'ballid' =>  $ballid,
				'source' => 'fromjcw',
				'first_score' => $val[4],
				'two_score' => $val[5],
				'three_score' => $val[6],
				'four_score' => $val[7],
				'add_score' => $add_score,
				'full_score' => $val[9],
				'addtime' => date('Y-m-d H:i:s',time()),
				'match_starttime'=>'',
				'result' => '',
				'status' => -1,
			 );
			
			 $this->saveToDB($iarr);
			 }
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
				'first_score' => $data['first_score'],
				'two_score' => $data['two_score'],
				'three_score' => $data['three_score'],
				'four_score' => $data['four_score'],
			    'add_score' => $data['add_score'],
				'full_score' => $data['full_score'],
				'result' => $data['result'],
				'addtime' => date('Y-m-d H:i:s',time()),
				'status' => $data['status']
            );
             if (!empty($data['match_starttime'])) {
                 $arr['match_starttime'] = $data['match_starttime'];
             }
            $sql="select * from tab_lottery_result_lancai where lotttime='".$arr['lotttime']."' and ballid='".$arr['ballid']."' and source='".$arr['source']."'";
			$num =$this->DB->execute($sql);
		    if(!$num)
			{
			    var_dump($arr);
		       $this->DB->data($arr)->table("tab_lottery_result_lancai")->add();

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
