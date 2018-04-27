<?php
//临时需求
require 'common.inc.php';
if(!$_POST['action']){
	$put = array('code'=>200,'msg'=>'你没权进行此操作！')；
	exit(json_encode($put));
}
$action = $_POST['action']?$_POST['action']:'';
if($action == 'good_sell'){/*外卖首页热销商品*/
	//获取已完成订单订单号
	/*$order = array();
	$result = $db->query("SELECT order_id FROM  {$DT_PRE}takeout_order WHERE status>=5 AND status<=6 AND order_id!=16");
	    while ($r = $db->fetch_array($result)){
			$order[]= $r; 
		}
	//据订单号查找data数据
	$dishes_data = array();
	for($i=0;$i<count($order);$i++){
        $order_id = $order[$i]['order_id'];
		$result = $db->query("SELECT * FROM {$DT_PRE}takeout_data WHERE order_id='$order_id' limit 0,50");
        while ($r = $db->fetch_array($result)){
			$dishes_data[]= $r; 
		}
	}
    //菜单数据分类
    $data = array();
		foreach($dishes_data as $x=>$y){
			$data[$y['dishes_id']][] = $y;
		}
	exit(json_encode($data));
	
	//重构数组
	$count = 0;
    $dishes_id =array();
	foreach($data as $k=>$v){
		for($i=0;$i<count($v);$i++){
			$count= $v[$i]['goods_number']+$count;
		}

		//设置热销商品
		if($count>=100){
			$dishes_id[] = $k; 	
		}
		
	}

	//根据重构的ID，显示热销商品信息
	$good_sell = array();
	 foreach($dishes_id as $k=>$v){
        $cuisine_id = $v;
		$good_sell[] = $db->get_one("SELECT * FROM  {$DT_PRE}takeout_dishes WHERE id ='$cuisine_id'");
	}
	exit(json_encode($goods_sell));*/

	//查找data数据
	$good = array();
	$result = $db->query("SELECT dishes_id ,goods_number FROM  {$DT_PRE}takeout_data");
	    while ($r = $db->fetch_array($result)){
			$good[]= $r; 
		}
	//菜单数据分类
	$data = array();
		foreach($good as $x=>$y){
			$data[$y['dishes_id']][] = $y;
		}
	//重构菜单数组
	$count = 0;
    $dishes_array =array();
	foreach($data as $k=>$v){
		for($i=0;$i<count($data[$k]);$i++){
			$count= $v[$i]['goods_number']+$count;
		}
		//设置热销商品
		if($count>=20){
			$id = $k;
			$num = $count*5;
            $dishes_array[] = array('dishes_id'=>$id,'dishes_sell_num'=>$num);
		}
		//初始化count值
        $count = 0;	
	}
  
    //根据重构的ID，显示热销商品信息
	$good_sell = array();
	 foreach($dishes_array as $k=>$v){
        $cuisine_id = $v['dishes_id'];
		$num = $v['dishes_sell_num'];
		$good_sell[] = $db->get_one("SELECT * FROM  {$DT_PRE}takeout_dishes WHERE id ='$cuisine_id' AND isup=0");
		//增加菜单销售数量
		$good_sell[$k]['dishes_sell_num'] = $num;	
	}
	//去掉不存在的菜单
	$new_good_sell = array();
	foreach($good_sell as $k=>$v){
		if($v['id']!=NULL){
			$new_good_sell[] = $v;  
		}
	}
    //重新排序
	$new_px = array();
	foreach ($new_good_sell as $v) {
		$new_px[] = $v['dishes_sell_num'];
    }
	array_multisort($new_px,SORT_DESC,$new_good_sell);
    $put = array('code'=>200,'msg'=>'ok','data'=>$new_good_sell)；
	exit(json_encode($put));
}else if($action == 'cheap_price'){/*外卖首页特价商品*/
	$cheap = array();
	$result = $db->query("SELECT * FROM {$DT_PRE}takeout_dishes WHERE dishes_discount !='0.00'");
        while ($r = $db->fetch_array($result)){
			$cheap[]= $r; 
		}
	$put = array('code'=>200,'msg'=>'ok','data'=>$cheap)；
	exit(json_encode($put));
}else if($action == 'new_goods'){/*外卖首页新出商品*/
    $new = array();
	$result = $db->query("SELECT * FROM {$DT_PRE}takeout_dishes ORDER BY addtime DESC LIMIT 0,50 ");
        while ($r = $db->fetch_array($result)){
			$new[]= $r; 
		}
	$put = array('code'=>200,'msg'=>'ok','data'=>$new)；
	exit(json_encode($put));
}else{/*action错误*/
    $put = array('code'=>-200,'msg'=>'非法操作'); 
    exit(json_encode($cheap));
}	

?>
			
			
			
	