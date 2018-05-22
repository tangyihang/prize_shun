<?php
include_once ROOT.'/Lib/Base.class.php';
class Jingcai extends Base{
    public $url;
    public $charset = 'GBK';
   	public $formatData  = array();
	public $DB;
   function __construct($url='')
   {
     $this->url=$url;
   }
    function run($lottid=''){
        $this->gethtmldata();
		$val2=array();
		$filename=date("YmdH",time()).".log";
		$this->writeLog($filename,json_encode($this->formatData));
		foreach ($this->formatData as $value) {
		   $val2['cz_type']='11';
		   $tempintweek=getIntWeek(substr($value['ball'], 0, -3));
		   $tempdate = get_date_by_week($tempintweek);
		   
		   $val2['lotttime']=$tempdate;
		   $val2['lottweek']=($tempintweek == '0') ? '7' : $tempintweek;
		   $val2['gamestarttime']=$value['gamestarttime'];
		   $val2['endtime']=$value['ball'];
		   $val2['gamename']=$value['gamename'];
		   $val2['hteam']=$value['hteam'];
		   $val2['vteam']=$value['vteam'];
		   $val2['status']=iconv('UTF-8','gbk//IGNORE',$value['status'])=="ÒÑ¿ªÊÛ" ? 0 : -1;
		   $val2['ballid']=preg_replace('@.*(\d{3})@','\\1',$value['ball']);
		   $val2['status_single']='209|'.$value['209'].'^210|'.$value['210'].'^211|'.$value['211'].'^212|'.$value['212'].'^213|'.$value['213'];
          	  
		  //if($val2['status']==0)
		   //{
	         $this->saveToDb($val2);
		   //}
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
    	$jingcaiarr=array();
    	$i=0;
    	foreach($match2[0] as $key=>$val){
    		preg_match_all($parten_td, $val, $matchtd);
    		if(count($matchtd[0])==12){
    		    $temp=$matchtd[0];
    		    $jingcaiarr[$i]['ball']=trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[0]));
    		    //$jingcaiarr[$i]['gamename']=$temp[1];//trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[1]));
    		    
    		    $jingcaiarr[$i]['gamename']=strip_tags($temp[1]);
				$teamstr=trim(strip_tags($temp[2]));
				$teamstr=str_replace("\n","",$teamstr);
				$teamstr=str_replace(" ","",$teamstr);
				$teamnamearr=explode('^',str_replace("VS","^",$teamstr));
				$jingcaiarr[$i]['hteam']=$teamnamearr[0];
				$jingcaiarr[$i]['vteam']=$teamnamearr[1];
				
    		    $jingcaiarr[$i]['gamestarttime']=date('YmdHis',strtotime(trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[3]))));
    		    $jingcaiarr[$i]['status']=trim(preg_replace('@<td.*>(.*)<\/td>@', '\\1', $temp[5]));
    		    $temp[5]=str_replace("\n","",$temp[5]);
    		    $temp[6]=str_replace("\n","",$temp[6]);
    		    $temp[7]=str_replace("\n","",$temp[7]);
    		    $temp[8]=str_replace("\n","",$temp[8]);
				$temp[9]=str_replace("\n","",$temp[9]);
				$temp[10]=str_replace("\n","",$temp[10]);
			  
    		    $jingcaiarr[$i]['209']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[6]));
    		    $jingcaiarr[$i]['210']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[7]));
    		    $jingcaiarr[$i]['211']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[8]));
    		    $jingcaiarr[$i]['212']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[9]));
				$jingcaiarr[$i]['213']=strip_tags(preg_replace('@.*<div.?class=\"(.*)\d+\"></div>.*@','\\1',$temp[10]));
    		    $i++;
    		}
    	}
		$this->formatData=$jingcaiarr;
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
      }else{
   	    $this->DB->data($arr)->table("tab_sport_lottery_info")->add();
      }
   }
}