<?php
namespace Home\Controller;
use Home\Controller\BaseController;
class KaijiangController extends BaseController {
	
	function index(){
		extract($_REQUEST, EXTR_OVERWRITE);
		global $caizhong;
		include APP_PATH.'Home/Conf/caizhong.php';
		$model=D('Kaijiang');
		$kjtime=isset($kjtime) ? $kjtime : 'today';
		$lotteryid=($lotteryid != '0') ? $lotteryid : '1,2,3,281,282,283,284,324,441,326,325';
	  	if($act == 'query'){
	  		$pageNum = isset($pageNum) ? $pageNum : '1';
	  		$numPerPage = isset($numPerPage) ? $numPerPage : '20';
	  		$startnum = ($pageNum - 1) * $numPerPage;
	  		if(!empty($issue)){
	  			$map['kj_issue'] = $issue;
	  		}else{
	  			if($kjtime == 'today'){
	  				$map['kj_date'] = array('like',date('Y-m-d',time()).'%');
	  			}else if($kjtime == 'yesterday'){
	  				$map['kj_date'] = array('like',date('Y-m-d',time()-1*24*3600).'%');
	  			}
	  		}
	  		$map['cz_id']=array('in', $lotteryid);
	  		$arr=$model->where($map)->order('kj_issue desc')->limit($startnum,$numPerPage)->select();
	  		$count = $model->where($map)->count();
	  		$yunum = $count%$numPerPage;
	  		if($yunum == '0'){
	  			$pageTotal = floor($count/$numPerPage);
	  		}else{
	  			$pageTotal = floor($count/$numPerPage)+1;
	  		}
	  		$kjlist=array();
	  		foreach($arr as $key=>$val){
	  			$kjlist[$key]['lotteryid']=$val['cz_id'];
	  			$kjlist[$key]['lotteryname']=$val['cz_name'];
	  			$kjlist[$key]['issue']=$val['kj_issue'];
	  			$kjlist[$key]['opentime']=$val['kj_date'];
	  			$kjlist[$key]['is_current_issue']=$val['is_current_issue'];
	  			if($val['kj_z_num'] == '-1') $val['kj_z_num'] = '';
	  			if($val['kj_t_num'] == '-1') $val['kj_t_num'] = '';
	  			if(in_array($val['cz_id'],array('1','3','281'))){
	  				$kjlist[$key]['lotterycode']='<em style="color:red;font-style:normal;">'.$val['kj_z_num'].'</em> <em style="color:blue;font-style:normal;">'.$val['kj_t_num'].'</em>';
	  			}else{
	  				$kjlist[$key]['lotterycode']=$val['kj_z_num'];
	  			}
	  			if($val['send_flag'] == '0'){
	  				$kjlist[$key]['b2cstatus']='未审核';
	  				$kjlist[$key]['b2bstatus']='未审核';
	  				$kjlist[$key]['b2coptype']='push';
	  				$kjlist[$key]['b2boptype']='push';
	  				$kjlist[$key]['b2ctarget'] = 'ajaxTodo';
	  				$kjlist[$key]['b2btarget'] = 'ajaxTodo';
	  				$kjlist[$key]['b2cisend'] = '0';
	  				$kjlist[$key]['b2bisend'] = '0';
	  				$kjlist[$key]['b2cstyle'] = 'push_ts';
	  				$kjlist[$key]['b2bstyle'] = 'push_ts';
	  			}else{
	  				$first = substr($val['send_flag'],0,1);
	  				$second = substr($val['send_flag'],1,1);
	  				$firstarr = $this->getstatus($first);
	  				$kjlist[$key]['b2cstatus'] = $firstarr['status'];
	  				$kjlist[$key]['b2coptype'] = $firstarr['optype'];
	  				$kjlist[$key]['b2ctarget'] = $firstarr['target'];
	  				$kjlist[$key]['b2cisend'] = $firstarr['isend'];
	  				$kjlist[$key]['b2cstyle'] = $firstarr['style'];
	  				$secondarr = $this->getstatus($second);
	  				$kjlist[$key]['b2bstatus'] = $secondarr['status'];
	  				$kjlist[$key]['b2boptype'] = $secondarr['optype'];
	  				$kjlist[$key]['b2btarget'] = $secondarr['target'];
	  				$kjlist[$key]['b2bisend'] = $secondarr['isend'];
	  				$kjlist[$key]['b2bstyle'] = $secondarr['style'];
	  			}
	  		}
	  		$this->assign('pageTotal',$pageTotal);
	  		$this->assign('totalCount',$count);
	  		$this->assign('pageNumShown','5');
	  		$this->assign('numPerPage',$numPerPage);
	  		$this->assign('currentPage',$pageNum);
	  		$this->assign('kjlist',$kjlist);
	  	} 	    
		$this->assign('issue',$issue);
		$this->assign('lotteryid',$_REQUEST['lotteryid']);
	  	$this->assign('kjtime',$kjtime);
	  	$this->assign('caizhong',$caizhong);
	  	$this->display();
	}
	
	
    //修改开奖数据
    function edit(){
    	global $cz_grade, $cz_grade_type;
    	include APP_PATH.'Home/Conf/caizhong.php';
    	$model=D('Kaijiang');//开奖信息表
    	$zjmodel=D('Zjinfo');//中奖详情表
    	$recid=$_REQUEST['recid'];
    	$arr=explode('_',$recid);
    	$lotteryid=$arr[0];
    	$issue=$arr[1];
    	$map = array(
    		'cz_id' => $lotteryid,
    		'kj_issue' => $issue	
    		);
    	//开奖信息查询
    	$kjinfo = $model->where($map)->select();
    	//中奖详情查询
    	$zjinfo = $zjmodel->where($map)->select();
    	$zjinfo[0]['jc_money'] = $this->convert_unit($zjinfo[0]['jc_money']);
    	$zjinfo[0]['tz_money'] = $this->convert_unit($zjinfo[0]['tz_money']);
    	$grade = $cz_grade_type[$lotteryid]['num'];
    	$levelarray = $cz_grade_type[$lotteryid]['levelvalue'];
    	$appendarray = $cz_grade_type[$lotteryid]['appendvalue'];
    	for($i=0; $i<$grade; $i++){
    		$grade_data[$i] = $cz_grade[$i];
    		if($zjinfo[0]){
    			$grade_data[$i][$cz_grade[$i]['basic_z']] = $zjinfo[0][$cz_grade[$i]['basic_z']];
    			if(empty($zjinfo[0][$cz_grade[$i]['basic_j']])){
					if(empty($levelarray[$cz_grade[$i]['levelid']])){
    					$grade_data[$i][$cz_grade[$i]['basic_j']] = '';
    				}else{
    					$grade_data[$i][$cz_grade[$i]['basic_j']] = $this->convert_unit($levelarray[$cz_grade[$i]['levelid']]);
    				}
    			}else{
    				$grade_data[$i][$cz_grade[$i]['basic_j']] = $this->convert_unit($zjinfo[0][$cz_grade[$i]['basic_j']]);
    			}
    			$grade_data[$i][$cz_grade[$i]['append_z']] = $zjinfo[0][$cz_grade[$i]['append_z']];
    			if(empty($zjinfo[0][$cz_grade[$i]['append_j']])){
					if(empty($appendarray[$cz_grade[$i]['appendid']])){
    					$grade_data[$i][$cz_grade[$i]['append_j']] = '';
    				}else{
    					$grade_data[$i][$cz_grade[$i]['append_j']] = $this->convert_unit($appendarray[$cz_grade[$i]['appendid']]);
    				}
    			}else{
					$grade_data[$i][$cz_grade[$i]['append_j']] = $this->convert_unit($zjinfo[0][$cz_grade[$i]['append_j']]);
    			}
    		}
    	}
    	$grade_append = $cz_grade_type[$lotteryid]['appendnum'];
    	$append_data = explode(',',$grade_append);
    	$this->assign('lotteryid',$lotteryid);
    	$this->assign('issue',$issue);
    	$this->assign('kjinfo',$kjinfo[0]);
    	$this->assign('grade_data',$grade_data);
    	$this->assign('append_data',$append_data);
    	$this->assign('zjinfo',$zjinfo[0]);
    	$this->display('edit');
    }
    
