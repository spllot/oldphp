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
	$payby=array(
		"&nbsp;&nbsp;&nbsp;&nbsp;無&nbsp;&nbsp;&nbsp;&nbsp;",
		"信用卡",
		"Web ATM",
		"ATM轉帳",
		"儲值金"
	);
$id=$_REQUEST['id'];
include './include/db_open.php';
$result = mysql_query("SELECT *, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), '0') AS A1, IFNULL((SELECT payBy FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), 0) AS P1, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 0) AS A2, IFNULL((SELECT payBy FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 4) AS P2 FROM Orders WHERE ID='$id' AND Seller='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
if($orders = mysql_fetch_array($result)){
	$result = mysql_query("SELECT * FROM Member WHERE No = '" . $orders['Member'] . "'") or die(mysql_error());
	$member = mysql_fetch_array($result);
	$result = mysql_query("SELECT * FROM Product WHERE No = '" . $orders['Product'] . "'") or die(mysql_error());
	$product = mysql_fetch_array($result);
	if($orders['Receipt'] == 0){
		$receipt = "<tr><td colspan='2' style='text-align:left'>我不要索取發票/收據</td></tr>";
	}
	else{
		$receipt = "<tr><td colspan='2' style='text-align:left'>我要索取發票/收據</td></tr>";
		$receipt .= (($orders['uniNo'] != "") ? "<tr><td style='text-align:right'>統一編號：</td><td style='text-align:left'>{$orders['uniNo']}</td></tr>" : "");
		$receipt .= (($orders['Title'] != "") ? "<tr><td style='text-align:right'>發票抬頭：</td><td style='text-align:left'>{$orders['Title']}</td></tr>" : "");
	}
	$deliver = $orders['Deliver'];
	$sql = "SELECT Orders.Price, Orders.dateSubmited, Orders.Deliver, Orders.ID, Orders.Status, Orders.pName, Orders.dateShipped, '' as Serial, Items.Refund, '0000-00-00 00:00:00' as dateVertify, '0000-00-00 00:00:00' as dateUse, Items.dateReturn, Items.Amount, Items.dateRefund, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), '0') AS A1, IFNULL((SELECT payBy FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), 0) AS P1, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 0) AS A2, IFNULL((SELECT payBy FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 4) AS P2, Items.Transfer, Items.Expire FROM Orders INNER JOIN Items ON Orders.ID = Items.orderID WHERE Orders.Seller = '" . $_SESSION['member']['No'] . "' AND Deliver=$deliver AND Orders.ID='$id'
	order by dateSubmited DESC, Refund, Serial";
	$result = mysql_query($sql) or die(mysql_error());

	$now = date('Y-m-d H:i:s');
	while($data = mysql_fetch_array($result)){
		if($data['Amount'] > 0){
			$left = $data['Amount'] - $data['Refund'];
			include 'orders_status.php';
			$s[$status] += $data['Amount'];
		}
	}
	foreach($s as $name=>$amount){
		$status1 .= <<<EOD
				<td style="color:white; text-align:center; background:#909090;" nowrap>{$name}</td>
EOD;
		$status2 .= <<<EOD
				<td style="background:white; text-align:center;padding:2px;">{$amount}</td>
EOD;
	}
$status_all =<<<EOD
			<tr>
				<td style="text-align:right" valign="top">付款方式：</td>
				<td style='text-align:left'>
					<table style="background:#606060; border:solid 1px #606060" cellpadding="1" cellspacing="1">
					<Tr>
						<td style="color:white; text-align:center; background:#909090;" nowrap>{$payby[$orders['P1']]}</td>
						<td style="color:white; text-align:center; background:#909090;" nowrap>{$payby[4]}</td>
					</tr>
					<Tr>
						<td style="background:white; text-align:center;padding:2px;">{$orders['A1']}</td>
						<td style="background:white; text-align:center;padding:2px;">{$orders['A2']}</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="text-align:right" valign="top">訂單狀態：</td>
				<td style='text-align:left'>
					<table style="background:#606060; border:solid 1px #606060" cellpadding="1" cellspacing="1">
					<Tr>{$status1}</tr>
					<Tr>{$status2}</tr>
					</table>
				</td>
			</tr>
EOD;


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
					<td style="text-align:right">驗證到期日：</td>
					<td style='text-align:left'>{$product['dateExpire']}</td>
				</tr>{$status_all}{$receipt}
			</table>
EOD;
	}
	else{
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
					<td style="text-align:right">郵遞區號：</td>
					<td style='text-align:left'>{$orders['Zip']}
					<input type="hidden" name="rzip" value="">
					<select name="county" disabled></select>
					<select name="area" disabled></select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">收貨地址：</td>
					<td style='text-align:left'>{$orders['Address']}</td>
				</tr>{$status_all}{$receipt}
			</table>
<script language="javascript">
genCounty(document.hForm.county);
chgCounty(document.hForm.county, hForm.area);
chgArea(document.hForm.area, hForm.rzip);
document.hForm.rzip.value = "{$orders['Zip']}";
setArea(document.hForm.county, hForm.area, hForm.rzip.value);
</script>	
EOD;
	}
	
	echo <<<EOD
		<center>
		<form name="hForm" method="post" action="orders_help_save.php" target="iAction">
		<input type="hidden" name="id" value="$id">
		<table width="500">
			<tr>
				<td nowrap style="text-align:left; background:#f3f3f3;" colspan="2">買家聯絡資訊 (訂單編號：{$id})</td>
			</tr>
			<tr>
				<td align="center" colspan="2">{$info}</td>
			</tr>
			<tr>
				<td align="center" colspan="2"><br><input type="button" class="btn" value="確定" onClick="parent.dialogClose();" style="width:100px"></td>
			</tr>
		</table>
		</form>
		</center>
EOD;


}
?>
<script language="javascript">
function Save(){
	if(!hForm.type[0].checked && !hForm.type[1].checked && !hForm.type[2].checked && !hForm.type[3].checked && !hForm.type[4].checked && !hForm.type[5].checked && !hForm.type[6].checked && !hForm.type[7].checked && !hForm.type[8].checked){
		alert("請選擇問題選項!");
	}
	else if(!hForm.content.value){
		alert("請輸入問題描述!");
	}
	else{
		hForm.submit();
	}
}


</script>