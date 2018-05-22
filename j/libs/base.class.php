<?php
class base
{
	function getToken($params) {
		
		if (!is_array($params)) return '';
		ksort($params);
		$string = '';
		foreach ($params as $key=>$value) {
			if ($key == 'token') {
				continue;
			}
			$string .= $key . $value;
		}
		$appId = 4;
		$appKey = 'b9705bc6df1e6b9';
		$string .= $appKey;
		return substr(md5($string), 8, 16);
	}

	function getTeam($teamArr,$sport){
       
       $team=array();
       $weekarr=array('1'=>'周一','2'=>'周二','3'=>'周三','4'=>'周四','5'=>'周五','6'=>'周六','7'=>'周日');
	   $spf_arr=array('3'=>"胜",'1'=>"平",'0'=>"负",'33'=>"胜胜",'31'=>"胜平",'13'=>"平胜",'11'=>"平平",'13'=>"平胜",'03'=>"负胜",'01'=>"负平",'00'=>"负负",
	   's0'=>"0球",'s1'=>"1球",'s2'=>"2球",'s3'=>"3球",'s4'=>"4球",'s5'=>"5球",'s6'=>"6球",'s7'=>"7球",
	   '0100'=>"1:0",'-1-3'=>"胜其他",'-1-0'=>"平其他",'-1-1'=>"负其他",'0100'=>'1:0','0200'=>'2:0','0201'=>'2:1','0300'=>'3:0','0301'=>'3:1','0302'=>'3:2','0400'=>'4:0','0401'=>'4:1','0402'=>'4:2','0500'=>'5:0','0501'=>'5:1','0502'=>'5:2','0000'=>'0:0','0101'=>'1:1','0202'=>'2:2','0303'=>'3:3','0001'=>'0:1','0002'=>'0:2','0102'=>'1:2','0003'=>'0:3','0103'=>'1:3','0203'=>'2:3','0004'=>'0:4','0104'=>'1:4','0204'=>'2:4','0005'=>'0:5','0105'=>'1:5','0205'=>'2:5'
	   );
	  
       foreach($teamArr as $key=>$val){
          $id=preg_replace('@\d{3}\^([0-9]{5,})\(.*\)@', '\\1', $val);
		  $resp=$this->send($id,$sport);
          $arr=json_decode($resp,true);
		  $w=preg_replace('@(\d{1})\d{3}@', '\\1', $arr['msg']['error_code']['num']);
		  $team[$id]['week']=$weekarr[$w];
          $team[$id]['ballid']=preg_replace('@(\d{1})(\d{3})@', '\\2', $arr['msg']['num']);
          $team[$id]['hteam']=$arr['msg']['error_code']['h_cn']; //$arr['msg']['h_cn'];
          $team[$id]['vteam']= $arr['msg']['error_code']['a_cn'];
		  $temstr=preg_replace('@.*\((.*)\)@', '\\1', $val);
		  $tem1=explode(',',$temstr);
		  $contentstr="";
		  for($i=0;$i<count($tem1);$i++){
			  $temp2=explode('_',$tem1[$i]);
			  $contentstr.=$spf_arr[$temp2[0]]."[".$temp2[1]."] ";
		  }
		  $team[$id]['content'].=$contentstr;
          $this->send($id);
       }
       return $team;
	}
	//
	function send($matchId,$sport='fb'){
		$time=time();
		$params=array(
		  'time' => $time,
		  'matchId' => $matchId,
		  'sport' => $sport,
		  'appId' => 4
		);
		$token=$this->getToken($params);
		$url="http://www.zhiying365.com/api/getMatchInfo.php?token=".$token."&time=".$time."&matchId=".$matchId."&sport=fb&appId=4";
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;

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
	   return $this->getbipiaoval($tempvalue,$zongzhu,$youhuanum);
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
	    return $this->getbipiaojianval($tempvalue,$zongzhu,$youhuanum);
	}
	//
	function getticketinfo($vv,$teamnameinfo,$ticketsp)
	{  
	    $bqcarr213 = array('33'=>'胜胜','30'=>'胜负','31'=>'胜平','11'=>'平平','10'=>'平负','13'=>'平胜','03'=>'负胜','01'=>'负平','00'=>'负负','90'=>'胜其他','99'=>'平其他','09'=>'负其他');
	    $bqcarr211 = array('0100'=>'1:0','0200'=>'2:0','0201'=>'2:1','0300'=>'3:0','0301'=>'3:1','0302'=>'3:2','0400'=>'4:0','0401'=>'4:1','0402'=>'4:2','0500'=>'5:0','0501'=>'5:1','0502'=>'5:2','0000'=>'0:0','0101'=>'1:1','0202'=>'2:2','0303'=>'3:3','0001'=>'0:1','0002'=>'0:2','0102'=>'1:2','0003'=>'0:3','0103'=>'1:3','0203'=>'2:3','0004'=>'0:4','0104'=>'1:4','0204'=>'2:4','0005'=>'0:5','0105'=>'1:5','0205'=>'2:5','-1-3'=>"胜其他",'-1-0'=>"平其他",'-1-1'=>"负其他",'s0'=>'0球','s1'=>'1球','s2'=>'s2球','s3'=>'3球','s4'=>'4球','s5'=>'5球','s6'=>'6球','s7'=>'7+球'); 
	    $temp  = array();
	    $temps  = array();
	    $str="";
	    $temp  = split('[\^_]',$vv);
	    $wanfo =$temp[0];
	    $teamnum = $temp[1];
	    $type=$temp[2];
	    $temps['hteam'] = $teamnameinfo[$teamnum]['hteam'];  //$teamnameinfo[]  ;//$teamnameinfo[$teamnum]['hteam'];
	    $temps['vteam'] = $teamnameinfo[$teamnum]['vteam']; //$teamnameinfo[$teamnum]['vteam'];
	    $temps['teamid'] = $vv;
	   // $temps['isconcede'] = -1;  //$teamnameinfo[$teamnum]['isconcede'];
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
	function chaipiao($yuhua_lotterycode){
	    if(empty($yuhua_lotterycode)){return false;}
	    $temp = array();
	    $date = array();
	    $sparray= array();
	    $renyispvalue = array();
	    $temp = explode(';',$yuhua_lotterycode);
	    foreach($temp as $key=>$val)//209^130923-001(1_3.40,3_2.60)
	     {
	          preg_match('/[0-9]{3}\^[0-9]+/',$val,$arr);//获取209^130923-001
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
	//function getTeamInfo($teamcode,$yuhua_lotteryid,$touzhuarray){
	//	return '';
	//}

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
	     preg_match_all('/[0-9]{5,11}/', implode(',',$arr),$tempa2);
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
	   
	   $temp_list1 = $this->getCombinationToString($arr, ($m-1));
	
	   foreach ($temp_list1 as $s)
		{
		   preg_match_all('/[0-9]{5,11}/',$temp_firstelement,$tempa2);
		  // print_r($tempa2);
	       $tempstr = $tempa2[0][0];
		   //echo $tempstr;
		   $checks = $s;
		   //echo  $checks;
		   $checks = $checks;
		   //echo $checks;
		   if(strpos($checks,$tempstr)){
			  ;// echo 1;
		   }else{
		      $s = $temp_firstelement.','.$s;
		      $result[] = $s;
		   }
		}
	 unset($temp_list1);
	 $temp_list2 = $this->getCombinationToString($arr, $m);
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
	//
	function round2($num,$precision){
	    $pow = pow(10,$precision);
	    if(  (floor($num * $pow * 10) % 5 == 0) && (floor( $num * $pow * 10) == $num * $pow * 10) && (floor($num * $pow) % 2 ==0) ){//舍去位为5 && 舍去位后无数字 && 舍去位前一位是偶数    =》 不进一
	        return floor($num * $pow)/$pow;
	    }else{//四舍五入
	        return round($num,$precision);
	    }
	}

}
