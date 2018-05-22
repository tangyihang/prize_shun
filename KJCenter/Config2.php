<?php

//开奖后台配置信息
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PWD', 'meijun820526^&LKASI');
define('DB_NAME', 'zhiying');
define('DB_PORT', 3306);
define('DB_PREFIX', '');

//Redis链接
$_RC['HOST']="127.0.0.1";
$_RC['PWD']="123456";
$_RC['PORT']="6379";
//网站开售时间 足球
$_JC=array();
$_JC['starttime']="09:20:00";
//$_JC['endtime']="23:50:00";
$_JC['endtime']="23:50:00";
$_JC['addtime']="3600";
//$_JC['tqtime']="600"; //提前时间 s
$_JC['tqtime']="600"; //提前时间 s

//网站开售时间 篮球
$_LC['starttime']="09:00:00";
//$_LC['endtime']="23:50:00";
$_LC['endtime']="23:50:00";
$_LC['addtime']="3600";
//$_LC['tqtime']="600"; //提前时间 s
$_LC['tqtime']="600"; //提前时间 s
