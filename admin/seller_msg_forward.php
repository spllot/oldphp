<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
require_once("../class/javascript.php");
require_once("../class/tools.php");
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->seller_msg][1])){exit("權限不足!!");}
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$itemlist = $_REQUEST["itemlist"];
$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$usefor = $_REQUEST['usefor'];
$tab = $_REQUEST['tab'];
if ($itemlist <> ""){
		include("../include/db_open.php");
		$result = mysql_query("SELECT Help.*, Product.Deliver, Product.Mode, (SELECT Nick FROM Member WHERE No=Help.Seller) AS sName, (SELECT userID FROM Member WHERE No=Help.Seller) AS sEMail, (SELECT Nick FROM Member WHERE No=Help.Member) AS mName, (SELECT userID FROM Member WHERE No=Help.Member) AS mEMail FROM Help INNER JOIN Product ON Product.No = Help.Product WHERE Help.No IN ($itemlist)");
		while($rs=mysql_fetch_array($result)){

			$m_subject = "InTimeGo轉寄買家問題通知信";
			$m_recipient = $rs['sEMail'];

			
			$m_content = <<<EOD
				<span style="color:red">本信件由InTimeGo即購網轉寄，賣家需自行回覆電子郵件給買家</span>
				<table>
					<tr>
						<td style="text-align:right" valign="top">賣家商品與資訊：</td>
						<td><a href="http://{$WEB_HOST}/product{$tab1}_detail.php?no={$rs['Product']}" target="_blank">{$rs['pName']}</a></td>
					</tr>
					<tr>
						<td style="text-align:right" valign="top">買家暱稱：</td>
						<td>{$rs['mName']}</td>
					</tr>
					<tr>
						<td style="text-align:right" valign="top">買家電子郵件：</td>
						<td>{$rs['mEMail']}</td>
					</tr>
					<tr>
						<td style="text-align:right" valign="top">買家問題詢問內容：</td>
						<td>{$rs['Content']}</td>
					</tr>
			</table>
EOD;
			$sql = "UPDATE Help SET dateForward = CURRENT_TIMESTAMP WHERE No = '" . $rs['No'] . "'";
			mysql_query($sql) or die (mysql_error());

			$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
			mysql_query($sql) or die (mysql_error());

			$sql = "insert into Message(Subject, Content, Sender, `To`, dateSent, Type) SELECT '$m_subject', '$m_content', 'service@intimego.com', userID, CURRENT_TIMESTAMP, '3' FROM Member WHERE No='" . $rs['Seller'] . "'";
			mysql_query($sql) or die (mysql_error());
		}
		include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Redirect("seller_msg.php?pageno=$pageno&usefor=$usefor&tab=$tab");
?>