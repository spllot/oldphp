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



$no = $_REQUEST['id'];
include './include/db_open.php';


$balance=0;
$left = 0;
$use = 0;
$result = mysql_query("SELECT COALESCE(SUM(Amount), 0) as Amount FROM logTransaction WHERE Owner='" . $_SESSION['member']['userID'] . "'");
if($rs=mysql_fetch_array($result)){
	$balance = $rs['Amount'];
}

$sql = "SELECT *, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Product='$no' AND Member='" . $_SESSION['member']['No'] . "'), 0) AS Amounts, IFNULL((SELECT COUNT(*) FROM Orders WHERE Product='$no' AND Member='" . $_SESSION['member']['No'] . "'), 0) AS Buy, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.No=logCoupon.couponNo WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No AND Orders.Status <> 3), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, IFNULL((SELECT count(*) FROM Coupon WHERE Status = 1 AND Product=Product.No), 10000) AS coupon_used, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Address1 FROM Member WHERE No = Product.Member) AS Address1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, IF(Product.Activity = 0 AND (SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude) AS L1, IF(Product.Activity = 0 AND (SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude) AS L2, IFNULL((SELECT SUM(Quality) FROM logRating WHERE Owner = Product.Member), 0) as Rate, (SELECT Nick FROM Member WHERE Member.No = Product.Member) AS userName, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM FROM Product WHERE Status = 2 AND dateClose >= CURRENT_TIMESTAMP AND No = '$no' AND Cashflow=1 ORDER BY KM";
$result = mysql_query($sql) or die(mysql_error());
$data = mysql_fetch_array($result);


//檢查數量
if($data['Amount'] > 0){
	if($data['Quota'] <= $data['Solds']){
		JavaScript::Alert("已售完!");
		JavaScript::Execute("history.back()");
		exit;
	}

}

//檢查購買次數限制
if($data['Cashflow'] == 1 && $data['Deliver'] == 0 && ($data["Restrict"] == 3 || $data["Restrict"] == 4)){
	if($data['Buy'] > 0){
		JavaScript::Alert("每人只限購買一次!");
		JavaScript::Execute("history.back()");
		exit;
	}
	if(($data['Amounts'] + $amount) > $data['maxBuy']){
		JavaScript::Alert("每人只限購買{$data['maxBuy']}張!");
		JavaScript::Execute("history.back()");
		exit;
	}
}








if($balance >= $data['Price1']){
	$use = $data['Price1'];
}
else{
	$use = $balance;
}

$left = $balance - $use;
$total = ceil(($data['Price1'] - $use) * 100/100);

$deliver = (($data['Deliver'] == 1) ? "" : "none");
$receipt = (($data['Receipt'] == 3) ? "none" : "");



if($data['Broadcast'] == 1){
	$referral=<<<EOD
				<tr>
					<td colspan="2" align="left" style="text-align:left; padding-top:10px; padding-bottom:10px; ">
			<Table align="center" width="660" border=0>
				<tr>
					<td></td>
					<td valign="top" align="center">
						介紹商品購買者的手機號碼：<input type="text" name="referral" style="width:100px"><span style="color:gray">&nbsp;Ex. 0912345678</span>
					</td>
				</tR>
				<tr>
					<td valign="top" width=40 style="color:gray">[註]：</td>
					<td valign="top" align="left" style="color:gray">若您是由會員好友介紹而購買商品, 請輸入好友手機號碼, 您的好友會因您的輸入而享有利潤回饋, 詳情請參考[全民賺好康活動]。</td>
				</tR>
			</table></td>
				</tr>
EOD;
}


if($data['Mode'] == 1){
	if($data['Deliver'] == 0){
		$tab=1;
	}
	else{
		$tab=2;
	}
}
else{
	if($data['Deliver'] == 0){
		$tab=4;
	}
	else{
		$tab=5;
	}
}

$date = date('Y-m-d H:i:s');

