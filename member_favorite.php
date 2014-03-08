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
$today = date('Y-m-d');
$sql = "SELECT Favorite.dateAdded, Product.*, DATEDIFF(dateClose,'$today') AS days, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Product='$no' AND Member='" . $_SESSION['member']['No'] . "'), 0) AS Amounts, IFNULL((SELECT COUNT(*) FROM Orders WHERE Product='$no' AND Member='" . $_SESSION['member']['No'] . "'), 0) AS Buy, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.No=logCoupon.couponNo WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, IFNULL((SELECT count(*) FROM Coupon WHERE Status = 1 AND Product=Product.No), 10000) AS coupon_used, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Address1 FROM Member WHERE No = Product.Member) AS Address1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude) AS L1, IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude) AS L2, IFNULL((SELECT SUM(Quality) FROM logRating WHERE Owner = Product.Member), 0) as Rate, (SELECT Nick FROM Member WHERE Member.No = Product.Member) AS userName, (SELECT warning FROM Member WHERE Member.No = Product.Member) AS warning, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM FROM Product INNER JOIN Favorite ON Product.No = Favorite.Product WHERE Favorite.Member = '" . $_SESSION['member']['No'] . "'";//dateApprove <> '0000-00-00 00:00:00'";//
$result = mysql_query($sql);
$num = mysql_num_rows($result);
$pagesize  = 10;
$pages = ceil($num / $pagesize);
$pageno = $_REQUEST['pageno'];
if($pageno == "" || $pageno > $pages){$pageno = 1;}

