<?php
namespace Home\Model;
use Think\Model;
class ResultModel extends Model
{	
	protected $fields = array('lotteryid','lotttime','ballid','source','half_score','full_score','match_starttime','result','addtime','status');
	protected $tableName = 'tab_lottery_result';
}
