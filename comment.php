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
<title>InTimeGo—發表商品評論</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<center>
<table style="width:670px" cellpadding="5" cellspacing="0">
	<tr style="height:30px">
		<td style="text-align:left; font-weight:bold; color:white; background:gray" align="left">發表商品評論</td>
	</tr>
	<tr>
		<td>
		<?
		if($product){
			echo <<<EOD
			<form name="rForm" method="post" action="comment_save.php" target="iAction">
				<input type="hidden" name="product" value="$id">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="width:70px; text-align:right">商品：</td>
					<td align="left">{$product['Name']}</td>
				</tr>
				<tr>
					<td style="text-align:right">評分：<br>(0-5)　</td>
					<td align="left">
						<input type="text" name="rating" style="width:600px" onKeyUp="chkRate();">
					</td>
				</tr>
				<tr>
					<td style="text-align:right">留言：</td>
					<td align="left"><textarea name="content" style="width:600px; height:100px"></textarea></td>
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
			<input type="button" value="取消" onClick="$.fn.colorbox.close();" style="width:90px; height:30px">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<? if($product && $_SESSION['member']){?>
			<input type="button" value="確定" onClick="Save();" style="width:90px; height:30px">
		<?}?>
		</td>
	</tr>
</table>

</center>

<script language="javascript">
	var rForm = document.rForm;
	function chkRate(){
		if(isNaN(parseFloat(rForm.rating.value))){
			rForm.rating.value="";
		}
	}
	function Save(){
		var rating = parseFloat(rForm.rating.value);
		if(!rForm.rating.value){
			alert("請輸入評分!");
			rForm.rating.focus();
		}
		else if(rating <0 || rating > 5){
			alert("評分請輸入0-5");
			rForm.rating.value="";
		}
		else if(!rForm.content.value){
			alert("請輸入留言內容!");
			rForm.content.focus();
		}
		else{
			rForm.submit();
			$.fn.colorbox.close();
		}
	}
</script>