<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

$item = $_REQUEST['item'];
$coupon_name = $_REQUEST['coupon_name'];
$coupon_YN = $_REQUEST['coupon_YN'];
$coupon_info = $_REQUEST['coupon_info'];
$coupon_quota = $_REQUEST['coupon_quota'];
$coupon_generate = $_REQUEST['coupon_generate'];

JavaScript::setCharset("UTF-8");
//exit;
//print_r($_REQUEST);
if($item != "" && $coupon_name != "" && $coupon_info != "" && $coupon_quota != ""){


	
	if(mb_strlen($coupon_name,'utf8') > 10){JavaScript::Alert('商品名稱不可超過10個字'); exit();}
	if(mb_strlen($coupon_info,'utf8') > 45){JavaScript::Alert('優惠活動不可超過45個字'); exit();}

	include './include/db_open.php';
	$result = mysql_query("SELECT * FROM Product WHERE No = '$item'");
	$product = mysql_fetch_array($result);
	$sql = "UPDATE Product SET coupon_YN='$coupon_YN', coupon_name='$coupon_name', coupon_info='$coupon_info', coupon_quota='$coupon_quota', coupon_generate='$coupon_generate' WHERE Member='" . $_SESSION['member']['No'] . "' AND No = '$item' AND coupon_YN=0";
	mysql_query($sql) or die (mysql_error());

	if($coupon_YN == 1){
		if($coupon_generate == 1){
			$char = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
			$result = mysql_query("SELECT DISTINCT Product FROM Coupon WHERE Member='" . $_SESSION['member']['No'] . "'");
			$num = mysql_num_rows($result);
			$prefix = $char[$num%26];
			$codes = "<TABLE cellpadding=1 cellspacing=1>";
			for($i=0; $i<$coupon_quota; $i++){
				if($i % 5 == 0){$codes .= "<tr>";}
				$sort = $i+1;
				$code = $prefix . str_pad($sort, 4, "0", STR_PAD_LEFT) . Tools::newCode(2);
				$sql = "INSERT INTO Coupon SET Product='$item', Serial='$code', Member='". $_SESSION['member']['No'] . "', Sort='$sort'";
				mysql_query($sql);
				$codes .= "<td style=\"padding:2px; text-align:left; width:120px;\" nowrap>(" . $sort . ") " . $code . "</td>";
				if($i % 5 == 4){$codes .= "</tr>";}
			}
			$codes .= "</table>";
			$m_subject = "InTimeGo即購網【{$product['Name']}】優惠活動憑證碼索引";
			$m_recipient = $_SESSION['member']['userID'];
			$m_content = <<<EOD
				<table>
					<tr>
						<td>
							親愛的 {$_SESSION['member']['Name']} ：
						</td>
					</tr>
					<tr>
						<td>
							這封信是由 InTimeGo即購網(本站) 所發送的。<br>
							您已經完成了商品【{$product['Name']}】的優惠活動設定！<br>
							附上系統為您產生的優惠活動憑證碼索引：<br>
							{$codes}
							<br><br>

	<br><br>
						</td>
					</tr>
					<tr>
						<td>
							InTimeGo即購網<br>
							http://{$WEB_HOST}<br>
						</td>
					</tr>
				</table>
EOD;
			$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '" . $rs['Name'] . "', '$m_content', CURRENT_TIMESTAMP)";
			mysql_query($sql) or die (mysql_error());
		}
		else{
			for($i=0; $i<$coupon_quota; $i++){
				$sort = $i+1;
				$code = '';
				$sql = "INSERT INTO Coupon SET Product='$item', Serial='$code', Member='". $_SESSION['member']['No'] . "', Sort='$sort'";
				mysql_query($sql);
			}
		}
	}
	include './include/db_close.php';
	JavaScript::Execute("parent.Close();");
	JavaScript::Execute("window.parent.location.href='seller_item2.php';");
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
