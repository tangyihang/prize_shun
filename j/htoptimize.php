<?php
if(!defined('LOTTERY_ROOT') || !isset($smarty)){
	exit('Access Denied');
}
define('EASY_ROOT', substr(dirname(__FILE__), 0, -16).DIRECTORY_SEPARATOR.'home'.DIRECTORY_SEPARATOR.'php_client'.DIRECTORY_SEPARATOR);
require_once EASY_ROOT.'lib/easy.client.manager.php';
require_once EASY_ROOT.'lib/cache/functions.php';
include_once(substr(dirname(__FILE__), 0, -16).DIRECTORY_SEPARATOR.'lottery/config.php');
$clientmanager = new easyxmlmanager('');
$manager = new easyxmlmanager();
$yuhua_lotteryid      = isset($_POST['yuhua_lotteryid'])?intval($_POST['yuhua_lotteryid']):'208';//彩种
$yuhua_schememoney    = isset($_POST['yuhua_schememoney'])?intval($_POST['yuhua_schememoney']):'';//投注钱数
$yuhua_lotterynumbers = isset($_POST['yuhua_lotterynumbers'])?trim($_POST['yuhua_lotterynumbers']):'';//投注注数
$yuhua_gate           = isset($_POST['yuhua_gate'])?trim($_POST['yuhua_gate']):'';//过关方式
$yuhua_lotterycode    = isset($_POST['yuhua_lotterycode'])?trim($_POST['yuhua_lotterycode']):'';//投注信息yuhua_lotterymode
$yuhua_lotterymode    = isset($_POST['yuhua_lotterymode'])?trim($_POST['yuhua_lotterymode']):'';//最大场次和最小场次
$lotteryvalue = isset($_REQUEST['TotalMoney'])?trim($_REQUEST['TotalMoney']):'';//用户填写的钱数
$submit_act   = isset($_REQUEST['submit_act'])?trim($_REQUEST['submit_act']):''; //投注倍数
if(empty($yuhua_gate) || empty($yuhua_lotterycode))
{
 echo "Access request failed";
 exit;
}
if(empty($lotteryvalue)){$lotteryvalue=$yuhua_schememoney;}
//获得赛事的名称
preg_match_all('/[0-9]{6}-[0-9]{3}/',$yuhua_lotterycode,$temparr);
$teamcode = array_unique($temparr[0]);
$touzhuarray = array();
$touzhuarray = explode(';',$yuhua_lotterycode);
$teaminfo = getTeamInfo($teamcode,$yuhua_lotteryid,$touzhuarray);//左边显示的投注信息
/*拆票*/
$ticketinfous = chaipiaoinfo($yuhua_lotterycode);//得到投注信息例如[0] => 209^130923-001_1[1] => 209^130923-001_3[2] => 209^130923-002_0
$childtype = explode('^',$yuhua_gate);//Array ( [0] => 102 [1] => 103 )
$ticketsp = $ticketinfous['spvalue'];//每个投注的sp[209^130923-001_1] => 3.40[209^130923-001_3] => 2.60[209^130923-002_0] => 2.85[209^130923-002_1] => 3.15
$hebingyouhua = array();
foreach($childtype as $kl=>$vl)
{
   $vp = str_replace('10','',$vl);
   $aaaa = getCombinationToString($ticketinfous['date'],$vp);
   $hebingyouhua = array_merge($hebingyouhua,$aaaa);//得到票有多少张
}
//获取每张票的主队和客队
$teamnameinfo = teaminfonames($teaminfo);
//奖金优化开始

$youhuanum = $lotteryvalue/2;
$date = getcalculatespval($ticketsp,$hebingyouhua);
$ticketspvalue = $date['sparray'];//获取每张票的SPval  renyispval
$youhuanum = $lotteryvalue/2;
$renyispvalue = '3840';
$renyikey     = key($date['renyispval']);
$middelvalue =0;//计算出spvalue的中间值
foreach($ticketspvalue as $k=>$v)
{
$middelvalue = (float)$middelvalue+(float)$renyispvalue/(float)$v;
}
$pingjunvalue = (float)$youhuanum/(float)$middelvalue;
//计算每一票应该有多少注
$tempvalue =array();//存放计算出来的注数的数组
$zongzhu = 0;
foreach($ticketspvalue as $ka=>$va)
{
  $temp=array();$zhushus='';
  $temp['spvalue']=$va;
  $zhushus = (float)$pingjunvalue*(float)$renyispvalue/(float)$va;
  if($zhushus<1){
    $temp['zhusui']=1;  
  }else{
    $temp['zhusui'] = round($zhushus);
  }
  $temp['money']=$temp['zhusui']*$va;
  $zongzhu = (int)$zongzhu + (int)$temp['zhusui'];
  $tempvalue[$ka] =$temp; 
}
//补票开始
if(($zongzhu == $youhuanum) || ($zongzhu < $youhuanum))
{
  $date = getbipiaoval($tempvalue,$zongzhu,$youhuanum);//补票以后的值
}else{
  $date = getbipiaojianval($tempvalue,$zongzhu,$youhuanum);//补票以后的值
}

