<?php
include './include/session.php';
$no		=$_REQUEST['no'];
$start	=$_REQUEST['start'];
//print_r($_REQUEST);
if($no!="" && $start != ""){
	include './include/db_open.php';
	$sql = "SELECT * FROM Member WHERE No='" . $_SESSION['member']['No'] . "'";
	$result=mysql_query($sql) or die(mysql_error());
	$member=mysql_fetch_array($result);
	$sql = "SELECT *, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Product='$no' AND Member='" . $_SESSION['member']['No'] . "'), 0) AS Amounts, IFNULL((SELECT COUNT(*) FROM Orders WHERE Product='$no' AND Member='" . $_SESSION['member']['No'] . "'), 0) AS Buy, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.Serial=logCoupon.Serial WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, IFNULL((SELECT count(*) FROM Coupon WHERE Status = 1 AND Product=Product.No), 10000) AS coupon_used, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Address1 FROM Member WHERE No = Product.Member) AS Address1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0 AND Product.mobile > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude) AS L1, IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0 AND Product.mobile > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude) AS L2, IFNULL((SELECT SUM(Quality) FROM logRating WHERE Owner = Product.Member), 0) as Rate, (SELECT Nick FROM Member WHERE Member.No = Product.Member) AS userName, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0 AND Product.mobile > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM FROM Product WHERE No = '$no' ORDER BY KM";
//	echo $sql;
	$result = mysql_query($sql) or die(mysql_error());
	$data = mysql_fetch_array($result);

	$sql = "SELECT * FROM Member WHERE No='" . $data['Member'] . "'";
	$result=mysql_query($sql) or die(mysql_error());
	$seller=mysql_fetch_array($result);
	$h = 400;
	if($start == 1){//商家->買家收件
		$address = $data['Address'];//start
		$lat1 = $member['Latitude0'];//end
		$long1 = $member['Longitude0'];
		$title="買家收件地址";//end
		if($data['M1'] > 0 && $data['mobile'] == 1){
			$address = $data['Address1'];
		}
		if($address == ""){
			$address = $data['L1'] . ", " . $data['L2'];
		}

		$address2 = $lat1 . ", " . $long1;
		
		if($data['Status2'] == 2){
			$address = $data['L1'] . ", " . $data['L2'];
		}
		if($seller['Status1'] == 3 && $seller['Empty'] == 3 && $data['Transport']==1){
			$h = 360;
			$lat1 = $data['L1'];//end
			$long1 = $data['L2'];

			$address = $lat1 . ", " . $long1;

			$address2 = $seller['taxi_addr'] . $seller['taxi_dest'];
			$caption = "<div style='color:red; text-align:center; line-height:40px; height:40px'>此行車路徑僅作為駕駛與乘客參考，實際路徑由駕駛依據實際路況而決定</div>";
		}
	
	}
	else{//買家->商家
		$address = $_SESSION['Address'];//start
		$lat1 = $data['L1'];//end
		$long1 = $data['L2'];
		$title=$data['Seller'];//"我的現在位置";//end
		$address2 = $lat1 . ", " . $long1;
	}




	include './include/db_close.php';
}
else{
	exit;
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<html>
<head>
<title></title>

	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false" charset="utf-8"></script>
    <script type="text/javascript">
	var directionDisplay;
	var directionsService = new google.maps.DirectionsService();
	function initialize() {
		/*
		*/
		var latlng = new google.maps.LatLng(<?=$lat1?>,  <?=$long1?>);//25.0421009063721,  121.565002441406
		directionsDisplay = new google.maps.DirectionsRenderer();
		var myOptions = {
			zoom: 14,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControl: false
		};
		var map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
		directionsDisplay.setMap(map);
		directionsDisplay.setPanel(document.getElementById("directionsPanel"));
		/*
		var marker = new google.maps.Marker({
			position: latlng, 
			map: map, 
			title:"<?=$title?>"
		});
		*/
	}
	function calcRoute() {
		var start = "<?=$address?>";//document.getElementById("routeStart").value;
		var end = "<?=$address2?>";//"<?=$lat1?>,  <?=$long1?>";
		var request = {
			origin:start,
			destination:end,
			travelMode: google.maps.DirectionsTravelMode.DRIVING
		};
		directionsService.route(request, function(response, status) {
			if (status == google.maps.DirectionsStatus.OK) {
				directionsDisplay.setDirections(response);
			} else {
				if (status == 'ZERO_RESULTS') {
					alert('No route could be found between the origin and destination.');
				} else if (status == 'UNKNOWN_ERROR') {
					alert('A directions request could not be processed due to a server error. The request may succeed if you try again.');
				} else if (status == 'REQUEST_DENIED') {
					alert('This webpage is not allowed to use the directions service.');
				} else if (status == 'OVER_QUERY_LIMIT') {
					alert('The webpage has gone over the requests limit in too short a period of time.');
				} else if (status == 'NOT_FOUND') {
					alert('At least one of the origin, destination, or waypoints could not be geocoded.');
				} else if (status == 'INVALID_REQUEST') {
					alert('The DirectionsRequest provided was invalid.');					
				} else {
					alert("There was an unknown error in your request. Requeststatus: \n\n"+status);
				}
			}
		});
	}
	</script>
</head>
<body onLoad="initialize();">
<center>
	<div id="map_canvas" style="width:720px; height:<?=$h?>px"></div>
	<?=$caption?>
</center>
	<script language="javascript">calcRoute();</script>
</body>
</html>