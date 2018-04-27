<?php
require 'common.inc.php';
require  'mapnews.php';
require DT_ROOT ."/include/module.func.php";
require DT_ROOT . '/include/post.func.php';
if (!$_POST[ 'for_ajax' ]) {
	$put = array('code'=>-200,'msg'=>'err','data'=>'action_miss');
	exit(json_encode($put));
	}
$for_ajax = $_POST['for_ajax']?$_POST['for_ajax']:'';
if($for_ajax == 'takeout_restaurant'){
	/*检测商铺*/
	$id=86;
	//$id = $_POST['id']?$_POST['for_ajax']:'86';
    $get_lat  = $_POST[ 'lat' ]?$_POST[ 'lat' ]:'';
	$get_lng  = $_POST[ 'lng' ]?$_POST[ 'lng' ]:'';
	if($id==''){
		$put = array('code'=>-201,'msg'=>'err','data'=>'店铺id为空！');
		exit(json_encode($put));	
	}
    if($get_lat=='' && $get_lng==''){
		$put = array('code'=>-202,'msg'=>'err','data'=>'定位失败！');
		exit(json_encode($put));	
	}
    $res = $db->get_one("SELECT * FROM  {$DT_PRE}takeout_message WHERE  id ='$id'");
	if(!$res){
		$put = array('code'=>-203,'msg'=>'err','data'=>'店铺不存在！');
		exit(json_encode($put));
	}
	if($res['status']!= 3){
		$put = array('code'=>-204,'msg'=>'err','data'=>'店铺未审核！');
		exit(json_encode($put));
	}
    if($res['business']!=1){
		$put = array('code'=>-205,'msg'=>'err','data'=>'店铺未营业！');
		exit(json_encode($put));
	}
	$distancece = GetDistancenews($r['lat'], $r['lng'], $lat, $lng);
	if($distancece>$res['radius']){
	    $put = array('code'=>-206,'msg'=>'err','data'=>'不在配送范围！');
		exit(json_encode($put));
	}
    //营业时间
	$time =  array();
	$result = $db->query("SELECT * FROM {$DT_PRE}takeout_opentime WHERE restaurant_id=$id");
	while ($r = $db->fetch_array($result)){
		$time[] = array($r['begun_time'],$r['over_time']);   
	}
   
	for($i=0;$i<count($time);$i++){
		if ($time[$i][0]<date("H:i:s") && $time[$i][1]>date("H:i:s")) {
			$check = 'ok';	
		}		
	}
	if($check!='ok'){
	    $put = array('code'=>-207,'msg'=>'err','data'=>'不在营业时间！');
		exit(json_encode($put));
	}
	//得到数组，重组数组
    $cuisine_category =array();
    $dishes = array();
    $result = $db->query("SELECT id,cuisine,edittime FROM  {$DT_PRE}takeout_cuisine_category WHERE takeout_id ='$id'");
	while ($r = $db->fetch_array($result)){
			   $cuisine_category[] = $r;
		   }

    foreach($cuisine_category as $k=>$v){
        $cuisine_id = $v['id'];
		$result = $db->query("SELECT * FROM  destoon_takeout_dishes WHERE  cuisine_id ='$cuisine_id'");
	    while ($r = $db->fetch_array($result)){
			$dishes[]= $r; 
		}
		//菜单分类
		$data = array();
		foreach($dishes as $x=>$y){
			$data[$y['cuisine_id']][] = $y;
		}
		//将分类写入数组cuisine_category中
		$cuisine_category[$k]['dishes_data'] = $data[$v['id']];
		
	}
	/*热销商品*/
	$order = array();
	$result = $db->query("SELECT order_id FROM  {$DT_PRE}takeout_order WHERE  restaurant_id=$id AND status>=5 AND status<=6");
	    while ($r = $db->fetch_array($result)){
			$order[]= $r; 
		}
	$dishes_data = array();
	for($i=0;$i<count($order);$i++){
        $order_id = $order[$i]['order_id'];
		$result = $db->query("SELECT * FROM {$DT_PRE}takeout_data WHERE order_id='$order_id'");
        while ($r = $db->fetch_array($result)){
			$dishes_data[]= $r; 
		}
	}
    //菜单数据分类
    $data = array();
		foreach($dishes_data as $x=>$y){
			$data[$y['dishes_id']][] = $y;
		}
	//重构数组
	$count = 0;
    $dishes_id =array();
	foreach($data as $k=>$v){
		for($i=0;$i<count($v);$i++){
			$count= $v[$i]['goods_number']+$count;
		}
		//设置热销商品
		if($count>=2){
			$dishes_id[] = $k; 	
		}
		
	}
	//根据重构的ID，显示热销商品信息
	$good_sell = array();
	 foreach($dishes_id as $k=>$v){
        $cuisine_id = $v;
		$good_sell[] = $db->get_one("SELECT * FROM  {$DT_PRE}takeout_dishes WHERE id ='$cuisine_id'");
	}
	/*特价商品*/
	$cheap = array();
	$result = $db->query("SELECT * FROM {$DT_PRE}takeout_dishes WHERE takeout_id=$id AND dishes_discount !='0.00'");
        while ($r = $db->fetch_array($result)){
			$cheap[]= $r; 
		}
	/*组合数组*/
    $data = array('cuisine_category'=>$cuisine_category)+$res;
	$data = array('good_sell_goods'=>$good_sell)+array('cheap_sell_goods'=>$cheap)+$data;
	exit(json_encode($data));

}else{
    $put = array('code'=>-200,'msg'=>'err','data'=>'action_miss');
	exit(json_encode($put));
}  

?>
			
			
			
	