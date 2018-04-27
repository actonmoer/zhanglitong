<?php
require 'common.inc.php';
require DT_ROOT . '/include/post.func.php';
require DT_ROOT ."/module/member/member.class.php";
require DT_ROOT ."/include/module.func.php";
/**
 * 路由文件
 * @var [type]
 */
global $_userid;
global $_username;
$timenow = timetodate($DT_TIME, 3);
global $memberurl;
$memberurl = $CFG['url'];
global $myurl;
$myurl = userurl($_username);
global $timenow;
$timenow = timetodate($DT_TIME, 3);
$mobiletype = get_env('mobile');
global $DT_TIME;
if (!isset($_GET['act'])) {
	$route = 'delivery';
} else {
	$route = $_GET['act'];
}

call_user_func($route);

/**
 * 餐厅列表
 * @return [type] [description]
 */
function delivery()
{
	include template('delivery','mobile');
}

/**
 * 餐厅信息页
 * @return [type] [description]
 */
function restaurant()
{
	include template('delivery-list', 'mobile');
}



/**
 * 包房评论展示
 * @return [type] [description]
 */
function takeout_comment()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$evaluate = 1;
	$table    = $DT_PRE . "takeout_comment";
	$table1    = $DT_PRE . "takeout_message";
	$data = array();
	if ($_GET['id']) {
		$condition.=" restaurant_id = '" . intval($_GET['id']) . "'";
	}

	if ($evaluate) {
        $condition .= " AND a.seller_star = ".$evaluate ;
	}

	$for_sql   = "select a.seller_comment,a.seller_ctime,a.seller_star,m.passport,m.username from $table AS a INNER join {$DT_PRE}member m ON a.buyer=m.userid where $condition ";
	$data     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
		$r['head'] = useravatar($r['username'], 'large');
		$r['seller_ctime'] = date('Y-m-d',$r['seller_ctime']);
	    $data['comment'][] = $r;
	}
	$db->free_result($result);

	$condition="id = '" . $_GET[ 'id' ] . "' AND status=3 ";

	$for_sql   = "select id,take_out_shop_name,contact,distribution_price,start_price,shop_tip,thumb,province,city,area,address,userid from $table1 where $condition ";
	$data['restaurant']     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
	    $data['restaurant'] = $r;
	}
	$db->free_result($result);
    
	$m=$db->get_one("SELECT * FROM {$DT_PRE}member WHERE userid=".$data['restaurant']['userid']);
	$data['username']=$m['username']; 
	
	include template('takeout_comment','mobile');
}

/**
 * 外卖详情
 * @return [type] [description]
 */
function details()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$table3 = $DT_PRE . "takeout_opentime";
	$db = $GLOBALS['db'];
	$evaluate = 1;
	$table1    = $DT_PRE . "takeout_message";
	$data = array();
	if ($_GET['id']) {
		$condition.=" restaurant_id = '" . intval($_GET['id']) . "'";
	}

	$condition="id = '" . $_GET[ 'id' ] . "' AND status=3 ";

	$for_sql   = "select id,take_out_shop_name,month_sell_count,thumb_banner1,contact,distribution_price,start_price,shop_tip,thumb,province,city,area,address,userid from $table1 where $condition ";
	$data['restaurant']     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
	    $data['restaurant'] = $r;
	}

	$db->free_result($result);

	$dizhi = $data['restaurant']['province'].$data['restaurant']['city'].$data['restaurant']['area'].$data['restaurant']['address'];

	// 营业时间
	$for_sql = "select * from $table3 where restaurant_id=$_GET[id]";
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
	    $time_array[] = $r;
	    $data['restaurant']['opentime'].="$r[begun_time]-$r[over_time]&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	$db->free_result($result);
    $m=$db->get_one("SELECT * FROM {$DT_PRE}member WHERE userid=".$data['restaurant']['userid']);
	$data['username']=$m['username']; 
	include template('details','mobile');
}

/**
 * 外卖添加订单
 * @return [type] [description]
 */
