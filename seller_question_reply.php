<?php
include './include/session.php';
include './include/db_open.php';
$id = $_REQUEST['no'];
$sql = "SELECT logComment.*, Product.Deliver, Product.Name, Product.Mode, Product.Deliver, Member.Nick, Member.userID FROM logComment INNER JOIN Product ON Product.No = logComment.transactionNo INNER JOIN Member ON Member.No=logComment.rateBy  WHERE Owner='" . $_SESSION['member']['No'] . "' AND Question = 1 AND logComment.No='$id'";
//echo $sql;
$result = mysql_query($sql) or die(mysql_error());

$product = mysql_fetch_array($result);


include './include/db_close.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo—回覆商品問題</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<center>
<table style="width:660px" cellpadding="5" cellspacing="0">
	<tr style="height:30px">
		<td style="text-align:left; font-weight:bold; color:white; background:gray" align="left">回覆商品問題</td>
	</tr>
	<tr>
		<td>
		<?
		if($product){
			$date = (($product['dateReplied']=="0000-00-00 00:00:00")?"尚未":$product['dateReplied']);
			echo <<<EOD
			<form name="rForm" method="post" action="seller_question_save.php" target="iAction">
				<input type="hidden" name="no" value="$id">
			<table>
				<tr>
					<td align="right">商品：</td>
					<td align="left">{$product['Name']}</td>
				</tr>
				<tr>
					<td align="right">日期：</td>
					<td align="left">{$product['dateRated']}</td>
				</tr>
				<tr>
					<td align="right">暱稱：</td>
					<td align="left">{$product['Nick']}</td>
				</tr>
				<tr>
					<td align="right">電子郵件：</td>
					<td align="left">{$product['userID']}</td>
				</tr>
				<tr>
					<td align="right">問題：</td>
					<td align="left"><textarea name="content" style="width:400px; height:100px">{$product['Content']}</textarea></td>
				</tR>
				<tr>
					<td align="right">回覆：</td>
					<td align="left"><textarea name="reply" style="width:400px; height:100px">{$product['Reply']}</textarea></td>
				</tR>
				<tr>
					<td align="right">回覆日期：</td>
					<td align="left">{$date}</td>
				</tr>
			</table>
			</form>
EOD;
		}
		?>
		</td>
	</tr>
	<tr>
		<td align="center">
			<input type="button" value="取消" onClick="$.fn.colorbox.close();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<? if($product['dateReplied']=="0000-00-00 00:00:00"){?>
			<input type="button" value="回覆" onClick="Save();">
		<?}?>
		</td>
	</tr>
</table>

</center>

<script language="javascript">
	function Save(){
		var rForm = document.rForm;
		if(!rForm.reply.value){
			alert("請輸入回覆內容!");
			rForm.reply.focus();
		}
		else{
			rForm.submit();
			$.fn.colorbox.close();
		}
	}
</script>