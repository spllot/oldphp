<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
JavaScript::setCharset("UTF-8");
if(empty($_SESSION['member'])){
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

$id = $_REQUEST['id'];
$catalog = $_REQUEST['type'];
$content  = $_REQUEST['content'];
if($id != "" && $content != "" && $catalog != ""){
	include './include/db_open.php';
	$result = mysql_query("SELECT * FROM Orders WHERE ID='$id'") or die(mysql_error());
	if($orders = mysql_fetch_array($result)){
		$sql = "INSERT INTO Help SET orderID='$id', Catalog = '$catalog', Member = '" . $_SESSION['member']['No'] . "', isSeller = '0', Seller = '" . $orders['Seller'] . "', Product = '" . $orders['Product'] . "', pName = '" . $orders['pName'] . "', Content = '$content', dateSubmited = CURRENT_TIMESTAMP";
		mysql_query($sql) or die(mysql_error());
		JavaScript::Alert("問題已送出，我們會盡快為您處理!");
		JavaScript::Execute("window.parent.Close()");

	}
	include './include/db_close.php';
}
?>