    //保存开奖号码
    function save_code(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	global $cz_grade, $cz_grade_type;
    	include APP_PATH.'Home/Conf/caizhong.php';
    	$model=D('Kaijiang');
    	$zjmodel=D('Zjinfo');//中奖详情表
    	$kjdata=array(
    			'kj_issue'=>$kj_issue,
    			'kj_date'=>$kj_date,
    			'kj_z_num'=>$kj_z_num,
    			'kj_t_num'=>empty($kj_t_num)?'-1':$kj_t_num
    	);
    	$map = array(
    			'cz_id' => $lotteryid,
    			'kj_issue' => $old_issue
    	);
    	//开奖信息更新
    	$result = $model->where($map)->save($kjdata);
    	if($result){
    		echo $str = '{"statusCode":"200","message":"修改开奖信息成功！"}';
    		exit;
    	}else{
    		echo $str='{"statusCode":"300","message":"修改失败或者没有更新任何内容！"}';
    		exit;
    	}
    }
    
    function edit_act(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	global $cz_grade, $cz_grade_type;
    	include APP_PATH.'Home/Conf/caizhong.php';
    	$model=D('Kaijiang');
    	$zjmodel=D('Zjinfo');//中奖详情表
		//传统足彩处理
    	/*if(in_array($lotteryid, array('324','441','325','326'))){
    		$kj_z_num = $this->handle_lottery_zc($lottery_zc_num);
    		$kj_t_num = '-1';
    	}*/
    	$kjdata=array(
    		'kj_issue'=>$kj_issue,
    		'kj_date'=>$kj_date,
    		'kj_z_num'=>$kj_z_num,
    		'kj_t_num'=>empty($kj_t_num)?'-1':$kj_t_num
    	);
    	$zjdata = array(
    		'kj_issue' => $kj_issue,
    		'kj_date' => $zj_kj_date,
    		'jc_money' => $jc_money,
    		'tz_money' => $tz_money,
    		'p_name' => 'system',
    		'p_time' => date('Y-m-d H:i:s')
    	);
    	if($grade) $zjdata = array_merge($grade, $zjdata);
    	if($grade_money) $zjdata = array_merge($grade_money, $zjdata);
    	if($append_grade) $zjdata = array_merge($append_grade, $zjdata);
    	if($append_grade_money) $zjdata = array_merge($append_grade_money, $zjdata);
    	$map = array(
    		'cz_id' => $lotteryid,
    		'kj_issue' => $old_issue
    		);
    	//开奖信息更新
    	$result = $model->where($map)->save($kjdata);
    	//中奖详情更新
    	$count=$zjmodel->where($map)->count();
    	if($count == 0){
    		$zjdata['cz_id'] = $lotteryid;
    		$result_zj = $zjmodel->add($zjdata);
    	}else{
    		$result_zj = $zjmodel->where($map)->save($zjdata);
    	}
    	if($result || $result_zj){
    		$element = array();
    		$element['lotteryid'] = $cz_grade_type[$lotteryid]['playid'];
    		$element['lotteryissue'] = $kj_issue;
    		if(in_array($lotteryid,array('1','3','281'))){
    			$bonuscode=$kjdata['kj_z_num'].' '.$kjdata['kj_t_num'];
    		}else{
    			$bonuscode=$kjdata['kj_z_num'];
    		}
    		$element['bonuscode'] = str_replace(' ', ',', $bonuscode);
    		$element['bonustime'] = $kj_date.' 00:00';
    		$element['salevalue'] = number_format($zjdata['tz_money'] * 100,0,'.','');
    		$element['nextbonusvalue'] = $zjdata['jc_money']*100;
    		$bonusinfo = $this->convert_array($lotteryid, $zjdata);
    		$element['bonusinfo'] = json_encode($bonusinfo);
    		$flag = str_split($send_flag);
    		if(in_array($flag[0],array('1','2'))){//B2C-已导入，已确认的状态可以修改开奖信息
    			$resultb2c = $this->edit_interface('tob2c',$element);
    		}
    		if(in_array($flag[1],array('1','2'))){//B2B-已导入，已确认的状态可以修改开奖信息
    			$resultb2b = $this->edit_interface('tob2b',$element);
    		}
    		if($resultb2c && $resultb2c['errorcode'] != '0'){
    			echo $str = '{"statusCode":"300","message":"B2C操作异常：'.$resultb2c['errorcode'].'---'.$resultb2c['errormsg'].'！"}';
    			exit;
    		}else if($resultb2b && $resultb2b['errorcode'] != '0'){
    			echo $str = '{"statusCode":"300","message":"B2B操作异常：'.$resultb2b['errorcode'].'---'.$resultb2b['errormsg'].'！"}';
    			exit;
    		}else{
    			echo $str = '{"statusCode":"200","message":"修改开奖信息成功！"}';
    			exit;
    		}
    	}else{
    		echo $str='{"statusCode":"300","message":"修改失败或者没有更新任何内容！"}';
    		exit;
    	}
    }
    //修改B2C和B2B开奖数据
    function edit_interface($token, $element){
    	if($token == 'tob2c'){
    		$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB1'));
    	}else if($token == 'tob2b'){
    		$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB2'));
    	}
    	$header=array(
    			'transactiontype'=>'60082',
    			'messengerid'=>time('YmdHis',time()).uniqid(6),
    			'agenterid'=>'10000001',
    	);
    	$ielement=array(
    			'lotteryid'=>$element['lotteryid'],
    			'lotteryissue' => $element['lotteryissue'],
    			'bonuscode' => $element['bonuscode'],
    			'bonustime' => $element['bonustime'],
    			'salevalue' => $element['salevalue'],
    			'nextbonusvalue' => $element['nextbonusvalue'],
    			'bonusinfo' => $element['bonusinfo']
    	);
    	$HttpObj->send($header,$ielement,'10000001','','web');
    	$result = array(
    			'errorcode' => $HttpObj->getoelementvalue('errorcode'),
    			'errormsg' => $HttpObj->getoelementvalue('errormsg')
    			);
    	return $result;
    }
    
