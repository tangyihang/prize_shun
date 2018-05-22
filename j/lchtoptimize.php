<?php
if(!defined('LOTTERY_ROOT') || !isset($smarty)){
	exit('Access Denied');
}
define('FILE_TOOR',substr(dirname(__FILE__), 0, -8).DIRECTORY_SEPARATOR);
define('EASY_ROOT', substr(dirname(__FILE__), 0, -16).DIRECTORY_SEPARATOR.'home'.DIRECTORY_SEPARATOR.'php_client'.DIRECTORY_SEPARATOR);
require_once EASY_ROOT.'lib/easy.client.manager.php';
require_once EASY_ROOT.'lib/cache/functions.php';
require_once EASY_ROOT.'lib/cache/functions.php';
include_once(substr(dirname(__FILE__), 0, -16).DIRECTORY_SEPARATOR.'lottery/config.php');
error_reporting(E_PARSE);
$clientmanager = new easyxmlmanager('');
$manager = new easyxmlmanager();
$yuhua_lotteryid      = isset($_POST['yuhua_lotteryid'])?intval($_POST['yuhua_lotteryid']):'208';//彩种
$yuhua_schememoney    = isset($_POST['yuhua_schememoney'])?intval($_POST['yuhua_schememoney']):'';//投注钱数
$yuhua_lotterynumbers = isset($_POST['yuhua_lotterynumbers'])?trim($_POST['yuhua_lotterynumbers']):'';//投注注数
$yuhua_gate           = isset($_POST['yuhua_gate'])?trim($_POST['yuhua_gate']):'';//过关方式
$yuhua_lotterycode    = isset($_POST['yuhua_lotterycode'])?trim($_POST['yuhua_lotterycode']):'';//投注信息yuhua_lotterymode
$yuhua_lotterymode    = isset($_POST['yuhua_lotterymode'])?trim($_POST['yuhua_lotterymode']):'';//最大场次和最小场次
$lotteryvalue = isset($_REQUEST['TotalMoney'])?trim($_REQUEST['TotalMoney']):'';//用户填写的钱数
$submit_act   = isset($_REQUEST['submit_act'])?trim($_REQUEST['submit_act']):'';//投注倍数
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
   $vp = str_replace('0','',$vl);
   $aaaa = getCombinationToString($ticketinfous['date'],$vp-1);
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
   $peilv = $peilv*2;
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
	$str.=round($peilv, 2);
    $allmoney[] = round($peilv*$zhushusinfo, 2);
	$str.='</td><td class="noteBetObj">'.$zhushusinfo.'</td><td><div class="noteBox"><a class="float_l symboljian" href="javascript:void(0)" data-type="-1" hidefocus>-</a>';
    $str.='<span class="float_l  note_input noteValObj ';
	$str.='">'.round($peilv*$zhushusinfo, 2).'</span><input type="text" value="'.round($peilv*$zhushusinfo, 2).'" size="8" style="display:none;" class="float_l note_input2 " data-oldVal="'.round($peilv, 2).'"><a class="float_l symbol updateNoteObj" href="javascript:void(0);" data-type="1" hidefocus>+</a></div></td></tr>';
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
$smarty->display(LOTTERY_ROOT.'plugins/templates/lchtoptimize.html');


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
	$bqcarr216214 = array('0'=>'负','3'=>'胜');
    $bqcarr215 = array('1'=>'大分','2'=>'小分');
    $bqcarr217 = array('1'=>'胜[1-5]','2'=>'胜[6-10]','3'=>'胜[11-15]','4'=>'胜[16-20]','5'=>'胜[21-25]','6'=>'胜[26+]','7'=>'负[1-5]','8'=>'负[6-10]','9'=>'负[11-15]','10'=>'负[16-20]','11'=>'负[21-25]','12'=>'负[26+]');
	 if($wanfo == '216' || $wanfo == '214'){
	   $str=$bqcarr216214[$type];
	 }elseif($wanfo == '215'){
	   $str=$bqcarr215[$type];
	 }elseif($wanfo == '217'){
	   $str=$bqcarr217[$type];
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
    foreach($temp as $key=>$val) //216^20131106-301(0_1.65,3_1.84)
     {
          preg_match('/[0-9]{3}\^[0-9]{8}-[0-9]{3}/',$val,$arr);//获取209^130923-001
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
	   $ielement['lotteryid']= '218';
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
          $temp['teamid'] =$val['element']['lotttime'].'-'.$temp['ballid'];
         // $temp['iscode'] = getTeamSp($temp['lotttime'],$temp['ballid'],$yuhua_lotteryid);
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

			  if($wanfainfo[0] == '216')
			  {
			      preg_match('/\((.*?)\)/',$wanfainfo[1],$peem);
				  $temptouzhu[216]['info']=$peem[1];
				  $temptouzhu[216]['infovalue'] = disposetouzhuvalue($peem[1],'216');
			  }elseif($wanfainfo[0] == '214'){
			      preg_match('/\((.*?)\)/',$wanfainfo[1],$peem);
				  $temptouzhu[214]['info']=$peem[1];
				  $temptouzhu[214]['infovalue'] = disposetouzhuvalue($peem[1],'214');
				  preg_match('/\d{8}-\d{3}/',$wanfainfo[1],$codearrdd);
				  $timebill = explode('-',$codearrdd[0]); 
				  $temptouzhu[214]['fen'] = getTeamSp($timebill[0],$timebill[1],'214');
			  }elseif($wanfainfo[0] == '215'){
			      preg_match('/\((.*?)\)/',$wanfainfo[1],$peem);
				  $temptouzhu[215]['info']=$peem[1];
				  $temptouzhu[215]['infovalue'] = disposetouzhuvalue($peem[1],'215');
				   preg_match('/\d{8}-\d{3}/',$wanfainfo[1],$codearrdd);
				  $timebill = explode('-',$codearrdd[0]); 
				  $temptouzhu[215]['fen'] = getTeamSp($timebill[0],$timebill[1],'215');
			  }elseif($wanfainfo[0] == '217'){
			      preg_match('/\((.*?)\)/',$wanfainfo[1],$peem);
				  $temptouzhu[217]['info']=$peem[1];
				  $temptouzhu[217]['infovalue'] = disposetouzhuvalue($peem[1],'217');
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
 $bqcarr216214 = array('0'=>'主负','3'=>'主胜');
 $bqcarr215 = array('1'=>'大分','2'=>'小分');
 $bqcarr217 = array('1'=>'主胜[1-5]','2'=>'主胜[6-10]','3'=>'主胜[11-15]','4'=>'主胜[16-20]','5'=>'主胜[21-25]','6'=>'主胜[26+]','7'=>'主负[1-5]','8'=>'主负[6-10]','9'=>'主负[11-15]','10'=>'主负[16-20]','11'=>'主负[21-25]','12'=>'主负[26+]'); 
  
	  $tempteam=array();
      $temp = array();
      $temp = explode(',',$date);
	  rsort($temp);
	  foreach($temp as $k=>$v)
		{
	         $strarray =array();
             $strarray = explode('_',$v);
			 $str = '';
			 $infoteam = array();
			 $spvalue = $strarray[1];
			 //if($strarray[0]==3){$str = '主胜';}elseif($strarray[0]==0){$str = '主负';}
			 $infoteam['touzhutype'] = $strarray[0];
             $infoteam['sp'] =  $spvalue;
			 if($lotteryid == '214' || $lotteryid == '216'){
                 $infoteam['touzhuval'] = $bqcarr216214[$strarray[0]];
			   }elseif($lotteryid == '215'){
			    $infoteam['touzhuval'] = $bqcarr215[$strarray[0]];
			   }elseif($lotteryid == '217'){
			   $infoteam['touzhuval'] = $bqcarr217[$strarray[0]];
			   }
             $infoteam['lotteryid'] = $lotteryid;
         $tempteam[] = $infoteam;
	    }
	   return $tempteam;
	
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
/****************************************************************************************
 *获取动态的sp值
 *球队的日期 lotttime  str 20131031 
 *球队编号   ballid   int  302
 *彩种编号   yuhua_lotteryid   int  106
 ****************************************************************************************
*/
/*function getTeamSp($lotttime,$ballid,$yuhua_lotteryid){
	$temp= array();
	$cache_path = FILE_TOOR.'zdj/jc/cachefiles';//缓存文件地址
	$cache_name = '__Lottery_LC_guding'.$yuhua_lotteryid.'_SP_Cache__';//得到文件的名称
	$getSpList = cachedata($cache_name,'','','',$cache_path);//得到SPVAL
    foreach($getSpList as $key=>$val)
	{
	   if($val['LOTTTIME'] == $lotttime && $val['TEAMID']== $ballid)
		{
		      if($yuhua_lotteryid == '215'){
			     $temp = $val['SP_Z'];
			  }elseif($yuhua_lotteryid == '214'){
			    $temp= $val['SP_RSF3'];
			  }
		      
		}
	
	}
    return $temp;
}
*/
//更改getteamSp 内容 2014 01 28  username：lsd
function getTeamSp($lotttime,$ballid,$yuhua_lotteryid){
	$temp= array();
	$cache_path = FILE_TOOR.'zdj/jc/cachefiles/lc';//缓存文件地址
	$cache_name = 'lottery_'.$yuhua_lotteryid;//得到文件的名称
	$getSpList = cachedata($cache_name,'','','',$cache_path);//得到SPVAL
	$lotttimes=substr($lotttime,0,4)."-".substr($lotttime,4,2)."-".substr($lotttime,6,2);
    foreach($getSpList as $key=>$val)
	{
	   if($val['lotttime'] == $lotttimes && $val['ballid']== $ballid)
		{
		      if($yuhua_lotteryid == '215'){
				 $temp_sp=unserialize($val['spinfo']);
			     $temp = $temp_sp['z'];
			  }elseif($yuhua_lotteryid == '214'){
				 $temp_sp=unserialize($val['spinfo']);
			     $temp= $temp_sp['rf'];
			  }
		      
		}
	
	}
    return $temp;
}
?>
