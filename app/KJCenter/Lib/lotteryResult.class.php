<?php 

/**
 * 抓取数字彩开奖信息
 * @param string
 * @date 2015.02
 */

class lotteryResult{
	public $charset='utf-8';
	public $DB;
	public $endtime = 20;
	public $officetime = 1;
	
	private $lotteryid = '';
	private $httpurl = '';
	private $fromsource = '';
	private $kjinfoData = array();
	private $formatData = array();
	private $cacheDate = array();
	private $zjData = array();
	private $zjinfoData = array();
	
	//传统足彩使用
	private $lotteryissue = '';
	private $datasource = array();
	private $ballinfo = array();
	
	/**
	 * 启用MYSQL数据库
	 * @param
	 */
	public function initDB(){
		require_once CLASS_ROOT.'mysql.class.php';
		$this->DB = new MySQL(DB_HOST,DB_USER,DB_PWD,DB_NAME);
		$this->TAB_KJ = 'tab_kaijiang_info';
		$this->TAB_ZC_SC = 'tab_issueinfo_extend_football';
		$this->TAB_ZC_ISSUE = 'tab_cz_issue_info';
		$this->TAB_ZJ_INFO = 'tab_cz_zj_info';
	}
	
	/**
	 * 彩乐乐执行开奖数据抓取任务
	 * @param string $lotteryid, $url, $fromsource
	 */
	public function runCailele($lotteryid, $url, $fromsource){
		$this->lotteryid = $lotteryid;
		$this->httpurl = $url;
		$this->fromsource = $fromsource;
		$this->getxmldata();
		$this->compareCacheInfo();
	}
	
	/**
	 * zhcw-执行开奖数据抓取任务
	 * @param string $lotteryid, $url, $fromsource
	 */
	public function runZhcw($lotteryid, $url, $fromsource){
		$this->lotteryid = $lotteryid;
		$this->httpurl = $url;
		$this->fromsource = $fromsource;
		$this->makehtml_zhcw();
	}
	
	/**
	 * 中国体彩网-执行开奖数据抓取任务
	 * @param string $lotteryid, $url, $fromsource
	 */
	public function runGov($url, $fromsource){
		$this->httpurl = $url;
		$this->fromsource = $fromsource;
		$this->makehtml_gov();
	}
	

	/**
	 * 500w-执行开奖数据抓取任务
	 * @param string $lotteryid, $url, $fromsource
	 */
	public function runCache($lotteryid, $url, $fromsource){
		$this->lotteryid = $lotteryid;
		$this->httpurl = $url;
		$this->fromsource = $fromsource;
		$this->getxmldata();
		//500w开奖信息写入缓存
		$this->writetocache($this->formatData, 'kaijiang_'.$this->lotteryid);
	}
	
	/**
	 * 500w-抓取传统足彩开奖数据
	 * @param string $lotteryid, $url, $fromsource
	 */
	public function runZC($lotteryid, $url, $issue){
		$this->lotteryid = $lotteryid;
		$this->httpurl = $url;
		$this->lotteryissue = $issue;
		if($this->lotteryissue){
			$this->httpurl = $url.'?e='.$this->lotteryissue;
		}else{
			$this->httpurl = $url;
		}
		if(in_array($lotteryid, array('324','441'))){
			$this->makehtml_sfc();
		}else{
			$this->makehtml();
		}
		$this->setFormatData_zc();
		//录入数据
		$this->updateKJInfo();
	}
	
