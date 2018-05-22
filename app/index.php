<?php
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
define('APP_DEBUG',True);  // 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
// 定义应用目录
define('APP_PATH','./Application/');
ini_set('pcre.backtrack_limit', 1000000); //抓取大数据时需要开启此功能
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

