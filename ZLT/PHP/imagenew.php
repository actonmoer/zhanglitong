<?php
require 'common.inc.php';
require 'img_api.php';
//�̼�ͷ��
$thumb = $_FILES[ 'thumb' ];
	if (!$thumb){
	    $put = array('code' =>-210,'msg' => '��ѡ���ϴ���ͼƬ');
	    exit(json_encode($put));
	}
    $img = img_save($thumb);
	if ( !$img['success'] ) {
		$put = array('code' =>-207,'msg' => 'ͼƬ�ϴ�ʧ��','data'=>$img['message']);
	    exit(json_encode($put));
	}else{
	$thumb = $img['url'];
    $put = array('code'=>'200','thumb' =>$thumb);
	exit(json_encode($put)); 
	}
?>