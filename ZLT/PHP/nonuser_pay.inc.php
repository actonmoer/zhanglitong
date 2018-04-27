<?php

defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT . '/module/' . $module . '/common.inc.php';
require DT_ROOT . '/include/post.func.php';
include load('order.lang');
$PAY = cache_read('pay.php');
$amount = isset($amount) ? dround($amount) : '';

//访问设备
$vister = getvisterinfo();
function get_reason_url($reason) {
    $url = '';
    $arr = explode('|', $reason);
    switch ($arr[0]) {
        case 'deposit':
            $url = 'deposit.php?action=add&sum=' . intval($arr[1]);
            break;
        case 'credit':
            $url = 'credit.php?action=buy&sum=' . intval($arr[1]);
            break;
        case 'vip':
            $url = 'vip.php?action=renew&sum=' . intval($arr[1]);
            break;
        case 'pay':
            if (is_numeric($arr[1]) && is_numeric($arr[2]))
                $url = DT_PATH . 'api/redirect.php?mid=' . intval($arr[1]) . '&itemid=' . intval($arr[2]) . '&sum=1';
            break;
        case 'trade':
            if (is_numeric($arr[1]))
                $url = 'trade.php?action=update&step=pay&itemid=' . intval($arr[1]);
            break;
        case 'trades':
            $url = 'trade.php?action=muti&ids=' . $arr[1];
            break;
        case 'group':
            if (is_numeric($arr[1]))
                $url = 'group.php?action=update&step=pay&itemid=' . intval($arr[1]);
            break;
        default:
            break;
    }
    return $url;
}

