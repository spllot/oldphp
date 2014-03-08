<?php
include './include/session.php';
$no = $_REQUEST['product'];
$status = (($_REQUEST['status'] != "") ? $_REQUEST['status'] : "0");
$balance=0;

include './include/db_open.php';





$result = mysql_query("SELECT * FROM Product WHERE No = '$no' AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
if($product=mysql_fetch_array($result)){
}
else{
	exit();
}

$result = mysql_query("SELECT COALESCE(SUM(Amount), 0) as Amount FROM logTransaction WHERE Owner='" . $_SESSION['member']['userID'] . "'");
if($rs=mysql_fetch_array($result)){
	$balance = $rs['Amount'];
}

$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}

if($product['coupon_YN']==0){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo—優惠訊息(優訊)發送設定</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<center>
<table style="width:660px" cellpadding="5" cellspacing="0">
	<tr style="height:30px">
		<td style="text-align:left; font-weight:bold; color:white; background:gray" align="left">優惠訊息(優訊)發送設定</td>
	</tr>
	<tr>
		<td align="left">
		<form name="iForm" method="post" target="iAction">
			<input type="hidden" name="item" value="<?=$no?>">
			<input type="hidden" name="coupon_YN" value="">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="text-align:right; vertical-align:top" nowrap>商品名稱：<br>(10字內)&nbsp;&nbsp;&nbsp;&nbsp;</td>	
					<td valign="top"><input type="text" name="coupon_name" style="width:600px" value="<?=(($product['coupon_name']=="") ? $product['Name'] : $product['coupon_name'])?>" maxlength=10></td>
				</tr>
				<tr>
					<td style="text-align:right; vertical-align:top" nowrap>優惠活動：<br>(45字內)&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td style="padding-bottom:10px">
						<textarea style="width:600px; height:100px" name="coupon_info" maxlength=40><?=$product['coupon_info']?></textarea><br>
						<Table align="left" width="600">
							<tr>
								<td valign="top" width=30 style="color:gray">(ex1).</td>
								<td valign="top" align="left" style="color:gray">出示此憑證，方可享有網路售價之優惠。</td>
							</tR>
							<tr>
								<td valign="top" width=30 style="color:gray">(ex2).</td>
								<td valign="top" align="left" style="color:gray">出示憑證加30元，可享精緻下午茶一份;本活動限量供應，售完為止。</td>
							</tR>
						</table>				
					</td>
				</tr>
				<tr>
					<td style="text-align:right; vertical-align:top" nowrap>限量：</td>
					<td>
						<input type="text" name="coupon_quota" value="<?=(($product['coupon_quota']>0) ? $product['coupon_quota'] : "")?>" style="width:100px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="checkbox" name="coupon_generate" value="1"<?=(($product['coupon_generate'] == 1) ? " CHECKED" : "")?>>我要產生憑證驗證碼

						<Table align="left" width="600">
							<tr>
								<td valign="top" width=40 style="color:gray">[註]：</td>
								<td valign="top" width=20 style="color:gray">(1).</td>
								<td valign="top" align="left" style="color:gray">當系統發送達到限量值時，或是儲值金已用盡，商品介紹頁面之 [優惠訊息發送] 功能將自動取消。</td>
							</tR>
							<tr>
								<td></td>
								<td valign="top" width=20 style="color:gray">(2).</td>
								<td valign="top" align="left" style="color:gray">勾選 [我要產生憑證驗證碼]，系統將依據填寫之限量數目，產生對應之憑證碼，經由e-mail寄給賣家查詢之用；賣家可以驗證消費者之憑證碼，以控制消費數量於限量值之中。</td>
							</tR>
						</table>				

					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td align="center">
			<input type="button" value="暫存設定" onClick="Draft();" style="width:120px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?
	if($balance > $_CONFIG['coupon']){
?>
			<input type="button" value="優訊設置確認" onClick="Save();" style="width:120px">
<?
	}
	else{
?>
			<input type="button" value="餘額不足" disabled style="width:120px">
<?
	}
?>
		</td>
	</tr>
</table>
</center>
<script language="javascript">
var iForm = document.iForm;
function getNum(x){
	var r = parseInt(x, 10);
	if(isNaN(r)){
		return 0;
	}
	return r;
}
function Draft(){
	var quota = getNum(iForm.coupon_quota.value);
	if(!iForm.coupon_info.value){
		alert("請輸入優惠活動!");
		iForm.coupon_info.focus();
	}
	else if(!iForm.coupon_quota.value){
		alert("請輸入限量!");
		iForm.coupon_quota.focus();
	}
	else if(quota > 9999 || quota < 1){
		alert("限量最少為1, 最多為9999!");
		iForm.coupon_quota.focus();
	}
	else{
		iForm.coupon_YN.value = "0";
		iForm.action="seller_item_coupon_save.php";
		iForm.submit();
		//$.fn.colorbox.close();
	}
}

function Save(){
	var quota = getNum(iForm.coupon_quota.value);
	if(!iForm.coupon_name.value){
		alert("請輸入商品名稱!");
		iForm.coupon_name.focus();
	}
	else if(!iForm.coupon_info.value){
		alert("請輸入優惠活動!");
		iForm.coupon_info.focus();
	}
	else if(!iForm.coupon_quota.value){
		alert("請輸入限量!");
		iForm.coupon_quota.focus();
	}
	else if(quota > 9999 || quota < 1){
		alert("限量最少為1, 最多為9999!");
		iForm.coupon_quota.focus();
	}
	else{
		iForm.coupon_YN.value = "1";
		iForm.action="seller_item_coupon_save.php";
		iForm.submit();
		//$.fn.colorbox.close();
	}
}

</script>



<?


}
else{

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo—優惠訊息發送狀態</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<center><form name="cForm">
<table style="width:660px" cellpadding="5" cellspacing="0">
	<tr style="height:30px">
		<td style="text-align:left; font-weight:bold; color:white; background:gray" align="left">優惠訊息發送狀態</td>
	</tr>
	<tr>
		<td align="left">
			<input type="radio" name="status" value="1"<?=(($status==1)?" CHECKED":"")?> onClick="setStatus();">已發送&nbsp;
			<input type="radio" name="status" value="0"<?=(($status==0)?" CHECKED":"")?> onClick="setStatus();">未發送
			<table cellpadding="1" cellspacing="1" bgcolor=gray width="630">
				<tr>
					<td style="background:white; width:40px; text-align:center">順序</td>
					<td style="background:white; width:100px; text-align:center">憑證碼</td>
					<td style="background:white; width:70px; text-align:center">狀態</td>
					<td style="background:white; width:70px; text-align:center">手機</td>
					<td style="background:white; text-align:center">E-mail</td>
					<td style="background:white; width:70px; text-align:center">發送日期</td>
				</tr>
			</table>
			<div id="coupon_list" style="width:650px; height:360px; overflow:auto"></div>
		</td>
	</tr>
	<tr>
		<td align="center">
			<input type="button" value="關閉" onClick="$.fn.colorbox.close();" style="width:120px">
		</td>
	</tr>
</table></form>
</center>
<script language="javascript">
	function setStatus(){
		if(cForm.status[0].checked){
			$("#coupon_list").load("seller_item_coupon_list.php?product=<?=$no?>&status=1");
		}
		else{
			$("#coupon_list").load("seller_item_coupon_list.php?product=<?=$no?>&status=0");
		}
	}
</script>
<script language="javascript">setStatus();</script>
<?
}
include './include/db_close.php';
?>
