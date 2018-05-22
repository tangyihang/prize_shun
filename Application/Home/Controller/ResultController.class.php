<?php
namespace Home\Controller;
use Home\Controller\BaseController;
class ResultController extends BaseController {
   
    function index(){
       $this->display();
    }

    function jingcai(){
    	extract($_REQUEST, EXTR_OVERWRITE);
        $status=isset($_POST['status']) ? $_POST['status']: 1;
        $lotttime= isset($_POST['lotttime']) ? $_POST['lotttime'] : date('Y-m-d',time()-24*3600);
        $ballid= isset($_POST['ballid']) ? $_POST['ballid']: '';
        $cnweek=array('1'=>'周一','2'=>'周二','3'=>'周三','4'=>'周四','5'=>'周五','6'=>'周六','7'=>'周日');
        if($act == 'query'){
        	$model=D('Result');
        	$pageNum = isset($pageNum) ? $pageNum : '1';
        	$numPerPage = isset($numPerPage) ? $numPerPage : '20';
        	$startnum = ($pageNum - 1) * $numPerPage;
        	if($status==1){
        		$having='count(*)<3';
        	}else{
        		$having='count(*)>2';
        	}
        	$map=array();
        	$map['lotttime']=$lotttime;
        	if($ballid)
        	{
        		$map['ballid']=array('like',"%".$ballid);
        	}
       
        	$res = $model->where($map)->group('lotttime,ballid,half_score,full_score')->select();
        	if($res){
        		$count = count($res);
        		$yunum = $count%$numPerPage;
        		if($yunum == '0'){
        			$pageTotal = floor($count/$numPerPage);
        		}else{
        			$pageTotal = floor($count/$numPerPage)+1;
        		}
        	}
        	$resultlist=$model->where($map)->field(true)->group('lotttime,ballid,half_score,full_score')->having($having)->limit($startnum, $numPerPage)->select();
        	foreach($resultlist as $key => $value){
        		$resultlist[$key]['id']=$key+1;
        		$resultlist[$key]['cnballid']=$cnweek[substr($value['ballid'],0,1)].substr($value['ballid'],1,3);
        	}
        	$this->assign('pageTotal',$pageTotal);
        	$this->assign('totalCount',$count);
        	$this->assign('pageNumShown','3');
	      	$this->assign('numPerPage',$numPerPage);
	      	$this->assign('currentPage',$pageNum);
        	$this->assign('resultlist',$resultlist);
        }
    	
        $this->assign('lotttime',$lotttime);
        $this->assign('ballid',$ballid);
        $this->assign('status',$status);
    	$this->display();
    }
    
