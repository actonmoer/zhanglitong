<?php
//禁止越墙
/*$servername =$_SERVER['SERVER_NAME'];//本地服务器
$url_form = $_SERVER['HTTP_REFERER'];//来访地址
if($servername!=substr($url_form,7,strlen($servername))){
     die(json_encode(['msg'=>'禁止翻墙']));
}*/

$moduleid = 2;
require 'common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
if(!$_userid){
		   $put = array('code' => -200,'msg' => 'not_login');
		   exit(json_encode($put)); 
    }else{
		  $condition = "m.userid='$_userid'";
		  $condition2 = "userid='$_userid'";
		  $user = $db->get_one("SELECT * FROM {$DT_PRE}member m,{$DT_PRE}company c WHERE m.userid=c.userid AND $condition");
         }

   
    $action = $_POST['action'] ? $_POST['action'] : '';

    //action丢失
	if($action == ''){
	      $put = array('code' => -201,'msg' => 'action_miss');
		  exit(json_encode($put));
	 }  
	//个人主页
    if($action=='myhome'){
		  $data = array('username'=>$user['username'],'truename'=>$user['truename'],'money'=>$user['money'],'groupid'=>$user['groupid']);
          $put = array('code' => 200,'msg' => 'ok','data'=>$data);
		  exit(json_encode($put)); 
     }
    //账户信息
    if($action=='mycount'){
		  $data = array('username'=>$user['username'],'deposit'=>$user['deposit'],'credit'=>$user['credit'],'regid'=>$user['regid']);
          $put = array('code' => 200,'msg' => 'ok','data'=>$data);
		  exit(json_encode($put)); 
     }
    //个人资料
	if($action=='mymassage'){
		  $data = array('username'=>$user['username'],'truename'=>$user['truename'],'gender'=>$user['gender'],'sound'=>$user['sound']);
          $put = array('code' => 200,'msg' => 'ok','data'=>$data);
		  exit(json_encode($put)); 
     }
    //修改昵称
	if($action=='edit_truename'){
		  $truename = $_POST['truename'] ? $_POST['truename'] : 'luyangjian123';
		  if($truename == ''){
		  $truename = $user['username'];
		  }else{
		  $truename = $truename;
		  }
		  $db->query("UPDATE {$DT_PRE}member SET truename='$truename' WHERE $condition2");
          $put = array('code' => 200,'msg' => 'ok');
		  exit(json_encode($put)); 
     }
	//修改性别
	if($action=='edit_gender'){
		  $gender = $_POST['gender'] ? $_POST['gender'] : '1';
		  if($gender == ''){
		  $gender = $user['gender'];
		  }else{
		  $gender = $gender;
		  }
		  $db->query("UPDATE {$DT_PRE}member SET gender='$gender' WHERE $condition2");
          $put = array('code' => 200,'msg' => 'ok');
		  exit(json_encode($put)); 
     }
	//公司基本资料
	if($action=='show_company'){
		  //此处需要稍作修改
		  if($user['groupid']=='5' && $user['regid']=='5')
		  {
          $put = array('code' => 200,'msg' => 'personal_member');
		  exit(json_encode($put)); 
		  }else{
		  $data=array($user['opened'],$user['company'],$user['type'],$user['address'],$user['regyear'],$user['cates'],$user['mode'],$user['buy'],$user['sell'],$user['servicescope'],$user['servicetime']);
		  $put = array('code' => 200,'msg' => 'show_company','data'=>$data);
		  exit(json_encode($put));  
		  }		  
     }
	//公司详细资料
	if($action=='shows_company'){
		  if($user['groupid']=='5' && $user['regid']=='5')
		  {
          $put = array('code' => 200,'msg' => 'personal_member');
		  exit(json_encode($put)); 
		  }else{
		  $comi = $db->get_one("SELECT * FROM {$DT_PRE}company_data WHERE $condition2");
		  $data=array($user['size'],$user['regunit'],$user['thumb'],$user['business'],$comi['content']);
		  $put = array('code' => 200,'msg' => 'shows_company','data'=>$data);
		  exit(json_encode($put)); 
		  }
     }
	//公司联系方式
	if($action=='contact_company'){
		  if($user['groupid']=='5' && $user['regid']=='5')
		  {
          $put = array('code' => 200,'msg' => 'personal_member');
		  exit(json_encode($put)); 
		  }else{
		  $data=array($user['address'],$user['telephone'],$user['mail'],$user['postcode'],$user['homepage']);
		  $put = array('code' => 200,'msg' => 'contact_company','data'=>$data);
		  exit(json_encode($put)); 
		  }
     }
    //绑定银行卡
	if($action=='bind_bank_card'){
		  $bank = $_POST['bank'];
		  $banktype = $_POST['banktype'];
		  //$branch = $_POST['branch'];
		  $name = $_POST['name'];
		  $account = $_POST['account'];
		  $password = $_POST['password'];
		  is_payword($_username, $password) or exit(json_encode('psw_err'));
		  $db->query("UPDATE {$DT_PRE}member SET bank='$bank',banktype='$banktype',vbank='1',truename='$name',account='$account' WHERE $condition2");
		  $put = array('code' => 200,'msg' => '绑定成功');
		  exit(json_encode($put)); 
     }
	//显示银行卡
	if($action=='show_bank'){
		  if($user['vbank']=='1'){
		  $data=array('bank'=>$user['bank'],'banktype'=>$user['banktype'],'branch'=>$user['branch'],'truename'=>$user['truename'],'account'=>$user['account'],'vbank'=>$user['vbank']);
		  $put = array('code' => 200,'msg' => 'show_bank','data'=>$data);
		  exit(json_encode($put));
		  }else{
		  $put = array('code' => 200,'data'=>$user['vbank']);
		  exit(json_encode($put));
		  } 
     }
	//删除银行卡
	/*if($action=='show_bank_card'){
		  $data=array($user['address'],$user['telephone'],$user['mail'],$user['postcode'],$user['homepage']);
		  $put = array('code' => 200,'msg' => 'personal_member','data'=>$data);
		  exit(json_encode($put)); 
     }*/
	//新增收货地址
	if($action=='add_address'){
		  $mobile = $_POST['mobile'] ? $_POST['mobile'] : '';
		  $area = $_POST['area'] ? $_POST['area'] :'';
		  $truename = $_POST['truename'] ? $_POST['truename'] : '';
		  $address = $_POST['address'] ? $_POST['address'] : '';
		  $postcode = $_POST['postcode'] ? $_POST['postcode'] : '';
		  $listorder = $_POST['listorder'] ? $_POST['listorder'] :'';
		  if($mobile==''|| $address=='' || $postcode=='' || $area==''){
		      $put = array('code'=>-201,'msg'=>'massage_miss');
			  exit(json_encode($put));
		  }
		  $username = $user['username'];
          if($listorder == '1'){
		      $db->query("UPDATE {$DT_PRE}address SET listorder=0 WHERE listorder=1 and username = '$username'"); 
		      }
		  $address=$area.$address;
		  
          $db->query("INSERT  INTO {$DT_PRE}address (listorder,address,postcode,mobile,truename,username,addtime,edittime,editor) VALUES ('$listorder','$address','$postcode','$mobile','$truename','$username','$DT_TIME','$DT_TIME','$username')"); 
		  $put = array('code' => 200,'msg' => 'add_address');
		  exit(json_encode($put));  
     }
    //显示收货地址
	if($action=='show_address'){
		  $username = $_username;
		  $itemid=array();
		  $ad = $db->query("SELECT * FROM {$DT_PRE}address WHERE username='$username'"); 
          while($r = $db->fetch_array($ad))
		  {  
          $itemid[] = array('listorder'=>$r['listorder'],'itemid'=>$r['itemid'],'truename'=>$r['truename'],'mobile'=>$r['mobile'],'area'=>$r['area'],'address'=>$r['address'],'postcode'=>$r['postcode']);
		  //$truenane[] = $r['truename'];
		  //$mobile[] = $r['mobile'];
		  //$address[] = $r['address'];
          }
		  //$data =array('id'=>$itemid,'truename'=>$truename,'mobile'=>$mobile,'address'=>$address); 
		  $put = array('code' => 200,'msg' => 'show_address','data'=>$itemid);
		  exit(json_encode($put)); 
     }

    //显示单个收货地址
	if($action=='show_one_address'){
		  $username = $_username;
		  $itemid  = $_POST['itemid']?$_POST['itemid']:'65';
		  if($itemid == ''){
		       $put = array('code' => -201,'msg' => '数据丢失');
		       exit(json_encode($put)); 
		  }else{
		       $ad = $db->get_one("SELECT * FROM {$DT_PRE}address WHERE itemid='$itemid' AND username='$username'");
			   if(!$ad){
		            $put = array('code' => -202,'msg' => '地址不存在');
		            exit(json_encode($put)); 
			   }else{
				    $put = array('code' => 200,'msg' => 'show_address','data'=>$ad);
		            exit(json_encode($put));
			        }
		       }
     }
    //修改收货地址
	if($action=='edit_addressnew'){
		  $listorder = $_POST['listorder'] ? $_POST['listorder'] :'';
		  $itemid = $_POST['itemid']? $_POST['itemid'] : '';		  
		  if($itemid!=''){
		  $mobile = $_POST['mobile'] ? $_POST['mobile'] : '';
		  $address = $_POST['address'] ? $_POST['address'] : '';
		  $area = $_POST['area'] ? $_POST['area'] : '';
		  $postcode = $_POST['postcode'] ? $_POST['postcode'] : '';
		  $truename = $_POST['truename'] ? $_POST['truename'] :'';
		  $address = $area.$address;
          if($listorder == '1'){
		      $db->query("UPDATE {$DT_PRE}address SET listorder=0 WHERE listorder=1 and username = '$_username'"); 
		  }
          $db->query("UPDATE {$DT_PRE}address SET listorder='$listorder',mobile='$mobile',address='$address',postcode='$postcode',truename='$truename',username='$_username' WHERE itemid='$itemid'");
		  $put = array('code' => 200,'msg' => 'ok');
		  exit(json_encode($put)); 
		  }else{
		       $put = array('code' => -202,'msg' => 'id_miss');
		       exit(json_encode($put)); 
		       }
     }
	//删除收货地址
    if($action=='del_address'){
		  $itemid = $_POST['itemid'] ? $_POST['itemid'] : '321';
		  if($itemid == ''){
		       $put = array('code' => -202,'msg' => 'id_miss');
		       exit(json_encode($put));
		  }else{
			   $address=$db->get_one("SELECT * FROM {$DT_PRE}address WHERE itemid='$itemid'");

			   if(!$address){
			       $put = array('code' => -201,'msg' => 'address_unfind');
		           exit(json_encode($put));
			   }
			   $db->query("DELETE FROM {$DT_PRE}address WHERE itemid='$itemid'");
		       $put = array('code' => 200,'msg' => 'del_address',);
		       exit(json_encode($put)); 
		       }
     }
	//账号设置
    if($action=='set_account'){
		  $data=array('mobile'=>$user['mobile'],'telephone'=>$user['telephone'],'email'=>$user['email'],'vtruename'=>$user['vtruename'],'vcompany'=>$user['vcompany']);
		  $put = array('code' => 200,'msg' => 'personal_member','data'=>$data);
		  exit(json_encode($put)); 
     }
	//检测是否提交过身份认证信息
	if($action =='check_truename' ){
		  $username = $user['username'];
          $check = $db->get_one("SELECT count(*) as num FROM {$DT_PRE}validate WHERE username='$username' AND type='truename'");
		  if($check['num'] == 0){
		      $put = array('code' => 200,'msg' => 'ok');
	          exit(json_encode($put));  
		  }else{
		      $put = array('code' =>-200,'msg' => '您已经提交过认证信息，请耐心等待审核！');
	          exit(json_encode($put)); 
		  }
    }
	//身份认证
    if($action=='truename_certification'){
		  $username = $user['username'];
		  //控制访问
		  $check = $db->get_one("SELECT count(*) as num FROM {$DT_PRE}validate WHERE username='$username' AND type='truename'");
		  if($check['num'] != 0){
		      $put = array('code' =>-400,'msg' => '您已经提交过认证信息，请耐心等待审核！');
	          exit(json_encode($put)); 
		  }
		  $truename = $_POST['truename'];
		  //检测姓名
		  if(!$truename){
		      $put = array('code' => -202,'msg' => '真实姓名不能为空');
	          exit(json_encode($put));  
		  }
		  //检测证件
		  $thumb = $_POST['thumb'];
		  if(!$thumb){
		      $put = array('code' => -203,'msg' => '请上传图片');
	          exit(json_encode($put));   
		  }
		  $thumb1 = $_POST['thumb1']?$_POST['thumb1']:'';
		  $thumb2 = $_POST['thumb2']?$_POST['thumb2']:'';
		  $db->query("INSERT INTO {$DT_PRE}validate (type,username,ip,addtime,status,editor,edittime,title,thumb,thumb1,thumb2) VALUES ('truename','$username','$DT_IP','$DT_TIME','2','$username','$DT_TIME','$truename','$thumb','$thumb1','$thumb2')");
		  $put = array('code' => 200,'msg' => 'ok');
		  exit(json_encode($put)); 
     }
    //显示状态信息
    if($action =='s_truename' || $action =='s_company'){
		  if($action =='s_truename'){
              $show = $db->get_one("SELECT * FROM {$DT_PRE}validate WHERE username='$_username' AND type='truename'");
		  }else{
		      $show = $db->get_one("SELECT * FROM {$DT_PRE}validate WHERE username='$_username' AND type='company'");
		  }
          if(!$show){
			  $put = array('code' => -200,'msg' => '请先提交身份验证信息');
	          exit(json_encode($put)); 
		  }else{
			  $put=array('code' =>200,'name'=>$show['title'],'status'=>$show['status']);
	          exit(json_encode($put));
		     
		  }
    }
    //检测是否提交过企业认证信息
	if($action =='check_company' ){
		  $username = $user['username'];
          $check = $db->get_one("SELECT count(*) as num FROM {$DT_PRE}validate WHERE username='$username' AND type='company'");
		  if($check['num'] == 0){
		      $put = array('code' => 200,'msg' => '还未有没验证！',);
	          exit(json_encode($put));  
		  }else{
		      $put = array('code' =>-200,'msg' => '您已经提交过认证信息，请耐心等待审核！');
	          exit(json_encode($put)); 
		  }
    }
	
	//企业认证
    if($action=='enterprise_certification'){
	      
		  //检测是否个人会员
	      if($_groupid==5){
	          $put = array('code' => -201,'msg' => '您好，您是个人用户没权进行此操作！');
	          exit(json_encode($put));  
	      }
		  $username = $user['username'];
          //控制访问
		  $check = $db->get_one("SELECT count(*) as num FROM {$DT_PRE}validate WHERE username='$username' AND type='company'");
		  if($check['num'] != 0){
		      $put = array('code' =>-400,'msg' => '您已经提交过认证信息，请耐心等待审核！');
	          exit(json_encode($put)); 
		  }
		  $company = $_POST['company'];
		  //检测公司名
		  if(!$company){
		      $put = array('code' => -202,'msg' => '公司名不能为空');
	          exit(json_encode($put));  
		  }
		  //检测证件
		  $thumb = $_POST['thumb'];
		  if(!$thumb){
		      $put = array('code' => -203,'msg' => '请上传图片');
	          exit(json_encode($put));   
		  }
		  $thumb1 = $_POST['thumb1']?$_POST['thumb1']:'';
		  $thumb2 = $_POST['thumb2']?$_POST['thumb2']:'';
		  $db->query("INSERT INTO {$DT_PRE}validate (type,username,ip,addtime,status,editor,edittime,title,thumb,thumb1,thumb2) VALUES ('company','$username','$DT_IP','$DT_TIME','2','$username','$DT_TIME','$company','$thumb','$thumb1','$thumb2')");
		  $put = array('code' => 200,'msg' => 'ok');
		  exit(json_encode($put)); 
     }
    //获取手机号码
	if($action=='get_number'){
	  $mobile = $user['mobile'];
	  if($mobile==''){
	     $put = array('code' => -200,'msg' => '手机号不存在');
	     exit(json_encode($put)); 
	  }
	  $put = array('code'=>200,'msg'=>$mobile);
	  exit(json_encode($put));
	}
	//修改登录密码
    if($action=='edit_password'){
          $password = $_POST['password']? $_POST['password'] : '654321';
		  $cpassword = $_POST['cpassword'] ? $_POST['cpassword'] : '654321';
		  $oldpassword = $_POST['oldpassword'] ? $_POST['oldpassword'] : '123456';
		 
		  if($user['password'] != dpassword($oldpassword, $user['passsalt']))
			  {
		      $put = array('code' => -203,'msg' => 'oldpassword_err');
		      exit(json_encode($put)); 
		      }
          if(strlen($password)<6)
		      {
		      $put = array('code' => -204,'msg' => 'lenpassword_err');
		      exit(json_encode($put));
			  }
		  if($password!=$cpassword)
		      {
              $put = array('code' => -205,'msg' => 'twopassword_err');
		      exit(json_encode($put));
		      }
		  //加密值存入数据库
		  $pass = dpassword($password, $user['passsalt']);
		  $db->query("UPDATE {$DT_PRE}member SET password='$pass' WHERE userid='$_userid'");
		  $put = array('code' => 200,'msg' => 'personal_member','data'=>$data);
		  exit(json_encode($put)); 
     }
	//修改支付密码
    if($action=='edit_payword'){
          $payword = $_POST['payword']? $_POST['payword'] : '';
		  $cpayword = $_POST['cpassword'] ? $_POST['cpayword'] : '';
		  $oldpayword = $_POST['oldpayword'] ? $_POST['oldpayword'] : '';
		  if( $user['payword']!= dpayword($oldpayword, $user['paysalt']))
			  {
		      $put = array('code' => -203,'msg' => 'oldpayword_err');
		      exit(json_encode($put)); 
		      }
          if(strlen($password)!=6)
		      {
		      $put = array('code' => -204,'msg' => 'lenpayword_err');
		      exit(json_encode($put));
			  }
		  if($payword!=$cpayword)
		      {
              $put = array('code' => -205,'msg' => 'twopayword_err');
		      exit(json_encode($put));
		      }
          $payss = dpassword($payword, $user['paysalt']);
		  $db->query("UPDATE {$DT_PRE}member SET payssword='$pass' WHERE userid='$_userid'");
		  //加密值存入数据库
		  $put = array('code' => 200,'msg' => 'personal_member','data'=>$data);
		  exit(json_encode($put));
	}
    //找回支付密码
	
	if($action == 'fg_payword'){
		  //第一步
		  if($_POST['step']=='step1'){
	          $mobile =$user['mobile'];
		  //if($mobile!=$user['mobile']){
		      //$put = array('code' => -200,'msg' => '请输入正确的手机号！');
		      //exit(json_encode($put)); 
		  //}else{
		       $auth = random(6, '0123456789');
               setcookie($mobile,md5($auth . '|' . $mobile), time()+60*5);
			   $content = lang('sms->sms_code', array($auth, $MOD['auth_days']*2)).$DT['sms_sign']; 
			   $sms_code = send_sms($mobile,$content);
               if(strpos($sms_code, $DT['sms_ok']) !== false) {
				     $put = array('code' => 200,'msg' => 'ok');
		             exit(json_encode($put));
				}else{
				     $put = array('code' => -200,'msg' => '短信发送失败！');
		             exit(json_encode($put));	
				} 
			    
		  }
          //第二步
		  if($_POST['step']=='step2'){
	           $code = $_POST['code']?$_POST['code']:'526431';
               $codecopy=md5($code . '|' . $user['mobile']);
               $myphonecookie=$_COOKIE[$user['mobile']];
		       //var_dump($myphonecookie,$codecopy);die;
		       if($codecopy!=$myphonecookie){
		            $put = array('code' => -201,'msg' => '验证码不正确');
		            exit(json_encode($put));
		       }else{
				    setcookie($user['mobile'], "", time() - 3600);
			        $put = array('code' => 200,'msg' => 'ok');
		            exit(json_encode($put)); 
		       }  
		   }
          //第三步
		  if($_POST['step']=='step3'){
		      $payword = $_POST['payword'];
		      $cpayword = $_POST['cpayword'];
		  if(!$payword or !$cpayword)
			  {
		      $put = array('code' => -200,'msg' => '请输入密码');
		      exit(json_encode($put));
		      }
          if(strlen($payword)!=6)
		      {
		      $put = array('code' => -201,'msg' => '请输入六位数的支付密码');
		      exit(json_encode($put));
			  }
		  if($payword!=$cpayword)
		      {
              $put = array('code' => -202,'msg' => '两个密码不一致');
		      exit(json_encode($put));
		      }
          $payss = dpassword($payword, $user['paysalt']);
		  $db->query("UPDATE {$DT_PRE}member SET payword='$payss' WHERE userid='$_userid'");
		  //加密值存入数据库
		  $put = array('code' => 200,'msg'=>'ok');
		  exit(json_encode($put));  
		  }
	}
    //我的订单
    if($action == 'my_order'){
		  //外卖订单
          //根据时间戳判断未付款订单,更新数据库
	      $result  = $db->query("SELECT order_id,addtime FROM {$DT_PRE}takeout_order WHERE buyer='$_userid' AND status='1'");
	      while($r = $db->fetch_array($result)){
		  $addtime = $DT_TIME-$r['addtime'];
		  $orderid = $r['order_id'];
		  if($addtime > 60*60*12){
		       $db->query("UPDATE {$DT_PRE}takeout_order SET status=8 ,updatetime='$DT_TIME',refund_reason='买家超时未付款，系统自动关闭！' WHERE  order_id='$orderid' AND buyer='$_userid'");
			  }
			}
          $order= array();
	      $result = $db->query("SELECT o.order_id,o.status,m.thumb,m.take_out_shop_name as name,o.addtime,o.actual_payment,'takeout' as namestatus,m.id FROM {$DT_PRE}takeout_order o,{$DT_PRE}takeout_message m WHERE o.buyer='$_userid' AND o.restaurant_id=m.id");
          while($r = $db->fetch_array($result)) {
          $order[] = $r;
          }
          //商场订单
          $mallorder = array();
          $result = $db->query("SELECT o.itemid as order_id,o.status,c.thumb,c.company as name,o.addtime,o.amount as actual_payment,'mall' as namestatus   FROM {$DT_PRE}mall_order o,{$DT_PRE}company c WHERE buyer='$_username' AND o.seller=c.username");
          while($r = $db->fetch_array($result)) {
          $mallorder[] = $r;
          }
		  $allorder = array_merge($order,$mallorder);
		  $allorderpx = array();
          foreach ($allorder as $v) {
          $allorderpx[] = $v['addtime'];
           }
          array_multisort($allorderpx, SORT_DESC, $allorder);
          exit(json_encode($allorder));
		  //array_multisort(array_column($allorder,'addtime'),SORT_DESC,$allorder);
          exit(json_encode($allorder));
	}
    //订单详情
    if($action == 'smy_order'){
	      $namestatus =$_POST['namestatus']?$_POST['namestatus']:'mall';
		  $order_id = $_POST['order_id']?$_POST['order_id']:'5115';
          //exit(json_encode($order_id));
		  if($order_id==''){
		     $put = array('code' => -200,'msg'=>'订单号为空！');
		     exit(json_encode($put));
		  }
          if($namestatus==''){
		     $put = array('code' => -201,'msg'=>'未能识别订单');
		     exit(json_encode($put));
		  }else if($namestatus=='takeout'){
			 $order = $db->get_one("SELECT restaurant_id,status,fee,amount-actual_payment as favourable_price,actual_payment,note,addtime FROM {$DT_PRE}takeout_order WHERE buyer='$_userid' AND order_id='$order_id'");
             if(!$order){
				  $put = array('code' => -202,'msg'=>'订单不存在');
		          exit(json_encode($put));
			 }else{
			      $resid = $order['restaurant_id'];
                  $res = $db->get_one("SELECT contact FROM {$DT_PRE}takeout_message WHERE id='$resid'");
			      $contact = $res['contact'];
			 }  
		     $orderdata= array();
	         $result = $db->query("SELECT dishes_name,goods_number,sell_price FROM  {$DT_PRE}takeout_data  WHERE  order_id='$order_id'"); 
             while($r = $db->fetch_array($result)) {
             $orderdata[] = $r;
             }
             $order = array('orderdata'=>$orderdata)+$order;
			 $order = array('phone'=>$contact)+$order;
             $put = array('code' =>200,'msg' => 'ok','data'=>$order);
	         exit(json_encode($put));
		  }else if($namestatus=='mall'){
	         $order= $db->get_one("SELECT buyer,seller,status,title,number,fee,fee_name,price-amount as favourable_price,amount,price as actual_payment,note,addtime  FROM {$DT_PRE}mall_order WHERE itemid='$order_id'");
			 if($order['buyer']!=$_username){
				  $put = array('code' =>-401,'msg' => '非法操作');
	              exit(json_encode($put));
			 }
			 $orderdata = array(array('dishes_name'=>$order['title'],'goods_number'=>$order['number'],'sell_price'=>$order['amount']));
			 $seller = $order['seller'];
			 $com = $db->get_one("SELECT telephone FROM {$DT_PRE}company WHERE username = '$seller'" );
			 $phone = $com['telephone'];
			 $order = array('orderdata'=>$orderdata)+$order;
			 $order = array('phone'=>$phone)+$order;
             $put = array('code' =>200,'msg' => 'ok','data'=>$order);
	         exit(json_encode($put));
		  }else{
		     $put = array('code' => -400,'msg'=>'非法操作');
		     exit(json_encode($put));
		  
		  }
         
	}	