	/**
	 * 异步通知
	 * @2015-10-19
	 */
	public function notifyResult($msgarr){
		$cz_arr = array('118'=>'1','116'=>'2','117'=>'3','100'=>'283','102'=>'284','103'=>'282','106'=>'281','108'=>'324','109'=>'441','110'=>'326','111'=>'325');
		$cz_id = $cz_arr[$msgarr[0]];
		$where = '`cz_id` = '.$cz_id.' and `kj_issue` = '.$msgarr[1];
		$result = $this->selectquery($this->TAB_KJ, 'send_flag', $where, '', '');
		if($result){
			$flag = str_split($result[0]['send_flag']);
			$savedata['sent_time'] = date('Y-m-d H:i:s');
			if($msgarr[2] == 'tob2c'){
				$savedata['send_flag'] = '3'.$flag[1];
			}else if($msgarr[2] == 'tob2b'){
				$savedata['send_flag'] = $flag[0].'3';
			}
			echo "update message:".$savedata['send_flag']."\n";
			$this->updatequery($this->TAB_KJ, $savedata, $where);
		}
		echo "-------------------END-------------------------\n";
	}
	
	
	/**
	 * 开奖地址解析
	 * @param string $file
	 */
	protected function getxmldata($file = ''){
		$xmlstr = $this->reCurl($this->httpurl);
		$xmlobj = @simplexml_load_string($xmlstr);
		$array = array();
		foreach($xmlobj as $key=>$value){
			$temp = (array)$value;
			$array[] = $temp['@attributes'];
		}
		// 检查是否已经获取到了数据
		if(is_null($array) || count($array) < 1){
			return false;
		}
		$this->setFormatData($array);
	}
	/**
	 * 重组数据
	 * @param array $array
	 */
	protected function setFormatData($array = array()){
		$this->formatData = array();
		$this->formatData['cz_id'] = $this->lotteryid;
		$this->formatData['cz_name'] = $this->getczname($this->lotteryid);
		//期号处理
		if($this->fromsource == '500w' && in_array($this->lotteryid, array(1,3))){
			$this->formatData['kj_issue'] = '20'.$array[0]['expect'];
		}else{
			$this->formatData['kj_issue'] = $array[0]['expect'];
		}
		//开奖号码处理
		$opencode = preg_replace('/(,)$/i','',$array[0]['opencode']);
		$lotterycode = preg_replace('/(\|)+/i',' ',$opencode);
		$lotterycode = preg_replace('/(,)+/i',' ',$lotterycode);
		if(in_array($this->lotteryid, array('1','3','281'))){
			$codearr = explode(' ',$lotterycode);
			if($this->lotteryid == '1'){
				$subset = array_splice($codearr, 6);
			}else if($this->lotteryid == '3'){
				$subset = array_splice($codearr, 7);
			}else if($this->lotteryid == '281'){
				$subset = array_splice($codearr, 5);
			}
			$this->formatData['kj_z_num'] = implode(' ',$codearr);
			$this->formatData['kj_t_num'] = implode(' ',$subset);
		}else{
			$this->formatData['kj_z_num'] = $lotterycode;
			$this->formatData['kj_t_num'] = '-1';
		}
		$this->formatData['t_one'] = '-1';
		$this->formatData['t_two'] = '-1';
		$this->formatData['t_three'] = '-1';
		$this->formatData['t_four'] = '-1';
		$this->formatData['t_five'] = '-1';
		$this->formatData['t_flag'] = '0';
		$this->formatData['is_current_issue'] = '0';
		$this->formatData['p_name'] = 'system';
		$this->formatData['p_time'] = date('Y-m-d H:i:s');
		$opentime = explode(' ',$array[0]['opentime']);
		$this->formatData['kj_date'] = $opentime[0];
	}
	

	/**
	 * 普通数字彩比对缓存数据
	 */
	protected function compareCacheInfo(){
		$cachelist = array();
		$cache_path = CACHE_ROOT.'lottery_result';
		// 缓存名称
		$cache_name = 'lottery_kaijiang_'.$this->formatData['cz_id'];
		$this->cacheDate = cachedata($cache_name,'','','',$cache_path);
		if($this->cacheDate){
			$cachestr = $this->cacheDate['cz_id'].'^'.$this->cacheDate['kj_issue'].'^'.$this->cacheDate['kj_z_num'].'^'.$this->cacheDate['kj_t_num'];
		}
		$tempkjinfo = $this->formatData['cz_id'].'^'.$this->formatData['kj_issue'].'^'.$this->formatData['kj_z_num'].'^'.$this->formatData['kj_t_num'];
		//比对zhcw数据和500w数据
		if($tempkjinfo == $cachestr){
			//更新开奖日期
			$this->formatData['kj_date'] = $this->cacheDate['kj_date'];
			$this->updateKJInfo();
		}
	}
	

