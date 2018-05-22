<?php

define('EASY_ROOT', substr(dirname(__FILE__), 0, -7).DIRECTORY_SEPARATOR.'php_client'.DIRECTORY_SEPARATOR);


require_once EASY_ROOT.'lib/easy.client.manager.php';




$clientmanager = new easyxmlmanager('');
$header = array(
		'messengerid' => $clientmanager->getmessageid(),
		'transactiontype' => '12002',
);

$element = array(
	'lotteryid' => '118',
	'issues' => '1',
);
var_Dump($element);
$clientmanager->execute($header,$element,'wangyue23',$_SERVER['REMOTE_ADDR'],'WEB');


echo $clientmanager->getoelementvalue('errorcode');

//var_Dump($clientmanager->getelements());
?>