<?php
include './include/session.php';
include './include/db_open.php';
$id = $_REQUEST['id'];
$sql = "SELECT *, (SELECT Name FROM Product WHERE No = logComment.transactionNo) AS pName, (SELECT Name FROM Member WHERE No = logComment.rateBy) AS rName FROM logComment WHERE No = '$id'";
$result = mysql_query($sql) or die(mysql_error());

$product = mysql_fetch_array($result);

$content = str_replace("\n", "<br>", $product['Content']);
switch($product['Quality']){
	case -1:
		$rate = "待改進(-1)";
		break;
	case 0:
		$rate = "普通(0)";
		break;
	case 1:
		$rate = "優良(+1)";
		break;
}
include './include/db_close.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo—檢視商品評論內容</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<center>
<table style="width:660px" cellpadding="5" cellspacing="0">
	<tr style="height:30px">
		<td style="text-align:left; font-weight:bold; color:white; background:gray" align="left">檢視商品評論內容</td>
	</tr>
	<tr>
		<td>
		<?
		if($product){
			echo <<<EOD
			<table>
				<tr>
					<td valign="top" bgcolor="#f7f7f7" style="width:60px; text-align:center">商品：</td>
					<td valign="top" align="left">{$product['pName']}</td>
				</tr>
				<tr style="display:none">
					<td valign="top" bgcolor="#f7f7f7" style="width:60px; text-align:center">評論：</td>
					<td valign="top" align="left">{$rate}</td>
				</tr>
				<tr>
					<td valign="top" bgcolor="#f7f7f7" style="width:60px; text-align:center">作者：</td>
					<td valign="top" align="left">{$product['rName']}</td>
				</tR>
				<tr>
					<td valign="top" bgcolor="#f7f7f7" style="width:60px; text-align:center">留言：</td>
					<td valign="top" align="left">{$content}</td>
				</tR>
				<tr>
					<td valign="top" bgcolor="#f7f7f7" style="width:60px; text-align:center">日期：</td>
					<td valign="top" align="left">{$product['dateRated']}</td>
				</tR>
			</table>
EOD;
		}
		?>
		</td>
	</tr>
	<tr>
		<td align="center"><hr>
			<input type="button" value="返回" onClick="history.back()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" value="關閉" onClick="parent.$.fn.colorbox.close();">
		</td>
	</tr>
</table>

</center>

<script language="javascript">
	function Save(){
		var rForm = document.rForm;
		if(!rForm.rate[0].checked && !rForm.rate[1].checked && !rForm.rate[2].checked){
			alert("請勾選評價!");
		}
		else if(!rForm.content.value){
			alert("請輸入留言內容!");
			rForm.content.focus();
		}
		else{
			rForm.submit();
		}
	}
</script>