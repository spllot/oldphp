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
$date=$_REQUEST['date'];
$id=$_REQUEST['id'];
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Orders WHERE ID='$id'") or die(mysql_error());
$orders = mysql_fetch_array($result);

$result = mysql_query("SELECT * FROM Payment WHERE Memo='$id' AND Complete = 0") or die(mysql_error());
$payment = mysql_fetch_array($result);
include './include/db_close.php';
$payby=array(
	"",
	"信用卡(Visa)",
	"Web ATM",
	"ATM轉帳",
	"儲值金"
);
$url = urlencode("orders" . (($orders['Deliver'] == 1) ? "2":"") . ".php");
?>
<script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
<br>
<br>
<br>
<center>
<table>
	<tr>
		<td align="center">
		<table>
			<Tr>
				<Td align="left">
					訂單編號：<?=$id?><br>
					商品名稱：<?=$orders['pName']?><br>
					付款金額：<?=($payment['Amount'] + $payment['Fee'])?><br>
					付款方式：<?=$payby[$payment['payBy']]?><br>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
					<td><input type="button" value="完成付款" onClick="Paid(this);"></td>
					<td style="width:100px">&nbsp;</td>
					<td><input type="button" value="取消付款" onClick="Cancel();"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</center>
<br>
<br>
<br>
<script language="javascript">
function Paid(x){
	x.disabled = true;
	$.post(
		'buynow_paid.php',
		{
			id: '<?=$id?>',
			date: '<?=$date?>'
		},
		function(data)
		{
			//alert(data);
			window.parent.location.href ="member.php?menu=8&url=<?=$url?>";
		}
	);		
}

function Cancel(){
	window.parent.location.href ="member.php?menu=8&url=<?=$url?>";
}


</script>