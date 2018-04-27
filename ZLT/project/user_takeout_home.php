<?php
require 'common.inc.php';
require 'img_api.php';
require_once '../include/post.func.php';
//$mobiletype = get_env('mobile');

$act = $_GET['act'];
if ($act == '') {
	$function_name = "shop_mes";
} else {
	$function_name = $act;
}
//检测是否开了酒家4
/*$for_sql = "select * from ".$DT_PRE."restaurant where userid=".$_userid;
$shop_mes = $db->get_one($for_sql);
if (!$shop_mes && $act != 'shopinfo') {
	include template('restaurant_user_index','mobile');
	exit;
}*/
call_user_func($function_name);

function default_mes()
{
	$db = $GLOBALS['db'];
}

/**
 * 添加酒家信息a11
 * @return [type] [description]
 */
function shopinfo()
{	
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$_userid = $GLOBALS['_userid'];

	$table5    = $DT_PRE . "takeout_category";

	$data = array();

	$for_sql   = "select * from $table5";
	$data     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
	    $shop_mes['category'][]= $r;
	}
	$db->free_result($result);
	/*
	$restaurant_id = $db->get_one("select id from destoon_restaurant where userid=".$_userid)['id'];
	$res_v = $db->query("select * from destoon_takeout_opentime where restaurant_id = ".$restaurant_id);

	while ($r = $db->fetch_array($res_v)) {
		$peisong_time[] = $r;
	}*/
	include template('takeout_shopinfo','member');
}

/**
 * 外卖餐厅信息显示
 * @return [type] [description]
 */
function shop_mes()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$_userid = $GLOBALS['_userid'];
	//获取酒家店铺基本信息
	$shop_mes = get_restaurant();
	include template('takeout_user_index','mobile');
}

/**
 * 编辑商家信息
 * @return
 */
function edit_restaurant()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$_userid = $GLOBALS['_userid'];
	$shop_mes = get_restaurant();
	include template('takeout_shopinfo','member');
}

/**
 * 房间列表管理
 * @return [type] [description]
 */
function room_manager()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$_userid = $GLOBALS['_userid'];

	$table = $DT_PRE . "room_message";
	$data = array();
	$condition .= " user_id = '$_userid'";

	$for_sql = "select id,room_name,use_count,people_count,sell_price,now_price,room_time,room_message from $table where $condition ";
	$data = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
		$r['room_time'] = json_decode( $r['room_time'],true );
		$tmp = "";
		if ($r['room_time']['morning'] == 1) {
			$tmp = "早";
		}
		if ($r['room_time']['noon'] == 1) {
			$tmp != '' ? $tmp .= '、中' : $tmp .= '中';
		}
		if ($r['room_time']['night'] == 1) {
			$tmp != '' ? $tmp .= '、晚' : $tmp .= '晚';
		}
		$r['room_time'] = $tmp;
	    $data[] = $r;
	}
	$db->free_result($result);
	include template('restaurant_room_list','mobile');
}

/**
 * 
 */
function room_info()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];

	$room_id = (int)$_GET['id'];
	if (empty($room_id)) {
		include template('restaurant_room_info','mobile');
	}
	$sql = "select * from ".$DT_PRE."room_message" ." where id=".$room_id;
	$data = $db->get_one($sql);
	// var_dump($data);die;
	$data['room_time'] = json_decode($data['room_time'],true);
	if ($data['room_time']['morning'] == 1) {
		$yingye_time = "早";
	}
	if ($data['room_time']['noon'] == 1) {
		$yingye_time != '' ? $yingye_time .= "、午" : $yingye_time .= '午';
	}
	if ($data['room_time']['night'] == 1) {
		$yingye_time != '' ? $yingye_time .= "、晚" : $yingye_time .= '晚';
	}
	// $data['room_scheduled_time'] = json_decode($data['room_scheduled_time'],true);
	// 查询包房使用时间
	$table1    = $DT_PRE . "room_time";
	$condition =" room_id = '" . $room_id . "'";
	$for_sql   = "select room_scheduled_time,morning,noon,night from $table1 where $condition ";
	$room_scheduled_time_old     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
		$r['room_scheduled_time'] = date("Y-m-d",$r['room_scheduled_time']);
		if ($r['morning'] != 1 && $r['morning'] != 0) {
			$r['morning'] = "早";
		}else{
			unset($r['morning']);
		}
		if ($r['noon'] != 1 && $r['noon'] != 0) {
			$r['noon'] = "午";
		}else{
			unset($r['noon']);
		}
		if ($r['night'] != 1 && $r['night'] != 0) {
			$r['night'] = "晚";
		}else{
			unset($r['night']);
		}
	    $room_scheduled_time_old[] = $r;

	}
	// print_r($room_scheduled_time_old);die();
	$db->free_result($result);

	include template("restaurant_room_read","mobile");
}

