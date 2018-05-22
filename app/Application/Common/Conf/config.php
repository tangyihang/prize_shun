<?php
return array(
    'APP_GROUP_MODE' => 0,
	'APP_GROUP_LIST' => 'Admin',
    'URL_MODEL'          => '1',
    'SESSION_AUTO_START' => true,
	'TMPL_CACHE_ON' => false,
    //数据库配置信息
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'zhcwsystem', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '1q2w3e4R!', // 密码
    'DB_PORT'   => 3306, // 端口
    'DB_PREFIX' => '', // 数据库表前缀
        // 开启路由
    //'URL_ROUTER_ON' => true,
    //'URL_MAP_RULES'=>array(
    //   'user/' => '/home/user'
    //),
    'INTER_LIB1' => array(
	'lotterycenter'=>'http://192.168.1.249/easylib/dotransaction.php',
	'agenterid'=>'10000001',
	'agenterkey'=>'e10adc3949ba59abbe56e057f20f883e',
	'charset'=>'utf-8'
	),
	
	'INTER_LIB2' => array(
	'lotterycenter'=>'http://192.168.1.249/ieasylib/dotransaction.php',
	'agenterid'=>'10000001',
	'agenterkey'=>'e10adc3949ba59abbe56e057f20f883e',
	'charset'=>'utf-8'
	),
);
