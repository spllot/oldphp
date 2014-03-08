<?php
include './include/session.php';
$tab = 4;
include './include/db_open.php';

$area=$_REQUEST['area'];
$type=$_REQUEST['type'];
$catalog=$_REQUEST['catalog'];
$catalog2=$_REQUEST['catalog2'];
$catalog3=$_REQUEST['catalog3'];



/*
$sql = "SELECT *, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(Product.Latitude, Product.Longitude, '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM, (SELECT Level FROM Member WHERE Member.No = Product.Member) AS Level FROM Product WHERE Status = 2 AND Mode = 2 AND Deliver = 0 AND dateClose >= CURRENT_TIMESTAMP";


$sql = "SELECT DISTINCT Member, (SELECT No FROM Product WHERE Member=P.Member ORDER BY Sort LIMIT 1) AS No FROM Product P WHERE Status = 2 AND Mode = 2 AND Deliver = 0 AND dateClose >= CURRENT_TIMESTAMP AND Cashflow = 0";
$result = mysql_query($sql) or die(mysql_error());
$num = mysql_num_rows($result);
$nos = "";
$i=0;
while($rs=mysql_fetch_array($result)){
	$i++;
	$nos .= $rs['No'];
	$nos .= (($num > $i) ? ",":"");
}




$sql = "SELECT *, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.Serial=logCoupon.Serial WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF(Product.Activity = 0 AND (SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF(Product.Activity = 0 AND (SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM, (SELECT Level FROM Member WHERE Member.No = Product.Member) AS Level FROM Product INNER JOIN Member ON Product.Member=Member.No WHERE Status = 2 AND Mode = 2 AND Deliver = 0 AND dateClose >= CURRENT_TIMESTAMP";
//$sql .= " AND (Cashflow = 1" . (($nos != "") ? " OR (Cashflow=0 AND No IN ($nos))" : "") . ")";
$sql .= (($area!="") ? " AND ((Latitude1 = 0 AND Area = '$area') OR (Latitude1 > 0 AND Area1 = '$area'))" : "");
if($type != ""){
	switch($type){
		case 'all':
			$sql .= " AND Activity = '0'";
			break;
		case 'activity':
			$sql .= " AND Activity = '1'";
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
//echo $sql;

*/





$c =  " AND Status = 2 AND Mode = 2 AND Deliver = 0 AND dateClose >= CURRENT_TIMESTAMP";
$c .= (($area!="") ? " AND (((SELECT Latitude1 FROM Member WHERE No = P.Member) = 0 AND Area = '$area') OR ((SELECT Latitude1 FROM Member WHERE No = P.Member) > 0 AND (SELECT Area1 FROM Member WHERE No=P.Member) = '$area'))" : "");
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
			$c .= " AND Transport = '1'";
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
//$sql = "SELECT DISTINCT Member, (SELECT No FROM Product WHERE Member=P.Member AND Cashflow=0 {$c} ORDER BY Sort LIMIT 1) AS No FROM Product P WHERE Cashflow = 0" . $c;

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



//echo $sql;
//$sql1 = "SELECT *, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.Serial=logCoupon.Serial WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF(Product.Activity = 0 AND (SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF(Product.Activity = 0 AND (SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM, (SELECT Level FROM Member WHERE Member.No = Product.Member) AS Level FROM Product WHERE 1=1 AND Cashflow=1";
//$sql1 .= $c;

//$sql2 = "SELECT *, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.Serial=logCoupon.Serial WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF(Product.Activity = 0 AND (SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF(Product.Activity = 0 AND (SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM, (SELECT Level FROM Member WHERE Member.No = Product.Member) AS Level FROM Product WHERE 1=1 AND Cashflow=0" . (($nos != "") ? " AND No IN ($nos)" : "");
//$sql2 .= $c;

//$sql = "($sql1) UNION ($sql2)";
//$sql .= " ORDER BY " .(($_SESSION['Latitude'] > 0 && $_SESSION['Longitude'] > 0) ? "KM, Level DESC, dateUpdate DESC" : "Level DESC, dateUpdate DESC");


