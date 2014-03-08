<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

$item = $_REQUEST['item'];
$product = $_REQUEST['product'];
$diff = $_REQUEST['diff'];

JavaScript::setCharset("UTF-8");
if($item != ""){
	$msg  = "";
	if(!empty($_SESSION['member'])){
		$items = explode(",", $item);
		$index = array_search($product, $items);
//		print_r($items);
//		echo "<br>";
		if($index + $diff >= 0 && $index + $diff < sizeof($items)){
			$temp = $items[$index + $diff];
			$items[$index + $diff] = $items[$index];
			$items[$index] = $temp;
//			print_r($items);
		}

		include './include/db_open.php';
		for($i=0; $i<sizeof($items); $i++){
			mysql_query("UPDATE Product SET Sort='" . ($i+1) . "' WHERE No='" . $items[$i] . "'") or die(mysql_error());
		}
		include './include/db_close.php';
	}
	if($msg != ""){
		JavaScript::Alert($msg);
	}
	JavaScript::Execute("window.parent.location.href='seller_item2.php';");
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
