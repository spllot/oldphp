<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
else if($_SESSION['member']['Seller'] != 2){
	JavaScript::setCharset("UTF-8");
	JavaScript::Redirect("./member_form.php");
	exit;
}
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}



$result = mysql_query("SELECT * FROM Member WHERE userID = '" . $_SESSION['member']['userID'] . "'");
$member = mysql_fetch_array($result);

$rating = 0;
$result = mysql_query("SELECT IFNULL(SUM(Quality), 0) AS COUNTS FROM logRating WHERE Owner='" . $_SESSION['member']['No'] . "'");
if($rs=mysql_fetch_array($result)){
	$rating = $rs['COUNTS'];
}

$area_list = "";
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_AREA' ORDER BY Sort");
while($rs=mysql_fetch_array($result)){
	$area_list .= "<option value='" . $rs['No'] . "'" . (($member['Area1'] == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}
include './include/db_close.php';

if($member['dateApprove'] != "0000-00-00 00:00:00"){


include 'seller_data_tab.php';






$lat = (($member['Latitude1'] > 0) ? $member['Latitude1'] : "");
$long = (($member['Longitude1'] > 0) ? $member['Longitude1'] : "");



$WEB_CONTENT = <<<EOD
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center">
		<br><Br><center>


<table border=0>
	<tr>
		<td align="center">
<form name="form1" method="post" target="iAction" action="seller_data_save.php">
<input type="hidden" name="latitude" value="({$member['rLatitude']}, {$member['rLongitude']})">
<table>
	<tr>
		<td style="text-align:right" nowrap>發票買受人：</td>
		<td style="text-align:left; color:gray;"><input type="text" style="width:300px" name="rname" value="{$member['rName']}"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>郵遞區號：</td>
		<td style="text-align:left"><input type="text" size="3" name="rzip" onKeyUp="setArea(form1.county, form1.area, this.value);" maxlength="5" onChange="setAddress();" value="{$member['rZip']}">
			<select name="county" onChange="chgCounty(form1.county, form1.area);chgArea(form1.area, form1.rzip);setAddress();"></select>
			<select name="area" onChange="chgArea(form1.area, form1.rzip);setAddress();"></select><font color=red>*</font>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<font color=gray>(建議填寫5碼郵遞區號)</font>
		</td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>發票地址：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="raddress" onChange="getLatitude(this);"  value="{$member['rAddress']}"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>統一編號：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="unino" value="{$member['uniNo']}"></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td style="text-align:right" nowrap>匯款帳號：</td>
		<td style="text-align:left"></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>銀行代號：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="bno" value="{$member['bNo']}"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>銀行名稱：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="bank" value="{$member['Bank']}"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>分支行名稱：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="branch" value="{$member['Branch']}"></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>用戶帳號：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="account" value="{$member['Account']}"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>金流交易評價：</td>
		<td style="text-align:left; color:blue">{$rating}&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:parent.Dialog('seller_trust.php?id={$_SESSION['member']['No']}')">檢閱賣家金流交易評價積分</a></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><hr>
			<table align="center">
				<tr>
					<td><img src="./images/checked.gif"></td>
					<td>我已閱讀並願意遵守<a href="./upload/{$_CONFIG['seller_policy']}" target="_blank">賣家電子商務服務條款(電子檔)</a></td>
				</tr>
			</table><br>
			<input type="button" value="資料變更" onClick="Save();" class="btn" style="width:150px">
		</td>
	</tr>
</table>
</form>
		</td>
	</tr>
	<tr style="display:none; height:20px"><td>&nbsp;</td></tr>
	<tr style="display:none; ">
		<td align="center" style="border:solid 2px gray; padding:10px;">
			<form name="form2" method="post" target="iAction" action="seller_data_update.php">
			<input type="hidden" name="latitude" value="({$member['Latitude1']}, {$member['Longitude1']})">
			<table>
				<tr>
					<td colspan="2">
						<table width="100%">
							<tr>
								<td width="30%" nowrap style="font-size:11pt;text-align:left">設定行動商店位置</td>
								<td width="20%" align="center" style="text-align:center"><input type="button" value="現在位置輸入" onClick="Update();"></td>
								<td width="20%" align="center" style="text-align:center"><input type="button" value="清除現在位置" onClick="Clear();"></td>
								<td width="30%" align="center" style="text-align:right">任選a項或b項填寫</td>
							</tr>
						</table>
					</td>
				</tR>
				<tr>
					<td nowrap style="font-size:11pt;">a.我的商品位置:</td>
					<td align="left" style="color:red"><input type="text" name="address" value="{$member['Address1']}" id="address" style="width:468px" onBlur="getLatitude1();">*</td>
				</tR>
				<tr>
					<td nowrap style="font-size:11pt;">b.我的商品經緯:</td>
					<td align="left" style="color:red">
						<select name="area" style="width:150px"><option value="">請選擇地區</option>$area_list</select>*
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<font color=black>緯度：</font><input type="text" name="lat" value="{$lat}" id="lat" style='width:70px'>*
						&nbsp;&nbsp;&nbsp;&nbsp;
						<font color=black>經度：</font><input type="text" name="long" value="{$long}" id="long" style='width:70px'>*
					</td>
				</tR>
				<tr>
					<td colspan="2" align="left">&nbsp;&nbsp;&nbsp;(ex). 台北市&nbsp;&nbsp;&nbsp;&nbsp;緯度：25.04&nbsp;&nbsp;&nbsp;&nbsp;經度：121.518</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr style="display:none; ">
		<td align="left">
			<Table align="left" width="660">
				<tr>
					<td valign="top" width=40>[註]：</td>
					<td valign="top" width=20>(1)</td>
					<td valign="top">若不輸入行動商店位置地址或經緯度, 系統將以"業者(商家)資訊"的住址設定成商品位置。</td>
				</tR>
				<tr>
					<td></td>
					<td valign="top" width=20>(2)</td>
					<td valign="top">同一賣家所登錄之商品, 含有不可移動販售之商品者, 建議非必要目的考量, 否則勿任意設立行動商品位置, 以免造成地址錯誤。</td>
				</tR>
			</table>	
		</td>
	</tr>
</table>
</center>
	

</td></tr></table>

<br>
<br>
<br>
<div id="map" style="display:none"></div>
<script language="javascript">
genCounty(document.form1.county);
chgCounty(document.form1.county, form1.area);
chgArea(document.form1.area, form1.rzip);
document.form1.rzip.value = "{$member['rZip']}";
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
			var map = new GMap2(document.getElementById("map"));
			var geocoder = new GClientGeocoder();
			map.addControl(new GSmallMapControl());
			geocoder.getLatLng(x.value, function(point) {
				if (!point) {
					alert('Google Maps 找不到該地址，將無法計算距離！');
				} else {
					form1.latitude.value = point;
				}
			});
	   }
	}
	else{
		form1.latitude.value = "";
	}
}

