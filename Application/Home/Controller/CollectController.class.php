<?php
namespace Home\Controller;
use Think\Controller;
class CollectController extends Controller {
    public function index(){

    }

    //获取比赛结果
    public function result()
    {

    }

    //获取竞彩 赛程
    public function Sporttery()
    {


       $ielement=array();
       $ielement['lotteryid']=210;
       $ielement['lotteryissue']='20000';


       $this->saveSporttery($ielement);
    }
    //
    public function getcontentxml(){
      $url='http://info.sporttery.cn/interface/interface_wms.php?action=wf_list&pkey=051749a7b47e012a2ec33ac11955801b&'.rand(111111111,1000000000);

    }



    //获取快3开奖数据
    public function k3()
    {
        $filename="http://www.aicai.com/lottery/kc!kc3.jhtml?time=1395728183377&gameIndex=311";
        $content=file_get_contents($filename);
        echo $content;
    }

    //篮彩sp
    public function lancaisp()
    {
       $stamptime=time();
       //篮彩获取 浮动sp值
       //$lanurl="http://info.sporttery.cn/basketball/hdc_list.php?t=";
       $lanurl="http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&poolcode[]=";
       $spgd_arr=array(
         0=>array('lotteryid'=>214,'url'=>$lanurl.'hdc&_='.$stamptime), //rfsf
         1=>array('lotteryid'=>215,'url'=>$lanurl.'hilo&_='.$stamptime), //dxf
         2=>array('lotteryid'=>216,'url'=>$lanurl.'mnl&_='.$stamptime), //sf
         3=>array('lotteryid'=>217,'url'=>$lanurl.'wnm&_='.$stamptime) //sfc
       );
      //篮彩获取 固定sp值
       //$spgd_arr=array(
        //  0=>array('lotteryid'=>'214','url'=>$lanurl.'/hdc_list.php?t='.$stamptime), //
        //  1=>array('lotteryid'=>'215','url'=>$lanurl.'/hilo_list.php?t='.$stamptime), //
        //  2=>array('lotteryid'=>'216','url'=>$lanurl.'/mnl_list.php?t='.$stamptime), //
         // 3=>array('lotteryid'=>'217','url'=>$lanurl.'/wnm_list.php?t='.$stamptime) //
       // );
        $spdata=array();
        for($i=0;$i<count($spgd_arr);$i++)
        {
    	   $html = file_get_contents($spgd_arr[$i]['url']); // 胜负
    	   $spdata[$i]=$this->makehtml1($html,$spgd_arr[$i]['lotteryid']);

        }
        
        foreach($spdata as $key=>$rows)
        {
          foreach($rows as $val){
          	// print_r($val);
             $this->savetoSPinfo($val);
          }
        }
    }
     public function makehtml1($htmlcontent,$lotteryid)
    {
        $sparr=$this->objectToArray(json_decode($htmlcontent));
        $key=0;
        foreach($sparr['data'] as $row)
        {
              $matchlist[$key]['lotttime']=str_replace('-','',$row['b_date']);
              $matchlist[$key]['ballid']=substr($row['num'],-3);
              $matchlist[$key]['lotteryid'] = $lotteryid;
               $matchlist[$key]['gameendtime'] = date("Y-m-d H:i:s ",strtotime($row['date']." ".$row['time'])) ;
              if($lotteryid==214)
              {
                 $matchlist[$key]['spinfo']=serialize(array('s'=>$row['hdc']['h'],'rf'=>$row['hdc']['fixedodds'],'f'=>$row['hdc']['a']));
                 $matchlist[$key]['single']=$row['hdc']['single'];
                 $matchlist[$key]['p_status']=$row['hdc']['p_status'];
              }
		      if($lotteryid==216){
		      	   
                 $matchlist[$key]['spinfo']=serialize(array('s'=>$row['mnl']['h'],'f'=>$row['mnl']['a']));
                 $matchlist[$key]['single']=$row['mnl']['single'];
                 $matchlist[$key]['p_status']=$row['mnl']['p_status'];
		      }
              if($lotteryid==217){
              	
              
								$sp_z=array();
								$sp_k=array();
								
								$sp_z[0]=$row['wnm']['w1'];
								$sp_z[1]=$row['wnm']['w2'];
								$sp_z[2]=$row['wnm']['w3'];
								$sp_z[3]=$row['wnm']['w4'];
								$sp_z[4]=$row['wnm']['w5'];
								$sp_z[5]=$row['wnm']['w6'];
								
								$sp_k[0]=$row['wnm']['l1'];
								$sp_k[1]=$row['wnm']['l2'];
								$sp_k[2]=$row['wnm']['l3'];
								$sp_k[3]=$row['wnm']['l4'];
								$sp_k[4]=$row['wnm']['l5'];
								$sp_k[5]=$row['wnm']['l6'];
								 
				        $matchlist[$key]['spinfo']=serialize(array('z'=>$sp_z,'k'=>$sp_k));
                $matchlist[$key]['single']=$row['wnm']['single'];
                $matchlist[$key]['p_status']=$row['wnm']['p_status'];
              }
              if($lotteryid==215){
              	 $dxf=array('d'=>$row['hilo']['h'],'x'=>$row['hilo']['l'],'z'=>floatval($row['hilo']['fixedodds']));
                 $matchlist[$key]['spinfo'] = serialize($dxf);
                 $matchlist[$key]['single']=$row['hilo']['single'];
                 $matchlist[$key]['p_status']=$row['hilo']['p_status'];
              }
              
              $key++;
        }
        return $matchlist;
    }
    //处理采集来的数据
    public function makehtml($htmlcontent,$lotteryid){
    	$trs_preg = "/<tr.*>(.*)<\/tr>/iUs";
	    $trs = array();

	    preg_match_all($trs_preg, $htmlcontent, $trs);

	    $tds_preg = "/<td.*>(.*)<\/td>/iUs";

        $td_list = array();
        foreach($trs[1] as $tr)
	    {
		   preg_match_all($tds_preg, $tr, $tds);
		  if(count($tds[1]) > 1){
			$td_list[] = $tds[1];
		  }
	    }
	    $matchlist=array();

	    foreach($td_list as $key=>$rows){
	      $matchlist[$key]['lotttime']=$this->get_date_by_week(trim(substr($rows[0], 0, -3)));
          $matchlist[$key]['ballid']=substr($rows[0], -3);
          $matchlist[$key]['lotteryid'] = $lotteryid;
          $matchlist[$key]['gameendtime']=date("Y-m-d H:i:s",strtotime('20'.$rows[3]));
          $pregstr = "'<[\/\!]*?[^<>]*?>'si";
          if($lotteryid==214){
             $rf=preg_replace($pregstr,'',$this->getScore($rows[2]));
             $matchlist[$key]['spinfo']=serialize(array('f'=> preg_replace($pregstr,'',$rows[4]),'s'=> preg_replace($pregstr,'',$rows[5]),'rf'=> $rf));
          }
	      if($lotteryid==215){
	      	 $z= preg_replace($pregstr,'',$this->getScore($rows[2]));
             $matchlist[$key]['spinfo']=serialize(array('d'=>preg_replace($pregstr,'',$rows[4]),'x'=>preg_replace($pregstr,'',$rows[5]),'z'=>$z));
          }
	      if($lotteryid==216)
	      {
             $matchlist[$key]['spinfo']=serialize(array('f'=>preg_replace($pregstr,'',$rows[4]),'s'=>preg_replace($pregstr,'',$rows[5])));
          }
	      if($lotteryid==217){
            $sp_z=array();
            $sp_k=array();
            for($i=5;$i<11;$i++){
               $temparr = $this->getZK_sfc_sp($rows[$i]);
               $sp_z[] = $this->check_data($temparr[1]);
			         $sp_k[] = $this->check_data($temparr[0]);
            }
            $matchlist[$key]['spinfo']=serialize(array('z'=>$sp_z,'k'=>$sp_k));
          }

	    }
	    return $matchlist;
    }
    //足彩SP
    public function zucaisp(){
    	 $stamptime=time();
        $jcurl="http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&poolcode[]=";

        //'http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&poolcode[]=had&_=1370669821125'//spf
        //http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&poolcode[]=crs&_=1379314411350 //bf
        //http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&poolcode[]=ttg&_=1370669821125 //zjq
        //http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&poolcode[]=hhad&_=1370669821125 //rq
        //http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&poolcode[]=hafu&_=1370669821125 //bqc

        $spgd_arr=array(
         0=>array('lotteryid'=>209,'url'=>$jcurl.'had&_='.$stamptime), //sf
         1=>array('lotteryid'=>210,'url'=>$jcurl.'hhad&_='.$stamptime), //sfc
         2=>array('lotteryid'=>211,'url'=>$jcurl.'crs&_='.$stamptime), //dxf
         3=>array('lotteryid'=>212,'url'=>$jcurl.'ttg&_='.$stamptime), //rfsf
         4=>array('lotteryid'=>213,'url'=>$jcurl.'hafu&_='.$stamptime) //sfc
        );
        $spdata=array();
        for($i=0;$i<count($spgd_arr);$i++)
        {
    	   $html = file_get_contents($spgd_arr[$i]['url']); // 胜负
    	   $spdata[$i]=$this->makehtml2($html,$spgd_arr[$i]['lotteryid']);
        }

        foreach($spdata as $key=>$rows)
        {
          foreach($rows as $val){
             $this->savetoSPinfo($val);
          }
        }
    }
    //竞彩足球处理sp
    public function makehtml2($htmlcontent,$lotteryid)
    {
        $sparr=$this->objectToArray(json_decode($htmlcontent));
        $key=0;
        foreach($sparr['data'] as $row)
        {
              $matchlist[$key]['lotttime']=str_replace('-','',$row['b_date']);
              $matchlist[$key]['ballid']=substr($row['num'],-3);
              $matchlist[$key]['lotteryid'] = $lotteryid;
               $matchlist[$key]['gameendtime'] = date("Y-m-d H:i:s ",strtotime($row['date']." ".$row['time'])) ;
              if($lotteryid==209){
                 $matchlist[$key]['spinfo']=serialize(array('s'=>$row['had']['h'],'p'=>$row['had']['d'],'f'=>$row['had']['a']));
                 $matchlist[$key]['single']=$row['had']['single'];
                 $matchlist[$key]['p_status']=$row['had']['p_status'];
              }
		      if($lotteryid==210){
                 $matchlist[$key]['spinfo']=serialize(array('s'=>$row['hhad']['h'],'p'=>$row['hhad']['d'],'f'=>$row['hhad']['a']));
                 $matchlist[$key]['single']=$row['hhad']['single'];
                 $matchlist[$key]['p_status']=$row['hhad']['p_status'];
		      }
              if($lotteryid==211){
              	$bf['sp_00']=$row['crs']['0000'];
				$bf['sp_01']= $row['crs']['0001'];
				$bf['sp_02']=$row['crs']['0002'];
				$bf['sp_03']=$row['crs']['0003'];
				$bf['sp_04']=$row['crs']['0004'];
				$bf['sp_05']=$row['crs']['0005'];
				$bf['sp_09']=$row['crs']['-1-a'];
				$bf['sp_10']=$row['crs']['0100'];
				$bf['sp_11']=$row['crs']['0101'];
				$bf['sp_12']=$row['crs']['0102'];

				$bf['sp_13']=$row['crs']['0103'];
				$bf['sp_14']=$row['crs']['0104'];
				$bf['sp_15']=$row['crs']['0105'];
				$bf['sp_20']=$row['crs']['0200'];
				$bf['sp_21']=$row['crs']['0201'];
				$bf['sp_22']=$row['crs']['0202'];
				$bf['sp_23']=$row['crs']['0203'];
				$bf['sp_24']=$row['crs']['0204'];
				$bf['sp_25']=$row['crs']['0205'];
				$bf['sp_30']=$row['crs']['0300'];
				$bf['sp_31']=$row['crs']['0301'];
				$bf['sp_32']=$row['crs']['0302'];
				$bf['sp_33']=$row['crs']['0303'];

				$bf['sp_40']=$row['crs']['0400'];
				$bf['sp_41']=$row['crs']['0401'];
				$bf['sp_42']=$row['crs']['0402'];
				$bf['sp_50']=$row['crs']['0500'];
				$bf['sp_51']=$row['crs']['0501'];
				$bf['sp_52']=$row['crs']['0502'];
				$bf['sp_90']=$row['crs']['-1-h'];
				$bf['sp_99']=$row['crs']['-1-d'];
                $matchlist[$key]['spinfo'] = serialize($bf);
                $matchlist[$key]['single']=$row['crs']['single'];
                $matchlist[$key]['p_status']=$row['crs']['p_status'];
              }
              if($lotteryid==212){
              	 $zjq=array('t0'=>$row['ttg']['s0'],'t1'=>$row['ttg']['s1'],'t2'=>$row['ttg']['s2'],'t3'=>$row['ttg']['s3'],'t4'=>$row['ttg']['s4'],'t5'=>$row['ttg']['s5'],'t6'=>$row['ttg']['s6'],'t7'=>$row['ttg']['s7']);
                 $matchlist[$key]['spinfo'] = serialize($zjq);
                 $matchlist[$key]['single']=$row['ttg']['single'];
                 $matchlist[$key]['p_status']=$row['ttg']['p_status'];
              }
              if($lotteryid==213){
              	 $bqc=array('ff'=>$row['hafu']['aa'],'fp'=>$row['hafu']['ad'],'fs'=>$row['hafu']['ah'],'pf'=>$row['hafu']['da'],'pp'=>$row['hafu']['dd'],'ps'=>$row['hafu']['dh'],'sf'=>$row['hafu']['ha'],'sp'=>$row['hafu']['hd'],'ss'=>$row['hafu']['hh']);
                 $matchlist[$key]['spinfo'] = serialize($bqc);
                 $matchlist[$key]['single']=$row['hafu']['single'];
                 $matchlist[$key]['p_status']=$row['hafu']['p_status'];
              }
              $key++;
        }
        return $matchlist;
    }
    //保存入库
    public function savetoSPinfo($data){
       $Sp=M('Spinfo');
       if($data['lotteryid']=='' || $data['lotttime']=='' || $data['ballid']=='' || $data['spinfo']=='' ){
          return false;
          exit;
       }else{
       	$result=$Sp->field('spinfo,single,lasttime')->where("lotteryid=".$data['lotteryid']." and lotttime='".date("Y-m-d",strtotime($data['lotttime']))
       	."' and ballid='".$data['ballid']."'")->order('addtime desc')->limit(1)->select();

       	if($data['spinfo']!=$result[0]['spinfo'] || trim($data['gameendtime'])!=$result[0]['lasttime'] || trim($data['single'])!=$result[0]['single']){
             $ielement=array(
	          'lotteryid' => $data['lotteryid'],
	          'lotttime' => $data['lotttime'],
	          'ballid' =>  $data['ballid'],
	          'spinfo' => $data['spinfo'],
	          'single' => $data['single'],
	          'p_status' => $data['p_status'],
	          'lasttime' => $data['gameendtime'],
	          'addtime' => date('Y-m-d H:i:s',time())
            );
            if($Sp->add($ielement)){
               echo "success<br>";
            }else{
               echo "error<br>";
            }
       	}
       }
    }
    //判断SP是否变化
    public function isChangeSP(){

    }
    //保存对阵信息
    public function saveSporttery($data){
       $ielement = array(
			'lotteryid'     => $data['lotteryid'],
			'lotteryissue'  => $data['lotteryissue'],
			'lotttime'      => $data['lotttime'],
			'lottweek'      => $data['lottweek'],
			'starttime'     => $data['starttime'],
			'endtime'       => $data['endtime'],
			'ballid'        => $data['ballid'],
			'gamename'      => $data['gamename'],
			'gamestarttime' => $data['gamestarttime'],
			'hteam'         => $data['hteam'],
			'isconcede'     => $data['isconcede'],
			'vteam'         => $data['vteam'],
			'status'        => $data['status'],
			'awardtime'     => ''
		);
     $Sporttery=M('Sportteryinfo');
     $result=$Sporttery->add($ielement);
     return $result;
    }
    //
    protected function get_date_by_week($thisWeek){
	$retdate = '';

	$weekint = '';
	if(is_numeric($thisWeek)){
		$weekint = $thisWeek;
	} else {
		$weekint = $this->getIntWeek($thisWeek);
	}
	// 1或7均为周日
	if($weekint == 7) $weekint = 0;

	$week = date('w');
	$n = $weekint - $week;
	$m = 7 - abs($n);

	if(abs($m) > abs($n)) {
		$retdate = date('Ymd', (time() + $n * 24 * 3600));
	} else {
		$retdate = date('Ymd', (time() - $m * 24 * 3600));
	}

	// 反验证生成是否正确，如果不正确返回false
	$newweek = date('w', strtotime($retdate));

	if($newweek != $weekint){
		return false;
	}

	return $retdate;
   }

