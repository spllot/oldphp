<?php
session_start();
include './include/db_open.php';
$id = $_REQUEST['id'];
$tab = $_REQUEST['tab'];
$area = $_REQUEST['area'];
$type = $_REQUEST['type'];
$catalog = $_REQUEST['catalog'];





$sql = "SELECT *, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, IFNULL((SELECT COUNT(*) FROM logCoupon WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No AND Orders.Status <> 3), 0) AS Sold, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(Product.Latitude, Product.Longitude, '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM, (SELECT Level FROM Member WHERE Member.No = Product.Member) AS Level FROM Product WHERE Member='$id' AND Status = 2 AND dateClose >= CURRENT_TIMESTAMP";
$sql .= (($area!="") ? " AND Area = '$area'" : "");
//$sql .= (($type!="") ? " AND Type = '$type'" : "");
$sql .= (($catalog!="") ? " AND Catalog = '$catalog'" : "");
switch($tab){
	case 1:
		$sql .= " AND Mode = 1 AND Deliver = 0";
		break;
	case 2:
		$sql .= " AND Mode = 1 AND Deliver = 1";
		break;
	case 4:
		$sql .= " AND Mode = 2 AND Deliver = 0";
		break;
	case 5:
		$sql .= " AND Mode = 2 AND Deliver = 1";
		break;
}
if($type != ""){
	switch($type){
		case 'all':
			$sql .= " AND Activity = '0'";
			break;
		case 'activity':
			$sql .= " AND Activity = '1'";
			break;
		default:
			$sql .= " AND Type = '$type'";
			break;
	}
}

$sql .= " ORDER BY " .(($_SESSION['Latitude'] > 0 && $_SESSION['Longitude'] > 0) ? "KM, Level DESC, dateUpdate DESC" : "Level DESC, dateUpdate DESC");
//echo $sql;
//echo $sql;
$result = mysql_query($sql) or die(mysql_error());
$num = mysql_num_rows($result);
$pagesize  = 10;
$pages = ceil($num / $pagesize);
$pageno = $_REQUEST['pageno'];
if($pageno == "" || $pageno > $pages){$pageno = 1;}