    function lancai(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	$status=isset($_POST['status']) ? $_POST['status']: 1;
    	$lotttime= isset($_POST['lotttime']) ? $_POST['lotttime'] : date('Y-m-d',time()-24*3600);
    	$ballid= isset($_POST['ballid']) ? $_POST['ballid']: '';
    	$cnweek=array('1'=>'周一','2'=>'周二','3'=>'周三','4'=>'周四','5'=>'周五','6'=>'周六','7'=>'周日');
    	if($act == 'query'){
    		$model=D('LcResult');
    		$pageNum = isset($pageNum) ? $pageNum : '1';
    		$numPerPage = isset($numPerPage) ? $numPerPage : '20';
    		$startnum = ($pageNum - 1) * $numPerPage;
    		if($status==1){
    			$having='count(*)<3';
    		}else{
    			$having='count(*)>2';
    		}
    		$map=array();
    		$map['lotttime']=$lotttime;
    		if($ballid)
    		{
    			$map['ballid']=array('like',"%".$ballid);
    		}
    		$res = $model->where($map)->group('lotttime,ballid,first_score,two_score,three_score,four_score,add_score,full_score')->select();
    		if($res){
    			$count = count($res);
    			$yunum = $count%$numPerPage;
    			if($yunum == '0'){
    				$pageTotal = floor($count/$numPerPage);
    			}else{
    				$pageTotal = floor($count/$numPerPage)+1;
    			}
    		}
    		$resultlist=$model->where($map)->group('lotttime,ballid,first_score,two_score,three_score,four_score,add_score,full_score')->having($having)->limit($startnum, $numPerPage)->select();
    		foreach($resultlist as $key => $value){
    			$resultlist[$key]['id']=$key+1;
    			$resultlist[$key]['cnballid']=$cnweek[substr($value['ballid'],0,1)].substr($value['ballid'],1,3);
    		}
    		$this->assign('pageTotal',$pageTotal);
    		$this->assign('totalCount',$count);
    		$this->assign('pageNumShown','3');
    		$this->assign('numPerPage',$numPerPage);
    		$this->assign('currentPage',$pageNum);
    		$this->assign('resultlist',$resultlist);
    		
    	}
    	$this->assign('lotttime',$lotttime);
    	$this->assign('ballid',$ballid);
    	$this->assign('status',$status);
		$this->display();
    }
    
	
    public function view(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	$source = isset($source) ? $source : 'jc';
    	$act = isset($act) ? $act : 'view';
    	$arr = explode('_',$recid);
    	$lotttime = $arr[0];
    	$ballid = $arr[1];
    	$model = D('Result');
    	$display = 'view';
    	if($source == 'lc'){
    		$model = D('LcResult');
    		$display = 'viewlc';
    	}
    	$where=array(
    			'lotttime'=> $lotttime,
    			'ballid' => $ballid
    	);
    	$Rlist=$model->where($where)->select();
    	$totals=$model->where($where)->count();
    	if($totals < 3){
    		for($i=0;$i<3-$totals;$i++){
    			$Rlist[]=array();
    		}
    	}
    	foreach($Rlist as $key=> $value){
    		$Rlist[$key]['id']=$key+1;
    	}
    	$this->assign('lotttime',$lotttime);
    	$this->assign('ballid',$ballid);
    	$this->assign('Rlist',$Rlist);
    	$this->assign('act',$act);
    	$this->display($display);
    }
    
    
	public function edit_act(){
    	$lotttime = $_POST['lotttime'];
    	$ballid = $_POST['ballid'];
    	$newlist=array();
    	$source_arr= $_POST['source'];
    	$half_arr= $_POST['half'];
    	$full_arr= $_POST['full'];
    	$isright_arr= $_POST['isRight'];
    	foreach($source_arr as $key => $value){
    		$newlist[] =array(
    				'lotteryid'=>0,
    				'lotttime'=>$lotttime,
    				'ballid'=>$ballid,
    				'source'=>$value,
    				'half_score'=>$half_arr[$key],
    				'full_score'=> $full_arr[$key],
    				'status'=>'-1',
    				'isright'=> $isright_arr[$key]
    			);
    	}
    	$noright=0;
      	foreach($newlist as $val){
      		if($val['isright']=='on'){
      			$noright++;
      	 		$model=D('Result');
      	  		$where=array(
      		 		'lotttime'=> $val['lotttime'],
      		 		'ballid'=> $val['ballid'],
      		 		'source'=> $val['source']
      			);
      			$countnum=$model->where($where)->count();
      			if($countnum>0){
      			 	$data=array(
	      		 		'half_score'=>$val['half_score'],
	      		 		'full_score'=>$val['full_score'],
	      		 		//'status'=>'-1'
      		 		);
	      		 	$result=$model->where($where)->save($data);
	      		 	if($result){
						echo $str='{"statusCode":"200","message":"修改赛果成功！"}';
						exit;
	      		 	}else{
	      		 	  	echo $str='{"statusCode":"300","message":"修改失败或者没有更新任何内容！"}';
						exit;
	      		 	}
      			}else{
    		    	$this->saveToDB('jc',$val);
    			}
			} 
    	}
		if($noright==0){
			echo $str='{"statusCode":"300","message":"未勾选需要修改的项目！"}';
			exit;
    	}
    }
    
