<?php
require "base.class.php";
class optimize extends base
{
	//had|74300|h#3.35|+1.00  //had:  209^700(1)
	public $type="pingjun";
	public $sport="fb";
	public $lotterycode="";
	public $childtype="";
	public $totalMoney="";
	public $lottoryType = array('had'=>209,'hhad'=>210,'crs'=>211,'ttg'=>212,'hafu'=>213);
	public $tzcodeArr = array('h'=>3,'a'=>'1','d'=>'0');
	public $tablestr='';
	public $teaminfo=array();
	public $renyispvalue='3840';
	
	function __construct($lotterycode,$childtype,$type,$totalMoney,$sport){
	   $this->lotterycode = $lotterycode;
	   $this->childtype = $childtype;
	   $this->type = $type;
	   $this->totalMoney = $totalMoney;
	   $this->sport=$sport;
	}
    public function getchildtypeArr(){
        return explode('^',$this->childtype);
    }

	public function make(){
	    $lotstr=implode(';', $this->toLotArr());
        preg_match_all('/[0-9]{5,}/',$lotstr,$temparr);
        $teamcode = array_unique($temparr[0]);
        $this->teaminfo=$this->getTeam($this->toLotArr(),$this->sport);

        $arr=$this->chaipiao($lotstr);
        $hebingyouhua = array();
		
        foreach($this->getchildtypeArr() as $kl=>$vl)
		{
		   $vp = str_replace('x1','',$vl);
		    //print_r($arr['date']);
		   $aaaa = $this->getCombinationToString($arr['date'],$vp);
		   //print_r($aaaa);
		   $hebingyouhua = array_merge($hebingyouhua,$aaaa);//得到票有多少张
		}
		
		$youhuanum=$this->totalMoney/2;
		$date = $this->getcalculatespval($arr['spvalue'],$hebingyouhua);
		$ticketsp=$arr['spvalue'];
		$ticketspvalue = $date['sparray'];//获取每张票的SPval  renyispval

		$renyikey     = key($date['renyispval']);
		$middelvalue =0;//计算出spvalue的中间值
		foreach($ticketspvalue as $k=>$v)
		{
			$middelvalue = (float)$middelvalue+(float)$this->renyispvalue/(float)$v;
		}
		$pingjunvalue = (float)$youhuanum/(float)$middelvalue;

		$zongzhu = 0;
		foreach($ticketspvalue as $ka=>$va)
		{
		  $temp=array();$zhushus='';
		  $temp['spvalue']=$va;
		  $zhushus = (float)$pingjunvalue*(float)$this->renyispvalue/(float)$va;
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
		  $date = $this->getbipiaoval($tempvalue,$zongzhu,$youhuanum);//补票以后的值
		}else{
		  $date = $this->getbipiaojianval($tempvalue,$zongzhu,$youhuanum);//补票以后的值
		}
        //得到一注的奖金进行排序
		$afteryouhuamoney = array();
		foreach($date as $kl=>$vl)
		{
		  $afteryouhuamoney[$kl] = $vl['spvalue'];
		}
       
		$methodname=$this->type;
		$date=$this->$methodname($date,$this->totalMoney,$youhuanum);
        asort($afteryouhuamoney);
         $ticksinfod = array();
		foreach($hebingyouhua as $ks=>$vs)
		{ 
		  $temp=array();
		  $a = explode(',',$vs);
		  foreach($a as $kk=>$vv)
			{
			$temp[] =$this->getticketinfo($vv,$this->teaminfo,$ticketsp); 
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
		
		$this->outStr($ticksinfod,$date);
		

	}
	function outStr($ticksinfod,$date){

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
		   $peilv = $this->round2($peilv*2,2);
		   $ks=rtrim($ks,',');
		   //if(empty($submit_act)){$zhushusinfo = 1;}else{$zhushusinfo = $date[$ks]['zhusui'];}
		   $tmpStr='';
		   $zhushusinfo = $date[$ks]['zhusui'];
		   foreach($v as $ke=>$va){
			   //print_r($va);
              $tmpStr.=$va['hteam'];
               $tmpStr.="[".$va['typeinfo']."]";
               if($va['lotteryid']=='210'){
               	   
                   $tmpStr.='('.$va['isconcede'].')';
               }
               $tmpStr.="×";
               
		   }
		   $tmpStr.='2=';
		   $tmpStr.=$peilv*$zhushusinfo;
		   $str.='<tr class="noteTrObj" data-teamVal="'.rtrim($teamVal,';').'" data-noteVal="'.round($peilv, 2).'" data-totalVal="'.round($peilv*$zhushusinfo, 2).'"  data-betVal="'.$zhushusinfo.'" data-passType="" >
             <td class="cc">'.$num.'</td><td class="cc" width="555">'.$tmpStr.'</td>
             <td  class="cc noteBetObj" >'.$zhushusinfo.'</td>
                <td  class="cc"><div class="jjyh">
                    <dl>
                      <dt class="noteBox"><a class="float_l symboljian" href="javascript:void(0)" data-type="-1" hidefocus>-</a></dt>
                      <dd>
                        <input type="text" name="" value="'.$peilv*$zhushusinfo.'">
                      </dd>
                      <dt class="noteBox"><a class="float_l symbol updateNoteObj" href="javascript:void(0)" data-type="+1" hidefocus>+</a></dt>
                    </dl>
                  </div></td>
		   </tr>';
		    /*$str.='<tr class="noteTrObj" data-teamVal="'.rtrim($teamVal,';').'" data-noteVal="'.round($peilv, 2).'" data-totalVal="'.round($peilv*$zhushusinfo, 2).'"  data-betVal="'.$zhushusinfo.'" data-passType=""><td>'.$num.'</td><td class="tal">';
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
		    */
		 $num++;
		}
		$this->tablestr=$str;
	}
	//将投注串转化为数组
	function toLotArr(){
		$arr=explode(',',$this->lotterycode);
		$newarr=array();
		for($i=0;$i<count($arr);$i++){
			 //$arr[$i]=str_replace('s', '', $arr[$i]);
             $arr[$i]=str_replace('#', '_', $arr[$i]);
             $arr[$i]=str_replace('&', ',', $arr[$i]);
             $tmp=explode('|', $arr[$i]);
             $patterns = array ('h', 'a','d');
             $replace = array ('3', '1','0');
             $newarr[]=$this->lottoryType[$tmp[0]].'^'.$tmp[1]."(".str_replace($patterns, $replace, $tmp[2]).")";
		}
		return $newarr;
	}
	//平均优化
	function pingjun($date,$lotteryvalue,$youhuanum){
		return $date;
	}
	//博热优化
	function bore($date,$lotteryvalue,$youhuanum){
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
	     return $date;
	}
	//博冷优化
	function boleng($date,$lotteryvalue,$youhuanum){
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
	   return $date;
	}
	//保本优化
	function baoben($afteryouhuamoney){
		;
	}
	//优化后输出结果
	function result(){
		return array();
	}	
}