$sql2 = "SELECT *, (SELECT Status2 FROM Member WHERE No=Product.Member) AS Status2, (SELECT Flag FROM Member WHERE No=Product.Member) AS Flag, (SELECT Intime FROM Member WHERE No=Product.Member) AS Intime, (SELECT taxi_addr FROM Member WHERE No=Product.Member) AS taxi_addr, (SELECT taxi_dest FROM Member WHERE No=Product.Member) as taxi_dest, (SELECT Name FROM Catalog WHERE No=Product.Type) AS TName, (SELECT Name FROM Catalog WHERE No=Product.Catalog) AS CName, (SELECT Nick FROM Member WHERE No=Product.Member) AS Nick, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.No=logCoupon.couponNo WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0 AND Product.mobile > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude) AS L1, IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0 AND Product.mobile > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude) AS L2, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0 AND Product.mobile > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0 AND Product.mobile > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM, (SELECT Level FROM Member WHERE Member.No = Product.Member) AS Level, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1 FROM Product WHERE 1=1 " . (($nos != "") ? " AND No IN ($nos)" : "");
$sql2 .= $c;

$sql = "$sql2";
$sql .= " ORDER BY " .(($_SESSION['Latitude'] > 0 && $_SESSION['Longitude'] > 0) ? "KM, Level DESC, dateUpdate DESC" : "Level DESC, dateUpdate DESC");

















$result = mysql_query($sql) or die(mysql_error());
$num = mysql_num_rows($result);
$pagesize  = 20;
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
	<table cellpadding='0' cellspacing='0' border='0' idd="tbl1">
