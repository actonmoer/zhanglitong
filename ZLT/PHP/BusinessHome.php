<?php
//禁止越墙
/*$servername =$_SERVER['SERVER_NAME'];//本地服务器
$url_form = $_SERVER['HTTP_REFERER'];//来访地址
if($servername!=substr($url_form,7,strlen($servername))){
     die(json_encode(['msg'=>'禁止翻墙']));
}*/
require 'common.inc.php';
require_once '../include/post.func.php';
$table = $DT_PRE . "takeout_message";
$table2 = $DT_PRE . "takeout_opentime";
$table3 = $DT_PRE . "takeout_category";
$table5 = $DT_PRE . "takeout_dishes";
//检测是否登录
if(!$_userid){
	$put = array('code' => -200,'msg' => 'not_login');
	exit(json_encode($put));  
 }else{ 
	    //检测是否个人会员
	    if($_groupid==5){
	          $put = array('code' => -201,'msg' => '您好，您是个人用户没权进行此操作！');
	          exit(json_encode($put));  
	         }
	    /*if($_vtruename!=1){
	          $put = array('code' => -202,'msg' => '请先完成实名认证！');
	          exit(json_encode($put)); 
	         }*/
	    //检测是否验证公司
	    /*if($_vcompany!=1){
	          $put = array('code' => -203,'msg' => '请先完成公司认证！');
	          exit(json_encode($put)); 
	         }*/
        //检测是否开了餐馆
        $res = $db->get_one("SELECT * FROM $table WHERE userid=$_userid");
       }


$action = $_POST['action'] ? $_POST['action'] : 'send_sms';
//检测是否有商铺
if($action == 'check_company_yn'){
 if(!$res){
		   $put = array('code' => -202,'msg' => 'not_res','data'=>$_company);
		   exit(json_encode($put));
          }
}
//action丢失
if($action == ''){
    $put = array('code' => -201,'msg' => 'action_miss');
	exit(json_encode($put));    
}
//调试接口
if($action=='test'){
    $test= $_POST['test'] ? $_POST['test'] : '接口测试';
	exit(json_encode($test)); 
}
//显示外卖店铺
if($action == 'show'){
    if(!$res){
	    $put = array('code' =>-200,'msg' => '店铺不存在');
	    exit(json_encode($put)); 
	}
    $restaurant_id = $res['id'];
	$opentime = array();
	$result = $db->query("SELECT begun_time,over_time FROM $table2 WHERE restaurant_id='$restaurant_id'");
    while($r = $db->fetch_array($result)) {
    $opentime[] = $r;
    }
	//var_dump($pentime);die;
    $data= array($res['take_out_shop_name'],$res['business'],$res['contact'],$res['province'].$res['city'].$res['area'].$res['address'],$res['shop_tip'],$res['thumb']);
	$put = array('code' =>200,'msg' => 'ok','data'=>$data,'time'=>$opentime);
	exit(json_encode($put)); 
}
//改变营业状态
if($action == 'change_business'){
	if($res['business']==1){
	$db->query("UPDATE $table SET business=2 WHERE userid=$_userid");
    $put = array('code' =>200,'msg' => 'ok','business'=>2);
	exit(json_encode($put));
	}
	if($res['business']==2){
    $db->query("UPDATE $table SET business=1 WHERE userid=$_userid");
    $put = array('code' =>200,'msg' => 'ok','business'=>1);
	exit(json_encode($put));
	}
    
}

