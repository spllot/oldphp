<?php
include './include/session.php';
require_once './class/javascript.php';
/*
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Redirect("./member_login.php?url=".urlencode($_SERVER['PHP_SELF']));
	exit;
}
*/
$tab = (($_REQUEST['tab'] == "") ? "4" : $_REQUEST['tab']);
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Member WHERE userID = '" . $_SESSION['member']['userID'] . "'");
$data = mysql_fetch_array($result);

$result = mysql_query("SELECT * FROM Catalog WHERE USEFOR='TYPE_AREA' ORDER BY Sort");
while($rs = mysql_fetch_array($result)){
	$area_list .= "<option value='{$rs['No']}'" . (($rs['No'] == $data['subscribeArea']) ? " SELECTED":"") . ">{$rs['Name']}</option>";
}
include './include/db_close.php';
$subscribe = (($data['Subscribe'] == 1) ? " CHECKED":"");
$display = (isset($_SESSION['member']) ? "" : "display:none");




$WEB_CONTENT = <<<EOD
<center>
<table width=100% cellpadding=10 style="background:white; height:500px">
	<tr>
		<td align="center"><form name="iForm" method="post" action="member_location_save.php" target="iAction" style="margin:0">
			<input type="hidden" name="latitude" value="({$_SESSION['Latitude']}, {$_SESSION['Longitude']})">
			<input type="hidden" name="url" value="{$_REQUEST['url']}">
			<table width=640 border=0>
				<tr style="$display">
					<td style="font-size:11pt; width:100px" nowrap>我的收件註冊位置:</td>
					<td style="font-size:11pt; text-align:left; color:gray" colspan="5">{$_SESSION['member']['Address0']}　(<font style="color:black">緯度:</font>{$_SESSION['member']['Latitude0']}，<font style="color:black">經度:</font>{$_SESSION['member']['Longitude0']})</td>
				</tr>
				<tr>
					<td nowrap style="font-size:11pt;">a.我的現在位置:</td>
					<td colspan="2" align="left" style="color:red"><input type="text" name="address" value="{$_SESSION['Address']}" id="address" size="40"  onBlur="getLatitude();">*</td>
					<td colspan="3" align="center">任選a項或b項填寫</td>
				</tR>
				<tr>
					<td nowrap style="font-size:11pt;">b.我的現在經緯:</td>
					<td colspan="2" align="left" style="color:red">
						<font color=black>緯度：</font><input type="text" name="lat" value="{$_SESSION['Latitude']}" id="lat" style='width:70px'>*
						&nbsp;&nbsp;&nbsp;
						<font color=black>經度：</font><input type="text" name="long" value="{$_SESSION['Longitude']}" id="long" style='width:70px'>*
					<td colspan="3" align="center"><input type="button" value="現在位置輸入" onClick="Update();"></td>
					</td>
				</tR>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td align="center">
			<table width=640>
				<tr>
					<td><div id="map1" style="width:640; height:400px;"></div></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top" align="center">
			<table style="width:640px">
				<tr>
					<td valign="top" align="right" nowrap style="font-size:11pt;color:gray">註(1): </td>
					<td valign="top" align="left" style="font-size:11pt;color:gray">[我的現在位置]尚未變更時, 以註冊時所填寫之[收件住址]為初始定位參考位置。</td>
				</tr>
				<tr>
					<td valign="top" align="right" nowrap style="font-size:11pt;color:gray">註(2): </td>
					<td valign="top" align="left" style="font-size:11pt;color:gray">[我的現在位置]僅做為買家前往商家購買商品之規劃用途, 商家之商品遞送到府服務並不針對[我的現在位置] 做為遞送目標, 如需改變商品遞送位置, 需由[用戶中心]>>[個人資料]之處更改”收件地址”。</td>
				</tr>
				<tr>
					<td valign="top" align="right" nowrap style="font-size:11pt;color:gray">註(3): </td>
					<td valign="top" align="left" style="font-size:11pt;color:gray">前台商品將依據會員[我的現在位置] 之設定, 做為商品陳列順序之參考。</td>
				</tr>
				<tr>
					<td valign="top" align="right" nowrap style="font-size:11pt;color:gray">註(4): </td>
					<td valign="top" align="left" style="font-size:11pt;color:gray">以上之商品地圖以會員位置為中心, 顯示商品相對於會員之方位, 若在圖中點選目標商品, 可以直接進入到商品說明頁面。</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td style="text-align:left; font-weight:bold; padding-left:20px">商家服務與商品搜尋：</td>
	</tr>
	<tr>
		<td style="text-align:left; padding-left:20px">
			<form name="sform" action="member_search2.php" target="iAction">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td><input type="text" name="keyword" style="width:480px"></td>
					<td style="width:80px; text-align:center">
						<select name="type">
							<option value="email">商家帳號</option>
							<option value="code">服務代碼</option>
						</select>
					</td>
					<td style="width:60px; text-align:center"><input type="button" value="搜尋" onClick="Search();"></td>
				</tr>
			</table>
			</form>
			<script language="javascript">
			function goSeller(xNo){
				window.open("member_intro.php?seller=" + xNo);
			}
			function goProduct(xType, xNo){
				window.open("member_product" + xType + "_detail.php?no=" + xNo);
			}
			function Search(){
				if(!sform.keyword.value){
					alert("請輸入查詢字串!");
					sform.keyword.focus();
				}
				else{
					sform.submit();
				}
			}
			</script>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>