EOD;
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($data = mysql_fetch_array($result)){
			$activity_join = "已售 --";
			$activity_timer = "--時--分--秒";

			if($data['Cashflow'] == 1){
				$close = date('Y-m-d', strtotime($data['dateExpire'] . "-" . $data['daysBeforeReserve'] . " day")) . "23:59:59";
				$left = strtotime($close) - time();
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
			if($data['Activity'] == 1){
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
				$activity = ";background:url('./images/activity.gif'); background-repeat:no-repeat; background-position:558px 23px";
			}
			else if($data['Transport'] == 1){
				$activity = ";background:url('./images/transoprt.gif'); background-repeat:no-repeat; background-position:558px 23px";
			}
			else if($data['hr'] == 1){
				$activity = ";background:url('./images/hr.gif'); background-repeat:no-repeat; background-position:558px 23px";
			}
			else if($data['event'] == 1){
				$activity = ";background:url('./images/activity2.gif'); background-repeat:no-repeat; background-position:558px 23px";
			}
			else if($data['hr'] == 1){
				$activity = ";background:url('./images/hr.gif'); background-repeat:no-repeat; background-position:558px 23px";
			}
			else if($data['event'] == 1){
				$activity = ";background:url('./images/activity2.gif'); background-repeat:no-repeat; background-position:558px 23px";
			}
			else if(strpos($data['CName'], "展演") > -1){
				$activity = ";background:url('./images/show.gif'); background-repeat:no-repeat; background-position:558px 23px";
			}
			else{
				$activity = ";background:url('./images/product.gif'); background-repeat:no-repeat; background-position:558px 23px";
			}

			
			$price = "$" . number_format($data['Price']);
			$sell = "$" . number_format($data['Price1']);
			$save = "$" . number_format($data['Price'] - $data['Price1']);
			if($_SESSION['Latitude'] > 0 && $_SESSION['Longitude']>0){
				$dis =(($data['Latitude']>0 && $data['Longitude'] > 0) ? "-距離：" .  (float)(number_format($data['KM'], 1)) . "公里" : "");
				$dis =(($data['L1']>0 && $data['L2'] > 0) ? "-距離：" .  (float)(number_format($data['KM'], 1)) . "公里" : "");
			}
			else{
				$dis = "-距離：<a id='dis{$data['No']}' style='color:#FFFFFF; text-decoration:underline'; href='javascript:void(0);' onMouseOver='showDis();setDisPos({$data['No']}); '> ? </a>";
			}
			$counts = 0;
			
			$used = "";
			if($data['Used'] == 1){$used = "<img src='./images/old_good.gif'>";}
			if($data['Sale'] == 1){$used = "<img src='./images/will_phase_out.gif'>";}
//			if($data['Allnew'] == 1){$used = "<img src='./images/allnew.gif'>";}

			if($data['Transport'] == 1){
				$discount = (($data['taxi_discount']) ? "{$data['taxi_discount']}折":"");
			}
			else{
				if($data['price_mode'] == 1){
					$discount = "<span style='color:red; font-size:40px; font-weight:bold; text-shadow: 0px 0px 6px rgba(255,255,255,0.7);'>- -</span><span style='color:red; font-size:16px; font-weight:bold; text-shadow: 0px 0px 6px rgba(255,255,255,0.7);'>折</span>";
				}
				else{
					$discount = (float)(number_format(($data['Price1'] / $data['Price'])*10,1));
					if($discount <= 0){
						$discount = "<span style='color:red; font-size:30px; font-weight:bold; text-shadow: 0px 0px 6px rgba(255,255,255,0.7);'>免費</span>";
					}
					else if($discount >= 10){
						$discount = "";
					}
					else{
						$d = explode(".", strval($discount));
						$discount = "<span style='color:red; font-size:40px; font-weight:bold; text-shadow: 0px 0px 6px rgba(255,255,255,0.7);'>". $d[0] . "</span>";
						if(sizeof($d) > 1){
							$discount .= "<span style='color:red; font-size:24px; font-weight:bold; text-shadow: 0px 0px 6px rgba(255,255,255,0.7);'>.". $d[1] . "</span>";
						}
						$discount = $discount . "<span style='color:red; font-size:16px; text-shadow: 0px 0px 6px rgba(255,255,255,0.7);'>折</span>";
					}
				}
			}
			$name = mb_substr($data['Name'], 0, 12, 'utf8') . ((mb_strlen($data['Name'], 'utf8') > 12) ? "…" : "") ;
			$seller = mb_substr($data['Nick'], 0, 4, 'utf8') . ((mb_strlen($data['Nick'], 'utf8') > 4) ? "…" : "") ;
			
			$city = (($data['M1'] > 0 && $data['mobile'] == 1) ? $data['Area1'] : $data['City']);
//			$city = (($data['Status2'] == 2) ? "XX" : $city);

			if($city == ""){
				if($data['mobile'] == 1){
					$city="XX";
				}
				else{
					$city="其它區域";
				}
			}


			$cashflow = (($data['Cashflow'] == 1) ? "pay_money.gif" : (($data['welfare'] == 1) ? "welfare.gif" : "non_pay_money.gif") );
			$hot = "";
			if($data['Cashflow'] == 1){
				$hot = "<img src='./images/explosive.gif' style='width:60px; height:60px'>";
				$activity_join = $data['Sold'] . "人購買";
			}
			else if($data['Cashflow'] == 0 && $data['Deliver'] == 0 && $data['coupon_YN'] == 1){
				$hot = "<img src='./images/anihot1_hot.gif' style='width:60px; height:60px'>";
				$activity_join = $data['Coupon'] . "人索取";
			}
			$hot = "<div style='width:213px; height:59px; border:solid 0px gray'>";
			if($data['coupon_YN'] == 1)
				$hot .= "<div style='float:right; width:63px; height:59px; overflow:hidden'><img src='./images/dribbbleA.gif'></div><div style='width:8px; float:right'></div>";
			
			if($data['M1'] > 0 && $data['mobile'] == 1)
				$hot .= "<div style='float:right; width:63px; height:59px; overflow:hidden'><img src='./images/dribbbleB.gif'></div><div style='width:8px; float:right'></div>";
			if($data['Flag'] > 0){
				$hot .= "<div style='float:right; width:63px; height:59px; overflow:hidden'><img src='./images/dribbbleC.gif'></div><div style='width:8px; float:right'></div>";
			}
			else if($data['event'] == 1){
				$now = time();
				$start = strtotime($data['event_date'] . " " . $data['event_start'] . ":00");
				$end = strtotime($data['event_date'] . " " . $data['event_end'] . ":00");
				$interval  = abs($start - $now);
				$minutes1   = round($interval / 60);
				$interval  = abs($end - $now);
				$minutes2   = round($interval / 60);
				if($minutes1 < 120 && $minutes2 > 0){
					$hot .= "<div style='float:right; width:63px; height:59px; overflow:hidden'><img src='./images/dribbbleC.gif'></div><div style='width:8px; float:right'></div>";
				}
			}
			
			$hot .= "</div>";

			$detail = <<<EOD
													<table cellpadding="0" cellspacing="0" idd="tbl2">
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
EOD;
			if($data['Transport'] == 1){
				$empty = (($data['Empty'] == 1) ? "<span style='font-size:30px'>空車</span>" : "<span style='font-size:24px'>載運中</span>");
				$sql = "SELECT IFNULL(COUNT(*), 0) AS Comments FROM logComment WHERE transactionNo = '{$data['No']}'";
				$r1 = mysql_query($sql) or die(mysql_error());
				list($comments) = mysql_fetch_row($r1);
			$detail = <<<EOD
				<table cellpadding="0" cellspacing="0" idd="tbl3">
					<tr style="height:37px">
						<td style="width:101px;text-align:center; background:#F7C74A">{$empty}</td>
						<td style="width:101px;text-align:center; background:#FFAA73">行車{$data['taxi_exp']}年</td>
						<td style="width:101px;text-align:center; background:#FF7510">{$comments}則評論</td>
					</tr>
					<tr style="height:4px"></tr>
					<tr style="height:37px">
						<td style="width:101px;text-align:center; background:#F7C74A">即時消費商品</td>
						<td style="width:101px;text-align:center; background:#FFAA73">車齡{$data['taxi_age']}年</td>
						<td style="width:101px;text-align:center; background:#FF7510">車牌{$data['taxi_plate']}</td>
					</tr>
				</table>{$js_count}
EOD;
			}
			
			$price_decoration = "line-through";
			if($data['Price'] == $data['Price1']){
				$price = "- -";
				$save = "- -";
				$price_decoration="none";
				$discount = "<span style='color:red; font-size:40px; font-weight:bold; text-shadow: 0px 0px 6px rgba(255,255,255,0.7);'>--</span> <span style='color:red; font-size:30px; font-weight:bold; text-shadow: 0px 0px 6px rgba(255,255,255,0.7);'>折</span>";
			}

			if($data['hr'] == 1){
				if($data['employer'] == 1){
					$discount = "<span style='font-size:30px; color:red'>徵求</span>";
				}
				else{
					$discount = "<span style='font-size:30px; color:red'>待徵</span>";
				}
				$price_info = "<div style='padding-top:2px;border:0px solid red;color:#FFFFFF; text-align:center; font-size:24px; line-height:50px; width:150px'>"  . $data['price_info'] . "</div>";
			}
			else if($data['Transport'] == 1){
				$discount = "<span style='color:red; font-weight:bold; text-shadow: 0px 0px 6px rgba(255,255,255,0.7);'>{$empty}</span>";
				$price_info = "<div style='padding-top:5px;color:#FFFFFF; text-align:center; font-size:24px; line-height:50px'>"  . $data['taxi_discount'] . "</div>";				
			}
			else if($data['price_mode'] == 1){
				$price_info = "<div style='padding-top:2px;border:0px solid red;color:#FFFFFF; text-align:center; font-size:24px; line-height:50px; width:150px'>"  . $data['price_info'] . "</div>";
			}
			else{
			if($data['Price1'] > 9999){
				$price_info = <<<EOD
								<table idd="tbl4">
									<tr>
										<td style="color:white; font-size:24px;" nowrap>{$sell}</td>
									</tr>
								</table>
EOD;
			}
			else{
				$price_info = <<<EOD
							<table idd="tbl5" border=0 style='border:0px solid blue;margin-top:1px;'>
								<tr>
									<td rowspan="2" style="color:white; font-size:24px; width:80px" nowrap>{$sell}</td>
									<td style="color:white; font-size:14px; text-decoration: {$price_decoration}" nowrap>原價 {$price}</td>
								</tr>
								<tr>
									<td style="color:white; font-size:14px;" nowrap>節省 {$save}</td>
								</tr>
							</table>
EOD;
			}
				}


			$price_info = <<<EOD
						<table idd="tbl6" style='border:0px solid green'>
							<td style="border:0px solid red;width:33px; height:54px; text-align:center"><img src="./images/price.png"></td>
							<td><div style="border:0px solid red;width:165px; height:54px; overflow:hidden">{$price_info}</div></td>
						</table>
EOD;
			$sold_info = <<<EOD
												<table style="width:300px" cellpadding="0" cellspacing="0" border=0 idd="tbl7">
													<tr>
														<td style="width:27px; height:22px; text-align:right; padding-right:2px"><img src="./images/sold.png"></td>
														<td style="width:60px; height:22px; line-height:22px; color:black">{$activity_join}</td>
														<td style="width:22px; height:22px; text-align:right; padding-right:2px"><img src="./images/clock.png"></td>
														<td style="width:100px; height:22px; line-height:22px; color:black"><div id="timer{$data['No']}">{$activity_timer}</div></td>
													</tr>
												</table>
EOD;
			if($data['event'] == 1){
				$empty_status = $data['event_date'] . (($data['event_start'] != "") ? ",&nbsp;&nbsp;" . $data['event_start'] : "") . (($data['event_end'] != "") ? "～" . $data['event_end'] : "");
				$sold_info = <<<EOD
												<table style="width:300px" cellpadding="0" cellspacing="0" border=0 idd="tbl7">
													<tr>
															<td style="width:27px; height:22px; text-align:right; padding-right:2px; padding-left:10px"><img src="./images/sold.png"></td>
															<td style="width:263px; height:22px; line-height:22px; color:black"><div style="width:263px; height:22px; overflow:hidden; color:black; font-size:14px">活動時間：{$empty_status}</div></td>
														</tr>
												</table>
EOD;
			
			
			}
			if($data['Transport'] == 1){
				switch($data['Empty']){
					case 1:
						$empty_status = "";
						break;
					case 2:
						$empty_status = "<span style='font-size:13px'>不開放共乘 或 共乘人數已額滿</span>";
						break;
					case 3:
						$empty_status = "<span style='font-size:13px'>共乘目標：" . $data['taxi_addr'] . $data['taxi_dest'] . "</span>";
						break;
					default:
						$empty_status = "";
				}
				$sold_info = <<<EOD
													<table style="width:300px" cellpadding="0" cellspacing="0" border=0 idd="tbl8">
														<tr>
															<td style="width:27px; height:22px; text-align:right; padding-right:2px; padding-left:10px"><img src="./images/sold.png"></td>
															<td style="width:263px; height:22px; line-height:22px; color:black"><div style="width:263px; height:22px; overflow:hidden; color:red">{$empty_status}</div></td>
														</tr>
													</table>
EOD;


			
			}
			if($data['hr'] == 1 || $data['event'] == 1 || $data['Transport'] == 1){
				$data['TName'] = "服務類型";
			}
			$WEB_CONTENT .= <<<EOD
				<tr>
					<td style="text-align:left; background:white">
						<div style="width:676px; height:279px; overflow:hidden; background:url('./images/bg_product4.png');">
							<div style="width:676px; height:279px; overflow:hidden">
								<table width="676" height="279" cellpadding="0" cellspacing="0" border=0 style="{$activity}" idd="tbl9">
									<tr>
										<td style="width:348px; height:22px; color:white">
											<table cellpadding="0" cellspacing="0" border=0  idd="tbl10">
												<tr>
													<td style="padding-left:15px; height:22px; color:white; text-align:center; vertical-align:bottom; font-size:11pt"><div style="width:160px; height:18px; overflow:hidden; text-align:center; padding-top:4px;">{$data['TName']}</div></td>
													<td style="padding-left:18px; height:22px; color:white; text-align:center; vertical-align:bottom; font-size:11pt"><div style="width:160px; height:18px; overflow:hidden; text-align:center; padding-top:4px;">{$city}{$dis}</div></td>
												</tr>
											</table>
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td style="padding-left:26px; padding-bottom:14px">
										<div style="width:305px; height:198px; overflow:hidden; border:solid 0px gray"><a href="product4_detail.php?no={$data['No']}&area={$area}&type={$type}&catalog={$catalog}"><img src="./upload/{$data['Photo']}" style="width:305px; height:198px" border='0'></a></div>
										</td>
										<td style="vertical-align:top; padding-left:5px" valign="top" >
											<div style="width:218px; height:28px; margin-top:15px; border:solid 0px gray; overflow:hidden"><a href="product4_detail.php?no={$data['No']}&area={$area}&type={$type}&catalog={$catalog}" style="color:#000000; font-size:22px; font-weight:bold" title="{$data['Name']}：{$data['Description']}">{$name}</a></div>
											<div style="width:270px; height:36px; line-height:18px; margin-top:4px; border:solid 0px gray; overflow:hidden; font-size:14px">{$data['Description']}</div>
											<div style="width:310px; height:34px; margin-top:2px; border:solid 1px transparent; overflow:hidden; text-align:center; color:blue; font-size:13px; line-height:30px"></div>
											<div style="width:280px; height:20px; margin-top:4px; border:solid 0px gray; overflow:hidden; text-align:center"><a href="member_product.php?no={$data['Member']}" style="color:#FFFFFF; font-size:14px" target="_blank" title="商家：{$data['Nick']}">商家 [{$seller}] 其它服務</a></div>
											<div style="width:300px; height:58px; margin-top:12px; border:solid 1px transparent; overflow:hidden">
												<table style="width:300px" cellpadding="0" cellspacing="0" border=0 idd="tbl11">
													<tr>
														<td><div style='height:54px; width:210px; overflow:hidden'>{$price_info}</div></td>
														<td style="width:90px; height:54px; text-align:center; padding-right:3px">{$discount}</td>
													</tr>
												</table>
											</div>
											<div style="width:300px; height:22px; margin-top:5px; border:solid 1px transparent; overflow:hidden">{$sold_info}</div>
										</td>
									</tr>
								</table>{$js_count}
							</div>
			
							<div class="label" style="position:relative; z-index:5; top:-244px; left:15px; width:70px; height:70px; text-align:center"><img src="./images/{$cashflow}"></div>
							<div class="label" style="position:relative; z-index:6; top:-167px; left:15px; width:70px; height:70px; text-align:center">{$used}</div>
							<div class="label" style="position:relative; z-index:5; top:-220px; left:107px; width:70px; height:70px; text-align:center">{$hot}</div>

							<!--table width="706" height="242" cellpadding="0" cellspacing="0" border=0 style="{$activity}">
								<tr>
									<td width="353" align='center' style="padding:22px; padding-bottom:22px; padding-left:22px;">
										<div style="width:305px; height:198px; overflow:hidden; border:solid 1px gray"><a href="product4_detail.php?no={$data['No']}&area={$area}&type={$type}&catalog={$catalog}"><img src="./upload/{$data['Photo']}" style="width:305px; height:198px" border='0'></a></div>
									</td>
									<td width="353" align='center' valign='top' aling='center' style="padding-top:22px; padding-bottom:22px; padding-right:22px" idd="tbl2">
										<table width="303" cellpadding="0" cellspacing="0" border="0" align='center'>
											<tr>
												<td align='left'><div style='position:relative; left:-10px; text-align:left; color:#F74521; font-weight:bold; line-height:30px; height:30px; overflow:hidden'>【<a href="product4_detail.php?no={$data['No']}&area={$area}&type={$type}&catalog={$catalog}" style="color:#F74521">{$name}</a>】{$discount}</div></td>
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
												<td>{$detail}</td>
											</tr>
										</table>
									</td>
								</tr>
							</table-->

						</div>
					</td>
				</tr>
				<tr>
					<td style=";height:13px; background:#FFFFFF"></td>
				</tr>
EOD;
		}
		else{
			break;
		}
	}
	$WEB_CONTENT .= "<tr>";
	$WEB_CONTENT .= "	<td style='padding-left:5px; padding-right:10px; text-align:right'><br><a href='#top' style='color:black' onClick=\"window.location.href='#top'\"><img src='./images/asc.gif' border=0>Top</a></td></tr>";