/**
 * 编辑包房
 * @return [type] [description]
 */
function edit_room_info()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];

	$room_id = (int)$_GET['id'];
	$sql = "select * from ".$DT_PRE."room_message" ." where id=".$room_id;
	$data = $db->get_one($sql);
	$data['room_time'] = json_decode($data['room_time'],true);
	if ($data['room_time']['morning'] == 1) {
		$yingye_time = "早";
	}
	if ($data['room_time']['noon'] == 1) {
		$yingye_time != '' ? $yingye_time .= "、午" : $yingye_time .= '午';
	}
	if ($data['room_time']['night'] == 1) {
		$yingye_time != '' ? $yingye_time .= "、晚" : $yingye_time .= '晚';
	}
	// $data['room_scheduled_time'] = json_decode($data['room_scheduled_time'],true);
	foreach ($data['room_scheduled_time'] as $key => $value) {
		
	}
	include template("restaurant_room_info","mobile");
}

/**
 * 菜单管理
 * @return [type] [description]
 */
function restaurant()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$_userid = $GLOBALS['_userid'];

	$table    = $DT_PRE . "takeout_cuisine_category";
	// $_POST[ 'takeout_id' ] = '1';
	
	
	$data = array();

	$condition.=" user_id = '" . $_userid . "'";


	$for_sql   = "select id,cuisine,edittime from $table where $condition ";
	$data     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
	    $data[] = $r;
	}
	$db->free_result($result);
	include template('takeout_manager','mobile');
}

/**
 * 食物列表
 * @return [type] [description]
 */
function food()
{	
	$shop_mes = get_restaurant();
	$result = $GLOBALS['db']->query("select a.id,a.takeout_id,a.cuisine_id,a.dishes,a.description,a.thumb,a.price,a.sell_price,a.use_count,a.addtime,a.edittime,b.cuisine from ".$GLOBALS['DT_PRE']."takeout_dishes as a LEFT JOIN ".$GLOBALS['DT_PRE']."takeout_cuisine_category as b ON a.cuisine_id=b.id  where a.user_id = ".$GLOBALS['_userid']." AND a.takeout_id = ".$shop_mes['restaurant']['id']);
	$data = [];
	while ($r = $GLOBALS['db']->fetch_array($result)) {
		$data[] = $r;
	}
	include template("takeout_food",'mobile');
}

/**
 * 编辑食物
 * @return [type] [description]
 */
function showfood()
{
	if (!$_GET['id']) {
		die("<script>alert('菜式id不能为空');history.back()</script>");
	}
	$shop_mes = get_restaurant();
	$data = $GLOBALS['db']->get_one("select a.id,a.takeout_id,a.cuisine_id,a.dishes,a.description,a.thumb,a.price,a.sell_price,a.use_count,a.addtime,a.edittime,b.cuisine from ".$GLOBALS['DT_PRE']."takeout_dishes as a LEFT JOIN ".$GLOBALS['DT_PRE']."takeout_cuisine_category as b ON a.cuisine_id=b.id  where a.user_id = ".$GLOBALS['_userid']." AND a.takeout_id = ".$shop_mes['restaurant']['id']." AND a.id =".$_GET['id']);

	if (!$data) {
		die("<script>alert('该菜式不存在');history.back()</script>");
	}
	include template("takeout_showfood",'mobile');
}

/**
 * 编辑菜、添加菜
 * @return [type] [description]
 */