//买家确认收货
if($action == 'order_ok'){
    $order_id=$_POST['order_id']?$_POST['order_id']:'takeout-201708011629082';
    $namestatus =$_POST['namestatus']?$_POST['namestatus']:'takeout';
	if($order_id==''){
	     $put = array('code' =>-201,'msg' => '订单号不存在');
	     exit(json_encode($put));
	}
    if($namestatus==''){
	     $put = array('code' => -200,'msg'=>'未能识别订单');
		 exit(json_encode($put));
	}else if($namestatus=='takeout'){
	     $order = $db->get_one("SELECT * FROM {$DT_PRE}takeout_order WHERE order_id='$order_id'");
		 if(!$order){
                $put = array('code' =>-202,'msg' => '订单不存在');
	            exit(json_encode($put));
	            }
		 //添加外卖ID，明天弄
		 $seller = $order['seller'];
		 $res = $db->get_one("SELECT * FROM {$DT_PRE}takeout_message WHERE userid='$seller'");
		 if($order['status']=='4'){
			    $res_id = $res['id'];
			    $db->query("UPDATE {$DT_PRE}takeout_order SET status=5 WHERE  order_id='$order_id' AND buyer ='$_userid'");
				$sql1 = "(order_id,restaurant_id,status,note,editor,addtime)";
                $sql2 = "('$order_id','$res_id','5','订单完成','$_username','$DT_TIME')";
                $db->query("INSERT INTO {$DT_PRE}takeout_order_status $sql1 VALUES $sql2");
				$put = array('code' =>200,'msg' => 'ok');
	            exit(json_encode($put));
		        }else{
				$put = array('code' =>-204,'msg' => '出现未知错误');
	            exit(json_encode($put));
				}
	}else if($namestatus=='mall'){
	     $order = $db->get_one("SELECT * FROM {$DT_PRE}takeout_order WHERE itemid='$order_id'");
		 if(!$order){
                $put = array('code' =>-202,'msg' => '订单不存在');
	            exit(json_encode($put));
	            }
		 if($order['status']=='3'){
			    $db->query("UPDATE $table6 SET status=4,updatetime=$DT_TIME WHERE  itemid='$order_id' AND buyer ='$_username'");
				$put = array('code' =>200,'msg' => 'ok');
	            exit(json_encode($put));
		        }else{
				$put = array('code' =>-204,'msg' => '出现未知错误');
	            exit(json_encode($put));
				}  
	  }
}
//订单状态仅限外卖
if($action=='show_order_status'){
	    $order_id=$_POST['order_id']?$_POST['order_id']:'';
        $namestatus =$_POST['namestatus']?$_POST['namestatus']:'';
        if($namestatus==''){
		     $put = array('code' => -201,'msg'=>'未能识别订单');
		     exit(json_encode($put));
		  }else if($namestatus=='takeout'){
		     $orderstatus=array();
             $result = $db->query("SELECT * FROM  {$DT_PRE}takeout_order_status  WHERE  order_id='$order_id'"); 
             while($r = $db->fetch_array($result)) {
             $orderstatus[] = array('status'=>$r['status'],'addtime'=>$r['addtime'],'note'=>$r['note']);
             }
             $put = array('code' =>200,'msg' => 'ok','data'=>$orderstatus);
	         exit(json_encode($put));
		  }else if($namestatus){
		     $order= $db->get_one("SELECT status,note,addtime  FROM {$DT_PRE}mall_order WHERE buyer='$_username' AND  itemid='$order_id'");
             $put = array('code' =>200,'msg' => 'ok','data'=>$order);
	         exit(json_encode($put));
		   
		   }	   

}


