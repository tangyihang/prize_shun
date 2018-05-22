<?php
/**
 * 全部玩法名称定义 
 * @param $caizhong 2015.03
 */

$caizhong = array(
	'0' => array(
		'name' => '双色球',
		'value' => '1'		
		),
	'1' => array(
		'name' => '3D',
		'value' => '2'		
		),
	'2' => array(
		'name' => '七乐彩',
		'value' => '3'		
		),
	'3' => array(
		'name' => '超级大乐透',
		'value' => '281'		
		),
	'4' => array(
		'name' => '排列三',
		'value' => '283'		
		),
	'5' => array(
		'name' => '排列五',
		'value' => '284'		
		),
	'6' => array(
		'name' => '七星彩',
		'value' => '282'		
		),
	'7'=>array(
		'name' => '胜负彩',
		'value' => '324'
		),
	'8'=>array(
		'name' => '胜负彩任九场',
		'value' => '441'
		),
	'9'=>array(
		'name' => '半全场',
		'value' => '326'
	),
	'10'=>array(
		'name' => '四场进球',
		'value' => '325'
	)		
);

/**
 * 足彩玩法名称定义
 * @param $caizhong 2015.03
 */
$cz_zucai = array(
		'0'=>array(
				'name' => '胜负彩',
				'value' => '324'
		),
		'1'=>array(
				'name' => '胜负彩任九场',
				'value' => '441'
		),
		'2'=>array(
				'name' => '半全场',
				'value' => '326'
		),
		'3'=>array(
				'name' => '四场进球',
				'value' => '325'
		)
);

/**
 * 玩法状态
 * @param $play_status 2015.03
 */
$play_status = array(
		'0'=>array(
				'name' => '预销售',
				'value' => '-1'
		),
		'1'=>array(
				'name' => '正常销售',
				'value' => '0'
		),
		'2'=>array(
				'name' => '临时关闭',
				'value' => '1'
		),
		'3'=>array(
				'name' => '进入封期',
				'value' => '2'
		),
		'4'=>array(
				'name' => '开奖公告录入完成',
				'value' => '3'
		),
		'5'=>array(
				'name' => '进行开奖算奖',
				'value' => '4'
		),
		'6'=>array(
				'name' => '算奖完成',
				'value' => '5'
		),
		'7'=>array(
				'name' => '进入进行派奖',
				'value' => '6'
		),
		'8'=>array(
				'name' => '派奖完成',
				'value' => '7'
		)
);

/**
 * 玩法奖等
 * @param $cz_grade 2015.03
 */
$cz_grade = array(
	'0' => array(
		'name' => '一等奖',
		'levelid' => '1',
		'appendid' => '11',
		'basic_z' => 'one_z',
		'basic_j' => 'one_j',
		'append_z' => 'onezj_z',
		'append_j' => 'onezj_j'
	),
	'1' => array(
		'name' => '二等奖',
		'levelid' => '2',
		'appendid' => '12',
		'basic_z' => 'two_z',
		'basic_j' => 'two_j',
		'append_z' => 'twozj_z',
		'append_j' => 'twozj_j'
	),
	'2' => array(
		'name' => '三等奖',
		'levelid' => '3',
		'appendid' => '13',
		'basic_z' => 'three_z',
		'basic_j' => 'three_j',
		'append_z' => 'threezj_z',
		'append_j' => 'threezj_j'
	),
	'3' => array(
		'name' => '四等奖',
		'levelid' => '4',
		'appendid' => '14',
		'basic_z' => 'four_z',
		'basic_j' => 'four_j',
		'append_z' => 'fourzj_z',
		'append_j' => 'fourzj_j'
	),
	'4' => array(
		'name' => '五等奖',
		'levelid' => '5',
		'appendid' => '15',
		'basic_z' => 'five_z',
		'basic_j' => 'five_j',
		'append_z' => 'fivezj_z',
		'append_j' => 'fivezj_j'
	),
	'5' => array(
		'name' => '六等奖',
		'levelid' => '6',
		'appendid' => '16',
		'basic_z' => 'six_z',
		'basic_j' => 'six_j',
		'append_z' => 'sixzj_z',
		'append_j' => 'sixzj_j'
	),
	'6' => array(
		'name' => '七等奖',
		'levelid' => '7',
		'appendid' => '17',
		'basic_z' => 'seven_z',
		'basic_j' => 'seven_j',
		'append_z' => 'sevenzj_z',
		'append_j' => 'sevenzj_j'
	),
	'7'=>array(
		'name' => '八等奖',
		'levelid' => '8',
		'appendid' => '18',
		'basic_z' => 'eight_z',
		'basic_j' => 'eight_j',
		'append_z' => 'eightzj_z',
		'append_j' => 'eightzj_j'
	),
	'8'=>array(
		'name' => '九等奖',
		'levelid' => '9',
		'appendid' => '19',
		'basic_z' => 'nine_z',
		'basic_j' => 'nine_j',
		'append_z' => 'ninezj_z',
		'append_j' => 'ninezj_j'
	),
	'9'=>array(
		'name' => '十等奖',
		'levelid' => '10',
		'appendid' => '20',
		'basic_z' => 'ten_z',
		'basic_j' => 'ten_j',
		'append_z' => 'tenzj_z',
		'append_j' => 'tenzj_j'
	)
);

/**
 * 玩法奖等个数及追号个数
 * @param $cz_grade_type 2015.03
 */
$cz_grade_type = array(
	'1' => array('num' => '6', 'appendnum' => '0,0,0,0,0,0', 'playid' => '118', 'levelvalue' => array('3' => '3000', '4' => '200', '5' => '10', '6' => '5'), 'appendvalue' => array()),
	'2' => array('num' => '3', 'appendnum' => '0,0,0', 'playid' => '116', 'levelvalue' => array('1' => '1040', '2' => '346', '3' => '173'), 'appendvalue' => array()),
	'3' => array('num' => '7', 'appendnum' => '0,0,0,0,0,0,0', 'playid' => '117', 'levelvalue' => array('4' => '200', '5' => '50', '6' => '10', '7' => '5'), 'appendvalue' => array()),
	'281' => array('num' => '6', 'appendnum' => '1,1,1,1,1,0', 'playid' => '106', 'levelvalue' => array('4' => '200', '5' => '10', '6' => '5'), 'appendvalue' => array('14' => '100', '15' => '5')),
	'283' => array('num' => '3', 'appendnum' => '0,0,0', 'playid' => '100', 'levelvalue' => array('1' => '1040', '2' => '346', '3' => '173'), 'appendvalue' => array()),
	'284' => array('num' => '1', 'appendnum' => '0', 'playid' => '102', 'levelvalue' => array('1' => '100000'), 'appendvalue' => array()),
	'282' => array('num' => '6', 'appendnum' => '0,0,0,0,0,0', 'playid' => '103', 'levelvalue' => array('3' => '1800', '4' => '300', '5' => '20', '6' => '5'), 'appendvalue' => array()),
	'324' => array('num' => '2', 'appendnum' => '0,0', 'playid' => '108', 'levelvalue' => array(), 'appendvalue' => array()),
	'441' => array('num' => '1', 'appendnum' => '0', 'playid' => '109', 'levelvalue' => array(), 'appendvalue' => array()),
	'326' => array('num' => '1', 'appendnum' => '0', 'playid' => '110', 'levelvalue' => array(), 'appendvalue' => array()),
	'325' => array('num' => '1', 'appendnum' => '0', 'playid' => '111', 'levelvalue' => array(), 'appendvalue' => array())
);



?>