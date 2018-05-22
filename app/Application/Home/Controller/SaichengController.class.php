<?php
namespace Home\Controller;
use Home\Controller\BaseController;
class SaichengController extends BaseController {
    function index(){
    	;
    }
    
    function changestatus(){
    	$ballarr=explode('_',$_REQUEST['ballid']);
        if($_REQUEST['cz_type']=='11'){
           $arr=array(
	    	  0=>array('lotteryid'=>209,'lotteryname'=>'胜平负'),
	    	  1=>array('lotteryid'=>210,'lotteryname'=>'让球胜平负'),
	    	  2=>array('lotteryid'=>211,'lotteryname'=>'比分'),
	    	  3=>array('lotteryid'=>212,'lotteryname'=>'总进球'),
	    	  4=>array('lotteryid'=>213,'lotteryname'=>'半全场'),
    	  );
    	  $cz_type_cn="竞彩足球";
        }
        if($_REQUEST['cz_type']=='12')
        {
           $arr=array(
	    	  0=>array('lotteryid'=>214,'lotteryname'=>'让分胜负'),
	    	  1=>array('lotteryid'=>215,'lotteryname'=>'大小分'),
	    	  2=>array('lotteryid'=>216,'lotteryname'=>'胜负'),
	    	  3=>array('lotteryid'=>217,'lotteryname'=>'胜分差'),
    	    );
    	    $cz_type_cn="竞彩篮球";
        }
    	
    	if($_REQUEST['act']=='zt'){
    	  $status=1;
    	  $btnvalue="暂停销售";
    	}
        if($_REQUEST['act']=='qx'){
    	  $btnvalue="取消比赛"; 
    	  $status=4;
    	}
       if($_REQUEST['act']=='xs'){
    	  $btnvalue="继续销售";
    	  $status=0; 
    	}
    	foreach($arr as $key=>$val){
    	   $where=array(
    	      'lotteryid' => $val['lotteryid'],
    	      'lotttime' => $ballarr[0],
    	      'ballid' => $ballarr[1],
    	   );
    	   $tempstatus=$this->getLotteryStatus($where);
    	 
    	   if($tempstatus==0){
    	     $statusstr="销售中";
    	   }
    	  if($tempstatus==1){
    	     $statusstr="暂停销售";
    	   }
    	  if($tempstatus==4){
    	     $statusstr="取消比赛";
    	   }
    	   $arr[$key]['status']=$statusstr;
    	   $arr[$key]['cz_type']=$cz_type_cn;
    	}
    	$this->assign('lotarr',$arr);
    	$this->assign('cz_type',$_REQUEST['cz_type']);
    	$this->assign('btnvalue',$btnvalue);
    	$this->assign('lotttime',$ballarr[0]);
    	$this->assign('ballid',$ballarr[1]);
    	$this->assign('status',$status);
    	$this->display('edit_status');
    }
    function donestatus(){
    	
    	$model=D('Saicheng');
    
    	$lotteryids=$_REQUEST['lotteryids'];
    	$arr=array(
    	  'cz_type'=>$_REQUEST['cz_type'],
    	  'lotttime'=>$_REQUEST['lotttime'],
    	  'ballid'=>$_REQUEST['ballid'],
    	  'status'=>$_REQUEST['status']
    	);
    	$str2='';
    	$runerror=0;
    	$runerror1=0;
    	foreach($lotteryids as $val){
    	   $arr['lotteryid']=$val;
    	   $temp[$val]=$arr['status'];
    	    $temarr=array(
		    	 'cz_type' => $arr['cz_type'],
		         'lotttime' => $arr['lotttime'],
		         'lotteryid'=> $val,
		         'ballid'=> $arr['ballid'],
    	         'status'=> $arr['status'],
    	   );
    	   $this->alertlotstatus($temarr);
    	   list($res,$res1)=$this->updatestatus($arr);
    	   $runerror+=$res['errrorcode'];
    	   $runerror1+=$res1['errrorcode'];
    	}
    	
    	 $where=array(
    	      'lotttime'=>$arr['lotttime'],
    	      'ballid'=>$arr['ballid'],
    	   );
    	  $lotarrlist=$model->where($where)->select();
    	  $tem2=unserialize($lotarrlist[0]['lotteryid_status']);
    	  $data1=array(
    	    'lotteryid_status'=>serialize(array_merge($temp,$tem2)),
    	  );
    	  $model->where($where)->save($data1);
    	if($runerror==0 && $runerror1==0)
    	{
    	   //publicstatus  -1： 未同步 ;0：b2b,b2c同时同步成功, 1:b2b成功，b2c不成功，2:b2b不成功，b2c成功 
    	   $data=array('publicstatus' => 0);
    	   $model->where($where)->save($data);
    	   $str='{"statusCode":"200","message":"操作成功！"}'; 
    	}else{
    	   if($runerror==0 && $runerror1!=0)
    	   {
    	      $data=array('publicstatus' => 2);
    	      $model->where($where)->save($data);
    	   }
    	   if($runerror!=0 && $runerror1==0){
    	      $data=array('publicstatus' => 1);
    	      $model->where($where)->save($data);
    	   }
    	   $str='{"statusCode":"300","message":"操作异常"}';
    	}
    	echo $str;
    	exit;
    }
    //更新彩种销售状态
    protected  function alertlotstatus($arr)
    {
       $LotstatusModel=D('Lotterystatus');
       $where=array(
         'cz_type' => $arr['cz_type'],
         'lotttime' => $arr['lotttime'],
         'lotteryid'=> $arr['lotteryid'],
         'ballid'=> $arr['ballid']
       );
       $olist=$LotstatusModel->where($where)->select();
       if(count($olist) > 0){
          $res=$LotstatusModel->where($where)->save($arr);
       }else{
          $res=$LotstatusModel->add($arr);
       }
       return $res;
    }
    