	/**
	 *---------------------------------------------
	 * 500w-传统足彩使用方法 start. 2015.03.24
	 *---------------------------------------------
	 */
	
	/**
	 * 足彩开奖数据重组
	 */
	protected function setFormatData_zc(){
		$this->formatData = array();
		$this->formatData['cz_id'] = $this->lotteryid;
		$this->formatData['cz_name'] = $this->getczname($this->lotteryid);
		$this->formatData['kj_issue'] = $this->datasource[0]['lotteryissue'];
		$gameresult = '';
		foreach($this->datasource as $k=>$v){
			$gameresult .= $v['gameresult'].' ';
		}
		$this->formatData['kj_z_num'] = trim($gameresult);
		$this->formatData['kj_t_num'] = '-1';
		$this->formatData['t_one'] = '-1';
		$this->formatData['t_two'] = '-1';
		$this->formatData['t_three'] = '-1';
		$this->formatData['t_four'] = '-1';
		$this->formatData['t_five'] = '-1';
		$this->formatData['t_flag'] = '0';
		$this->formatData['is_current_issue'] = '0';
		$this->formatData['p_name'] = 'system';
		$this->formatData['p_time'] = date('Y-m-d H:i:s');
		$this->formatData['kj_date'] = date('Y-m-d');
	}
	
	/**
	 * 足彩期信息查询
	 * @param $lotteryid string
	 */
	public function getzcissue($lotteryid){
		//$where = '`cz_id` = '.$lotteryid.' and `endtimestamp` <= \''.date('Y-m-d H:i:s').'\'';
		$where = '`cz_id` = '.$lotteryid;
		$result = $this->selectquery($this->TAB_ZC_ISSUE, 'lotteryissue', $where, 'lotteryissue desc', ' limit 3');
		return $result;
	}
	
	/**
	 * 获取足彩赛果_半全场/四场进球
	 */
	protected function makehtml(){
		$this->datasource = array();
		$html = file_get_contents($this->httpurl);
		if($this->charset != 'UTF-8'){
			$html = mb_convert_encoding($html, 'UTF-8','gb2312');
		}
		//获取期
		$parten_aissue="/<select id=\"sel_expect\".*>(.*)<\/select>/iUs";
		preg_match_all($parten_aissue,trim($html),$htmlmatch);
		$issue=preg_replace('@.*<option.*>(.*)<\/option>.*@', '\\1', $htmlmatch[1][0]);
		//获取对阵信息
		$parten='/<table.*?id=\"table_match\"*>(.*)<\/table>/iUs';
		preg_match_all($parten,trim($html),$match);
		$parten_tr='/<tr.*>(.*)<\/tr>/iUs';
		preg_match_all($parten_tr,trim($match[1][0]),$match2);
		$parten_td = "/<td.*>(.*)<\/td>/iUs";
		$parten_a="/<a.*>(.*)<\/a>/iUs";
		$parten_strong="/<strong.*>(.*)<\/strong>/iUs";
		foreach($match2[0] as $key=>$val){
			$tempresult = '';
			preg_match_all($parten_td, $val, $matchtd);
			if($key != 0){
				if(count($matchtd[1])==12){
					$temp = array();
					foreach($matchtd[1] as $key2=> $val2){
						if($key2==5 || $key2==7 ||$key2==10){
							$str=$val2;
						}else{
							$str=strip_tags($val2);
						}
						$str=str_replace("\n","",$str);
						$str=str_replace("\r","",$str);
						$str=str_replace("  ","",$str);
						$str = trim($str);
						$temp[$key2]= $str;
					}
					$this->datasource[$key]['ballid']=trim($temp[0]);
					$this->datasource[$key]['lotteryissue']=trim($issue);
					preg_match_all($parten_a, $temp[5], $hmatch);
					$this->datasource[$key]['homename']=trim($hmatch[1][0]);
					preg_match_all($parten_a, $temp[7], $gmatch);
					$this->datasource[$key]['guestname']=trim($gmatch[1][0]);
					preg_match_all($parten_strong, $temp[10], $rmatch);
					$tempresult = trim($rmatch[1][0]);
					if($tempresult == ''){
						$tempresult = '*';
					}
					$this->datasource[$key]['gameresult']=$tempresult;
				}else{
					if(in_array($this->lotteryid, array('326','325'))){
						preg_match_all($parten_strong, $matchtd[1][0], $rmatchtd);
						$tempresult = trim($rmatchtd[1][0]);
						if($tempresult == ''){
							$tempresult = '*';
						}
						$this->datasource[$key-1]['gameresult']=$this->datasource[$key-1]['gameresult'].' '.$tempresult;
					}
				}
			}
		}
		$balls = array();
		foreach ($this->datasource as $data) {
			$balls[] = $data['ballid'];
		}
		array_multisort($balls, SORT_ASC, $this->datasource);
	}
	
