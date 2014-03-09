<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->seller_export][1])){exit("權限不足!!");}
$no = $_REQUEST['no'];
$Y = $_REQUEST['Y'];
$M = $_REQUEST['M'];
$t = $_REQUEST['t'];
include("../include/db_open.php");

$result = mysql_query("SELECT * FROM logReceipt WHERE Seller='$no' AND Y='$Y' AND M='$M'") or die(mysql_error());
if($receipt = mysql_fetch_array($result)){
	if($receipt['ID'] == ""){
		$type = $receipt['Title'] + 2;
		$result = mysql_query("SELECT * FROM Receipt WHERE Type='$type' AND Total > Counts ORDER BY dateCreate ASC") or die(mysql_error());
		if($queue=mysql_fetch_array($result)){
			$id = substr($queue['Start'], 0, 2) . (substr($queue['Start'], 2) + $queue['Counts']);
			echo $id;
			mysql_query("UPDATE logReceipt SET ID='$id', Product='$t', dateUpdate=CURRENT_TIMESTAMP WHERE Seller='$no' AND Y='$Y' AND M='$M'") or die(mysql_error());
			mysql_query("UPDATE Receipt SET Counts = Counts + 1 WHERE No='" . $queue['No'] . "'") or die(mysql_error());
			if($type == 3){
				$result = mysql_query("SELECT * FROM Member WHERE No='$no'") or die(mysql_error());
				$member = mysql_fetch_array($result);
				$m_subject = "[" . $member['Name'] ."]先生/小姐_電子發票開立通知";
				$m_recipient = $member['userID'];
				$m_content = <<<EOD
				親愛的 {$member['Name']} ({$member['Nick']})，您好：<br><br>
				 
				非常感謝您使用InTimeGo平台販售商品，本月販售所得之發票已開立，你所選擇的發票類型 [電子發票] 將不直接寄送，請至InTimeGo即購網登入至會員後台「商家處理資訊-發票處理 (網站對賣家)」查看發票內容。<br><br>
				 
				再次感謝您的支持與愛護，歡迎您繼續使用我們的平台。 <br><br>

				InTimeGo即購網 敬啟
EOD;
		
				$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
				mysql_query($sql) or die (mysql_error());
			}
		}
	}
	else{
		echo $receipt['ID'];
	}
}
else{
	$sql = "INSERT INTO logReceipt SET Type='2', Title='', Y='$Y', M='$M', Seller='$no', Product='$t'";
	mysql_query($sql) or die(mysql_error());
	$type = 2;
	$result = mysql_query("SELECT * FROM Receipt WHERE Type='$type' AND Total > Counts ORDER BY dateCreate ASC") or die(mysql_error());
	if($queue=mysql_fetch_array($result)){
		$id = substr($queue['Start'], 0, 2) . (substr($queue['Start'], 2) + $queue['Counts']);
		echo $id;
		mysql_query("UPDATE logReceipt SET ID='$id', dateCreate=CURRENT_TIMESTAMP WHERE Seller='$no' AND Y='$Y' AND M='$M'") or die(mysql_error());
		mysql_query("UPDATE Receipt SET Counts = Counts + 1 WHERE No='" . $queue['No'] . "'") or die(mysql_error());
	}
}
include '../include/db_close.php';
?>