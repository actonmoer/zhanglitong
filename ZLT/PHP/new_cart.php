<?php
//购物车与下单
require 'common.inc.php';
//用户登录状态
if(!$_userid){
	$put = array('code'=>-200,'msg'=>'not_login');
	exit(json_encode($put));
}else{
	$userid=$_userid;
}

/*if(!$_POST['action']){
$put = array('code'=>-400,'msg'=>'非法操作');
exit(json_encode($put));
}*/

//传入action值
$action = $_POST['action']?$_POST['action']:'test';

if($action == ''){
	$put = array('code'=>-201,'msg'=>'data_miss');
	exit(json_encode($put));
}else if($action == 'add_cart'){//创建购物车
    //数组序列化
	$data = $_POST['data']?$_POST['data']:$array;
	$data = array($data);
	$data = serialize($data);
    //$data = unserialize($data);
	//exit(json_encode($data));die;
	$cart = $db->get_one("SELECT * FROM {$DT_PRE}cart WHERE userid = '$userid'");
	if(!$cart){
		$check=$db->query("INSERT INTO {$DT_PRE}cart (userid,data,edittime) VALUES ('$userid','$data','$DT_TIME')");
        if($check==false){
			$put = array('code'=>-200,'msg'=>'err','data'=>'购物车创建失败！');
	        exit(json_encode($put));
		}
	}
	if($cart['data']==''){
	    $check=$db->query("UPDATE {$DT_PRE}cart SET data='$data',edittime='$DT_TIME' WHERE userid = $userid");
		if($check==false){
			$put = array('code'=>-200,'msg'=>'err','data'=>'购物车创建失败！');
			exit(json_encode($put));
		}
	}
	$put = array('code'=>200,'msg'=>'ok','data'=>'创建购物车成功！');
	exit(json_encode($put));
}else if($action=='show_cart'){//展示购物车
	$cart = $db->get_one("SELECT * FROM {$DT_PRE}cart WHERE userid = '$userid'");
	if(!$cart || $cart['data']==''){
		$put = array('code'=>-200,'msg'=>'err','data'=>'购物车为空！');
		exit(json_encode($put));
	}else{
		//数组反序列化
	    $data = $cart['data'];
		$data = unserialize($data);
        //购物车数量
		$cart_num = count($data);
		for($i=0;$i<$cart_num;$i++){
			$data_shop_id = $data[$i]['shop_id'];
			$data_status  = $data[$i]['namestatus'];
			$data_message = $data[$i]['goods_message'];
			for($z=0;$z<count($data_message);$z++){
				$goods_id    = $data_message[$z]['goods_id'];
				$data_num = $data_message[$z]['num'];
				if($data_status == 'mall'){
					$cart_shop = $db->get_one("SELECT price,thumb,title FROM  {$DT_PRE}mall WHERE  itemid = $goods_id");
					$data_message[$z]['price']   = $data_num*$cart_shop['price'];
					$data_message[$z]['name']    = $cart_shop['title'];
					$data_message[$z]['pictrue'] = $cart_shop['thumb'];
				}
				if($data_status == 'takeout'){
					$cart_shop = $db->get_one("SELECT sell_price,thumb,dishes FROM  {$DT_PRE}takeout_dishes WHERE  id = $goods_id");
					$data_message[$z]['price']   = $data_num*$cart_shop['sell_price'];
					$data_message[$z]['name']    = $cart_shop['dishes'];
					$data_message[$z]['pictrue'] = $cart_shop['thumb'];
				}   
			     //购物车总价
			    $cart_price+=$data_message[$z]['price'];
                $data[$i]['goods_message'] = $data_message;
			}
			
			   
		}       
		    $data = array('cart_price'=>$cart_price)+$data;
		    $data = array('cart_num'=>$cart_num)+$data;
            $put  = array('code'=>200,'msg'=>'ok','data'=>$data);
			exit(json_encode($put));
	}
		       
}else if($action=='del_cart'){//清空购物车
	$check=$db->query("UPDATE {$DT_PRE}cart SET data='',edittime='$DT_TIME' WHERE userid = $userid ");
	if($check==true){
		$put = array('code'=>200,'msg'=>'ok','data'=>'清空购物车成功！');
		exit(json_encode($put));
	}else{
	    $put = array('code'=>-200,'msg'=>'err','data'=>'清空购物车失败！');
		exit(json_encode($put));
	}

}else if($action=='add_goods'){//添加商品
	$cart = $db->get_one("SELECT * FROM {$DT_PRE}cart WHERE userid = '$userid'");
	$array =array('shop_id'=>'86','namestatus'=>"takeout",'goods_message'=>array(array('goods_id'=>"2283",'num'=>"3",'spec_attr'=>"中")));
    //$array = $_POST['array']?$_POST['array']:$array;
	//不存在购物车
	if(!$cart){
		$data = array($array);
        $data = serialize($data);
		$check=$db->query("INSERT INTO {$DT_PRE}cart (userid,data,edittime) VALUES ('$userid','$data','$DT_TIME')");
		if($check==true){
			$put = array('code'=>200,'msg'=>'ok','data'=>'添加商品成功!');
			exit(json_encode($put));
		}else{
			$put = array('code'=>-200,'msg'=>'err','data'=>'添加商品失败！');
			exit(json_encode($put));
		}
	}
	//购物车为空值
    if($cart['data']==''){
		$data = array($array);
        $data = serialize($data);
		$check=$db->query("UPDATE {$DT_PRE}cart SET data='$data',edittime='$DT_TIME' WHERE userid = $userid");
		if($check==true){
			$put = array('code'=>200,'msg'=>'ok','data'=>'添加商品成功!');
			exit(json_encode($put));
		}else{
			$put = array('code'=>-200,'msg'=>'err','data'=>'添加商品失败！');
			exit(json_encode($put));
		}
	}
	//购物车不为空
    if($cart['data']!=''){
		$data = unserialize($cart['data']);
		$num2=count($data);
        $array =array('shop_id'=>'86','namestatus'=>"takeout",'goods_message'=>array(array('goods_id'=>"2283",'num'=>"3",'spec_attr'=>"中")));
        for($i=0;$i<count($data);$i++){
            if($data[$i]['shop_id']==$array['shop_id'] && $data[$i]['namestatus']==$array['namestatus']){
                $message = $data[$i]['goods_message'];
				$num=count($message);
				for($z=0;$z<$num;$z++){
					if($data[$i]['goods_message'][$z]['goods_id'] == $array['goods_message'][0]['goods_id']){
						$put = array('code'=>-201,'msg'=>'err','data'=>'商品已存在于购物车中');
						exit(json_encode($put));
					}
				}
				$message[$num] = $array['goods_message'][0];
				$data[$i]['goods_message'] = $message;
				$check = 'ok';    
			}else{
				$check2 = 'ok';
			}
		}
		if($check=='ok'){
			unset($data[$num]);
		}
		if($check2=='ok' && $check!='ok'){
			$data[$num2+1] = $array;
		}

		exit(json_encode($data));die;
		$data = serialize($data2);
		$check=$db->query("UPDATE {$DT_PRE}cart SET data='$data',edittime='$DT_TIME' WHERE userid = $userid");
		if($check==true){
			$put = array('code'=>200,'msg'=>'ok','data'=>'添加商品成功！');
			exit(json_encode($put));
		}else{
			$put = array('code'=>-200,'msg'=>'err','data'=>'添加商品失败！');
			exit(json_encode($put));
	    }
	}		
}else if($action=='edit_goods'){//编辑商品
	$cart = $db->get_one("SELECT * FROM {$DT_PRE}cart WHERE userid = $userid");
	if(!$cart || $cart['data']==''){
		$put = array('code'=>-200,'msg'=>'err','data'=>'购物车中不存在该商品');
		exit(json_encode($put));
	}else{
        $array =array('shop_id'=>'86','namestatus'=>"takeout",'goods_message'=>array('goods_id'=>"8888",'num'=>"66",'spec_attr'=>'小'));
		$data = unserialize($cart['data']);
		for($i=0;$i<count($data);$i++){
			//添加表示判断商品是否存在
			
			if($array['shop_id']==$data[$i]['shop_id'] && $data[$i]['namestatus'] == $array['namestatus']){
				for($z=0;$z<count($data[$i]['goods_message']);$z++){
                    //var_dump($data[$i]['goods_message'][$z]['goods_id']);die;
					if( $data[$i]['goods_message'][$z]['goods_id'] == $array['goods_message']['goods_id']){
						$data[$i]['goods_message'][$z]['num']       = $array['goods_message']['num'];
						$data[$i]['goods_message'][$z]['spec_attr'] = $array['goods_message']['spec_attr'];	
						//标志
						$check_data = 'ok';
					}
				}
			}
		} 
			if($check_data!='ok'){
				$put = array('code'=>-200,'msg'=>'err','data'=>'购物车中不存在该商品');
				exit(json_encode($put));		
			}
            //数组序列化更新数据库
            //exit(json_encode($data));
            $data = serialize($data);
            $check=$db->query("UPDATE {$DT_PRE}cart SET data='$data',edittime='$DT_TIME' WHERE userid = $userid");
			if($check==true){
				$put = array('code'=>200,'msg'=>'ok','data'=>'修改商品成功！');
				exit(json_encode($put));
			}else{
				$put = array('code'=>-200,'msg'=>'err','data'=>'修改商品失败！');
				exit(json_encode($put));
	        }
	}			
}else if($action=='del_goods'){//删除商品
	$cart = $db->get_one("SELECT * FROM {$DT_PRE}cart WHERE userid = $userid");
	//购物车为空
	if(!$cart || $cart['data']==''){
		$put = array('code'=>-200,'msg'=>'err','data'=>'购物车为空！');
		exit(json_encode($put));
	}
	//存在购物车
	$array =array('shop_id'=>'86','namestatus'=>'takeout','goods_id'=>828528);
	$data = unserialize($cart['data']);
	//删除选中id所在的购物车
	for($i=0;$i<count($data);$i++){
		if($data[$i]['shop_id']==$array['shop_id'] && $data[$i]['namestatus']==$array['namestatus']){
			for($z=0;$z<count($data[$i]['goods_message']);$z++){
				if( $data[$i]['goods_message'][$z]['goods_id'] == $array['goods_id']){
					if(count($data[$i]['goods_message']) == 1){
						unset($data[$i]);
					}
					if(count($data[$i]['goods_message']) > 1){
						unset($data[$i]['goods_message'][$z]);
					}
					//标志
					$check= 'ok';	
				}
			}	
		}
	}
	if($check!='ok'){
		$put = array('code'=>-201,'msg'=>'err','data'=>'购物车不存在该商品！');
		exit(json_encode($put));	
	}
	//exit(json_encode($data));
	$data = serialize($data);
    $check=$db->query("UPDATE {$DT_PRE}cart SET data='$data',edittime='$DT_TIME' WHERE userid = $userid");
	if($check==true){
		$put = array('code'=>200,'msg'=>'ok','data'=>'删除商品成功！');
		exit(json_encode($put));
	}else{
		$put = array('code'=>-200,'msg'=>'err','data'=>'删除商品失败！');
		exit(json_encode($put));
	} 
}else if($action == 'test'){
    $cart = $db->get_one("SELECT * FROM {$DT_PRE}cart WHERE userid = '$userid'");
	$array =array('shop_id'=>'86','namestatus'=>"takeout",'goods_message'=>array(array('goods_id'=>"2283",'num'=>"3",'spec_attr'=>"中")));
    //$array = $_POST['array']?$_POST['array']:$array;
	//不存在购物车
	if(!$cart){
		$data = array($array);
        $data = serialize($data);
		$check=$db->query("INSERT INTO {$DT_PRE}cart (userid,data,edittime) VALUES ('$userid','$data','$DT_TIME')");
		if($check==true){
			$put = array('code'=>200,'msg'=>'ok','data'=>'添加商品成功!');
			exit(json_encode($put));
		}else{
			$put = array('code'=>-200,'msg'=>'err','data'=>'添加商品失败！');
			exit(json_encode($put));
		}
	}
	//购物车为空值
    if($cart['data']==''){
		$data = array($array);
        $data = serialize($data);
		$check=$db->query("UPDATE {$DT_PRE}cart SET data='$data',edittime='$DT_TIME' WHERE userid = $userid");
		if($check==true){
			$put = array('code'=>200,'msg'=>'ok','data'=>'添加商品成功!');
			exit(json_encode($put));
		}else{
			$put = array('code'=>-200,'msg'=>'err','data'=>'添加商品失败！');
			exit(json_encode($put));
		}
	}
	//购物车不为空
    if($cart['data']!=''){
		$data = unserialize($cart['data']);
		$num2=count($data);
        $array =array('shop_id'=>'86','namestatus'=>"takeout",'goods_message'=>array(array('goods_id'=>"2283",'num'=>"3",'spec_attr'=>"中")));
        for($i=0;$i<count($data);$i++){
            if($data[$i]['shop_id']==$array['shop_id'] && $data[$i]['namestatus']==$array['namestatus']){
                $message = $data[$i]['goods_message'];
				$num=count($message);
				for($z=0;$z<$num;$z++){
					if($data[$i]['goods_message'][$z]['goods_id'] == $array['goods_message'][0]['goods_id']){
						$put = array('code'=>-201,'msg'=>'err','data'=>'商品已存在于购物车中');
						exit(json_encode($put));
					}
				}
				$message[$num] = $array['goods_message'][0];
				$data[$i]['goods_message'] = $message;
				$check = 'ok';    
			}else{
				$check2 = 'ok';
			}
		}
		if($check=='ok'){
			unset($data[$num]);
		}
		if($check2=='ok' && $check!='ok'){
			$data[$num2+1] = $array;
		}

		exit(json_encode($data));die;
		$data = serialize($data2);
		$check=$db->query("UPDATE {$DT_PRE}cart SET data='$data',edittime='$DT_TIME' WHERE userid = $userid");
		if($check==true){
			$put = array('code'=>200,'msg'=>'ok','data'=>'添加商品成功！');
			exit(json_encode($put));
		}else{
			$put = array('code'=>-200,'msg'=>'err','data'=>'添加商品失败！');
			exit(json_encode($put));
	    }
	}
		

}

?>