//创建外卖商铺步骤一
if($action == 'create_home' ){
	//商铺名
	if($_company == null){
	     $put = array('code' =>-201,'msg' => '公司不存在');
	     exit(json_encode($put));
	}
	$company  =  $_POST['company'] ? $_POST['company'] : 'luyangjian';
	if($company != $_company){
         $put = array('code' =>-201,'msg' => '请输入正确的商铺名称');
	     exit(json_encode($put));
	}
	//销售额
	$month_sell =  $_POST['month_sell'] ? $_POST['month_sell'] : '0';
	//联系电话
    $contact = $_POST['contact'] ? $_POST['contact'] :'18819470607';
    if ( $contact =='') {
		$put = array('code' =>-204,'msg' => '请输入正确的联系电话');
	    exit(json_encode($put));
	}
    //商铺介绍
	$shop_tip = $_POST['shop_tip']?$_POST['shop_tip'] : 'testte';
    if ($shop_tip ==''){
		$put = array('code' =>-205,'msg' => '店铺介绍不能为空');
	    exit(json_encode($put));
	}else if(strlen($shop_tip) <6){
	    $put = array('code' =>-206,'msg' => '店铺介绍不能少于6个字');
	    exit(json_encode($put));
	}
    //商铺地址
	$province = $_POST[ 'province' ]?$_POST[ 'province' ]:'';
	$city = $_POST[ 'city' ]?$_POST['city']:'';
	$area = $_POST[ 'area' ]?$_POST[ 'area' ]:'';
	$address = $_POST['address']?$_POST['address']:'';
    $areaid = $_POST['areaid']?$_POST['areaid']:'';
	if($province=='' || $city=='' || $address==''){
	    $put = array('code' =>-209,'msg' => '请输入地址');
	    exit(json_encode($put));
	}
    //经纬度 
	$lng = $_POST['lng'] ? $_POST['lng'] : '';
	$lat = $_POST['lat'] ? $_POST['lat'] : '';
    if ( $lng=='' || $lat=='') {
		 $put = array('code' =>-203,'msg' => '请获取经纬度');
	     exit(json_encode($put));
	}
    $thumb2 = $_POST['thumb2'] ? $_POST['thumb2'] : '';
	$thumb_banner1 = $_POST['thumb_banner1'] ? $_POST['thumb_banner1'] : '';
	if($thumb2==''){
	     $put = array('code' =>-212,'msg' => '请选择商家头像');
	     exit(json_encode($put));
	}
	$sql1="(take_out_shop_name,address,province,city,area,contact,thumb,thumb_banner1,month_sell_count,shop_tip,addtime,edittime,userid,status,business,areaid,lng,lat)";
	$sql2="('$company','$address','$province','$city','$area','$contact','$thumb2','$thumb_banner1','$month_sell','$shop_tip','$DT_TIME','$DT_TIME','$_userid','2','2','$areaid','$lng','$lat')";
	$status = $db->query("INSERT INTO $table $sql1 VALUES $sql2");
	//营业时间
	$beguntime = $_POST['beguntime'] ? $_POST['beguntime'] : '';
    $overtime = $_POST['overtime'] ? $_POST['overtime'] : '';
    if ( $beguntime == '' || $overtime == '') {
		 $put = array('code' =>-202,'msg' => '请设置时间');
	     exit(json_encode($put));
	}
    
    $res = $db->get_one("SELECT * FROM $table WHERE userid=$_userid");
	if(!$res){
	      $put = array('code' =>-211,'msg' => '出现未知错误');
	      exit(json_encode($put));
	}
	$id = $res['id'];
	$sql2="";
    foreach ($beguntime as $key => $value) {
		  $tmp = $overtime[$key];
		  $sql2 .= "('$id','$value:00','$tmp:00',$DT_TIME),";
		}
		
        $sql1 = "(restaurant_id,begun_time,over_time,add_time)";
		if ($sql2 != ''){
		    $db->query("DELETE FROM $table2 WHERE restaurant_id = $id");
			$sql2 = trim($sql2,',');
			$db->query("insert into $table2 $sql1 VALUES $sql2");
		}
	    die("<script>alert('正在前往设置商铺');window.location.href='BusinessHome/CreateHome-step02.html?company=$company'</script>");
}
//分类属性
if($action=='show_category'){
    $category = array();
    $result = $db->query("SELECT id,category_name FROM $table3");
    while($r = $db->fetch_array($result)) {
    $category[] = $r;
   }
   exit(json_encode($category));
}
//创建外卖商铺步骤二
if($action == 'create_home2' ){
   //检测步骤一是否成功
   if(!$res){
	    $put = array('code' =>-202,'msg' => 'err_res');
	    exit(json_encode($put)); 
	 }
   //提交数据
	$distribution_price = $_POST['distribution_price']?$_POST['distribution_price']:'';
    if($distribution_price == ''){
	     $put = array('code' =>-201,'msg' => '请填写配送费用');
	     exit(json_encode($put));
	}
	$delivery_time = $_POST['delivery_time']?$_POST['delivery_time']:'';
	if($delivery_time == ''){
	     $put = array('code' =>-203,'msg' => '请填写配送时间');
	     exit(json_encode($put));
	}
	  
	$start_price = $_POST['start_price']?$_POST['start_price']:'';
    if($delivery_time == ''){
	     $put = array('code' =>-204,'msg' => '请填写起送价格');
	     exit(json_encode($put));
	}
	$catid = $_POST['catid']?$_POST['catid']:'';
	if($catid == ''){
	     $put = array('code' =>-205,'msg' => '请选择所属分类');
	     exit(json_encode($put));
	}
    $radius = $_POST['radius']?$_POST['radius']:'';

	$db->query("UPDATE $table SET distribution_price='$distribution_price',delivery_time = '$delivery_time' ,start_price='$start_price',radius='$radius' WHERE userid='$_userid'");

	$put = array('code' =>200,'msg' => 'ok');
	exit(json_encode($put));
	}

