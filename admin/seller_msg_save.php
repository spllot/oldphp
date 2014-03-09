<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/tools.php';
require_once '../class/system.php';
require_once '../class/javascript.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->seller_msg][1])){exit("權限不足!!");}
$no =  Tools::parseInt2($_REQUEST["no"],0);
$reply = $_REQUEST["reply"];
$usefor = $_REQUEST['usefor'];
$tab = $_REQUEST['tab'];
if($no!= "" && $reply != "" ){
	include ("../include/db_open.php");

	mysql_query("UPDATE Help SET Reply='$reply', dateReplied = CURRENT_TIMESTAMP, replyBy = '" . $_SESSION['admin'] . "' WHERE No = '$no'");
	$result = mysql_query("SELECT Help.*, Product.Deliver, Product.Mode, (SELECT Nick FROM Member WHERE No=Help.Seller) AS sName, (SELECT userID FROM Member WHERE No=Help.Seller) AS sEMail, (SELECT Nick FROM Member WHERE No=Help.Member) AS mName, (SELECT userID FROM Member WHERE No=Help.Member) AS mEMail FROM Help INNER JOIN Product ON Product.No = Help.Product WHERE Help.No = '$no'");
	$rs = mysql_fetch_array($result);

	if($rs['Mode'] == 1){
		if($rs['Deliver'] == 0){
			$tab1=1;
		}
		else{
			$tab1=2;
		}
	}
	else{
		if($rs['Deliver'] == 0){
			$tab1=4;
		}
		else{
			$tab1=5;
		}
	}
	$m_subject = "InTimeGo線上回覆通知信";
	$m_recipient = $rs['mEMail'];
	$m_content = str_replace("\n", "<br>", $reply);
	if($rs['isSeller'] == 0){
		$m_content = <<<EOD
			<table>
				<tr>
					<td style="text-align:right" valign="top">賣家商品與資訊：</td>
					<td><a href="http://{$WEB_HOST}/product{$tab1}_detail.php?no={$rs['Product']}" target="_blank">{$rs['pName']}</a></td>
				</tr>
				<tr>
					<td style="text-align:right" valign="top">買家問題詢問內容：</td>
					<td>{$rs['Content']}</td>
				</tr>
				<tr>
					<td style="text-align:right" valign="top">回覆：</td>
					<td>{$m_content}</td>
				</tr>
		</table>
EOD;
	}
	else{
		$m_content = <<<EOD
			<table>
				<tr>
					<td style="text-align:right" valign="top">商品名稱：</td>
					<td><a href="http://{$WEB_HOST}/product{$tab1}_detail.php?no={$rs['Product']}" target="_blank">{$rs['pName']}</a></td>
				</tr>
				<tr>
					<td style="text-align:right" valign="top">賣家問題詢問內容：</td>
					<td>{$rs['Content']}</td>
				</tr>
				<tr>
					<td style="text-align:right" valign="top">回覆：</td>
					<td>{$m_content}</td>
				</tr>
		</table>
EOD;
	}
	$m_memo = "後台問題回覆";
	$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
	mysql_query($sql) or die (mysql_error());

	$sql = "insert into Message(Subject, Content, Sender, `To`, dateSent, Type) SELECT '$m_subject', '$m_content', 'service@intimego.com', userID, CURRENT_TIMESTAMP, '3' FROM Member WHERE No='" . $rs['Member'] . "'";
	mysql_query($sql) or die (mysql_error());
	include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
//echo $sql;
JavaScript::Redirect("seller_msg.php?pageno=$pageno&usefor=$usefor&tab=$tab");
?>