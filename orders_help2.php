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
$result = mysql_query("SELECT * FROM Orders WHERE ID='$id' AND Seller='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
if($orders = mysql_fetch_array($result)){
	echo <<<EOD
		<center>
		<form name="hForm" method="post" action="orders_help2_save.php" target="iAction">
		<input type="hidden" name="id" value="$id">
		<table width="700">
			<tr>
				<td nowrap style="text-align:left; background:#f3f3f3;" colspan="2">訂單交易查詢-問題選項</td>
			</tr>
			<tr>
				<td valign="top" align="right">問題選項：</td>
				<td nowrap style="">
					<table style="width:100%">
						<tr>
							<td style="width:33%; text-align:left"><input type="radio" name="type" value="1">出貨進度</td>
							<td style="width:33%; text-align:left"><input type="radio" name="type" value="2">退貨問題</td>
							<td style="width:33%; text-align:left"><input type="radio" name="type" value="3">退款問題</td>
						</tr>
						<tr>
							<td style="width:33%; text-align:left"><input type="radio" name="type" value="4">貨品問題</td>
							<td style="width:33%; text-align:left"><input type="radio" name="type" value="5">付款問題</td>
							<td style="width:33%; text-align:left"><input type="radio" name="type" value="6">保固問題</td>
						</tr>
						<tr>
							<td style="width:33%; text-align:left"><input type="radio" name="type" value="7">發票問題</td>
							<td style="width:33%; text-align:left"><input type="radio" name="type" value="8">匯款問題</td>
							<td style="width:33%; text-align:left"><input type="radio" name="type" value="9">其它問題</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right">問題描述：</td>
				<td nowrap style="">
					<textarea name="content" style="width:600px; height:100px"></textarea>
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