//编辑外卖商铺
if($action == 'edit_home'){   
    $restaurant_id = $res['id'];
	$opentime = array();
	$result = $db->query("SELECT begun_time,over_time FROM $table2 WHERE restaurant_id=$restaurant_id");
    while($r = $db->fetch_array($result)) {
    $opentime[] = $r;
    }
	$put = array('code' =>200,'msg' => 'ok','data'=>$res,'time'=>$opentime);
	echo json_encode($put); 
	//传值更新
}

//添加菜式
$table4 = $DT_PRE . "takeout_cuisine_category";
if($action == 'add_cuisine'){
  $cuisine = $_POST['cuisine'];
  if(!$cuisine){
    $put = array('code' =>-201,'msg' => '请输入菜式');
	exit(json_encode($put)); 
  }else{
    $add_cuisine= $db->get_one("SELECT * FROM $table4 WHERE user_id='$_userid' AND cuisine='$cuisine'");
    if($add_cuisine){
    $put = array('code' =>-202,'msg' => 'same');
	exit(json_encode($put)); 
    }
	$id = $res['id'];
    $sql1 = "(takeout_id,user_id,cuisine,edittime)";
    $sql2 = "('$id','$_userid','$cuisine','$DT_TIME')";
    $cuisine = $db->query("INSERT INTO $table4 $sql1 VALUES $sql2");
    $put = array('code' =>200,'msg' => 'ok');
	exit(json_encode($put)); 
  }
}