</center><div id="map" style="display:none"></div>
EOD;
include 'search.php';

$data['Name'] = "InTimeGo 即購網";

include 'template0.php';
?>
<script language="javascript">
	var iForm = document.iForm;
	function getLatitude(){
		if(iForm.address.value){
			if (GBrowserIsCompatible()) {
				var map = new google.maps.Map2(document.getElementById("map"));
				var geocoder = new google.maps.Geocoder();
				map.addControl(new GSmallMapControl());

                geocoder.geocode({ address: iForm.address.value }, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        var loc = results[0].geometry.location;
						iForm.lat.value = loc.lat();
						iForm.long.value =loc.lng();
						iForm.latitude.value = iForm.lat.value + "," + iForm.long.value;
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
	function Update(){
		if(iForm.address.value || (iForm.lat.value && iForm.long.value)){
//			getLatitude();
			setTimeout("iForm.submit();", 2000);
		}
		else{
			alert("請輸入現在位置或現在經緯!");
		}

	}
</script>
<script type="text/javascript">
		function createMarker(_point, name, phone, address, id, itype) {
			var icon=new GIcon();
			icon.image="./images/" + ((itype) ? itype : "hotel") + ".gif"; 
			icon.iconSize = new GSize(25, 25); 
			icon.iconAnchor = new GPoint(8,8);
			icon.infoWindowAnchor = new GPoint(8, 8);         	
			var marker = new GMarker(_point,{icon:icon, title: name});
			GEvent.addListener(marker, "mouseover", function() {
				var html = "<div align=left>";
				html += "<a href='product_detail.php?id=" + id + "'>" + name + "</a><br/>";
//				html += address + "<br/>";
//				html += phone + "<br/>";
				html += "</div>";          	
				marker.openInfoWindowHtml(html);
			});
			return marker;
		}
</script>



<script type="text/javascript">
  google.load("maps", "2",{"other_params":"sensor=true"});
<?if($_SESSION['Longitude']!="" && $_SESSION['Latitude']!=""){?>
function setCenter(){
	var map = new google.maps.Map2(document.getElementById("map1"));
	map.addControl(new GSmallMapControl());
	map.setCenter(new google.maps.LatLng(<?=$_SESSION['Latitude']?>, <?=$_SESSION['Longitude']?>), 10);
	var myLocation = new GMarker(new google.maps.LatLng(<?=$_SESSION['Latitude']?>, <?=$_SESSION['Longitude']?>));
	map.addOverlay(myLocation);
	myLocation.openInfoWindowHtml("<div style='width:60px; height:20px; text-align:center'>我的位置</div>");
<?

$area=$_REQUEST['area'];
$type=$_REQUEST['type'];
$catalog=$_REQUEST['catalog'];
include './include/db_open.php';

$c =  " AND Status = 2 AND Mode = 2 AND Deliver = 0 AND dateClose >= CURRENT_TIMESTAMP";
$c .= (($area!="") ? " AND (((SELECT Latitude1 FROM Member WHERE No = Product.Member) = 0 AND Area = '$area') OR ((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0 AND (SELECT Area1 FROM Member WHERE No=Product.Member) = '$area'))" : "");
if($type != ""){
	switch($type){
		case 'all':
			$c .= " AND Activity = '0' AND Transport=0 AND event=0 AND hr=0";
			break;
		case 'activity':
			$c .= " AND Activity = '1'";
			break;
		case 'welfare':
			$c .= " AND welfare = '1'";
			break;
		case 'free':
			$c .= " AND Price1 = '0' AND price_mode=0 AND Transport = 0";
			break;
		case 'allnew':
			$c .= " AND Allnew = '1'";
			break;
		case 'transfer':
			$c .= " AND Transport = '1'";
			break;
		case 'used':
			$c .= " AND Used = '1'";
			break;
		case 'sale':
			$c .= " AND Sale = '1'";
			break;
		case 'hr':
			$c .= " AND hr = '1'";
			break;
		case 'event':
			$c .= " AND event = '1'";
			break;
		default:
			$c .= " AND Type = '$type'";
			break;
	}
}
$c .= (($catalog!="") ? " AND Catalog = '$catalog'" : "");
$c .= (($catalog2!="") ? " AND Catalog2 = '$catalog2'" : "");
$c .= (($catalog3!="") ? " AND Catalog3 = '$catalog3'" : "");




$sql = "SELECT DISTINCT Member, (SELECT No FROM Product WHERE Member=P.Member {$c} ORDER BY Sort LIMIT 1) AS No FROM Product P WHERE 1=1" . $c;
$result = mysql_query($sql) or die(mysql_error());
$num = mysql_num_rows($result);
$nos = "";
$i=0;
while($rs=mysql_fetch_array($result)){
	$i++;
	$nos .= $rs['No'];
	$nos .= (($num > $i) ? ",":"");
}




$sql2 = "SELECT *, (SELECT Status2 FROM Member WHERE No=Product.Member) AS Status2, (SELECT Flag FROM Member WHERE No=Product.Member) AS Flag, (SELECT Intime FROM Member WHERE No=Product.Member) AS Intime, (SELECT taxi_addr FROM Member WHERE No=Product.Member) AS taxi_addr, (SELECT taxi_dest FROM Member WHERE No=Product.Member) as taxi_dest, (SELECT Name FROM Catalog WHERE No=Product.Type) AS TName, (SELECT Name FROM Catalog WHERE No=Product.Catalog) AS CName, (SELECT Nick FROM Member WHERE No=Product.Member) AS Nick, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.No=logCoupon.couponNo WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0 AND Product.mobile > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude) AS L1, IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0 AND Product.mobile > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude) AS L2, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0 AND Product.mobile > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0 AND Product.mobile > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM, (SELECT Level FROM Member WHERE Member.No = Product.Member) AS Level, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1 FROM Product WHERE 1=1 " . (($nos != "") ? " AND No IN ($nos)" : "");
$sql2 .= $c;

$sql = "$sql2";
$sql .= " ORDER BY " .(($_SESSION['Latitude'] > 0 && $_SESSION['Longitude'] > 0) ? "KM, Level DESC, dateUpdate DESC" : "Level DESC, dateUpdate DESC");



$result = mysql_query($sql) or die(mysql_error());
while($rs = mysql_fetch_array($result)){
	if($rs['L1'] > 0 && $rs['L2'] > 0){
		$itype = 'store';
		if($rs['hr'] == 1 && $rs['employer'] == 0){
			$itype = 'shape1';
		}
		if($rs['hr'] == 1 && $rs['employer'] == 1){
			$itype = 'shape2';
		}
		if($rs['event'] == 1){
			$itype = 'activity3';
		}
		if($rs['Activity'] == 1){
			$itype = 'gift';
		}
		if($rs['Transport'] == 1){
			$itype = 'car';
		}
		echo <<<EOD
		var marker = createMarker(new google.maps.LatLng( {$rs['L1']}, {$rs['L2']} ), '{$rs['Name']}', '{$rs['Phone']}', '{$rs['Address']}', '{$rs['No']}', '{$itype}');
		map.addOverlay(marker);
EOD;
	}
}


include './include/db_close.php';

?>
}
  google.setOnLoadCallback(setCenter);

<?}else{?>
	$("#map1").html("<font style='color:gray; font-size:12pt'>未設定現在位置，無法顯示地圖</font>");
<?}?>
</script>
