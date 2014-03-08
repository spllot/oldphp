<?php
include './include/session.php';
$tab = 5;
include './include/db_open.php';

$area=$_REQUEST['area'];
$type=$_REQUEST['type'];
$catalog=$_REQUEST['catalog'];




$sql = "SELECT *, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(Product.Latitude, Product.Longitude, '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM, (SELECT Level FROM Member WHERE Member.No = Product.Member) AS Level FROM Product WHERE Status = 2 AND Mode = 2 AND Deliver = 0 AND dateClose >= CURRENT_TIMESTAMP";


$sql = "SELECT *, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.Serial=logCoupon.Serial WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude) AS L1, IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude) AS L2, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM, (SELECT Level FROM Member WHERE Member.No = Product.Member) AS Level FROM Product WHERE Status = 2 AND Mode = 2 AND Deliver = 1 AND dateClose >= CURRENT_TIMESTAMP";

$sql .= (($area!="") ? " AND (((SELECT Latitude1 FROM Member WHERE No = Product.Member) = 0 AND Area = '$area') OR ((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0 AND (SELECT Area1 FROM Member WHERE No=Product.Member) = '$area'))" : "");

if($type != ""){
	switch($type){
		case 'all':
			$sql .= " AND Activity = '0'";
			break;
		case 'activity':
			$sql .= " AND Activity = '1'";
			break;
		case 'allnew':
			$c .= " AND Allnew = '1'";
			break;
		case 'transfer':
			$c .= " AND Transfer = '1'";
			break;
		case 'used':
			$sql .= " AND Used = '1'";
			break;
		case 'sale':
			$sql .= " AND Sale = '1'";
			break;
		default:
			$sql .= " AND Type = '$type'";
			break;
	}
}



$sql .= (($catalog!="") ? " AND Catalog = '$catalog'" : "");


$sql .= " ORDER BY " .(($_SESSION['Latitude'] > 0 && $_SESSION['Longitude'] > 0) ? "KM, Level DESC, dateUpdate DESC" : "Level DESC, dateUpdate DESC");
//echo $sql;

$c =  " AND Status = 2 AND Mode = 2 AND Deliver = 1 AND dateClose >= CURRENT_TIMESTAMP";
$c .= (($area!="") ? " AND (((SELECT Latitude1 FROM Member WHERE No = Product.Member) = 0 AND Area = '$area') OR ((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0 AND (SELECT Area1 FROM Member WHERE No=Product.Member) = '$area'))" : "");
if($type != ""){
	switch($type){
		case 'all':
			$c .= " AND Activity = '0'";
			break;
		case 'activity':
			$c .= " AND Activity = '1'";
			break;
		case 'allnew':
			$c .= " AND Allnew = '1'";
			break;
		case 'transfer':
			$c .= " AND Transfer = '1'";
			break;
		case 'used':
			$c .= " AND Used = '1'";
			break;
		case 'sale':
			$c .= " AND Sale = '1'";
			break;
		default:
			$c .= " AND Type = '$type'";
			break;
	}
}
$c .= (($catalog!="") ? " AND Catalog = '$catalog'" : "");
$sql = "SELECT DISTINCT Member, (SELECT No FROM Product WHERE Member=P.Member AND Cashflow=0 {$c} ORDER BY Sort LIMIT 1) AS No FROM Product P WHERE Cashflow = 0" . $c;
$result = mysql_query($sql) or die(mysql_error());
$num = mysql_num_rows($result);
$nos = "";
$i=0;
while($rs=mysql_fetch_array($result)){
	$i++;
	$nos .= $rs['No'];
	$nos .= (($num > $i) ? ",":"");
}
$nos = "";



//echo $sql;
$sql1 = "SELECT *, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.Serial=logCoupon.Serial WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude) AS L1, IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude) AS L2, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM, (SELECT Level FROM Member WHERE Member.No = Product.Member) AS Level FROM Product WHERE 1=1 AND Cashflow=1";
$sql1 .= $c;

$sql2 = "SELECT *, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.Serial=logCoupon.Serial WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude) AS L1, IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude) AS L2, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM, (SELECT Level FROM Member WHERE Member.No = Product.Member) AS Level FROM Product WHERE 1=1 AND Cashflow=0" . (($nos != "") ? " AND No IN ($nos)" : "");
$sql2 .= $c;

$sql = "($sql1) UNION ($sql2)";
$sql .= " ORDER BY " .(($_SESSION['Latitude'] > 0 && $_SESSION['Longitude'] > 0) ? "KM, Level DESC, dateUpdate DESC" : "Level DESC, dateUpdate DESC");


