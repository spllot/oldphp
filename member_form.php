<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}
$result = mysql_query("SELECT * FROM Member WHERE userID = '" . $_SESSION['member']['userID'] . "'");
$member = mysql_fetch_array($result);





include './include/db_close.php';
include 'seller_data_tab.php';

if($_CONFIG['cashflow'] == "Y"){
$WEB_CONTENT = <<<EOD

<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center">
		<br><Br><center>


<table border=0 width=500>
	<tr>
		<td style=" line-height:25px; text-align:left; font-weight:bold">申請成為金流商品賣家，請填入以下資訊</td>
	</tr>
	<tr>
		<td style="; padding-bottom:10px; border-bottom:solid 1px gray;color:gray; text-align:left">[說明]: 本站提供金流商品之刷卡/虛擬帳號轉帳收費服務，有利於會員購買商品得到便利之付款機制。商家申請成為金流商品賣家，可以建置金流交易商品，本站收取之服務費將開立發票給商家，所代收之金額也將匯款給商家，故您必須填寫商家發票與匯款資訊，以利本站完成金流作業。</td>
	</tr>
	<tr>
		<td style=";border-bottom:solid 1px gray">
			<form name="form1" method="post" target="iAction" action="member_form_save.php">
			<input type="hidden" name="latitude" value="">
			<table>
				<tr>
					<td style="text-align:right" nowrap>發票買受人：</td>
					<td style="text-align:left; color:gray;"><input type="text" style="width:120px" name="rname" maxlength="10" value=""><font color=red>*</font></td>
				</tr>
				<tr>
					<td style="text-align:right" nowrap>郵遞區號：</td>
					<td style="text-align:left"><input type="text" size="3" name="rzip" onKeyUp="setArea(form1.county, form1.area, this.value);" maxlength="3" onChange="setAddress();">
						<select name="county" onChange="chgCounty(form1.county, form1.area);chgArea(form1.area, form1.rzip);setAddress();"></select>
						<select name="area" onChange="chgArea(form1.area, form1.rzip);setAddress();"></select><font color=red>*</font>
					</td>
				</tr>
				<tr>
					<td style="text-align:right" nowrap>發票地址：</td>
					<td style="text-align:left"><input type="text" style="width:300px" name="raddress" onChange="getLatitude(this);" value=""><font color=red>*</font></td>
				</tr>
				<tr>
					<td style="text-align:right" nowrap>統一編號：</td>
					<td style="text-align:left"><input type="text" style="width:120px" name="unino" value=""></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td style="text-align:right" nowrap>匯款帳號：</td>
					<td style="text-align:left"></td>
				</tr>
				<tr>
					<td style="text-align:right" nowrap>銀行名稱：</td>
					<td style="text-align:left"><input type="text" style="width:120px" name="bank" value=""><font color=red>*</font></td>
				</tr>
				<tr>
					<td style="text-align:right" nowrap>分支行名稱：</td>
					<td style="text-align:left"><input type="text" style="width:120px" name="branch" value=""></td>
				</tr>
				<tr>
					<td style="text-align:right" nowrap>用戶帳號：</td>
					<td style="text-align:left"><input type="text" style="width:200px" name="account" value=""><font color=red>*</font></td>
				</tr>
				<tr>
					<td colspan="2" align="left" style="text-align:center; padding-top:10px;">
						<input type="checkbox" name="agree" value="1">我已閱讀並願意遵守<a href="javascript:parent.Dialog('policy2.php')">電子商務服務條款</a>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td align="center" style="padding-top:10px">
			<input type="button" value="申請成為金流商品賣家" onClick="Save();" class="btn">
		</td>
	</tr>
</table>
</center>
	

</td></tr></table>
	<br><Br>
<Br>


<div id="map" style="display:none"></div>
<script language="javascript">
genCounty(document.form1.county);
chgCounty(document.form1.county, form1.area);
chgArea(document.form1.area, form1.rzip);
document.form1.rzip.value = "{$rs['rZip']}";
setArea(document.form1.county, form1.area, form1.rzip.value);
</script>
<script language="javascript">
function setAddress(){
//	if(!form1.raddress.value){
		form1.raddress.value = form1.county.options[form1.county.options.selectedIndex].text + form1.area.options[form1.area.options.selectedIndex].text;
//	}
}
function getLatitude(x){
	if(x.value){
		if (GBrowserIsCompatible()) {
			var map = new google.maps.Map2(document.getElementById("map"));
			var geocoder = new google.maps.Geocoder();
			map.addControl(new GSmallMapControl());
			geocoder.geocode({ address: x.value }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var loc = results[0].geometry.location;
					form1.latitude.value = loc.lat() + "," + loc.lng();
                }
                else
                {
					alert('Google Maps 找不到該地址，將無法計算距離！');
                }
			});
	   }
	}
	else{
		form1.latitude.value = "";
	}
}

function Save(){
	if(!form1.rname.value){
		alert("請輸入發票買受人!");
		form1.rname.focus();
	}
	else if(!form1.rzip.value){
		alert("請輸入郵遞區號!");
		form1.rzip.focus();
	}
	else if(!form1.raddress.value){
		alert("請輸入發票地址!");
		form1.raddress.focus();
	}
	else if(!form1.bank.value){
		alert("請輸入銀行名稱!");
		form1.bank.focus();
	}
	else if(!form1.account.value){
		alert("請輸入用戶帳號!");
		form1.account.focus();
	}
	else if(!form1.agree.checked){
		alert("請閱讀並同意遵守電子商務服務條款!");
	}
	else{
		form1.submit();
	}
}
</script>
EOD;

}
else{
	$WEB_CONTENT = <<<EOD
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center">
			<Br><br><center>抱歉! 目前網站尚未開放金流申請功能。</center></td>
	</tr>
</table>

EOD;

}
include 'template2.php';
?>


<script language="javascript">
	google.load("maps", "2",{"other_params":"sensor=true"});
</script>