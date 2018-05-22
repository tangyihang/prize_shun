<?php
//$lotterycode="had|74300|h#3.35|+1.00,hhad|74301|d#3.35&a#3.50";
//$childtype="2_1";
ini_set('display_errors',0);
header("Content-type:text/html;charset=utf-8");
define('OPT_ROOT',dirname(__FILE__));
require OPT_ROOT."/libs/optimize.class.php";
require OPT_ROOT."/tpllibs/Smarty.class.php";
if(!class_exists('Smarty')){
   exit('Smarty class is not exists');
}
$base= new Smarty;
$base->debugging = false;
$base->caching=false;
$base->compile_dir=OPT_ROOT.'/templates_c';

$act=isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$action_arr=array('jc','ht');
if(in_array($act,$action_arr)){
  	require(OPT_ROOT."/plugins/".$act.".php");
}else{
	header("location:test.php");
   //echo "no access";
   exit;
}