$c =  " AND Status = 2 AND Mode = 2 AND Deliver = 1 AND dateClose >= CURRENT_TIMESTAMP";
$c .= (($area!="") ? " AND (((SELECT Latitude1 FROM Member WHERE No = P.Member) = 0 AND Area = '$area') OR ((SELECT Latitude1 FROM Member WHERE No = P.Member) > 0 AND (SELECT Area1 FROM Member WHERE No=P.Member) = '$area'))" : "");
if($type != ""){
	switch($type){
		case 'all':
			$c .= " AND Activity = '0'";
			break;
		case 'activity':
			$c .= " AND Activity = '1'";
			break;
		case 'used':
			$c .= " AND Used = '1'";
			break;
		case 'allnew':
			$c .= " AND Allnew = '1'";
			break;
		case 'transfer':
			$c .= " AND Transfer = '1'";
			break;
		case 'sale':
			$c .= " AND Sale = '1'";
			break;
		default:
			$c .= " AND Type = '$type'";
			break;
	}
}
$c .= (($catalog!="") ? " AND Catalog = '$catalog'" : "");
//$sql = "SELECT DISTINCT Member, (SELECT No FROM Product WHERE Member=P.Member AND Cashflow=0 {$c} ORDER BY Sort LIMIT 1) AS No FROM Product P WHERE Cashflow = 0" . $c;
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


$c =  " AND Status = 2 AND Mode = 2 AND Deliver = 1 AND dateClose >= CURRENT_TIMESTAMP";
$c .= (($area!="") ? " AND (((SELECT Latitude1 FROM Member WHERE No = Product.Member) = 0 AND Area = '$area') OR ((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0 AND (SELECT Area1 FROM Member WHERE No=Product.Member) = '$area'))" : "");
if($type != ""){
	switch($type){
		case 'all':
			$c .= " AND Activity = '0'";
			break;
		case 'activity':
			$c .= " AND Activity = '1'";
			break;
		case 'allnew':
			$c .= " AND Allnew = '1'";
			break;
		case 'transfer':
			$c .= " AND Transfer = '1'";
			break;
		case 'used':
			$c .= " AND Used = '1'";
			break;
		case 'sale':
			$c .= " AND Sale = '1'";
			break;
		default:
			$c .= " AND Type = '$type'";
			break;
	}
}
$c .= (($catalog!="") ? " AND Catalog = '$catalog'" : "");









$sql2 = "SELECT *, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.Serial=logCoupon.Serial WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude) AS L1, IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude) AS L2, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM, (SELECT Level FROM Member WHERE Member.No = Product.Member) AS Level, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1 FROM Product WHERE 1=1 " . (($nos != "") ? " AND No IN ($nos)" : "");
$sql2 .= $c;

$sql = "$sql2";
$sql .= " ORDER BY " .(($_SESSION['Latitude'] > 0 && $_SESSION['Longitude'] > 0) ? "KM, Level DESC, dateUpdate DESC" : "Level DESC, dateUpdate DESC");









$result = mysql_query($sql) or die(mysql_error());
$num = mysql_num_rows($result);
$pagesize  = 10;
$pages = ceil($num / $pagesize);
$pageno = $_REQUEST['pageno'];
if($pageno == "" || $pageno > $pages){$pageno = 1;}


$WEB_CONTENT = <<<EOD

<script language="javascript">
	function getClock(sec){
		var m = Math.floor(sec/60);
		var s = sec % 60;
		var h = Math.floor(m/60);
		m = m % 60;
		var d = Math.floor(h / 24);
		h = h % 24;
		if(d > 30){
			return ((d > 0)? d + "天" : "");
		}
		if(d < 1 && h < 1 && m < 60){
			return ((m > 0) ? m + "分" : "") + ((s > 0) ? s + "秒" : "");
		}
		else{
			h = d * 24 + h;
			return ((h > 0) ? h + "時" : "") + ((m > 0) ? m + "分" : "");
		}
	}

</script>
	<table cellpadding='0' cellspacing='0' border='0'>
