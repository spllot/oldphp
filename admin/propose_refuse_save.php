<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
require_once("../class/javascript.php");
require_once("../class/tools.php");
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->propose][1])){exit("權限不足!!");}
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$itemlist = $_REQUEST["memberlist"];
$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$area = $_REQUEST['area'];
$catalog = $_REQUEST['catalog'];
$type = $_REQUEST['type'];
$tab = $_REQUEST['tab'];
$keyword = $_REQUEST['keyword'];
$reason = $_REQUEST['reason'];
if ($itemlist <> ""){
	include("../include/db_open.php");
	$items = explode(",", $itemlist);
	for($i=0; $i<sizeof($items); $i++){
		$result = mysql_query("SELECT Product.Name, Member.Name AS userName, Member.userID FROM Product INNER JOIN Member ON Member.No=Product.Member WHERE Product.No='{$items[$i]}'") or die(mysql_error());
		$product = mysql_fetch_array($result);
		$sql = "UPDATE Product SET Status = 3, Reason='$reason' WHERE No = '{$items[$i]}'";
		mysql_query($sql) or die("資料庫錯誤：" . mysql_error());
		$m_subject = "InTimeGo即購網站商品/活動提案[{$product['Name']}]失敗";
		$m_recipient = $product['userID'];
		$name=$product['userName'];
		$m_content = <<<EOD
親愛的 {$name} ：<br>

此封信件由 InTimeGo即購網(本站) 所發出的。<br>

本次您所提案的新商品/活動內容審核失敗，其原因答覆如下:<br><br>

{$reason}<br><br>

您可以在會員後台，針對您所退回的商品/活動提案內容，重新修訂後再做提案，在此感謝您繼續支持我們提供之服務。<br><br>

InTimeGo即購網<br>
http://{$WEB_HOST}
EOD;

		$m_memo = "即購網提案失敗通知信";
		$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', '$date')";
		mysql_query($sql) or die (mysql_error());
	}
	include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Execute("window.returnValue=1");
JavaScript::Execute("window.close()");
?>