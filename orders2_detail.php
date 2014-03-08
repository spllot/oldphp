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
include './include/db_open.php';
$result = mysql_query("SELECT *, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), '0') AS A1, IFNULL((SELECT payBy FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), 0) AS P1, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 0) AS A2, IFNULL((SELECT payBy FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 4) AS P2 FROM Orders WHERE ID='$id' AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
$payby=array(
	"無",
	"信用卡(Visa)",
	"Web ATM",
	"ATM轉帳",
	"儲值金"
);
$deliver = 1;
$now = date('Y-m-d H:i:s');
if($orders = mysql_fetch_array($result)){
	$sql = "SELECT Orders.*, Items.Amount AS Amount2, Items.Refund, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), '0') AS A1, IFNULL((SELECT payBy FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), 0) AS P1, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 0) AS A2, IFNULL((SELECT payBy FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 4) AS P2, Items.Transfer, Items.Expire FROM Orders INNER JOIN Items ON Orders.ID=Items.orderID WHERE Member = '" . $_SESSION['member']['No'] . "' AND Deliver=$deliver AND Items.Amount > 0 AND Orders.ID='$id'";

	$result = mysql_query($sql) or die(mysql_error());

	while($data = mysql_fetch_array($result)){
		$left = $data['Amount'] - $data['Refund'];
		include 'orders_status.php';
		$s[$status] += $data['Amount2'];
	}
	foreach($s as $name=>$amount){
		$status1 .= <<<EOD
				<td style="color:white; text-align:center; background:#909090;" nowrap>{$name}</td>
EOD;
		$status2 .= <<<EOD
				<td style="background:white; text-align:center;padding:2px;">{$amount}</td>
EOD;
	}

	$total = $orders['A1'] + $orders['A2'];
	$info = <<<EOD
		<table>
			<tr>
				<td style="text-align:right">內容：</td>
				<td style='text-align:left'>{$orders['pName']}</td>
			</tr>
			<tr>
				<td style="text-align:right">金額：</td>
				<td style='text-align:left'>{$orders['Total']}</td>
			</tr>
			<tr>
				<td style="text-align:right" valign="top">付款方式：</td>
				<td style='text-align:left'>
					<table style="background:#606060; border:solid 1px #606060" cellpadding="1" cellspacing="1">
						<tr>
							<td style="color:white; text-align:center; background:#909090; width:100px">{$payby[$orders['P1']]}</td>
							<td style="color:white; text-align:center; background:#909090; width:100px">{$payby[$orders['P2']]}</td>
							<!--td style="color:white; text-align:center; background:#909090; width:100px">合計</td-->
						</tr>
						<tr>
							<td style="background:white; text-align:center;padding:2px">{$orders['A1']}</td>
							<td style="background:white; text-align:center;padding:2px">{$orders['A2']}</td>
							<!--td style="background:white; text-align:center;padding:2px">{$total}</td-->
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="text-align:right">數量：</td>
				<td style='text-align:left'>{$orders['Amount']}</td>
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
		</table>
EOD;
	echo <<<EOD
		<center>
		<form name="hForm" method="post" action="orders_help_save.php" target="iAction">
		<input type="hidden" name="id" value="$id">
		<table width="500">
			<tr>
				<td nowrap style="text-align:left; background:#f3f3f3;" colspan="2">訂單資訊 (訂單編號：{$id})</td>
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
</script>