//得到一注的奖金进行排序
$afteryouhuamoney = array();
foreach($date as $kl=>$vl)
{
  $afteryouhuamoney[$kl] = $vl['spvalue'];
}
//如果是一注的时候进行排序
if($submit_act=='bore') //博热优化
{ 
  /*找到计算后最小钱的个数*/
	$aftercalculate = array(); //存放计算出来的所有的钱数
	$afterspvalue = array();  //存放计算出来的spvalue
	foreach($date as $k=>$v)
	{
	  $aftercalculate[]=$v['money'];
	  $afterspvalue[$k]=$v['spvalue'];
	}
	//排序money找到最小值
	sort($aftercalculate);
	asort($afterspvalue);
    $afterminspvalue = key($afterspvalue);
	$afterminmoney =$aftercalculate[0];
    if($afterminmoney > $lotteryvalue)
	 {   
		 $borezhu = "";
	     $borezhu = $date[$afterminspvalue];
	     unset($date[$afterminspvalue]);
		 $afterzongshu = 0; 
		 foreach($date as $key=>$val)
		 {   
			 $aftertemp = '';
		     $aftertemp = ceil($lotteryvalue/$val['spvalue']);
			 if($aftertemp < 1 ){$aftertemp=1;}
             $date[$key]['zhusui'] = $aftertemp;
             $date[$key]['money']  = round($aftertemp*$val['spvalue'],2);
			 $afterzongshu+=$aftertemp;
		 }
       	$sengxia = $youhuanum-$afterzongshu;
        $borezhu['zhusui'] = $sengxia;
        $borezhu['money'] = round($sengxia*$borezhu['spvalue'],2);
		$tempboresa = array($afterminspvalue=>$borezhu);
		$date = array_merge($date,$tempboresa);
	 }

 asort($afteryouhuamoney); 
}elseif($submit_act=='boleng')//搏冷优化
{
 /*找到计算后最小钱的个数*/
	$aftercalculate = array(); //存放计算出来的所有的钱数
	$afterspvalue = array();  //存放计算出来的spvalue
	foreach($date as $k=>$v)
	{
	  $aftercalculate[]=$v['money'];
	  $afterspvalue[$k]=$v['spvalue'];
	}
	//排序money找到最小值
	sort($aftercalculate);
	arsort($afterspvalue);
    $afterminspvalue = key($afterspvalue);
	$afterminmoney =$aftercalculate[0];
    if($afterminmoney > $lotteryvalue)
	 {   
		 $borezhu = "";
	     $borezhu = $date[$afterminspvalue];
	     unset($date[$afterminspvalue]);
		 $afterzongshu = 0; 
		 foreach($date as $key=>$val)
		 {   
			 $aftertemp = '';
		     $aftertemp = ceil($lotteryvalue/$val['spvalue']);
			 if($aftertemp < 1 ){$aftertemp=1;}
             $date[$key]['zhusui'] = $aftertemp;
             $date[$key]['money']  = round($aftertemp*$val['spvalue'],2);
			 $afterzongshu+=$aftertemp;
		 }
       	$sengxia = 	$youhuanum-$afterzongshu;
        $borezhu['zhusui'] = $sengxia;
        $borezhu['money'] = round($sengxia*$borezhu['spvalue'],2);
		$tempboresa = array($afterminspvalue=>$borezhu);
		$date = array_merge($tempboresa,$date);
	 }
arsort($afteryouhuamoney);
}else{
asort($afteryouhuamoney);
}
/*根据博热和搏冷排序*/
//奖金优化结束
//获取票的详细信息
$ticksinfod = array();
foreach($hebingyouhua as $ks=>$vs)
{ 
  $temp=array();
  $a = explode(',',$vs);
  foreach($a as $kk=>$vv)
	{
	$temp[] =getticketinfo($vv,$teamnameinfo,$ticketsp); 
    }
   $ticksinfod[$vs] = $temp;
}
//博热优化把SPval的最小值放到开头
$paixuarray = array();
foreach($afteryouhuamoney as $key=>$val)
	{
       $paixuarray[$key]= $ticksinfod[$key];
    }
