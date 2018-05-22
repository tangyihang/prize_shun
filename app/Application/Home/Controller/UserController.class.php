<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller{
   public function index()
   {
   	 if(!session('auth'))
   	 {
   	    $this->redirect('home/user/login');
   	 }else{
   	    $User = M('User');

   	 }
   	  $this->display('touzhujilu');
   }
 public function login(){
 	  header("Pragma: no-cache");
	  $date = date('d/m/Y');
	  $this->assign('date',$date);
      $this->display('login');	
 }
 //
  public function verify()
  {
  	 $config =    array(
		    'fontSize'    =>    25,    // 验证码字体大小
		    'length'      =>    4,     // 验证码位数
		    'useNoise'    =>    false, // 关闭验证码杂点
		    'useCurve'    =>    false, // 关闭验证码杂点
      );
  	  $Verify = new \Think\Verify($config);
      $Verify->entry();
  }
   
 public function checklogin()
 {
 	    import('ORG.Util.Session');
		$Model=M('User');
		$where['username']=trim($_POST['username']);
		$pwd=md5(trim($_POST['password']));
		$vcode=$_POST['vcode'];
		
    if(!$this->check_verify($vcode))
    {
    	 $this->error(iconv('gbk','utf8','验证码错误'),U('login')); 
    }
	    $rows=$Model->where($where)->find();
		if( $pwd==$rows['password'] && $rows)
		{
			session('username',$_POST['username']);
			$this->success(iconv('gbk','utf8','登录成功'),__APP__.'/Home/');
		}else{
			$this->error(iconv('gbk','utf8','帐号密码错误'),U('login'));
		}
	}
  function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
   }
   
   public function  logout()
   {
         Session::destroy();
		 $this->success(iconv('gbk','utf8','退出成功'),__APP__.'index.php/Home/User/login');
   }
}