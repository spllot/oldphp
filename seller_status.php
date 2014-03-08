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

$rating = 0;
$result = mysql_query("SELECT IFNULL(SUM(Quality), 0) AS COUNTS FROM logRating WHERE Owner='" . $_SESSION['member']['No'] . "'");
if($rs=mysql_fetch_array($result)){
	$rating = $rs['COUNTS'];
}

$area_list = "";
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_AREA' ORDER BY Sort");
while($rs=mysql_fetch_array($result)){
	$area_list .= "<option value='" . $rs['No'] . "'" . (($member['area_web'] == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}
include './include/db_close.php';

//if($member['dateRequest'] != "0000-00-00 00:00:00" && $member['dateApprove'] != "0000-00-00 00:00:00"){
if(1==1){


include 'seller_data_tab.php';



$status11 = (($member['Status1'] == 1) ? " CHECKED": "");
$status12 = (($member['Status1'] == 2) ? " CHECKED": "");
$status13 = (($member['Status1'] == 3) ? " CHECKED": "");
$status21 = (($member['Status2'] == 1) ? " CHECKED": "");
$status22 = (($member['Status2'] == 2) ? " CHECKED": "");


$lat = (($member['latitude_web'] > 0) ? $member['latitude_web'] : "");
$long = (($member['longitude_web'] > 0) ? $member['longitude_web'] : "");

$dis_empty = "none";
if($member['Status1'] == 3){
	$dis_empty = "block";
}

$empty1 = (($member['Empty'] == 1) ? " CHECKED":"");
$empty2 = (($member['Empty'] == 2) ? " CHECKED":"");
$empty3 = (($member['Empty'] == 3) ? " CHECKED":"");

$WEB_CONTENT = <<<EOD
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center">
		<center>


<table border=0 width="90%">
	<tR>
		<td style="color:gray; text-align:left"><Br>
		[說明]：關於此頁面設定說明,可以<a href="{$_CONFIG['urlG']}" target="_blank">參考行動服務與設定</a>。
		</td>
	</tr>
	<tr>
		<td align="center"><br>
<form name="form1" method="post" target="iAction" action="seller_status_save.php">
<table>
	<tr>
		<td style="text-align:left"><span style="background:#000000; color:white;">&nbsp;&nbsp;本地服務營業狀態設定:&nbsp;&nbsp;</span></td>
	</tr>
	<tr>
		<td style="text-align:left"> 
			<table style="text-align:left">
				<tr>
					<td style="width:20px; text-align:center"><input type="radio" name="status1" value="1"{$status11} onClick="resetAddr();$('#empty').hide(); form1.submit();"></td>
					<td style="text-align:left">商家登入/登出網站時，商品&服務皆為”上架營業”狀態（24hr上架模式）。</td>
				</tr>
				<tr>
					<td style="width:20px; text-align:center"><input type="radio" name="status1" value="2"{$status12} onClick="resetAddr();$('#empty').hide(); form1.submit();"></td>
					<td style="text-align:left">商家登入/登出網站時，商品&服務皆為”不上架營業”狀態（暫時下架模式）。</td>
				</tr>
				<tr>
					<td style="width:20px; text-align:center" valign='top'><input type="radio" name="status1" value="3"{$status13} onClick="$('#empty').show(); form1.submit();"></td>
					<td style="text-align:left">商家登入網站商品&服務為” 上架營業”狀態，登出網站商品&服務為”不上架營業”狀態（[商品]_行動販售/[運輸]/[人力]/[活動]服務模式）。
						<div id="empty" style="display:{$dis_empty}; margins:2px:">
							<p style="height:10px;font-size:1px;">&nbsp;</p>
							[運輸]服務狀態設定：<br>
							<input type="radio" name="empty" value="1"{$empty1} onClick="resetAddr();form1.submit()">空車
							<input type="radio" name="empty" value="2"{$empty2} onClick="resetAddr();form1.submit()">載運中(不開放共乘 或 共乘人數已額滿)<br>
							<input type="radio" name="empty" value="3"{$empty3} onClick="setAddress2();form1.submit()">載運中(共乘) 
							行駛目標：

							<input type="text" size="3" name="taxi_zip" onKeyUp="setArea(form1.taxi_county, form1.taxi_area, this.value);" maxlength="5" value="{$member['taxi_zip']}" style="display:none"><input type="hidden" name="taxi_addr">
							<select name="taxi_county" onChange="chgCounty(form1.taxi_county, form1.taxi_area);chgArea(form1.taxi_area, form1.taxi_zip);setAddress2();form1.submit()"></select>
							<select name="taxi_area" onChange="chgArea(form1.taxi_area, form1.taxi_zip);setAddress2();form1.submit()"></select><font color=red>*</font>
							<input type="text" style="width:120px" name="taxi_dest" value="{$member['taxi_dest']}" onchange="setAddress2();form1.submit()" maxlength=8><br><span style="color:red;">注意: 載客(共乘)行駛目標右邊欄位, 填寫八個字以內之道路名稱。</span>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="text-align:left"><hr></td>
	</tr>
	<tr>
		<td style="text-align:left"><span style="background:#000000; color:white;">&nbsp;&nbsp;我的行動服務位置來源設定:&nbsp;&nbsp;</span></td>
	</tr>
	<tr>
		<td style="text-align:left">
			<table style="text-align:left">
				<tr>
					<td style="width:20px; text-align:center; vertical-align:top"><input type="radio" name="status2" value="1"{$status21} onClick="setAddress2();form1.submit();"></td>
					<td style="text-align:left; vertical-align:top">無論是行動裝置APP或是以PC連結本站時, 行動服務位置皆來自 [手動設定行動服務位置]。</td>
				</tr>
				<tr>
					<td style="width:20px; text-align:center; vertical-align:top"><input type="radio" name="status2" value="2"{$status22} onClick="setAddress2();form1.submit();"></td>
					<td style="text-align:left; vertical-align:top">當以行動裝置APP連結本站時, 行動服務位置來自行動裝置的位置偵測。</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td>
			<table>
				<tr>
					<td style="width:20px; text-align:center; vertical-align:top; color:gray">[註]:</td>
					<td style="text-align:left; vertical-align:top; color:gray">當使用行動裝置APP登出時，商品設定仍為”上架營業”狀態，APP最後所偵測之位置即為商品目前所在位置。</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="text-align:left"><hr></td>
	</tr>
</table>
</form>
		</td>
	</tr>
	<tr>
		<td align="center" style="padding-top:10px; padding-bottom:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td nowrap style="font-size:11pt;text-align:left"><span style="background:#000000; color:white;">&nbsp;&nbsp;手動設定行動服務位置 (任選a項或b項填寫)&nbsp;&nbsp;</span></td>
					<td nowrap align="right" style="text-align:right">
						<input type="button" value="現在位置輸入" onClick="Update();">
						<input type="button" value="清除現在位置" onClick="Clear();">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" style="border:solid 2px gray; padding:5px">
			<table border=0>
				<form name="form2" method="post" target="iAction" action="seller_data_update.php">
				<input type="hidden" name="latitude" value="({$member['latitude_web']}, {$member['longitude_web']})">
				<tr>
					<td nowrap style="font-size:11pt;">a.我的服務位置:</td>
					<td align="left" style="color:red"><input type="text" name="address" value="{$member['address_web']}" id="address" style="width:468px" onBlur="getLatitude1();">*</td>
				</tR>
				<tr>
					<td nowrap style="font-size:11pt;">b.我的服務經緯:</td>
					<td align="left" style="color:red">
						<select name="area" style="width:150px"><option value="">請選擇地區</option>$area_list</select>*
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<font color=black>緯度：</font><input type="text" name="lat" value="{$lat}" id="lat" style='width:70px'>*
						&nbsp;&nbsp;&nbsp;&nbsp;
						<font color=black>經度：</font><input type="text" name="long" value="{$long}" id="long" style='width:70px'>*
					</td>
				</tR>
				<tr>
					<td colspan="2" align="left" style="; color:gray">&nbsp;&nbsp;&nbsp;(ex). 台北市&nbsp;&nbsp;&nbsp;&nbsp;緯度：25.04&nbsp;&nbsp;&nbsp;&nbsp;經度：121.518</td>
				</tr>
				</form>
			</table>
		</td>
	</tr>
	<tr>
		<td align="left">
			<Table align="left" width="660">
				<tr>
					<td valign="top" width=40 style="; color:gray">[註]：</td>
					<td valign="top" width=20 style="; color:gray">(1)</td>
					<td valign="top" style="; color:gray">若無手動輸入行動服務位置地址或經緯度資料時, 系統將以服務介紹頁之"業者(商家)資訊"的住址設定成服務位置。</td>
				</tR>
				<tr>
					<td></td>
					<td valign="top" width=20 style="; color:gray">(2)</td>
					<td valign="top" style="; color:gray">行動賣家請勿使用自己帳號幫友人提案服務, 以避免發生商品成交卻無法遞送商品的問題。</td>
				</tR>
				<tr>
					<td></td>
					<td valign="top" width=20 style="; color:gray">(3)</td>
					<td valign="top" style="; color:gray">行動商家本身所提案服務, 不可含有不能移動販售之商品, 一旦商家手動設定行動服務位置, 則商家所有商品位置皆會改變為商家所輸入的位置。</td>
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
	var form1 = document.form1;
genCounty(document.form1.taxi_county);
chgCounty(document.form1.taxi_county, form1.taxi_area);
chgArea(document.form1.taxi_area, form1.taxi_zip);
document.form1.taxi_zip.value = "{$member['taxi_zip']}";
setArea(document.form1.taxi_county, form1.taxi_area, form1.taxi_zip.value);
</script>
<script language="javascript">
function resetAddr(){
	form1.taxi_zip.value = '';
	form1.taxi_dest.value = '';
	setArea(document.form1.taxi_county, form1.taxi_area, form1.taxi_zip.value);
}
function setAddress2(){
		form1.taxi_addr.value = form1.taxi_county.options[form1.taxi_county.options.selectedIndex].text + form1.taxi_area.options[form1.taxi_area.options.selectedIndex].text;
}
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
	else if(!form1.branch.value){
		alert("請輸入分支行名稱!");
		form1.branch.focus();
	}
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