    public function edit_act_lc(){
    	$lotttime = $_POST['lotttime'];
    	$ballid = $_POST['ballid'];
    	$newlist=array();
    	$source_arr= $_POST['source'];
    	$first_arr= $_POST['first'];
    	$two_arr= $_POST['two'];
    	$three_arr= $_POST['three'];
    	$four_arr= $_POST['four'];
		$add_arr= $_POST['jia'];
		$full_arr= $_POST['full'];
    	$isright_arr= $_POST['isRight'];
    	foreach($source_arr as $key => $value){
    		$newlist[] =array(
    				'lotteryid'=>0,
    				'lotttime'=>$lotttime,
    				'ballid'=>$ballid,
    				'source'=>$value,
    				'first_score'=>$first_arr[$key],
    				'two_score'=> $two_arr[$key],
    				'three_score'=> $three_arr[$key],
    				'four_score'=> $four_arr[$key],
					'add_score'=> $add_arr[$key],
					'full_score'=> $full_arr[$key],
    				'status'=>'-1',
    				'isright'=> $isright_arr[$key]
    			);
    	}
    	$noright=0;
    	foreach($newlist as $val){
    		if($val['isright']=='on'){
    			$noright++;
    			$model=D('LcResult');
    			$where=array(
    					'lotttime'=> $val['lotttime'],
    					'ballid'=> $val['ballid'],
    					'source'=> $val['source']
    			);
    			$countnum=$model->where($where)->count();
    			if($countnum>0){
    				$data=array(
    						'first_score'=>$val['first_score'],
    						'two_score'=>$val['two_score'],
    						'three_score'=>$val['three_score'],
    						'four_score'=>$val['four_score'],
							'add_score'=> $val['add_score'],
					        'full_score'=> $val['full_score'],
    						//'status'=>'-1'
    				);
    				$result=$model->where($where)->save($data);
    				if($result){
    					echo $str='{"statusCode":"200","message":"修改赛果成功！"}';
    					exit;
    				}else{
    					echo $str='{"statusCode":"300","message":"修改失败或者没有更新任何内容！"}';
    					exit;
    				}
    			}else{
    				$result = $this->saveToDB('lc',$val);
    			}
    		}
    	}
    	if($noright==0){
    		echo $str='{"statusCode":"300","message":"未勾选需要修改的项目！"}';
    		exit;
    	}
    }
    
    
    public function saveToDB($source, $data){
    	if($source == 'jc'){
    		$model=D('Result');
    		$ielement=array(
    				'lotteryid' => $data['lotteryid']?$data['lotteryid']:0,
    				'lotttime' => $data['lotttime'],
    				'ballid' =>  $data['ballid'],
    				'source' => $data['source'],
    				'half_score' => $data['half_score'],
    				'full_score' => $data['full_score'],
    				'addtime' => date('Y-m-d H:i:s'),
    				'status' => '-1'
    		);
    	}else if($source == 'lc'){
    		$model=D('LcResult');
    		$ielement=array(
    				'lotteryid' => $data['lotteryid']?$data['lotteryid']:0,
    				'lotttime' => $data['lotttime'],
    				'ballid' =>  $data['ballid'],
    				'source' => $data['source'],
    				'first_score' => $data['first_score'],
    				'two_score' => $data['two_score'],
    				'three_score' => $data['three_score'],
    				'four_score' => $data['four_score'],
    				'addtime' => date('Y-m-d H:i:s'),
    				'status' => '-1'
    		);
    	}
    	if($data['lotttime']=='' || $data['ballid']=='' || $data['status']=='' ){
    		echo $str='{"statusCode":"300","message":"操作异常！"}';
    		exit;
    	}else{
    		if($model->add($ielement)){
    			echo $str='{"statusCode":"200","message":"修改赛果成功！"}';
    			exit;
    		}else{
    			echo $str='{"statusCode":"300","message":"修改失败或者没有更新任何内容！"}';
    			exit;
    		}
    	}
    }
	
