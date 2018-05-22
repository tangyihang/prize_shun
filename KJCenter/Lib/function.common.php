<?php
/**
 * 抓取程序公共函数
 * @author GWB 2011-11-10
 *
 */
/**
 * 验证SP值
 * 如果格式不是546565.21或65456.1则返回空
 * 如果格式正确，则返回该	SP值
 * @param string $str 要验证的SP值
 */
function check_data($str){
	$pregstr = "/^\d+(\.\d{1,2})?$/";
	if(preg_match($pregstr, $str)){
		if($str == '0000') $str = '-';
		return $str;
	} else {
		return '';
	}
}

/**
 * 将中文星期转换为数字
 * 
 * 注：保证当前文件为UTF-8编码
 * @param string $weekName 中文星期
 */
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
	
	if(in_array($weekName, $weekarr)){
		$weekint = str_replace(array_values($weekarr), array_keys($weekarr), $weekName);
	} else {
		$weekint = -1;
	}
	
	return intval($weekint);
}

/**
 * 根据星期获取最小日期
 * @param string $thisWeek 要获取的星期汉字，保证编码为UTF-8 (0,7作为周日处理)
 * @return string $retdate 获取到的日期格式：Ymd 如果传入参数错误或生成失败都返回false (周日只返回7)
 */
function get_date_by_week($thisWeek,$gamestarttime=''){
	   $retdate = '';
       $weekint = '';
		if(is_numeric($thisWeek)){
			$weekint = $thisWeek;
		} else {
			$weekint = getIntWeek($thisWeek);
		}
		// 1或7均为周日
		if($weekint == 7) $weekint = 0;
		$curweek = date('w');
		$curtime=time();
		//$curtime=strtotime('2016-12-18 13:00:00');
		//$curweek = 0;
		$datenum=ceil((strtotime($gamestarttime)-$curtime)/(3600*24)-1);
		$num=0;
		if($datenum > 0){
			if($curweek > $weekint){
				$num=$weekint+7-$curweek;
			}else{
				$num=$weekint-$curweek;
			}
			$retdate = date('Ymd', ($curtime + $num * 24 * 3600));
			if($datenum>6){
		       $retdate=date('Ymd',strtotime($retdate)+intval($datenum/7)*3600*24*7);
	         }
			 
			if(strtotime($retdate) > strtotime($gamestarttime) && date('w', strtotime($retdate)) == $weekint){
			   $retdate = date('Ymd',strtotime($retdate)-7*3600*24);
		    } 
			 
		}else{
		    if($curweek == 0) $curweek = 7;
			if($curweek==7 && $weekint==0){
				$num=0;
			}else{
				if($curweek >= $weekint){
					$num=$curweek-$weekint;
				}else{
					$num=$curweek+(7-$weekint);
				}
			}
			$retdate = date('Ymd', ($curtime - $num * 24 * 3600));
		}
		// 反验证生成是否正确，如果不正确返回false
		$newweek = date('w', strtotime($retdate));
		
		if($newweek != $weekint){
			return false;
		}
		return $retdate;
}

/**
 * 将数组转换为字符串格式
 * @param $arr
 */
function arr2str($arr){
	$retstr = '';
	if(is_array($arr)){
		foreach ($arr as $key => $value){
			$retstr .= ' ['.$key.']=>{'. arr2str($value).'}';
		}
	} else {
		$retstr = $arr;
	}
	return $retstr;
}

/**
 * 创建目录结构
 * @param string $path
 * @param string $mode
 */
function mkpath($path, $mode = 0777){
	$dirs = explode('/',$path);
	$pos = strrpos($path, ".");
	if ($pos === false) {
		$subamount=0;
	} else {
		$subamount=1;
	}
	for ($c=0;$c < count($dirs) - $subamount; $c++)
	{
		$thispath = ''; 
		for ($cc=0; $cc <= $c; $cc++) 
		{
			$thispath.=$dirs[$cc].'/'; 
		}
		if (!file_exists($thispath)) {
			mkdir($thispath,$mode); 
		}
	}
}

/**
 * 调试日志
 * @param string $msg
 */
function debuglog($msg){
	$logfilename = APP_ROOT . 'log/' . date('Ymd') . '.log';
	
	if (! file_exists ( $logfilename )) {
		mkpath ( $logfilename );
	}
	
	// 记录接收到的数据包
	error_log ( '['.date('Y-m-d H:i:s').']' . $msg . chr ( 13 ) . chr ( 10 ), 3, $logfilename );
}
?>