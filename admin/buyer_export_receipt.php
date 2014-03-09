<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->buyer_export][1])){exit("權限不足!!");}
$feedback['receipt']="";
$no = $_REQUEST['no'];
$type = 3;
$type = 2;
$title = 0;
list($Y, $M) = explode("-", date('Y-m'));



$result = mysql_query("SELECT * FROM logReceipt WHERE Seller='$no' AND Y='$Y' AND M='$M'") or die(mysql_error());
if($rs = mysql_fetch_array($result)){
	$type = $rs['Type'];
	$title = $rs['Title'];
}

$result = mysql_query("SELECT * FROM Config WHERE 1=1");
while($rs=mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}
$result = mysql_query("SELECT COUNT(*) FROM logCoupon WHERE Member='$no' AND Receipt = '' AND Phone <> ''") or die(mysql_error());
list($counts)=mysql_fetch_row($result);
$amount = $counts * $_CONFIG['coupon'];



$id = "";
$sql = "SELECT * FROM logReceipt WHERE Seller='$no' AND Y='$Y' AND M='$M'";
$result1 = mysql_query($sql) or die(mysql_error());
if($log = mysql_fetch_array($result1)){
	$id = $log['ID'] . "(共用)";
	mysql_query("UPDATE logReceipt SET SMS='$amount' WHERE No = '" . $log['No'] . "'") or die(mysql_error());
}
else{
	$result = mysql_query("SELECT * FROM Receipt WHERE Type='" . ($title + 2) . "' AND Total > Counts ORDER BY dateCreate ASC") or die(mysql_error());
	if($queue=mysql_fetch_array($result)){
		$id = substr($queue['Start'], 0, 2) . (substr($queue['Start'], 2) + $queue['Counts']);
	}
}


if($id != ""){
	$feedback['receipt'] = $id;
	$feedback['type'] = $type;
	mysql_query("UPDATE logCoupon SET Receipt='$id' WHERE Member='$no' AND Receipt = '' AND Phone <> ''") or die(mysql_error());
	mysql_query("UPDATE Receipt SET Counts = Counts + 1 WHERE No='" . $queue['No'] . "'") or die(mysql_error());
	mysql_query("INSERT INTO logReceiptSMS SET dateCreate=CURRENT_TIMESTAMP, ID='$id', Member='$no', Type='$type', Title='$title', Amount='$amount'") or die(mysql_error());
	if($type == 3){
		$result = mysql_query("SELECT * FROM Member WHERE No='$no'") or die(mysql_error());
		$member = mysql_fetch_array($result);
		$m_subject = "[" . $member['Name'] ."]先生/小姐_電子發票開立通知";
		$m_recipient = $member['userID'];
		$m_content = <<<EOD
		親愛的 {$member['Name']} ({$member['Nick']})，您好：<br><br>
		 
		非常感謝您使用InTimeGo平台販售商品，本季優惠憑證之發票已開立，發票類型 [電子發票] 將不直接寄送，請至InTimeGo即購網登入至會員後台「商家處理資訊-發票處理 (網站對賣家)」查看發票內容。<br><br>
		 
		再次感謝您的支持與愛護，歡迎您繼續使用我們的平台。 <br><br>

		InTimeGo即購網 敬啟
EOD;

		$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
		mysql_query($sql) or die (mysql_error());
	}
}
echo json_encode($feedback);
include("../include/db_close.php");
?>