function takeout_order_add()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$table1    = $DT_PRE . "takeout_message";
	$table2 = $DT_PRE . "takeout_opentime";
	$db = $GLOBALS['db'];
	$data = array();
	$_userid = $GLOBALS['_userid'];
	$_username = $GLOBALS['_username'];
	if ( !$_userid ) {
		die("<script>alert('请登录');window.location.href='logout.php'</script>");
	}

	if (!intval($_GET['id'])) {
		die("<script>alert('请选择外卖店铺');history.back()</script>");
		// $condition.=" restaurant_id = '" . intval($_GET['id']) . "'";
	}	

	$session = new dsession();
	$cart = $_SESSION['takeout_cart'];

	$data = [];
	if ($cart[$_GET['id']]) {	
		$data = $cart[$_GET['id']];
	}

	if (!$data) {
		die("<script>alert('购物车为空');history.back()</script>");
	}
	$for_sql   = "select a.id,a.delivery_time,a.start_price,a.distribution_price,a.userid,c.sellersubsidy from $table1 AS a  INNER JOIN $table2 AS d ON a.id=d.restaurant_id LEFT JOIN {$DT_PRE}member AS c ON a.userid=c.userid where a.id='".$_GET['id']."' AND status=3 AND business=1  AND d.begun_time<='".date("H:i:s")."' AND d.over_time>='".date("H:i:s")."'";

	$result = $db->query($for_sql);
	$takeout_shop = [];
	while ($r = $db->fetch_array($result)) {
		$takeout_shop = $r;
	}
	$db->free_result($result);

	if (!$takeout_shop) {
		die("<script>alert('抱歉,该店铺已经打烊');window.location.href='takeout.php'</script>");
	}

	$subsidy = 0;

	if($takeout_shop['sellersubsidy']>0){
		$subsidy = $data['detail_cash']*$takeout_shop['sellersubsidy']*0.01;
	}
	
	$timer=strtotime('now');
	$timer=$timer+60*$takeout_shop['delivery_time'];
    $delivery_time = date('H:i',$timer);


	$lists = array();
	$addr_id = intval(get_cookie('addr_id'));
	$addr_url = 'address.php?auth=' . encrypt($DT_URL);
	if ($addr_id) {
	    $t = $db->get_one("SELECT * FROM {$DT_PRE}address WHERE itemid=$addr_id");
	    if ($t && $t['username'] == $_username)
	        $lists = $t;
	}
	if (!$lists)
	    $lists = $db->get_one("SELECT * FROM {$DT_PRE}address WHERE username='$_username' ORDER BY listorder ASC,itemid ASC");

	// $condition = "username='$GLOBALS[_username]'";
 //    $result = $db->query("SELECT * FROM {$DT_PRE}address WHERE $condition ORDER BY listorder ASC,itemid ASC LIMIT 1");
	// while($r = $db->fetch_array($result)) {
	// 	$r['adddate'] = timetodate($r['addtime'], 'Y/m/d H:i');
	// 	$lists = $r;
	// }

	include template('takeout_order_add','mobile');
}

/**
 * 外卖地址列表
 * @return [type] [description]
 */
function takeout_address_list()
{
	if(intval($_GET[ 'id' ])){
		$addr_id = $_GET[ 'id' ];
		set_cookie('last_restaurant_id',$addr_id);
	}else{
		$addr_id = intval(get_cookie('last_restaurant_id'));
	}

	$DT_PRE = $GLOBALS['DT_PRE'];
	$table1    = $DT_PRE . "takeout_message";
	$db = $GLOBALS['db'];
	$data = array();
	$_userid = $GLOBALS['_userid'];
	$_username = $GLOBALS['_username'];
	if ( !$_userid ) {
		die("<script>alert('请登录');window.location.href='logout.php'</script>");
	}


	$lists = array();

	$condition = "username='$GLOBALS[_username]'";
    $result = $db->query("SELECT * FROM {$DT_PRE}address WHERE $condition ORDER BY listorder ASC,itemid ASC ");
	while($r = $db->fetch_array($result)) {
		$r['adddate'] = timetodate($r['addtime'], 'Y/m/d H:i');
		$lists[] = $r;
	}

	include template('takeout_address_list','mobile');
}

/**
 * 外卖地址修改
 * @return [type] [description]
 */