//	$WEB_CONTENT .= "	<td style='padding-left:5px; padding-right:10px; text-align:right'><br><a href='product4.php?type=$type&catalog=$catalog&catalog2=$catalog2&catalog3=$catalog3&pageno=$pageno' style='color:black'><img src='./images/asc.gif' border=0>Top</a></td></tr>";
	$WEB_CONTENT .= "<tr>";
	$WEB_CONTENT .= "	<td style='padding-left:5px; padding-right:10px' idd='tbl3'>";
	$WEB_CONTENT .= "		<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" align='center' style=\"height:40px; background:url('./images/bg_pagger.png'); background-repeat:repeat-x\">";
    $WEB_CONTENT .= "           <tr>";
	if($pageno > 1){
		$WEB_CONTENT .= "             <td style='width:74px; height:40px; text-align:center; background-image:url(./images/btn_100_black.jpg1); background-repeat:no-repeat; background-position: center center'>";
		$WEB_CONTENT .= "				<a href=\"javascript:" . (($pageno > 1) ? "setPage(" . ($pageno - 1) . ")" : "void(0)"). ";\" style='color:black; text-decoration:underline'>上一頁</a>";
	}
	else{
		$WEB_CONTENT .= "             <td style='width:74px; height:40px; text-align:left; padding-left:10px;'>&nbsp;";
	}
	$WEB_CONTENT .= "			  </td>";
    $WEB_CONTENT .= "             <td align=\"center\" nowrap><table idd='tbl4'><tr>";
	for($i=0; $i<$pages; $i++){
		$p = "<div style='width:18px; height:18px; border:solid 0px black; line-height:18px'>" . ($i+1) . "</div>";
		if(($i+1)==$pageno){
			$WEB_CONTENT .= "<td style='text-decoration:none; width:20px; color:; text-align:center'>" . $p . "</td>";		
		}
		else{
			$WEB_CONTENT .= "<td onClick=\"javascript:setPage(" . ($i+1) . ");\" style='cursor:pointer; color:; text-decoration:underline; width:20px; text-align:center'>" . $p . "</td>";		
		}
	}
	$WEB_CONTENT .= "			</tr></table></td>";
	if($pageno < $pages){
		$WEB_CONTENT .= "			<td style='width:74px; height:40px; text-align:center; background-image:url(./images/btn_100_black.jpg1); background-repeat:no-repeat; background-position: center center'>";
		$WEB_CONTENT .= "				<a href=\"javascript:" . (($pageno < $pages) ? "setPage(" . ($pageno + 1) . ")" : "void(0)") . ";\" style='color:black; text-decoration:underline'>下一頁</a>";
	}
	else{
		$WEB_CONTENT .= "             <td style='width:74px; height:40px; text-align:left; padding-left:10px;'>&nbsp;";
	}
	$WEB_CONTENT .= "			</td>";
	$WEB_CONTENT .= "			</tr>";
	$WEB_CONTENT .= "		</table>";
	$WEB_CONTENT .= "	</td>";
	$WEB_CONTENT .= "</tr>";
}

