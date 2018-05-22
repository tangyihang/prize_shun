<?php
if(!defined('LOTTERY_ROOT') || !isset($smarty)){
	exit('Access Denied');
}
error_reporting(0);
define('FILE_TOOR',substr(dirname(__FILE__), 0, -8).DIRECTORY_SEPARATOR);
define('EASY_ROOT', substr(dirname(__FILE__), 0, -16).DIRECTORY_SEPARATOR.'home'.DIRECTORY_SEPARATOR.'php_client'.DIRECTORY_SEPARATOR);
require_once EASY_ROOT.'lib/easy.client.manager.php';
require_once EASY_ROOT.'lib/cache/functions.php';
include_once(substr(dirname(__FILE__), 0, -16).DIRECTORY_SEPARATOR.'lottery/config.php');
$yuhua_lotteryid      = isset($_REQUEST['submit_lotteryid'])?intval($_REQUEST['submit_lotteryid']):intval($_REQUEST['LotteryId']);//彩种
$yuhua_schememoney    = isset($_REQUEST['submit_lotteryvalue'])?intval($_REQUEST['submit_lotteryvalue']):'';//投注钱数
$yuhua_lotterynumbers = isset($_REQUEST['submit_appnum'])?trim($_REQUEST['submit_appnum']):'';//投注注数
$yuhua_lotterycode    = isset($_REQUEST['submit_code'])?trim($_REQUEST['submit_code']):'';//投注信息yuhua_lotterymode
$submit_issueflag    = isset($_REQUEST['submit_issueflag'])?trim($_REQUEST['submit_issueflag']):'';//原版的投注信息yuhua_lotterymode
$lotteryvalue = isset($_REQUEST['TotalMoney'])?trim($_REQUEST['TotalMoney']):$yuhua_schememoney;//用户填写的钱数
$submit_act   = isset($_REQUEST['submit_act'])?trim($_REQUEST['submit_act']):'pingjun'; //优化类型
if(empty($yuhua_lotterycode) && empty($yuhua_lotteryid)){
echo "";
exit;
}
$templotterycode = explode('#',$yuhua_lotterycode);
$code = $templotterycode[1];//投注记录
/*格式化过关方式把2,3,4=102,103,104*/
$gate = $templotterycode[0];//过关方式 
$gatenew1 = preg_split('/[:%]+/',$submit_issueflag);//Array ( [0] => 7000 [1] => 7006,7005,7004,7003 [2] => 20131106-301(0),20131106-302(0))
$gatenew = str_replace(',','^',$gatenew1[1]);
$clientmanager = new easyxmlmanager('');
$manager = new easyxmlmanager();
$teaminfo = getTeamInfo($code,$yuhua_lotteryid);//左边投注信息
$tmpmatchinfo = touzhuticket($code); //Array ( [20131031-301] => 0;3 [20131031-302] => 0;3 [20131031-303] => 0 [20131031-304] => 3 )
$ticketinfous = touzhuticketInfo($tmpmatchinfo);//[0] => 130913-001_0 [1] => 130913-001_1 [2] => 130913-001_3
$grouptickets = groupticket($ticketinfous,$gate);//获取票的组合
/*计算票数开始*/
$youhuanum = $lotteryvalue/2;
//获取票的详细信息
$ticksinfod = array();
$ticketspvalue = array();
foreach($grouptickets as $ks=>$vs)
{ 
  $temp=array();
  $afterjisuansp = 1;
  $a = explode(',',$vs);
  foreach($a as $kk=>$vv)
	{
	 $tempgetticketinfo = array();
     $tempgetticketinfo = getticketinfo($vv,$teaminfo);//得到每张票的球队名称
	 $temp[] = $tempgetticketinfo;
	 $afterjisuansp *=$tempgetticketinfo['spvalue'];
    }
   $ticksinfod[$vs] = $temp;
   $ticketspvalue[$vs] = $afterjisuansp*2; //得到每张票计算后的SPVAL数组
   $renyispvalue = $afterjisuansp*2;//得到任意的spval
}
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
  $temp=array();
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
unset($temp);
//补票开始
if(($zongzhu == $youhuanum) || ($zongzhu < $youhuanum))
{
   $date = getbipiaoval($tempvalue,$zongzhu,$youhuanum);//补票以后的值
}else{
   $date = getbipiaojianval($tempvalue,$zongzhu,$youhuanum);//补票以后的值
}

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
             $date[$key]['zhusui'] = $aftertemp;
             $date[$key]['money']  = round($aftertemp*$val['spvalue'],2);
			 $afterzongshu+=$aftertemp;
		 }
       	$sengxia = 	$youhuanum-$afterzongshu;
        $borezhu['zhusui'] = $sengxia;
        $borezhu['money'] = round($sengxia*$borezhu['spvalue'],2);
		$tempboresa = array($afterminspvalue=>$borezhu);
		$date = array_merge($date,$tempboresa);
	 }
 asort($ticketspvalue);  
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
arsort($ticketspvalue);  
}else{
asort($ticketspvalue); 
}
//按照不同的优化方式去排序
$paixuarray = array();
foreach($ticketspvalue as $key=>$val)
	{
       $paixuarray[$key]= $ticksinfod[$key];
    }