	/**
	 * 获取足彩赛果_胜负彩/任九场
	 */
	protected function makehtml_sfc(){
		$this->datasource = array();
		$html = file_get_contents($this->httpurl);
		if($this->charset != 'UTF-8'){
			$html = mb_convert_encoding($html, 'UTF-8','gb2312');
		}
		//获取期
		$parten_aissue="/<select id=\"sel_expect\".*>(.*)<\/select>/iUs";
		preg_match_all($parten_aissue,trim($html),$htmlmatch);
		$issue=preg_replace('@.*<option.*>(.*)<\/option>.*@', '\\1', $htmlmatch[1][0]);
		//获取对阵信息
		$parten='/<table.*?id=\"table_match\"*>(.*)<\/table>/iUs';
		preg_match_all($parten,trim($html),$match);
		$parten_tr='/<tr id=\"(.*)\".*>(.*)<\/tr>/iUs';
		preg_match_all($parten_tr,trim($match[1][0]),$match2);
		$parten_td = "/<td.*>(.*)<\/td>/iUs";
		$parten_a="/<a.*>(.*)<\/a>/iUs";
		$parten_strong="/<strong.*>(.*)<\/strong>/iUs";
		foreach($match2[0] as $key=>$val){
			$tempresult = '';
			preg_match_all($parten_td, $val, $matchtd);


			if(count($matchtd[1])==12){
				$temp = array();
				foreach($matchtd[1] as $key2=> $val2){
					if($key2==5 || $key2==7 ||$key2==10){
						$str=$val2;
					}else{
						$str=strip_tags($val2);
					}
					$str=str_replace("\n","",$str);
					$str=str_replace("\r","",$str);
					$str=str_replace("  ","",$str);
					$str = trim($str);
					$temp[$key2]= $str;
				}
				$this->datasource[$key]['ballid']=trim($temp[0]);
				$this->datasource[$key]['lotteryissue']=trim($issue);
				preg_match_all($parten_a, $temp[5], $hmatch);
				$this->datasource[$key]['homename']=trim($hmatch[1][0]);
				preg_match_all($parten_a, $temp[7], $gmatch);
				$this->datasource[$key]['guestname']=trim($gmatch[1][0]);
				preg_match_all($parten_strong, $temp[10], $rmatch);
				$tempresult = trim($rmatch[1][0]);
				if($tempresult == ''){
					$tempresult = '*';
				}
				$this->datasource[$key]['gameresult']=$tempresult;
			}else{
				if(in_array($this->lotteryid, array('326','325'))){
					preg_match_all($parten_strong, $matchtd[1][0], $rmatchtd);
					$tempresult = trim($rmatchtd[1][0]);
					if($tempresult == ''){
						$tempresult = '*';
					}
					$this->datasource[$key-1]['gameresult']=$this->datasource[$key-1]['gameresult'].' '.$tempresult;
				}
			}
		}
		$balls = array();
		foreach ($this->datasource as $data) {
			$balls[] = $data['ballid'];
		}
		array_multisort($balls, SORT_ASC, $this->datasource);
	}
	
