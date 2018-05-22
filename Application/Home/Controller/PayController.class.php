<?php
/*支付功能*/
namespace Home\Controller;
use Think\Controller;
class PayController extends Controller {
    public function index()
    {
         $Pay = new \Org\Util\Ifz();
         //echo $Pay->get_code();
    }
    public function alipay()
    {
      echo 1;

    }
    public function response(){
       echo 1;
    }
}