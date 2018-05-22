<?php
namespace Home\Controller;
use Home\Controller\BaseController;
class IssueController extends BaseController {
	/**
	 * 	ORACLE玩法期管理
	 */
    function index(){
		extract($_REQUEST, EXTR_OVERWRITE);
      	global $play_status;
      	include APP_PATH.'Home/Conf/caizhong.php';
		$starttime = isset($starttime) ? trim($starttime) : date('Y-m-d',time()-1*24*3600);
      	$endtime = isset($endtime) ? trim($endtime) : date('Y-m-d',time()+1*24*3600);
      	$source = isset($source) ? $source : 'b2c';
      	$status = isset($status) ? $status : '';
      	if($act == 'query'){
	      	$model = D('Issue');
	      	$pageNum = isset($pageNum) ? $pageNum : '1';
	      	$numPerPage = isset($numPerPage) ? $numPerPage : '20';
	      	if($source == 'b2c'){
	      		$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB1'));
	      	}else if($source == 'b2b'){
	      		$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB2'));
	      	}
	      	$header=array(
	      			'transactiontype' => '60004',
	      			'messengerid' => time('YmdHis',time()).uniqid(6),
	      			'agenterid' => '10000001',
	      	);
	      	$ielement=array(
	      			'lotteryid' => $lotteryid,
	      			'lotteryissue' => $lotteryissue,
	      			'startdate' => $starttime,
	      			'enddate' => $endtime,
	      			'status' => $status,
	      			'sortmode' => '',
	      			'sortparameter' => 'lotteryid',
	      			'pageindex'     => $pageNum,
	      			'pagesize'      => $numPerPage,
	      			'pagetotal'     => ''
	      	);
	      	$HttpObj->send($header,$ielement,'10000001','','web');
	      	$pageTotal = $HttpObj->getoelementvalue('pagetotal');
	      	$olist = $HttpObj->getelements();
	      	$issuelist = array();
	      	foreach($olist as $key=>$val){
	      		$issuelist[$key]['lotteryid'] = $val['lotteryid'];
	      		$issuelist[$key]['description'] = $val['description'];
	      		$issuelist[$key]['lotteryissue'] = $val['lotteryissue'];
	      		$issuelist[$key]['lotterytype'] = $val['lotterytype'];
	      		$issuelist[$key]['starttimestamp'] = date('Y-m-d H:i',strtotime($val['starttimestamp']));
	      		$issuelist[$key]['endtimestamp'] = date('Y-m-d H:i',strtotime($val['endtimestamp']));
	      		$issuelist[$key]['endtime1'] = date('Y-m-d H:i',strtotime($val['endtime1']));
	      		$issuelist[$key]['endtime2'] = date('Y-m-d H:i',strtotime($val['endtime2']));
	      		$issuelist[$key]['endtime3'] = date('Y-m-d H:i',strtotime($val['endtime3']));
	      		$issuelist[$key]['status'] = $val['status'];
	      		$issuelist[$key]['statusdesc'] = $this->get_lotterystatus($val['status']);
	      	}
	      	$totalCount = $pageTotal * $numPerPage;
	      	$this->assign('issuelist',$issuelist);
	      	$this->assign('pageTotal',$pageTotal);
	      	$this->assign('totalCount',$totalCount);
	      	$this->assign('pageNumShown','5');
	      	$this->assign('numPerPage',$numPerPage);
	      	$this->assign('currentPage',$pageNum);
		}
		$play_list = $this->getSystemPlayWay('b2c');
		$this->assign('lotteryid',$_REQUEST['lotteryid']);
		$this->assign('lotteryissue',$lotteryissue);
      	$this->assign('starttime',$starttime);
      	$this->assign('endtime',$endtime);
      	$this->assign('source',$source);
      	$this->assign('status',$status);
      	$this->assign('play_list',$play_list);
      	$this->assign('play_status',$play_status);
	 	$this->display();
    }
    