    //添加开奖信息
	function add(){
		extract($_REQUEST, EXTR_OVERWRITE);
		global $caizhong, $cz_grade, $cz_grade_type;
		include APP_PATH.'Home/Conf/caizhong.php';
		$kjtime=isset($kjtime) ? $kjtime : date('Y-m-d',time());
		$lotteryid = isset($lotteryid) ? $lotteryid : '1';
		$grade = $cz_grade_type[$lotteryid]['num'];
		$levelarray = $cz_grade_type[$lotteryid]['levelvalue'];
		$appendarray = $cz_grade_type[$lotteryid]['appendvalue'];
		for($i=0; $i<$grade; $i++){
			$grade_data[$i] = $cz_grade[$i];
			if(empty($levelarray[$cz_grade[$i]['levelid']])){
				$grade_data[$i]['basic_v'] = '';
			}else{
				$grade_data[$i]['basic_v'] = $this->convert_unit($levelarray[$cz_grade[$i]['levelid']]);
			}
			if(empty($appendarray[$cz_grade[$i]['appendid']])){
				$grade_data[$i]['append_v'] = '';
			}else{
				$grade_data[$i]['append_v'] = $this->convert_unit($appendarray[$cz_grade[$i]['appendid']]);
			}
		}
		$grade_append = $cz_grade_type[$lotteryid]['appendnum'];
		$append_data = explode(',',$grade_append);
		if($act == "dopost"){
			$ajaxjson = array();
			$ajaxjson['code'] = '0';
			//$ajaxjson['lottery'] = $lotteryid;
			$ajaxjson['grade_data'] = $grade_data;
			$ajaxjson['append_data'] = $append_data;
			echo json_encode($ajaxjson);
		}else{
			$this->assign('kjtime',$kjtime);
			$this->assign('caizhong',$caizhong);
			$this->assign('grade_data',$grade_data);
	    	$this->assign('append_data',$append_data);
			$this->display('add');
		}
	}
    