$WEB_CONTENT = <<<EOD
<div  style="background:#FFFFFF">
			<center>
			<form name="iForm" method="post">
			<input type="hidden" id="product" name="product" value="{$data['No']}">
			<table border=0 cellpadding="0" cellspacing="0">
				<tr><td style="; width:120px">&nbsp;</td></tr>
				<tr style="; display:none">
					<td style="text-align:right; padding-top:2px; padding-bottom:2px" nowrap>訂單日期：</td>
					<td style="text-align:left"><input type="text" id="date" name="date" value="{$date}"></td>
				</tr>
				<tr>
					<td style="text-align:right; padding-top:2px; padding-bottom:2px" nowrap>手機：</td>
					<td style="text-align:left"><input type="text" name="phone" maxlength="10" value="{$_SESSION['member']['Phone']}" style="width:380px"><font color=red>*</font>
					</td>
				</tr>
				<tr>
					<td style="text-align:right; padding-top:2px; padding-bottom:2px" nowrap></td>
					<td style="text-align:left; color:gray">Ex. 0912345678</td>
				</tr>
				<tr>
					<td style="text-align:right; padding-top:2px; padding-bottom:2px" nowrap>付款方式：</td>
					<td style="text-align:left">
					<select name="payby" onChange="setTotal();" style="width:381px">
						<option value="1">信用卡(Visa)</option>
						<option value="3">ATM轉帳</option>
					</select><font color=red>*</font></td>
				</tr>
				<tr style="display:{$deliver}">
					<td style="text-align:right; padding-top:2px; padding-bottom:2px" nowrap>真實姓名：</td>
					<td style="text-align:left"><input type="text" name="name" maxlength="10" value="{$_SESSION['member']['Name']}" style="width:380px"><font color=red>*</font>
					</td>
				</tr>
				<tr style="display:{$deliver}">
					<td style="text-align:right; padding-top:2px; padding-bottom:2px" nowrap>郵遞區號：</td>
					<td style="text-align:left"><input type="text" size="3" name="rzip" onKeyUp="setArea(iForm.county, iForm.area, this.value);" maxlength="5" onChange="setAddress();" value="{$_SESSION['member']['Zip0']}">
						<select name="county" onChange="chgCounty(iForm.county, iForm.area);chgArea(iForm.area, iForm.rzip);setAddress();"></select>
						<select name="area" onChange="chgArea(iForm.area, iForm.rzip);setAddress();"></select><font color=red>*</font>
						&nbsp;&nbsp;
						<font style="color:gray">(建議填寫5碼郵遞區號)</font>
					</td>
				</tr>
				<tr style="display:{$deliver}">
					<td style="text-align:right; padding-top:2px; padding-bottom:2px" nowrap>收貨地址：</td>
					<td style="text-align:left"><input type="text" name="address" maxlength="10" value="{$_SESSION['member']['Address0']}" style="width:380px"><font color=red>*</font>
					</td>
				</tr>
				<tr style="display:{$receipt}">
					<td style="text-align:right; padding-top:2px; padding-bottom:2px" nowrap valign="top">發票/收據：</td>
					<td style="text-align:left">
						<input type="radio" name="receipt" value=0>不索取
						<input type="radio" name="receipt" value=1>取得紙本發票或收據&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:blue; cursor:pointer" onClick="$('.receipt').toggle();">需打統編，請按此</span>
					</td>
				</tr>
				<tr class="receipt" style="display:none;">
					<td style="text-align:right; padding-top:2px; padding-bottom:2px" nowrap>公司抬頭：</td>
					<td style="text-align:left"><input type="text" name="title" style="width:380px"></td>
				</tr>
				<tr class="receipt" style="display:none;">
					<td style="text-align:right; padding-top:2px; padding-bottom:2px" nowrap>統一編號：</td>
					<td style="text-align:left"><input type="text" name="unino" style="width:380px"></td>
				</tr>
				<tr>
					<td style="text-align:right; padding-top:10px; padding-bottom:10px; background:#ffffcc" nowrap>現有儲值金：</td>
					<td style="padding-top:10px; padding-bottom:10px; background:#ffffcc">
					<table align="center">
						<tr>
							<td style="width:60px"><div id="balance" style="; text-align:left; color:blue">$balance</div></td>
							<td style="padding-left:20px">儲值金抵付：</td>
							<td style="width:60px" align="left"><input type="text" name="use" id="use" style="width:50px; color:blue" value="{$use}" onKeyUp="setTotal();"></td>
							<td style="padding-left:20px">剩餘儲值金：</td>
							<td style="width:60px"><div id="left" style="; text-align:left; color:blue">$left</div></td>
						</tr>
					</table>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td colspan="2" align="center">
					<table cellpadding="0" cellspacing="0" align="center" border=0 style="width:600px">
						<tr>
							<td style="width:120px; border-bottom:solid 2px gray; ">商品名稱</td>
							<td style="width:120px; border-bottom:solid 2px gray; width:50px ">&nbsp;</td>
							<td style="width:120px; border-bottom:solid 2px gray; width:100px ">單價</td>
							<td style="width:120px; border-bottom:solid 2px gray; width:100px ">數量</td>
							<td style="width:120px; border-bottom:solid 2px gray; width:100px ">總計</td>
							<td style="width:120px; border-bottom:solid 2px gray; width:100px ">需付</td>
						</tr>
						<tr style="height:40px">
							<td style="; border-bottom:solid 2px gray">{$data['Name']}</td>
							<td style="; border-bottom:solid 2px gray">&nbsp;</td>
							<td style="; border-bottom:solid 2px gray"><div id="price">{$data['Price1']}</div></td>
							<td style="; border-bottom:solid 2px gray"><input type="text" name="amount" id="amount" style="width:50px" value="1" onKeyUp="setTotal();"></td>
							<td style="; border-bottom:solid 2px gray"><div id="subtotal">{$data['Price1']}</div></td>
							<td style="; border-bottom:solid 2px gray"><div id="total">{$total}</div></td>
						</tr>
					</table>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>{$referral}
				<tr>
					<td colspan="2" align="left" style="text-align:center; padding-top:10px; padding-bottom:10px">
						<input type="checkbox" name="agree" value="1">我已閱讀並願意遵守<a href="javascript:parent.Dialog('policy.php')">電子商務服務條款</a>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center" style="text-align:cente; padding-top:10px; padding-bottom:10px"><input type="button" value="購買" onClick="Save();" class="btn" style="width:150px"></td>
				</tr>
				<tr>
					<td colspan="2" align="left" style="text-align:left; padding-top:10px; padding-bottom:10px; "></td>
				</tr>
			</table>
			</form>	
			</center>
			<Table align="center" width="660" border=0>
				<tr>
					<td valign="top" width=60 style="color:gray">[說明]：</td>
					<td valign="top" width=20 style="color:gray">(1)</td>
					<td valign="top" align="left" style="color:gray">若您選擇使用ATM 轉帳，您將會經由E-mail 收到一組專屬的虛擬帳號，而您必須在期限內使用此帳號進行轉帳，轉帳期限為訂購隔天晚上12點前，若超過時限轉帳就會失效。</td>
				</tR>
				<tr>
					<td></td>
					<td valign="top" width=20 style="color:gray">(2)</td>
					<td valign="top" align="left" style="color:gray">到店商品之發票或收據, 在消費者到店消費商品時, 將由店家直接開立之; 宅配商品之發票或收據, 最遲將於商品鑑賞期之後一周內, 由商品賣家寄送給消費者。</td>
				</tR>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
			</table>
	