unset($ticksinfod);
$ticksinfod = $paixuarray;

//排序

$appnum=1;
$str='';
$num = 1;
foreach($ticksinfod as $k=>$v)
{
  $temps = array();
  $peilv =1;
  $teamVal="";
  $passType = "";
  $bisoshi='';
  $ks='';
  foreach($v as $ke=>$va){
    $teamVal.= $va['teamid'].';';
    $bisoshi.= $va['biaoshi'].';';
    $temps[] = $va['spvalue'];
    $ks.=$va['teamid'].',';
  }
  foreach ($temps as $ki=>$vi)
  {
    $peilv = $peilv*$vi;
  }
   $peilv = round2($peilv*2,2);
   $ks=rtrim($ks,',');
   //if(empty($submit_act)){$zhushusinfo = 1;}else{$zhushusinfo = $date[$ks]['zhusui'];}
   $zhushusinfo = $date[$ks]['zhusui'];
    $str.='<tr class="noteTrObj" data-teamVal="'.rtrim($teamVal,';').'" data-noteVal="'.round($peilv, 2).'" data-totalVal="'.round($peilv*$zhushusinfo, 2).'"  data-betVal="'.$zhushusinfo.'" data-passType=""><td>'.$num.'</td><td class="tal">';
   foreach($v as $ke=>$va)
	{
     $str.='<a class="sortObj" href="javascript:void(0);" data-val="'.$va['teamid'].'" hidefocus>'.$va['hteam'].'</a>[<span class="gray3 sortObj" data-val="'.$va['teamid'].'">';
     $str.=$va['typeinfo'];
     if($va['lotteryid']=='210'){
        $str.='<em class="font_green">('.$va['isconcede'].')</em>';
     }
	 $str.='</span>]×';
    }
    $str.='2=';
	$str.=$peilv;
    $allmoney[] = $peilv*$zhushusinfo;
	$str.='</td><td class="noteBetObj">'.$zhushusinfo.'</td><td><div class="noteBox"><a class="float_l symboljian" href="javascript:void(0)" data-type="-1" hidefocus>-</a>';
    $str.='<span class="float_l  note_input noteValObj ';
	$str.='">'.$peilv*$zhushusinfo.'</span><input type="text" maxlength="8" value="'.$peilv*$zhushusinfo.'" size="8" style="display:none;" class="float_l note_input2 " data-oldVal="'.$peilv.'"><a class="float_l symbol updateNoteObj" href="javascript:void(0);" data-type="1" hidefocus>+</a></div></td></tr>';
 $num++;
}

//获取票的详细信息
if(empty($submit_act) || $submit_act == 'pingjun'){$pingjunyouhu = 'yhmenuBtnSed';}else{$pingjunyouhu = '';}
$smarty->assign('botype',count($childtype));
$smarty->assign('jingjinyusuan',count($touzhuarray));
$smarty->assign('pingjunyouhu',$pingjunyouhu);
$smarty->assign('str',$str);
$smarty->assign('yuhua_schememoney',$lotteryvalue);//钱数
$smarty->assign('childtype',$yuhua_gate);//左边显示的投注信息
$smarty->assign('teaminfo',$teaminfo);//左边显示的投注信息$ticketsp
$smarty->assign('ticketsp',json_encode($ticketsp));//左边显示的投注信息$ticketsp
$smarty->assign('postticksinfod',json_encode($ticksinfod));//左边显示的投注信息
$smarty->assign('postteamdetail',json_encode($teaminfo));//左边显示的投注信息$yuhua_gate$teaminfo   $ticksinfod yuhua_lotterymode
$smarty->assign('codes',$yuhua_lotterycode);
$smarty->assign('yuhua_lotterymode',$yuhua_lotterymode);
$smarty->assign('submit_act',$submit_act);
$smarty->display(LOTTERY_ROOT.'plugins/templates/htoptimize.html');


