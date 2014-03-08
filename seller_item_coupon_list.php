<?php
include './include/session.php';
$no = $_REQUEST['product'];
$status = (($_REQUEST['status'] != "") ? $_REQUEST['status'] : "0");

include './include/db_open.php';

$sql = "SELECT Coupon.Sort, Coupon.Serial, Coupon.Status, logCoupon.EMail, logCoupon.Phone, logCoupon.dateSent FROM Coupon LEFT Outer join logCoupon ON Coupon.No = logCoupon.couponNo WHERE Coupon.Product='$no' AND Coupon.Status='$status' order by Coupon.Sort";
$result = mysql_query($sql) or die (mysql_error());
if(mysql_num_rows($result) > 0){
	while($rs=mysql_fetch_array($result)){
		$status = (($rs['Status'] == 0) ? "尚未" : "已") . "發送";
		$coupons .=<<<EOD
			<tr><td style="background:white; width:40px; text-align:center">{$rs['Sort']}</td>
				<td style="background:white; width:100px; text-align:center">{$rs['Serial']}</td>
				<td style="background:white; width:70px; text-align:center">{$status}</td>
				<td style="background:white; width:70px; text-align:center; font-size:11px">{$rs['Phone']}&nbsp;</td>
				<td style="background:white; text-align:center; font-size:11px">{$rs['EMail']}&nbsp;</td>
				<td style="background:white; width:70px; text-align:center; font-size:11px">{$rs['dateSent']}&nbsp;</td>
			</tr>
EOD;
	}
}
else{
		$coupons .=<<<EOD
			<tr>
				<td colspan="5" style="background:white; text-align:center">查無資料</td>
			</tr>
EOD;
}


include './include/db_close.php';


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<table cellpadding="1" cellspacing="1" bgcolor=gray width="630">
				<?=$coupons?>
			</table>