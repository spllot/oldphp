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
$sql = "SELECT *, IFNULL((SELECT SUM(Amount) FROM Items WHERE orderID=Orders.ID AND Refund = 1), 0) AS Refund, IFNULL((SELECT count(*) FROM logCertify WHERE orderID=Orders.ID AND Refund = 0 AND dateUse <> '0000-00-00 00:00:00' AND dateVertify <> '0000-00-00 00:00:00'), 0) AS Used, (SELECT payBy FROM Payment WHERE Memo=Orders.ID AND payBy<>4) AS payBy FROM Orders WHERE ID='$id' AND Member='" . $_SESSION['member']['No'] . "'";
//echo $sql;
$result = mysql_query($sql) or die(mysql_error());
if($orders = mysql_fetch_array($result)){
	$left = $orders['Amount'] - $orders['Refund'] - $orders['Used'];
	$display = (($orders['payBy'] == 3) ? "" : "none");
	echo <<<EOD
		<center>
		<form name="hForm" method="post" action="orders_refund_save.php" target="iAction">
		<input type="hidden" name="id" value="$id">
		<table width="700">
			<tr>
				<td nowrap style="text-align:left; background:#f3f3f3;" colspan="2">訂單交易查詢-{$id}退訂申請</td>
			</tr>
			<tr>
				<td valign="top" align="right" nowrap>問題描述：</td>
				<td nowrap style="">
					<textarea name="content" style="width:600px; height:100px"></textarea>
				</td>
			</tr>
			<tr>
				<td align="left" colspan="2">
					<table>
						<tr>
							<td nowrap>本訂單購買商品數量：</td>
							<td style="; padding-left:2px; padding-right:50px">{$orders['Amount']}</td>
							<td nowrap>本訂單可退商品數量：</td>
							<td style="; padding-left:2px; padding-right:50px">{$left}</td>
							<td nowrap>本訂單退訂商品數量：</td>
							<td><input type="text" style="width:50px" value="" name="amount"></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr style="display:{$display}">
				<td align="left" colspan="2"><br>
					<table>
						<tr>
							<td valign="top"><input type="checkbox" name="transfer" value="1"></td>
							<td>商品辦理退貨退款, ATM轉帳之款項，自動轉為儲值金 (建議此方式, 將不會額外扣取匯款手續費)；如果你是採用信用卡或儲值金付款，則無需理會此勾選。
								<table>
									<tr>
										<td style="color:gray">[註]:</td>
										<td style="color:gray">以信用卡付費退款，則不會扣取金流手續費。</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="2"><br><input type="button" class="btn" value="送出" onClick="Save();" style="width:100px"></td>
			</tr>
		</table>
		</form>
		</center>
EOD;


}
?>
<script language="javascript">
function Save(){
	if(!hForm.content.value){
		alert("請輸入問題描述!");
	}
	else if(!hForm.amount.value){
		alert("請輸入退訂商品數量!");
	}
	else if(parseInt(hForm.amount.value, 10) < 1){
		alert("退訂商品數量不可小於1");
	}
	else if(parseInt(hForm.amount.value, 10) > <?=$left?>){
		alert("退訂商品數量不可大於可退商品數量(<?=$left?>)");
	}
	else{
		hForm.submit();
	}
}


</script>