//展示菜式
if($action == 'show_cuisine'){
    $cuisine = array();
	$result = $db->query("SELECT id,cuisine,edittime FROM $table4 WHERE user_id=$_userid");
    while($r = $db->fetch_array($result)) {
    $cuisine[] = $r;
    }
    $put = array('code' =>200,'msg' => 'ok','data'=>$cuisine);
	exit(json_encode($put));
}
//修改菜式
if($action == 'edit_cuisine'){
  $cuisine = $_POST['cuisine'];
  $id = $_POST['id'];
  if(!$id){
    $put = array('code' =>-202,'msg' => '不知道哪一个是需要修改的菜式');
	exit(json_encode($put));  
  }
  if(!$cuisine){
    $put = array('code' =>-201,'msg' => '请选择修改的菜式');
	exit(json_encode($put)); 
  }else{
	$add_cuisine= $db->get_one("SELECT * FROM $table4 WHERE user_id='$_userid' AND cuisine='$cuisine'");
    if($add_cuisine){
    $put = array('code' =>-202,'msg' => 'same');
	exit(json_encode($put));
	}
	$db->query("UPDATE $table4 SET cuisine='$cuisine',edittime='$DT_TIME' WHERE id=$id");
    $put = array('code' =>200,'msg' => 'ok','time'=>$cuisine);
	exit(json_encode($put));
  }
}
//删除菜式，删除后连同其下级菜单也一并删除
if($action == 'del_cuisine'){
    $id = $_POST['id'];
    if(!$id){
    $put = array('code' =>-201,'msg' => '请选择要删除的菜式');
	exit(json_encode($put));    
    }else{
	$cuisine = $db->get_one("SELECT * FROM $table4 WHERE id=$id");
	if(!$cuisine || $cuisine['user_id'] != $_userid){
        $put = array('code' =>-222,'msg' => '非法操作');
		exit(json_encode($put));
	    }
		$db->query("DELETE FROM $table4 WHERE id=$id");
		$db->query("DELETE FROM $table5 WHERE cuisine_id='$id' AND user_id ='$_userid' ");
		$put = array('code' =>200,'msg' => 'ok');
		exit(json_encode($put));
	} 
}
//添加菜单
if($action == 'add_dishes'){

    $name = $_POST['name'];
	if(!$name){
	    $put = array('code' =>-200,'msg' => '请填写商品名');
		exit(json_encode($put));  
	}

	$cuisine= $_POST['cuisine'];
	if(!$cuisine){
	    $put = array('code' =>-201,'msg' => '请选择商品分类');
		exit(json_encode($put));
	}
    $check = $db->get_one("SELECT * FROM $table5 WHERE user_id ='$_userid' AND cuisine_id='$cuisine' AND dishes='$name'");
	if($check){
	    $put = array('code' =>-400,'msg' => '商品名重复');
		exit(json_encode($put));
	}
    $thumb = $_POST['thumb'];
	if(!$thumb){
	    $put = array('code' =>-202,'msg' => '请上传菜式图');
	    exit(json_encode($put)); 
	}
	$isup = $_POST['isup'];
	if($isup != 0 && $isup != 1){
	    $put = array('code' =>-203,'msg' => '参数错误，p');
	    exit(json_encode($put)); 
	}
    //开启关闭多规格
	$is_spec = $_POST['is_spec'];
	if($is_spec !=0 && $is_spec !=1 ){
	    $put = array('code' =>-204,'msg' => '参数错误，c');
	    exit(json_encode($put)); 
	}
    if($is_spec == 1){
	    $spec_attr = $_POST['spec_attr'];
        $spec_price = $_POST['spec_price'];
        $specStr='';
	    foreach ($spec_attr as $k=>$v){
	    $specStr.=$v.'|'.$spec_price[$k].',';
	    }
	    $specStr=substr($specStr,0,strlen($specStr)-1);
		}else{
		$specStr = '';
		}

	 $price = $_POST['price'] ? $_POST['price'] : '';
	 $sell_price = $_POST['sell_price'] ? $_POST['sell_price'] : '';
     if($sell_price == ''){
	    $put = array('code' =>-204,'msg' => '请填写销售价格');
		exit(json_encode($put));  
	 }
     $takeout_id = $res['id'];
	 $sql1 = "(takeout_id,cuisine_id,user_id,dishes,thumb,price,sell_price,isup,is_spec,spec_attr,addtime,edittime)";
	 $sql2 = "('$takeout_id','$cuisine','$_userid','$name','$thumb','$price','$sell_price','$isup','$is_spec','$specStr','$DT_TIME','$DT_TIME')";
	 $status = $db->query("INSERT INTO $table5 $sql1 VALUES $sql2");
     if($status){
	      $put = array('code' =>200,'msg' => 'ok');
          exit(json_encode($put));
     }else{
	      $put = array('code' =>-200,'msg' => '出现未知错误');
          exit(json_encode($put));
	     }
}
//展示全部菜单
if($action == 's_dishes'){
    $dishes= array();
	$result = $db->query("SELECT d.is_spec,d.thumb,c.cuisine,d.dishes,d.addtime,d.edittime,d.isup,d.use_count,d.sell_price,d.id FROM $table4 c,$table5 d WHERE d.user_id='$_userid' AND c.id=d.cuisine_id  order by d.edittime ASC");
    while($r = $db->fetch_array($result)) {
    $dishes[] = $r;
    }
    $put = array('code' =>200,'msg' => 'ok','data'=>$dishes);
	exit(json_encode($put));  
}
//展示一个菜单
if($action == 'so_dishes'){
    $id = $_POST['id'];
	$result = $db->get_one("SELECT * FROM $table5 WHERE id=$id");
	$spec_attr = $result['spec_attr'];
	$a=explode(",",$spec_attr);
	$b=array();
    foreach($a as $u){
    $b[] = explode("|",$u);
    }
	$put = array('code' =>200,'msg' => 'ok','data'=>$result,'data2'=>$b);
	exit(json_encode($put));
	}