function get_reason($reason) {
    global $db, $L;
    $str = '';
    $arr = explode('|', $reason);
    switch ($arr[0]) {
        case 'deposit':
            $str = $L['charge_reason_deposit'];
            break;
        case 'credit':
            $str = $L['charge_reason_credit'];
            break;
        case 'vip':
            $str = $L['charge_reason_vip'];
            break;
        case 'pay':
            if (is_numeric($arr[1]) && is_numeric($arr[2])) {
                $t = $db->get_one("SELECT title FROM " . get_table(intval($arr[1])) . " WHERE itemid=" . intval($arr[2]));
                if ($t)
                    $str = $t['title'];
            }
            break;
        case 'trade':
            if (is_numeric($arr[1])) {
                $t = $db->get_one("SELECT title FROM " . $db->pre . "mall_order WHERE itemid=" . intval($arr[1]));
                if ($t)
                    $str = $t['title'];
            }
            break;
        case 'trades':
            $ids = explode(',', $arr[1]);
            $t = $db->get_one("SELECT title FROM " . $db->pre . "group_order WHERE itemid=" . intval($ids[0]));
            if ($t)
                $str = $L['charge_reason_muti'] . $t['title'] . '...';
            break;
        case 'group':
            if (is_numeric($arr[1])) {
                $t = $db->get_one("SELECT title FROM " . $db->pre . "group_order WHERE itemid=" . intval($arr[1]));
                if ($t)
                    $str = $t['title'];
            }
            break;
        default:
            break;
    }
    return $str;
}
//初始化action
$action = $_POST['action']?$_POST['touser']:'';
switch ($action){
	  //非用户直接支付
      case 'payforseller':
            $touser = $_POST['touser'];
            $money = $_POST['money'];
			$bank = $_POST['bank'];
			$orderid = $_POST['payid'];
			//同步返回地址
			$receive_url = $MOD['linkurl'] . 'nonuser_pay.php';
			$charge_title = "付款给商家";
			$subject = "掌里通付款:$orderid";
			//var_dump($touser,$charge,$bank,$orderid,$receive_url,$charge_title,$subject);
			//die;
			$table = $DT_PRE . "member";
			$sql1 = $db->get_one("SELECT * FROM  $table WHERE username = '$touser'");
			$sql2 = $db->get_one("SELECT * FROM  $table  WHERE username = '$_username'");
            $malldiscount = $sql1['malldiscount'];
			$moneys = $money;
			$charge = $money;
            $s1 = "(orderid,touser,username,money,charge,status)";
            $s2 = "('$orderid','$touser','$_username','$money','$charge','1')";
            $db->query("INSERT INTO {$DT_PRE}mallpay_order  $s1 VALUES $s2");

			if($bank == 'alipay' || $bank == 'weixin'){
				   set_cookie('pay_id',$orderid);
				   $amount = $charge;
				   set_cookie('pay_amount',$amount);
				   set_cookie('returnurl', 'charge.php');
				   set_cookie('bank',$bank);
				   set_cookie('seller',$touser);
				   set_cookie('money',$moneys);
				   $subject_wap = $subject;
				   $show_url = $receive_url;

				   if ($bank == 'weixin') {
								 //微信付款
								 //区分是否微信浏览器 
								  if(strpos($_SERVER['HTTP_USER_AGENT'],"MicroMessenger")){ 
												   $collect_url = '/api/wxpay/jsapi/WxpayAPI_php_v3/example/jsapi.php';
												   header("Location: " . $collect_url . "");
								   }else{
												   include DT_ROOT . '/api/pay/' . $bank . '/send.inc.php';	   
										}

								 }else{            //支付宝付款
												   if ($bank == 'alipay') {
														include DT_ROOT . '/api/pay/' . $bank . 'wap' . '/send.inc.php';
												   }
										   exit;
										} 
								 exit;
						}else{       

								 die("请选择支付方式");

							 }
		 break;
    default:
        /*
         * 从支付宝返回后
         * array(18) { 
          ["body"]=> string(34) "会员(wuzhu)充值(流水号:544)"
          ["buyer_email"]=> string(11) "13281162711"
          ["buyer_id"]=> string(16) "2088802003093155"
          ["exterface"]=> string(25) "create_direct_pay_by_user"
          ["is_success"]=> string(1) "T"
          ["notify_id"]=> string(76) "RqPnCoPT3K9%2Fvwbh3InUHy0IXaYhTMB8dmmisDG2sQ5V0BKZqRHiieZ2n5%2BE%2B6z2NhD%2B"
          ["notify_time"]=> string(19) "2016-04-02 16:26:27"
          ["notify_type"]=> string(17) "trade_status_sync"
          ["out_trade_no"]=> string(3) "544"
          ["payment_type"]=> string(1) "1"
          ["seller_email"]=> string(17) "2861872798@qq.com"
          ["seller_id"]=> string(16) "2088811350424271"
          ["subject"]=> string(21) "掌里通会员充值"
          ["total_fee"]=> string(4) "0.01"
          ["trade_no"]=> string(28) "2016040221001004150219495901"
          ["trade_status"]=> string(13) "TRADE_SUCCESS"
          ["sign"]=> string(32) "ced0760eb7926ca9a19016e8fb813c5e"
          ["sign_type"]=> string(3) "MD5" }
         */

        $_POST = $_DPOST;
        $_GET = $_DGET;
        $head_title = $L['charge_title']; //'完成充值'
        //$passed = true;
        $charge_errcode = '';
        $charge_status = 0;
        $charge_forward = '';
        /*
          0 fail
          1 success
          2 unknow
         */
        // $pay_id = intval(get_cookie('pay_id'));  //因为增加订房和外卖和商场的回调，所以就不取整了
        $pay_id = get_cookie('pay_id');

        //回调增加订房和外卖的回调

        // 判断有没有订单号，订单前缀mall= 商场，room=订房，takeout = 外卖。无后缀表示本来的充值。如果没有订单号，就默认读取充值的第一条。
        if ($pay_id) {

           // 此处增加判断，是订房的订单还是外卖的，还是充值的回调。
            $type = explode('-', $pay_id);
            if ($type[0]=='reserve_restaurant') {//预定餐观
                  include DT_ROOT . '/api/callbacks/reserve_restaurant.php';
                  exit();
                }else if ($type[0]=='takeout') {//外卖
                  include DT_ROOT . '/api/callbacks/takeout.php';
                  exit();
                }else if ($type[0]=='mall') {//商场
                  include DT_ROOT . '/api/callbacks/mall.php';
                  exit();
                }else if ($type[0]=='pay') {//转账与直接支付
                  include DT_ROOT . '/api/callbacks/pay.php';
                  exit();
                }else{//正常充值
                    $r = $db->get_one("SELECT * FROM {$DT_PRE}finance_charge WHERE itemid=$pay_id");
                    if ($r && $r['username'] == $_username) {
                        //
                    } else {
                        $r = $db->get_one("SELECT * FROM {$DT_PRE}finance_charge WHERE username='$_username' ORDER BY itemid DESC");
                    }
                }
        } else {
            $r = $db->get_one("SELECT * FROM {$DT_PRE}finance_charge WHERE username='$_username' ORDER BY itemid DESC");
        }
        /*
         * array(12) { ["itemid"]=> string(3) "545" ["username"]=> string(5) "wuzhu" ["bank"]=> string(6) "alipay" ["amount"]=> string(4) "0.01" ["fee"]=> string(4) "0.00" ["money"]=> string(4) "0.01" ["sendtime"]=> string(10) "1459586376" ["receivetime"]=> string(10) "1459586412" ["editor"]=> string(7) "Nalipay" ["status"]=> string(1) "3" ["reason"]=> string(0) "" ["note"]=> string(0) "" } 
         */
        if ($r) {
            $charge_orderid = $r['itemid'];
            $charge_money = $r['amount'] + $r['fee'];
            $charge_amount = $r['amount'];
            if ($r['status'] == 0) {
                $receive_url = '';
                $bank = $r['bank'];
                $editor = 'R' . $bank;
                $note = '';

                /*
                 * 调用支付接口，做认证，获取$charge_status
                 * 写资金流水
                 */
                if ($vister == 'pc') {
                    include DT_ROOT . '/api/pay/' . $bank . '/receive.inc.php';
                } else {
                    if ($bank == 'alipay') {
                        include DT_ROOT . '/api/pay/' . $bank . 'wap' . '/receive.inc.php';
                    } else {
                        include DT_ROOT . '/api/pay/' . $bank . '/receive.inc.php';
                    }
                }
                if ($charge_status == 1) {
                    $db->query("UPDATE {$DT_PRE}finance_charge SET status=3,money=$charge_money,receivetime='$DT_TIME',editor='$editor' WHERE itemid=$charge_orderid");
                    money_add($r['username'], $r['amount']);
                    money_record($r['username'], $r['amount'], $PAY[$bank]['name'], 'system', $L['charge_online'], $L['charge_id'] . ':' . $charge_orderid);
                    if ($MOD['credit_charge'] > 0) {
                        $credit = intval($r['amount'] * $MOD['credit_charge']);
                        if ($credit > 0) {
                            credit_add($r['username'], $credit);
                            credit_record($r['username'], $credit, 'system', $L['charge_reward'], $L['charge'] . $r['amount'] . $DT['money_unit']);
                        }
                    }
                    if ($r['reason']) {
                        $url = get_reason_url($r['reason']);
                        if ($url)
                            $charge_forward = $url;
                    }
                    if ($bank == 'tenpay') {
                        $show_url = $charge_forward ? $charge_forward : 'charge.php';
                        if (strpos($show_url, '://') === false)
                            $show_url = $MOD['linkurl'] . $show_url;
                        $resHandler->doShow($show_url);
                    }
                } else if ($charge_status == 2) {
                    $db->query("UPDATE {$DT_PRE}finance_charge SET status=1,receivetime='$DT_TIME',editor='$editor',note='$note' WHERE itemid=$charge_orderid");
                }
            } else if ($r['status'] == 1) {
                $charge_status = 2;
                $charge_errcode = $L['charge_msg_order_fail'] . $charge_orderid; //'订单状态为失败，ID:'
            } else if ($r['status'] == 2) {
                $charge_status = 2;
                $charge_errcode = $L['charge_msg_order_cancel'] . $charge_orderid; //'订单状态为作废，ID:'
            } else {
                if ($DT_TIME - $r['receivetime'] < 600) {
                    if ($r['reason']) {
                        $url = get_reason_url($r['reason']);
                        if ($url)
                            $charge_forward = $url;
                    }
                    $charge_status = 1;
                } else {
                    dheader('?action=record'); //转充值记录
                }
            }
        } else {
            $charge_status = 2;
            $charge_errcode = $L['charge_msg_not_order']; //'未找到充值纪录'
        }
        break;
}
include template('charge', $module);
?>