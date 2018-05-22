<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {
  public function _initialize(){
	  	 if(!session('username')){
	  	 	 echo "<p style='line-height:36px;padding-left:100px;font-size:16px;'>您尚未登录，请<a href='/'><b>登录</b></a></p>";
			 exit;
			 //$this->redirect(__APP__.'/Home/User/login');
	  	 }
  }	
  public function saveToDB($model,$data,$type='basketball'){
   	  //竞彩篮球入库
   	  if($type=='basketball')
   	   {
          if($data['lotttime']=='' || $data['ballid']=='' || $data['status']=='' ){
                return false;
                exit;
            }else{
             $ielement=array(
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
				          'status' => $data['status'],
            );
            if($model->add($ielement)){
               echo "success<br>";
            }else{
               echo "error<br>";
            }
        }
      }
      //竞彩足球入库
       if($type=='football')
   	   {
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
				          'match_starttime' => $data['match_starttime'],
				          'result' => $data['result'],
				          'addtime' => date('Y-m-d H:i:s',time()),
				          'status' => $data['status'],
             );
            if($model->add($ielement)){
               echo "success<br>";
            }else{
               echo "error<br>";
            }
       }
     }
  }
  
  /*
  * 方法功能
  *把 周三001 转换转换成 3001 并推算出当前比赛的对阵时间
  *如：当前 3001 当天时间是2014-11-07 周五 即推算出3001的对阵时间为2014-11-05
  */
  
  public function ballidto($str){
  	$cnweekarr=array(0=>'周日',1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五',6=>'周六');
  	$cnweek=preg_replace('@\d+@','',$str);

  	
  	foreach($cnweekarr as $key=> $val){
  	  if($val==$cnweek){
  	     $week=$key;	
  	  }	
  	}
  	$ballid=$week.preg_replace('@^.*(\d{3})$@','\\1',$str);
  	//这里只能推算已经结束的比赛
    if(date('w',time())-$week >=0){
       $days=date('w',time())-$week;
    }
    if(date('w',time())-$week <0){
       $days=date('w',time())+7-$week;
    }
    $lotttime=date('Y-m-d',time()-$days*24*3600);
   
    return array($lotttime,$ballid);
  }
}