<?php
header("Content-type: text/html; charset=GBK");
define('LIB_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR.'Lib'.DIRECTORY_SEPARATOR);
define('CLASS_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR.'Class'.DIRECTORY_SEPARATOR);
define('CACHE_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR.'Cache'.DIRECTORY_SEPARATOR);
require_once 'Config.php';
require_once LIB_ROOT.'lotteryResult.class.php';
require_once LIB_ROOT.'cache/functions.php';

$lotteryResult = new lotteryResult();
$lotteryResult->initDB();
/**
 * 500w普通数字彩抓取开奖信息写入缓存
 */
$source_500w = array(
	'1' => 'http://www.500wan.com/static/info/kaijiang/xml/ssq/list10.xml',
	'2' => 'http://www.500wan.com/static/info/kaijiang/xml/sd/list10.xml',
	'3' => 'http://www.500wan.com/static/info/kaijiang/xml/qlc/list10.xml',
	'281' => 'http://www.500wan.com/static/info/kaijiang/xml/dlt/list10.xml',
	'283' => 'http://www.500wan.com/static/info/kaijiang/xml/pls/list10.xml',
	'284' => 'http://www.500wan.com/static/info/kaijiang/xml/plw/list10.xml',
	'282' => 'http://www.500wan.com/static/info/kaijiang/xml/qxc/list10.xml'
	//'324' => 'http://www.500wan.com/static/info/kaijiang/xml/sfc/list10.xml',
	//'441' => 'http://www.500wan.com/static/info/kaijiang/xml/sfc/list10.xml',
	//'326' => 'http://www.500wan.com/static/info/kaijiang/xml/zc6/list10.xml',
	//'325' => 'http://www.500wan.com/static/info/kaijiang/xml/jq4/list10.xml'
	);
foreach($source_500w as $k=>$v){
	$lotteryResult->runCache($k, $v, '500w');
}

