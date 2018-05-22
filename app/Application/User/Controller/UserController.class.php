<?php
namespace Home\Controller;
use Think\Template\Driver\Mobile;
use Think\Controller;
class UserController extends Controller{
  //$User=new D("User");
   public function index()
   {
   	 if(!session('auth'))
   	 {
   	    $this->redirect('/home/user/login');
   	 }else{
   	    $User = M('User');

   	 }
   	  $this->display('touzhujilu');
   }
   public function login()
   {
   	 if($_REQUEST)
   	 {
   	 	$username=$_REQUEST['username'];
   	 	$passwd=md5($_REQUEST['passwd']);
        $User = M('User');
        $userid=$User->where("username='".$username."' and passwd='".$passwd."'")->getField('userid');
        if($userid){
            session('auth',$userid);
            $this->redirect('/home/user/');
        }
   	 }
      $this->display();
   }
   public function register(){
     if($_REQUEST)
     {

        $passwd=$_REQUEST['passwd'];
        $vpasswd=$_REQUEST['vpasswd'];
        if($passwd=='' || $_REQUEST['username']==''){
            $this->redirect('/home/user/register');
         }

        $User=M('User');

        if($User->where("username='".$_REQUEST['username']."'")->getField('userid')){
             $this->redirect('/home/user/register');
        }

        if($passwd != $vpasswd)
        {
           $msg = 'password error';
           $this->assign('msg',$msg);
        }else{
           $data=array(
              'username'=> $_REQUEST['username'],
              'passwd' => md5($_REQUEST['passwd'])
            );
            if($result=$User->add($data)){
               $userid=$User->where("username=".$data['username'])->getField('userid');
            }
          session('auth',$userid);
     	$this->redirect('/home/user');
        }
     }
      $this->display();
   }
   public function  logout()
   {
     ;
   }
}