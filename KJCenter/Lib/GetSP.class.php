<?php
/*
* 从数据库里查询数据,写入缓存文件,数据库mysql
* 写入缓存文件数据为1~5s钟保证 数据的及时性,缓解查询数据库压力
* auther hezhengde
*/
class GetSP
{
	 public $DB;
	 function __construct(){
	 	;
	 	}
	 /*竞彩篮球*/
	 //[214,215,216,217]
	 function lancai($lotteryarr)
	 {
	 	$type="bk";
	 	for($i=0;$i<count($lotteryarr);$i++)
	 	{
	 		 $result=$this->query($lotteryarr[$i]);
	 		 $this->writecache($result,$lotteryarr[$i],$type); 
	 	}
		return $result;
	 }
	 /*
	 *竞彩足球[209,210,211,212,213]
	 */
	 function zucai($lotteryarr)
	 {
	 	$type="fb";
	 	for($i=0;$i<count($lotteryarr);$i++)
	 	{
	 		 $result=$this->query($lotteryarr[$i]);
	 		 $this->writecache($result,$lotteryarr[$i],$type); 
	 	}
		return $result;
	 }
	 //
	 function writecache($data,$lotteryid,$type)
	 {
	    require_once ROOT . "/Lib/cache/functions.php";
	 	$cache_path = ROOT . 'log/cachefiles/'.$type;
		// 缓存时间
		$cache_time = -1;
		// 缓存名称
		$cache_name = 'lottery_'.$lotteryid;
		cachedata($cache_name, $data, $cache_time, '', $cache_path);
	 }
	 
	 function query($lotteryid)
	 {
		//查询当前时间在售的最新的sp值
		$timenum=intval(date("H",time()));
		if($timenum<24){
	 	   $datetime=date('Y-m-d',time()-3600*24);
		}else{
	 	   $datetime=date('Y-m-d',time());
		}
		$sql="SELECT lotttime,lotteryid,ballid,spinfo,single,p_status FROM (SELECT * FROM lottery_spinfo WHERE lotttime>='".$datetime."' AND lotteryid=".$lotteryid." ORDER BY ADDTIME DESC) a GROUP BY lotttime,ballid";
	
		$result=$this->DB->query($sql);
		return $result;
	 }
}
?>