/*补票函数*/
function getbipiaoval($tempvalue,$zongzhu,$youhuanum){
  $monyarray=array();
  $spvaluearray = array();
  $zhusui=array();
  $newarr = array();
  $maxmoney='';
  $tempnewarr=array();
  $k='';
if($zongzhu == $youhuanum){
	return $tempvalue;
	exit;
	}
  foreach($tempvalue as $k=>$v)
	{
      $monyarray[]=$v['money'];
	  $spvaluearray[$k] = $v['spvalue'];
      $zhusui[$k]=$v['zhusui'];
      $newarr[$k] = (float)$v['money']+(float)$v['spvalue'];//都加上一注
    }
  //求数组的最大值
  rsort($monyarray);
  $maxmoney = $monyarray[0];
  foreach($newarr as $ka=>$va)
	{
      $newarr[$ka] = (float)$newarr[$ka] - (float)$maxmoney;
    }
  $tempnewarr = $newarr;
  asort($newarr);
  reset($newarr);
  $k = key($newarr);
  foreach($tempnewarr as $ki=>$vi)
	{ 
	      if($ki == $k)
			{
		       $tempvalue[$ki]['zhusui'] = (int)$tempvalue[$ki]['zhusui']+1;
               $tempvalue[$ki]['money'] = (float)$tempvalue[$ki]['money'] +(float)$tempvalue[$ki]['spvalue'];
			 }
      }
	unset($monyarray);
	unset($newarr);
	unset($spvaluearray);
	unset($zhusui);
   $zongzhu = (int)$zongzhu +1;
   return getbipiaoval($tempvalue,$zongzhu,$youhuanum);
}

//在票四舍五入如多的情况下
function getbipiaojianval($tempvalue,$zongzhu,$youhuanum)
{ 
  $monyarray=array();
  $spvaluearray = array();
  $zhusui=array();
  $newarr = array();
  $maxmoney='';
  $tempnewarr=array();
  $k='';
   if($zongzhu == $youhuanum){
	return $tempvalue;
	exit;
	}
  foreach($tempvalue as $k=>$v)
	{
      $monyarray[]=$v['money'];
	  $spvaluearray[$k] = $v['spvalue'];
      $zhusui[$k]=$v['zhusui'];
      if($v['zhusui'] >1)
      {
        $newarr[$k] = (float)$v['money']-(float)$v['spvalue'];//都减去一注
      }
      
    }
    sort($monyarray);
	$minmoney = $monyarray[0];
    foreach($newarr as $ka=>$va)
	  {
      $newarr[$ka] = (float)$newarr[$ka] - (float)$minmoney;
      }
  $tempnewarr = $newarr;
  arsort($newarr);
  reset($newarr);
  $k = key($newarr);
  foreach($tempnewarr as $ki=>$vi)
	{ 
	      if($ki == $k)
			{
		       $tempvalue[$ki]['zhusui'] = (int)$tempvalue[$ki]['zhusui']-1;
               $tempvalue[$ki]['money'] = (float)$tempvalue[$ki]['money']-(float)$tempvalue[$ki]['spvalue'];
			 }
      }
	unset($monyarray);
	unset($newarr);
	unset($spvaluearray);
	unset($zhusui);
    $zongzhu = (int)$zongzhu - 1;
    return getbipiaojianval($tempvalue,$zongzhu,$youhuanum);
}
function getticketinfo($vv,$teamnameinfo,$ticketsp)
{  
    $bqcarr213 = array('33'=>'胜胜','30'=>'胜负','31'=>'胜平','11'=>'平平','10'=>'平负','13'=>'平胜','03'=>'负胜','01'=>'负平','00'=>'负负','90'=>'胜其他','99'=>'平其他','09'=>'负其他');
    $bqcarr211 = array('10'=>'1:0','20'=>'2:0','21'=>'2:1','30'=>'3:0','31'=>'3:1','32'=>'3:2','40'=>'4:0','41'=>'4:1','42'=>'4:2','50'=>'5:0','51'=>'5:1','52'=>'5:2','00'=>'0:0','11'=>'1:1','22'=>'2:2','33'=>'3:3','01'=>'0:1','02'=>'0:2','12'=>'1:2','03'=>'0:3','13'=>'1:3','23'=>'2:3','04'=>'0:4','14'=>'1:4','24'=>'2:4','05'=>'0:5','15'=>'1:5','25'=>'2:5','90'=>'胜其他','99'=>'平其他','09'=>'负其他','0'=>'0球','1'=>'1球','2'=>'2球','3'=>'3球','4'=>'4球','5'=>'5球','6'=>'6球','7'=>'7+球'); 
    $temp  = array();
    $temps  = array();
    $str="";
    $temp  = split('[\^_]',$vv);
    $wanfo =$temp[0];
    $teamnum = $temp[1];
    $type=$temp[2];
    $temps['hteam'] = $teamnameinfo[$teamnum]['hteam'];
    $temps['vteam'] = $teamnameinfo[$teamnum]['vteam'];
    $temps['teamid'] = $vv;
    $temps['isconcede'] = $teamnameinfo[$teamnum]['isconcede'];
    $temps['spvalue'] = $ticketsp[$vv];
    $temps['lotteryid'] = $wanfo;
    $temps['biaoshi'] = $wanfo.'-'.$type;
    if($wanfo == '209' || $wanfo == '210'){
    if($type==3){$str = '主';}elseif($type==1){$str = '平';}elseif($type==0){$str = '客';}
     }elseif($wanfo == '213'){
      $str = $bqcarr213[$type]; 
     }else{
      $str = $bqcarr211[$type];    
     }
    $temps['typeinfo'] = $str;
    return $temps;
}
//获取每张票的主队和客队
function teaminfonames($teaminfo)
{
    $temp=array();
    if(is_array($teaminfo)){
        foreach($teaminfo as $k=>$v)
         {
           $temps = array();
           $temps['hteam']=$v['hteam'];
           $temps['vteam']=$v['vteam'];
           $temps['isconcede']=$v['isconcede'];
           $temp[$v['teamid']] = $temps; 
         }
    }
    return $temp;
}