	//添加开奖号码
	function add_code(){
		extract($_REQUEST, EXTR_OVERWRITE);
		global $caizhong;
		include APP_PATH.'Home/Conf/caizhong.php';
		$model=D('Kaijiang');
		$map = array(
			'cz_id' => $lotteryid,
			'kj_issue' => trim($kj_issue)
		);
		$count = $model->where($map)->count();
		if($count == 0){
			foreach($caizhong as $k=>$v){
				if($lotteryid == $v['value']){
					$cz_name = $v['name'];
				}
			}
			$kjdata = array(
					'cz_id' => $lotteryid,
					'cz_name' => $cz_name,
					'kj_issue' => trim($kj_issue),
					'kj_z_num' => trim($kj_z_num),
					'kj_t_num' => trim($kj_t_num),
					'p_name' => 'system',
					'p_time' => date('Y-m-d H:i:s'),
					't_one' => '-1',
					't_two' => '-1',
					't_three' => '-1',
					't_four' => '-1',
					't_five' => '-1',
					't_flag' => '0',
					'is_current_issue' => '0',
					'kj_date' => $kj_date
			);
			$result = $model->add($kjdata);
			if($result){
				if(!in_array($lotteryid, array('324','441','325','326'))){
					$this->handlecurIssue($lotteryid, trim($kj_issue));
				}
				echo $str='{"statusCode":"200","message":"录入开奖信息成功！"}';
				exit;
			}else{
				echo $str='{"statusCode":"300","message":"修改失败或者没有更新任何内容！"}';
				exit;
			}
		}else{
			echo $str='{"statusCode":"300","message":"'.$kj_issue.'期数据已经存在！"}';
			exit;
		}
		
	}
	