	/**
	 *---------------------------------------------
	 * zhcw-双色球,3D,七乐彩使用方法 start. 2015.03
	 *---------------------------------------------
	 */
	
	protected function makehtml_zhcw(){
		$html=file_get_contents($this->httpurl);
		$htmlarr = explode(';',$html);
		//开奖详情
		$kjData = str_replace('var kjData_'.$this->lotteryid.' = eval(\'(\' + \'','',trim($htmlarr[0]));
		$kjData = str_replace('\' + \')\')','',$kjData);
		$kjDataArr = $this->objectToArray(json_decode($kjData));
		$issueNos = str_replace('var issueNos_'.$this->lotteryid.' =', '', $htmlarr[2]);
		$issueNos = str_replace('"', '', trim($issueNos));
		//开奖号码更新
		$issueArr = explode(',',$issueNos);
		$issue = $issueArr[0];
		if($kjDataArr[$issue]){
			$newarr = $kjDataArr[$issue];
			$this->setFormatData_zhcw($newarr);
			//中彩网和500w缓存数据比对
			$this->compareCacheInfo();
		}
	}
	
	/**
	 * 重组开奖数据，存入库中
	 * @param $array array 重组内容
	 */
	protected function setFormatData_zhcw($array){
		$this->formatData = array();
		$this->formatData['cz_id'] = $this->lotteryid;
		$this->formatData['cz_name'] = $this->getczname($this->lotteryid);
		$this->formatData['kj_issue'] = $array['kjIssue'];
		if(in_array($this->lotteryid, array('1','3'))){
			$newarray = explode(' ',$array['kjZNum']);
			sort($newarray);
			$array['kjZNum'] = implode(' ',$newarray);
		}
		$this->formatData['kj_z_num'] = $array['kjZNum'];
		$this->formatData['kj_t_num'] = $array['kjTNum'];
		$this->formatData['t_one'] = '-1';
		$this->formatData['t_two'] = '-1';
		$this->formatData['t_three'] = '-1';
		$this->formatData['t_four'] = '-1';
		$this->formatData['t_five'] = '-1';
		$this->formatData['t_flag'] = '0';
		$this->formatData['is_current_issue'] = '0';
		$this->formatData['p_name'] = 'system';
		$this->formatData['p_time'] = date('Y-m-d H:i:s');
		$this->formatData['kj_date'] = $array['kjDate'];
	}
	
	
	/**
	 *---------------------------------------------
	 * 中国体彩网-体彩玩法使用方法 start. 2015.03
	 *---------------------------------------------
	 */
	/**
	 * 中国体彩网-获取开奖信息
	 */
	protected function makehtml_gov(){
		$html = file_get_contents($this->httpurl);
		$parten='/<tr align=\"center\".*>(.*)<\/tr>/iUs';
		preg_match_all($parten,trim($html),$draws);
		$parten_td = "/<td.*>(.*)<\/td>/iUs";
		foreach($draws[1] as $key=>$val){
			$this->formatData = array();
			preg_match_all($parten_td, $val, $drawstd);
			$temp=array();
			foreach($drawstd[1] as $key2=>$val2){
				$str=strip_tags($val2);
				$str = trim($str);
				if($str != ''){
					$temp[$key2]= $str;
				}
			}
			if($temp[0] == '大乐透'){
				$this->formatData['cz_id'] = '281';
				$this->formatData['cz_name'] = '超级大乐透';
			}else if($temp[0] == '排列3'){
				$this->formatData['cz_id'] = '283';
				$this->formatData['cz_name'] = '排列三';
			}else if($temp[0] == '排列5'){
				$this->formatData['cz_id'] = '284';
				$this->formatData['cz_name'] = '排列五';
			}else if($temp[0] == '7星彩'){
				$this->formatData['cz_id'] = '282';
				$this->formatData['cz_name'] = '七星彩';
			}
			$temp_z_num='';$temp_t_num='';
			foreach($temp as $key3=>$val3){
				if($key3 >= 2 && $key3%2 == 0){
					if($temp[0] == '大乐透'){
						if($key3 <= 10){
							$temp_z_num .=$temp[$key3].' ';
						}else{
							$temp_t_num .=$temp[$key3].' ';
						}
					}else{
						$temp_z_num .=$temp[$key3].' ';
						$temp_t_num = '-1';
					}
				}
			}
			$this->formatData['kj_z_num'] = trim($temp_z_num);
			$this->formatData['kj_t_num'] = trim($temp_t_num);
			$this->formatData['kj_issue'] = $temp[1];
			$this->formatData['t_one'] = '-1';
			$this->formatData['t_two'] = '-1';
			$this->formatData['t_three'] = '-1';
			$this->formatData['t_four'] = '-1';
			$this->formatData['t_five'] = '-1';
			$this->formatData['t_flag'] = '0';
			$this->formatData['is_current_issue'] = '0';
			$this->formatData['p_name'] = 'system';
			$this->formatData['p_time'] = date('Y-m-d H:i:s');
			$this->formatData['kj_date'] = date('Y-m-d');
			//中国体彩网和500w缓存数据比对
			$this->compareCacheInfo();
		}
		
	}
	

