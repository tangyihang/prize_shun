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
           $val2['cz_type']='12';
           $tempintweek=getIntWeek(substr($value['ball'], 0, -3));
		   $tempdate = get_date_by_week($tempintweek);
		   
		   $val2['lotttime']=$tempdate;
		   $cnweek=trim(preg_replace("@(.*)(\d{3})@","\\1",$value['ball']));
		   $val2['lottweek']=($tempintweek == '0') ? '7' : $tempintweek;
		   
		   $val2['gamestarttime']=$value['gamestarttime'];
		   $val2['endtime']='';
		   $val2['gamename']=$value['gamename'];
		   $val2['hteam']=$value['hteam'];
		   $val2['vteam']=$value['vteam'];
		   
		   $val2['ballid']=preg_replace('@.*(\d{3})@','\\1',$value['ball']);
		   
		   $val2['status_single']='214|'.$value['214'].'^215|'.$value['215'].'^216|'.$value['216'].'^217|'.$value['217'];
		   $val2['status_single']=strip_tags($val2['status_single']);
		   $this->saveToDb($val2);
        }
   }
   //
   function gethtmldata(){
       $html=file_get_contents($this->url);
      if($this->charset != 'UTF-8'){
	       $html = mb_convert_encoding($html, 'UTF-8','gb2312');
	  }
		$parten='/<div class=\"match_list\">.*<table width=\"100%\".*>(.*)<\/table>/iUs';
    	preg_match_all($parten,trim($html),$match_html);
		$parten='/<table width=\"100%\".*>(.*)<\/table>/iUs';
		
    	preg_match_all($parten,trim($match_html[0][1]),$match);
    	$parten='/<tr.*>(.*)<\/tr>/iUs';
    	$parten_td = "/<td.*>(.*)<\/td>/iUs";
    	$parten_span="/<span.*>(.*)<\/span>/iUs";
    	$parten_a="/<a.*>(.*)<\/a>/iUs";
		$parten_str = "'<[\/\!]*?[^<>]*?>'si";
		$parten_div="/<div.*>(.*)<\/div>/iUs";
		$parten_font="/<font.*>(.*)[+,-]{1}.*<\/font>/iUs";
    	preg_match_all($parten,$match[0][0],$match2);
    	$lancaiarr=array();
    	$i=0;
    	foreach($match2[0] as $key=>$val){
    		preg_match_all($parten_td, $val, $matchtd);
    		if(count($matchtd[0])==10){
    		    $temp=$matchtd[0];
    		    $lancaiarr[$i]['ball']=trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[0]));
    		    $lancaiarr[$i]['gamename']=trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[1]));
				$teamstr=trim(strip_tags($temp[2]));
				$teamstr=str_replace("\n","",$teamstr);
				$teamstr=str_replace(" ","",$teamstr);
				$teamnamearr=explode('^',str_replace("VS","^",$teamstr));
				$lancaiarr[$i]['vteam']=$teamnamearr[0];
				$lancaiarr[$i]['hteam']=$teamnamearr[1];
				
    		    $lancaiarr[$i]['gamestarttime']=date('YmdHis',strtotime(trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[3]))));
    		    $lancaiarr[$i]['status']=trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[4]));
    		    $temp[5]=str_replace("\n","",$temp[5]);
    		    $temp[6]=str_replace("\n","",$temp[6]);
    		    $temp[7]=str_replace("\n","",$temp[7]);
    		    $temp[8]=str_replace("\n","",$temp[8]);
    		    $lancaiarr[$i]['216']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[5]));
    		    $lancaiarr[$i]['214']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[6]));
    		    $lancaiarr[$i]['215']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[7]));
    		    $lancaiarr[$i]['217']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[8]));
    		    
    		    $i++;
    		}
    	}
		$this->formatData=$lancaiarr;
   }
   
   function saveToDb($arr)
   {
   	  $sql="select * from tab_sport_lottery_info where cz_type=".$arr['cz_type']." and lotttime=".$arr['lotttime']." and ballid=".$arr['ballid'];
      $num =$this->DB->execute($sql);
      if($num>0){
      	$where=array(
	      	 'cz_type'=>$arr['cz_type'],
	      	 'lotttime'=>$arr['lotttime'],
	      	 'ballid'=>$arr['ballid'],
      	);
      	$sql="update tab_sport_lottery_info set status_single='".$arr['status_single']."' WHERE cz_type=".$arr['cz_type']." and lotttime=".$arr['lotttime']." and ballid=".$arr['ballid'];
        $num =$this->DB->execute($sql);
        echo $num;
      }else{
   	    $this->DB->data($arr)->table("tab_sport_lottery_info")->add();
      }
   }
}
?>