$products = "<table width='700' align='center' border=0>";
$products .= "<tr><td valign=middle style='border-bottom:solid 1px gray; line-height:60px; text-align:left; font-weight:bold;font-size:16px;'>我的收藏與追蹤</td></tr>";
$products .= "<tr><td height=6 style='font-size:1px;'>&nbsp;</td></tr>";
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
//	for ($i = 0; $i < $pagesize; $i++) {
		while($data = mysql_fetch_array($result)){
			$price = "$" . number_format($data['Price']);
			$sell = "$" . number_format($data['Price']*$data['Discount'] / 10);
			$sell = "$" . number_format($data['Price1']);
			$save = "$" . number_format($data['Price']*(10-$data['Discount']) / 10);
			$save = "$" . number_format($data['Price'] - $data['Price1']);
			$used = (($data['Used'] == 1) ? "(中古品)" : "");
			$discount = (float)(number_format($data['Discount'],2));
			if($discount <= 0){
				$discount = "免費";
			}
			else if($discount >= 10){
				$discount = "";
			}
			else{
				$discount = $discount . "折";
			}
			if($data['Mode'] == 1){
				if($data['Deliver'] == 0){
					$type=1;
				}
				if($data['Deliver'] == 1){
					$type=2;
				}
			}
			if($data['Mode'] == 2){
				if($data['Deliver'] == 0){
					$type=4;
				}
				if($data['Deliver'] == 1){
					$type=5;
				}
			}
			$url = "product{$type}_detail.php?no={$data['No']}";
			$disable = (($data['Status'] != 2 || $data['days'] <= 0) ? "!" : "");
			$dis = (($data['Status'] != 2 || $data['days'] <= 0) ? "none" : "");
			if($data['Deliver'] == 0){
				if($data['M1'] > 0 && $data['mobile'] == 1){
					$address = (($data['Address1'] != "") ? $data['Address1'] : "");
				}
				else{
					$address = (($data['Address'] != "") ? $data['Address'] : "");
				}
				$l1 = number_format($data['L1'], 2);
				$l2 = number_format($data['L2'], 2);

				if($address == ""){
					$address = "{$data['L1']},{$data['L2']}";
				}
				$map_url = "http://maps.google.com/maps?f=q&source=s_q&hl=zh-TW&geocode=&ie=UTF8&z=14&iwloc=A&output=embed&hq=&q=" . $address . "&hnear=" . $address . "&&sll={$data['L1']},{$data['L2']}";
				$position = <<<EOD
					服務位置：<{$disable}a href="javascript:parent.Dialog1('$map_url', 600);">地圖查詢</a><br>
					<span style="display:$dis">
					緯度：<font color=gray>{$l1}</font>　經度：<font color=gray>{$l2}</font></span><br>
EOD;
			}
			else{
				$position = "";
			}
			$status = "<font>上架中</font>";
			switch($data['Status']){
				case 0:;
					$status = "<font>未上架</font>";
					break;
				case 1:
					$status = "<font>未上架</font>";
					break;
				case 2:
					$status = "<font>上架中</font>";
					break;
				case 3:
					$status = "<font>未上架</font>";
					break;
				case 4:
					$status = "<font>未上架</font>";
					break;
			}
			
			$status = (($data['Status'] != 2 || $data['days'] <= 0) ? "未上架" : "上架中");

			$price1 = "售價：{$sell}" . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;原價：{$price}";
			$price2 = "原價：{$price}";

			if($data['Transport'] == 1){
				$price1 = $data['taxi_discount'];
				$price2 = "<br>";
			}
			else if($data['price_mode'] == 1){
				$price1 = $data['price_info'];
				$price2 = "<br>";
			}
			$price2 = "收藏日期：" . substr($data['dateAdded'], 0, 10);
			$products .= <<<EOD
				<tr>
					<td>
						<table style="width:100%">
							<tr>
								<td style="width:152px"><{$disable}a href="$url"><img src="./upload/{$data['Photo']}" style="width:152px; height:99px; border:solid 1px gray"></a></td>
								<td align="left" style="padding-left:5px; line-height:30px"><{$disable}a href="$url" target="_blank">
									{$data['Name']}</a><br>
								{$price1}<br>
								{$price2}	 <!--(<font style='color:red'>{$discount}</font>)-->
								</td>
								<td style="text-align:left; line-height:30px; width:215px">
							服務狀態：{$status}<br>
						{$position}
								</td>
								<td style="width:50px; text-align:center"><a href="javascript:Delete('{$data['No']}');" title="移除"><img src="./images/delete.gif" border="0"></a></td>
							</tr>
						</table>
						<div style="text-align:left">商家[<a href="member_intro.php?seller={$data['Member']}" target="_blank">{$data['userName']}</a>]預告事項：{$data['warning']}</div>
									<br>
					</td>
				</tr>
EOD;
		}
	/*
		else{
			break;
		}
	}
	$products .= "<tr>";
	$products .= "	<td>";
	$products .= "		<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">";
    $products .= "           <tr>";
    $products .= "             <td style='width:100px; text-align:left; padding-left:10px'>";
	if($pageno > 1)
	$products .= "				<a href=\"javascript:" . (($pageno > 1) ? "setPage(" . ($pageno - 1) . ")" : "void(0)"). ";\">上一頁</a>";
	$products .= "			  </td>";
    $products .= "             <td align=\"center\" nowrap>";
	for($i=0; $i<$pages; $i++){
		$products .= "<a href=\"javascript:setPage(" . ($i+1) . ");\" style='color:black; text-decoration:none'>" . ($i+1) . "</a>&nbsp;";		
	}
	$products .= "			</td>";
	$products .= "			<td style='width:100px; text-align:right; padding-right:10px'>";
	if($pageno < $pages)
	$products .= "				<a href=\"javascript:" . (($pageno < $pages) ? "setPage(" . ($pageno + 1) . ")" : "void(0)") . ";\">下一頁</a>";
	$products .= "			</td>";
	$products .= "			</tr>";
	$products .= "		</table>";
	$products .= "	</td>";
	$products .= "</tr>";
	*/
}
$products .= "</table>";
include './include/db_close.php';




$WEB_CONTENT = <<<EOD
<center>
$products
</center>
EOD;
include 'template2.php';
?>
<form name="iForm" method="post" target="iAction" action="del_favorite.php">
	<input type="hidden" name="product" value="">
</form>
<script language="javascript">
function Delete(x){
	var iForm = document.iForm;
	iForm.product.value = x;
	iForm.submit();
}
</script>