//修改菜单
if($action == 'e_dishes'){
	//对应ID
    $id = $_POST['id']?$_POST['id']:'';
	if($id==''){
	    $put = array('code' =>-200,'msg' => '不知道修改的商品');
		exit(json_encode($put)); 
	}
	//修改名称
    $name = $_POST['name'];
	if(!$name){
	    $put = array('code' =>-201,'msg' => '请填写商品名');
		exit(json_encode($put));  
	}
    //修改分类
	$cuisine= $_POST['cuisine'];
	if(!$cuisine){
	    $put = array('code' =>-202,'msg' => '请选择商品分类');
		exit(json_encode($put));
	}
	//控制名称，不可重复。
	$check = $db->get_one("SELECT count(*) as num FROM $table5 WHERE user_id ='$_userid' AND cuisine_id='$cuisine' AND dishes='$name'");
	if($check['num']>1){
	    $put = array('code' =>-400,'msg' => '商品名重复');
		exit(json_encode($put));
	}
    //修改图片
    $thumb = $_POST['thumb'];
	if(!$thumb){
	    $put = array('code' =>-203,'msg' => '请上传菜式图');
	    exit(json_encode($put)); 
	}
    //上架下架
	$isup = $_POST['isup'];
	if($isup != 0 && $isup != 1){
	    $put = array('code' =>-204,'msg' => '参数错误，p');
	    exit(json_encode($put)); 
	}
    //开启关闭多规格
	$is_spec = $_POST['is_spec'];
	if($is_spec !=0 && $is_spec !=1 ){
	    $put = array('code' =>-205,'msg' => '参数错误，c');
	    exit(json_encode($put)); 
	}

    if($is_spec == 1){
	    $spec_attr = $_POST['spec_attr'];
        $spec_price = $_POST['spec_price'];
        $specStr='';
	    foreach ($spec_attr as $k=>$v){
	    $specStr.=$v.'|'.$spec_price[$k].',';
	    }
	    $specStr=substr($specStr,0,strlen($specStr)-1);
		}else{
		$specStr = '';
		}

	$price = $_POST['price'] ? $_POST['price'] : '';
	$sell_price = $_POST['sell_price'];
    if(!$sell_price){
	    $put = array('code' =>-206,'msg' => '请填写销售价格');
		exit(json_encode($put));  
	 }

    $db->query("UPDATE $table5 SET thumb='$thumb',cuisine_id='$cuisine',dishes='$name',price='$price',sell_price='$sell_price',edittime='$DT_TIME',is_spec='$is_spec',spec_attr='$specStr',isup='$isup' WHERE user_id ='$_userid' AND id='$id' ");

	//添加菜单时间
	$beguntime = $_POST['beguntime'];
    $overtime = $_POST['overtime'];
	if($beguntime && $overtime){
       $sql2="";
       foreach ($beguntime as $key => $value) {
		    $tmp = $overtime[$key];
		    $sql2 .= "('$id','$value:00','$tmp:00',$DT_TIME),";
	        }
            $sql1 = "(dishes_id,begun_time,over_time,add_time)";
	   if ($sql2 != ''){
		    $table8 = $DT_PRE . "takeout_dishes_opentime";
		    $db->query("DELETE FROM $table8 WHERE dishes_id = $id");
	        $sql2 = trim($sql2,',');
		    $db->query("insert into $table8 $sql1 VALUES $sql2");
	     }
	 }
	 $put = array('code' =>200,'msg' => 'ok');
     exit(json_encode($put));
}
//删除菜单
if($action == 'd_dishes'){
    $id = $_POST['id'];
    if(!$id){
    $put = array('code' =>-201,'msg' => '请选择要删除的菜单');
	exit(json_encode($put));     
    }else{
	$dishes = $db->get_one("SELECT * FROM $table5 WHERE id='$id'");
	if(!$dishes || $dishes['user_id'] != $_userid){
        $put = array('code' =>-222,'msg' => '非法操作');
	    exit(json_encode($put));
	    }
		$db->query("DELETE FROM $table5 WHERE id='$id'");
		$put = array('code' =>200,'msg' => 'ok');
	    exit(json_encode($put));
	} 
}
//上架与下架
if($action == 'c_dishes'){
    $isup = $_POST['isup']?$_POST['isup']:'0';
	$id = $_POST['id']?$_POST['id']:'13391';
	$dishes = $db->get_one("SELECT * FROM $table5 WHERE id='$id'");
	if(!$dishes){
	     $put = array('code' =>-222,'msg' => '非法操作');
	     exit(json_encode($put));
	}
	if($isup == '0'){
	     $db->query("UPDATE $table5 SET isup=1 WHERE user_id ='$_userid' AND id='$id' ");
		 $put = array('code' =>200,'msg' => 'ok','status'=>'1');
	     exit(json_encode($put));
	}else if($isup == '1'){
	     $db->query("UPDATE $table5 SET isup=0 WHERE user_id ='$_userid' AND id='$id' ");
		 $put = array('code' =>200,'msg' => 'ok','status'=>'0');
	     exit(json_encode($put));
	}else{
	     $put = array('code' =>-222,'msg' => '非法操作');
	     exit(json_encode($put));
	}
}
//新订单/待配送/已发货/已完成/无效单
//1待支付2支付成功3商家接单4卖家发货5已确认，未评价6订单完成7退款中8订单关闭
$table6 = $DT_PRE . "takeout_order";
$table7 = $DT_PRE . "takeout_data";
$table9 = $DT_PRE . "takeout_order_status";
if($action == 'send_sms'){
	send_message(18819470607, 'test', 'test');
    send_sms(18819470607, '你有已付款订单，点击查看 http://www.zhanglitong.com/member/trade.php');
	die;
}
if($action == 'n_order'){
	$resid = $res['id'];
    //根据时间戳判断未付款订单,更新数据库
	$result = $db->query("SELECT order_id,addtime FROM $table6 WHERE restaurant_id='$resid' AND status='1'");
	while($r = $db->fetch_array($result)) {
    $addtime = $DT_TIME-$r['addtime'];
	$orderid = $r['order_id'];
	if($addtime > 60*60*12){
	   $db->query("UPDATE $table6 SET status=8 ,updatetime='$DT_TIME',refund_reason='买家超时未付款，系统自动关闭！' WHERE  order_id='$orderid' AND restaurant_id ='$resid'");
	}
    }
    $order= array();
	$result = $db->query("SELECT order_id,amount,buyer_name,buyer_phone,buyer_address,note,addtime,status FROM $table6 WHERE restaurant_id='$resid'");
    while($r = $db->fetch_array($result)) {
    $order[] = $r;
    }
    $allorderpx = array();
    foreach ($order as $v){
    $allorderpx[] = $v['addtime'];
	}
    array_multisort($allorderpx, SORT_DESC, $order);   
    exit(json_encode($order));
}
//接单
if($action == 'g_order'){
    $order_id=$_POST['orderid']?$_POST['orderid']:'takeout-201707221138121';
	$res_id = $res['id'];
	if(!$order_id){
	     $put = array('code' =>-200,'msg' => '非法操作');
	     exit(json_encode($put));
	}else{
	     $order = $db->get_one("SELECT * FROM $table6 WHERE order_id='$order_id'");
         
		 if(!$order){
                $put = array('code' =>-201,'msg' => '订单不存在');
	            exit(json_encode($put));
	            }
		 if($order['status']=='2'){
			    $db->query("UPDATE $table6 SET status=3 WHERE  order_id='$order_id' AND restaurant_id ='$res_id'");

				$sql1 = "(order_id,restaurant_id,status,note,editor,addtime)";
                $sql2 = "('$order_id','$res_id','3','商家接单成功','$_username','$DT_TIME')";
                $db->query("INSERT INTO $table9 $sql1 VALUES $sql2");
		        
				$put = array('code' =>200,'msg' => 'ok');
	            exit(json_encode($put));

		        }else{
				$put = array('code' =>-202,'msg' => '出现未知错误');
	            exit(json_encode($put));
				}
	    }
}
//配送
if($action == 't_order'){
    $order_id=$_POST['orderid'];
	$res_id = $res['id'];
	if(!$order_id){
	     $put = array('code' =>-200,'msg' => '非法操作');
	     exit(json_encode($put));
	}else{
	     $order = $res = $db->get_one("SELECT * FROM $table6 WHERE order_id='$order_id'");
         if(!$order){
                $put = array('code' =>-201,'msg' => '订单不存在');
	            exit(json_encode($put));
	            }
		 if($order['status']=='3'){
			    $db->query("UPDATE $table6 SET status=4 WHERE  order_id='$order_id'");
				$sql1 = "(order_id,restaurant_id,status,note,editor,addtime)";
                $sql2 = "('$order_id','$res_id','4','商品配送中','$_username','$DT_TIME')";
                $db->query("INSERT INTO $table9 $sql1 VALUES $sql2");
		        $put = array('code' =>200,'msg' => 'ok');
	            exit(json_encode($put));
		        }else{
				$put = array('code' =>-202,'msg' => '出现未知错误');
	            exit(json_encode($put));
				}
	    }
}
//订单详细
if($action == 's_order'){
    $order_id=$_POST['orderid']?$_POST['orderid']:'takeout-201707221113336';
	$resid = $res['id'];
    $order= array();
	$result = $db->query("SELECT o.order_id,o.status,o.amount-o.actual_payment as favourable_price,o.fee,d.dishes_name,d.goods_number,sell_price,o.fee,o.amount,o.buyer_name,o.buyer_phone,o.buyer_address,o.note,o.addtime FROM $table6 o,$table7 d WHERE o.restaurant_id='$resid' AND o.order_id='$order_id' AND d.order_id=o.order_id"); 
    while($r = $db->fetch_array($result)) {
    $order[] = $r;
    }
    exit(json_encode($order));
}

//订单评论
 $tables = $DT_PRE . "takeout_comment";
 if($action == 's_comment'){
    $restaurant_id=$res['id'];
	$com=array();
	$result = $db->query("SELECT * FROM  $tables WHERE restaurant_id='$restaurant_id'"); 
    while($r = $db->fetch_array($result)) {
    $com[] = $r;
    }
	$put = array('code' =>200,'msg' =>$com );
	exit(json_encode($put));
 }
 //回复评论
 if($action=='r_comment'){
	$id = $_POST['id']?$_POST['id']:'';
	$content = $_POST['content']?$_POST['content']:'';
    if($id==''){
	   $put = array('code' =>-201,'msg' =>'信息丢失!' );
	   exit(json_encode($put));
	}
    if($content=='' || strlen($content)<8){
	   $put = array('code' =>-202,'msg' =>'内容不能为空');
	   exit(json_encode($put));
	}
    $db->query("UPDATE $tables SET seller_rtime=$DT_TIME,seller_reply='$content'  WHERE  id='$id'");
	$put = array('code' =>200,'msg' =>'ok' );
	exit(json_encode($put));
 }
 ?>