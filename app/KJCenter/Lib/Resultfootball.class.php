<?php
/*
ץȡ������������
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
   *��500wanץȡ
   *www.500.com
   *����������ʼץȡ���
   */
   function from500(){
	 ;  
   }
   /*
   *��163ץȡ
   *caipiao.163.com
   *����������ʼץȡ���
   */
   function from163(){
	;
   }
   /*
   *�Ӿ���������ץȡ
   *www.sporttery.cn
   *�����Ĺ���������һ�����������2Сʱ���߸���
   */
   function fromjcw(){
    return 1;
   }
  
}