function getArea(){
	for(var i=1; i<form2.area.options.length; i++){
		if(form2.address.value.indexOf(form2.area.options[i].text)==0){
			form2.area.options.selectedIndex = i;
			break;
		}
	}
	if(form2.area.options.selectedIndex == 0)
		form2.area.options.selectedIndex = form2.area.options.length-1;
}

function getLatitude1(){
		getArea();
		if (GBrowserIsCompatible()) {
			var map = new google.maps.Map2(document.getElementById("map"));
			var geocoder = new google.maps.Geocoder();
			map.addControl(new GSmallMapControl());

            geocoder.geocode({ address: form2.address.value }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var loc = results[0].geometry.location;
					form2.lat.value = loc.lat();
					form2.long.value =loc.lng();
					form2.latitude.value = form2.lat.value + "," + form2.long.value;
                }
                else
                {
					alert('Google Maps 找不到該地址，將無法計算距離！');
                }
            });
	   }
}

function Clear(){
	if(confirm("確定要清除現在位置?")){
		form2.action="seller_data_clear.php";
		form2.submit();
	}
}
function Update(){
	if(form2.address.value || (form2.area.value && form2.lat.value && form2.long.value)){
//		getLatitude1();
		setTimeout("form2.submit();", 2000);
	}
	else{
		alert("請輸入商品位置或現在經緯!");
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
	/*
	else if(!form1.branch.value){
		alert("請輸入分支行名稱!");
		form1.branch.focus();
	}
	*/
	else if(!form1.account.value){
		alert("請輸入用戶帳號!");
		form1.account.focus();
	}
	else{
		if(confirm("對於賣家之發票地址、用戶帳號更改, 系統將發出電子郵件確認變更, \\n待會員回覆確認信之後, 新的發票地址、用戶帳號始得生效, \\n否則仍以舊有發票地址、用戶帳號作為依據")){
			form1.submit();
		}
	}
}
</script>
EOD;

}
include 'template2.php';
?>

<script language="javascript">
	google.load("maps", "2",{"other_params":"sensor=true"});
</script>