$WEB_CONTENT = "<table cellpadding='0' cellspacing='0' border='0'>";
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($data = mysql_fetch_array($result)){
			$price = "$" . ($data['Price']);
			$sell = "$" . ($data['Price1']);
			$save = "$" . ($data['Price'] - $data['Price1']);
			if($_SESSION['Latitude'] > 0 && $_SESSION['Longitude']>0){
				$dis =(($data['Latitude']>0 && $data['Longitude'] > 0) ? "距離：" .  (float)(number_format($data['KM'], 1)) . "公里" : "");
				$dis =(($data['L1']>0 && $data['L2'] > 0) ? "距離：" .  (float)(number_format($data['KM'], 1)) . "公里" : "");
			}
			else{
				$dis = "距離：? ";//(請輸入<a href='member_location.php?url=" . urlencode("product4.php?area=$area&catalog=$catalog&type=$type&pageno=$pageno") . "&tab=4' style='text-decoration:underline'>我的位置</a>，再回頁面)</font>";
			}
			$dis = "";
			$counts = 0;
			$used = (($data['Used'] == 1) ? "<img src='./images/old_good.gif'>" : "");
			$used = (($data['Sale'] == 1) ? "<img src='./images/will_phase_out.gif'>" : $used);
			$cashflow = (($data['Cashflow'] == 1) ? "pay_money.gif" :"non_pay_money.gif");
			$discount = (float)(number_format(($data['Price1'] / $data['Price'])*10,1));
			if($discount <= 0){
				$discount = "免費";
			}
			else if($discount >= 10){
				$discount = "";
			}
			else{
				$discount = $discount . "折";
			}
			$name = mb_substr($data['Name'], 0, 12, 'utf8') . ((mb_strlen($data['Name'], 'utf8') > 12) ? "…" : "") ;
			$city = (($data['M1'] > 0 && $data['mobile'] == 1) ? "移動" : $data['City']);
			$city = (($data['M1'] > 0 && $data['mobile'] == 1) ? $data['Area1'] : $data['City']);
			$city = (($data['Status2'] == 2) ? "XX" : $city);

			if($tab == "2" || $tab == "5") $city = "宅配";

			$cashflow = (($data['Cashflow'] == 1) ? "pay_money.gif" :"non_pay_money.gif");
			$hot = "";
			$activity_join = "N.A.";
			$activity_timer = "N.A.";

			if($data['Cashflow'] == 1){
				$hot = "<img src='./images/explosive.gif'>";
				$activity_join = $data['Sold'] . "人購買";
			}
			else if($data['Cashflow'] == 0 && $data['Deliver'] == 0 && $data['coupon_YN'] == 1){
				$hot = "<img src='./images/anihot1_hot.gif'>";
				$activity_join = $data['Coupon'] . "人索取";
			}
			if($data['Activity'] == 1){
//				$data['Price1'] = 0;
				$left = strtotime($data['activity_end'] . " 23:59:59") - time();
				$m = floor($left / 60);
				$h = floor($m / 60);
				$m = $m % 60;
				$d = floor($h / 24);
				$h = $h % 24;
				if($d > 30){
					$activity_timer = (($d > 0) ? $d . "天" : "");
//					$activity_timer .= (($h > 0) ? $h. "時" : "");
//					$activity_timer .= (($m > 0) ? $m . "分" : "");
				}
				else{
					$h = $d * 24 + $h;
					$activity_timer = (($h > 0) ? $h. "時" : "");
					$activity_timer .= (($m > 0) ? $m . "分" : "");
				}
				$activity_join = $data['Joins'] . "人參加";
				$activity = ";background:url('./images/activity.gif'); background-repeat:no-repeat; background-position:590px -2px";
			}
			else{
				$activity = ";background:url('./images/product.gif'); background-repeat:no-repeat; background-position:590px -2px";
			}
			$used = (($data['Used'] == 1) ? "<img src='./images/old_good.gif'>" : "");
			$used = (($data['Sale'] == 1) ? "<img src='./images/will_phase_out.gif'>" : $used);

			if($data['Mode'] == 1){
				if($data['Broadcast'] == 1){
					$cashflow = "broadcast.gif";
					$hot = "<img src='./images/explosive.gif'>";
				}
				else{
					$cashflow = "none1.gif";
					$hot = "";
				}
			}

			$WEB_CONTENT .= <<<EOD
				<tr>
					<td style="text-align:left; background:white">
						<div style="height:242px; overflow:hidden">
							<div>
							<table width="706" height="242" cellpadding="0" cellspacing="0" border=0 style="{$activity}">
								<tr>
									<td width="353" align='center' style="padding:22px; padding-bottom:22px; padding-left:22px;">
										<div style="width:305px; height:198px; overflow:hidden; border:solid 1px gray"><a href="product{$tab}_detail.php?no={$data['No']}&area={$area}&type={$type}&catalog={$catalog}" target="iContent"><img src="./upload/{$data['Photo']}" style="width:305px; height:198px" border='0'></a></div>
									</td>
									<td width="353" align='center' valign='top' aling='center' style="padding-top:22px; padding-bottom:22px; padding-right:22px">
										<table width="303" cellpadding="0" cellspacing="0" border="0" align='center'>
											<tr>
												<td align='left'><div style='text-align:left; color:#F74521; font-weight:bold; line-height:30px; height:30px; overflow:hidden'><a href="product{$tab}_detail.php?no={$data['No']}&area={$area}&type={$type}&catalog={$catalog}" style="color:#F74521" target="iContent">【{$name}】{$discount}</a></div></td>
											</tr>
											<tr>
												<td align='left'><div style="text-align:left; height:40px; overflow:hidden">{$data['Description']}</div></td>
											</tr>
											<tr>
												<td height="52" style="vertical-align:bottom; text-align:right; color:#F74521">$dis</td>
											</tr>
											<tr>
												<td>
													<table cellpadding="0" cellspacing="0">
														<tr style="height:37px">
															<td style="width:101px;text-align:center; background:#F7C74A">原價{$price}</td>
															<td style="width:101px;text-align:center; background:#FFAA73">售{$sell}</td>
															<td style="width:101px;text-align:center; background:#FF7510">省{$save}</td>
														</tr>
														<tr style="height:4px"></tr>
														<tr style="height:37px">
															<td style="width:101px;text-align:center; background:#F7C74A">{$city}</td>
															<td style="width:101px;text-align:center; background:#FFAA73">{$activity_join}</td>
															<td style="width:101px;text-align:center; background:#FF7510">{$activity_timer}</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							</div>
							<div class="label" style="position:relative; z-index:5; top:-226px; left:25px; width:70px; height:70px; text-align:center"><img src="./images/{$cashflow}"></div>
							<div class="label" style="position:relative; z-index:6; top:-158px; left:25px; width:70px; height:70px; text-align:center">{$used}</div>
							<div class="label" style="position:relative; z-index:5; top:-232px; left:260px; width:70px; height:70px; text-align:center">{$hot}</div>
						</div>
					</td>
				</tr>
				<tr>
					<td style=";height:13px; background:#525552"></td>
				</tr>
EOD;
		}
		else{
			break;
		}
	}
	$WEB_CONTENT .= "<tr>";
	$WEB_CONTENT .= "	<td style='padding: 10px;'>";
	$WEB_CONTENT .= "		<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">";
    $WEB_CONTENT .= "           <tr>";
	if($pageno > 1){
		$WEB_CONTENT .= "             <td style='width:74px; height:25px; text-align:center; background-image:url(./images/btn_100_black.jpg1); background-repeat:no-repeat; background-position: center center'>";
		$WEB_CONTENT .= "				<a href=\"javascript:" . (($pageno > 1) ? "setPage(" . ($pageno - 1) . ")" : "void(0)"). ";\" style='color:white; text-decoration:underline'>上一頁</a>";
	}
	else{
		$WEB_CONTENT .= "             <td style='width:74px; height:25px; text-align:left; padding-left:10px;'>&nbsp;";
	}
	$WEB_CONTENT .= "			  </td>";
    $WEB_CONTENT .= "             <td align=\"center\" nowrap><table><tr>";
	for($i=0; $i<$pages; $i++){
		$p = "<div style='width:18px; height:18px; border:solid 0px black; line-height:18px'>" . ($i+1) . "</div>";
		if(($i+1)==$pageno){
			$WEB_CONTENT .= "<td style='text-decoration:underline; width:20px; color:white; text-align:center'>" . $p . "</td>";		
		}
		else{
			$WEB_CONTENT .= "<td onClick=\"javascript:setPage(" . ($i+1) . ");\" style='cursor:pointer; color:white; text-decoration:none; width:20px; text-align:center'>" . $p . "</td>";		
		}
	}
	$WEB_CONTENT .= "			</tr></table></td>";
	if($pageno < $pages){
		$WEB_CONTENT .= "			<td style='width:74px; height:25px; text-align:center; background-image:url(./images/btn_100_black.jpg1); background-repeat:no-repeat; background-position: center center'>";
		$WEB_CONTENT .= "				<a href=\"javascript:" . (($pageno < $pages) ? "setPage(" . ($pageno + 1) . ")" : "void(0)") . ";\" style='color:white; text-decoration:underline'>下一頁</a>";
	}
	else{
		$WEB_CONTENT .= "             <td style='width:74px; height:25px; text-align:left; padding-left:10px;'>&nbsp;";
	}
	$WEB_CONTENT .= "			</td>";
	$WEB_CONTENT .= "			</tr>";
	$WEB_CONTENT .= "		</table>";
	$WEB_CONTENT .= "	</td>";
	$WEB_CONTENT .= "</tr>";
}

$WEB_CONTENT .= "</table>";






include './include/db_close.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link type="text/css" href="style.css" rel="stylesheet" />
<div style="border-right:solid 10px #525252; background:#525552">
<?
if($num == 1){
	echo $WEB_CONTENT;
	echo <<<EOD
		<br>
<br>
<br>
<br>
<br>
<br>
<br>
EOD;
}
else if($num >=2){
	echo $WEB_CONTENT;
}
else{
	echo <<<EOD
		<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
EOD;

}

?>
</div>