EOD;
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($data = mysql_fetch_array($result)){
			$activity_join = "N.A.";
			$activity_timer = "N.A.";
			if($data['Cashflow'] == 1){
				if($data['Duration'] == 0){
					$activity_timer="不限時";
				}
				else if($data['Druation'] == 1){
					$left = strtotime($data['dateClose']) - time();
					$m = floor($left / 60);
					$h = floor($m / 60);
					$m = $m % 60;
					$d = floor($h / 24);
					$h = $h % 24;
					if($d > 30){
						$activity_timer = (($d > 0) ? $d . "天" : "");
					}
					else{
						$h = $d * 24 + $h;
						$activity_timer = (($h > 0) ? $h. "時" : "");
						$activity_timer .= (($m > 0) ? $m . "分" : "");
					}
					$js_count =<<<EOD
						<script language="javascript">
							var t{$data['No']} = $left;
							function Count{$data['No']}(){
								if(t{$data['No']} > 0){
									t{$data['No']} --;
									$("#timer{$data['No']}").html(getClock(t{$data['No']}));
									setTimeout("Count{$data['No']}()", 1000);
								}
								else{
									$("#timer{$data['No']}").html("已逾期");
								}
							}
							Count{$data['No']}();
						</script>
EOD;
				}
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
				}
				else{
					$h = $d * 24 + $h;
					$activity_timer = (($h > 0) ? $h. "時" : "");
					$activity_timer .= (($m > 0) ? $m . "分" : "");
				}
				$js_count =<<<EOD
					<script language="javascript">
						var t{$data['No']} = $left;
						function Count{$data['No']}(){
							if(t{$data['No']} > 0){
								t{$data['No']} --;
								$("#timer{$data['No']}").html(getClock(t{$data['No']}));
								setTimeout("Count{$data['No']}()", 1000);
							}
							else{
								$("#timer{$data['No']}").html("已逾期");
							}
						}
						Count{$data['No']}();
					</script>
EOD;
				$activity_join = $data['Joins'] . "人參加";
				$activity = ";background:url('./images/activity.gif'); background-repeat:no-repeat; background-position:586px -2px";
			}
			else{
				$activity = ";background:url('./images/product.gif'); background-repeat:no-repeat; background-position:586px -2px";
			}
			$price = "$" . ($data['Price']);
			$sell = "$" . ($data['Price1']);
			$save = "$" . ($data['Price'] - $data['Price1']);
			/*
			if($_SESSION['Latitude'] > 0 && $_SESSION['Longitude']>0){
				$dis =(($data['Latitude']>0 && $data['Longitude'] > 0) ? "距離：" .  (float)(number_format($data['KM'], 1)) . "公里" : "");
			}
			else{
				$dis = "距離：? (請輸入<a href='member_location.php?url=" . urlencode("product5.php?area=$area&catalog=$catalog&type=$type&pageno=$pageno") . "&tab=4' style='text-decoration:underline'>我的位置</a>，再回頁面)</font>";
			}
			*/
			$counts = 0;
			$used = (($data['Used'] == 1) ? "<img src='./images/old_good.gif'>" : "");
			$used = (($data['Sale'] == 1) ? "<img src='./images/will_phase_out.gif'>" : $used);


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
			$city = (($data['M1'] > 0 && $data['mobile'] == 1) ? $data['Area1'] : $data['City']);
			$cashflow = (($data['Cashflow'] == 1) ? "pay_money.gif" :"non_pay_money.gif");
			$hot = "";
			if($data['Cashflow'] == 1){
				$hot = "<img src='./images/explosive.gif' style='width:60px; height:60px'>";
				$activity_join = $data['Sold'] . "人購買";
			}
			else if($data['Cashflow'] == 0 && $data['Deliver'] == 0 && $data['coupon_YN'] == 1){
				$hot = "<img src='./images/anihot1_hot.gif' style='width:60px; height:60px'>";
				$activity_join = $data['Coupon'] . "人索取";
			}
			$city = "- - -";
			$city = "宅配";
			$WEB_CONTENT .= <<<EOD
				<tr>
					<td style="text-align:left; background:white">
						<div style="height:242px; overflow:hidden">
							<div>
							<table width="706" height="242" cellpadding="0" cellspacing="0" border=0 style="{$activity}">
								<tr>
									<td width="353" align='center' style="padding:22px; padding-bottom:22px; padding-left:22px;">
										<div style="width:305px; height:198px; overflow:hidden; border:solid 1px gray"><a href="product5_detail.php?no={$data['No']}&area={$area}&type={$type}&catalog={$catalog}"><img src="./upload/{$data['Photo']}" style="width:305px; height:198px" border='0'></a></div>
									</td>
									<td width="353" align='center' valign='top' aling='center' style="padding-top:22px; padding-bottom:22px; padding-right:22px">
										<table width="303" cellpadding="0" cellspacing="0" border="0" align='center'>
											<tr>
												<td align='left'><div style='position:relative; left:-10px; text-align:left; color:#F74521; font-weight:bold; line-height:30px; height:30px; overflow:hidden'>【<a href="product5_detail.php?no={$data['No']}&area={$area}&type={$type}&catalog={$catalog}" style="color:#F74521">{$name}</a>】{$discount}</div></td>
											</tr>
											<tr>
												<td align='left'><div style="text-align:left; height:40px; overflow:hidden">{$data['Description']}</div></td>
											</tr>
											<tr>
												<td height="52" style="vertical-align:bottom; text-align:right; color:#F74521">
												<div style="float:right">{$dis}</div>
												<div style='position:relative; left:-10px; text-align:left; color:green'>【<a href="member_product.php?no={$data['Member']}" style="; color:green" target="_blank">賣家其它商品/活動</a>】</div>
												</td>
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
															<td style="width:101px;text-align:center; background:#FF7510"><div id="timer{$data['No']}">{$activity_timer}</div></td>
														</tr>
													</table>{$js_count}
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

include 'search.php';

$usefor = basename($_SERVER['PHP_SELF']);
$usefor = strtoupper(substr($usefor, 0, 8));
$catalog = $_REQUEST['catalog'];
include 'template0.php';
?>
<script language="javascript">
//parent.iAD.location.href="ad.php?usefor=<?=$usefor?>&catalog=<?=$catalog?>";
</script>
<script language="javascript">
parent.setTab(5);
</script>