function takeout_address_edit()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$table1    = $DT_PRE . "takeout_message";
	$db = $GLOBALS['db'];
	$data = array();
	$_userid = $GLOBALS['_userid'];
	$_username = $GLOBALS['_username'];
	$addr_id = intval($_GET['id']);
	if ( !$_userid ) {
		die("<script>alert('请登录');window.location.href='logout.php'</script>");
	}
	if ( !$addr_id ) {
		die("<script>alert('地址id不能为空');window.location.href='logout.php'</script>");
	}


	$lists = array();

	$condition = " itemid=$addr_id AND username='$GLOBALS[_username]'";
	$t = $db->get_one("SELECT * FROM {$DT_PRE}address WHERE $condition ");
	if (!$t) {
		die("<script>alert('该地址不存在');history.back()</script>");
	}
	include template('takeout_address_edit','mobile');
}


/**
 * 增加外卖地址
 * @return [type] [description]
 */
function takeout_address_add()
{

	if(intval($_GET[ 'id' ])){
		$addr_id = $_GET[ 'id' ];
		set_cookie('last_restaurant_id',$addr_id);
	}else{
		$addr_id = intval(get_cookie('last_restaurant_id'));
	}
	include template('takeout_address_add','mobile');
}

/**
 * 外卖结账
 * @return [type] [description]
 */
function takeout_pay()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$_userid = $GLOBALS['_userid'];

	$table    = $DT_PRE . "takeout_order";
	$table1    = $DT_PRE . "takeout_message";

	$order_id = trim($_GET[ 'order_id' ]);


	if ( !$_userid ) {
		die("<script>alert('请登录');window.location.href='logout.php'</script>");
	}

	if ( !$order_id ) {
		die("<script>alert('订单号不能为空');window.location.href='takeout.php?act=order_list'</script>");
	}

	$condition.=" a.order_id = '" . $order_id . "' AND a.buyer='".$_userid."' AND b.status=3 AND b.business=1 AND d.begun_time<='".date("H:i:s")."' AND d.over_time>='".date("H:i:s")."' ";

	$for_sql   = "select a.order_id,a.amount,a.actual_payment,a.addtime,a.status,b.thumb,b.take_out_shop_name from $table as a left join $table1 as b on a.restaurant_id=b.id  INNER JOIN {$DT_PRE}takeout_opentime AS d ON b.id=d.restaurant_id where $condition order by a.addtime desc ";
	$data     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
	    $data = $r;
	}
	$db->free_result($result);

	if (!$data ) {
		die("<script>alert('抱歉,该订单不存在或者该店铺已打烊');window.location.href='takeout.php?act=order_list'</script>");
	}

	if ( $data['status']!=='1' ) {
		die("<script>alert('该订单已经支付');window.location.href='takeout.php?act=order_list'</script>");
	}

	if ($data['actual_payment'] == '0') {
		die("<script>window.location.href='takeout.php?act=takeout_pay_zlt&order_id=$_GET[order_id]'</script>");
	}

	include template('takeout_pay','mobile');
}

