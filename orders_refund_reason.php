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
$id=$_REQUEST['no'];
include './include/db_open.php';
$result = mysql_query("SELECT Orders.pName, Orders.ID, Items.Reason, Orders.Amount, Items.Amount AS Amount2, Items.Refund, Items.dateRequest FROM Orders INNER JOIN Items ON Orders.ID = Items.orderID WHERE Items.No='$id' AND Orders.Seller='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
if($orders = mysql_fetch_array($result)){
	$left = $orders['Amount'] - $orders['Refund'];
	echo <<<EOD
		<center>
		<table width="700">
			<tr>
				<td nowrap style="text-align:left; background:#f3f3f3;" colspan="2">商品交易記錄-{$orders['ID']}退訂問題</td>
			</tr>
			<tr>
				<td valign="top" align="right" nowrap>品名：</td>
				<td nowrap style="color:blue1;text-align:left">{$orders['pName']}</td>
			</tr>
			<tr>
				<td valign="top" align="right" nowrap>退訂日期：</td>
				<td nowrap style="color:blue1;text-align:left">{$orders['dateRequest']}</td>
			</tr>
			<tr>
				<td valign="top" align="right" nowrap>問題描述：</td>
				<td nowrap style="">
					<textarea name="content" style="color:blue1;width:600px; height:100px">{$orders['Reason']}</textarea>
				</td>
			</tr>
			<tr>
				<td align="left" colspan="2">
					<table>
						<tr>
							<td nowrap>本訂單購買商品數量：</td>
							<td style="color:blue1; padding-left:2px; padding-right:200px">{$orders['Amount']}</td>
							<td nowrap>本訂單退訂商品數量：</td>
							<td style="color:blue1;">{$orders['Amount2']}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="2"><br><input type="button" class="btn" value="確定" onClick="parent.dialogClose();"></td>
			</tr>
		</table>
		</center>
EOD;


}
?>
<script language="javascript">
</script>