//申请退款
if($action == 'backout_order'){
    $order_id = $_POST['order_id']?$_POST['order_id']:'takeout_201708011629082';
    $namestatus = $_POST['namestatus']?$_POST['namestatus']:'takeout';
	$buyer_reason = $_POST['buyer_reason']?$_POST['buyer_reason']:'123456';
	if($order_id==''){
	     $put = array('code' =>-201,'msg' => '订单号不存在');
	     exit(json_encode($put));
	}
    if($namestatus==''){
	     $put = array('code' => -200,'msg'=>'未能识别订单');
		 exit(json_encode($put));
	}else if($namestatus=='takeout'){
	     $order = $db->get_one("SELECT * FROM {$DT_PRE}takeout_order WHERE order_id='$order_id'");
		 if(!$order){
                $put = array('code' =>-202,'msg' => '订单不存在');
	            exit(json_encode($put));
	            }
		 //添加外卖ID，明天弄
		 $seller = $order['seller'];
		 $res = $db->get_one("SELECT * FROM {$DT_PRE}takeout_message WHERE userid='$seller'");
		 if($order['status']=='2' || $order['status']=='3'){
			    $res_id = $res['id'];
			    $db->query("UPDATE {$DT_PRE}takeout_order SET status=7,buyer_reason='$buyer_reason' WHERE  order_id='$order_id' AND buyer ='$_userid'");
				$sql1 = "(order_id,restaurant_id,status,note,editor,addtime)";
                $sql2 = "('$order_id','$res_id','7','申请退款','$_username','$DT_TIME')";
                $db->query("INSERT INTO {$DT_PRE}takeout_order_status $sql1 VALUES $sql2");
				$put = array('code' =>200,'msg' => 'ok');
	            exit(json_encode($put));
		        }else{
				$put = array('code' =>-204,'msg' => '出现未知错误');
	            exit(json_encode($put));
				}
	}else if($namestatus=='mall'){
	     $order = $db->get_one("SELECT * FROM {$DT_PRE}takeout_order WHERE itemid='$order_id'");
		 if(!$order){
                $put = array('code' =>-202,'msg' => '订单不存在');
	            exit(json_encode($put));
	            }
		 if($order['status']=='2' || $order['status']=='3'){
			    $db->query("UPDATE $table6 SET status=5,updatetime=$DT_TIME WHERE  itemid='$order_id' AND buyer ='$_username'");
				$put = array('code' =>200,'msg' => 'ok');
	            exit(json_encode($put));
		        }else{
				$put = array('code' =>-204,'msg' => '出现未知错误');
	            exit(json_encode($put));
				}  
	  }
}
//验证订单
if($action =='show_comment'){
	 if(!$_POST['order_id'] || !$_POST['namestatus']){
	         $put = array('code' =>-200,'msg' => '数据丢失');
	         exit(json_encode($put));
	 }else{
     $order_id = $_POST['order_id'];
     $namestatus =  $_POST['namestatus'];
	 }
	 if($namestatus=='takeout'){
		   $order = $db->get_one("SELECT o.restaurant_id,o.buyer,o.seller,m.take_out_shop_name,m.thumb FROM {$DT_PRE}takeout_order o,{$DT_PRE}takeout_message m WHERE o.order_id='$order_id' AND o.restaurant_id = m.id");
		   if(!$order){
                  $put = array('code' =>-201,'msg' => '订单不存在，没法评论');
	              exit(json_encode($put));
		   }else{ 
			      $put = array('code' =>200,'msg' => 'ok','data'=>$order);
	              exit(json_encode($put));
		   }
	 }else if($namestatus=='mall'){
		   $order = $db->get_one("SELECT o.mallid as restaurant_id,o.buyer,o.seller,c.company,c.thumb FROM {$DT_PRE}mall_order o,{$DT_PRE}company c WHERE o.itemid='$order_id' AND o.seller=c.usernmae ");
           if(!$order){
                  $put = array('code' =>-201,'msg' => '订单不存在，没法评论');
	              exit(json_encode($put));
		   }else{ 
			      $put = array('code' =>200,'msg' => 'ok','data'=>$order);
	              exit(json_encode($put));
		   }

	 }else{
	        $put = array('code' =>-400,'msg' => '非法操作');
	        exit(json_encode($put));
	 }
	


}