unset($ticksinfod);
$ticksinfod = $paixuarray;
/*投注票的处理开始*/
$str='';
$allmoney=array();
$appnum=1;
$num = 1;
foreach($ticksinfod as $k=>$v)
{
  $temps = array();
  $peilv =1;
  $teamVal="";
  $passType = "";
  foreach($v as $ke=>$va){
    //$teamVal.= substr($va['ticketinfo'],2).';';
	$teamVal.= $va['ticketinfo'].';';
	$passType.=$va['shengping'].'-';
    $temps[] = $va['spvalue'];
  }
   foreach ($temps as $ki=>$vi)
	{
	 $peilv = $peilv*$vi;
	}
     $peilv = $peilv*2;
	$appnum = $date[$k]['zhusui'];
    //if(empty($submit_act)){$appnum = 1;}else{$appnum = $date[$k]['zhusui'];}
   $str.='<tr class="noteTrObj" data-teamVal="'.rtrim($teamVal,';').'" data-noteVal="'.round($peilv, 2).'" data-totalVal="'.round($peilv*$appnum, 2).'"  data-betVal="'.$appnum.'" data-passType="'.rtrim($passType,'_').'"><td>'.$num.'</td><td class="tal">';
   foreach($v as $ke=>$va)
	 {
	   $va['hteam'] = $yuhua_lotteryid == '215' ? $va['teamweek']:$va['hteam'];
       $str.='<a class="sortObj" href="javascript:void(0);" data-val="'.$va['ticketinfo'].'" hidefocus>'.$va['hteam'].'</a>[<span class="gray3 sortObj" data-val="'.$va['ticketinfo'].'">';
	    if($yuhua_lotteryid == '215'){
	       if($va['shengping'] == '1'){$str.='大分';}elseif($va['shengping'] == '2'){$str.='小分';}
	      }else{
	       if($va['shengping'] == '1'){$str.='平';}elseif($va['shengping'] == '3'){$str.='胜';}elseif($va['shengping'] == '0'){$str.='负';}
	     }
		$str.='</span>]×';
     }
	$str.='2=';
	$str.=round($peilv, 2);
	$str.='</td><td class="noteBetObj">'.$appnum.'</td><td><div class="noteBox"><a class="float_l symboljian" href="javascript:void(0)" data-type="-1" hidefocus>-</a>';
    $str.='<span class="float_l  note_input noteValObj ';
	$str.='">'.round($peilv*$appnum, 2).'</span><input type="text" value="'.round($peilv*$appnum, 2).'" size="8" style="display:none;" class="float_l note_input2 " data-oldVal="'.round($peilv, 2).'"><a class="float_l symbol updateNoteObj" href="javascript:void(0);" data-type="1" hidefocus>+</a></div></td></tr>';
    $num++;
}
$zhichibo = count(explode(',',$gate));
$smarty->assign('submit_act',$submit_act);
$smarty->assign('lotteryvalue',$lotteryvalue);
$smarty->assign('zhichibo',$zhichibo);
$smarty->assign('ticksinfod',$str);
$smarty->assign('teamdetails',$teaminfo);//球队信息
$smarty->assign('lotteryid',$yuhua_lotteryid); //彩种ID
$smarty->assign('codes',$yuhua_lotterycode);  //优化投注信息
$smarty->assign('childtype',$gatenew);  //过关方式
$smarty->assign('submit_date',$code);  //投注信息
$smarty->assign('submit_issueflag',$submit_issueflag);  //投注信息
$smarty->display(LOTTERY_ROOT.'plugins/templates/jcoptimize.html');
/**************************************************************************************** 
 *组票以后根据组票信息找到球队信息
 * 
 *       
 * hebingyouhua 
 ****************************************************************************************
*/
function getticketinfo($ticket,$ticketinfo)
{   
    $piaohao="";$touzhuspf="";$temppiaohao = "";
    $temppiaohao = explode("_",$ticket);
    $piaohao = $temppiaohao[0];
    $touzhuspf = $temppiaohao[1];
	return array('hteam'=>$ticketinfo[$piaohao]['hteam'],'vteam'=>$ticketinfo[$piaohao]['vteam'],'shengping'=>$touzhuspf,'spvalue'=>$ticketinfo[$piaohao]['sp'.$touzhuspf],'ticketinfo'=>$ticket,'teamweek'=>$ticketinfo[$piaohao]['lotttimename']);

}


