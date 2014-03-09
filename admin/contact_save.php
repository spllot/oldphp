<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/tools.php';
require_once '../class/system.php';
require_once '../class/javascript.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->buyer_msg][1])){exit("權限不足!!");}
$no =  Tools::parseInt2($_REQUEST["no"],0);
$reply = $_REQUEST["reply"];
$usefor = $_REQUEST['usefor'];
if($no!= "" && $reply != "" ){
	include ("../include/db_open.php");
	mysql_query("UPDATE Contact SET Reply='$reply', dateReplied = CURRENT_TIMESTAMP, replyBy = '" . $_SESSION['admin'] . "' WHERE No = '$no'");
	$result = mysql_query("SELECT * FROM Contact WHERE No = '$no'");
	$rs = mysql_fetch_array($result);

	$m_subject = "InTimeGo線上回覆通知信";
	$m_recipient = $rs['EMail'];
	$type = array("", "網站問題詢問", "網站建議事項", "商家合作諮詢");
	switch($rs['Catalog']){
		case 1:
			$contact = "姓名：" . $rs['Name'] . "<br>";
			$contact .= "電子郵件：" . $rs['EMail'] . "<br>";
			$contact .= "問題：" . $rs['Content'] . "<br>";
			break;
		case 2:
			$contact = "姓名：" . $rs['Name'] . "<br>";
			$contact .= "電子郵件：" . $rs['EMail'] . "<br>";
			$contact .= "建議：" . $rs['Content'] . "<br>";
			break;
		case 3:
			$contact = "商家(商品)名稱：" . $rs['Name'] . "<br>";
			$contact .= "網站介紹：" . $rs['Intro'] . "<br>";
			$contact .= "電子郵件：" . $rs['EMail'] . "<br>";
			$contact .= "聯絡人： " . $rs['Contact'] . "<br>";
			$contact .= "聯絡電話：" . $rs['Phone'] . "<br>";
			break;
	}


	$m_content = str_replace("\n", "<br>", $reply);
	$m_content = <<<EOD
		<table>
			<tr>
				<td style="text-align:right" valign="top">{$type[$rs['Catalog']]}：</td>
				<td>{$contact}</td>
			</tr>
			<tr>
				<td style="text-align:right" valign="top">回覆：</td>
				<td>{$m_content}</td>
			</tr>
		</table>
EOD;
	$m_memo = "前台問題回覆";
	$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
	mysql_query($sql) or die (mysql_error());
	include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
//echo $sql;
JavaScript::Redirect("contact.php?pageno=$pageno&usefor=$usefor");
?>