/*总共投注的玩法有多少投注*/
function chaipiaoinfo($yuhua_lotterycode){
    if(empty($yuhua_lotterycode)){return false;}
    $temp = array();
    $date = array();
    $sparray= array();
    $renyispvalue = array();
    $temp = explode(';',$yuhua_lotterycode);
    foreach($temp as $key=>$val)//209^130923-001(1_3.40,3_2.60)
     {
          preg_match('/[0-9]{3}\^[0-9]{6}-[0-9]{3}/',$val,$arr);//获取209^130923-001
          preg_match('/\((.*?)\)/',$val,$peem);
          $shushu = explode(',',$peem[1]);
          foreach($shushu as $ke=>$va)
          {
             $wanfa = explode('_',$va);
             $date[] = $arr[0].'_'.$wanfa[0];
             $sparray[$arr[0].'_'.$wanfa[0]]=$wanfa[1];
          }
     } 
  $date = array_unique($date);
  $ss = array('date'=>$date,'spvalue'=>$sparray);
  return $ss;
}

//获取球队信息
function getTeamInfo($teamcode,$yuhua_lotteryid,$touzhuarray){
	global $manager;
	$teamcodes = array();
   if(is_array($teamcode)){
	   sort($teamcode);
      foreach($teamcode as $key=>$val){
	   $header=null;
       $ielement=null;
	   $newarr= array();
       $newarr = explode('-',$val);
       $header = array('transactiontype'=>40003);
	   $ielement['lotteryid']= '210';
	   $ielement['lotttime']='20'.$newarr[0];
	   $ielement['ballid']= $newarr[1];
	   $manager->execute($header,$ielement,'',$_SERVER['REMOTE_ADDR'],'WEB');
	   $teamlist = $manager->getelements();
	   $teamcodes[]=$teamlist;
	   }
	    $teamifos = setteaminfo($teamcodes,$touzhuarray);
        return $teamifos;
	  }else{
	       $header=null;
		   $ielement=null;
		   $header =  array('transactiontype'=>40003);
		   $ielement['lotteryid']=$yuhua_lotteryid;
		   $ielement['ballid']= $teamcode;
		   $manager->execute($header,$ielement,'',$_SERVER['REMOTE_ADDR'],'WEB');
		   $teamlist = $manager->getelements();
		   return $teamlist;
	 
	}
}