//评论订单
if($action == 'buyer_comment'){
	 if(!$_POST['restaurant_id'] || !$_POST['seller'] || !$_POST['buyer'] || !$_POST['seller_star'] || !$_POST['seller_comment'] || !$_POST['namestatus'] || !$_POST['order_id']){
	         $put = array('code' =>-200,'msg' => '数据丢失');
	         exit(json_encode($put));
	 }else{  
		     $order_id = $_POST['order_id']?$_POST['order_id']:'';
			 $restaurant_id = $_POST['restaurant_id']?$_POST['restaurant_id']:'';
			 $seller = $_POST['seller']?$_POST['seller']:'';
			 $buyer = $_POST['buyer']?$_POST['buyer']:'';
			 $seller_star = $_POST['seller_star']?$_POST['seller_star']:'';
			 $seller_comment = $_POST['seller_comment']?$_POST['seller_comment']:'';
	 }
	 if($_POST['namestatus'] == 'takeout'){
		     $order = $db->get_one("SELECT * FROM {$DT_PRE}takeout_order WHERE order_id= '$order_id'");
		     if($order['status']==6){
			 $put = array('code' =>-202,'msg' => '您已经评论过该订单了！');
	         exit(json_encode($put));  
			 }
			 $sql1 = "(restaurant_id,seller,buyer,seller_star,seller_comment,seller_ctime)";
			 $sql2 = "('$restaurant_id','$seller','$buyer','$seller_star','$seller_comment','$DT_TIME')";
			 $db->query("INSERT INTO {$DT_PRE}takeout_comment $sql1 VALUES $sql2");
			 $db->query("UPDATE {$DT_PRE}takeout_order SET status=6 WHERE order_id = '$order_id '");
             $put = array('code' =>200,'msg' => 'ok');
	         exit(json_encode($put));
	 }else if ($_POST['namestatus'] == 'mall'){
		     /*$order = $db->get_one("SELECT * FROM {$DT_PRE}takeout_order WHERE itemid = '$order_id'");
		     if($order['status']==6){
			 $put = array('code' =>-202,'msg' => '您已经评论过该订单了！');
	         exit(json_encode($put));  
			 }*/
	         $sql1 = "(mallid,seller,buyer,note,editor,addtime)";
			 $sql2 = "('$restaurant_id','$seller','$buyer','$seller_star','$seller_comment','$DT_TIME')";
			 $db->query("INSERT INTO {$DT_PRE}mall_comment $sql1 VALUES $sql2");
             $put = array('code' =>200,'msg' => 'ok');
	         exit(json_encode($put));
	 }else{
	         $put = array('code' =>-201,'msg' => '出现未知错误');
	         exit(json_encode($put));
	 }			 
}
//删除订单
if($action=='del_order_id'){
	 if(!$_POST['order_id'] || !$_POST['namestatus']){
	         $put = array('code' =>-200,'msg' => '数据丢失');
	         exit(json_encode($put));
	 }
     $order_id = $_POST['order_id']?$_POST['order_id']:'';
	 $namestatus =  $_POST['namestatus']?$_POST['namestatus']:'';
	 if($namestatus=='takeout'){
	         $order =$db->get_one("SELECT * FROM {$DT_PRE}takeout_order WHERE order_id= '$order_id'");
			 if($order['buyer'] != $_userid ){
			        $put = array('code' =>-404,'msg' => '非法操作');
	                exit(json_encode($put));
			 }else{
				    $buyer = $_userid.'注：已删';
			        $db->query("UPDATE {$DT_PRE}takeout_order SET buyer='$buyer' WHERE order_id = '$order_id '");
					$put = array('code' =>200,'msg' => '删除成功！');
	                exit(json_encode($put));
			 }
	 
	 }else if($namestatus=='mall'){
	         $order =$db->get_one("SELECT * FROM {$DT_PRE}takeout_order WHERE itemid= '$order_id'");
             if($order['buyer'] != $_username ){
			        $put = array('code' =>-404,'msg' => '非法操作');
	                exit(json_encode($put));
			 }else{
				    $buyer = $_username.'注：已删';
			        $db->query("UPDATE {$DT_PRE}mall_order SET buyer='$buyer' WHERE order_id = '$order_id '");
					$put = array('code' =>200,'msg' => '删除成功！');
	                exit(json_encode($put));
			 }
			 
	 }else{
	         $put = array('code' =>-201,'msg' => '出现未知错误');
	         exit(json_encode($put));
	 
	 }  

}
//检测商铺
if($action == 'check_shop'){
	$res = $db->get_one("SELECT * FROM {$DT_PRE}takeout_message WHERE userid=$_userid");
    if($res!=false){
		$check = 1;
	}else{
		$check = 0;
	}
    $put = array('code' =>200,'msg' =>$check);
	exit(json_encode($put));
}

