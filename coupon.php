<?php
include './include/session.php';
$ip = ((getenv(HTTP_X_FORWARDED_FOR)) ?  getenv(HTTP_X_FORWARDED_FOR) :  getenv(REMOTE_ADDR));
$no = $_REQUEST['id'];

$coupon_quota=0;
$balance=0;

include './include/db_open.php';

$result = mysql_query("SELECT *, IFNULL((SELECT count(*) FROM Coupon WHERE Status = 1 AND Product=Product.No), 10000) AS coupon_used FROM Product WHERE No = '$no'") or die(mysql_error());
if($product=mysql_fetch_array($result)){
}
else{
	exit();
}

$result = mysql_query("SELECT COALESCE(SUM(Amount), 0) as Amount FROM logTransaction WHERE Owner=(SELECT userID FROM Member WHERE No='" . $product['Member'] . "')");
if($rs=mysql_fetch_array($result)){
	$balance = $rs['Amount'];
}

$coupon_quota = $product['coupon_quota'] - $product['coupon_used'];

$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}

if($product['coupon_YN']==1){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo—我要取得優惠資訊與憑證</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<center>
<table style="width:660px" cellpadding="5" cellspacing="0">
	<tr style="height:30px">
		<td style="text-align:left; font-weight:bold; color:white; background:gray" align="left">我要取得優惠資訊與憑證</td>
	</tr>
	<tr>
		<td align="left">
			<form name="cForm" method="post" target="iActionxx">
			<input type="hidden" name="item" value="<?=$no?>">
			<table cellpadding="0" cellspacing="0" border="0">
<? if($coupon_quota > 0 && $balance >= $_CONFIG['coupon']){?>
				<tr>
					<td style="text-align:right; vertical-align:top; line-height:30px" nowrap>我的電子郵件：</td>
					<td><input type="text" name="email1" value="" style="width:550px"></td>
				</tr>
				<tr>
					<td style="text-align:right; vertical-align:top; line-height:30px" nowrap>我的手機號碼：</td>
					<td><input type="text" name="phone" value="" style="width:550px"></td>
				</tr>
<?}?>
				<tr>
					<td></td>
					<td align="center" style="padding-top:15px; padding-bottom:15px">
<? if($coupon_quota > 0 && $balance >= $_CONFIG['coupon']){?>
						<input type="button" value="送出" onClick="Save();" style="width:120px">
<?}else if($balance < $_CONFIG['coupon']){?>
						<input type="button" value="商家儲值點數不足，按此關閉" onClick="$.fn.colorbox.close();" style="width:220px">
<?}else if($coupon_quota < 1){?>
						<input type="button" value="憑證已發送完畢，按此關閉" onClick="$.fn.colorbox.close();" style="width:220px">
<?}?>
					</td>
				</tr>
<? if($coupon_quota > 0 && $balance >= $_CONFIG['coupon']){?>
				<tr>
					<td></td>
					<td align="left">[說明]:需至少填一項，或者都填寫</td>
				</tr>
<?}?>
			</table>
			</form>
		</td>
	</tr>
</table><iframe name="iActionxx" width="100%" height="100" style="display:none"></iframe>
</center>
<script language="javascript">
var cForm = document.cForm;

function Save(){
//	alert('111');
	if(!cForm.email1.value && !cForm.phone.value){
		alert("需至少填一項，或者都填寫!");
	}
	else{
		cForm.action="coupon_send.php";
		cForm.submit();
	}
}

	function dialogClose(){
		$.fn.colorbox.close();
	}
</script>



<?

}

include './include/db_close.php';
?>