	function add_act(){
		extract($_REQUEST, EXTR_OVERWRITE);
		global $caizhong;
		include APP_PATH.'Home/Conf/caizhong.php';
		$model=D('Kaijiang');	
		$zjmodel=D('Zjinfo');//中奖详情表
		$map = array(
			'cz_id' => $lotteryid,
			'kj_issue' => trim($kj_issue)
		);
		$count = $model->where($map)->count();
		$count_zj = $zjmodel->where($map)->count();
		if($count == 0){
			foreach($caizhong as $k=>$v){
				if($lotteryid == $v['value']){
					$cz_name = $v['name'];
				}
			}
			//传统足彩处理
			/*if(in_array($lotteryid, array('324','441','325','326'))){
				$kj_z_num = $this->handle_lottery_zc($lottery_zc_num);
				$kj_t_num = '-1';
			}*/
			$kjdata = array(
				'cz_id' => $lotteryid,
				'cz_name' => $cz_name,
				'kj_issue' => trim($kj_issue),
				'kj_z_num' => trim($kj_z_num),
				'kj_t_num' => trim($kj_t_num),
				'p_name' => 'system',
				'p_time' => date('Y-m-d H:i:s'),
				't_one' => '-1',
				't_two' => '-1',
				't_three' => '-1',
				't_four' => '-1',
				't_five' => '-1',
				't_flag' => '0',
				'is_current_issue' => '0',
				'kj_date' => $kj_date
			);
			$result = $model->add($kjdata);
			$zjdata = array(
					'cz_id' => $lotteryid,
					'kj_issue' => trim($kj_issue),
					'kj_date' => $zj_kj_date,
					'jc_money' => $jc_money,
					'tz_money' => $tz_money,
					'p_name' => 'system',
					'p_time' => date('Y-m-d H:i:s')
			);
			if($grade) $zjdata = array_merge($grade, $zjdata);
			if($grade_money) $zjdata = array_merge($grade_money, $zjdata);
			if($append_grade) $zjdata = array_merge($append_grade, $zjdata);
			if($append_grade_money) $zjdata = array_merge($append_grade_money, $zjdata);
			if($count_zj == 0){
				$result_zj = $zjmodel->add($zjdata);
			}else{
				$result_zj = $zjmodel->where($map)->save($zjdata);
			}
			if($result || $result_zj){
				//callbackType:closeCurrent
				//$str='{"statusCode":"200","message":"录入开奖信息成功!","navTabId":"","rel":"add","callbackType":"","forwardUrl":"","confirmMsg":""}';
				if(!in_array($lotteryid, array('324','441','325','326'))){
					$this->handlecurIssue($lotteryid, trim($kj_issue));
				}
				echo $str='{"statusCode":"200","message":"录入开奖信息成功！"}';
				exit;
			}else{
				echo $str='{"statusCode":"300","message":"修改失败或者没有更新任何内容！"}';
				exit;
			}
		}else{
			echo $str='{"statusCode":"300","message":"'.$kj_issue.'期数据已经存在！"}';
			exit;
		}
	}
	
	//删除
	function delete(){
		extract($_REQUEST, EXTR_OVERWRITE);
		$model=D('Kaijiang');//开奖信息表
		$zjmodel=D('Zjinfo');//中奖详情表
		$arr = explode('_',$recid);
		$lotteryid=$arr[0];
		$issue=$arr[1];
		$map = array(
			'cz_id' => $lotteryid,
			'kj_issue' => $issue
			);
		$list = $model->where($map)->select();
		$list_zj = $zjmodel->where($map)->select();
		if($list[0]['send_flag'] == '0' || ($list[0]['send_flag'] == '0' && $list_zj[0]['send_flag'] == '0')){
			$result = $model->where($map)->delete();
			$result_zj = $zjmodel->where($map)->delete();
			if($result || $result_zj){
				echo $str = '{"statusCode":"200","message":"操作成功!"}';
				exit;
			}else{
				echo $str = '{"statusCode":"300","message":"操作异常!"}';
				exit;
			}
		}else{
			echo $str = '{"statusCode":"300","message":"无法进行删除操作！"}';
			exit;
		}
	}
	
	//设为当前期
	function setcurrentissue(){
		extract($_REQUEST, EXTR_OVERWRITE);
		$model=D('Kaijiang');//开奖信息表
		$arr = explode('_',$recid);
		$lotteryid=$arr[0];
		$issue=$arr[1];
		$map = array('cz_id' => $lotteryid,'kj_issue' => $issue);
		$map2 = array('cz_id' => $lotteryid,'kj_issue' => array('neq',$issue));
		$current = array('is_current_issue' => '1'	);
		$nocurrent = array('is_current_issue' => '0');
		$result = $model->where($map)->save($current);
		$result2 = $model->where($map2)->save($nocurrent);
		if($result || $result2){
			echo $str='{"statusCode":"200","message":"操作成功！"}';
			exit;
		}else{
			echo $str = '{"statusCode":"300","message":"操作异常！"}';
			exit;
		}
	}
	
	function handlecurIssue($lotteryid,$issue){
		$model=D('Kaijiang');//开奖信息表
		$map = array('cz_id' => $lotteryid,'kj_issue' => $issue);
		$map2 = array('cz_id' => $lotteryid,'kj_issue' => array('neq',$issue));
		$current = array('is_current_issue' => '1'	);
		$nocurrent = array('is_current_issue' => '0');
		$model->where($map)->save($current);
		$model->where($map2)->save($nocurrent);
	}
	