/*格式化求出的值*/
function setteaminfo($teamcodes,$touzhuarray)
{ 
   $temp=array();
   $temparray=array();
   $weekarray=array('','周一','周二','周三','周四','周五','周六','周日');
   if(is_array($teamcodes))
	{
      foreach($teamcodes as $key=>$val)
		{
		  $str='';
		  $teamcont = array();
	      $temp['hteam'] = $val['element']['hteam'];
	      $temp['vteam'] = $val['element']['vteam'];
	      $temp['lotttime'] = $val['element']['lotttime'];
	      $temp['ballid'] =   $val['element']['ballid'];
	      $temp['lottweek'] =   $val['element']['lottweek'];
	      $temp['isconcede'] =   $val['element']['isconcede'];
	      $temp['lotttimename'] = $weekarray[$temp['lottweek']].$val['element']['ballid'];
          $temp['lotttimeval'] = substr($val['element']['lotttime'],2);
          $temp['teamid'] = substr($val['element']['lotttime'],2).'-'.$temp['ballid'];
		  foreach($touzhuarray as $ke=>$va){
		    $tam = '';
	        $tam = strpos($va,$temp['teamid']);
			if($tam)
			  {
			    $teamcont[] = $va;
			  }
		    }
		   $temp['touzhiinfo'] = $teamcont;
		   $temparray[] = $temp;
		}
		$temparrays = disposetouzhiinfo($temparray);
       return $temparrays;
    }else{
	  return false;
	}

}

