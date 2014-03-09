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
$keyword = $_REQUEST['keyword'];
$tab = $_REQUEST['tab'];
if ($itemlist <> ""){
		include("../include/db_open.php");
		
		$sql = "SELECT *, (SELECT userID FROM Member WHERE Member.No = Product.Member) as EMail FROM Product WHERE No IN($itemlist)";
		$result = mysql_query($sql) or die(mysql_error());
		$subject = "IntimeGo即購網商品下架通知";
		$date = date('Y') . "年" . date('n') . "月" . date('j') . "日";
		while($rs= mysql_fetch_array($result)){
			$content = <<<EOD
			<table>
				<tr>
					<td align="left">親愛的賣家會員您好:</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td align="left">您的 {$rs['Name']} 依照InTimeGo 即購網下架規範，已於{$date}自動被系統刪除，特此告知! 如有新的商品提案，歡迎您隨時連結InTimeGo 即購網建置。</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td align="right"><a href="http://{$WEB_HOST}">InTimeGo 即購網</a> 敬上</td>
				</tr>
			</table>
EOD;
			$sql = "insert into queueEMail(Subject, Content, Recipient, Memo, dateRequested, dateSent) VALUES ('$subject', '$content', '" . $rs['EMail'] . "', '', CURRENT_TIMESTAMP, '0000-00-00 00:00:00')";
			mysql_query($sql) or die (mysql_error());
		}





		$sql = "DELETE FROM Product WHERE No IN ($itemlist)";
		mysql_query($sql) or die("資料庫錯誤：" . mysql_error());
		include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Redirect("propose.php?tab=$tab&pageno=$pageno&area=$area&type=$type&catalog=$catalog&keyword=$keyword");
?>