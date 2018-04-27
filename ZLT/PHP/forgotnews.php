<?php
$moduleid = 2;
require 'common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
//找回登录密码
	$action=='fg_password';
	if($action == 'fg_password'){
		  $_POST['step']='step1';
		  //第一步
		  if($_POST['step']=='step1'){
	          $mobile =$_POST['mobile'];
			  $user = $db->get_one("SELECT userid FROM {$DT_PRE}member WHERE mobile='$mobile'");
		  if(count($user)!=1){
			  if(count($user)==0){
			       $put = array('code' => -200,'msg' => '用户不存在！');
		           exit(json_encode($put)); 
			  }
			  $user = $db->get_one("SELECT userid FROM {$DT_PRE}member WHERE username='$mobile'");
              if(count($user)==0){
			       $put = array('code' => -200,'msg' => '用户不存在！');
		           exit(json_encode($put)); 
			  }
		  }
		       $auth = random(6, '0123456789');
               setcookie($mobile,md5($auth . '|' . $mobile), time()+60*5);
			   $content = lang('sms->sms_code', array($auth, $MOD['auth_days']*5)).$DT['sms_sign']; 
			   send_sms($mobile,$content);
			   $put = array('code' => 200,'msg' => 'ok');
		       exit(json_encode($put)); 
		  }
          //第二步
		  if($_POST['step']=='step2'){
			   $mobile =  $_POST['mobile']?$_POST['mobile']:'18819470607';
	           $code = $_POST['code']?$_POST['code']:'526431';
               $codecopy=md5($code . '|' . $mobile);
               $myphonecookie=$_COOKIE[$mobile];
		       //var_dump($myphonecookie,$codecopy);die;
		       if($codecopy!=$myphonecookie){
		            $put = array('code' => -201,'msg' => '验证码不正确');
		            exit(json_encode($put));
		       }else{
				    setcookie($mobile, "", time() - 3600);
			        $put = array('code' => 200,'msg' => 'ok');
		            exit(json_encode($put)); 
		       }  
		   }
          //第三步
		  if($_POST['step']=='step3'){
			  $mobile = $_POST['mobile'];
              $user = $db->get_one("SELECT userid,paysalt FROM {$DT_PRE}member WHERE mobile='$mobile' or username='mobile'");
              if(count($user)!=1){
			  $put = array('code' => -200,'msg' => '出现未知错误！');
		      exit(json_encode($put)); 
			  }
			  $userid=$user['userid'];
			  $password = $_POST['password']? $_POST['password'] : '654321';
		      $cpassword = $_POST['cpassword'] ? $_POST['cpassword'] : '654321';
          if(strlen($payword)<6)
		      {
		      $put = array('code' => -200,'msg' => '请输入六位数以上的密码');
		      exit(json_encode($put));
			  }
		  if($password!=$cpassword)
		      {
              $put = array('code' => -201,'msg' => '两个密码不一致');
		      exit(json_encode($put));
		      }
          $pass = dpassword($password, $user['paysalt']);
		  $db->query("UPDATE {$DT_PRE}member SET password='$pass' WHERE userid='$userid'");
		  //加密值存入数据库
		  $put = array('code' => 200,'msg'=>'ok');
		  exit(json_encode($put));  
		  }
	}

?>