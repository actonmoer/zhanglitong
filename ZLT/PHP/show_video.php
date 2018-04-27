<?php
require 'common.inc.php';

$table  = "destoon_category";
$table2 = "destoon_video_14";
$catid=$id=$video=array();

$sql="SELECT catid,catname from $table WHERE moduleid='14'";
$result=$db->query($sql);
while($row=$db->fetch_array($result)){
	$id[] = $row['catid'];
	$message[]=$row;
}
  
$catid=$_POST['catid']?$_POST['catid']:"";
if($catid!=""){
$result=$db->query("SELECT * from $table2 WHERE catid=$catid");
while($row=$db->fetch_array($result)){
      $video[]=$row;
  }  
} 

          $put = array('code' => 200,'msg' => 'show_video','data'=>$video+$message);          
          exit(json_encode($put));