	/**
	 *---------------------------------------------
	 * 公共使用  start. 2015.03
	 *---------------------------------------------
	 */
	
	/**
	 * 更新开奖数据
	 * @param array $formatData 更新内容
	 */
	public function updateKJInfo(){
		$tempkjlist = array();
		if(empty($this->formatData['cz_id']) || empty($this->formatData['kj_issue'])){
			return false;
		}
		$where = '`cz_id` = '.$this->formatData['cz_id'].' and `kj_issue` = '.$this->formatData['kj_issue'];
		$this->kjinfoData = $this->selectquery($this->TAB_KJ, '*', $where, '', '');
		if(count($this->kjinfoData) > 0){
			foreach ($this->kjinfoData as $kj){
				$tempkjlist[$kj['cz_id'].$kj['kj_issue']] = array(
						'kjinfo' => $kj['kj_z_num'].'^'.$kj['kj_t_num']
				);
			}
		}
		$tempkey = $this->formatData['cz_id'].$this->formatData['kj_issue'];
		if(in_array($tempkey, array_keys($tempkjlist))){
			$tempvalue['kj_z_num']=$this->formatData['kj_z_num'];
			$tempvalue['kj_t_num']=$this->formatData['kj_t_num'];
			$tempvalue['kj_date']=$this->formatData['kj_date'];
			$tempstr = $this->formatData['kj_z_num'].'^'.$this->formatData['kj_t_num'];
			if($tempkjlist[$tempkey]['kjinfo'] != $tempstr){
				$updatearr = array('324','325','326','441');
				if(in_array($this->formatData['cz_id'],$updatearr)){
					$str = strpos($tempkjlist[$tempkey]['kjinfo'],"*");
					if($str != false || $str == 0){
						$where = '`cz_id` = '.$this->formatData['cz_id'].' and `kj_issue` = '.$this->formatData['kj_issue'];
						$this->updatequery($this->TAB_KJ, $tempvalue, $where);
					}
				}	
			}
		}else{
			$this->writequery($this->TAB_KJ, $this->formatData);
			//2015-10-15
			$arr = array('324','325','326','441');
			if(!in_array($this->formatData['cz_id'],$arr)){
				$where2 = '`cz_id` = '.$this->formatData['cz_id'].' and `kj_issue` = '.$this->formatData['kj_issue'];
				$where3 = '`cz_id` = '.$this->formatData['cz_id'].' and `kj_issue` != '.$this->formatData['kj_issue'];
				$temp2['is_current_issue']='1';
				$temp3['is_current_issue']='0';
				$this->updatequery($this->TAB_KJ, $temp2, $where2);
				$this->updatequery($this->TAB_KJ, $temp3, $where3);	
			}
			//end
		}
	}
	
	
	/**
	 * 获取彩种名称
	 * @param $lotteryid string
	 */
	public function getczname(){
		$czname = array(
				'1' => '双色球',
				'2' => '3D',
				'3' => '七乐彩',
				'281' => '超级大乐透',
				'282' => '七星彩',
				'283' => '排列三',
				'284' => '排列五',
				'324' => '胜负彩',
				'325' => '四场进球',
				'326' => '半全场',
				'441' => '胜负彩任九场'
		);
		return $czname[$this->lotteryid];
	}
	
