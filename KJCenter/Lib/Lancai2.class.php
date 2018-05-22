<?php
/**
 * 竞彩篮球对阵抓取
 * @author HXD 2015-03-21 上线使用
 */
include_once ROOT.'/Lib/Base.class.php';
class Lancai extends Base{
   public $url;
   public $charset = 'GBK';
   	public $formatData  = array();
   function __construct($url)
   {
     $this->url=$url;
   }
   function run(){
        $this->gethtmldata();
        $val2=array();
        $arrweek=array('周一'=>1,'周二'=>2,'周三'=>3,'周四'=>4,'周五'=>5,'周六'=>6,'周日'=>7);
        foreach($this->formatData as $value){
           $val2['s_code']='BK';
		   $tempintweek=getIntWeek(substr($value['ball'], 0, -3));
		   $tempdate = get_date_by_week($tempintweek,$value['gamestarttime']);
		   if(time()-strtotime($tempdate) >6*3600*24){
			   continue;
		   }
		   $week=($tempintweek == '0') ? '7' : $tempintweek;
		   $val2['num']=$week.preg_replace('@.*(\d{3})@','\\1',$value['ball']);
		   $val2['date']=date('Y-m-d',strtotime($value['gamestarttime']));
		   $val2['time']=date('H:i:s',strtotime($value['gamestarttime']));
		   $val2['b_date'] = date('Y-m-d',strtotime($tempdate));
			$val2['l_code'] = '';
		   $val2['l_id'] = '0';
		   $val2['h_id'] = '0';
		   $val2['danguan'] = '0';
		   $val2['h_code']='';
		   $val2['a_id'] = '0';
		   $val2['a_code'] = '';
		   $val2['l_cn'] = strip_tags($value['gamename']);
		   $val2['h_cn'] = $value['hteam'];
		   $val2['a_cn'] = $value['vteam'];
		   $val2['color'] = $value['bgcolor']?$value['bgcolor']:0;
		   $val2['status'] = $value['status'];
		   $val2['id'] = $value['mid'];
		   //if($val2['num']=='2301'){
			 //  continue;
		  //}
		    if($value['status']=='Selling' || $value['status']=='Close')
		   { 
	          print_r($val2);
		      $this->saveToDb($val2);
		   }
        }
   }
   //
   function gethtmldata(){
       $html=file_get_contents($this->url);
      if($this->charset != 'UTF-8'){
	       $html = mb_convert_encoding($html, 'UTF-8','gb2312');
	  }
		$parten='/<tr windex=\"\d+\" lindex=\"\d+\".*>(.*)<\/tr>/iUs';
    	preg_match_all($parten,trim($html),$match_html);
		
    	$parten='/<tr.*>(.*)<\/tr>/iUs';
    	$parten_td = "/<td.*>(.*)<\/td>/iUs";
    	$parten_span="/<span.*>(.*)<\/span>/iUs";
    	$parten_a="/<a.*>(.*)<\/a>/iUs";
		$parten_str = "'<[\/\!]*?[^<>]*?>'si";
		$parten_div="/<div.*>(.*)<\/div>/iUs";
		$parten_font="/<font.*>(.*)[+,-]{1}.*<\/font>/iUs";
    	//preg_match_all($parten,$match[0][0],$match2);
    	$lancaiarr=array();
    	$i=0;
    	foreach($match_html[0] as $key=>$val){
    		preg_match_all($parten_td, $val, $matchtd);
    		if(count($matchtd[0])==11){
    		    $temp=$matchtd[0];
				
    		    $lancaiarr[$i]['ball']=trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[0]));
    		    $lancaiarr[$i]['gamename']=strip_tags(trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[1])));
				$lancaiarr[$i]['bgcolor']='';
				$teamstr=trim(strip_tags($temp[2]));
				$teamstr=str_replace("\n","",$teamstr);
				$teamstr=str_replace(" ","",$teamstr);
				$teamnamearr=explode('^',str_replace("VS","^",$teamstr));
				$lancaiarr[$i]['vteam']=trim($teamnamearr[0]);
				$lancaiarr[$i]['hteam']=trim($teamnamearr[1]);
				
    		    $lancaiarr[$i]['gamestarttime']=date('YmdHis',strtotime(trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[3]))));
    		    preg_match_all("/m=\d+/is", $temp[2], $midarr);
			    $lancaiarr[$i]['mid']=preg_replace('@^m=(\d+)$@','\\1',$midarr[0][0]);
			
				$status='';
				if(iconv('UTF-8','gbk//IGNORE',trim(strip_tags($temp[5])))=="已开售"){
					$status='Selling';
				}
				if(iconv('UTF-8','gbk//IGNORE',trim(strip_tags($temp[5])))=="停售"){
					$status='Close';
				}
				$lancaiarr[$i]['status']=$status;
				
    		    $temp[6]=str_replace("\n","",$temp[6]);
    		    $temp[7]=str_replace("\n","",$temp[7]);
    		    $temp[8]=str_replace("\n","",$temp[8]);
    		    $temp[9]=str_replace("\n","",$temp[9]);
    		    $lancaiarr[$i]['216']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\"></div>.*@','\\1',$temp[6]));
    		    $lancaiarr[$i]['214']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\"></div>.*@','\\1',$temp[7]));
    		    $lancaiarr[$i]['215']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\"></div>.*@','\\1',$temp[8]));
    		    $lancaiarr[$i]['217']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\"></div>.*@','\\1',$temp[9]));
    		    $i++;
    		}
    	}
		
		$this->formatData=$lancaiarr;
   }
   
   function saveToDb($arr)
   {
   	  //print_r($arr);
	  $sql="select * from bk_betting where b_date='".$arr['b_date']."' and num=".$arr['num'];
   //  echo $sql;

      $num =$this->DB->execute($sql);
      if($num>0){
      	$where=array(
	      	 'b_date'=>$arr['b_date'],
	      	 'num'=>$arr['num'],
      	);
      	$sql="update bk_betting set status='".$arr['status']."'  WHERE index_show!=1 and b_date='".$arr['b_date']."' and num='".$arr['num']."'";
        echo $sql."\n";
		$res =$this->DB->execute($sql);
	    if($res){
			echo "update success! \n";
		}
      }else{
   	    $res=$this->DB->data($arr)->table("bk_betting")->add();
        if($res){
			echo "new add match data to DBbase success \n";
		}else{
			echo $this->DB->error();
			echo "error";
		}
	  }
   }
   //
   function updateGameStatus(){
	   $sql="update bk_betting set status='Final' where date='".date('Y-m-d',time())."' and time<'".date('H:i:s',time())."'";
       echo $sql."\n";
	   $res =$this->DB->execute($sql);
       if($res){
		   echo "update status ".$res."success!";
	   }else{
		   echo "no update status ";
	   }	   
   }
}
?>