</div>		
<script language="javascript">
genCounty(document.iForm.county);
chgCounty(document.iForm.county, iForm.area);
chgArea(document.iForm.area, iForm.rzip);
document.iForm.rzip.value = "{$_SESSION['member']['Zip0']}";
setArea(document.iForm.county, iForm.area, iForm.rzip.value);
</script>	
EOD;



include 'search.php';
include 'template0.php';

?>

<script language="javascript">parent.setTab(<?=$tab?>);</script>
<script language="javascript">
function Payment(oid){
//	window.open("buynow_confirm.php?id=" + oid + "&date=" + iForm.date.value);
	parent.Dialog1("buynow_confirm.php?id=" + oid + "&date=" + iForm.date.value, 320);
}
function goOrders(deliver){
	window.parent.location.href='member.php?menu=8&url=' + encodeURI('orders' + ((deliver==1) ? '2' : '') + '.php') ;
}
function Save(){
	var total = parseInt($("#total").html(), 10);
	if(!iForm.phone.value){
		alert("請輸入手機號碼!");
	}
	else if('<?=$receipt?>' != 'none' && !iForm.receipt[0].checked && !iForm.receipt[1].checked){
		alert("請選擇是否索取發票!");
	}
	else if('<?=$deliver?>' != 'none' && !iForm.name.value){
		alert("請輸入真實姓名!");
	}
	else if('<?=$deliver?>' != 'none' && !iForm.rzip.value){
		alert("請輸入郵遞區號!");
		iForm.rzip.focus();
	}
	else if('<?=$deliver?>' != 'none' && !iForm.address.value){
		alert("請輸入收貨地址!");
	}
	else if(parseInt(iForm.amount.value,10) < 1){
		alert("請輸入購買數量!");
	}
	else if(total > 0 && total < 100 && iForm.payby.value=="1"){
		alert("使用信用卡付款不可少於100元，請選擇其它付款方式!");
	}
	else if(!iForm.agree.checked){
		alert("請仔細閱讀電子商務服務條款，並勾選願意遵守!");
	}
	else{
		iForm.target="iAction";
		iForm.action="buynow_save.php";
		iForm.submit();
	}
}

function setTotal(){
	var maxbuy = 0;
	var ratio = 1;

	switch(iForm.payby.value){
		case '1':
			ratio = 1.03;
			break;
		case '2':
			ratio = 1.02;
			break;
		case '3':
			ratio = 1.005;
			break;
	}
	ratio = 1;

	if($("#restrict").val() == "3"){
		maxbuy= parseInt($("#maxbuy").val(), 10);
	}
	var balance = parseInt($("#balance").html(), 10);
	var use = parseInt($("#use").val(), 10);
	if(use > balance){
		use = balance;
	}

	var amount = 1;
	if(maxbuy > 0 && amount > maxbuy){
		alert("每人限購 " + maxbuy + " 張!");
		$("#amount").val("1");
	}
	else{
		amount = parseInt($("#amount").val(), 10);
	}
	
	var price = parseInt($("#price").html(), 10);


	if(use > amount * price){
		use = amount * price;
	}
	$("#use").val(use);


	var total = Math.ceil((price * amount - use)*ratio);

	$('#left').html(balance-use);
	$('#subtotal').html(price * amount);
	$('#total').html(total);
}
</script>

<script language="javascript">
/*
	$('#date').datetimepicker({
		dateFormat: "yy-mm-dd",
		timeFormat: "HH:mm",
		closeText: '確定'
	});
*/
</script>