//处理投注信息
function disposetouzhiinfo($temparray){
  if(is_array($temparray)){
    foreach($temparray as $key=>$val)
	  {
	     $temp = array();
		 $temptouzhu = array();
		 $temp = $val['touzhiinfo'];
		 foreach($temp as $ke=>$va)
		  {
		      $wanfainfo = array();
              $wanfainfo = explode('^',$va);
			  if($wanfainfo[0] == '209')
			  {
			      preg_match('/\((.*?)\)/',$wanfainfo[1],$peem);
				  $temptouzhu[209]['info']=$peem[1];
				  $temptouzhu[209]['infovalue'] = disposetouzhuvalue($peem[1],'209');
			  }elseif($wanfainfo[0] == '210'){
			      preg_match('/\((.*?)\)/',$wanfainfo[1],$peem);
				  $temptouzhu[210]['info']=$peem[1];
				  $temptouzhu[210]['infovalue'] = disposetouzhuvalue($peem[1],'210');
			  }elseif($wanfainfo[0] == '211'){
			      preg_match('/\((.*?)\)/',$wanfainfo[1],$peem);
				  $temptouzhu[211]['info']=$peem[1];
				  $temptouzhu[211]['infovalue'] = disposetouzhuvalue($peem[1],'211');
			  }elseif($wanfainfo[0] == '212'){
			      preg_match('/\((.*?)\)/',$wanfainfo[1],$peem);
				  $temptouzhu[212]['info']=$peem[1];
				  $temptouzhu[212]['infovalue'] = disposetouzhuvalue($peem[1],'212');
			  }elseif($wanfainfo[0] == '213'){
			      preg_match('/\((.*?)\)/',$wanfainfo[1],$peem);
				  $temptouzhu[213]['info']=$peem[1];
				  $temptouzhu[213]['infovalue'] = disposetouzhuvalue($peem[1],'213');
			  }
		  }

		$temparray[$key]['touzhiinfos'] = $temptouzhu;
	  }
     return $temparray;
   }else{
   return false;
  }
}
//格式化投注的信息值
function disposetouzhuvalue($date,$lotteryid)
{ 
 $bqcarr213 = array('33'=>'胜胜','30'=>'胜负','31'=>'胜平','11'=>'平平','10'=>'平负','03'=>'负胜','13'=>'平胜','01'=>'负平','00'=>'负负','90'=>'胜其他','99'=>'平其他','09'=>'负其他');
 $bqcarr211 = array('10'=>'1:0','20'=>'2:0','21'=>'2:1','30'=>'3:0','31'=>'3:1','32'=>'3:2','40'=>'4:0','41'=>'4:1','42'=>'4:2','50'=>'5:0','51'=>'5:1','52'=>'5:2','00'=>'0:0','11'=>'1:1','22'=>'2:2','33'=>'3:3','01'=>'0:1','02'=>'0:2','12'=>'1:2','03'=>'0:3','13'=>'1:3','23'=>'2:3','04'=>'0:4','14'=>'1:4','24'=>'2:4','05'=>'0:5','15'=>'1:5','25'=>'2:5','90'=>'胜其他','99'=>'平其他','09'=>'负其他','0'=>'0球','1'=>'1球','2'=>'2球','3'=>'3球','4'=>'4球','5'=>'5球','6'=>'6球','7'=>'7+球'); 
  if($lotteryid == '209' || $lotteryid == '210')
	{ 
	  $tempteam=array();
      $temp = array();
      $temp = explode(',',$date);
	  foreach($temp as $k=>$v)
		{
	         $strarray =array();
             $strarray = explode('_',$v);
			 $str = '';
			 $infoteam = array();
			 $spvalue = $strarray[1];
			 if($strarray[0]==3){$str = '主';}elseif($strarray[0]==1){$str = '平';}elseif($strarray[0]==0){$str = '客';}
			 $infoteam['touzhutype'] = $strarray[0];
             $infoteam['sp'] =  $spvalue;
             $infoteam['touzhuval'] = $str;
             $infoteam['lotteryid'] = $lotteryid;
         $tempteam[] = $infoteam;
	    }
	   return $tempteam;
	}else{
	   $tempteam=array();
      $temp = array();
      $temp = explode(',',$date);
	  foreach($temp as $k=>$v)
		{
	         $strarray =array();
             $strarray = explode('_',$v);
			 $str = '';
			 $infoteam = array();
			 $spvalue = $strarray[1];
             if($lotteryid == '213'){
             $str = $bqcarr213[$strarray[0]]; 
             }else{
             $str = $bqcarr211[$strarray[0]];   
             }
			 $infoteam['touzhutype'] = $strarray[0];
			 $infoteam['sp'] =  $spvalue;
             $infoteam['touzhuval'] = $str;
             $infoteam['lotteryid'] = $lotteryid;
         $tempteam[] = $infoteam;
	    }
	   return $tempteam;
	}
}
//组合
function getCombinationToString($arr,$m)
{
  $result = array();
  if($m ==1){return $arr;}
  if($m == count($arr))
    {
     preg_match_all('/[0-9]{1,7}-[0-9]{1,3}/', implode(',',$arr),$tempa2);
     $size1 = count($tempa2[0]);
     $size2 = count(array_unique($tempa2[0]));
     if($size1 ==  $size2){
         $res =  implode(',',$arr);
	     $result[] = $res;
	     return $result;
        }else{
	   return $result;
	    }
    }

   $temp_firstelement = $arr[0];
   unset($arr[0]);
   $arr = array_values($arr);
   $temp_list1 = getCombinationToString($arr, ($m-1));
   foreach ($temp_list1 as $s)
	{
	   preg_match_all('/[0-9]{1,7}-[0-9]{1,3}/',$temp_firstelement,$tempa2);
       $tempstr = $tempa2[0][0];
	   $checks = $s;
	   $checks = '('.$checks;
	   if(strpos($checks,$tempstr)){}else{
	      $s = $temp_firstelement.','.$s;
	      $result[] = $s;
	   }
	}
 unset($temp_list1);
 $temp_list2 = getCombinationToString($arr, $m);
 foreach ($temp_list2 as $s){$result[] = $s;} 
 unset($temp_list2);
 return $result;
}
/*得到计算出来的SPVAL*/
function getcalculatespval($ticketsp,$hebingyouhua)
{  
    $temparray = array();
   if(is_array($hebingyouhua)){
    foreach($hebingyouhua as $k=>$v)
      {
        $temp=array();
        $temp=explode(',',$v);
        $strcon = 1;
        $renyi = array();
        foreach($temp as $k1=>$v1)
        {
          $strcon = $strcon*$ticketsp[$v1]; 
        }
        $strcon = $strcon*2;
        $temparray[$v] = $strcon;
        $renyi[$v]=$strcon;
      }
    
    return array('sparray'=>$temparray,'renyispval'=>$renyi);
  }
}
function round2($num,$precision){
    $pow = pow(10,$precision);
    if(  (floor($num * $pow * 10) % 5 == 0) && (floor( $num * $pow * 10) == $num * $pow * 10) && (floor($num * $pow) % 2 ==0) ){//舍去位为5 && 舍去位后无数字 && 舍去位前一位是偶数    =》 不进一
        return floor($num * $pow)/$pow;
    }else{//四舍五入
        return round($num,$precision);
    }
}
?>
