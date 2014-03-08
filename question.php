<?php
include './include/session.php';
include './include/db_open.php';
$id = $_REQUEST['product'];
$sql = "SELECT *, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(Product.Latitude, Product.Longitude, '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM FROM Product WHERE Status = 2 AND No = '$id'";
$result = mysql_query($sql) or die(mysql_error());

$product = mysql_fetch_array($result);


include './include/db_close.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo—詢問商家問題</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<center>
<table style="width:660px" cellpadding="5" cellspacing="0">
	<tr style="height:30px">
		<td style="text-align:left; font-weight:bold; color:white; background:gray" align="left">詢問商家問題</td>
	</tr>
	<tr>
		<td>
		<?
		if($product){
			echo <<<EOD
			<form name="rForm" method="post" action="question_save.php" target="iAction">
				<input type="hidden" name="product" value="$id">
			<table>
				<tr>
					<td>商品：</td>
					<td align="left">{$product['Name']}</td>
				</tr>
				<tr style="display:none">
					<td>評價：</td>
					<td align="left">
						<input type="radio" name="rate" value="1">優良(+1)
						<input type="radio" name="rate" value="0">普通(0)
						<input type="radio" name="rate" value="-1">待改進(-1)
					</td>
				</tr>
				<tr>
					<td>問題：</td>
					<td align="left"><textarea name="content" style="width:400px; height:100px"></textarea></td>
				</tR>
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
		<? if($product && $_SESSION['member']){?>
			<input type="button" value="確定" onClick="Save();">
		<?}?>
		</td>
	</tr>
</table>

</center>

<script language="javascript">
	function Save(){
		var rForm = document.rForm;
		if(!rForm.content.value){
			alert("請輸入問題內容!");
			rForm.content.focus();
		}
		else{
			rForm.submit();
			$.fn.colorbox.close();
		}
	}
</script>