/**************************************************************************************** 
 *格式化投注信息
 *code str 20131031-301(0;3),20131031-302(0;3),20131031-303(0),20131031-304(3)
 * return array tmpmatchinfo = Array ( [20131031-301] => 0;3 [20131031-302] => 0;3 [20131031-303] => 0 [20131031-304] => 3 )
 ****************************************************************************************
*/
function touzhuticket($code)
{  
   $tmpmatchinfo = array();
   $tempcode= explode(',',$code);
   foreach($tempcode as $ke =>$va)
    {  
      preg_match('/\d{8}-\d{3}/',$va,$macthtouzhu);
      preg_match('/\((.*?)\)/',$va,$peem);
	  $tmpmatchinfo[$macthtouzhu[0]] = $peem[1];   //[130913-001] => (3,1,0) [130913-002] => (3,1,0) [130913-003] => (3,1,0) 
    }
  return  $tmpmatchinfo;
}

/****************************************************************************************
 *拆票函数
 * 格式化以后的投注信息 $tmpmatchinfo 130913-001] => (3,1,0) [130913-002] => (3,1,0) [130913-003] => (3,1,0) 
 * 返回值 return temp  [0] => 130913-001_0 [1] => 130913-001_1 [2] => 130913-001_3
 ****************************************************************************************
**/
function touzhuticketInfo($tmpmatchinfo){
	if(!is_array($tmpmatchinfo)){return false;}
  foreach($tmpmatchinfo as $k=>$v){
     if(strpos($v,'0') !== false)
	  {
	   $temp[] = $k.'_0';
	  }
	  if(strpos($v,'1')!== false)
	  {
	   $temp[] = $k.'_1';
	  }
	  if(strpos($v,'2')!== false)
	  {
	   $temp[] = $k.'_2';
	  }
	  if(strpos($v,'3') !== false)
	  {
	   $temp[] = $k.'_3';
	  }
  }
  return $temp;

}

/**************************************************************************************** 
 *根据自己的需要进行组票
 * ticketinfous  array  需要组合票的数组 例如：[0] => 130913-001_0 [1] => 130913-001_1 [2] => 130913-001_3
 * gate          str    过关方式         例如：1,2,3 也就是几串几的意思
 * hebingyouhua  array  组合之后的票    [0] => 130913-001_0:130913-001_3 [1] => 130913-001_3:130913-001_1
 ****************************************************************************************
*/
function groupticket($ticketinfous,$gate){
	$hebingyouhua = array();
	$gatearray = explode(',',$gate);
	sort($gatearray);
	foreach($gatearray as $kl=>$vl)
	  {
	    $aaaa = getCombinationToString($ticketinfous,$vl);
	    $hebingyouhua = array_merge($hebingyouhua,$aaaa);
	  }
	  return $hebingyouhua;
}


/****************************************************************************************
 *获取球队信息
 *投注信息 teamcodes   str  20131031-301(0;3),20131031-302(0;3),20131031-303(0),20131031-304(3)
 *彩种ID   yuhua_lotteryid   int  216
 ****************************************************************************************
*/
function getTeamInfo($teamcodes,$yuhua_lotteryid){
	global $manager;
	$teamcode = explode(',',$teamcodes);
	$teamcodes = array();
	$teamlist = array();
   if(is_array($teamcode)){
	   sort($teamcode);
      foreach($teamcode as $key=>$val){
	   $header=null;
       $ielement=null;
	   $newarr= array();
       $newarr = explode('-',$val);
       $header = array('transactiontype'=>40003);
	   $ielement['lotteryid']= $yuhua_lotteryid;
	   $ielement['lotttime']=$newarr[0];
	   $ielement['ballid']= substr($newarr[1],'0','3');
	   $manager->execute($header,$ielement,'',$_SERVER['REMOTE_ADDR'],'WEB');
	   $element = $manager->getelements();
	   preg_match('/\((.*?)\)/',$val,$peem);
	   $element['element']['betcode'] = $peem[1];
       $teamcodes[]=$element;
	   }
	    $teamifos = setteaminfo($teamcodes,$yuhua_lotteryid);//格式化球队信息
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

/****************************************************************************************
 *格式化从数据库中取出的球队信息
 *球队信息 teamcodes  array  array(element=>('hteam')) 主队客队编号 
 *彩种ID   yuhua_lotteryid   int  216
 ****************************************************************************************
*/
function setteaminfo($teamcodes,$yuhua_lotteryid)
{ 
   $temp=array();
   $bet = array();
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
	      $temp['betcode'] =   $val['element']['betcode'];
	      $temp['lotttimename'] = $weekarray[$temp['lottweek']].$val['element']['ballid'];
          $temp['lotttimeval'] = $val['element']['lotttime'];
          $temp['teamid'] = $val['element']['lotttime'].'-'.$temp['ballid'];
		  if(strpos($temp['betcode'],'3') !== false || strpos($temp['betcode'],'1') !== false){$temp['spvaluecheck3'] = 1;}else{$temp['spvaluecheck3'] = 0;}
		  if(strpos($temp['betcode'],'0') !== false || strpos($temp['betcode'],'2') !== false){$temp['spvaluecheck0'] = 1;}else{$temp['spvaluecheck0'] = 0;}
	      //获取动态的SPval
		  $tempspval = getTeamSp($temp['lotttime'],$temp['ballid'],$yuhua_lotteryid);//获取动态饿SPVAL
		  $res = array_merge($temp,$tempspval);
		  $newkey = $temp['lotttime'].'-'.$temp['ballid'];
          $temparray[$newkey]=$res;
		  unset($temp);
		}
       return $temparray;
    }else{
	  return false;
	}

}