function edit_food()
{

	$shop_mes = get_restaurant();
	$dishes=array();
	 $dishes_times=[];
	if($_GET[ 'id' ]){
	   $dishes = $GLOBALS['db']->get_one("select a.id,a.takeout_id,a.cuisine_id,a.dishes,a.spec_attr,a.is_spec,a.description,a.thumb,a.price,a.sell_price,a.use_count,a.addtime,a.edittime,a.isup,b.cuisine from ".$GLOBALS['DT_PRE']."takeout_dishes as a LEFT JOIN ".$GLOBALS['DT_PRE']."takeout_cuisine_category as b ON a.cuisine_id=b.id  where a.user_id = ".$GLOBALS['_userid']." AND a.takeout_id = ".$shop_mes['restaurant']['id']." AND a.id =".$_GET['id']);
	   $dishes_times=$GLOBALS['db2']->select("takeout_dishes_opentime","*",["dishes_id"=>$_GET[ 'id' ]]);
      
    }
	 
	$cuisine_mes = $GLOBALS['db']->query("select * from ".$GLOBALS['DT_PRE']."takeout_cuisine_category where user_id = ".$GLOBALS['_userid']." AND takeout_id=".$shop_mes['restaurant']['id']);
	$data = [];
	while ($r = $GLOBALS['db']->fetch_array($cuisine_mes)) {
		$data[] = $r;
	}

  
		
	
	include template("takeout_edit_food",'mobile');
}

/**
 * 更新、添加菜
 * @return [type] [description]
 */
function update_food()
{
	
	$shop_mes = get_restaurant();
	//获取菜式分类
	$cuisine_id = $_POST['cuisine_id'];
	$takeout_id = $shop_mes['restaurant']['id'];
	$cuisine_name = $GLOBALS['db']->get_one("select cuisine from ".$GLOBALS['DT_PRE']."takeout_cuisine_category where id=".$cuisine_id." and user_id = ".$GLOBALS['_userid']." AND takeout_id=$takeout_id");
	$cuisine_name = $cuisine_name['cuisine'];

	if (!$cuisine_name) {
		die("<script>alert('菜类不能为空');history.back()</script>");
	}

	$dishes = $_POST['dishes'];
	$sell_price = $_POST['sell_price'];
	$price = $_POST['price'];
	$thumb = '';
	$is_spec=0;
	if($_POST['is_spec']=="1"){
		$is_spec=1;
		
	}
	
	
	
	$isup=0;
	if($_POST['isup']=="1"){
		$isup=1;
		
	}
	
	
	$specStr='';
	
	foreach ($_POST['spec_attr'] as $k=>$v){
	   $specStr.=$v.'|'.$_POST['spec_price'][$k].',';
	}
	$specStr=substr($specStr,0,strlen($specStr)-1);
	
	
	
	
	if (!$_POST['id']) {
		if ( !$_FILES[ 'thumb' ]['name'] ) {
			die("<script>alert('菜式缩略图不能为空');history.back()</script>");
		}
	}else{
		if ( !$_FILES[ 'thumb' ]['name'] ) {
			//$arr=$GLOBALS['db2']->get("takeout_dishes", "thumb", ["id" => $_POST['id']]);
			$arr=$GLOBALS['db']->get_one("select thumb from ".$GLOBALS['DT_PRE']."takeout_dishes where  id='".$_POST['id']."'");
			$thumb = $arr['thumb'];
		
		}
		
		
		
	}

	if($_FILES[ 'thumb' ]['name']){
		$img = img_save($_FILES[ 'thumb' ]);
		if ( !$img['success'] ) {
		die("<script>alert('缩略图上传失败，原因：".$img['message']."');history.back()</script>");
		}
		$thumb = $img['url'];
	}
	


	$time = time();
	$_userid = $GLOBALS['_userid'];

	if (!$_POST['id']) {
		
		
		
		//添加
		$sql = "insert into ".$GLOBALS['DT_PRE']."takeout_dishes (cuisine_id,takeout_id,user_id,dishes,thumb,price,sell_price,addtime,edittime,isup,is_spec,spec_attr)";
		$sql .= "values($cuisine_id,$takeout_id,'$_userid','$dishes','$thumb','$price','$sell_price',$time,$time,'$isup','$is_spec','$specStr')";
	} else {
		$dishes_id=$_POST['id'];
		foreach ($_POST['start'] as $key => $value) {
			$tmp = $_POST['end'][$key];
			if($_POST['times'][$key]){
				
				$GLOBALS['db2']->update("takeout_dishes_opentime",[
				"begun_time"=>$value.':00',
				"over_time"=>$tmp.':00',		
				],
				["id"=>$_POST['times'][$key]]);
				
				
				 //$db->query("update destoon_takeout_opentime set `begun_time`='$value:00',`over_time`='$tmp:00' where id=".$_POST['times'][$key]);
				
			}else{
				$sql_value .= "($dishes_id,'$value:00','$tmp:00',$time)";
				$sql_value = trim($sql_value,',');
				$GLOBALS['db']->query("insert into ".$GLOBALS['DT_PRE']."takeout_dishes_opentime(`dishes_id`,`begun_time`,`over_time`,`add_time`) VALUES $sql_value");
			}
		
	    }
		$sql = " update ".$GLOBALS['DT_PRE']."takeout_dishes set cuisine_id=$cuisine_id,dishes='$dishes',
		sell_price='$sell_price',price='$price',thumb='$thumb',edittime=$time,isup='$isup',is_spec='$is_spec',spec_attr='$specStr' where id =".$_POST['id']." and user_id=$_userid AND takeout_id=$takeout_id";
	}
	$res = $GLOBALS['db']->query($sql);
	if ($res) {
		die("<script>window.location.href='user_takeout_home.php?act=food'</script>");
	} else {
		die("<script>alert('错误，请重试');history.back()</script>");
	}
}