	//推送数据到B2B
	function push(){
		extract($_REQUEST, EXTR_OVERWRITE);
		global $cz_grade, $cz_grade_type;
		include APP_PATH.'Home/Conf/caizhong.php';
		$model=D('Kaijiang');//开奖信息表
		$zjmodel=D('Zjinfo');//中奖详情表
		$arr=explode('_',$recid);
		$lotteryid=$arr[0];
		$issue=$arr[1];
    	$map = array(
    		'cz_id' => $lotteryid,
    		'kj_issue' => $issue	
    		);
    	//开奖信息查询
    	$kjinfo = $model->where($map)->select();
    	if($kjinfo){
    		if(in_array($lotteryid,array('1','3','281'))){
    			$bonuscode=$kjinfo[0]['kj_z_num'].' '.$kjinfo[0]['kj_t_num'];
    		}else{
    			$bonuscode=$kjinfo[0]['kj_z_num'];
    		}
    		$bonuscode = str_replace(' ', ',', $bonuscode);
    		$bonustime = $kjinfo[0]['kj_date'].' 00:00';
    	}
    	$zjinfo = $zjmodel->where($map)->select();
    	if($zjinfo[0]){
    		$nextbonusvalue = $this->convert_unit($zjinfo[0]['jc_money']);
    		$salevalue = $this->convert_unit($zjinfo[0]['tz_money']);
    		$bonusinfo = $this->convert_array($lotteryid, $zjinfo[0]);
    		if(count($bonusinfo) <= 0){
    			echo $str='{"statusCode":"300","message":"中奖详情不完整！"}';
	    		exit;
    		}
    		if($token == 'tob2c'){
    			$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB1'));
    			if($kjinfo[0]['send_flag'] == '0'){
    				$savedata['send_flag'] = '10';
    			}else{
    				$flag = str_split($kjinfo[0]['send_flag']);
    				$savedata['send_flag'] = '1'.$flag[1];
    			}
    		}else if($token == 'tob2b'){
    			$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB2'));
    			if($kjinfo[0]['send_flag'] == '0'){
    				$savedata['send_flag'] = '01';
    			}else{
    				$flag = str_split($kjinfo[0]['send_flag']);
    				$savedata['send_flag'] = $flag[0].'1';
    			}
    		}
    		$header=array(
    				'transactiontype'=>'60081',
    				'messengerid'=>time('YmdHis',time()).uniqid(6),
    				'agenterid'=>'10000001',
    		);
    		$ielement=array(
    				'lotteryid'=>$cz_grade_type[$lotteryid]['playid'],
    				'lotteryissue' => $issue,
    				'bonuscode' => $bonuscode,
    				'bonustime' => $bonustime,
    				'salevalue' => number_format($salevalue * 100,0,'',''),
    				'nextbonusvalue' => $nextbonusvalue * 100,
    				'bonusinfo' => json_encode($bonusinfo)
    		);
    		$HttpObj->send($header,$ielement,'10000001','','web');
    		if($HttpObj->getoelementvalue('errorcode') == 0){
    			$savedata['sent_time'] = date('Y-m-d H:i:s');
    			$result = $model->where($map)->save($savedata);
	    		if($result){
					echo $str='{"statusCode":"200","message":"操作成功！"}';
	    			exit;
				}else{
					echo $str='{"statusCode":"300","message":"操作异常，请联系技术！"}';
					exit;
				}	
    		}else{
    			echo $str='{"statusCode":"300","message":"'.$HttpObj->getoelementvalue('errorcode').'---'.$HttpObj->getoelementvalue('errormsg').'"}';
    			exit;
    		}
    	}else{
    		echo $str='{"statusCode":"300","message":"中奖详情不完整！"}';
    		exit;
    	}			
	}
	
