<?php
/*
ץȡ������������
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
   *��500wanץȡ
   *www.500.com
   *����������ʼץȡ���
   */
   function from500(){
	    $model=D('Resultlan');
    	$starttime=microtime(get_as_float);
        $datestr=$_GET['date'];
    	if($datestr==''){
    		$url="http://live.500.com/lq.php";
    	}else{
    		$url="http://live.500.com/lq.php?c=jc&e=".$datestr;//��ѯ��ʷ��������
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
    		$temp1=explode('-',$val[22]);
    		$temp2=explode('-',$val[23]);
    		list($lotttime,$ballid)=$this->ballidto($val[26]);
    	  $macth['ballid']=$ballid;
        $status=$val[0]==11 ? '-1':'1';
    	  $macth['status']=$status;
    	  $macth['source'] = 'from500';
    	  $macth['first_score']=$temp1[0].":".$temp2[0];
      	$macth['two_score']=$temp1[1].":".$temp2[1];
      	$macth['three_score']=$temp1[2].":".$temp2[2];
      	$macth['four_score']=$temp1[3].":".$temp2[3];
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
   *��163ץȡ
   *caipiao.163.com
   *����������ʼץȡ���
   */
   function from163(){
	    $model=D('Resultlan');
    	$starttime=microtime(get_as_float);
    	$datestr=$_GET['date'];
    	if($datestr==''){
    		$url="http://live.caipiao.163.com/basketball";
    	}else{
    		$url="http://live.caipiao.163.com/basketball?date=".$datestr;//��ѯ��ʷ��������
    	}
    	$html = file_get_contents($url);
    	$parten='/<section class=\"liveItem\".*>(.*)<\/section>/iUs';
    	$partendiv="/<div.*>.*<\/div>/iUs";
    	$partentable="/<tbody.*>.*<\/tbody>/iUs";
    	$partentd="/<td.*>.*<\/td>/iUs";
    	$fparten="@��������@";
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
    		$macth['add_score']=strip_tags($tdmatch[0][4]).":".strip_tags($tdmatch[0][16]);
    		$newresult[]=$macth;
    	}
    	print_r($newresult);
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
   *�Ӿ���������ץȡ
   *www.sporttery.cn
   *�����Ĺ���������һ�����������2Сʱ���߸���
   */
   function fromjcw(){
		// ���Ŀ���ַ�Ƿ�Ϊ��
		if($this->httpUrl == ''){
			echo 1;
		}
		// ��ȡĿ��http��������
		$opts = array('http'=> array('method' => 'GET', 'timeout' => 10));
		$context = stream_context_create($opts);
		$httpContent = file_get_contents($this->httpUrl, false, $context);
		if(!$httpContent){
			return ;
		}
		// Ŀ����벻��UTF-8��Ҫת��
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
			if(count($tds[1]) == 11 || count($tds[1])==9){
				if(count($tds[1])==9)
				{
					$tds[1][10]=$tds[1][8];
					$tds[1][9]=$tds[1][7];
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
			if($val[8]){
		     list($lotttime,$ballid)=$this->ballidto(iconv('UTF-8','GBK',$val[1]));
			 $f = explode(':',$val[4]);
			 $t = explode(':',$val[5]);
			 $th = explode(':',$val[6]);
			 $fo = explode(':',$val[7]);
			 $full = explode(':',$val[8]);
			 
			 $add_score=($full[0]-($f[0]+$t[0]+$th[0]+$fo[0])).":".($full[1]-($f[1]+$t[1]+$th[1]+$fo[1]));
			 $iarr=array(
				'lotttime' => $lotttime,
				'ballid' =>  $ballid,
				'source' => 'fromjcw',
				'first_score' => $val[4],
				'two_score' => $val[5],
				'three_score' => $val[6],
				'four_score' => $val[7],
				'add_score' => $add_score,
				'full_score' => $val[8],
				'addtime' => date('Y-m-d H:i:s',time()),
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
				'lotteryid' => $data['lotteryid'],
				'lotttime' => $data['lotttime'],
				'ballid' =>  $data['ballid'],
				'source' => $data['source'],       
				'first_score' => $data['first_score'],
				'two_score' => $data['two_score'],
				'three_score' => $data['three_score'],
				'four_score' => $data['four_score'],
			    'add_score' => $data['add_score'],
				'full_score' => $data['full_score'],
				'match_starttime' => $data['match_starttime'],
				'result' => $data['result'],
				'addtime' => date('Y-m-d H:i:s',time()),
				'status' => $data['status']
            );
            $sql="select * from tab_lottery_result_lancai where lotttime='".$arr['lotttime']."' and ballid='".$arr['ballid']."' and source='".$arr['source']."'";
			$num =$this->DB->execute($sql);
			echo $num;
		    if(!$num)
			{	
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
  	$cnweekarr=array(0=>'����',1=>'��һ',2=>'�ܶ�',3=>'����',4=>'����',5=>'����',6=>'����');
  	$cnweek=preg_replace('@\d+@','',$str);

  	
  	foreach($cnweekarr as $key=> $val){
  	  if($val==$cnweek){
  	     $week=$key;	
  	  }	
  	}
  
  	//����ֻ�������Ѿ������ı���
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