/**
 * 编辑菜系
 */
function edit_cuisine()
{
	$id = $_GET['id'];

	if ( !$_GET[ 'id' ] ) {
		die("<script>alert('菜系id不能为空');history.back()</script>");
	}

	$cuisine_mes = $GLOBALS['db']->get_one("select * from ".$GLOBALS['DT_PRE']."takeout_cuisine_category where id=".$id." and user_id=".$GLOBALS['_userid']);


	if ( !$cuisine_mes ) {
		die("<script>alert('菜系不存在');history.back()</script>");
	}
	include template("takeout_edit_cuisine","mobile");
}

/**
 * 编辑、添加菜系
 * @return [type] [description]
 */
function update_cuisine()
{
	if($_GET['id']) {
		$id = $_GET['id'];
		$cuisine_mes = $GLOBALS['db']->get_one("select * from ".$GLOBALS['DT_PRE']."takeout_cuisine_category where id=".$id." and user_id=".$GLOBALS['_userid']);
	}
	if ($_POST['cuisine']) {
		$cuisine = trim($_POST['cuisine']);
		$id = $_POST['id'];
		$_userid = $GLOBALS['_userid'];
		$time = time();
		if (!empty($id)) {
			$sql = "update ".$GLOBALS['DT_PRE']."takeout_cuisine_category set cuisine='$cuisine',edittime = $time where id = $id";
		} else {
			$shop_mes = get_restaurant();
			$sql = "insert into ".$GLOBALS['DT_PRE']."takeout_cuisine_category (cuisine,edittime,user_id,takeout_id) values('$cuisine',$time,$_userid,".$shop_mes['restaurant']['id'].")";
		}
		$res = $GLOBALS['db']->query($sql);
		if ($res) {
			die("<script>window.location.href='user_takeout_home.php?act=restaurant'</script>");
		} else {
			die("<script>alert('错误，请重试');history.back()</script>");
		}
	}
	include template("takeoutup_date_cuisine","mobile");
}

/**
 * 订单
 */
function order_list()
{
	include template("takeout_home_order","mobile");
}

/**
 * 用户端的订单详情
 * @return [type] [description]
 */