    /**
     * 	ORACLE期信息修改
     */
    function edit(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	$conArr = explode('_',$condition);
    	if($conArr[4] == 'b2c'){
    		$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB1'));
    	}else if($conArr[4] == 'b2b'){
    		$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB2'));
    	}
    	$header=array(
    			'transactiontype' => '63000',
    			'messengerid' => time('YmdHis',time()).uniqid(6),
    			'agenterid' => '10000001',
    	);
    	if($act == 'edit'){
    		$header['transactiontype'] = '63001';
    		$ielement=array(
    				'lotteryid' => $lotteryid,
    				'lotteryissue' => $lotteryissue,
    				'starttime'  =>  $starttimestamp,
    				'endtime'  =>  $endtimestamp,
    				'endtime1'  =>  $endtime1,
    				'endtime2'  =>  $endtime2,
    				'endtime3'  =>  $endtime3
    		);
    		$HttpObj->send($header,$ielement,'10000001','','web');
    		if(in_array($lotteryid, array('108','109','110','111'))){
    			$this->edit_issue($ielement);
    		}
    		if($HttpObj->getoelementvalue('errorcode') == '0'){
    			//echo $str='{"statusCode":"200","message":"操作成功！","callbackType":"forward","forwardUrl":"/Home/Issue/?lotteryid='.$conArr[0].'&lotteryissue='.$conArr[1].'&starttime='.$conArr[2].'&endtime='.$conArr[3].'&source='.$conArr[4].'&status='.$conArr[5].'&act=query"}';
    			echo $str = '{"statusCode":"200","message":"操作成功！"}';
    			exit;
    		}else{
    			echo $str = '{"statusCode":"300","message":"'.$HttpObj->getoelementvalue('errorcode').'---'.$HttpObj->getoelementvalue('errormsg').'！"}';
    			exit;
    		}
    	}else{
    		$arr = explode('_',$recid);
    		$lotteryid = $arr[0];
    		$lotteryissue = $arr[1];
    		$ielement=array(
    				'lotteryid' => $lotteryid,
    				'lotteryissue' => $lotteryissue
    		);
    		$HttpObj->send($header,$ielement,'10000001','','web');
    		$olist = $HttpObj->getelements();
    		foreach($olist as $key=>$val){
    			$olist[$key]['starttimestamp'] = date('Y-m-d H:i',strtotime($val['starttime']));
    			$olist[$key]['endtimestamp'] = date('Y-m-d H:i',strtotime($val['endtime']));
    			$olist[$key]['endtime1'] = date('Y-m-d H:i',strtotime($val['endtime1']));
    			$olist[$key]['endtime2'] = date('Y-m-d H:i',strtotime($val['endtime2']));
    			$olist[$key]['endtime3'] = date('Y-m-d H:i',strtotime($val['endtime3']));
    		}
    		$this->assign('issueinfo', $olist['element']);
    		$this->assign('lotteryid', $lotteryid);
    		$this->assign('lotteryissue', $lotteryissue);
    		$this->assign('condition',$condition);
    		$this->display();
    	}
    }
    
     /**
     * 	MYSQL期信息修改
     */
    function edit_issue($element){
    	$model=D('Issue');
    	$lotarr = array('108'=>'324','109'=>'441','110'=>'326','111'=>'325');
    	$map = array('cz_id' => $lotarr[$element['lotteryid']], 'lotteryissue' => $element['lotteryissue']);
    	$savedata = array(
    			'starttimestamp' => $element['starttime'],
    			'endtimestamp' => $element['endtime'],
    			'endtime1' => $element['endtime1'],
    			'endtime2' => $element['endtime2'],
    			'endtime3' => $element['endtime3'],
    	);
    	$model->where($map)->save($savedata);
    }
    
    /**
     *	ORACLE设置期结时间 
     */
    function setendtime(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	$conArr = explode('_',$condition);
    	if($conArr[4] == 'b2c'){
    		$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB1'));
    	}else if($conArr[4] == 'b2b'){
    		$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB2'));
    	}
    	if($act == 'set'){
    		$header=array(
    				'transactiontype' => '60003',
    				'messengerid' => time('YmdHis',time()).uniqid(6),
    				'agenterid' => '10000001',
    		);
    		$ielement=array(
    				'lotteryid' => $lotteryid,
    				'lotteryissue' => $lotteryissue,
    				'endtime1'  =>  $endtime1,
    				'endtime2'  =>  $endtime2,
    				'endtime3'  =>  $endtime3
    		);
    		$HttpObj->send($header,$ielement,'10000001','','web');
    		if($HttpObj->getoelementvalue('errorcode') == '0'){
    			echo $str = '{"statusCode":"200","message":"操作成功！"}';
    			exit;
    		}else{
    			echo $str = '{"statusCode":"300","message":"'.$HttpObj->getoelementvalue('errorcode').'---'.$HttpObj->getoelementvalue('errormsg').'！"}';
    			exit;
    		}
    		
    	}else{
    		$arr = explode('_',$recid);
    		$lotteryid = $arr[0];
    		$lotteryissue = $arr[1];
    		$this->assign('lotteryid', $lotteryid);
    		$this->assign('lotteryissue', $lotteryissue);
    		$this->assign('condition',$condition);
    		$this->display();
    	}
    }
    
