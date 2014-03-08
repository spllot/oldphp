<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';

JavaScript::setCharset("UTF-8");
if(empty($_SESSION['member'])){
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
$id=$_REQUEST['id'];
$sort=$_REQUEST['sort'];
include './include/db_open.php';
$result = mysql_query("SELECT *, (SELECT Serial FROM logCertify WHERE orderID=Orders.ID AND Sort='$sort') AS Serial, (SELECT dateSent FROM logCertify WHERE orderID=Orders.ID AND Sort='$sort') AS dateSent FROM Orders WHERE ID='$id' AND Seller='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
if($orders = mysql_fetch_array($result)){
	$result = mysql_query("SELECT * FROM Member WHERE No = '" . $orders['Member'] . "'") or die(mysql_error());
	$member = mysql_fetch_array($result);
	$serial = substr($orders['Serial'], 0, 7) . '***';

	if($orders['dateSent'] == '0000-00-00 00:00:00'){
		$btn = '<input type="button" class="btn" value="發送憑證" onClick="Send();" style="width:100px">';
	}
	else{
		$btn = '<input type="button" class="btn" value="憑證已發送" disabled>';
	}

	if($orders['Deliver'] == 0){
		$info = <<<EOD
			<table>
				<tr>
					<td style="text-align:right">姓名：</td>
					<td style='text-align:left'>{$member['Name']}</td>
				</tr>
				<tr>
					<td style="text-align:right">手機號碼：</td>
					<td style='text-align:left'>{$orders['Phone']}</td>
				</tr>
				<tr>
					<td style="text-align:right">E-mail：</td>
					<td style='text-align:left'>{$member['userID']}</td>
				</tr>
				<tr>
					<td style="text-align:right">憑證碼：</td>
					<td style='text-align:left'>{$serial}</td>
				</tr>
			</table>
EOD;
	}
	else{
		if($orders['Receipt'] == 0){
			$receipt = "<tr><td colspan='2' style='text-align:left'>我不要索取發票/收據</td></tr>";
		}
		else{
			$receipt = "<tr><td colspan='2'>我要索取發票/收據</td></tr>";
			$receipt .= (($orders['uniNo'] != "") ? "<tr><td style='text-align:right'>統一編號：</td><td style='text-align:left'>{$orders['uniNo']}</td></tr>" : "");
			$receipt .= (($orders['Title'] != "") ? "<tr><td style='text-align:right'>發票抬頭：</td><td style='text-align:left'>{$orders['Title']}</td></tr>" : "");
		}
		$info = <<<EOD
			<table>
				<tr>
					<td style="text-align:right">手機號碼：</td>
					<td style='text-align:left'>{$orders['Phone']}</td>
				</tr>
				<tr>
					<td style="text-align:right">E-mail：</td>
					<td style='text-align:left'>{$member['userID']}</td>
				</tr>
				<tr>
					<td style="text-align:right">收貨人：</td>
					<td style='text-align:left'>{$orders['Name']}</td>
				</tr>
				<tr>
					<td style="text-align:right">收貨地址：</td>
					<td style='text-align:left'>{$orders['Address']}</td>
				</tr>{$receipt}
			</table>
EOD;
	}
	echo <<<EOD
		<center>
		<form name="hForm" method="post" action="orders_orders_buyer_send.php" target="iAction">
		<input type="hidden" name="id" value="$id">
		<table width="500">
			<tr>
				<td nowrap style="text-align:left; background:#f3f3f3;" colspan="2">買家聯絡資訊 (訂單編號：{$id})</td>
			</tr>
			<tr>
				<td align="center" colspan="2">{$info}</td>
			</tr>
			<tr>
				<td align="center" colspan="2"><br>{$btn}</td>
			</tr>
		</table>
		</form>
		</center>
EOD;


}
?>
<br>
<br>
<br>
<script language="javascript">
function Send(){
}
</script>