/****************************************************************************************
 *获取动态的sp值
 *球队的日期 lotttime  str 20131031 
 *球队编号   ballid   int  302
 *彩种编号   yuhua_lotteryid   int  106
 ****************************************************************************************
*/
function getTeamSp($lotttime,$ballid,$yuhua_lotteryid){
	$temp= array();
	$cache_path = FILE_TOOR.'zdj/jc/cachefiles';//缓存文件地址
	$cache_name = '__Lottery_LC_guding'.$yuhua_lotteryid.'_SP_Cache__';//得到文件的名称
	$getSpList = cachedata($cache_name,'','','',$cache_path);//得到SPVAL
    foreach($getSpList as $key=>$val)
	{
	   if($val['LOTTTIME'] == $lotttime && $val['TEAMID']== $ballid)
		{
		  if($yuhua_lotteryid == '216'){
		     $temp['sp3'] = $val['SP_SF1'];
		     $temp['sp0'] = $val['SP_SF2'];
		    }elseif($yuhua_lotteryid == '214'){
			  $temp['sp3'] = $val['SP_RSF1'];
		      $temp['sp0'] = $val['SP_RSF2'];
		      $temp['rangfen'] = $val['SP_RSF3'];
			}elseif($yuhua_lotteryid == '215'){
		      $temp['rangfen'] = $val['SP_Z'];
			  $temp['sp1'] = $val['SP_D'];
		      $temp['sp2'] = $val['SP_X'];
			}
		 
	    }
	
	}
    return $temp;
}

/*
 ***************************************************************************************
 *排列组合组票函数
 * $arr      array  例子：[0] => 130913-001_0 [1] => 130913-001_1 [2] => 130913-001_3
 * $m        int    例子 1-99整数 就是需要几个组合到一张票就传几
 ***************************************************************************************
 */
//组合函数
function getCombinationToString($arr,$m)
{
   $result = array();
   if ($m ==1){return $arr;}

  if($m == count($arr)){
	preg_match_all('/[0-9]{1,7}-[0-9]{1,3}/', implode(',',$arr),$tempa2);
	$size1 = count($tempa2[0]);
	$size2 = count(array_unique($tempa2[0]));
	if($size1 ==  $size2)
	   {
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
	preg_match_all('/[0-9]{1,8}-[0-9]{1,3}/',$temp_firstelement,$tempa2);
	//print_r($tempa2);
	$tempstr = $tempa2[0][0];
	$checks = $s;
	$checks = '('.$checks;
	if(strpos($checks,$tempstr))
	  {

	  }else
	  {
	  $s = $temp_firstelement.','.$s;
	  $result[] = $s;
	  }
	}
	unset($temp_list1);

    $temp_list2 = getCombinationToString($arr, $m);
	foreach ($temp_list2 as $s)
	{
	$result[] = $s;
	} 
	unset($temp_list2);

	return $result;
}

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
               $tempvalue[$ki]['money'] = (float)$tempvalue[$ki]['money'] +(float)$tempvalue[$ki]['spvalue'];
			 }
      }
	unset($monyarray);
	unset($newarr);
	unset($spvaluearray);
	unset($zhusui);
    $zongzhu = (int)$zongzhu - 1;
    return getbipiaojianval($tempvalue,$zongzhu,$youhuanum);
}
?>