    //同步更新b2b与b2c后台
    protected function updatestatus($arr){
    	
         $header = array(
    			'transactiontype' => '40008',
                'messengerid' => time('YmdHis',time()).uniqid(6),
    			'agenterid' => '10000001',
    	);
    	
    	$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB1'));
    	$HttpObj1 = new \Org\Net\HttpClient(C('INTER_LIB2'));
    	$ielement = array(
             'lotteryid'     =>  $arr['lotteryid'],
             'lotteryissue'  =>  20000,
             'lotttime'      =>  $arr['lotttime'],
             'gamename'     =>  '',
             'isconcede'     =>  '',
             'iscore'     =>  '',
             'ballid'        =>  $arr['ballid'],
             'status'        =>  $arr['status'],
        );
        $HttpObj->send($header,$ielement,'10000001','','web');
        $HttpObj1->send($header,$ielement,'10000001','','web');
        $result = array(
    			'errorcode' => $HttpObj->getoelementvalue('errorcode'),
    		'errormsg' => $HttpObj->getoelementvalue('errormsg')
    	);
        $result1 = array(
    			'errorcode' => $HttpObj1->getoelementvalue('errorcode'),
    			'errormsg' =>  $HttpObj1->getoelementvalue('errormsg')
    	);
    	return array($result,$result1);
    }
    protected function getLotteryStatus($where){
         $LotstatusModel=D('Lotterystatus');
         $rows=$LotstatusModel->where($where)->select();
         return $rows[0]['status'];
    }
    function jingcai(){
    	$model=D('Saicheng');
    	$ballid=isset($_REQUEST['ballid']) ? trim($_REQUEST['ballid']) : '';
    	$weekarr=array(1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五',6=>'周六',7=>'周日');
    	$lotttime=isset($_REQUEST['lotttime']) && !empty($_REQUEST['lotttime']) ? $_REQUEST['lotttime'] : date('Y-m-d',time());
    	$map=array(
	    	'cz_type'=>11,
	    	'lotttime'=>date('Ymd',strtotime($lotttime))
    	);
    	if($ballid!=''){
    		$map['ballid'] = $ballid;
    	}
    	$numPerPage=18;
      $currentPage=isset($_REQUEST['pageNum']) ? $_REQUEST['pageNum'] : 1;
      $pageNumShown=10;
       
    	$totalCount=count($model->where($map)->select());
    	
    	$olist=$model->where($map)->limit(($currentPage-1)*$numPerPage,$numPerPage)->select();
    	
    	foreach($olist as $key=>$val)
    	{
    	  $wnum=$val['lottweek'];
    	  $olist[$key]['cnweek']=$weekarr[$wnum];
    	  $olist[$key]['gamestarttime']=date('y-m-d H:i',strtotime($val['gamestarttime']));
    	  $temp=explode("^",$val['status_single']);
    	  $olist[$key]['209']='';
    	  $olist[$key]['210']='';
    	  $olist[$key]['211']='';
    	  $olist[$key]['212']='';
    	  $olist[$key]['213']='';
    	 for($i=0;$i<count($temp);$i++)
    	  {
    	     $temp2=explode('|',$temp[$i]);
    	     $lotteryid=$temp2[0];
    	     $val2=$temp2[1];
    	      $where=array(
		         'cz_type' =>11,
		         'lotttime' => $val['lotttime'],
		         'lotteryid'=> $lotteryid,
		         'ballid'=> $val['ballid']
		       );
		      $statusval=$this->getLotteryStatus($where);
    	     if($statusval==1 || $statusval==4){
    	       $stylename= $statusval==1 ?  "zanting" : "closed";
    	       $olist[$key][$lotteryid]=$stylename;
    	     }else{
    	       $olist[$key][$lotteryid]=$temp2[1];
    	     }
    	  }
    	  if($val['publicstatus']==0)
    	  {
    	  	 $statuscn="推送成功";
    	  }
    	  if($val['publicstatus']==-1)
    	  {
    	  	 $statuscn="未推送";
    	  }
    	 if($val['publicstatus']==1)
    	  {
    	  	 $statuscn="b2b推送成功,b2c推送失败";
    	  }
    	  if($val['publicstatus']==2)
    	  {
    	  	 $statuscn="b2c推送成功,b2b推送失败";
    	  }
    	  $olist[$key]['publicstatus']=$statuscn;
    	}
    	
    	$this->assign('currentPage',$currentPage);
    	$this->assign('numPerPage',$numPerPage);
    	$this->assign('totalCount',$totalCount);
    	$this->assign('pageNumShown',$pageNumShown);
    	$this->assign('lotttime',$lotttime);
    	$this->assign('ballid',$ballid);
    	$this->assign('sailist',$olist);
      $this->display();
    }
    function lancai(){
      $model=D('Saicheng');
    	$weekarr=array(1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五',6=>'周六',7=>'周日');
    	$ballid=isset($_REQUEST['ballid']) ? trim($_REQUEST['ballid']) : '';
    	$lotttime=isset($_REQUEST['lotttime']) && !empty($_REQUEST['lotttime']) ? $_REQUEST['lotttime'] : date('Y-m-d');
    	
    	$map=array(
    	  'cz_type'=>12,
    	  'lotttime'=>date('Ymd',strtotime($lotttime))
    	);
    	if($ballid!=''){
    		$map['ballid'] = $ballid;
    	}
    	$numPerPage=18;
      $currentPage=isset($_REQUEST['pageNum']) ? $_REQUEST['pageNum'] : 1;
      $pageNumShown=10;
      
    	$totalCount=count($model->where($map)->select());
    	
    	$olist=$model->where($map)->limit(($currentPage-1)*$numPerPage,$numPerPage)->select();
    	
    	foreach($olist as $key=>$val){
    	  $wnum=$val['lottweek'];
    	  $olist[$key]['cnweek']=$weekarr[$wnum];
    	  $olist[$key]['gamestarttime']=date('y-m-d H:i',strtotime($val['gamestarttime']));
    	  $temp=explode("^",$val['status_single']);
    	  $olist[$key]['214']='';
    	  $olist[$key]['215']='';
    	  $olist[$key]['216']='';
    	  $olist[$key]['217']='';
    	  for($i=0;$i<count($temp);$i++)
    	  {
    	     $temp2=explode('|',$temp[$i]);
    	     $lotteryid=$temp2[0];
    	     $where=array(
		         'cz_type' =>12,
		         'lotttime' => $val['lotttime'],
		         'lotteryid'=> $lotteryid,
		         'ballid'=> $val['ballid']
		       );
		      $statusval=$this->getLotteryStatus($where);
    	     if($statusval==1 || $statusval==4){
    	       $stylename= $statusval==1 ?  "zanting" : "closed";
    	       $olist[$key][$lotteryid]=$stylename;
    	     }else{
    	       $olist[$key][$lotteryid]=$temp2[1];
    	     }
    	     
    	      if($val['publicstatus']==0)
	    	  {
	    	  	 $statuscn="推送成功";
	    	  }
	    	  if($val['publicstatus']==-1)
	    	  {
	    	  	 $statuscn="未推送";
	    	  }
	    	 if($val['publicstatus']==1)
	    	  {
	    	  	 $statuscn="b2b推送成功,b2c推送失败";
	    	  }
	    	  if($val['publicstatus']==2)
	    	  {
	    	  	 $statuscn="b2c推送成功,b2b推送失败";
	    	  }
	    	     $olist[$key]['publicstatus']=$statuscn;
	    	  }
    	}
    	$this->assign('ballid',$ballid);
    	$this->assign('lotttime',$lotttime);
    	$this->assign('currentPage',$currentPage);
    	$this->assign('numPerPage',$numPerPage);
    	$this->assign('totalCount',$totalCount);
    	$this->assign('pageNumShown',$pageNumShown);
    	$this->assign('sailist',$olist);
      $this->display();
    }
    
    function zucai(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	global $cz_zucai;
    	include APP_PATH.'Home/Conf/caizhong.php';
    	$model = D("Foot");
    	$issue = $model->field('max(lotteryissue) as lotteryissue')->select();
    	$lotteryissue = empty($lotteryissue) ? $issue[0]['lotteryissue'] : $lotteryissue;
    	if($act == "query"){
    		$lotteryid = ($lotteryid != '0') ? $lotteryid : '324,441,326,325';
	    	$map = array('lotteryid' => array('in', $lotteryid), 'lotteryissue' => $lotteryissue);
	    	$olist = $model->where($map)->group('lotteryid')->select();
	    	$matchlist = array();
	    	foreach($olist as $key=>$val){
	    		$matchlist[$key]['lotteryid'] = $val['lotteryid'];
	    		$matchlist[$key]['lotteryissue'] = $val['lotteryissue'];
	    		$matchlist[$key]['lotteryname'] = $this->getlotteryname($val['lotteryid'], $cz_zucai);
	    		$matchlist[$key]['send_flag'] = $val['send_flag'];
	    		if($val['send_flag'] == '0'){
	    			$matchlist[$key]['b2cstatus']='未审核';
	    			$matchlist[$key]['b2bstatus']='未审核';
	    			$matchlist[$key]['b2coptype']='push';
	    			$matchlist[$key]['b2boptype']='push';
	    			$matchlist[$key]['b2ctarget'] = 'ajaxTodo';
	    			$matchlist[$key]['b2btarget'] = 'ajaxTodo';
	    			$matchlist[$key]['b2cisend'] = '0';
	    			$matchlist[$key]['b2bisend'] = '0';
	    			$matchlist[$key]['b2cstyle'] = 'push_ts';
	    			$matchlist[$key]['b2bstyle'] = 'push_ts';
	    		}else{
	    			$first = substr($val['send_flag'],0,1);
	    			$second = substr($val['send_flag'],1,1);
	    			$firstarr = $this->get_zcduizhen($first);
	    			$matchlist[$key]['b2cstatus'] = $firstarr['status'];
	    			$matchlist[$key]['b2coptype'] = $firstarr['optype'];
	    			$matchlist[$key]['b2ctarget'] = $firstarr['target'];
	    			$matchlist[$key]['b2cisend'] = $firstarr['isend'];
	    			$matchlist[$key]['b2cstyle'] = $firstarr['style'];
	    			$secondarr = $this->get_zcduizhen($second);
	    			$matchlist[$key]['b2bstatus'] = $secondarr['status'];
	    			$matchlist[$key]['b2boptype'] = $secondarr['optype'];
	    			$matchlist[$key]['b2btarget'] = $secondarr['target'];
	    			$matchlist[$key]['b2bisend'] = $secondarr['isend'];
	    			$matchlist[$key]['b2bstyle'] = $secondarr['style'];
	    		}
	    	}
	    	$this->assign('matchlist', $matchlist);
    	}
    	$this->assign('lotteryid', $_REQUEST['lotteryid']);
    	$this->assign('lotteryissue', $lotteryissue);
    	$this->assign('cz_zucai',$cz_zucai);
       	$this->display();
    }
    //编辑赛程
    function edit(){
    	extract($_REQUEST, EXTR_OVERWRITE);
        if($act=='zucai'){
        	global $cz_zucai,$cz_grade_type;
        	include APP_PATH.'Home/Conf/caizhong.php';
        	$model = D("Foot");
        	$arr = explode('_',$recid);
			$lotteryid = $arr[0];
			$lotteryissue = $arr[1];
			$token = isset($token)?$token:'local';
       		if($token == 'local'){
       			$map = array('lotteryid' => $lotteryid, 'lotteryissue' => $lotteryissue);
       			$olist = $model->where($map)->order('ballid*1 asc')->select();
       			foreach($olist as $key=>$val){
       				$olist[$key]['lotteryname'] = $this->getlotteryname($val['lotteryid'], $cz_zucai);
       				$olist[$key]['starttime'] = date('Y-m-d H:i',strtotime($val['starttime']));
       			}	
       		}else{
       			$ielement=array(
       				'lotteryid'=>$cz_grade_type[$lotteryid]['playid'],
       				'lotteryissue' => $lotteryissue,
       				'ballid' => ''
       			);
       			$olist = $this->view_zucai($token, $ielement);
       			foreach($olist as $key=>$val){
       				$olist[$key]['lotteryname'] = $val['description'];
       			}
       			
       		}
       		$this->assign('token', $token);
        	$this->assign('olist', $olist);
           	$this->display('edit_zucai');
        }
    }
   	
    //添加赛程
    function add(){
    	extract($_REQUEST, EXTR_OVERWRITE);
        if($act == 'zucai'){
        	global $cz_zucai;
        	include APP_PATH.'Home/Conf/caizhong.php';
        	$list = array('','','','','','','','','','','','','','');
        	$this->assign('football',$list);
        	$this->assign('cz_zucai',$cz_zucai);
        	$this->display('add_zucai');
        }
    }
    
    //删除
    function delete(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	if($act == 'zucai'){
    		$model=D('Foot');
    		$arr = explode('_',$recid);
    		$lotteryid = $arr[0];
    		$lotteryissue = $arr[1];
    		$map = array('lotteryid' => $lotteryid, 'lotteryissue' => $lotteryissue);
    		$list = $model->where($map)->limit(0,1)->select();
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
    	
    }
    
    //查看B2C/B2B推送数据
    function view_zucai($token, $ielement){
    	if($token == 'tob2c'){
    		$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB1'));
    	}else if($token == 'tob2b'){
    		$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB2'));
    	}
    	$header=array(
    		'transactiontype'=>'60006',
    		'messengerid'=>time('YmdHis',time()).uniqid(6),
    		'agenterid'=>'10000001',
    	);
    	$HttpObj->send($header,$ielement,'10000001','','web');
    	return $HttpObj->getelements();
    }
    
    //保存足彩对阵信息
    function act_zucai(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	$model = D("Foot");
    	$model2 = D("Issue");
    	if($token == "add"){
    		$map = array('lotteryid' => $lotteryid, 'lotteryissue'=>$lotteryissue);
    		$count = $model->where($map)->count();
    		if($count <= 0){
    			$map2 =  array('cz_id' => $lotteryid, 'lotteryissue'=>$lotteryissue);
    			$count2 = $model2->where($map2)->count();
    			if($count2 > 0){
	    			//保存对阵信息
		    		if($lotteryid == '324' || $lotteryid == '441'){
		    			$ball_count = 14;
		    		}else if($lotteryid == '325'){
		    			$ball_count = 4;
		    		}else if($lotteryid == '326'){
		    			$ball_count = 12;
		    		}
		    		
		    		$dataList = array();
		    		for($i = 0; $i < $ball_count; $i++){
		    			$dataList[$i]['lotteryid'] = $lotteryid;
		    			$dataList[$i]['lotteryissue'] = $lotteryissue;
		    			$ballid = 'ballid_'.$i;
						$dataList[$i]['ballid'] = $$ballid;
				       	$matchname = 'matchname_'.$i;
				       	$dataList[$i]['matchname'] = $$matchname;
				       	$starttime = 'starttime_'.$i;
				       	$dataList[$i]['starttime'] = $$starttime;
				       	$mainname = 'mainname_'.$i;
				       	$dataList[$i]['mainname'] = $$mainname;
				      	$custname = 'custname_'.$i;
				       	$dataList[$i]['custname'] = $$custname;
		    		}
		    		$result = $model->addAll($dataList);
		    		if($lotteryid == '324'){
		    			for($i = 0; $i < $ball_count; $i++){
		    				$dataList[$i]['lotteryid'] = '441';
		    			}
		    			$result = $model->addAll($dataList);
		    		}
		    		if($result){
		    			echo $str = '{"statusCode":"200","message":"操作成功！"}';
		    			exit;
		    		}else{
		    			echo $str = '{"statusCode":"300","message":"操作异常！"}';
		    			exit;
		    		}
    			}else{
    				echo $str = '{"statusCode":"300","message":"请检查新期是否存在！"}';
    				exit;
    			}
    		}else{
    			echo $str = '{"statusCode":"300","message":"'.$lotteryissue.'期数据已经存在！"}';
    			exit;
    		}
    	}else if($token == "edit"){ // 更新
    		global $cz_zucai,$cz_grade_type;
    		include APP_PATH.'Home/Conf/caizhong.php';
    		if($act == "saveform"){
    			$map = array('lotteryid' => $lotteryid, 'lotteryissue' => $lotteryissue, 'ballid' => $ballid);
    			$savedata = array(
    				'matchname'	=> $matchname,
    				'mainname' => $mainname,
    				'custname' => $custname,
    				'starttime' => $starttime
    				);
    			$result = $model->where($map)->save($savedata);
    			if($result){
    				$element = $savedata;
    				$element['lotteryid'] = $cz_grade_type[$lotteryid]['playid'];
    				$element['lotteryissue'] = $lotteryissue;
    				$element['ballid'] = $ballid;
    				$flag = str_split($sendflag);
    				if($flag[0] == '1'){//B2C-已导入
    					$resultb2c = $this->edit_interface('tob2c',$element);
    				}
    				if($flag[1] == '1'){//B2B-已导入
    					$resultb2b = $this->edit_interface('tob2b',$element);
    				}
    				if($resultb2c && $resultb2c['errorcode'] != '0'){
    					echo $str = '{"statusCode":"300","message":"B2C操作异常：'.$resultb2c['errorcode'].'---'.$resultb2c['errormsg'].'！","rel":"add"}';
    					exit;
    				}else if($resultb2b && $resultb2b['errorcode'] != '0'){
    					echo $str = '{"statusCode":"300","message":"B2B操作异常：'.$resultb2b['errorcode'].'---'.$resultb2b['errormsg'].'！","rel":"add"}';
    					exit;
    				}else{
    					echo $str = '{"statusCode":"200","message":"修改开奖信息成功！","rel":"add","callbackType":"forward","forwardUrl":"/index.php/Home/Saicheng/edit?recid='.$lotteryid.'_'.$lotteryissue.'&act=zucai"}';
    					exit;
    				}
    			}else{
    				echo $str='{"statusCode":"300","message":"修改失败或者没有更新任何内容！"}';
    				exit;
    			}
    		}else{
    			$arr = explode('_',$recid);
    			$lotteryid = $arr[0];
    			$lotteryissue = $arr[1];
    			$ballid = $arr[2];
    			$map = array('lotteryid' => $lotteryid, 'lotteryissue' => $lotteryissue, 'ballid' => $ballid);
    			$match = $model->where($map)->select();
    			$match[0]['lotteryname'] = $this->getlotteryname($match[0]['lotteryid'], $cz_zucai);
    			$this->assign('match', $match[0]);
    			$this->assign('token', $token);
    			$this->display('edit_zucai');
    		}
    	}
    	
    }
 
    function edit_interface($token, $ielement){
    	if($token == 'tob2c'){
    		$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB1'));
    	}else if($token == 'tob2b'){
    		$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB2'));
    	}
    	$header=array(
    			'transactiontype'=>'63003',
    			'messengerid'=>time('YmdHis',time()).uniqid(6),
    			'agenterid'=>'10000001',
    	);
    	$HttpObj->send($header,$ielement,'10000001','','web');
    	$result = array(
    			'errorcode' => $HttpObj->getoelementvalue('errorcode'),
    			'errormsg' => $HttpObj->getoelementvalue('errormsg')
    	);
    	return $result;
    }
    
    /**
     * 推送足彩对阵信息
     */
    function push(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	global $cz_grade_type;
    	include APP_PATH.'Home/Conf/caizhong.php';
    	$model = D('Foot');
    	$arr = explode('_',$recid);
    	$lotteryid = $arr[0];
    	$issue = $arr[1];
    	$sendflag = $arr[2];
    	$map = array('lotteryid' => $lotteryid, 'lotteryissue' => $issue);
    	$matchlist = $model->where($map)->select();
    	if($matchlist){
    		if($token == 'tob2c'){
    			$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB1'));
    			if($sendflag == '0'){
    				$savedata['send_flag'] = '10';
    			}else{
    				$flag = str_split($sendflag);
    				$savedata['send_flag'] = '1'.$flag[1];
    			}
    		}else if($token == 'tob2b'){
    			$HttpObj = new \Org\Net\HttpClient(C('INTER_LIB2'));
    			if($sendflag == '0'){
    				$savedata['send_flag'] = '01';
    			}else{
    				$flag = str_split($sendflag);
    				$savedata['send_flag'] = $flag[0].'1';
    			}
    		}
    		$list = array();
    		foreach($matchlist as $key=>$val){
    			$list[$key]['ballid'] = $val['ballid'];
    			$list[$key]['matchname'] = $val['matchname'];
    			$list[$key]['starttime'] = date('Y-m-d H:i',strtotime($val['starttime']));
    			$list[$key]['mainname'] = $val['mainname'];
    			$list[$key]['custname'] = $val['custname'];
    			$list[$key]['mainballs'] = '';
    			$list[$key]['custballs'] = '';
    			$list[$key]['mainballvalue'] = '';
    			$list[$key]['custballvalue'] = '';
    		}
    		$header = array(
    			'transactiontype' => '60005',
    			'messengerid' => time('YmdHis',time()).uniqid(6),
    			'agenterid' => '10000001',
    		);
    		$ielement = array(
    			'lotteryid'     => $cz_grade_type[$lotteryid]['playid'],
    			'lotteryissue'  => $issue,
    			'footlist'      => json_encode($list)
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
    
    
    function getlotteryname($id, $array){
    	$name = '';
    	foreach($array as $k=>$v){
    		if($id == $v['value']){
    			$name = $v['name'];
    		}
    	}
    	return $name;
    }
    
    
    /**
     * 获取足彩对阵信息推送状态
     */
    function get_zcduizhen($status){
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
    
    /**
     * 系统录入足彩对阵信息
     */
    function grabzc(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	global $cz_zucai;
    	include APP_PATH.'Home/Conf/caizhong.php';
    	$model = D("Issue");
    	//默认胜负彩
    	$zcid = empty($zcid) ? '324' : $zcid;
    	$map = array('cz_id' => $zcid);
    	$issue = $model->field('max(lotteryissue) as lotteryissue')->where($map)->select();
    	$this->assign('issue',$issue[0][lotteryissue]);
    	$this->assign('cz_zucai',$cz_zucai);
    	$this->display('grabzc');
    }
    
    //获取足彩最新期号
    function getgrabissue(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	$model = D("Issue");
    	if($grabid){
    		$map = array('cz_id' => $grabid);
    		$issue = $model->field('max(lotteryissue) as lotteryissue')->where($map)->select();
    		echo $str = '{"statusCode":"200","message":"操作成功！","grabissue":"'.$issue[0][lotteryissue].'"}';
    		exit;
    	}
    	
    }
    
    //抓取任务开始
    function grabzc_run(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	$model = D("Foot");
    	$model2 = D("Issue");
    	$grabarr = array(
    		'324' => 'http://www.tjlottery.gov.cn/ly/do.php/getmatchinfo/z/type/108/issue/',
    		'441' => 'http://www.tjlottery.gov.cn/ly/do.php/getmatchinfo/z/type/109/issue/',
    		'325' => 'http://www.tjlottery.gov.cn/ly/do.php/getmatchinfo/z/type/111/issue/',
    		'326' => 'http://www.tjlottery.gov.cn/ly/do.php/getmatchinfo/z/type/110/issue/'		
    	);
    	$map = array('lotteryid' => $lotteryid, 'lotteryissue'=>$lotteryissue);
    	$count = $model->where($map)->count();
    	if($count <= 0 && $grabarr[$lotteryid]){
    		$map2 =  array('cz_id' => $lotteryid, 'lotteryissue'=>$lotteryissue);
    		$count2 = $model2->where($map2)->count();
    		if($count2 > 0){
    			$graburl = $grabarr[$lotteryid].$lotteryissue;
    			$grablist = $this->requestCurl($graburl);
    			$grablist = $this->jsonToArray($grablist);
    			if($lotteryid == '326'){
    				$grablist = $this->increameArray($grablist);
    			}
    			$ballarr = array('324'=>14, '441'=>14, '325'=>4, '326'=>12);
    			$ball_count = $ballarr[$lotteryid];
    			if(count($grablist) == $ball_count){
    				foreach($grablist as $k=>$row){
    					$grablist[$k]['lotteryid'] = $lotteryid;
    					$grablist[$k]['lotteryissue'] = $lotteryissue;
    				}
    				$result = $model->addAll($grablist);
    				if($result){
    					echo $str = '{"statusCode":"200","message":"操作成功！"}';
    					exit;
    				}else{
    					echo $str = '{"statusCode":"300","message":"操作异常！"}';
    					exit;
    				}
    			}else{
    				echo $str = '{"statusCode":"300","message":"抓取信息异常！"}';
    				exit;
    			}
    		}else{
    			echo $str = '{"statusCode":"300","message":"请检查新期是否存在！"}';
    			exit;
    		}
    	}else{
    		echo $str = '{"statusCode":"300","message":"'.$lotteryissue.'期数据已经存在！"}';
    		exit;
    	}
    }
    
    
    function jsonToArray($jsonStr){
    	$footballList_format = array();
    	$transfer = array();
    	$footballList = json_decode($jsonStr);
    	foreach ($footballList as $key => $value){
    		if(is_array($value)){
    			
    		}else{
    			$transfer[$key] = $value;
    			if (in_array($key, array('mainTeam','cusTeam','matchName','matchTime'))){
    				$$key = explode(',', $transfer[$key]);
    				$footballList_format[$key] = $$key;
    			}
    		}
    	}
    	$temp_array = $footballList_format;
    	$footballList_format = array();
    	$len = count($temp_array['matchName']);
    	for($k=0; $k<$len; $k++){
    		$footballList_format[$k]['ballid'] = $k+1;
    		$footballList_format[$k]['matchname'] = $temp_array['matchName'][$k];
    		$footballList_format[$k]['starttime'] = substr($temp_array['matchTime'][$k],0,16);
    		$footballList_format[$k]['mainname'] = $temp_array['mainTeam'][$k];
    		$footballList_format[$k]['custname'] = $temp_array['cusTeam'][$k];
    	}
    	return $footballList_format;
    }
    
    function increameArray($tempArray){
    	$footballList = array();
    	if(!is_array($tempArray))
    		return '';
    	$ballid = 0;
    	foreach($tempArray as $element){
    		$key_value = $element['custname'];
    		$element['custname'] = $key_value;
    		$element['ballid'] = ++$ballid;
    		array_push($footballList,$element);
    		$element['custname'] = $key_value;
    		$element['ballid'] = ++$ballid;
    		array_push($footballList,$element);
    	}
    	return $footballList;
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