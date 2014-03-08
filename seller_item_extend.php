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
$type = $_REQUEST['type'];

JavaScript::setCharset("UTF-8");
if($item != ""){
	$msg  = "";
	if(!empty($_SESSION['member'])){
		$items = explode(",", $item);
		include './include/db_open.php';
		for($i=0; $i<sizeof($items); $i++){
			$result = mysql_query("SELECT *, DATEDIFF(dateClose, CURRENT_TIMESTAMP) as days FROM Product WHERE Member='" . $_SESSION['member']['No'] . "' AND No = {$items[$i]}");
			$rs = mysql_fetch_array($result);
			if($rs['days'] <= 30){
				$sql = "UPDATE Product SET dateClose=DATE_ADD(dateClose, INTERVAL 6 MONTH) WHERE Member='" . $_SESSION['member']['No'] . "' AND No = {$items[$i]}";
				mysql_query($sql) or die (mysql_error());
			}
			else{
				$msg .= $rs['Name'] . "未達下架前1個月, 尚不可延長提案\\n";
			}
		}
		include './include/db_close.php';
	}
	if($msg != ""){
		JavaScript::Alert($msg);
	}
	JavaScript::Execute("window.parent.location.href='seller_item.php?type=$type&var=" . time() . "'");
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
