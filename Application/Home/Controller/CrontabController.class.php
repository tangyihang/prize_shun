<?php
namespace Home\Controller;
use Think\Controller;
class CrontabController extends Controller {
	public function _initialize(){
		;
	}
    public function index()
    {
        
    }
    
	/**
	 * 定时查询派奖状态
	 * @param string 
	 */
	function cron_resultStatus(){
		$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB1'));
		$model=D('Kaijiang');
		$map['kj_date'] = array('like',date('Y-m-d',time()).'%');
		//$map['kj_date'] = array('like','2015-11-04%');
		$map['send_flag']  = array('neq','33');
		$array=$model->where($map)->select();
		$cz_arr = array('1'=>'118','2'=>'116','3'=>'117','283'=>'100','284'=>'102','282'=>'103','281'=>'106','324'=>'108','441'=>'109','326'=>'110','325'=>'111');
		foreach($array as $key=>$val){
			$lotteryid = $cz_arr[$val['cz_id']];
			$flag = str_split($val['send_flag']);
			if(count($flag) > 1){
				$awardArr = array();
				foreach($flag as $k => $v){
					if($v != '3'){
						$result = $this->interface_60004($k, $lotteryid, $val['kj_issue']);
						$pstatus = $result['elemets']['element']['status'];
						$map2 = array(
							'cz_id' => $val['cz_id'],
							'kj_issue' => $val['kj_issue']
							);
						$kjinfo=$model->where($map2)->select();
						$send_flag = str_split($kjinfo[0]['send_flag']);
						$savedata['sent_time'] = date('Y-m-d H:i:s');
						if($k == 0 && $pstatus == '7'){
							$savedata['send_flag'] = '3'.$send_flag[1];
							$model->where($map2)->save($savedata);
						}else if($k == 1 && $pstatus == '7'){
							$savedata['send_flag'] = $send_flag[0].'3';
							$model->where($map2)->save($savedata);
						}
					}
				}
			}
		}
	}
	
	/**
	 * 查询彩种开奖公告_60004
	 * @param string 
	 */
	function interface_60004($flagk, $lotteryid, $lotteryissue){
		if($flagk == 0){
			$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB1'));
		}else if($flagk == 1){
			$HttpObj=new \Org\Net\HttpClient(C('INTER_LIB2'));
		}else{
			return false;
		}
		$header=array(
			'transactiontype'=>'60004',
			'messengerid'=>time('YmdHis',time()).uniqid(6),
			'agenterid'=>'10000001',
		);
		$ielement=array(
			'lotteryid'     =>  $lotteryid,
			'lotteryissue'  =>  $lotteryissue,
			'startdate'     =>  '',
			        'enddate'       =>  '',
			        'status'        =>  '',
			        'sortmode'      =>  '',
			        'sortparameter' => 'lotteryid',
			        'pageindex'     => '0',
			        'pagesize'      => '10',
			        'pagetotal'     => ''
		);
		$HttpObj->send($header,$ielement,'10000001','','web');
		$result = array(
			'errorcode' => $HttpObj->getoelementvalue('errorcode'),
			'errormsg' => $HttpObj->getoelementvalue('errormsg'),
			'elemets' => $HttpObj->getelements()
		);
		return $result;
	}
    
}
