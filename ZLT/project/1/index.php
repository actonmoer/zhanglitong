<?php

/*
  [Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
  This is NOT a freeware, use is subject to license.txt
 */
/*
  if ($_SERVER['HTTP_HOST'] == 'm.zhanglitong.com') {
  $url = 'http://www.zhanglitong.com' . $_SERVER['PHP_SELF'];
  if ($_SERVER['QUERY_STRING'])
  $url .= '?' . $_SERVER['QUERY_STRING'];
  header('location:' . $url);
  }
 */
require 'common.inc.php';
$mobiletype = get_env('mobile');
/*
 * 在pc�?$mobiletype = array(2) { ["os"]=> string(0) "" ["browser"]=> string(0) "" } 
 */

/*
 * $module = "company"
 * $mobile_modules = array(16) { 
 * [0]=> string(6) "member" 
 * [1]=> string(4) "sell" 
 * [2]=> string(3) "buy" 
 * [3]=> string(5) "quote" 
 * [4]=> string(7) "company" 
 * [5]=> string(7) "exhibit" 
 * [6]=> string(7) "article" 
 * [7]=> string(4) "info" 
 * [8]=> string(3) "job" 
 * [9]=> string(4) "know" 
 * [10]=> string(5) "brand" 
 * [11]=> string(4) "mall" 
 * [12]=> string(5) "group" 
 * [13]=> string(5) "video" 
 * [14]=> string(5) "photo" 
 * [15]=> string(4) "club" } 
 */

/*
  2016.02.12 加上当前用户做邀请人，用�?掌里通生意我也要链接
  exit;
 */

/*
  if (isset($_username) && $action !='ajax') {
  //获取完整的url
  $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  if ($_SERVER['QUERY_STRING'])
  $url .= '?' . $_SERVER['QUERY_STRING'];

  if (strpos($url, 'inviter') === false) {
  if ($_username) {
  $url = add_querystring_var($url, 'inviter', $_username);
  } else {
  $url = add_querystring_var($url, 'inviter', 'destoon');
  }
  gheader($url);
  exit;
  header('location:' . $url);
  } else {

  }
  }
 */


/*
 * 判断是否在微信下
 */
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
    $isweixin = 0;
} else {
    $isweixin = 1;
}
$userlocation = array();
$isweixin = 1;

if ($isweixin == 1) {
    $userlocation = get_cookie('userlocation');

    if (!empty($userlocation)) {
        $ttt = explode(',', $userlocation);
        $userlocation = array('longitude' => $ttt[0], 'latitude' => $ttt[1]);
    } else {
        /*
         * 暂时关闭，把程序和公众号完全对接好再开通�?
          require DT_ROOT . '/api/weixin/jssdk.php';
          $yourAppID = 'wxa79ee47780bf5f07';
          $yourAppSecret = '40209e9a0bc8d77d2787f2f134c57caa';
          $jssdk = new JSSDK($yourAppID, $yourAppSecret);
          $signPackage = $jssdk->GetSignPackage();
         * 
          require DT_ROOT . '/api/weixin/init.inc.php';
          $signPackage = $wx->GetSignPackage();
         */
    }
}



$inviterurl = inviterurl($_username); //string(82) "http://www.zhanglitong.com/mobile/index.php?moduleid=16&itemid=497&inviter=destoon" 
if ($_username)
    $inviter = $_username;



if (in_array($module, $mobile_modules)) {
    $pages = '';


    if ($cityid && !$areaid) {
        $areaid = $cityid;
        $ARE = $AREA[$cityid];
    }


    require DT_ROOT . '/module/' . $module . '/common.inc.php'; //"E:/www/destoon/module/company/common.inc.php"
    include 'include/' . $module . '.inc.php'; //"include/company.inc.php"
} else {
    $ads = array();
    $pid = intval($EXT['mobile_pid']);
    if ($pid > 0) {
        $result = $db->query("SELECT * FROM {$DT_PRE}ad WHERE pid=$pid AND status=3 AND totime>$DT_TIME ORDER BY listorder ASC,addtime ASC LIMIT 10", 'CACHE');
        while ($r = $db->fetch_array($result)) {
            $r['image_src'] = linkurl($r['image_src']);
            $r['url'] = $r['stat'] ? DT_PATH . '/api/redirect.php?aid=' . $r['aid'] : linkurl($r['url']);
            $ads[] = $r;
        }
    }
    $MOD_MY = array();
    $data = '';
    $local = get_cookie('mobile_setting');
    if ($local) {
        $data = $local;
    } else if ($_userid) {
        $data = file_get(DT_ROOT . '/file/user/' . dalloc($_userid) . '/' . $_userid . '/mobile.php');
        if ($data)
            set_cookie('mobile_setting', $data, $DT_TIME + 30 * 86400);
    }
    if ($data) {
        $MOB_MOD = array();
        foreach ($MOB_MODULE as $m) {
            $MOB_MOD[$m['moduleid']] = $m;
        }
        foreach (explode(',', $data) as $id) {
            if (isset($MOB_MOD[$id]))
                $MOD_MY[] = $MOB_MOD[$id];
        }
    }
    if (count($MOD_MY) < 2)
        $MOD_MY = $MOB_MODULE;
    $head_name = $EXT['mobile_sitename'] ? $EXT['mobile_sitename'] : $DT['sitename'];
    $foot = 'home';
    include template('index', 'mobile');
}


if (DT_CHARSET != 'UTF-8')
    toutf8();
?>

<?php

/*
 * http://blog.csdn.net/default7/article/details/12995359
 * 使用PHP程序header location 进行跳转的注意的一个细节！ 
 */

function gheader($url) {
    /*
      echo '<html><head><meta http-equiv="Content-Language" content="zh-CN"><meta HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=gb2312"><meta http-equiv="refresh"
      content="0;url=' . $url . '"><title>loading ... </title></head><body>
      <script>window.location="' . $url . '";</script></body></html>';
      exit();
     */
}
?>