    /**
     *	ORACLE期销售状态修改
     */
    function setstatus(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	$arr = explode('_',$recid);
    	$lotid = $arr[0];
    	$lotissue = $arr[1];
    	if($act == 'set'){
    		$conArr=explode('_',$condition);
    		if($conArr[4] == 'b2c'){
    			$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB1'));
    		}else if($conArr[4] == 'b2b'){
    			$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB2'));
    		}
    		$header=array(
    				'transactiontype' => '60001',
    				'messengerid' => time('YmdHis',time()).uniqid(6),
    				'agenterid' => '10000001',
    		);
    		$ielement=array(
    				'lotteryid' => $lotteryid,
    				'lotteryissue' => $lotteryissue,
    				'status'  =>  $issuestatus
    		);
    		$HttpObj->send($header,$ielement,'10000001','','web');
    		if($HttpObj->getoelementvalue('errorcode') == '0'){
    			echo $str = '{"statusCode":"200","message":"操作成功！"}';
    			exit;
    		}else{
    			echo $str = '{"statusCode":"300","message":"操作失败！"}';
    			exit;
    		}
    	}else{
    		$this->assign('lotteryid', $lotid);
    		$this->assign('lotteryissue', $lotissue);
    		$this->assign('condition',$condition);
    		$this->display();
    	}
    }
    