function takeout_pay_zlt()
{
	global $memberurl;global $myurl;global $_username;global $timenow;global $DT_TIME;
	$_money = $GLOBALS['_money'];
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$_userid = $GLOBALS['_userid'];

	$table    = $DT_PRE . "takeout_order";
	$table1    = $DT_PRE . "takeout_message";

	$order_id = trim($_GET[ 'order_id' ]);

	if ( !$_userid ) {
		die("<script>alert('请登录');window.location.href='logout.php'</script>");
	}

	if ( !$order_id ) {
		die("<script>alert('订单号不能为空');window.location.href='takeout.php?act=order_list'</script>");
	}
	$condition ='';
	$condition.=" a.order_id = '" . $order_id . "' AND a.buyer='".$_userid."' AND b.status=3 AND b.business=1 AND d.begun_time<='".date("H:i:s")."' AND d.over_time>='".date("H:i:s")."' ";

	$for_sql   = "select a.id as oid,a.order_id,a.restaurant_id,a.buyer_address,a.amount,a.actual_payment,a.addtime,a.status,b.thumb,b.take_out_shop_name,c.username as seller,c.sellersubsidy from $table as a left join $table1 as b on a.restaurant_id=b.id INNER JOIN {$DT_PRE}takeout_opentime AS d ON b.id=d.restaurant_id  LEFT JOIN {$DT_PRE}member AS c ON a.seller=c.userid where $condition order by a.addtime desc ";

	$data     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
	    $data = $r;
	}
	$db->free_result($result);
	$restaurant_id = $data['restaurant_id'];

	if (!$data ) {
		die("<script>alert('抱歉,该订单不存在或者该店铺已打烊');window.location.href='takeout.php?act=order_list'</script>");
	}

	if ( $data['status']!=='1' ) {
		die("<script>alert('该订单已经支付');window.location.href='takeout.php?act=order_list'</script>");
	}

    $money = $data['actual_payment'];


		// 支付开始
	if ($_POST['takeout_zlt']) {
        is_payword($_username, $_POST['password']) or die("<script>alert('密码错误');history.back()</script>");
        money_add($_username, -$money);
        money_record($_username, -$money, '站内', 'system', '外卖订单支付', '订单号' . $order_id);

        $db->query("UPDATE {$table} SET status=2,updatetime=$DT_TIME WHERE order_id = '" . $order_id . "'");
       
	    $time = time();
		$sql1 = "(order_id,restaurant_id,status,note,editor,addtime)";
		$sql2 = "('$order_id','$restaurant_id','2','支付成功','$_username','$time')";
	    $db->query("INSERT INTO {$DT_PRE}takeout_order_status  $sql1 VALUES$sql2");

		// 发站内信
		$touser = $data['seller'];
        $title = "您有一笔新的外卖订单";
        $url = $memberurl . 'mobile/user_takeout_home.php?act=order&order_id=' . $data['order_id'];
        $trade_message_c2 = "买家 <a href=\"{V0}\" class=\"t\">{V1}</a> 于 <span class=\"f_gray\">{V2}</span> 支付了您的订单<br/><a href=\"{V3}\" class=\"t\" target=\"_blank\">&raquo; 请点这里立即处理或查看详情</a>;";

        $content = lang($trade_message_c2, array($myurl, $_username, $timenow, $url));
        send_message($touser, $title, $content);
		
		//发送短信
		
		$m=$db->get_one("SELECT * FROM ".$DT_PRE ."member WHERE username='$touser'");
		//发送app语音
        $jpushtype = 1;//app类型
        $jpushurl = 'http://www.zhanglitong.com/msg_go.php';//APP跳转地址
		$jpushapiuserid=$m['userid'];//app别名
        $jpushalert = '您有一个外卖订单需要接单！';//消息
        $jpushsound ='您有一个外卖订单需要接单！掌里通提示，您有一个外卖订单需要接单！掌里通提示，您有一个外卖订单需要接单！';//语音
        include 'jpush.php';
        // appuserid 1a0018970a91e557d7b
		if (!empty($m['mobile'])){
		    send_sms($m['mobile'],  '你有新配送订单,点击查看:http://www.zhanglitong.com/msg_go.php');
			send_weixin($touser, '会员：' . $_username . ' 发出配送订单，点击前往查看');
		}
		
        $_money-=$money;
		include template('takeout_pay_success','mobile');
		die();
	}


	include template('takeout_pay_zlt','mobile');
}

// 外卖订单列表展示
function order_list()
{
	include template('takeout_order_list','mobile');
}

// 外卖订单展示
function order()
{	
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$_userid = $GLOBALS['_userid'];

	$table    = $DT_PRE . "takeout_order";
	$table1    = $DT_PRE . "takeout_message";
	$table2    = $DT_PRE . "takeout_data";
	$table3    = $DT_PRE . "takeout_order_status";

	$order_id = trim($_GET[ 'order_id' ]);


	if ( !$_userid ) {
		die("<script>alert('请登录');window.location.href='logout.php'</script>");
	}

	if ( !$order_id ) {
		die("<script>alert('订单号不能为空');window.location.href='takeout.php?act=order_list'</script>");
	}

	$condition.=" a.order_id = '" . $order_id . "' AND a.buyer='".$_userid."'";

	$for_sql   = "select a.note,a.order_id,a.amount,a.fee,a.actual_payment,a.addtime,a.status,b.thumb,b.take_out_shop_name,b.contact,b.id as takeoutid from $table as a left join $table1 as b on a.restaurant_id=b.id where $condition order by a.addtime desc ";
	$data     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
		$r['addtime'] = date( "Y-m-d H:i",$r['addtime'] );
	    $data['order'] = $r;
	}
	$db->free_result($result);
	if (!$data ) {
		die("<script>alert('该订单不存在');window.location.href='takeout.php?act=order_list'</script>");
	}

	$conditio=" order_id = '" . $order_id . "'";

	$for_sql   = "select * from $table2  where $conditio order by id desc ";
	$data['dishes']     = [];
	$result = $db->query($for_sql);

	while ($r = $db->fetch_array($result)) {
	    $data['dishes'][] = $r;
	}
	$db->free_result($result);

	

	$for_sql   = "select note,addtime from $table3 where $conditio order by status desc,addtime desc";
	
	$data['wuliu']     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
		$r['addtime'] = date( "Y-m-d H:i",$r['addtime'] );
	    $data['wuliu'][] = $r;
	}
	$db->free_result($result);

	if (!$data ) {
		die("<script>alert('该订单不存在');window.location.href='takeout.php?act=order_list'</script>");
	}
	include template('takeout_order','mobile');
}

