<?php
/*$servername =$_SERVER['SERVER_NAME'];//本地服务器
$url_form = $_SERVER['HTTP_REFERER'];//来访地址
if($servername!=substr($url_form,7,strlen($servername))){
     die(json_encode(['msg'=>'禁止翻墙']));
}*/
require 'common.inc.php';
if($moduleid < 4) $moduleid = 4;
$AREA = cache_read('area.php');
$pid = $_POST['pid'] ? $_POST['pid'] :'0';
$back_link = $pid ? 'area.php?moduleid='.$moduleid.'&pid='.$AREA[$pid]['parentid'] : mobileurl($moduleid);
$lists = array();
foreach($AREA as $a) {
	if($a['parentid'] == $pid) $lists[] = $a;
}
$head_title = $MOD['name'].$DT['seo_delimiter'].$head_title;
$put=array('code'=>200,'msg'=>'ok',$lists);
die(json_encode($put));
if(DT_CHARSET != 'UTF-8') toutf8();
?>