	function convert_array($lotteryid,$array=array()){
		global $cz_grade, $cz_grade_type;
		include APP_PATH.'Home/Conf/caizhong.php';
		$grade_append = $cz_grade_type[$lotteryid]['appendnum'];
		$append_data = explode(',',$grade_append);
		$grade_data = array();
		$temp_append = array();
		$grade = $cz_grade_type[$lotteryid]['num'];
		for($i=0; $i<$grade; $i++){
			$grade_data[$i]['levelid'] = $cz_grade[$i]['levelid'];
			$grade_data[$i]['investcount'] = $array[$cz_grade[$i]['basic_z']];
			$tempbonus = $this->convert_unit($array[$cz_grade[$i]['basic_j']]);
			$grade_data[$i]['bonus'] = number_format($tempbonus * 100,0,'','');
			if($append_data[$i] == '1'){
				$temp_append[$i]['levelid'] = $cz_grade[$i]['appendid'];
				$temp_append[$i]['investcount'] = $array[$cz_grade[$i]['append_z']];
				$tempbonus2 = $this->convert_unit($array[$cz_grade[$i]['append_j']]);
				$temp_append[$i]['bonus'] = number_format($tempbonus2 * 100,0,'','');
			}			
		}
		//合并
		$newarr = array_merge($grade_data, $temp_append);
		return $newarr;
	}
	
	
	//查看推送详情
	function view(){
		extract($_REQUEST, EXTR_OVERWRITE);
		global $cz_grade, $cz_grade_type;
		include APP_PATH.'Home/Conf/caizhong.php';
		if($token == 'tob2c'){
			$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB1'));	
		}else if($token == 'tob2b'){
			$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB2'));
		}
		$arr=explode('_',$recid);
		$lotteryid = $arr[0];
		$issue = $arr[1];
		$header=array(
				'transactiontype'=>'60083',
				'messengerid'=>time('YmdHis',time()).uniqid(6),
				'agenterid'=>'10000001',
		);
		$ielement=array(
				'lotteryid'=>$cz_grade_type[$lotteryid]['playid'],
				'lotteryissue' => $issue	
		);
		$HttpObj->send($header,$ielement,'10000001','','web');
		$bonuscode = $HttpObj->getoelementvalue('bonuscode');
		$bonustime = $HttpObj->getoelementvalue('bonustime');
		$salevalue = $this->convert_unit($HttpObj->getoelementvalue('salevalue')/100);
		$nextbonusvalue = $this->convert_unit($HttpObj->getoelementvalue('nextbonusvalue')/100);
		$lotteryid = $HttpObj->getoelementvalue('lotteryid');
		$lotteryissue = $HttpObj->getoelementvalue('lotteryissue');
		$olist = $HttpObj->getelements();
		foreach($olist as $k=>$v){
			$olist[$k]['bonus'] = $this->convert_unit($v['bonus']/100);
		}
		$this->assign('bonuscode',$bonuscode);
		$this->assign('bonustime',$bonustime);
		$this->assign('salevalue',$salevalue);
		$this->assign('nextbonusvalue',$nextbonusvalue);
		$this->assign('lotteryid',$lotteryid);
		$this->assign('lotteryissue',$lotteryissue);
		$this->assign('olist',$olist);
		$this->display('view');
	}
	
	//确认录入开奖公告
	function affirm(){
		extract($_REQUEST, EXTR_OVERWRITE);
		global $cz_grade, $cz_grade_type;
		include APP_PATH.'Home/Conf/caizhong.php';
		$model=D('Kaijiang');//开奖信息表
		$arr=explode('_',$recid);
		$lotteryid=$arr[0];
		$issue=$arr[1];
		$map = array(
				'cz_id' => $lotteryid,
				'kj_issue' => $issue
		);
		//开奖信息查询
		$kjinfo = $model->where($map)->select();
		if($token == 'tob2c'){
			$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB1'));
			if($kjinfo){
				$flag = str_split($kjinfo[0]['send_flag']);
				$savedata['send_flag'] = '2'.$flag[1];
			}
		}else if($token == 'tob2b'){
			$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB2'));
			if($kjinfo){
				$flag = str_split($kjinfo[0]['send_flag']);
				$savedata['send_flag'] = $flag[0].'2';
			}
		}
		$header=array(
				'transactiontype'=>'60087',
				'messengerid'=>time('YmdHis',time()).uniqid(6),
				'agenterid'=>'10000001',
		);
		$ielement=array(
				'lotteryid'=>$cz_grade_type[$lotteryid]['playid'],
				'lotteryissue' => $issue,
				'status' => '3'
		);
		$HttpObj->send($header,$ielement,'10000001','','web');
		if($HttpObj->getoelementvalue('errorcode') == '0'){
			$savedata['sent_time'] = date('Y-m-d H:i:s');
			$result = $model->where($map)->save($savedata);
			if($result){
				echo $str='{"statusCode":"200","message":"操作成功！"}';
				exit;
			}else{
				echo $str='{"statusCode":"300","message":"操作异常，请联系技术！"}';
				exit;
			}	
		}else{
			echo $str='{"statusCode":"300","message":"'.$HttpObj->getoelementvalue('errorcode').'---'.$HttpObj->getoelementvalue('errormsg').'！"}';
			exit;
		}
	}
	