// 外卖订单退款页面
function takeout_orders_refunds()
{	
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$_userid = $GLOBALS['_userid'];

	$table    = $DT_PRE . "takeout_order";
	$table1    = $DT_PRE . "takeout_message";
	$table2    = $DT_PRE . "takeout_data";
	$table3    = $DT_PRE . "takeout_order_status";

	$order_id = trim($_GET[ 'order_id' ]);


	if ( !$_userid ) {
		die("<script>alert('请登录');window.location.href='logout.php'</script>");
	}

	if ( !$order_id ) {
		die("<script>alert('订单号不能为空');window.location.href='takeout.php?act=order_list'</script>");
	}

	$condition.=" a.order_id = '" . $order_id . "' AND a.buyer='".$_userid."'";

	$for_sql   = "select a.order_id,a.amount,a.fee,a.actual_payment,a.addtime,a.status,b.thumb,b.take_out_shop_name,b.contact from $table as a left join $table1 as b on a.restaurant_id=b.id where $condition order by a.addtime desc ";
	$data     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
		$r['addtime'] = date( "Y-m-d H:i",$r['addtime'] );
	    $data['order'] = $r;
	}
	$db->free_result($result);

	

	if (!$data ) {
		die("<script>alert('该订单不存在');window.location.href='takeout.php?act=order_list'</script>");
	}

	$conditio=" order_id = '" . $order_id . "'";

	$for_sql   = "select * from $table2  where $conditio order by id desc ";
	$data['dishes']     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
	    $data['dishes'][] = $r;
	}
	$db->free_result($result);


	if (!$data ) {
		die("<script>alert('该订单不存在');window.location.href='takeout.php?act=order_list'</script>");
	}
	include template('takeout_orders_refunds','mobile');
}


// 外卖评论
function comment_add()
{
	$DT_PRE = $GLOBALS['DT_PRE'];
	$db = $GLOBALS['db'];
	$_userid = $GLOBALS['_userid'];

	$table    = $DT_PRE . "takeout_order";
	$table1    = $DT_PRE . "takeout_message";
	$table2    = $DT_PRE . "takeout_data";
	$table3    = $DT_PRE . "takeout_order_status";

	$order_id = trim($_GET[ 'order_id' ]);


	if ( !$_userid ) {
		die("<script>alert('请登录');window.location.href='logout.php'</script>");
	}

	if ( !$order_id ) {
		die("<script>alert('订单号不能为空');window.location.href='takeout.php?act=order_list'</script>");
	}

	$condition.=" a.order_id = '" . $order_id . "' AND a.buyer='".$_userid."' AND a.status=5";

	$for_sql   = "select a.order_id,a.amount,a.fee,a.actual_payment,a.addtime,a.status,b.thumb,b.take_out_shop_name,b.contact from $table as a left join $table1 as b on a.restaurant_id=b.id where $condition order by a.addtime desc ";
	$data     = [];
	$result = $db->query($for_sql);
	while ($r = $db->fetch_array($result)) {
		$r['addtime'] = date( "Y-m-d H:i",$r['addtime'] );
	    $data['order'] = $r;
	}
	$db->free_result($result);
     
	
	 
	if (!$data ) {
		die("<script>alert('非法操作');window.location.href='takeout.php?act=order_list'</script>");
	}

	include template('takeout_comment_add','mobile');
}

?>
