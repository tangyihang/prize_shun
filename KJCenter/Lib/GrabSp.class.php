<?php
/*
*抓取中国竞彩网官网的即时sp值
*支持每分钟抓取一次
*/
class GrabSp
 {
	public $httpUrl;
    public $charset = 'GBK';
   	public $formatData  = array();
	public $DB;
	public $opts = array('http'=>array('method'=>"GET",'timeout'=>3)); //超时3s
	Public $odds_url="http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&poolcode[]=";
	function __construct($url='')
    {
     ;
    }
	/*
	*抓取页面
	*/
	public function getHtmlContent($url){
		$html = file_get_contents($url,false, stream_context_create($this->opts));
		if(!$html){
			echo "抓取时间为失败或者超时\n";
			return false;
		}
		return $html;
	}
	/*
	*
	*解决抓取页面被302 301的情况，而抓取不了数据的问题
	*/
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
	/*
	*获取竞彩足球胜平负的SP值
	*/
	public function had(){
		$stamptime=time();
		$fb_url=$this->odds_url.'had&_='.$stamptime;
		$html = $this->getHtmlContent_cookie($fb_url);
		if(!$html){
			echo "数据为空,中断程序，执行下一步\n";
			return false;
		}
    	$spdata=$this->makeFB($html,'had');
		//抓取数据入redis库
		$this->savetoRedis($spdata,'209');
		foreach($spdata as $val){
			unset($val['lotttime']);
			unset($val['ballid']);
			unset($val['single']);
			unset($val['p_status']);
			$this->savetoSPinfo($val,'fb_odds_had');
		}
	}
	/*
	*获取竞彩足球让球胜平负的SP值
	*/
	public function hhad(){
		$stamptime=time();
		$fb_url=$this->odds_url.'hhad&_='.$stamptime;
		$html = $this->getHtmlContent_cookie($fb_url); 
		if(!$html){
			echo "数据为空,中断程序，执行下一步\n";
			return false;
		}
    	$spdata=$this->makeFB($html,'hhad');
		$this->savetoRedis($spdata,'210');
		foreach($spdata as $val){
			unset($val['lotttime']);
			unset($val['ballid']);
			unset($val['single']);
			unset($val['p_status']);
			$this->savetoSPinfo($val,'fb_odds_hhad');
		}
	}
	/*
	*获取竞彩足球半全场的SP值
	*/
	public function hafu(){
		$stamptime=time();
		$fb_url=$this->odds_url.'hafu&_='.$stamptime;
		$html = $this->getHtmlContent_cookie($fb_url); 
		if(!$html){
			echo "数据为空,中断程序，执行下一步\n";
			return false;
		}
    	$spdata=$this->makeFB($html,'hafu');
		$this->savetoRedis($spdata,'213');
		foreach($spdata as $val){
			unset($val['lotttime']);
			unset($val['ballid']);
			unset($val['single']);
			unset($val['p_status']);
			$this->savetoSPinfo($val,'fb_odds_hafu');
		}
	}
	/*
	*获取竞彩足球比分的SP值
	*/
	public function crs(){
		$stamptime=time();
		$fb_url=$this->odds_url.'crs&_='.$stamptime;
		$html = $this->getHtmlContent_cookie($fb_url); 
		if(!$html){
			echo "数据为空,中断程序，执行下一步\n";
			return false;
		}
    	$spdata=$this->makeFB($html,'crs');
		$this->savetoRedis($spdata,'211');
		foreach($spdata as $val){
			unset($val['lotttime']);
			unset($val['ballid']);
			unset($val['single']);
			unset($val['p_status']);
			$this->savetoSPinfo($val,'fb_odds_crs');
		}
	}
	/*
	*获取竞彩足球总进球的SP值
	*/
	public function ttg(){
		$stamptime=time();
		$fb_url=$this->odds_url.'ttg&_='.$stamptime;
		$html = $this->getHtmlContent_cookie($fb_url); 
		if(!$html){
			echo "数据为空,中断程序，执行下一步\n";
			return false;
		}
    	$spdata=$this->makeFB($html,'ttg');
		$this->savetoRedis($spdata,'212');
		foreach($spdata as $val){
			unset($val['lotttime']);
			unset($val['ballid']);
			unset($val['single']);
			unset($val['p_status']);
			$this->savetoSPinfo($val,'fb_odds_ttg');
		}
	}
	/*
	*获取竞彩篮球让分胜负的SP值
	*/
	public function hdc(){
		$stamptime=time();
		$fb_url=$this->odds_url.'hdc&_='.$stamptime;
		$html = $this->getHtmlContent_cookie($fb_url); 
		if(!$html){
			echo "数据为空,中断程序，执行下一步\n";
			return false;
		}
    	$spdata=$this->makeBK($html,'hdc');
		$this->savetoRedis($spdata,'214');
		foreach($spdata as $val){
			unset($val['lotttime']);
			unset($val['ballid']);
			unset($val['single']);
			unset($val['p_status']);
			$this->savetoSPinfo($val,'bk_odds_hdc');
		}
	}
	/*
	*获取竞彩篮球胜负的SP值
	*/
	public function mnl(){
		$stamptime=time();
		$fb_url=$this->odds_url.'mnl&_='.$stamptime;
		$html = $this->getHtmlContent_cookie($fb_url); 
		if(!$html){
			echo "数据为空,中断程序，执行下一步\n";
			return false;
		}
    	$spdata=$this->makeBK($html,'mnl');
		$this->savetoRedis($spdata,'216');
		foreach($spdata as $val){
			unset($val['lotttime']);
			unset($val['ballid']);
			unset($val['single']);
			unset($val['p_status']);
			$this->savetoSPinfo($val,'bk_odds_mnl');
		}
	}
	/*
	*获取竞彩篮球大小分的SP值
	*/
	public function hilo(){
		$stamptime=time();
		$fb_url=$this->odds_url.'hilo&_='.$stamptime;
		$html = $this->getHtmlContent_cookie($fb_url); 
		if(!$html){
			echo "数据为空,中断程序，执行下一步\n";
			return false;
		}
    	$spdata=$this->makeBK($html,'hilo');
		$this->savetoRedis($spdata,'215');
		foreach($spdata as $val){
			unset($val['lotttime']);
			unset($val['ballid']);
			unset($val['single']);
			unset($val['p_status']);
			$this->savetoSPinfo($val,'bk_odds_hilo');
		}
	}
	/*
	*获取竞彩篮球胜分差的SP值
	*/
	public function wnm(){
		$stamptime=time();
		$fb_url=$this->odds_url.'wnm&_='.$stamptime;
		$html = $this->getHtmlContent_cookie($fb_url); 
		if(!$html){
			echo "数据为空,中断程序，执行下一步\n";
			return false;
		}
    	$spdata=$this->makeBK($html,'wnm');
		//
		$this->savetoRedis($spdata,'217');
		foreach($spdata as $val){
			unset($val['lotttime']);
			unset($val['ballid']);
			unset($val['single']);
			unset($val['p_status']);
			$this->savetoSPinfo($val,'bk_odds_wnm');
		}
	}

	/*
	*$lotteryNum 彩种编号
	*/
     public function makeFB($htmlcontent,$lotteryNum)
    {
        $sparr=$this->objectToArray(json_decode($htmlcontent));
        $i=0;
		$match=array();
        foreach($sparr['data'] as $row)
        {
           $match[$i]['s_code']='FB';
		   $match[$i]['m_id']=$row['id'];
		   $match[$i]['m_num']=getIntWeek(substr($row['num'], 0, -3)).preg_replace('@.*(\d{3})@','\\1',$row['num']);
		   $match[$i]['date']=$row['date'];
		   $match[$i]['time']=$row['time'];
		   
		   $match[$i]['lotttime']=date("Ymd",strtotime($row['b_date']));
		   $match[$i]['ballid']=preg_replace('@\d{1}(\d{3})@','\\1',$match[$i]['m_num']);
		   
		   
		   if($lotteryNum=='had' || $lotteryNum=='hhad'){
			  $match[$i]['p_id']=isset($row['had']) ? $row['had']['p_id'] : $row['hhad']['p_id'];
			  $match[$i]['a']=isset($row['had']) ? $row['had']['a'] : $row['hhad']['a'];
              $match[$i]['d']=isset($row['had']) ? $row['had']['d'] : $row['hhad']['d'];
              $match[$i]['h']=isset($row['had']) ? $row['had']['h'] : $row['hhad']['h'];
              $match[$i]['goalline']= isset($row['had']) ? ($row['had']['fixedodds']) : $row['hhad']['fixedodds'].".00";
              $match[$i]['single']=	isset($row['had']) ? $row['had']['single'] : $row['hhad']['single'];
              $match[$i]['p_status']=	isset($row['had']) ? $row['had']['p_status'] : $row['hhad']['p_status'];			  
		   }
		    if($lotteryNum=='hafu'){
			  $match[$i]['p_id']=$row['hafu']['p_id'];
			  $match[$i]['aa'] = $row['hafu']['aa'];
              $match[$i]['ad'] = $row['hafu']['ad'];
              $match[$i]['ah'] = $row['hafu']['ah'];	
              $match[$i]['da'] = $row['hafu']['da'];
              $match[$i]['dd'] = $row['hafu']['dd'];
              $match[$i]['dh'] = $row['hafu']['dh'];	
              $match[$i]['ha'] = $row['hafu']['ha'];
              $match[$i]['hd'] = $row['hafu']['hd'];
              $match[$i]['hh'] = $row['hafu']['hh'];
              $match[$i]['single']=	$row['hafu']['single'];
              $match[$i]['p_status']= $row['hafu']['p_status'];			  
		   }
		    if($lotteryNum=='crs'){
				$match[$i]['p_id']=$row['crs']['p_id'];
			    $match[$i]['0000']=$row['crs']['0000'];
			    $match[$i]['0001']= $row['crs']['0001'];
			    $match[$i]['0002']=$row['crs']['0002'];
			    $match[$i]['0003']=$row['crs']['0003'];
				$match[$i]['0004']=$row['crs']['0004'];
				$match[$i]['0005']=$row['crs']['0005'];
				$match[$i]['-1-a']=$row['crs']['-1-a'];
				$match[$i]['0100']=$row['crs']['0100'];
				$match[$i]['0101']=$row['crs']['0101'];
				$match[$i]['0102']=$row['crs']['0102'];

				$match[$i]['0103']=$row['crs']['0103'];
				$match[$i]['0104']=$row['crs']['0104'];
				$match[$i]['0105']=$row['crs']['0105'];
				$match[$i]['0200']=$row['crs']['0200'];
				$match[$i]['0201']=$row['crs']['0201'];
				$match[$i]['0202']=$row['crs']['0202'];
				$match[$i]['0203']=$row['crs']['0203'];
				$match[$i]['0204']=$row['crs']['0204'];
				$match[$i]['0205']=$row['crs']['0205'];
				$match[$i]['0300']=$row['crs']['0300'];
				$match[$i]['0301']=$row['crs']['0301'];
				$match[$i]['0302']=$row['crs']['0302'];
				$match[$i]['0303']=$row['crs']['0303'];

				$match[$i]['0400']=$row['crs']['0400'];
				$match[$i]['0401']=$row['crs']['0401'];
				$match[$i]['0402']=$row['crs']['0402'];
				$match[$i]['0500']=$row['crs']['0500'];
				$match[$i]['0501']=$row['crs']['0501'];
				$match[$i]['0502']=$row['crs']['0502'];
				$match[$i]['-1-h']=$row['crs']['-1-h'];
				$match[$i]['-1-d']=$row['crs']['-1-d'];
                $match[$i]['single']=	$row['crs']['single'];
                $match[$i]['p_status']= $row['crs']['p_status'];					
		   }
		  
		    if($lotteryNum=='ttg'){
			  $match[$i]['p_id']=$row['ttg']['p_id'];
			  $match[$i]['s0']=$row['ttg']['s0'];
              $match[$i]['s1']=$row['ttg']['s1'];
              $match[$i]['s2']=$row['ttg']['s2'];
              $match[$i]['s3']=$row['ttg']['s3'];
              $match[$i]['s4']=$row['ttg']['s4'];
              $match[$i]['s5']=$row['ttg']['s5'];
			  $match[$i]['s6']=$row['ttg']['s6'];
			  $match[$i]['s7']=$row['ttg']['s7'];
			  $match[$i]['single']=	$row['ttg']['single'];
              $match[$i]['p_status']= $row['ttg']['p_status'];
		   }
		   $i++;
        }
        return $match;
    }
	/*
	*$lotteryNum 彩种编号
	*/
    public function makeBK($htmlcontent,$lotteryNum)
    {
        $sparr=$this->objectToArray(json_decode($htmlcontent));
        $i=0;
		$match=array();
        foreach($sparr['data'] as $row)
        {
           $match[$i]['s_code']='BK';
		   $match[$i]['m_id']=$row['id'];
		   $match[$i]['m_num']=getIntWeek(substr($row['num'], 0, -3)).preg_replace('@.*(\d{3})@','\\1',$row['num']);
		   $match[$i]['date']=$row['date'];
		   $match[$i]['time']=$row['time'];
		   $match[$i]['lotttime']=date("Ymd",strtotime($row['b_date']));
		   $match[$i]['ballid']=preg_replace('@\d{1}(\d{3})@','\\1',$match[$i]['m_num']);
		   
		   if($lotteryNum=='mnl'){
			  $match[$i]['h'] = $row['mnl']['h'];
			  $match[$i]['a'] = $row['mnl']['a'];
              $match[$i]['p_id']=$row['mnl']['p_id'];
              $match[$i]['single']=	$row['mnl']['single'];
              $match[$i]['p_status']= $row['mnl']['p_status'];			  
		   }
		    if($lotteryNum=='hilo'){
			  $match[$i]['h']=$row['hilo']['h'];
              $match[$i]['l']=$row['hilo']['l'];
              $match[$i]['goalline']= isset($row['hilo']['fixedodds']) ? ($row['hilo']['fixedodds']."0") : '';
              $match[$i]['p_id']=$row['hilo']['p_id'];
              $match[$i]['single']=	$row['hilo']['single'];
              $match[$i]['p_status']= $row['hilo']['p_status'];			  
		   }
		    if($lotteryNum=='hdc'){
			  $match[$i]['h']=$row['hdc']['h'];
              $match[$i]['a']=$row['hdc']['a'];
              $match[$i]['goalline']= isset($row['hdc']['fixedodds']) ? ($row['hdc']['fixedodds']."0") : '';
              $match[$i]['p_id']=$row['hdc']['p_id'];
              $match[$i]['single']=	$row['hdc']['single'];
              $match[$i]['p_status']= $row['hdc']['p_status'];			  
		   }
		    if($lotteryNum=='wnm'){
			  $match[$i]['w1']=$row['wnm']['w1'];
              $match[$i]['w2']=$row['wnm']['w2'];
              $match[$i]['w3']=$row['wnm']['w3'];
              $match[$i]['w4']=$row['wnm']['w4'];
              $match[$i]['w5']=$row['wnm']['w5'];
              $match[$i]['w6']=$row['wnm']['w6'];
              $match[$i]['l1']=$row['wnm']['l1'];
              $match[$i]['l2']=$row['wnm']['l2'];
              $match[$i]['l3']=$row['wnm']['l3'];	
              $match[$i]['l4']=$row['wnm']['l4'];	
              $match[$i]['l5']=$row['wnm']['l5'];
              $match[$i]['l6']=$row['wnm']['l6'];
              $match[$i]['p_id']=$row['wnm']['p_id'];
              $match[$i]['single']=	$row['wnm']['single'];
              $match[$i]['p_status']= $row['wnm']['p_status'];			  
		   }
		   $i++;
        }
        return $match;
    }
    //保存入库
    public function savetoSPinfo($data,$table){
		
       if($data['s_code']=='' || $data['m_id']==''){
          return false;
          exit;
       }else{
		 $sql="select * from ".$table." where 1 and m_id='".$data['m_id']."'";
		 $rows =$this->DB->query($sql);
		 $flag=false;
		 //比较录入的数据与数据里最新的数据是否一致，如果不一致添加入库
		 if($rows){
			 foreach($data as $key=>$val){
			 if($val!=$rows[0][$key]){
				 $flag=true;
			 }
		    } 
		 }else{
			$flag=true; 
		 }
       	if($flag){
            $data['createtime']=date('Y-m-d H:i:s',time());
			$this->DB->table($table)->where("m_id='".$data['m_id']."'")->delete();
			$res1= $this->DB->data($data)->table($table)->add();
			$res= $this->DB->data($data)->table($table."_HIS")->add();
            if($res && $res1){
               echo "table:$table update sp data,table:$table"."_HIS,add sp data success  \n";
            }else{
               echo "error：".$this->DB->error()." \n";
			   
            }
			$this->DB->free();
       	}else{
			;//echo "没有SP数据需要更新 \n";
		}
       }
    }
    //
    protected function get_date_by_week($thisWeek){
	$retdate = '';

	$weekint = '';
	if(is_numeric($thisWeek)){
		$weekint = $thisWeek;
	} else {
		$weekint = $this->getIntWeek($thisWeek);
	}
	// 1或7均为周日
	if($weekint == 7) $weekint = 0;

	$week = date('w');
	$n = $weekint - $week;
	$m = 7 - abs($n);

	if(abs($m) > abs($n)) {
		$retdate = date('Ymd', (time() + $n * 24 * 3600));
	} else {
		$retdate = date('Ymd', (time() - $m * 24 * 3600));
	}

	// 反验证生成是否正确，如果不正确返回false
	$newweek = date('w', strtotime($retdate));

	if($newweek != $weekint){
		return false;
	}

	return $retdate;
   }

	/**
	 * 验证SP值
	 * 如果格式不是546565.21、23.233或65456.1则返回空
	 * 如果格式正确，则返回该	SP值
	 * @param string $str 要验证的SP值
	 */
	protected function check_data($str){
		$pregstr = "/^\d+(\.\d{1,3})?$/";
		if(preg_match($pregstr, $str)){
			if($str == '0000') $str = '-';
			return $str;
		} else {
			return '';
		}
	}
	//
function getIntWeek($weekName){
	$weekint = '';

	$weekarr = array(
		'7' => '周日',
		'1' => '周一',
		'2' => '周二',
		'3' => '周三',
		'4' => '周四',
		'5' => '周五',
		'6' => '周六'
	);
	$weekName=iconv('gbk', 'utf-8', $weekName);
	if(in_array($weekName, $weekarr)){
		$weekint = str_replace(array_values($weekarr), array_keys($weekarr), $weekName);
	} else {
		$weekint = -1;
	}

	return intval($weekint);
}

	protected function getScore($istr)
	{
		$str = preg_replace("'<[\/\!]*?[^<>]*?>'si", '', $istr);
		$reg = '/([+-]?\d+(\.\d+)?)/';
		$arr = array();
		preg_match_all($reg, $str, $arr);

		if(isset($arr[1][0])){
			return end($arr[1]);
		} else {
			return '';
		}
	}
	protected function getZK_sfc_sp($sp_str)
	{
		$spstr = '';
		$pregstr = "'<[\/\!]*?[^<>]*?>|\s*'si";
		$spstr = preg_replace("'<br\s*?[\/\!]*?>'si", '###', $sp_str);
		$spstr = trim(preg_replace($pregstr, '', $spstr));
		return explode('###', $spstr);
	}
	protected function objectToArray($e){
	    $e=(array)$e;
	    foreach($e as $k=>$v){
	        if( gettype($v)=='resource' ) return;
	        if( gettype($v)=='object' || gettype($v)=='array' )
	            $e[$k]=(array)$this->objectToArray($v);
	    }
	    return $e;
	}
	//保存redis库
	public function savetoRedis($spdata,$lotteryid){
		//抓取数据入redis库
		global $_RC;
	    include_once ROOT.'/Class/redis/Redisspvalue.class.php';
		$redis=new Redisspvalue($_RC['HOST'],$_RC['PWD']);
	    $redis->setSp($lotteryid,$spdata);
		echo "保存数据到redis服务器完成 \n";
		//redis end;
	}
}