	/**
	 * 验证SP值
	 * 如果格式不是546565.21、23.233或65456.1则返回空
	 * 如果格式正确，则返回该	SP值
	 * @param string $str 要验证的SP值
	 */
	protected function check_data($str){
		$pregstr = "/^\d+(\.\d{1,3})?$/";
		if(preg_match($pregstr, $str)){
			if($str == '0000') $str = '-';
			return $str;
		} else {
			return '';
		}
	}
	//
function getIntWeek($weekName){
	$weekint = '';

	$weekarr = array(
		'7' => '周日',
		'1' => '周一',
		'2' => '周二',
		'3' => '周三',
		'4' => '周四',
		'5' => '周五',
		'6' => '周六'
	);
	$weekName=iconv('gbk', 'utf-8', $weekName);
	if(in_array($weekName, $weekarr)){
		$weekint = str_replace(array_values($weekarr), array_keys($weekarr), $weekName);
	} else {
		$weekint = -1;
	}

	return intval($weekint);
}

	protected function getScore($istr)
	{
		$str = preg_replace("'<[\/\!]*?[^<>]*?>'si", '', $istr);
		$reg = '/([+-]?\d+(\.\d+)?)/';
		$arr = array();
		preg_match_all($reg, $str, $arr);

		if(isset($arr[1][0])){
			return end($arr[1]);
		} else {
			return '';
		}
	}
	protected function getZK_sfc_sp($sp_str)
	{
		$spstr = '';
		$pregstr = "'<[\/\!]*?[^<>]*?>|\s*'si";
		$spstr = preg_replace("'<br\s*?[\/\!]*?>'si", '###', $sp_str);
		$spstr = trim(preg_replace($pregstr, '', $spstr));
		return explode('###', $spstr);
	}
	protected function objectToArray($e){
	    $e=(array)$e;
	    foreach($e as $k=>$v){
	        if( gettype($v)=='resource' ) return;
	        if( gettype($v)=='object' || gettype($v)=='array' )
	            $e[$k]=(array)$this->objectToArray($v);
	    }
	    return $e;
	}
}