	/**
	 * 	ORACLE玩法期状态管理
	 */
	function setissue(){
		extract($_REQUEST, EXTR_OVERWRITE);
		if($act == "set"){
			$arr = explode('^',$recid);
			$lotteryid = $arr[0];
			$lotteryname = $arr[1];
			$source = $arr[2];
			$this->assign('lotteryid',$lotteryid);
			$this->assign('lotteryname',$lotteryname);
			$this->assign('source',$source);
			$this->assign('act',$act);
			$this->display();
		}else if($act == "dopost"){
			if($source == "b2c"){
				$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB1'));
			}else if($source == "b2b"){
				$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB2'));
			}			
			$header = array(
					'transactiontype' => '60002',
					'messengerid' => time('YmdHis',time()).uniqid(6),
					'agenterid' => '10000001',
			);
			$ielement = array(
					'lotteryid' => $lotteryid,
					'status'  => $playstatus
			);
			$HttpObj->send($header,$ielement,'10000001','','web');
			if($HttpObj->getoelementvalue('errorcode') == '0'){
				echo $str = '{"statusCode":"200","message":"操作成功！"}';
				exit;
			}else{
				echo $str = '{"statusCode":"300","message":"操作失败！"}';
				exit;
			}
		}else{
			$play_list = $this->getSystemPlayWay('b2c');
			$play_list2 = $this->getSystemPlayWay('b2b');
			foreach($play_list as $key=>$val){
				$play_list[$key]['salestatue'] = $this->get_lotterystatus($val['salestatue']);
			}
			foreach($play_list2 as $key=>$val){
				$play_list2[$key]['salestatue'] = $this->get_lotterystatus($val['salestatue']);
			}
			$this->assign('play_list',$play_list);
			$this->assign('play_list2',$play_list2);
			$this->display();
		}
	}
    
	
	/**
	 * MYSQL足彩期查询
	 */
	function zcissue(){
		extract($_REQUEST, EXTR_OVERWRITE);
		global $cz_zucai;
		include APP_PATH.'Home/Conf/caizhong.php';
		$lotteryid = ($lotteryid != '0') ? $lotteryid : '324,441,326,325';
		$lotteryissue = isset($lotteryissue) ? trim($lotteryissue) : '';
		$starttime = isset($starttime) ? trim($starttime) : date('Y-m-d',time()-2*24*3600);
		$endtime = isset($endtime) ? trim($endtime) : date('Y-m-d',time()+2*24*3600);
		if($act == 'query'){
			$model = D('Issue');
			$pageNum = isset($pageNum) ? $pageNum : '1';
			$numPerPage = isset($numPerPage) ? $numPerPage : '20';
			$startnum = ($pageNum - 1) * $numPerPage; 
			$map['cz_id']=array('in', $lotteryid);
			if(!empty($lotteryissue)){
				$map['lotteryissue']=$lotteryissue;
			}else{
				$s = $starttime.' 00:00:00';
				$e = $endtime.' 23:59:59';
				$map['endtimestamp']=array('between',"$s,$e");
			}
			$olist = $model->where($map)->order('lotteryissue desc')->limit($startnum,$numPerPage)->select();
			$count = $model->where($map)->count();
			$yunum = $count%$numPerPage;
			if($yunum == '0'){
				$pageTotal = floor($count/$numPerPage);
			}else{
				$pageTotal = floor($count/$numPerPage)+1;
			}
			$issuelist = array();
			foreach($olist as $key=>$val){
				$issuelist[$key]['cz_id'] = $val['cz_id'];
				$issuelist[$key]['lotteryname'] = $this->getlotteryname($val['cz_id'], $cz_zucai);
				$issuelist[$key]['lotteryissue'] = $val['lotteryissue'];
				$issuelist[$key]['starttimestamp'] = date('Y-m-d H:i',strtotime($val['starttimestamp']));
				$issuelist[$key]['endtimestamp'] = date('Y-m-d H:i',strtotime($val['endtimestamp']));
				$issuelist[$key]['endtime1'] = date('Y-m-d H:i',strtotime($val['endtime1']));
				$issuelist[$key]['endtime2'] = date('Y-m-d H:i',strtotime($val['endtime2']));
				$issuelist[$key]['endtime3'] = date('Y-m-d H:i',strtotime($val['endtime3']));
				if($val['send_flag'] == '0'){
					$issuelist[$key]['b2cstatus']='未审核';
					$issuelist[$key]['b2bstatus']='未审核';
					$issuelist[$key]['b2coptype']='push';
					$issuelist[$key]['b2boptype']='push';
					$issuelist[$key]['b2ctarget'] = 'ajaxTodo';
					$issuelist[$key]['b2btarget'] = 'ajaxTodo';
					$issuelist[$key]['b2cisend'] = '0';
					$issuelist[$key]['b2bisend'] = '0';
					$issuelist[$key]['b2cstyle'] = 'push_ts';
					$issuelist[$key]['b2bstyle'] = 'push_ts';
				}else{
					$first = substr($val['send_flag'],0,1);
					$second = substr($val['send_flag'],1,1);
					$firstarr = $this->get_zcstatus($first);
					$issuelist[$key]['b2cstatus'] = $firstarr['status'];
					$issuelist[$key]['b2coptype'] = $firstarr['optype'];
					$issuelist[$key]['b2ctarget'] = $firstarr['target'];
					$issuelist[$key]['b2cisend'] = $firstarr['isend'];
					$issuelist[$key]['b2cstyle'] = $firstarr['style'];
					$secondarr = $this->get_zcstatus($second);
					$issuelist[$key]['b2bstatus'] = $secondarr['status'];
					$issuelist[$key]['b2boptype'] = $secondarr['optype'];
					$issuelist[$key]['b2btarget'] = $secondarr['target'];
					$issuelist[$key]['b2bisend'] = $secondarr['isend'];
					$issuelist[$key]['b2bstyle'] = $secondarr['style'];
				}
			}
			$this->assign('issuelist',$issuelist);
			$this->assign('pageTotal',$pageTotal);
			$this->assign('totalCount',$count);
			$this->assign('pageNumShown','5');
			$this->assign('numPerPage',$numPerPage);
			$this->assign('currentPage',$pageNum);
		}
		$this->assign('lotteryid',$_REQUEST['lotteryid']);
		$this->assign('lotteryissue',$lotteryissue);
		$this->assign('starttime',$starttime);
		$this->assign('endtime',$endtime);
		$this->assign('cz_zucai',$cz_zucai);
		$this->display();
	}
	/**
	 * MYSQL足彩期添加
	 */
	function add(){
		extract($_REQUEST, EXTR_OVERWRITE);
		global $cz_zucai;
		include APP_PATH.'Home/Conf/caizhong.php';
		if($_REQUEST['act']=='add'){
			$model = D('Issue');
			$map = array('cz_id' => $lotteryid, 'lotteryissue' => $lotteryissue);
			$data = array(
				'cz_id' => $lotteryid,
				'lotteryissue' => $lotteryissue,
				'starttimestamp' => $starttimestamp,
				'endtimestamp' => $endtimestamp,
				'endtime1' => $endtime1,
				'endtime2' => $endtime2,
				'endtime3' => $endtime3
			);
			$count = $model->where($map)->count();
			if($count == 0){
				$result = $model->add($data);
				if($result){
					echo $str = '{"statusCode":"200","message":"操作成功！"}';
					exit;
				}else{
					echo $str = '{"statusCode":"300","message":"操作失败！"}';
					exit;
				}
			}else{
				echo $str = '{"statusCode":"300","message":"'.$lotteryissue.'期已存在，请重新录入!"}';
				exit;
			}
		}
		$this->assign('cz_zucai',$cz_zucai);
		$this->display();
	}
	/**
	 * MYSQL足彩期编辑
	 */
	function edit_zucai(){
		extract($_REQUEST, EXTR_OVERWRITE);
		global $cz_zucai;
		include APP_PATH.'Home/Conf/caizhong.php';
		$model=D('Issue');
		if($act == 'edit'){
			$map = array('cz_id' => $lotteryid, 'lotteryissue'=>$lotteryissue);
			$list = $model->where($map)->select();
			if($list[0]['send_flag'] == '0'){
				$savedata = array(
					'starttimestamp' => $starttimestamp,
					'endtimestamp' => $endtimestamp,
					'endtime1' => $endtime1,
					'endtime2' => $endtime2,
					'endtime3' => $endtime3,
					);
				$result = $model->where($map)->save($savedata);
				if($result){
					echo $str='{"statusCode":"200","message":"操作成功！"}';
					exit;
				}else{
					echo $str='{"statusCode":"300","message":"修改失败或者没有更新任何内容！"}';
					exit;
				}
			}else{
				echo $str = '{"statusCode":"300","message":"无法进行修改操作!"}';
				exit;
			}
		}else{
			$arr = explode('_',$recid);
			$lotteryid = $arr[0];
			$lotteryissue = $arr[1];
			$map = array('cz_id' => $lotteryid, 'lotteryissue'=>$lotteryissue);
			$list = $model->where($map)->select();
			foreach($list as $key=>$val){
				$list[$key]['lotteryname']=$this->getlotteryname($val['cz_id'], $cz_zucai);
				$list[$key]['starttimestamp'] = date('Y-m-d H:i',strtotime($val['starttimestamp']));
				$list[$key]['endtimestamp'] = date('Y-m-d H:i',strtotime($val['endtimestamp']));
				$list[$key]['endtime1'] = date('Y-m-d H:i',strtotime($val['endtime1']));
				$list[$key]['endtime2'] = date('Y-m-d H:i',strtotime($val['endtime2']));
				$list[$key]['endtime3'] = date('Y-m-d H:i',strtotime($val['endtime3']));
			}
			$this->assign('issueinfo', $list[0]);
			$this->display();
		}
	}
	/**
	 * MYSQL足彩期删除
	 */
	function delete(){
		extract($_REQUEST, EXTR_OVERWRITE);
		$model = D('Issue');
		$arr = explode('_',$recid);
		$lotteryid = $arr[0];
		$issue = $arr[1];
		$map = array('cz_id' => $lotteryid, 'lotteryissue' => $issue);
		$list = $model->where($map)->select();
		if($list[0]['send_flag'] == '0'){
			$result = $model->where($map)->delete();
			if($result){
				echo $str = '{"statusCode":"200","message":"操作成功！"}';
				exit;
			}else{
				echo $str = '{"statusCode":"300","message":"操作异常！"}';
				exit;
			}
		}else{
			echo $str = '{"statusCode":"300","message":"无法进行删除操作！"}';
			exit;
		}
		
	}