function user_room_orders_mes()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$order_status['1'] =  '待支付';
	$order_status['2'] = '支付成功';
	$order_status['3'] = '订单关闭';
	$order_status['4'] = '退款中';
	$order_status['5'] = '已确认，未评价';
	$order_status['6'] = '订单完成';
	$order_status_style['1'] = "color: #ed4948";
	$order_status_style['2'] = "color: #70d616";
	$order_status_style['3'] = "color: #a1a1a1";
	$order_status_style['4'] = "color: #0d8ece";
	$order_status_style['5'] = "";
	$order_status_style['6'] = "color: #333333";
	$evaluate['0'] = '暂未评论';
	$evaluate['1'] = '好评';
	$evaluate['2'] = '中评';
	$evaluate['3'] = '差评';

	//获取订单信息
	$order_id = $_GET['orderid'];
	$sql = "select o.*,o.addtime as times,room_mes.* from ".$DT_PRE."room_orders as o left join ".$DT_PRE."room_message as room_mes ON 
	 o.room_id=room_mes.id where o.orderid='".$order_id."'";
	$result = $db->get_one($sql);
	$result['style'] = $order_status_style[$result['status']];
	$result['font'] = $order_status[$result['status']];
	$result['evaluate'] = $evaluate[$result['evaluate']];
	$result['addtime'] = date("Y-m-d H:i:s",$result['times']);
	//去除点的菜
	$sql = "select * from ".$DT_PRE."cuisine_orders where order_id='".$order_id."'";
	$res = $db->query($sql);
	$cuisine_orders['1'] = '已确认';
	$cuisine_orders['2'] = '下订单';
	$cuisine_orders['3'] = '订单关闭';
	while ($r = $db->fetch_array($res)) {
		$r['addtime'] = date("Y-m-d H:i:s",$r['addtime']);
		$r['status'] = $cuisine_orders[$r['status']];
		$cuisine[] = $r;
	}
	if ($result['status'] > 1 && $result['status'] < 4) {
		$js_font = "申请退款";
		$js_script = "javascript:tuikuan();";
	} else if ($result['status'] == 4) {
		$js_font = "退款中";
		$js_script = "javascript:;";
	} elseif ($result['status'] == 5) {
		$js_font = "去评价";
		$js_script = "javascript:goComment($order_id);";
	} elseif ($result['status'] == 6) {
		$js_font = "订单完成";
		$js_script = "javascript:;";
	} elseif ($result['status'] == 1) {
		$js_font = "去支付";
		$js_script = "/member/charge.php?action=room_confirm&orderid=$order_id&bank=alipay";
	}
	include template("user_room_orders_mes","mobile");
}
function order()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];

	$_userid = $GLOBALS['_userid'];

	$table    = $DT_PRE . "takeout_order";
	$table1    = $DT_PRE . "takeout_message";
	$table2    = $DT_PRE . "takeout_data";
	$table3    = $DT_PRE . "takeout_order_status";

	if ( !$_GET[ 'order_id' ] ) {
		die("<script>alert('系统繁忙');history.back()</script>");
	}
	$order_id = $_GET[ 'order_id' ];
	$data = array();

	$condition.=" a.order_id = '" . $order_id . "' AND a.seller='".$_userid."'";

	$for_sql   = "select a.order_id,a.buyer_reason,a.buyer_name,a.buyer_phone,a.buyer_address,a.note,a.seller_star,a.amount,a.fee,a.actual_payment,a.addtime,a.status,b.thumb,b.take_out_shop_name,b.contact from $table as a left join $table1 as b on a.restaurant_id=b.id where $condition order by a.addtime desc ";
	$data     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
		$r['addtime'] = date( "Y-m-d H:i",$r['addtime'] );
	    $data['order'] = $r;
	}
	$db->free_result($result);

	$conditio=" order_id = '" . $order_id . "'";

	$for_sql   = "select * from $table2  where $conditio order by id desc ";
	$data['dishes']     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
	    $data['dishes'][] = $r;
	}
	$db->free_result($result);


	$for_sql   = "select note,addtime from $table3 where $conditio order by status asc,addtime asc ";

	$data['wuliu']     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
		$r['addtime'] = date( "Y-m-d H:i",$r['addtime'] );
	    $data['wuliu'][] = $r;
	}
	$db->free_result($result);


	$order_status_name['1'] = "未支付";
	$order_status_name['2'] = "支付成功";
	$order_status_name['3'] = "以接单";
	$order_status_name['4'] = "以配送";
	$order_status_name['5'] = "已确认，未评价";
	$order_status_name['6'] = "订单完成";
	$order_status_name['7'] = "申请退款";
	$order_status_name['8'] = "订单关闭";



	$order_head_name['1'] = "订单详情";
	$order_head_name['2'] = "商家接单";
	$order_head_name['3'] = "商家接单";
	$order_head_name['4'] = "正在派送";
	$order_head_name['5'] = "订单详情";
	$order_head_name['6'] = "订单详情";
	$order_head_name['7'] = "退款审核";
	$order_head_name['8'] = "订单详情";


	$data['order']['status_name'] = $order_status_name[$data['order']['status']];
	$data['order']['order_head_name'] = $order_head_name[$data['order']['status']];

	$order_status_class['1'] = "eva good_eva";
	$order_status_class['2'] = "eva normal_eva";
	$order_status_class['3'] = "eva bad_eva";
	$data['order']['evaluate_class']= $order_status_class[$data['order']['seller_star']];

	if ($data['order']['seller_star'] == 1) {
		$data['order']['evaluate_name'] = "好评";
	} else if ($data['order']['seller_star'] == 2) {
		$data['order']['evaluate_name'] = "中评";
	} else if ($data['order']['seller_star'] == 3){
		$data['order']['evaluate_name'] = "差评";
	} else {
		$data['order']['evaluate_name'] = "";
	}
	include template("takeout_home_order_info","mobile");
}
function comment()
{
	$restaurant = get_restaurant();
	include template("takeout_home_comment_list","mobile");
}