	/**
	 * 连接MYSQL:根据条件查询信息
	 * @param string $table 查询表 
	 * @param string $fields 字段 默认*
	 * @param string $where $order $limit 查询条件
	 */
	public function selectquery($table, $fileds, $where, $order, $limit){
		$select_sql = 'select ';
		$fields = !empty($fileds)?$fileds:'*';
		$select_sql.=$fields;
		$select_sql.= ' from `'.$table.'` ';
		!empty($where)?($select_sql.=' where '.$where):'';
		!empty($order)?($select_sql.=' order by '.$order):'';
		!empty($limit)?($select_sql.=' '.$limit):'';
		//echo $select_sql;
		$result=$this->DB->query($select_sql);
		return $result;
	}
	
	/**
	 * 连接MYSQL:插入表
	 * @param string $table 表名
	 * @param array $data 插入内容
	 */
	public function writequery($table, $data)
	{
		$add_sql = 'insert into `'.$table.'` (';
		$value = $field = '';
		foreach($data as $k=>$v){
			$field .= '`'.$k.'`,';
			if(is_int($v))
				$value .= $v.',';
			else
				$value .= '\''.$v.'\',';
		}
		$add_sql .= rtrim($field,',').') values ('.rtrim($value,',').')';
		//echo $add_sql.'<br />';
		//exit;
		$result = $this->DB->execute($add_sql);
		return $result;
	}
	
	/**
	 * 连接MYSQL:更新表
	 * @param string $table 表名
	 * @param array $data 更新内容
	 * @param string $where 更新条件
	 */
	public function updatequery($table, $data, $where){
		$update_sql = 'update `'.$table.'` set ';
		foreach($data as $k=>$v){
			if(is_numeric($v))
				$update_sql .= '`'.$k.'` = '.$v.',';
			else
				$update_sql .= '`'.$k.'` = \''.$v.'\',';
		}
		$update_sql = rtrim($update_sql,',');
		if(isset($where))
			$update_sql .= ' where '.$where;
		$result = $this->DB->execute($update_sql);
		return $result;
	}
	
	/**
	 * 对象转化成数组
	 * @param $e array
	 */
	protected function objectToArray($e){
		$e=(array)$e;
		foreach($e as $k=>$v){
			if( gettype($v)=='resource' ) return;
			if( gettype($v)=='object' || gettype($v)=='array' )
				$e[$k]=(array)$this->objectToArray($v);
		}
		return $e;
	}
	
	/**
	 * 请求方式
	 * @param string $remote_server 请求地址
	 */
	function reCurl($remote_server){
		$header[] = "Content-type: text/xml; charset=utf-8";
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$remote_server);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($ch, CURLOPT_POST, 0);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	/**
	 * 开启缓存
	 * @param array $data 缓存数据
	 * @param string $value 缓存标识
	 */
	public function writetocache($data, $value){
		$cache_path = CACHE_ROOT.'lottery_result';
		// 缓存名称
		$cache_name = 'lottery_'.$value;
		$cache_time = -1;
		cachedata($cache_name, $data, $cache_time, '', $cache_path);
	}
}



/**
 * 开启抓取日志
 * @param string $msg
 */
function writekjlog($msg){
	/*$logfilename = APP_ROOT . 'log/' . date('Ymd') . '.log';
	if (! file_exists ( $logfilename )) {
		mkpath ( $logfilename );
	}

	// 记录接收到的数据包
	error_log ( '['.date('Y-m-d H:i:s').']' . $msg . chr ( 13 ) . chr ( 10 ), 3, $logfilename );
	*/
	$logfilename =  date('YmdH') .'.log';
	// 记录接收到的数据包
	error_log ( '['.date('Y-m-d H:i:s').']' . $msg . chr ( 13 ) . chr ( 10 ), 3, $logfilename );
	
}


?>