	/**
	 * MSYQL足彩期信息推送
	 */
	function push(){
		extract($_REQUEST, EXTR_OVERWRITE);
		global $cz_grade_type;
		include APP_PATH.'Home/Conf/caizhong.php';
		$model = D('Issue');
		$arr = explode('_',$recid);
		$lotteryid = $arr[0];
		$issue = $arr[1];
		$map = array('cz_id' => $lotteryid, 'lotteryissue' => $issue);
		$issueinfo = $model->where($map)->select();
		if($issueinfo){
			if($token == 'tob2c'){
				$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB1'));
				if($issueinfo[0]['send_flag'] == '0'){
					$savedata['send_flag'] = '10';
				}else{
					$flag = str_split($issueinfo[0]['send_flag']);
					$savedata['send_flag'] = '1'.$flag[1];
				}
			}else if($token == 'tob2b'){
				$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB2'));
				if($issueinfo[0]['send_flag'] == '0'){
					$savedata['send_flag'] = '01';
				}else{
					$flag = str_split($issueinfo[0]['send_flag']);
					$savedata['send_flag'] = $flag[0].'1';
				}
			}
			$header = array(
					'transactiontype' => '60011',
					'messengerid' => time('YmdHis',time()).uniqid(6),
					'agenterid' => '10000001',
			);
			$ielement = array(
					'lotteryid' => $cz_grade_type[$lotteryid]['playid'],
					'lotteryissue' => $issue,
					'starttimestamp' => date('Y-m-d H:i',strtotime($issueinfo[0]['starttimestamp'])),
					'endtimestamp' => date('Y-m-d H:i',strtotime($issueinfo[0]['endtimestamp'])),
					'endtime1' => date('Y-m-d H:i',strtotime($issueinfo[0]['endtime1'])),
					'endtime2' => date('Y-m-d H:i',strtotime($issueinfo[0]['endtime2'])),
					'endtime3' => date('Y-m-d H:i',strtotime($issueinfo[0]['endtime3']))
			);
			$HttpObj->send($header,$ielement,'10000001','','web');
			if($HttpObj->getoelementvalue('errorcode') == '0'){
				$savedata['sent_time'] = date('Y-m-d H:i:s');
				$result = $model->where($map)->save($savedata);
				if($result){
					echo $str = '{"statusCode":"200","message":"操作成功！"}';
					exit;
				}else{
					echo $str = '{"statusCode":"300","message":"操作异常！"}';
					exit;
				}
			}else{
				echo $str = '{"statusCode":"300","message":"'.$HttpObj->getoelementvalue('errorcode').'---'.$HttpObj->getoelementvalue('errormsg').'！"}';
				exit;
			}
		}else{
			echo $str = '{"statusCode":"300","message":"操作异常！"}';
			exit;
		}
		
	}
	
