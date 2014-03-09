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
		$result = mysql_query("SELECT Product.*, Member.Name AS userName, Member.userID FROM Product INNER JOIN Member ON Member.No = Product.Member WHERE Product.No IN ($itemlist)");
		$approve = date('Y-m-d H:i:s');
		while($rs=mysql_fetch_array($result)){
			if($rs['Mode'] == 1){
				$close = date('Y-m-d H:i:s', strtotime($approve . " +" . $rs['daysOnSale'] . " day"));
				if($rs['Deliver'] == 0){
					$close = date('Y-m-d', strtotime($rs['dateExpire'] . " -" . $rs['daysBeforeReserve'] . " day")) . " 23:59:59";
				}
			}
			else{
				if($rs['Activity'] == 1){
					$close = $rs['activity_end'] . " 23:59:59";
				}
				else if($rs['Cashflow'] == 1){
					if($rs['Deliver'] == 0){
						$close = date('Y-m-d H', strtotime($rs['dateExpire'] . "-" . $rs['daysBeforeReserve'] . " day")) . "23:59:59";
					}
					else{
						if($rs['Duration'] == 0){
							$close = "9999-12-31 23:59:59";
						}
						else{
							$close = date('Y-m-d H:i:s', strtotime($approve . " +" . $rs['daysOnSale'] . " day"));
						}
					}
				}
				else{
					$close = date('Y-m-d H:i:s', strtotime($approve . " +6 month"));
					$close = "9999-12-31 23:59:59";
				}
			}
			$code = Tools::newPassword(10);
			$sql = "UPDATE Product SET Status = 2, Code='$code', dateApprove='$approve', dateClose='$close', Sort=0, Reason='' WHERE No ='" . $rs['No'] . "'";
			mysql_query($sql) or die("資料庫錯誤：" . mysql_error());

			if($rs['Mode'] == 2 && $rs['Deliver'] == 0){
				$result1 = mysql_query("SELECT * FROM Product WHERE Status IN(2, 6) AND Mode=2 AND Deliver=0 AND dateClose >= CURRENT_TIMESTAMP AND Member='" . $rs['Member'] . "' ORDER BY Sort, dateApprove DESC") or die(mysql_error());
				$i=0;
				while($rs1=mysql_fetch_array($result1)){
					$i++;
					mysql_query("UPDATE Product SET Sort='" . $i . "' WHERE No='" . $rs1['No'] . "'") or die(mysql_error());
				}
			}
			if($rs['Mode'] == 2 && $rs['Deliver'] == 1){
				$result1 = mysql_query("SELECT * FROM Product WHERE Status IN(2, 6) AND Mode=2 AND Deliver=1 AND dateClose >= CURRENT_TIMESTAMP AND Member='" . $rs['Member'] . "' ORDER BY Sort, dateApprove DESC") or die(mysql_error());
				$i=0;
				while($rs1=mysql_fetch_array($result1)){
					$i++;
					mysql_query("UPDATE Product SET Sort='" . $i . "' WHERE No='" . $rs1['No'] . "'") or die(mysql_error());
				}
			}


			$m_subject = "InTimeGo即購網站商品/活動提案[{$rs['Name']}]通過";
			$m_recipient = $rs['userID'];
			$name=$rs['userName'];
			$date = date('Y-m-d H:i:s');

//			$url = "http://{$WEB_HOST}/product_confirm.php?email={$rs['userID']}&code={$code}&no={$rs['No']}";
			if($rs['Mode'] == 1){
				if($rs['Deliver'] == 0){
					$type=1;
				}
				if($rs['Deliver'] == 1){
					$type=2;
				}
			}
			if($rs['Mode'] == 2){
				if($rs['Deliver'] == 0){
					$type=4;
				}
				if($rs['Deliver'] == 1){
					$type=5;
				}
			}
			$url = "http://{$WEB_HOST}/product{$type}_detail.php?no={$rs['No']}";
	
			$m_content = <<<EOD

親愛的 {$name} ：<br>

此封信件由 InTimeGo即購網(本站) 所發出的。<br>

本站於 {$date} 已通過您的新商品/活動提案，基於網站安全考量，請點選下列網址，以確認您的商品/活動提案內容無誤。<br><br>

<a href="{$url}">{$url}</a>
<br><br>
若此商品/活動內容並非您所提案，代表您的資料已被駭客入侵，建議此時需儘速到網站變更您的密碼，並將商品/活動做下架處理。<br><br>

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
//print_r($_REQUEST);
JavaScript::Redirect("propose.php?tab=$tab&pageno=$pageno&area=$area&type={$_REQUEST['type']}&catalog=$catalog&keyword=$keyword");
?>