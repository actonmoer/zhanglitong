<?php
require 'common.inc.php';
require 'img_api.php';
//商家头像
$thumb = $_FILES[ 'thumb' ];
	if (!$thumb){
	    $put = array('code' =>-210,'msg' => '请选择上传的图片');
	    exit(json_encode($put));
	}
    $img = img_save($thumb);
	if ( !$img['success'] ) {
		$put = array('code' =>-207,'msg' => '图片上传失败','data'=>$img['message']);
	    exit(json_encode($put));
	}else{
	$thumb = $img['url'];
    $put = array('code'=>'200','thumb' =>$thumb);
	exit(json_encode($put)); 
	}
?>