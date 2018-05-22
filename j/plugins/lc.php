<?php
//$yuhua_lotteryid      = isset($_POST['yuhua_lotteryid'])?intval($_POST['yuhua_lotteryid']):'208';//彩种
$money    = isset($_POST['money']) ? intval($_POST['money']) : '';//投注钱数
$multiple = isset($_POST['multiple']) ? trim($_POST['multiple']) : 0;//投注倍数
$childtype           = isset($_POST['user_select'])?trim($_POST['user_select']):'';//过关方式
$lotterycode    = isset($_POST['combination']) ? trim($_POST['combination']):'';//投注信息yuhua_lotterymode
//$type= isset($_POST['type']) ? $_POST['type'] : "pingjun";
//$type    = isset($_POST['yuhua_lotterymode'])?trim($_POST['yuhua_lotterymode']):'';//最大场次和最小场次
if($_REQUEST['childtype']){
	$childtype           = $_REQUEST['childtype'];
}
$lotteryvalue = isset($_REQUEST['TotalMoney']) ? trim($_REQUEST['TotalMoney']):'';//用户填写的钱数
if($lotteryvalue){
	$money=$lotteryvalue;
}
$type   = isset($_REQUEST['submit_act'])?trim($_REQUEST['submit_act']):'pingjun'; //投注倍数
//pingjun";  //bore boleng,pinjun
if($_REQUEST['yuhua_lotterycode']){
  $lotterycode=$_REQUEST['yuhua_lotterycode'];
}
if(!$lotterycode){
  echo "投注内容不能为空";
  exit;
}
if(!$childtype){
  echo "串关方式不能为空";
  exit;
}
//$lotteryvalue=$totalMoney;
//$yuhua_schememoney=$totalMoney;
$Opt=new optimize($lotterycode,$childtype,$type,$lotteryvalue);
$Opt->make();
$Opt->result();
$teaminfo=$Opt->teaminfo;
if(empty($submit_act) || $submit_act == 'pingjun'){$pingjunyouhu = 'yhmenuBtnSed';}else{$pingjunyouhu = '';}
$base->assign('botype',count($childtype));
$base->assign('type',$Opt->type);
$base->assign('jingjinyusuan',count($touzhuarray));
$base->assign('pingjunyouhu',$pingjunyouhu);
$base->assign('yuhua_schememoney',$money);//钱数
$base->assign('childtype',$childtype);//左边显示的投注信息
$base->assign('teaminfo',$teaminfo);//左边显示的投注信息$ticketsp
$base->assign('ticketsp',json_encode($ticketsp));//左边显示的投注信息$ticketsp
$base->assign('postticksinfod',json_encode($ticksinfod));//左边显示的投注信息
$base->assign('postteamdetail',json_encode($teaminfo));//左边显示的投注信息$yuhua_gate$teaminfo   $ticksinfod yuhua_lotterymode
$base->assign('codes',$lotterycode);
$base->assign('yuhua_lotterymode',$yuhua_lotterymode);
$base->assign('submit_act',$submit_act);
$base->assign('str',$Opt->tablestr);
$base->display(OPT_ROOT.'/optimize.html');

?>
