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
$result = mysql_query("SELECT * FROM Member WHERE userID = '" . $_SESSION['member']['userID'] . "'");
$data = mysql_fetch_array($result);

$result = mysql_query("SELECT * FROM Catalog WHERE USEFOR='TYPE_AREA' ORDER BY Sort");
while($rs = mysql_fetch_array($result)){
	$area_list .= "<option value='{$rs['No']}'" . (($rs['No'] == $data['subscribeArea']) ? " SELECTED":"") . ">{$rs['Name']}</option>";
}
include './include/db_close.php';
$subscribe = (($data['Subscribe'] == 1) ? " CHECKED":"");
$WEB_CONTENT = <<<EOD
<center>
<table border=0>
	<tr>
		<td style="border-bottom:solid 1px gray; line-height:40px; text-align:left; font-weight:bold">個人資料</td>
	</tr>
	<tr>
		<td>
<form name="iForm" method="post" target="iAction" action="member_data_save.php">
<input type="hidden" name="latitude" value="({$data['Latitude0']}, {$data['Longitude0']})">
<table>
	<tr>
		<td style="text-align:right" nowrap>E-mail帳號：</td>
		<td style="text-align:left">{$_SESSION['member']['userID']}</td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>手機(全數字)：</td>
		<td style="text-align:left; color:gray;"><input type="text" style="width:300px" name="phone" maxlength="10" value="{$data['Phone']}"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>真實姓名：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="name" value="{$data['Name']}"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>暱稱設定：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="nick" value="{$data['Nick']}"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>郵遞區號：</td>
		<td style="text-align:left"><input type="text" size="3" name="rzip" onKeyUp="setArea(iForm.county, iForm.area, this.value);" maxlength="5" onChange="setAddress();" value="{$data['Zip0']}">
			<select name="county" onChange="chgCounty(iForm.county, iForm.area);chgArea(iForm.area, iForm.rzip);setAddress();"></select>
			<select name="area" onChange="chgArea(iForm.area, iForm.rzip);setAddress();"></select><font color=red>*</font>
			&nbsp;&nbsp;&nbsp;&nbsp;
			(建議填寫5碼郵遞區號)</font>
		</td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>收件地址：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="address" onChange="getLatitude(this);" value="{$data['Address0']}"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>會員等級：</td>
		<td style="text-align:left">{$_SESSION['member']['Level']}</font></td>
	</tr>
	<tr style="display:none">
		<td style="text-align:right"></td>
		<td style="text-align:left"><input type="checkbox" name="subscribe" value="1"{$subscribe}>
			願意收到"即購網"相關好康資訊，所在地：<select name="subscribe_area"><option value="">--請選擇--</option>{$area_list}</select><font color=red>*</font>
		</td>
	</tr>
	<tr>
		<td></td>
		<td style="text-align:left; padding-top:30px"><a href="javascript:showPass();">修改密碼 (任意六碼)</a></td>
	</tr>
	<tr class="pass" style="display:none">
		<td style="text-align:right" nowrap>舊密碼：</td>
		<td style="text-align:left; color:gray;"><input type="password" style="width:300px" name="pass1" maxlength="20" value=""><font color=red>*</font></td>
	</tr>
	<tr class="pass" style="display:none">
		<td style="text-align:right" nowrap>新密碼：</td>
		<td style="text-align:left; color:gray;"><input type="password" style="width:300px" name="pass2" maxlength="20" value=""><font color=red>*</font></td>
	</tr>
	<tr class="pass" style="display:none">
		<td style="text-align:right" nowrap>確認密碼：</td>
		<td style="text-align:left; color:gray;"><input type="password" style="width:300px" name="pass3" maxlength="20" value=""><font color=red>*</font></td>
	</tr>
	<tr>
		<td colspan="2" align="center" style="padding-top:5px"><hr>
			<input type="button" value="儲存修改" onClick="Save();" class="btn" style="width:150px">
		</td>
	</tr>
</table>
</form>
		</td>
	</tr>
</table>
</center><div id="map" style="display:none"></div>
<script language="javascript">
genCounty(document.iForm.county);
chgCounty(document.iForm.county, iForm.area);
chgArea(document.iForm.area, iForm.rzip);
document.iForm.rzip.value = "{$data['Zip0']}";
setArea(document.iForm.county, iForm.area, iForm.rzip.value);
</script>
EOD;
include 'template2.php';
?>

<script language="javascript">
function showPass(){
	$(".pass").toggle();
}

function check_phone(phone){
	var error = false
	if( phone.length != 10 )
		return false;
	if(phone.substring(0, 2) != "09") return false;
	for( idx = 0 ; idx <phone.length ; idx++ ) {
		if( !( phone.charAt(idx)>= '0' && phone.charAt(idx) <= '9' ) ) {
			error = true;
		break;
		}
	}
	if( error == true )
		return false;
	else
		return true;
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
						iForm.latitude.value = loc.lat() + "," + loc.lng();
                    }
                    else
                    {
						alert('Google Maps 找不到該地址，將無法計算距離！');
                    }
				});
		   }
		}
		else{
			iForm.latitude.value = "";
		}
	}
function Save(){
	if(!iForm.phone.value){
		alert("請輸入手機!");
		iForm.phone.focus();
	}
	else if(!check_phone(iForm.phone.value)){
		alert("手機格式錯誤!");
		iForm.phone.focus();
	}
	else if(!iForm.name.value){
		alert("請輸入真實姓名!");
		iForm.name.focus();
	}
	else if(!iForm.nick.value){
		alert("請輸入暱稱設定!");
		iForm.nick.focus();
	}
	else if(!iForm.rzip.value){
		alert("請輸入郵遞區號!");
		iForm.rzip.focus();
	}
	else if(!iForm.address.value){
		alert("請輸入收貨地址!");
		iForm.address.focus();
	}
	else if(iForm.pass1.value && !iForm.pass2.value){
		alert("請輸入新密碼!");
		$(".pass").show();
		iForm.pass2.focus();
	}
	else if(iForm.pass1.value && iForm.pass2.value!=iForm.pass3.value){
		alert("新密碼不相符，請確認!");
		$(".pass").show();
		iForm.pass3.focus();
	}
	else{
//		$.blockUI({ message: '資料處理中，請稍候…' });
		if(confirm("對於會員之手機、收件地址更改, 系統將發出電子郵件確認變更, \n待會員回覆確認信之後, 新的手機、收件地址始得生效, \n否則仍以舊有手機、收件地址作為依據")){
			setTimeout("iForm.submit();", 2000);
		}
	}
}
</script>
<script language="javascript">
	google.load("maps", "2",{"other_params":"sensor=true"});
</script>