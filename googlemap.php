<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAAwnI081IB-1YQtn3DiExB7hQAaOLpgxbI1qAjGGRql3xj-qYYohQaZIKM_4qi4nCHaMvKs5cHNkkAWA" type="text/javascript" charset="utf-8"></script>
<body  topmargin="0" leftmargin="0" bottommargin="0" rightmargin="0">
<div id='map' style='width:488px; height:300px'></div>
</body>
<script type="text/javascript"> 
		function createMarker(_point, name, phone, address, id) {
			var icon=new GIcon();
			icon.image="./images/hotel.gif"; 
			icon.iconSize = new GSize(25, 25); 
			icon.iconAnchor = new GPoint(8,8);
			icon.infoWindowAnchor = new GPoint(8, 8);         	
			var marker = new GMarker(_point,{icon:icon, title: address});
			GEvent.addListener(marker, "mouseover", function() {
				var html = "<div align=left>";
				html += "<a href='detail_Rent.php?id=" + id + "'>" + address + "</a><br/>";
				html += name + "<br/>";
				html += phone + "<br/>";
				html += "</div>";          	
				marker.openInfoWindowHtml(html);
			});
			return marker;
		}
</script>
<script type="text/javascript"> 
	var map = new GMap(document.getElementById("map"));
	map.addControl(new GLargeMapControl());
	map.centerAndZoom(new GPoint(120.715,   24.1823), 2);
//	var myLocation = new GMarker(new GPoint(120.6852, 24.1366));
//	map.addOverlay(myLocation);
//	myLocation.openInfoWindowHtml("我的位置");
 
</script>
<script language="javascript"> 
	var marker = createMarker(new GPoint( 120.715,   24.1823), '張小姐', '0928388344', '台中市北屯區松竹路１段', '181');
	map.addOverlay(marker);
</script>