<?php

require 'common.inc.php';
require 'img_api.php';

require DT_ROOT . "/testurl.php";
require DT_ROOT . "/include/module.func.php";
require DT_ROOT . "/include/cache.func.php";
require_once '../include/post.func.php';
require DT_ROOT . '/module/member/member.class.php';
$mobiletype = get_env('mobile');
$act = $_GET['act'];
$db2->update("restaurant_waiter", ["oid"=>0]);

 


if ($act == '') {
    $function_name = "show_mes";
} else {
    $function_name = $act;
}





call_user_func($function_name);

//餐位实时管理
function show_desk() {
    //showerror();
    global $db2;
    global $_userid;
    session_start();
    if (isset($_SESSION['waiter_id'])) {
        $_userid = $_SESSION['_userid'];
    } else if (!$_userid) {
        die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
    }
    $rid = $db2->get("restaurant", "id", ["userid" => $_userid]);
    $desks = $db2->select("restaurant_desk", ["id", "desk", "is_use", "desk_id"], ["restaurant_id" => $rid]);
    include template('reserve_desk', 'mobile');
}
//包房实时管理
function show_room() {
    global $db2;
    global $_userid;
    session_start();
    if (isset($_SESSION['waiter_id'])) {
        $_userid = $_SESSION['_userid'];
    } else if (!$_userid) {
        die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
    }
    $rid = $db2->get("restaurant", "id", ["userid" => $_userid]);
    $rooms = $db2->select("room_message", ["id", "room_name", "is_use"], ["restaurant_id" => $rid]);
    include template('reserve_room', 'mobile');
}
//订单列表
function order_list() {
    //showerror();
    global $db2;
    global $_userid;
    session_start();
    if (isset($_SESSION['waiter_id'])) {
        $_userid = $_SESSION['_userid'];
    } else if (!$_userid) {
        die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
    }
    $status = $_GET['status'];
    $map = [];
    if ($status == "ok") {
        $map["status"] = [6, 7];
    } else if ($status > 0) {
        $map["status"] = $status;
    }
    $rid = $db2->get("restaurant", "id", ["userid" => $_userid]);
    $map["restaurant_id"] = $rid;
	$map["ORDER"] = ["addtime"=>"DESC"];
    $data = $db2->select("restaurant_reserve_orders", "*", $map);
    foreach ($data as $k => $v) {
        $rd_arr[]=$v['id'];
		$str=explode('-',$v['order_id']);
		$data[$k]['order_id'] = $str[1];
        //$v['buyer']=$db2->get("member","turename",["userid"=>$v['buyer_uid']]);
        if ($v['status'] == 1) {
            $data[$k]['status_text'] = '待支付';
        }
        if ($v['status'] == 2) {
            $data[$k]['status_text'] = '支付成功';
        }
        if ($v['status'] == 3) {
            $data[$k]['status_text'] = '进行中';
        }
        if ($v['status'] == 4) {
            $data[$k]['status_text'] = '订单关闭';
        }
        if ($v['status'] == 5) {
            $data[$k]['status_text'] = '退款中';
        }
        if ($v['status'] == 6 || $v['status'] == 7) {
            $data[$k]['status_text'] = '订单完成';
        }
		$order_price=0;
		$cuisines = $db2->select("restaurant_order_cuisine", ["detial_cash","status"],["order_id" =>  $v['id']]);
		foreach ($cuisines as $k => $v) {
		   $order_price+= $v['detial_cash'];
        }       
		$data[$k]['price'] += $order_price;
		
    }
    foreach($rd_arr as $k=>$v){
        $sstr='';
        //读取餐桌
        $desks = $db2->select("restaurant_desk", ["[>]order_belong_table" => ["id" => "belong_id"]], ["restaurant_desk.id", "restaurant_desk.desk", "restaurant_desk.desk_id", "restaurant_desk.is_use"], ["order_belong_table.belong_table" => "desk", "order_belong_table.order_id" =>$v]);
        if(count(array_filter($desks))!=0){
            foreach($desks as $kk=>$vv){
                $sstr.=$vv['desk']."  ";
            }
        }
        
       
        // var_dump($desks);
        //读取包房
        $rooms = $db2->select("room_message", ["[>]order_belong_table" => ["id" => "belong_id"]], ["room_message.id", "room_message.room_name", "room_message.is_use"], ["order_belong_table.belong_table" => "room", "order_belong_table.order_id" =>$v]);
        // var_dump($rooms);
        if(count(array_filter($rooms))!=0){
        foreach($rooms as $kk=>$vv){
            $sstr.=$vv['room_name']."  ";
        }
        }
        $data[$k]['room_desk']=$sstr;
    }
    include template("order_list", 'mobile');
}
function test_show(){
    global $_userid,$db2;
    $type='desk';//$_POST['type'] ? $_POST['type']:"";
    $restaurant_id=324;//$_POST['rid'] ? $_POST['rid']:"";
    $commomid = 84;//$_POST['commomid'] ? $_POST['commomid']:"";
    $order_id="";//"reserve_resraurant-20171128281";//$_POST['order_id'] ? $_POST['order_id']:"";

    if(!$_userid){
    $put = array('code' => -400,'msg' => 'err','data'=>'未登录');
        exit(json_encode($put));
    }

    if($type=='desk'){
        $is_use = $db2->get("restaurant_desk",["is_use","userid_now"],["id"=>$commomid]);
    }else{
        $is_use = $db2->get("room_message",["is_use","userid_now"],["id"=>$commomid]);
    }
    // var_dump($is_use);die;
    if($is_use['is_use']=='0'){
        $put = array('code' => 200,'msg' => 'err','data'=>true);
        exit(json_encode($put));
    }else{
        $orders = $db2->get("restaurant_reserve_orders",['buyer_uid','status'], ['order_id'=>$order_id]);    
        // echo in_array($orders['status'],[2,3,1]);die;
        if($_userid==$orders['buyer_uid'] && in_array($orders['status'],[2,3,1])){
            // echo "hello";
            $put = array('code' => 200,'msg' => 'err','data'=>true);
            exit(json_encode($put));
        }else{
            $put = array('code' => -200,'msg' => 'ok','data'=>false);
            exit(json_encode($put));
        }
    }
}
//显示订单
function order_form() {
    global $db2;
    global $_userid;
    session_start();
    if (isset($_SESSION['waiter_id'])) {
        $_userid = $_SESSION['_userid'];
    } else if (!$_userid) {
        die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
    }
    //读取订单详情
    $rid = $db2->get("restaurant", "id", ["userid" => $_userid]);
    $map["restaurant_id"] = $rid;
    $map["id"] = $_GET['oid'];
    $item = $db2->get("restaurant_reserve_orders", "*", $map);
    //读取餐桌
    //$desks=$db2->select("restaurant_desk",["[>]order_belong_table"=>["id"=>"belong_id"]],
    //["restaurant_desk.id","restaurant_desk.desk","restaurant_desk.desk_id","restaurant_desk.is_use",
    //"order_belong_table.id(bid)"],
    //["order_belong_table.belong_table"=>"desk","order_belong_table.order_id"=>$item['id']]);
    //读取包房
    //$rooms=$db2->select("room_message",["[>]order_belong_table"=>["id"=>"belong_id"]],
    //["room_message.id","room_message.room_name","room_message.is_use",
    //"order_belong_table.id(bid)"],
    //["order_belong_table.belong_table"=>"room","order_belong_table.order_id"=>$item['id']]);
    include template("order_form", 'mobile');
}
//修改订单价格
function order_price_edit() {
    global $db2;
    global $_userid;
    session_start();
    if (isset($_SESSION['waiter_id'])) {
           $_userid = $_SESSION['_userid'];
    } else if (!$_userid) {
        die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
    }
    $rel = $db2->update("restaurant_reserve_orders", ["price" => $_POST["price"], "price_remark" =>  $_POST["price_remark"]], ["id" => $_POST["oid"]]);
	
   // if ($rel > 0) {
        die("<script>alert('修改成功!');window.location.href='?act=order_info&oid=" . $_POST['oid'] . "'</script>");
    //}
}
/*
//删除订单的桌或房

function del_obj(){
	global $db2;global $_userid;
	session_start();
	$bid=$_GET['bid'];
	if(isset($_SESSION['waiter_id'])&$_SESSION['waiter_id']!=""){
		$_userid=$_SESSION['_userid'];
		
	}else if ( !$_userid ) {
		die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
	}
	$oid=$db2->get("order_belong_table","order_id",["id"=>$bid]);
	if($_GET['type']=='desk'){
		$rel=$db2->delete("order_belong_table",["id"=>$bid]);
		if($rel){
		   die("<script>alert('请登录');window.location.href=?act=order_info&oid=".$oid);
		}
	}else if($_GET['type']=='room'){
		$rel=$db2->delete("order_belong_table",["id"=>$bid]);
		if($rel){
		   die("<script>alert('请登录');window.location.href=?act=order_info&oid=".$oid);
		}	
	}
	
	
	
}




//给订单新增房或桌

function add_obj(){
	global $db2;global $_userid;
	session_start();
	$bid=$_GET['obj_id'];
	if(isset($_SESSION['waiter_id'])&$_SESSION['waiter_id']!=""){
		$_userid=$_SESSION['_userid'];
		
	}else if ( !$_userid ) {
		die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
	}
	$oid=$db2->get("order_belong_table","order_id",["id"=>$bid]);
	if($_GET['type']=='desk'){
		
		
		
	}else if($_GET['type']=='room'){
		
	}
	
	
	
}
*/
//订单详情
function order_info() {
    global $db2;
    global $_userid,$CFG;
	$waiter_code=null;
    session_start();
    if (isset($_SESSION['waiter_id'])) {
           $_userid = $_SESSION['_userid'];
		   $db2->update("restaurant_waiter", ["oid"=>$_GET['oid']],["id"=>$_SESSION['waiter_id']]);
		   $num=$db2->count("user_session",["userid"=>$_SESSION['waiter_id'],"type"=>"reserve-waiter-".$_GET['oid']]);
		   $waiter_code=getRandomString(16);
			if($num>0){
				$db2->update("user_session",["sessid"=>$waiter_code],["userid"=>$_SESSION['waiter_id'],"type"=>"reserve-waiter-".$_GET['oid']]);
			}else{
				$db2->insert("user_session",["userid"=>$_SESSION['waiter_id'],"sessid"=>$waiter_code,"type"=>"reserve-waiter-".$_GET['oid']]);
                //var_dump($db2->error());	die;			
			}
			
    } else if (!$_userid) {
        die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
    }
    //读取订单详情
    $rid = $db2->get("restaurant", "id", ["userid" => $_userid]);
    $map["restaurant_id"] = $rid;
    $map["id"] = $_GET['oid'];
    $orders = $db2->get("restaurant_reserve_orders", "*", $map);
	if(!$orders){
		 die("<script>alert('该订单不存在！');</script>");
	}
	$order_price=$orders['price'];
	
	
	$cuisines = $db2->select("restaurant_order_cuisine", ["detial_cash","status"],["order_id" => $_GET['oid']]);
	//var_dump($cuisines);die;
	foreach ($cuisines as $k => $v) {
	   $order_price+= $v['detial_cash'];
	}
	

	$orderids= explode('-',$orders['order_id']);
    $uname = $db2->get("member", "username", ["userid" => $orders['buyer_uid']]);
	
    //$v['buyer']=$db2->get("member","turename",["userid"=>$v['buyer_uid']]);
    if ($orders['status'] == 1) {
        $orders['status_text'] = '待支付';
    }
    if ($orders['status'] == 2) {
        $orders['status_text'] = '支付成功';
    }
    if ($orders['status'] == 3) {
        $orders['status_text'] = '进行中';
    }
    if ($orders['status'] == 4) {
        $orders['status_text'] = '订单关闭';
    }
    if ($orders['status'] == 5) {
        $orders['status_text'] = '退款中';
    }
    if ($orders['status'] == 6) {
        $orders['status_text'] = '已完成，未评价';
    }
    if ($orders['status'] == 7) {
        $orders['status_text'] = '订单完成';
    }
    if ($orders['status'] == 8) {
        $orders['status_text'] = '退款成功';
	}
	if ($orders['status'] == 9) {
		$orders['status_text'] = '商家拒绝退款';
	}
	//读取餐桌
	$desks = $db2->select("restaurant_desk", ["[>]order_belong_table" => ["id" => "belong_id"]], ["restaurant_desk.id", "restaurant_desk.desk", "restaurant_desk.desk_id", "restaurant_desk.is_use"], ["order_belong_table.belong_table" => "desk", "order_belong_table.order_id" => $orders['id']]);
	//var_dump($db2->error());die;
	//读取包房
	$rooms = $db2->select("room_message", ["[>]order_belong_table" => ["id" => "belong_id"]], ["room_message.id", "room_message.room_name", "room_message.is_use"], ["order_belong_table.belong_table" => "room", "order_belong_table.order_id" => $orders['id']]);
	//读取菜式
	$cuisines = $db2->select("mall", ["[>]restaurant_order_cuisine" => ["itemid" => "foreign_id"]], ["restaurant_order_cuisine.add_title","restaurant_order_cuisine.id", "mall.title", "restaurant_order_cuisine.singel_price", "restaurant_order_cuisine.detial_cash", "restaurant_order_cuisine.status", "restaurant_order_cuisine.department", "restaurant_order_cuisine.num", "restaurant_order_cuisine.addtime"], ["restaurant_order_cuisine.order_id" => $orders['id']]);
	foreach ($cuisines as $k => $v) {
		if ($v['status'] == 1) {
			$cuisines[$k]['status_text'] = "即上";
		}
		if ($v['status'] == 2) {
			$cuisines[$k]['status_text'] = "叫起";
		}
		if ($v['status'] == 3) {
			$cuisines[$k]['status_text'] = "已上";
		}
	}
    
     $invoice=[];
	 $redis = new redis();
	 $redis->connect('127.0.0.1', 6379);
	 $redis->auth("zltgs");
	 if(!empty($redis->get($arrs['order_id'].'-invoice'))){
	     $invoice=unserialize($redis->get($arrs['order_id'].'-invoice')); 
	 }
	include template("order_info", 'mobile');
}




 //删除菜式
    function cuisines_del() {
		
		showerror();
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
		
        $rel = $db2->delete("restaurant_order_cuisine",["id" =>$_GET['cid']]);
		if($rel>0){
			 die("<script>alert('删除成功！');location.href='reserve_home.php?act=order_info&oid=$_GET[oid]';</script>");
		}
       
    }










    //订单菜式信息
    function order_cuisine_info() {
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        $cuisineid = $_GET['cuisineid'];
        //读取菜式
        $item = $db2->get("mall", ["[>]restaurant_order_cuisine" => ["itemid" => "foreign_id"]], ["restaurant_order_cuisine.add_title", "restaurant_order_cuisine.singel_price", "restaurant_order_cuisine.detial_cash", "restaurant_order_cuisine.status", "restaurant_order_cuisine.department", "restaurant_order_cuisine.num", "restaurant_order_cuisine.reason", "restaurant_order_cuisine.status", "restaurant_order_cuisine.id", "restaurant_order_cuisine.order_id", "mall.title"], ["restaurant_order_cuisine.id" => $cuisineid]);
        include template('cuisine_form', 'mobile');
    }
    //订单菜式修改
    function order_cuisine_edit() {
        showerror();
        global $DT_PRE;
        global $db;
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        $data = [];
        foreach ($_POST as $k => $v) {
            if ($k != "cuisineid" & $k != "oid") {
                $data[$k] = $v;
            }
        }
        if ($data["num"] == 0) {
            $id = $db2->delete("restaurant_order_cuisine", ["id" => $_POST['cuisineid']]);
            if ($id > 0) {
                die("<script>alert('菜式清除成功');location.href='reserve_home.php?act=order_info&oid=" . $_POST['oid'] . "';</script>");
            }
        } else {
            //var_dump($data);die;
            $data["detial_cash"] = $data["num"] * $data["singel_price"];
            $id = $db2->update("restaurant_order_cuisine", $data, ["id" => $_POST['cuisineid']]);
            if ($id > 0) {
                die("<script>alert('修改菜式信息成功');location.href='reserve_home.php?act=order_info&oid=" . $_POST['oid'] . "';</script>");
            }
        }
    }
    //修改包房
    function room_form() {
        //showerror();
        global $db2;
        global $_userid,$CFG;
        $item = [];
        $title = "";
        $act = "";
		$url="";
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        if (!empty($_GET["roomid"])) {
            $act = "room_edit";
            $title = "修改";
            $deskid = $_GET["roomid"];
            $item = $db2->get("room_message", "*", ["id" => $_GET["roomid"]]);
            $waiter_str=$item['waiter_ids'];
            // var_dump($waiter_str);die;
            if(!empty($waiter_str)){
                $waiter_ids=explode(",",$waiter_str);
                $waiter_ids=array_filter($waiter_ids);
                // var_dump($waiter_ids);die;
                foreach($waiter_ids as $k=>$v){                    
                    $msg=$db2->get("member", "mobile", ["userid" => $v]);
                    if($msg){    
                        $res.=$msg.",";            
                    }else{
                        $res="";
                        break;
                    }
                }               
            }

			$url=curlQuery($CFG['url'].'mobile/newdemo/demo2.html?&rid='.$item['restaurant_id'].'&commonid='.$item['id'].'&reserve_type=room');
        } else {
            $title = "新增";
            $act = "room_add";
        }
		 
		
        include template('room_form', 'mobile');
    }
    //删除包房
    function room_del() {
        //showerror();
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        if (!empty($_GET["roomid"])) {
            $desk = $db2->get("room_message", ["is_use", "userid_now"], ["id" => $_GET["deskid"]]);
            if ($desk['is_use'] == 1 || $desk['userid_now'] > 0) {
                die("<script>alert('该餐桌已被客户预定或正在使用中,不能删除');location.href='?act=desk_form&deskid='" . $_GET["deskid"] . ";</script>");
            }
            $orders = $db2->count("restaurant_reserve_orders", ["[>]order_belong_table" => ["id" => "order_id"]], ["restaurant_reserve_orders.status" => 2, "order_belong_table.belong_id" => $_GET["roomid"], "order_belong_table.belong_table" => "room"]);
            if ($orders > 0) {
                die("<script>alert('该餐桌已被客户预定或正在使用中,不能删除');</script>");
            }
            $rel = $db2->delete("room_message", ["id" => $_GET["roomid"]]);
            if ($rel > 0) {
                die("<script>alert('删除包房成功');location.href='?act=show_room';</script>");
            }
        }
        include template('room_form', 'mobile');
    }
    //修改包房
    function room_edit() {
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        $peoplecount = empty($_POST["people_count"]) ? die("<script>alert('容纳人数不能为空');history.back()</script>") : $_POST["people_count"];
        $is_use = $_POST["is_use"];
        $room_name = empty($_POST["room_name"]) ? die("<script>alert('房间名不能为空');history.back()</script>") : $_POST["room_name"];
        $info = $_POST["info"];
        $reserve_price = $_POST["reserve_price"];
        $edittime = strtotime("now");
        $give_good = $_POST["give_good"];
        $facilities = $_POST["facilities"];
        $refund_rule = $_POST["refund_rule"];
        $reserve_price = $_POST["reserve_price"];
        $lock_qrcode=$_POST['lock_qrcode'];
        $min_consumption = $_POST["min_consumption"];
        $qrcode = "";
        $thumb_banner = "";
        $thumb_banner2 = "";
        $thumb_banner3 = "";
        $waiter_str=$_POST['waiters'];
        if(!empty($waiter_str)){
            $waiter_ids=explode(",",$waiter_str);
            $waiter_ids=array_filter($waiter_ids);
            // var_dump($waiter_ids);die;
            foreach($waiter_ids as $k=>$v){
                
                $msg=$db2->get("member", "userid", ["mobile" => $v]);
                if($msg){    
                    $res.=$msg.",";            
                }else{
                    $res="";
                    break;
                }
            }               
        }
        if (!$_FILES['qr_code']['name']) {
            $qrcode = $_POST["old_qr_code"];
        } else {
            $img = img_save($_FILES['qr_code']);
            if (!$img['success']) {
                die("<script>alert('二维码图上传失败，原因：" . $img['message'] . "');history.back()</script>");
            }
            $qrcode = $img['url'];
        }
        if (!$_FILES['thumb_banner']['name']) {
            $thumb_banner = $_POST["old_thumb_banner"];
        } else {
            $img = img_save($_FILES['thumb_banner']);
            if (!$img['success']) {
                die("<script>alert('幻灯图1上传失败，原因：" . $img['message'] . "');history.back()</script>");
            }
            $thumb_banner = $img['url'];
        }
        if (!$_FILES['thumb_banner2']['name']) {
            $thumb_banner2 = $_POST["old_thumb_banner2"];
        } else {
            $img = img_save($_FILES['thumb_banner2']);
            if (!$img['success']) {
                die("<script>alert('幻灯图2上传失败，原因：" . $img['message'] . "');history.back()</script>");
            }
            $thumb_banner2 = $img['url'];
        }
        if (!$_FILES['thumb_banner3']['name']) {
            $thumb_banner3 = $_POST["old_thumb_banner2"];
        } else {
            $img = img_save($_FILES['thumb_banner3']);
            if (!$img['success']) {
                die("<script>alert('幻灯图3上传失败，原因：" . $img['message'] . "');history.back()</script>");
            }
            $thumb_banner3 = $img['url'];
        }
        $roomid = $db2->update("room_message", ["people_count" => $peoplecount,"lock_qrcode"=>$lock_qrcode,"waiter_ids" => $res, "is_use" => $is_use, "room_name" => $room_name, "edittime" => $edittime, "reserve_price" => $reserve_price, "info" => $info, "thumb_banner" => $thumb_banner, "thumb_banner2" => $thumb_banner2, "thumb_banner3" => $thumb_banner3, "qrcode" => $qrcode, "give_good" => $give_good, "facilities" => $facilities, "refund_rule" => $refund_rule, "min_consumption" => $min_consumption], ["id" => $_POST["roomid"]]);
        if ($roomid > 0) {
            die("<script>alert('修改包房成功');location.href='?act=show_room';</script>");
        }
    }
    //增加包房
    function room_add() {
        showerror();
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        $peoplecount = empty($_POST["people_count"]) ? die("<script>alert('容纳人数不能为空');history.back()</script>") : $_POST["people_count"];
        $is_use = $_POST["is_use"];
        $room_name = empty($_POST["room_name"]) ? die("<script>alert('房间名不能为空');history.back()</script>") : $_POST["room_name"];
        $info = $_POST["info"];
        $reserve_price = $_POST["reserve_price"];
        $addtime = strtotime("now");
        $give_good = $_POST["give_good"];
        $facilities = $_POST["facilities"];
        $refund_rule = $_POST["refund_rule"];
        $reserve_price = $_POST["reserve_price"];
        $min_consumption = $_POST["min_consumption"];
        $lock_qrcode=$_POST['lock_qrcode'];
        $thumb_banner = "";
        $thumb_banner2 = "";
        $thumb_banner3 = "";
        $waiter_str=$_POST['waiters'];
        // var_dump($waiter_str);die;
        if(!empty($waiter_str)){
            $waiter_ids=explode(",",$waiter_str);
            $waiter_ids=array_filter($waiter_ids);
            // var_dump($waiter_ids);die;
            foreach($waiter_ids as $k=>$v){
                
                $msg=$db2->get("member", "userid", ["mobile" => $v]);
                if($msg){    
                    $res.=$msg.",";            
                }else{
                    $res="";
                    break;
                }
            }               
        }
        if (!$_FILES['thumb_banner']['name']) {
            die("<script>alert('幻灯图1不能为空');history.back()</script>");
        } else {
            $img = img_save($_FILES['thumb_banner']);
            if (!$img['success']) {
                die("<script>alert('幻灯图1上传失败，原因：" . $img['message'] . "');history.back()</script>");
            }
            $thumb_banner = $img['url'];
        }
        if (!$_FILES['thumb_banner2']['name']) {
            die("<script>alert('幻灯图2不能为空');history.back()</script>");
        } else {
            $img = img_save($_FILES['thumb_banner2']);
            if (!$img['success']) {
                die("<script>alert('幻灯图2上传失败，原因：" . $img['message'] . "');history.back()</script>");
            }
            $thumb_banner2 = $img['url'];
        }
        if (!$_FILES['thumb_banner3']['name']) {
            die("<script>alert('幻灯图3不能为空');history.back()</script>");
        } else {
            $img = img_save($_FILES['thumb_banner3']);
            if (!$img['success']) {
                die("<script>alert('幻灯图3上传失败，原因：" . $img['message'] . "');history.back()</script>");
            }
            $thumb_banner3 = $img['url'];
        }
        $rid = $db2->get("restaurant", "id", ["userid" => $_userid]);
        $roomid = $db2->insert("room_message", ["restaurant_id" => $rid,"lock_qrcode"=>$lock_qrcode, "waiter_ids" => $res,"people_count" => $peoplecount, "is_use" => $is_use, "room_name" => $room_name, "addtime" => $addtime, "reserve_price" => $reserve_price, "info" => $info, "thumb_banner" => $thumb_banner, "thumb_banner2" => $thumb_banner2, "thumb_banner3" => $thumb_banner3, "qrcode" => "", "give_good" => $give_good, "facilities" => $facilities, "refund_rule" => $refund_rule, "min_consumption" => $min_consumption]);
        if ($roomid > 0) {
            die("<script>alert('新增包房成功');location.href='?act=show_room';</script>");
        }
    }
    //修改餐桌
    function desk_form2() {
        //showerror();
        global $db2;
        global $_userid,$CFG;
        $item = [];
        $title = "";
        $act = "";
		$url="";
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        if (!empty($_GET["deskid"])) {
            $act = "desk_edit";
            $title = "修改";
            $deskid = $_GET["deskid"];
            $item = $db2->get("restaurant_desk", "*", ["id" => $_GET["deskid"]]);
            $waiter_str=$item['waiter_ids'];
            // var_dump($waiter_str);die;
            if(!empty($waiter_str)){
                $waiter_ids=explode(",",$waiter_str);
                $waiter_ids=array_filter($waiter_ids);
                // var_dump($waiter_ids);die;
                foreach($waiter_ids as $k=>$v){                    
                    $msg=$db2->get("member", "mobile", ["userid" => $v]);
                    if($msg){    
                        $res.=$msg.",";            
                    }else{
                        $res="";
                        break;
                    }
                }               
            }
           
			$url=curlQuery($CFG['url'].'mobile/newdemo/demo2.html?&rid='.$item['restaurant_id'].'&commonid='.$_GET["deskid"].'&reserve_type=desk');
        } else {
            $title = "新增";
            $act = "desk_add";
        }
        include template('desk_form', 'mobile');
    }
    function desk_form() {
        //showerror();
        global $db2;
        global $_userid,$CFG;
        $item = [];
        $title = "";
        $act = "";
		$url="";
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        if (!empty($_GET["deskid"])) {
            $act = "desk_edit";
            $title = "修改";
            $deskid = $_GET["deskid"];
            $item = $db2->get("restaurant_desk", "*", ["id" => $_GET["deskid"]]);
            $waiter_str=$item['waiter_ids'];
            // var_dump($waiter_str);die;
            if(!empty($waiter_str)){
                $waiter_ids=explode(",",$waiter_str);
                $waiter_ids=array_filter($waiter_ids);
                // var_dump($waiter_ids);die;
                foreach($waiter_ids as $k=>$v){                    
                    $msg=$db2->get("member", "mobile", ["userid" => $v]);
                    if($msg){    
                        $res.=$msg.",";            
                    }else{
                        $res="";
                        break;
                    }
                }               
            }
           
			$url=curlQuery($CFG['url'].'mobile/newdemo/demo2.html?&rid='.$item['restaurant_id'].'&commonid='.$_GET["deskid"].'&reserve_type=desk');
        } else {
            $title = "新增";
            $act = "desk_add";
        }
        include template('desk_form', 'mobile');
    }
    //删除餐桌
    function desk_del() {
        //showerror();
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        if (!empty($_GET["deskid"])) {
            $desk = $db2->get("restaurant_desk", ["is_use", "userid_now"], ["id" => $_GET["deskid"]]);
            if ($desk['is_use'] == 1 || $desk['userid_now'] > 0) {
                die("<script>alert('该餐桌已被客户预定或正在使用中,不能删除');location.href='?act=desk_form&deskid='" . $_GET["deskid"] . ";</script>");
            }
            $orders = $db2->count("restaurant_reserve_orders", ["[>]order_belong_table" => ["id" => "order_id"]], ["restaurant_reserve_orders.status" => 2, "order_belong_table.belong_id" => $_GET["deskid"], "order_belong_table.belong_table" => "desk", ]);
            if ($orders > 0) {
                die("<script>alert('该餐桌已被客户预定或正在使用中,不能删除');</script>");
            }
            $rel = $db2->delete("restaurant_desk", ["id" => $_GET["deskid"]]);
            if ($rel > 0) {
                die("<script>alert('删除餐桌成功');location.href='?act=show_desk';</script>");
            }
        }
        include template('desk_form', 'mobile');
    }
    //修改餐桌
    function desk_edit() {
        //showerror();
        global $db2;$CFG;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        $desk = empty($_POST["desk"]) ? die("<script>alert('餐桌名不能为空');history.back()</script>") : $_POST["desk"];
        $hold = empty($_POST["hold"]) ? die("<script>alert('容纳人数不能为空');history.back()</script>") : $_POST["hold"];
        $desk_id = $_POST["desk_id"];
        $is_use = $_POST["is_use"];
        $lock_qrcode = $_POST["lock_qrcode"];
        $edittime = strtotime("now");
        $info = $_POST["info"];
        $waiter_str=$_POST['waiters'];
        if(!empty($waiter_str)){
            $waiter_ids=explode(",",$waiter_str);
            // $len=count($waiter_ids);
            // var_dump($waiter_ids[$len-1]);die;
            // if($waiter_ids[$len-1]==""){
            //     echo "hello";
            //     unset($waiter_ids[$len]);
            // }
            $waiter_ids=array_filter($waiter_ids);
            // var_dump($waiter_ids);die;
            foreach($waiter_ids as $k=>$v){
                
                $msg=$db2->get("member", "userid", ["mobile" => $v]);
                if($msg){    
                    $res.=$msg.",";            
                }else{
                    $res="";
                    break;
                }
            }               
        }
        //var_dump($res);die;
        if (!$_FILES['qr_code']['name']) {
            $qr_code = $_POST["old_qr_code"];
        } else {
            $img = img_save($_FILES['qr_code']);
            if (!$img['success']) {
                die("<script>alert('缩略图上传失败，原因：" . $img['message'] . "');history.back()</script>");
            }
            $qr_code = $img['url'];
        }
        $min_consumption = $_POST["min_consumption"];
        $did = $db2->update("restaurant_desk", ["desk" => $desk,"waiter_ids" => $res,"lock_qrcode"=>$lock_qrcode, "hold" => $hold, "desk_id" => $desk_id, "is_use" => $is_use, "edittime" => $edittime, "info" => $info, "qr_code" => $qr_code, "min_consumption" => $min_consumption], ["id" => $_POST["deskid"]]);
        if ($did > 0) {
            die("<script>alert('修改餐桌成功');location.href='?act=show_desk';</script>");
        }
    }
    //增加餐桌
    function desk_add() {
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        $desk = empty($_POST["desk"]) ? die("<script>alert('餐桌名不能为空');history.back()</script>") : $_POST["desk"];
        $hold = empty($_POST["hold"]) ? die("<script>alert('容纳人数不能为空');history.back()</script>") : $_POST["hold"];
        $desk_id = $_POST["desk_id"];
        $is_use = $_POST["is_use"];
        $lock_qrcode=$_POST['lock_qrcode'];
        $edittime = strtotime("now");
        $info = $_POST["info"];
        $waiter_str=$_POST['waiters'];
        // var_dump($waiter_str);die;
        if(!empty($waiter_str)){
            $waiter_ids=explode(",",$waiter_str);
            $waiter_ids=array_filter($waiter_ids);
            // var_dump($waiter_ids);die;
            foreach($waiter_ids as $k=>$v){
                
                $msg=$db2->get("member", "userid", ["mobile" => $v]);
                if($msg){    
                    $res.=$msg.",";            
                }else{
                    $res="";
                    break;
                }
            }               
        }

        $rid = $db2->get("restaurant", "id", ["userid" => $_userid]);
        $min_consumption = $_POST["min_consumption"];
        $did = $db2->insert("restaurant_desk", ["restaurant_id" => $rid,"lock_qrcode"=>$lock_qrcode,"waiter_ids" => $res,"desk" => $desk, "hold" => $hold, "desk_id" => $desk_id, "is_use" => $is_use, "edittime" => $edittime, "info" => $info, "qr_code" =>"", "min_consumption" => $min_consumption]);
        if ($did > 0) {
            die("<script>alert('新增餐桌成功');location.href='?act=show_desk';</script>");
        }
    }
    //显示工作人员
    function waiter_list() {
        //showerror();
        $auths = [];
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        $map = [];
        if ($_POST['is_use']) {
            $map['is_use'] = $_POST['is_use'];
        }
        if ($_POST['department']) {
            $map['department[~]'] = $_POST['department'];
        }
        $rid = $db2->get("restaurant", "id", ["userid" => $_userid]);
        $map['restaurant_id'] = $rid;
        $data = $db2->select("restaurant_waiter", "*", $map);
        //echo $db2->last();die;
        //分割处理部门属性
        $departments = $db2->select("restaurant_waiter", "department", ["restaurant_id" => $rid]);
        $departmentstr = "";
        $department_arr = [];
        if (count($departments) > 0) {
            foreach ($departments as $k => $v) {
                $departmentstr.= $v;
            }
            $departmentstr = substr($departmentstr, 0, strlen($departmentstr) - 1);
            $department_arr = array_unique(explode(',', $departmentstr));
        }
        foreach ($data as $k => $v) {
            $auth_ids = explode(',', $v['auths']);
            $auth_menu = "";
            foreach ($auth_ids as $k2 => $v2) {
                $menu = $db2->get("reserve_auth", "menu", ["id" => $v2]);
                $auth_menu = $auth_menu . $menu . "&nbsp;&nbsp;&nbsp;";
            }
            $data[$k]['auth_str'] = substr($auth_menu, 0, strlen($auth_menu) - 1);
        }
        include template('reserve_waiter', 'mobile');
    }
    //修改工作人员
    function waiter_form() {
        //showerror();
        global $db2;
        global $_userid;
        $item = [];
        $title = "";
        $act = "";
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        if (!empty($_GET["waiterid"])) {
            $rid = $db2->get("restaurant", "id", ["userid" => $_userid]);
            $act = "waiter_edit";
            $title = "修改";
            $waiterid = $_GET["waiterid"];
            $item = $db2->get("restaurant_waiter", "*", ["id" => $_GET["waiterid"], "restaurant_id" => $rid]);
			$item['zlt_username']=$db2->get("member", "username", ["userid" => $item['userid']]);
        } else {
            $title = "新增";
            $act = "waiter_add";
        }
        $ids = [];
        if ($item['auths']) {
            $ids = explode(',', $item['auths']);
        }
        $authStr = get_nextauth(0, '', $ids);
        include template('waiter_form', 'mobile');
    }
    //增加工作人员
    function waiter_add() {
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
		$zlt_userid=$db2->get("member","userid",["OR" =>["username"=>$_POST["zlt_username"],"mobile"=>$_POST["zlt_username"]]]);
	
		if(empty($zlt_userid)){
			 die("<script>alert('该用户不存在！');location.href='".$_SERVER['HTTP_REFERER']."'</script>");
		}else{
		     $sum=$db2->count("restaurant_waiter",["userid"=>$zlt_userid]);
			 if($sum>0){
				 die("<script>alert('该用户早已经被绑定为服务员了！');location.href='".$_SERVER['HTTP_REFERER']."'</script>");
			 }
		}
        $job_name = empty($_POST["job_name"]) ? msg('职称不能为空') : $_POST["job_name"];
        $username = empty($_POST["username"]) ? msg('用户名不能为空') : $_POST["username"];
        $password = empty($_POST["password"]) ? msg('密码不能为空') : $_POST["password"];
        $is_use = $_POST["is_use"];
        $department = $_POST["department"];
        $auths = implode(",", $_POST['auths']);
        $rid = $db2->get("restaurant", "id", ["userid" => $_userid]);
        $wid = $db2->insert("restaurant_waiter", ["restaurant_id" => $rid, "userid" => $zlt_userid, "job_name" => $job_name, "username" => $username, "password" => $password, "is_use" => $is_use, "department" => $department . ',', "auths" => $auths]);
        if ($wid > 0) {
            die("<script>alert('新增工作人员成功');location.href='?act=waiter_list';</script>");
        }
    }
    //修改工作人员
    function waiter_edit() {
        //showerror();
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } elseif (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        $waiterid = $_POST["waiterid"];
        $job_name = empty($_POST["job_name"]) ? msg('职称不能为空') : $_POST["job_name"];
        $username = empty($_POST["username"]) ? msg('用户名不能为空') : $_POST["username"];
        $password = empty($_POST["password"]) ? msg('密码不能为空') : $_POST["password"];
        $is_use = $_POST["is_use"];
        $department = $_POST["department"];
        if (substr($department, strlen($department) - 1, strlen($department)) != ',') {
            $department.= ',';
        }

		
		$zlt_userid=$db2->get("member","userid",["OR" =>["username"=>$_POST["zlt_username"],"mobile"=>$_POST["zlt_username"]]]);
		if(empty($zlt_userid)){
			 die("<script>alert('该用户不存在！');location.href='".$_SERVER['HTTP_REFERER']."'</script>");
		}else{
		     $sum=$db2->count("restaurant_waiter",["userid"=>$zlt_userid]);
			 if($sum>0){
				 die("<script>alert('该用户早已经被绑定为服务员了！');location.href='".$_SERVER['HTTP_REFERER']."'</script>");
			 }
		}
		
		
		
        $auths = implode(",", $_POST['auths']);
        $wid = $db2->update("restaurant_waiter", ["userid" => $zlt_userid, "job_name" => $job_name, "username" => $username, "password" => $password, "is_use" => $is_use, "department" => $department, "auths" => $auths], ["id" => $waiterid]);
        if ($wid > 0) {
            die("<script>alert('修改工作人员成功');location.href='?act=waiter_list';</script>");
        }
    }
    //修改商家信息
    function info_edit() {
		showerror();
        global $DT_PRE;
        global $db;
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        $rid = $db2->get("restaurant", "id", ["userid" => $_userid]);
        $data = [];
        foreach ($_POST as $k => $v) {
            $data[$k] = $v;
        }
   
        if(empty($_POST['lng'])||empty($_POST['lat'])){
			 die("<script>alert('经纬度不能为空！');location.href='reserve_home.php?act=info_get';</script>");
		}
	
   
        $id = $db2->update("restaurant", $data, ["id" => $rid]);
        // 更改商家分类 为 151
         $db2->update("company", ["catids" => 151], ["userid" => $_userid]);
	    //var_dump($db2->error());die;
       // if ($id > 0) {
			cache_reserve_restaurant();
            die("<script>alert('修改商家信息成功');location.href='reserve_home.php?act=show_mes';</script>");
      //  }
	  
    }
    //读取
    function info_get() {
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        $item = $db2->get("restaurant", "*", ["userid" => $_userid]);
        include template('restaurant_info', 'mobile');
    }
    //删除工作人员
    function waiter_del() {
        global $db2;
        global $_userid;
        session_start();
        if (isset($_SESSION['waiter_id']) & $_SESSION['waiter_id'] != "") {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        $wid = $_GET["waiterid"];
        $rel = $db2->delete("restaurant_waiter", ["id" => $wid]);
        if ($rel > 0) {
            die("<script>location.href='?act=waiter_list';</script>");
        }
    }
	
	
	
	
	
	
	
    //更改订单状态
    function channge_order_status() {
        
        global $db2;
        global $_userid;
        global $memberurl;
        global $myurl;
        global $CFG;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
        $oid = $_GET["oid"];
        $order = $db2->get("restaurant_reserve_orders", "*", ["id" => $oid]);
        //客人已到
        if ($_GET["type"] == "begin" & $order['status'] == 2) {
            $id = $db2->update("restaurant_reserve_orders", ["status" => 3], ["id" => $oid]);
            if ($id > 0) {
				$objs=$db2->select("order_belong_table",["belong_id","belong_table"],["order_id"=>$oid]);
				
				//更改当前桌房状态为使用中
				foreach($objs as $k=>$v){
					if($v["belong_table"]=="desk"){
						
						$db2->update("restaurant_desk",
						["is_use"=>1,"userid_now"=>$order["buyer_uid"],"order_id"=>$oid],
						["id"=>$v["belong_id"]]);
						
					}
					if($v["belong_table"]=="room"){
						$db2->update("room_message",
						["is_use"=>1,"userid_now"=>$order["buyer_uid"],"order_id"=>$oid],
						["id"=>$v["belong_id"]]);
					}
					
				}
                die("<script>alert('订单修改状态成功!');location.href='reserve_home.php?act=order_info&oid=$oid'</script>");
            }
            
            //结账
        } else if ($_GET["type"] == "close" & $order['status'] == 3) {
		
            //cash预定金额,price订单总价
            $order_price = $order['price'];
            $cuisines = $db2->select("restaurant_order_cuisine", ["detial_cash","status"],["order_id" => $oid]);
		    //var_dump($cuisines);die;
			$cuisine_finish=0;
            foreach ($cuisines as $k => $v) {
                $order_price+= $v['detial_cash'];
				if($v['status']!=3){
					$cuisine_finish=1;
				}
            }
            //平台折扣
            if ($order["subsidy"] > 0) {
                $order_price = $order_price * $order["subsidy"];
            }
			//echo $order_price;die;
            //如果预交定金大于订单总价,在定金里扣除，再把剩余打回给客户
			if($cuisine_finish==1){
				  die("<script>alert('该订单还有未上的菜式,如果客户不要,请处理取消后再结账！');window.history.go(-1);</script>");
			}
			
			if($order_price<=0){
				  die("<script>alert('订单价格不能为0！');window.history.go(-1);</script>");
			}
			
            if ($order['cash'] >= $order_price) {
				
                $surplus = $order['cash'] - $order_price; //余额
                $seller_name = $db2->get("member", "username", ["userid" => $_userid]);
                $buyer = $db2->get("member", ["username", "money"], ["userid" => $order["buyer_uid"]]);
                //给商家打入账单金额
                money_record($seller_name, -$surplus, '站内', 'system', '预定订单成功结账,返还预定定金余额给客户', '订单号' . $order["order_id"]);
                $touser = $seller_name;
                $title = "您有一笔预定订单成功结账";
                $url = $CFG['url'] . 'mobile/reserve_home.php?act=order_info&oid=' . $oid;
                $trade_message_c2 = "买家 <a href=\"{V0}\" class=\"t\">{V1}</a> 于 <span class=\"f_gray\">{V2}</span> 的预定订单结账成功，金额:￥" . $order_price . "<br/><a href=\"{V3}\" class=\"t\" target=\"_blank\">&raquo; 请点这里查看详情</a>;";
                $content = lang($trade_message_c2, array(
                    $url,
                    $buyer['username'],
                    date('Y-m-d H:i', $order['addtime']) ,
                    $url
                ));
                send_message($touser, $title, $content);
                money_add($seller_name, -$surplus);
                //给客户打回定金金额
                money_record($buyer['username'], $surplus, '站内', 'system', '预定订单结账成功,预定金余额自动入账', '订单号' . $order["order_id"]);
                $touser = $buyer['username'];
                $title = "您有一笔预定订单成功结账";
                $url = $CFG['reserve_url']."orderList/"; //待改，跳去客户订单详情
                $trade_message_c2 = "您 <a href=\"{V0}\" class=\"t\">{V1}</a> 于 <span class=\"f_gray\">{V2}</span> 的预定订单结账成功，订单总价:￥" . $order_price . ",你的预定金额：￥" . $order["cash"] . ",余额：￥" . $surplus . "已打入你的账户,请留意<br/><a href=\"{V3}\" class=\"t\" target=\"_blank\">&raquo; 请点这里查看详情</a>;";
                $content = lang($trade_message_c2, array(
                    $url,
                    $buyer['username'],
                    date('Y-m-d H:i', $order['addtime']) ,
                    $url
                ));
                send_message($touser, $title, $content);
                $money = $surplus + $buyer['money'];
                $db2->update("member", ["money" => $money], ["userid" => $order["buyer_uid"]]);
                //更新订单状态与桌,房状态
                $id = $db2->update("restaurant_reserve_orders", ["status" => 6], ["id" => $oid]);
                $db2->update("restaurant_desk", ["userid_now" => 0, "is_use" => 0,"order_id" => 0], ["order_id" => $oid]);
                $db2->update("room_message", ["userid_now" => 0, "is_use" => 0,"order_id" => 0], ["order_id" => $oid]);
                if ($id > 0) {
                    die("<script>alert('订单结账成功!');location.href='reserve_home.php?act=order_info&oid=$oid'</script>");
                }
            } else {
				//欠付金额
				
				$owe_balance=$order_price-$order['cash'];
				$db2->update("restaurant_reserve_orders",["owe_balance"=>$owe_balance,"is_owe"=>1,"price"=>$order_price,"is_checkout"=>1],["id" => $oid]);
				$seller_name = $db2->get("member", "username", ["userid" => $_userid]);
                $buyer = $db2->get("member", "username", ["userid" => $order["buyer_uid"]]);			
				$touser = $buyer;
				
                $title = "您有一笔预定需要结账";
                $url = $CFG['url']."/mobile/reserve.php?act=reserve_owe_pay&order_id=".$order['order_id']; //待改，跳去客户订单详情
                $trade_message_c2 = "您 <a href=\"{V0}\" class=\"t\">{V1}</a> 于 <span class=\"f_gray\">{V2}</span> 的预定订单需要结账，订单总价:￥" . $order_price . ",扣除预定金额：￥" . $order["cash"] . ",剩余：￥" . $owe_balance. "需要支付,请点击<br/><a href=\"{V3}\" class=\"t\" target=\"_blank\">&raquo; 请点这里进行支付</a>;";
                $content = lang($trade_message_c2, array(
                    $url,
                    $buyer,
                    date('Y-m-d H:i', $order['addtime']) ,
                    $url
                ));
				//echo $order_price;die;
                send_message($touser, $title, $content);
	            die("<script>alert('订单成功提交结账!请等待客户确认！');window.history.back();</script>");
            }
            //同意退款
            
        } else if ($_GET["type"] == "refunds_ok") {
            $seller_name = $db2->get("member", "username", ["userid" => $_userid]);
            $buyer = $db2->get("member", ["username", "money"], ["userid" => $order["buyer_uid"]]);
            //给商家打入账单金额
            money_record($seller_name, -$order['cash'], '站内', 'system', '预定订单客户退款', '订单号' . $order["order_id"]);
            $touser = $seller_name;
            $title = "您有一笔预定订单客户申请退款成功";
            $url = $CFG['url'] . 'mobile/reserve_home.php?act=order_info&oid=' . $oid;
            $trade_message_c2 = "买家 <a href=\"{V0}\" class=\"t\">{V1}</a> 于 <span class=\"f_gray\">{V2}</span> 的预定订单退款成功，预定金额:￥" . $order['cash'] . "<br/><a href=\"{V3}\" class=\"t\" target=\"_blank\">&raquo; 请点这里查看详情</a>;";
            $content = lang($trade_message_c2, array(
                $url,
                $buyer['username'],
                date('Y-m-d H:i', $order['addtime']) ,
                $url
            ));
            send_message($touser, $title, $content);
            money_add($seller_name, -$order['cash']);
            //给客户打回定金金额
            money_record($buyer['username'], $order['cash'], '站内', 'system', '预定订单退款成功', '订单号' . $order["order_id"]);
            $touser = $buyer['username'];
            $title = "您有一笔预定订单退款成功";
            $url =  $CFG['reserve_url']."orderList/"; //待改，跳去客户订单详情
            $trade_message_c2 = "您 <a href=\"{V0}\" class=\"t\">{V1}</a> 于 <span class=\"f_gray\">{V2}</span>的预定订单退款成功，预定金额:￥" . $order['cash'] . "<br/><a href=\"{V3}\" class=\"t\" target=\"_blank\">&raquo; 请点这里查看详情</a>;";
            $content = lang($trade_message_c2, array(
                $url,
                $buyer['username'],
                date('Y-m-d H:i', $order['addtime']) ,
                $url
            ));
            money_add($buyer['username'], $order['cash']);
            send_message($touser, $title, $content);
            //更新订单状态
            $id = $db2->update("restaurant_reserve_orders", ["status" => 8], ["id" => $oid]);
            if ($id > 0) {
                die("<script>alert('同意退款成功!');location.href='reserve_home.php?act=order_info&oid=$oid'</script>");
            }
            //拒绝退款
            
        } else if ($_GET["type"] == "refunds_no") {
            //给客户打回定金金额
        
            $touser = $buyer['username'];
            $title = "您有一笔预定订单退款申请失败";
            $url =  $CFG['reserve_url']."orderList/"; //待改，跳去客户订单详情
            $trade_message_c2 = "您 <a href=\"{V0}\" class=\"t\">{V1}</a> 于 <span class=\"f_gray\">{V2}</span>下的预定订单申请退款，商家拒接,预定金额:￥" . $order['cash'] . "<br/><a href=\"{V3}\" class=\"t\" target=\"_blank\">&raquo; 请点这里查看详情</a>;";
            $content = lang($trade_message_c2, array(
                $url,
                $buyer['username'],
                date('Y-m-d H:i', $order['addtime']) ,
                $url
            ));
            send_message($touser, $title, $content);
			 //更新订单状态
            $id = $db2->update("restaurant_reserve_orders", ["status" => 9], ["id" => $oid]);
			if ($id > 0) {
                die("<script>alert('拒绝退款成功!');location.href='reserve_home.php?act=order_info&oid=$oid'</script>");
			}
        }
    }
    //员工登录页显示
    function login_show() {
        include template('waiter_login', 'mobile');
    }
    //员工登录
    function waiter_login() {
        global $db2;
        global $_userid;
        if (empty($_POST['username'])) {
            die("<script>alert('用户名不能为空');history.back()</script>");
        }
        if (empty($_POST['password'])) {
            die("<script>alert('密码不能为空');history.back()</script>");
        }
        $waiter = $db2->get("restaurant_waiter", ["id", "is_use", "auths"], ["username" => $_POST['username'], "password" => $_POST['password'] ]);
        if (count($waiter) > 0) {
            if ($waiter['is_use'] == 0) {
                die("<script>alert('你的账户被停用，请联系管理层！');history.back()</script>");
            } else {
                session_start();
                $waiter2 = $db2->get("restaurant_waiter",["[>]restaurant"=>["restaurant_id"=>"id"]], ["restaurant_waiter.auths","restaurant.userid"],["restaurant_waiter.is_use" => 1, "restaurant_waiter.id" => $waiter['id']]);     
				$_SESSION['waiter_id'] = $waiter['id'];
                $_SESSION['waiter_auths'] = $waiter2['auths'];
                $_SESSION['_userid'] = $waiter2['userid'];
				$_SESSION['is_waiter'] = 1;
                die("<script>location.href='?act=show_mes'</script>");
            }
        } else {
            die("<script>alert('账户或密码错误！');history.back()</script>");
        }
    }
    //退出
    function loginout() {
		global $db2;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
			$db2->delete("user_session",["userid"=>$_SESSION['waiter_id']]);
            unset($_SESSION['waiter_id']);
            unset($_SESSION['waiter_auths']);
            unset($_SESSION['_userid']);
            die("<script>window.location.href='?act=login_show'</script>");
        }else{
			die("<script>window.location.href='logout.php'</script>");
		}
    }
	
	
	
	
	
	
    function show_mes() {
        showerror();
        global $db2;
        global $_userid;
        global $db;
        global $DT_PRE;
        $auths = [];
		
		
	
		
		

        session_start();
        if (isset($_SESSION['waiter_id'])) {
				$auth_ids = explode(',', $_SESSION['waiter_auths']);
				foreach ($auth_ids as $k => $v) {
					$auth = $db2->get("reserve_auth", "*", ["reserve_type" => "restaurant", "id" => $v]);
					if ($auth['pid'] == 0 & $auth['url'] != "") {
						$auths[] = $auth;
					}
				}
				
				$_userid = $_SESSION['_userid'];
        } else {		
            if (!$_userid) {
                die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
            }	
        }
		//echo $_userid;die;
		$catid=$db2->get("company","catid",["userid"=>$_userid]);
		if(strpos($catid,'151') == false){
			die("<script>alert('你的公司主营行业分类必须设置为住宿餐饮！');window.location.href='my.php';</script>");
		}
	
		$rsum=$db2->count("restaurant",["userid"=>$_userid]);
        
		
	  
	  
        if($rsum==0){
			$db2->insert("restaurant",
			["userid"=>$_userid,
			 "business"=>0,
			 "status"=>0
			]);
			cache_reserve_restaurant();		
			die("<script>alert('你的预定开通信息已录入，请等待管理员审核！');window.history.back();</script>");
		}
		
		
		
		
		
        $company = $db2->get("company", ["company", "thumb"], ["userid" => $_userid]);
        $restaurant = $db2->get("restaurant", ["id", "month_sells", "business","status"], ["userid" => $_userid]);
		
		if($restaurant['status']==0){
		    die("<script>alert('你的预定店铺在审核当中！');window.history.back();</script>");
		}
        $num = $db->get_one("select count(*) as count_data from " . $DT_PRE . "restaurant_reserve_orders where restaurant_id='" . $restaurant['id'] . "'and (status=6 or status=7)  and DATE_FORMAT(FROM_UNIXTIME(addtime),'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')") ['count_data'];
        $month_sell = $restaurant['month_sells'] + $num;
        $todays = $db->get_one("select count(*) as count_data from " . $DT_PRE . "restaurant_reserve_orders where restaurant_id='" . $restaurant['id'] . "'  and DATE_FORMAT(FROM_UNIXTIME(addtime),'%Y%m%d')=DATE_FORMAT(CURDATE(),'%Y%m%d')") ['count_data'];
        $todays = empty($todays) ? 0 : $todays;
        $onorders = $db->get_one("select count(*) as count_data from " . $DT_PRE . "restaurant_reserve_orders where restaurant_id='" . $restaurant['id'] . "' and status not in (4,6,7)") ['count_data'];
        $onorders = empty($onorders) ? 0 : $onorders;
        $business = $restaurant['business'] == 1 ? '是' : '否';
        include template('reserve_user_center', 'mobile');
    }
    function get_nextauth($pid, $str, $ids) {
        $str_1 = "";
        if ($pid > 0) {
            $str_1 = '|_' . $str;
        }
        global $auth_item, $db2;
        $arr = $db2->select("reserve_auth", "*", ["reserve_type" => "restaurant", "pid" => $pid]);
        foreach ($arr as $k => $v) {
            $checked = "";
            foreach ($ids as $k2 => $v2) {
                if ($v2 == $v['id']) {
                    $checked = "checked";
                }
            }
            $auth_item.= "<li>$str_1<input style='width:auto;height:11px;' type='checkbox'  $checked name='auths[]' value='$v[id]'/>$v[menu]</li>";
            get_nextauth($v['id'], $str . '_', $ids);
        }
        $str = "";
        return $auth_item;
    }
    //判断是当前用户是否拥有操作权限
    function is_have_auth($menu){
        global $db2;
        global $_userid;
        if (isset($_SESSION['waiter_id'])) {
            $rel = false;
            $auth_id = $db2->get("reserve_auth", "id", ["reserve_type" => "restaurant", "menu" => $menu]);
            $auth_ids = $db2->get("restaurant_waiter", "auths", ["id" => $_SESSION['waiter_id']]);
            $auth_ids_arr = explode(',', $auth_ids);
            foreach ($auth_ids_arr as $k => $v) {
                if ($v == $auth_id) {
                    $rel = true;
                }
            }
            return $rel;
        } else {
            return true;
        }
    }
    function book_desk(){
        global $db2;
        $desk_id=$_GET["deskid"];
        $desk_info= $db2->get("restaurant_desk", "*", ["id" =>$desk_id]);
        if($desk_info && $desk_info['is_use']!='1'){
            include template('book_desk', 'mobile');
        }else{
            die("<script>alert('该餐桌已被预定...');location.href='?act=show_desk';</script>");
        }
    }


    function book_room(){
        global $db2;
        $room_id=$_GET["roomid"];
        $room_info= $db2->get("room_message", "*", ["id" =>$room_id]);
        if($room_info && $room_info['is_use']!='1'){
            include template('book_room', 'mobile');
        }else{
            die("<script>alert('该房间已被预定...');location.href='?act=show_desk';</script>");
        }
    }

    function book_room_add(){
        global $db2;
        $room_id=$_POST["roomid"];
        $timer=strtotime($_POST['timer']);
        if($timer<strtotime ("now")){
            die("<script>alert('预定时间小于当前时间，请重试!!');location.href='?act=show_desk';</script>");
        }
        $room_info= $db2->get("room_message", "*", ["id" =>$room_id]);
//        var_dump($room_info);die;
        if($room_info && $room_info['is_use']!='1'){
            $mobile=$_POST['mobile'];
            $contact_name=$_POST['name'];
            $is_use=1;
            $buyer_msg=$db2->get("member",["userid","sellersubsidy"],["mobile"=> $mobile]);
            if(!$buyer_msg){
                die("<script>alert('系统暂无此用户!!');location.href='?act=show_room';</script>");
            }
//            $restaurant_id=$db2->get("restaurant",["id"],["restaurant_id"=>$room_info['restaurant_id']]);
            $restaurant_id=$db2->get("restaurant",["id","userid"],["id"=>$room_info['restaurant_id']]);
            $res=$db2->insert("restaurant_reserve_orders",
                ["order_id"=> "reserve_resraurant-".date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                    "restaurant_id"=>$restaurant_id['id'],
                    "seller_uid"=>$restaurant_id['userid'],     //$_userid,
                    "buyer_uid"=>$buyer_msg['userid'],
                    "addtime"=>$timer,      //strtotime("now"),
                    "updatetime"=>strtotime("now"),
                    "status"=>1,
                    "mobile"=>$mobile,
                    "contact_name"=>$contact_name,
                    "arrive_time"=> date("Y-m-d h:i:s"),
                    "subsidy"=>$buyer_msg['sellersubsidy'],
                    "is_go_order"=>0
                ]);
            if (!$res) {
                die("<script>alert('预定餐桌失败');location.href='?act=show_desk';</script>");
            }
            $order=$db2->query("SELECT LAST_INSERT_ID();")->fetchAll();
            //  die(json_encode(['code'=>getHttpStatus(),'order_id'=>$order[0]["LAST_INSERT_ID()"],"reserve_type"=> $arrs['reserve_type'], "belong_id"=>  $foreignid]));
            $rel=$db2->insert("order_belong_table",[
                "order_id"=>$order[0]["LAST_INSERT_ID()"],
                "belong_table"=> "room",
                "belong_id"=> $room_info['id']
            ]);
            //   更改餐桌状态
            $did = $db2->update("room_message", ["is_use" => $is_use, "edittime" =>strtotime("now")], ["id" => $room_id]);
            if ($did > 0) {
                die("<script>alert('预定房间成功');location.href='?act=show_room';</script>");
            }
        }else{
            die("<script>alert('该房间已经被预定ing');location.href='?act=show_room';</script>");
        }
    }
    // 刷新餐馆的缓存
    function update_cache(){
//        global $_userid;
//        var_dump($_userid);
        //cache_reserve_restaurant();
        cache_reserve_cuisines();
    }

    // 餐馆的开启或者关闭营业
    function openRestaurant(){
        global $db2;
        $restaurant_id=$_POST['id'];
        $status=$_POST['status'];
        if(in_array($status,[0,1])){
            $did = $db2->update("restaurant", ["business" => $status, "edittime" =>strtotime("now")], ["id" => $desk_id]);
            if ($did > 0) {
                die("<script>alert('预定餐桌成功');location.href='?act=show_desk';</script>");
            }
        }else{
            die("<script>alert('失败，请确定是否有该权限.');location.href='?act=show_mes';</script>");
        }

    }


    function book_desk_add(){
        global $db2;
        $desk_id=$_POST["deskid"];
        $timer=strtotime($_POST['timer']);
        if($timer<strtotime ("now")){
            die("<script>alert('预定时间小于当前时间，请重试!!');location.href='?act=show_desk';</script>");
        }
        $desk_info= $db2->get("restaurant_desk", "*", ["id" =>$desk_id]);
//        var_dump($desk_info['restaurant_id']);die;
        if($desk_info && $desk_info['is_use']!='1'){

            $mobile=$_POST['mobile'];
            $contact_name=$_POST['name'];
            $is_use=1;
            $buyer_msg=$db2->get("member",["userid","sellersubsidy"],["mobile"=> $mobile]);
            if(!$buyer_msg){
                die("<script>alert('系统暂无此用户!!');location.href='?act=show_desk';</script>");
            }
            $restaurant_id=$db2->get("restaurant",["id","userid"],["id"=>$desk_info['restaurant_id']]);

            $res=$db2->insert("restaurant_reserve_orders",
                ["order_id"=> "reserve_resraurant-".date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                    "restaurant_id"=>$restaurant_id['id'],
                    "seller_uid"=>$restaurant_id['userid'], //$_userid,
                    "buyer_uid"=>$buyer_msg['userid'],
                    "addtime"=>$timer,      //strtotime("now"),
                    "updatetime"=>strtotime("now"),
                    "status"=>1,
                    "mobile"=>$mobile,
                    "contact_name"=>$contact_name,
                    "arrive_time"=> date("Y-m-d h:i:s"),
                    "subsidy"=>$buyer_msg['sellersubsidy'],
                    "is_go_order"=>0
                ]);
            if (!$res) {
                die("<script>alert('预定餐桌失败');location.href='?act=show_desk';</script>");
            }
//            var_dump($_userid);die;
            $order=$db2->query("SELECT LAST_INSERT_ID();")->fetchAll();
            //  die(json_encode(['code'=>getHttpStatus(),'order_id'=>$order[0]["LAST_INSERT_ID()"],"reserve_type"=> $arrs['reserve_type'], "belong_id"=>  $foreignid]));
            $rel=$db2->insert("order_belong_table",[
                "order_id"=>$order[0]["LAST_INSERT_ID()"],
                "belong_table"=> "desk",
                "belong_id"=>  $desk_info['id']
            ]);
            //   更改餐桌状态
            $did = $db2->update("restaurant_desk", ["is_use" => $is_use, "edittime" =>strtotime("now")], ["id" => $desk_id]);
            if ($did > 0) {
                die("<script>alert('预定餐桌成功');location.href='?act=show_desk';</script>");
            }
        }else{
            die("<script>alert('该餐桌已经被预定');location.href='?act=show_desk';</script>");
        }
    }
    //服务员加菜
    function add_cuisines(){
        global $db2;
        global $_userid;
        global $CFG;
        session_start();
        if (isset($_SESSION['waiter_id'])) {
            $_userid = $_SESSION['_userid'];
        } else if (!$_userid) {
            die("<script>alert('请登录');window.location.href='?act=login_show'</script>");
        }
         $_userid=$db2->get("restaurant_reserve_orders","buyer_uid",["id"=>$_GET['oid']]);
         header( "Location: $CFG[reserve_url]" );
    }

	
	
	
	
	//随机生成识别码
	function getRandomString($len, $chars=null)
	{
		if (is_null($chars)){
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		}  
		mt_srand(10000000*(double)microtime());
		for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
			$str .= $chars[mt_rand(0, $lc)];  
		}
		return $str;
	}
	
	
	
?>