//账号金额
if($action=='show_money'){
	$money=$user['money'];
	$put = array('code' => 200,'msg'=>'show_money','data'=>$money);
	exit(json_encode($put));
}

//充值记录
if($action=='recharge_record'){
	$order=array();
	$username = $_username;
	$result = $db->query("SELECT itemid,amount,fee,sendtime,receivetime,status FROM {$DT_PRE}finance_charge where username = '$username'");
          while($row = $db->fetch_array($result))
		  {  
            $order[]=$row;
          } 
          if(!$order){
	          $put = array('code' => -202,'msg' => 'false');
	          exit(json_encode($put));
		  }else{
              $put = array('code' =>200,'msg' => 'recharge_record','data'=>$order);
	          exit(json_encode($put));
	      }
   }    


//充值详细记录
if($action=='show_one_recharge'){	
	$itemid=$_POST['itemid']?$_POST['itemid']:"";
	if($itemid == ''){
		       $put = array('code' => -201,'msg' => '数据丢失');
		       exit(json_encode($put)); 
		  }else{
	$username = $_username;
	$order=array();	 
	$result = $db->get_one("SELECT f.itemid,f.bank,f.amount,f.fee,f.sendtime,f.receivetime,f.status,f.note,m.money FROM {$DT_PRE}finance_charge f,{$DT_PRE}member m where itemid='$itemid' AND m.username='$username'");
            if(!$result){
	            $put = array('code' => -202,'msg' => 'false');
	            exit(json_encode($put));
	        }else{
                $put = array('code' =>200,'msg' => 'show_one_recharge','data'=>$result);
                exit(json_encode($put));
            }
     }   
}
//账号明细
if($action=='account_detail'){
	$order=array();
	$username = $_username;
	$result = $db->query("SELECT itemid,username,amount,balance,addtime,reason FROM {$DT_PRE}finance_record where username = '$username' order by addtime DESC");
            while($row = $db->fetch_array($result))
		    {  
            $order[]=$row;
            }
            if(!$order){
		        $put = array('code' => -202,'msg' => 'false');
		        exit(json_encode($put)); 
	        }else{
                $put = array('code' =>200,'msg' => 'account_detail','data'=>$order);
	            exit(json_encode($put));
	        }
}


//账号明细详细
if($action=='show_one_account'){
		  $username = $_username;
		  $itemid = $_POST['itemid']?$_POST['itemid']:'';
		  if($itemid == ''){
		       $put = array('code' => -201,'msg' => '数据丢失');
		       exit(json_encode($put)); 
		  }else{
		       $result = $db->get_one("SELECT f.itemid,f.username,f.bank,f.amount,f.addtime,f.reason,m.money FROM {$DT_PRE}finance_record f,{$DT_PRE}member m WHERE itemid='$itemid' AND m.username='$username'");
			   if(!$result){
		            $put = array('code' => -202,'msg' => 'false');
		            exit(json_encode($put)); 
			   }else{
				    $put = array('code' => 200,'msg' => 'show_one_account','data'=>$result);
		            exit(json_encode($put));
			   }		   
	     }
     }



?>