/**
 * 清空包房时间段
 * @return [type] [description]
 */
function clear_history()
{
	include template("clear_room","mobile");
}

/**
 * 用户端 订单列表
 */
function user_room_order()
{
	include template('user_room_order','mobile');
}

/**
 * 返回外卖餐厅的信息
 * @return [type] [description]
 */
function get_restaurant()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$_userid = $GLOBALS['_userid'];
	$table    = $DT_PRE . "takeout_message";
	$table1    = $DT_PRE . "takeout_dishes";
	$table2    = $DT_PRE . "takeout_cuisine_category";
	$table3 = $DT_PRE . "takeout_opentime";
	//获取酒家店铺基本信息
		if (!$_userid) {
		die("<script>alert('请登录');history.back()</script>");
	}
	
	$condition="userid = '" .$_userid . "'";

	$for_sql   = "select id,catid,areaid,delivery_time,thumb_banner1,take_out_shop_name,contact,distribution_price,start_price,shop_tip,thumb,province,city,area,address,business,lng,lat,radius,month_sell_count from $table where $condition ";

	$shop_mes['restaurant'] = $db->get_one($for_sql);
    if($shop_mes['restaurant']){
		// 营业时间
		// var_dump($shop_mes['restaurant']['id']);die;
		$for_sql = "select * from $table3 where restaurant_id=".$shop_mes['restaurant']['id'];
		$result = $db->query($for_sql);
		while ($r = $db->fetch_array($result)) {
			$time_array[] = $r;
			$shop_mes['restaurant']['opentime'].=" $r[begun_time] - $r[over_time]&nbsp;&nbsp;&nbsp;&nbsp;";
			
		}
		// var_dump($time_array);die;
		$db->free_result($result);

		foreach ($time_array as $key => $value) {
			if ($value['begun_time']<date("H:i:s") && $value['over_time']>date("H:i:s")) {
				$shop_mes['is_open'] =1;
			}
		}

		$condition2="takeout_id = '" . $shop_mes['restaurant']['id']  . "'";

		$for_sql   = "select id as category_id,cuisine,edittime from $table2 where $condition2 ";
		$shop_mes['cuisine_category']     = [];
		$result = $db->query($for_sql);
		while ($r = $db->fetch_array($result)) {
			$shop_mes['cuisine_category'][] = $r;
		}
		
		$db->free_result($result);

		$for_sql   = "select id as dishes_id,cuisine_id,description,dishes,thumb,price,sell_price,addtime,edittime from $table1 where $condition2 ";
		$shop_mes['dishes_list']     = [];
		$result = $db->query($for_sql);
		while ($r = $db->fetch_array($result)) {
			$shop_mes['dishes_list'][] = $r;
		}
		
		$db->free_result($result);
		$shop_mes['time_array'] = $time_array;
			
		$table5    = $DT_PRE . "takeout_category";

		$data = array();

		$for_sql   = "select * from $table5";
		$data     = [];
		$result = $db->query($for_sql);
		while ($r = $db->fetch_array($result)) {
			$shop_mes['category'][]= $r;
		}
		$db->free_result($result);
    }
	return $shop_mes;
	
}
