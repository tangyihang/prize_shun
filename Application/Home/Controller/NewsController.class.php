<?php
namespace Home\Controller;
use Think\Controller;
class NewsController extends Controller {
    public function index(){
    	$User=M('User');
    	$this->display();
    }
    public function user(){
     echo 2;
   }
}