<?php
/*
抓取竞彩足球赛果
@author PaulHE
287568970@qq.com
*/
include_once ROOT.'/Lib/Base.class.php';
class Resultfootball extends Base{
    public $url;
    public $charset = 'GBK';
   	public $formatData  = array();
	public $DB;
   function __construct($url='')
   {
     $this->url=$url;
   }
   /*
   *从500wan抓取
   *www.500.com
   *比赛结束后开始抓取入库
   */
   function from500(){
	 ;  
   }
   /*
   *从163抓取
   *caipiao.163.com
   *比赛结束后开始抓取入库
   */
   function from163(){
	;
   }
   /*
   *从竞彩网官网抓取
   *www.sporttery.cn
   *官网的公布的数据一般比赛结束后2小时或者更晚
   */
   function fromjcw(){
    return 1;
   }
  
}