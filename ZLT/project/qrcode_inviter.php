<?php

require '../../common.inc.php';

header("Content-type:image/png");
//$_userid or dheader('image/qrcode_error.png');
if (!isset($inviter)) {
    $userinfo = $db->get_one("SELECT * FROM {$db->pre}weixin_inviter WHERE username='$_username'");
    if (!empty($userinfo)) {
        $sid = $userinfo['id'];
    } else {
        $inviter = $_username;
        $db->query("INSERT INTO {$DT_PRE}weixin_inviter (username,addtime) VALUES ('$inviter','$DT_TIME')");
        $sid = $db->insert_id();
    }
} else {
    $userinfo = $db->get_one("SELECT * FROM {$db->pre}weixin_inviter WHERE username='$inviter'");
    if (!empty($userinfo)) {
        $sid = $userinfo['id'];
    } else {
        $db->query("INSERT INTO {$DT_PRE}weixin_inviter (username,addtime) VALUES ('$inviter','$DT_TIME')");
        $sid = $db->insert_id();
    }
}
//var_dump($sid);
//exit;
require DT_ROOT . '/api/weixin/init.inc.php';
$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
$par = '{"action_name": "QR_LIMIT_SCENE","action_info": {"scene": {"scene_id":' . $sid . '}}}';


/*
 * $url = string(178) "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=aBqtkuk6rE45Xw-FNlKGzrvqbv4Yz4N7gmSRgcsCPAS8MzIGpkxnUe3MiPUzvB08ylF6rYqSVUXvKDeNL9TIELjwpAyOeua8sYHbjRZl3R4UGOjAFAKWE" 
 * $par = string(99) "{"expire_seconds": 1800,"action_name": "QR_SCENE","action_info": {"scene": {"scene_id":189788833}}}" 
 * 
 */
$arr = $wx->http_post($url, $par);

/*
 * $url = string(178) "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=aBqtkuk6rE45Xw-FNlKGzrvqbv4Yz4N7gmSRgcsCPAS8MzIGpkxnUe3MiPUzvB08ylF6rYqSVUXvKDeNL9TIELjwpAyOeua8sYHbjRZl3R4UGOjAFAKWE" 
 * $par = string(99) "{"expire_seconds": 1800,"action_name": "QR_SCENE","action_info": {"scene": {"scene_id":887357156}}}" 
 * $arr = array(3) { 
 * ["ticket"]=> string(96) "gQGE8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0YwMHZvSFBsNW5xZi01b3NuMk9CAAIERZFQVgMECAcAAA==" 
 * ["expire_seconds"]=> int(1800) 
 * ["url"]=> string(43) "http://weixin.qq.com/q/F00voHPl5nqf-5osn2OB" } 
 * 
 */
$a=dheader('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($arr['ticket']));
var_dump($a);die;
?>