	//算奖，派奖
	function awardresult(){
		ini_set('max_execution_time','1800');
		extract($_REQUEST, EXTR_OVERWRITE);
		global $cz_grade, $cz_grade_type;
		include APP_PATH.'Home/Conf/caizhong.php';
		$model=D('Kaijiang');//开奖信息表
		$arr=explode('_',$recid);
		$lotteryid=$arr[0];
		$issue=$arr[1];
		$map = array(
				'cz_id' => $lotteryid,
				'kj_issue' => $issue
		);
		if($token == 'tob2c'){
			$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB1'));
		}else if($token == 'tob2b'){
			$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB2'));
		}
		$header=array(
				'transactiontype'=>'60084',
				'messengerid'=>time('YmdHis',time()).uniqid(6),
				'agenterid'=>'10000001',
		);
		$ielement=array(
				'lotteryid'=>$cz_grade_type[$lotteryid]['playid'],
				'lotteryissue' => $issue
		);
		//开奖信息查询
		$kjinfo = $model->where($map)->select();
		if($kjinfo){
			$flag = str_split($kjinfo[0]['send_flag']);
		}
		if($token == 'tob2c'){
			$savedata['send_flag'] = '4'.$flag[1];
		}else if($token == 'tob2b'){
			$savedata['send_flag'] = $flag[0].'4';
		}
		$model->where($map)->save($savedata);
		$HttpObj->send($header,$ielement,'10000001','','web');
		$errorcode = $HttpObj->getoelementvalue('errorcode');
		if($errorcode == '0'){
			if($token == 'tob2c'){
				$savedata2['send_flag'] = '3'.$flag[1];
			}else if($token == 'tob2b'){
				$savedata2['send_flag'] = $flag[0].'3';
			}
			$model->where($map)->save($savedata2);
			echo $str='{"statusCode":"200","message":"操作成功！"}';
			exit;
		}else if($errorcode == '10018'){
			echo $str='{"statusCode":"200","message":"派奖中，请稍后！"}';
			exit;
		}else{
			if($token == 'tob2c'){
				$savedata3['send_flag'] = '2'.$flag[1];
			}else if($token == 'tob2b'){
				$savedata3['send_flag'] = $flag[0].'2';
			}
			$model->where($map)->save($savedata3);
			if($errorcode == ''){
				echo $str='{"statusCode":"200","message":"派奖中，请稍后！"}';
			}else{
				echo $str='{"statusCode":"300","message":"'.$HttpObj->getoelementvalue('errorcode').'---'.$HttpObj->getoelementvalue('errormsg').'！"}';
			}
			exit;
		}
	}
	
	
	function getstatus($status){
		$result = array();
		switch($status){
			case '1':
				$result['status'] = '已导入';
				$result['optype'] = 'affirm';
				$result['target'] = 'ajaxTodo';
				$result['isend'] = '0';
				$result['style'] = 'right_d';
				break;
			case '2':
				$result['status'] = '开奖公告录入完成';
				$result['optype'] = 'awardresult';
				$result['target'] = 'ajaxTodo';
				$result['isend'] = '0';
				$result['style'] = 'troops_pj';
				break;
			case '3':
				$result['status'] = '算奖，派奖完成';
				$result['optype'] = 'index';
				$result['target'] = '';
				$result['isend'] = '1';
				$result['style'] = 'succeed_wc';
				break;
			case '4':
				$result['status'] = '派奖中';
				$result['optype'] = 'index';//awardresult
				$result['target'] = '';//ajaxTodo
				$result['isend'] = '1';
				$result['style'] = 'troops_pjz';
				break;
			default:
				$result['status'] = '未审核';
				$result['optype'] = 'push';
				$result['target'] = 'ajaxTodo';
				$result['isend'] = '0';
				$result['style'] = 'push_ts';
		}
		return $result;
	}
	
	//处理足彩赛果
	function handle_lottery_zc($data){
		foreach($data as $k=>$v2){
			if($v2 == ""){
				$tmp = '*';
			}else{
				$tmp = trim($v2);
			}
			$num .= $tmp.' ';
		}
		$num = trim($num);
		return $num;
	}
	
	function convert_unit($m){
		if(floatval($m)<0){
			$m = '0.00';
		}else{
			$m = number_format($m, 2, '.', '');
		}
		return $m;
	}
	
	//补抓开奖号码
	function supplycode(){
		global $cz_zucai;
		include APP_PATH.'Home/Conf/caizhong.php';
		$this->assign('cz_zucai',$cz_zucai);
		$this->display('supply');
	}
    
	function cron_supply(){
		extract($_REQUEST, EXTR_OVERWRITE);
		if($kj_issue){
			$url = 'http://newm.198tc.com/KJCenter/cron_result_500w_zc.php?date='.$kj_issue;
			$this->requestCurl($url);
			echo $str = '{"statusCode":"200","message":"操作成功！"}';
			exit;
		}else{
			echo $str='{"statusCode":"300","message":"操作异常！"}';
			exit;
		}
		
	}
	
	/**
	 * 请求方式
	 * @param string $remote_server 请求地址
	 */
	function requestCurl($remote_server){
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
	
	
}