	/**
	 * 获取系统支持的玩法列表
	 */
	function getSystemPlayWay($source){
		if($source == "b2c"){
			$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB1'));
		}else if($source == "b2b"){
			$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB2'));
		}
		$header = array(
				'transactiontype' => '60007',
				'messengerid' => time('YmdHis',time()).uniqid(6),
				'agenterid' => '10000001',
		);
		$ielement = array(
				'lotteryid' => ''
		);
		$HttpObj->send($header,$ielement,'88888888','','web');
		$olist = $HttpObj->getelements();
		return $olist;
	}
    
	/**
	 * 获取足彩期推送状态
	 */
	function get_zcstatus($status){
		$result = array();
		if($status == '1'){
			$result['status'] = '已导入';
			$result['optype'] = 'index';
			$result['target'] = '';
			$result['isend'] = '1';
			$result['style'] = 'succeed_wc';
		}else{
			$result['status'] = '未审核';
			$result['optype'] = 'push';
			$result['target'] = 'ajaxTodo';
			$result['isend'] = '0';
			$result['style'] = 'push_ts';
		}
		return $result;
	}
   	
	function get_lotterystatus($param){
		$lottery_status = '未知';
    	switch ($param) {
	        case 0:
	           $lottery_status = '正常';
	           break;
	        case 2:
	           $lottery_status = '进入封期';
	           break;
	       	case 3:
	           $lottery_status = '开奖公告录入完成';
	           break;
	       	case 4:
	           $lottery_status = '开奖算奖';
	           break;
	       	case 5:
	           $lottery_status = '算奖完成';
	           break;
	       	case 6:
	           $lottery_status = '进行派奖';
	           break;
	       	case 7:
	           $lottery_status = '派奖完成';
	           break;
	       	case 1:
	           $lottery_status = '临时关闭';
	           break;
	       	case -1:
	           $lottery_status = '预售';
	           break;
	       	default:
	            break;
		}
		return $lottery_status;
	}
	
	function getlotteryname($id, $array){
		$name = '';
		foreach($array as $k=>$v){
			if($id == $v['value']){
				$name = $v['name'];
			}
		}
		return $name;
	}
	
	
	
	
}