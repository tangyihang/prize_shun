<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>登录</title>
<link href="/assets/admin/view/css/take.css" rel="stylesheet" type="text/css" />
</head>
<script language="javascript"  src="/assets/admin/view/js/jquery1.4.2.js"></script>
        <script language="javascript">
            $(document).ready(function(){
                var msg = '<?php echo ($msg); ?>';
                if(msg!=''){
                    alert(msg);
                }

                $("#imageVerify").click( function() { //alert('dd');
                    var timestamp = (new Date()).valueOf();
                    $("#imageVerify").attr({ src:"<?php echo U('home/user/verify');?>&timestamp="+timestamp , alt: "验证码" });
                });

                $("#sub").click(function(){
                    if($("#username").val()==''){
                        alert('用户名不能为空');
                        return false;
                    }

                    if($("#password").val()==''){
                        alert('密码不能为空！');
                        return false;
                    }
                    if($("#verify").val()==''){
                        alert('验证码不能为空！');
                        return false;
                    }
                    $("#register").submit();
                });

                $("body").bind('keyup',function(event){
                if (event.keyCode == 13)
                {
                	if($("#username").val()==''){
                        alert('用户名不能为空');
                        return false;
                    }
                    if($("#password").val()==''){
                        alert('密码不能为空！');
                        return false;
                    }
                    if($("#verify").val()==''){
                        alert('验证码不能为空！');
                        return false;
                    }
                    $("#register").submit();
                }
            });
            })
        </script>
<body>
    <div class="take_mange">
      <div class="take_cont">
        <div class="take_name"><img src="/assets/admin/view/images/name_03.jpg"></div>
        <div class="take_form">
          <p>Date: <?php echo ($date); ?></p>
          <form action="<?php echo U('home/user/checklogin');?>" method="post" id="register">
          <ul class="login_m">
             <li class="wole_com">欢迎登录</li>
             <li class="admin_name"><input id="username" name="username" type="text" value=""></li>
             <li class="admin_pass"><input id="password" name="password" type="password" value=""></li>
             <li class="checking">
               <div class="chek_input"><input id="verify" type="text" name="vcode" onblur=" if(this.value == '') { this.value = '请输入验证码'; }" onfocus="if(this.value == '请输入验证码') { this.value = ''; }" value="请输入验证码"></div>
               <div class="change_ipc"><img id="imageVerify" src="<?php echo U('home/user/verify');?>" width="90" height="30"></div>
          
             </li>
             <!--<li class="input_word">输入上图文字</li>-->
             <li class="login_butt" id="sub"><a href="#"></a></li>
          </ul>
          <form>
        </div>
      </div>
    </div>
</body>
</html>