	/**
   	 * 添加赛果 
   	 * */
    function add(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	if($act == 'jc'){
    		$this->display('addjc');
    	}elseif($act == 'lc'){
    		$this->display('addlc');
    	}
    }
    
    function add_act_jc(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	$model = D('Result');
    	$weekday = date('w',strtotime($lotttime));
    	if($weekday == '0'){
    		$weekday = '7';
    	}
    	$ballids = $weekday.$ballid;
    	$map = array('ballid' => $ballids, 'lotttime' => $lotttime);
    	$res = $model->where($map)->select();
    	if(count($res) == 0){
    		$rdata = array(
    			'lotteryid' => '0',
    			'ballid' => $ballids,
    			'lotttime' => $lotttime,
    			'match_starttime' => $matchtime,
    			'source' => $sourcejc,
    			'addtime' => date('Y-m-d H:i:s'),
    			'status' => '-1'
    			);
    		$result = $model->add($rdata);
    		if($result){
    			echo $str='{"statusCode":"200","message":"录入竞彩赛果信息成功！"}';
    			exit;
    		}else{
    			echo $str='{"statusCode":"300","message":"录入竞彩赛果信息失败！"}';
    			exit;
    		}
    		
    	}else{
    		echo $str='{"statusCode":"300","message":"'.$lotttime.'('.$ballid.')赛果信息已存在，请重新添加赛果！"}';
    		exit;
    	}
    	
    }
    
    function add_act_lc(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	$model=D('LcResult');
    	$weekday = date('w',strtotime($lotttime));
    	if($weekday == '0'){
    		$weekday = '7';
    	}
    	$ballids = $weekday.$ballid;
    	$map = array('ballid' => $ballids, 'lotttime' => $lotttime);
    	$res = $model->where($map)->select();
    	if(count($res) == 0){
    		$rdata = array(
    				'lotteryid' => '0',
    				'ballid' => $ballids,
    				'lotttime' => $lotttime,
    				'match_starttime' => $matchtime,
    				'source' => $sourcejc,
    				'addtime' => date('Y-m-d H:i:s'),
    				'status' => '-1'
    		);
    		$result = $model->add($rdata);
    		if($result){
    			echo $str='{"statusCode":"200","message":"录入篮彩赛果信息成功！"}';
    			exit;
    		}else{
    			echo $str='{"statusCode":"300","message":"录入篮彩赛果信息失败！"}';
    			exit;
    		}
    	
    	}else{
    		echo $str='{"statusCode":"300","message":"'.$lotttime.'('.$ballid.')赛果信息已存在，请重新添加赛果！"}';
    		exit;
    	}
    
    }
    
    /**
     * 暂停派奖/开启派奖
     **/
    function pauseAward(){
    	extract($_REQUEST, EXTR_OVERWRITE);
    	if($act == "jc"){
    		$model = D('Result');
    	}else if($act == "lc"){
    		$model = D('LcResult');
    	}
    	$arr = explode('_', $recid);
    	$lotttime = $arr[0];
    	$ballid = $arr[1];
    	if($rstatus == '4'){
    		//开启派奖
    		$map = array(
    				'lotttime'=> $lotttime,
    				'ballid'=> $ballid
    		);
    		$rdata = array('status' => '-1');
    		$result = $model->where($map)->save($rdata);
    		if($result){
    			echo $str = '{"statusCode":"200","message":"修改开奖信息成功！"}';
    			exit;
    		}else{
    			echo $str='{"statusCode":"300","message":"修改失败或者没有更新任何内容！"}';
    			exit;
    		}
    	}else{
    		//暂停派奖
    		$map = array(
    			'lotttime'=> $lotttime,
    			'ballid'=> $ballid
    		);
    		$rdata = array('status' => '4');
    		$result = $model->where($map)->save($rdata);
    		if($result){
    			echo $str = '{"statusCode":"200","message":"修改开奖信息成功！"}';
    			exit;
    		}else{
    			echo $str='{"statusCode":"300","message":"修改失败或者没有更新任何内容！"}';
    			exit;
    		}
    	}
    }
    
}
