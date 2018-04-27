<?php
require 'common.inc.php';
require  'mapnews.php';
require DT_ROOT ."/include/module.func.php";
require DT_ROOT . '/include/post.func.php';

    if (!$_POST['for_ajax']) {
	die(json_encode(['success'=>true,'message'=>'']));
    }

    $for_ajax=$_POST['for_ajax'];

	if($for_ajax=='takeout_restaurant_list'){
		$table  = $DT_PRE . "takeout_message";
		//获取定位经纬度
        $lat = $_POST['lat']?$_POST['lat']:'23.407463';
		$lng = $_POST['lng']?$_POST['lng']:'113.216342';
		if($lat==''){
			$put = array('success'=>false,'message'=>'','data'=>'lat_lost');
			die(json_encode($put));
		}
		if($lng==''){
			$put = array('success'=>false,'message'=>'','data'=>'lng_lost');
			die(json_encode($put));
		}
        //获取排序方式
		$order =  $_POST['order']?$_POST['order']:'1';
		switch ($order){
			case '1':   //离我最近
				$orderby = 'distance';
				break;
			case '2':	//好评优先
				$orderby = "score DESC ";
				break;
			case '3':	//最新发布
				$orderby = "addtime ASC";
				break;
			case '4':	//人气优先
				$orderby ="month_sell_count DESC";
				break;
			/*case '5':	//价格最低
				$orderby =" sell_price ASC ";
				break;
			case '6':	//价格最高
				$orderby.=" sell_price DESC ";
				break;*/
			default:    //距离最近
				$orderby='distance';
				break;

		}
		//获取分类id
        $catid  = $_POST['catid']?$_POST['catid']:'';
        $search =  $_POST['search']?$_POST['search']:'';
		if($catid!='' && $search!=''){
			$put = array('success'=>false,'message'=>'','data'=>'Unknow_mistake');
			die(json_encode($put));
		}
		$data = array();
		if($catid!='' && $search==''){//选中分类查找店铺
           //die(json_encode('1'));
		   if($orderby=='distance'){
		        $for_sql  = "select * from $table WHERE catid=$catid AND status=3 AND business = 1 ";
		   }else{
				$for_sql  = "select * from $table WHERE catid=$catid AND status=3 AND business = 1 ORDER BY $orderby";
		   }
		   
		   $result = $db->query($for_sql);
		   while ($r = $db->fetch_array($result)){
			   //计算距离加入数组中
			   $datapx = GetDistancenews($r['lat'], $r['lng'], $lat, $lng);
			   $datapx = number_format($datapx,3);
			   $data[] =  $order = array('datapx'=>$datapx)+$r;
		   }
		}else if($search!='' && $catid==''){//搜素商铺名像$seach的商铺
			   //die(json_encode('2'));
               $for_sql  = "select * from $table WHERE take_out_shop_name LIKE '%" . $search . "%' AND status=3 AND business = 1";
			   $result = $db->query($for_sql);
		   while ($r = $db->fetch_array($result)){
			   $datapx = GetDistancenews($r['lat'], $r['lng'], $lat, $lng);
			   $datapx = number_format($datapx,3);
			   $data[] =  $order = array('datapx'=>$datapx)+$r;
		   }
		}else{//查找所有审核通过，正在营业的店铺
			//die(json_encode('3'));
		   if($orderby=='distance'){
			   $for_sql  = "select * from $table WHERE status=3 AND business = 1 ";
		   }else{
		       $for_sql  = "select * from $table WHERE status=3 AND business = 1 ORDER BY $orderby";
		   }
		   $result = $db->query($for_sql);
		   while ($r = $db->fetch_array($result)){
			   $datapx = GetDistancenews($r['lat'], $r['lng'], $lat, $lng);
			   $datapx = number_format($datapx,3);
			   $data[] =  $order = array('datapx'=>$datapx)+$r;
		   }
	}
    //优先按距离排序
	if($orderby=='distance'){
	$datapx = array();
    foreach ($data as $v){
	   $datapx[] = $v['datapx'];
       }
    array_multisort($datapx, SORT_ASC, $data);
	}
    die(json_encode($data));
}
?>