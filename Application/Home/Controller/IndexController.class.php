<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	  public function _initialize(){
	  	 if(!session('username')){
	  		   $this->redirect('/Home/User/login');
	  	}
	  }
    public function index(){
	
    	$User=M('User');
        $this->display();
    }
    public function ticketedit(){
      $state=$_REQUEST['state'];
      $id=$_REQUEST['recid'];
      $model=D('Ticket');
      $where=array('Id'=>$id);
      $data=array('State'=>$state);
      if($result=$model->where($where)->save($data)){
      	  $Dao =M();
      	  $dbname="ticketprintout_".date('Ym',time());
      	  $selectstr="AgentId,OrdenId,OrdenId_Msg,State,IsPost,ReceiveTime,EndTime,PrintOutTime,SendTime,SchemeCode,SchemeCodeToPrint,Times,Amount,NoteCount,PrintFileName,ConfirmFileName,LotteryPeriod,TypeID,TypeName,PassMode,PassModeName,AnalysisContent,SourceContent,State_Set,FormatData,PC_Num,PrintSchemeCode,PrintTimes,PrintAmount,PrintNo,PassWord,PrintOdds,PrintPeriod,TicketInfo";
		  $sql="INSERT INTO ".$dbname." (".$selectstr.") SELECT ".$selectstr." FROM ticketprintout_error WHERE Id=".$id;
		  $Dao->execute($sql);
		  $model->where($where)->delete();
          $str='{"statusCode":"200","message":"修改成功","navTabId":"result","rel":"","callbackType":"","forwardUrl":"","confirmMsg":""}';
	      echo $str;
      }else{
          $str='{"statusCode":"300","message":"操作错误","navTabId":"result","rel":"","callbackType":"","forwardUrl":"","confirmMsg":""}';
	      echo $str;
      }
	  exit;
    }
    //
    public function edit()
    {
    	$recid=$_REQUEST['recid'];
    	$arr=explode('_',$recid);
    	$lotttime=$arr[0];
    	$ballid=$arr[1];
    	$model=D('Result');
    	
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
    	//print_r($Rlist);
    	$this->assign('lotttime',$lotttime);
    	$this->assign('ballid',$ballid);
    	$this->assign('Rlist',$Rlist);
    	$this->display('editresult');
    }
    
    public function view(){
    	$recid=$_REQUEST['recid'];
    	$arr=explode('_',$recid);
    	$lotttime=$arr[0];
    	$ballid=$arr[1];
    	$model=D('Ticket');
    	
    	$where=array(
    	  'Id'=> $recid,
    	);
    	$Tlist=$model->where($where)->select();
        $AnalysisContent=$Tlist[0]['AnalysisContent'];
    	$this->assign('AnalysisContent',$AnalysisContent);
    	$this->display('viewticket');
    }
    public function ticketerror(){
	   $model=D('Ticket');
	   $where=array(
    	  'lotttime'=> $lotttime,
    	  'ballid' => $ballid
    	);
	   $Tlist=$model->select();
	   $this->assign('Ticketlist',$Tlist);
	   $this->display('ticketerror');
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
    		$newlist[] =array('lotteryid'=>'','lotttime'=>$lotttime,'ballid'=>$ballid,'source'=>$value,'half_score'=>$half_arr[$key],'full_score'=> $full_arr[$key],'status'=>'-1','isright'=> $isright_arr[$key]);
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
	      		 'status'=>'-1'
      		 );
      		 if($result=$model->where($where)->save($data)){
	      		 	 $resarr=array();
	      		 	 $resarr['statusCode']=200;
	      		 	 $resarr['message']='修改赛果成功！';
	      		 	 $resarr['navTabId']='result';
	      		 	 $resarr['rel']='editresult';
	      		 	 $resarr['callbackType']='';
	      		 	 $resarr['forwardUrl']='';
	      		 	 $resarr['confirmMsg']='';
      		 	   echo json_encode($resarr);
      		 	  exit;
      		 }else{
      		 	  $str='{
								"statusCode":"300",
								"message":"修改失败或者没有更新任何内容",
								"navTabId":"",
								"rel":"",
								"callbackType":"closeCurrent",
								"forwardUrl":"",
								"confirmMsg":""
		           }';
		           echo $str;
		           exit;
      		 }
      		}else{
    		    $this->saveToDB($val);
    		  }
    	  } 
    	}
    	if($noright==0){
    		  $resarr=array();
	      		 	 $resarr['statusCode']=300;
	      		 	 $resarr['message']='未勾选需要修改的项目';
	      		 	 $resarr['navTabId']='';
	      		 	 $resarr['rel']='editresult';
	      		 	 $resarr['callbackType']='closeCurrent';
      		 	   echo json_encode($resarr);
      		 	  exit;
    	}
    }
    
    public function saveToDB($data){
     	 
       $Result=M('Result');
       if($data['lotttime']=='' || $data['ballid']=='' || $data['status']=='' ){
          return false;
          exit;
       }else{
             $ielement=array(
				          'lotteryid' => $data['lotteryid'],
				          'lotttime' => $data['lotttime'],
				          'ballid' =>  $data['ballid'],
				          'source' => $data['source'],
				          'half_score' => $data['half_score'],
				          'full_score' => $data['full_score'],   
           );
           
           
            if($Result->add($ielement)){
               $str='{
								"statusCode":"200",
								"message":"\u64cd\u4f5c\u6210\u529f",
								"navTabId":"",
								"rel":"page1",
								"callbackType":"",
								"forwardUrl":"",
								"confirmMsg":"",
						   }';
						   echo $str;
						   exit;
            }else{
	             $str='{
								"statusCode":"300",
								"message":"修改失败或者没有更新任何内容",
								"navTabId":"",
								"rel":"",
								"callbackType":"closeCurrent",
								"forwardUrl":"",
								"confirmMsg":""
		           }';
		           echo $str;
		           exit;
            }
       }
    }
    public function logout(){
         session(null);
		 header("Expires: Mon, 26 Jul 1970 05:00:00 GMT"); 
		 $this->success('退出成功',__APP__.'/Home/User/login');
		 //$this->redirect('/Home/User/login');
    }
}