$WEB_CONTENT .= "</table>";
$url = urlencode("product4.php?area=$area&catalog=$catalog&type=$type&pageno=$pageno");
$WEB_CONTENT .= <<<EOD
<div id="dis" style="position:absolute;display:none; width:120px; padding:0px; text-align:center; background:#ffffcc; border-radius: 8px; border:solid 5px gray; z-index:99; padding-left:5px; padding-right:5px; padding-bottom:5px" onMouseOver="showDis();" onMouseOut="hideDis();">
&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:red">距離： ? </span><br>
<a href='member_location.php?url=$url&tab=4' style='text-decoration:underline'>請輸入我的位置<br>，再回頁面！</a></font>
</div>
EOD;

include './include/db_close.php';

include 'search.php';

$usefor = basename($_SERVER['PHP_SELF']);
$usefor = strtoupper(substr($usefor, 0, 8));
$catalog = $_REQUEST['catalog'];
include 'template0.php';
?>

<script language="javascript">
function setPage(x){
	parent.setPage(x);
}
function setDisPos(x){
	var p = $("#dis"+x).position();
	$("#dis").css({"top": p.top-3, "left": p.left-$("#dis").width()+25});

}
function showDis(){
	$("#dis").show();
}
function hideDis(){
	$("#dis").hide();
}
//parent.iAD.location.href="ad.php?usefor=<?=$usefor?>&catalog=<?=$catalog?>";
</script>
<script language="javascript">
parent.setTab(4);
</script>