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
		    'fontSize'    =>    25,    // ��֤�������С
		    'length'      =>    4,     // ��֤��λ��
		    'useNoise'    =>    false, // �ر���֤���ӵ�
		    'useCurve'    =>    false, // �ر���֤���ӵ�
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
    	 $this->error(iconv('gbk','utf8','��֤�����'),U('login')); 
    }
	    $rows=$Model->where($where)->find();
		if( $pwd==$rows['password'] && $rows)
		{
			session('username',$_POST['username']);
			$this->success(iconv('gbk','utf8','��¼�ɹ�'),__APP__.'/Home/');
		}else{
			$this->error(iconv('gbk','utf8','�ʺ��������'),U('login'));
		}
	}
  function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
   }
   
   public function  logout()
   {
         Session::destroy();
		 $this->success(iconv('gbk','utf8','�˳��ɹ�'),__APP__.'index.php/Home/User/login');
   }
}