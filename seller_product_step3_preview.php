<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
require_once getcwd() . '/class/facebook.php';

if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.close();");
	exit;
}

function fetchUrl($url){
//	echo $url . "<br>";
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_TIMEOUT, 20);
     $retData = curl_exec($ch);
     curl_close($ch); 
 
     return $retData;
}
$data['no'] = $_REQUEST['no'];
$data['Duration'] = $_REQUEST['duration'];
$data['Status'] = $_REQUEST['status'];
$data['Referral'] = $_REQUEST['referral'];
$data['Editor'] = $_REQUEST['editor'];
$data['Agree'] = $_REQUEST['agree'];
$data['Deliver'] = $_REQUEST['deliver'];
$data['Area'] = $_REQUEST['area'];
$data['Catalog'] = $_REQUEST['catalog'];
$data['Type'] = $_REQUEST['type'];
$data['daysBeforeReserve'] = $_REQUEST['daysbeforereserve'];
$data['Price'] = $_REQUEST['price'];
$data['Price1'] = $_REQUEST['price1'];
$data['Discount'] = $_REQUEST['discount'];
$data['daysOnSale'] = $_REQUEST['daysonsale'];
$data['Quota'] = $_REQUEST['quota'];
$data['Amount'] = $_REQUEST['amount'];
$data['Name'] = $_REQUEST['name'];
$data['Description'] = $_REQUEST['description'];
$data['Photo'] = $_REQUEST['pic'];
$data['dateValidate'] = $_REQUEST['datevalidate'];
$data['dateExpire'] = $_REQUEST['dateexpire'];
$data['Hours'] = $_REQUEST['hours'];
$data['Restrict'] = $_REQUEST['restrict'];
$data['Use2'] = $_REQUEST['use2'];
$data['Buy3'] = $_REQUEST['buy3'];
$data['Use3'] = $_REQUEST['use3'];
$data['Buy4'] = $_REQUEST['buy4'];
$data['Memo'] = $_REQUEST['memo'];
$data['Slide'] = $_REQUEST['slide'];
$data['Slide2'] = $_REQUEST['slide2'];
$data['Slide3'] = $_REQUEST['slide3'];
$data['Slide4'] = $_REQUEST['slide4'];
$data['Special1'] = $_REQUEST['special1'];
$data['Special2'] = $_REQUEST['special2'];
$data['Special3'] = $_REQUEST['special3'];
$data['Special4'] = $_REQUEST['special4'];
$data['Special5'] = $_REQUEST['special5'];
$data['Seller'] = $_REQUEST['seller'];
$data['Url'] = $_REQUEST['url'];
$data['Phone'] = $_REQUEST['phone'];
$data['Receipt'] = $_REQUEST['receipt'];
$data['Intro'] = stripslashes($_REQUEST['intro']);
$data['About'] = $_REQUEST['about'];
$data['openHours'] = $_REQUEST['openhours'];
$data['Address'] = $_REQUEST['address'];
$data['Map'] = $_REQUEST['map'];
$data['isDonate'] = $_REQUEST['isdonate'];
$data['Donate'] = $_REQUEST['donate'];
$l = explode(",", str_replace(array("(", ")", " "), "", $_REQUEST['latitude']));
$data['Latitude'] = $l[0];
$data['Longitude'] = $l[1];
$data['Cashflow'] = $_REQUEST['cashflow'];
$data['Activity'] = $_REQUEST['activity'];

$data['activity_page'] = $_REQUEST['activity_page'];
$data['activity_start'] = $_REQUEST['activity_start'];
$data['activity_end'] = $_REQUEST['activity_end'];
$data['activity_ann'] = $_REQUEST['activity_ann'];
$data['activity_min'] = $_REQUEST['activity_min'];
$data['activity_per'] = $_REQUEST['activity_per'];
$data['activity_draw'] = $_REQUEST['activity_draw'];
$data['activity_info'] = $_REQUEST['activity_info'];
$data['activity_holder'] = $_REQUEST['activity_holder'];
$data['activity_email'] = $_REQUEST['activity_email'];
$data['activity_quota'] = $_REQUEST['activity_quota'];


$data['Transport'] = $_REQUEST['transport'];
$data['welfare'] = $_REQUEST['welfare'];
$data['price_info'] = $_REQUEST['price_info'];
$data['price_mode'] = $_REQUEST['price_mode'];
$data['taxi_exp'] = $_REQUEST['taxi_exp'];
$data['taxi_sex'] = $_REQUEST['taxi_sex'];
$data['taxi_age'] = $_REQUEST['taxi_age'];
$data['taxi_plate'] = $_REQUEST['taxi_plate'];
$data['taxi_company'] = $_REQUEST['taxi_company'];
$data['taxi_discount'] = $_REQUEST['taxi_discount'];
$data['Allnew'] = $_REQUEST['Allnew'];

$data['hr'] = $_REQUEST['hr'];
$data['event'] = $_REQUEST['event'];
$data['event_date'] = $_REQUEST['event_date'];
$data['event_start'] = $_REQUEST['event_start'];
$data['event_end'] = $_REQUEST['event_end'];








//print_r($data);
//print_r($_REQUEST);
switch($data['Restrict']){
	case 2:
		$data['maxBuy'] = 1;
		$data['maxUse'] = $data['Use2'];
		break;
	case 3:
		$data['maxBuy'] = $data['Buy3'];
		$data['maxUse'] = $data['Use3'];
		break;
	case 4:
		$data['maxBuy'] = $data['Buy4'];
		break;
	default:
		$data['maxBuy'] = 1;
		$data['maxUse'] = 1;
		break;
}
switch($data['Deliver']){
	case 1:
		$tab = 5;
		break;
	case 0:
		$tab = 4;
		break;
}
include './include/db_open.php';
$no = $_REQUEST['no'];

$holder = "商家";
if($data['Activity'] == 1){
	$holder="商家";
}


$sql = "SELECT *, (SELECT Name FROM Catalog WHERE Catalog.No = Member.Area1) AS Area1, IFNULL((SELECT SUM(Quality) FROM logRating WHERE Owner = Member.No), 0) as Rate FROM Member WHERE Member.No = '" . $_SESSION['member']['No'] . "'";
$result = mysql_query($sql) or die(mysql_error());
if($rs = mysql_fetch_array($result)){
	$data['userName'] = $rs['Name'];
	$data['Rate'] = $rs['Rate'];
	$data['M1'] = $rs['Latitude1'];
	$data['L1'] = (($rs['Latitude1'] > 0) ? $rs['Latitude1'] : $data['Latitude']);
	$data['L2'] = (($rs['Longitude1'] > 0) ? $rs['Longitude1'] : $data['Longitude']);
	$data['Address1'] = $rs['Address1'];
	$data['Area1'] = $rs['Area1'];
}
$sql = "SELECT Name FROM Catalog WHERE Catalog.No = '" . $data['Area'] . "'";
$result = mysql_query($sql) or die(mysql_error());
if($rs = mysql_fetch_array($result)){
	$data['City'] = $rs['Name'];
}

			$activity_join = "N.A.";
			$activity_ratio = "N.A.";
			$activity_timer = "N.A.";
			$activity_ratio = "限量 --";
			$activity_join = "已售 --";
			$activity_timer = "--時--分--秒";


$WEB_CONTENT = "<table cellpadding='0' cellspacing='0' border='0' width='706' style='background:#FFFFFF'>";
		if($data){
			$receipt = array("", "可以提供發票", "可以提供收據", "都無法提供");
			$price = "$" . ($data['Price']);
			$price = (($data['Price'] > 0) ? "$" . ($data['Price']) : " --");
			$sell = "$" . ($data['Price1']);
			$save = $data['Price'] - $data['Price1'];
			$save = "$" . (($save > 0) ? number_format($save) : " --");

			if($data['Transport'] == 1){
				$discount = (($data['taxi_discount']) ? "{$data['taxi_discount']}折":"");
			}
			else{
				if($data['price_mode'] == 1){
					$discount = "折扣 --";
				}
				else{
					$discount = (float)(number_format(($data['Price1'] / $data['Price'])*10,1));
					if($discount <= 0){
						$discount = "免費商品";
					}
					else if($discount >= 10){
						$discount = "折扣 --";
					}
					else{
						$d = explode(".", strval($discount));
						$discount = "". $d[0] . "";
						if(sizeof($d) > 1){
							$discount .= ".". $d[1] . "";
						}
						$discount = $discount . "折";
					}
				}
			}

			$city = (($data['M1'] > 0 && $data['mobile'] == 1) ? $data['Area1'] : $data['City']);
			$city = (($data['Status2'] == 2) ? "XX" : $city);
			if($data['Deliver'] == 1)
				$city = "宅配";
			
			$detail = <<<EOD
															<table cellpadding="0" cellspacing="0">
																<tr style="height:30px">
																	<td style="width:100px;text-align:center; background:#FFAA73">原價{$price}</td>
																	<td style="width:100px;text-align:center; background:#FF7510">{$discount}</td>
																</tr>
																<tr style="height:2px"></tr>
																<tr style="height:30px">
																	<td style="width:100px;text-align:center; background:#FFAA73">{$activity_join}</td>
																	<td style="width:100px;text-align:center; background:#FF7510">{$activity_ratio}</td>
																</tr>
																<tr style="height:2px"></tr>
																<tr style="height:30px">
																	<td style="width:100px;text-align:center; background:#FFAA73">{$city}</td>
																	<td style="width:100px;text-align:center; background:#FF7510; font-size:14px; font-size:14px"><div id="timer{$data['No']};">{$activity_timer}</div></td>
																</tr>
															</table>
EOD;

	$seller1 = "業者";
	$product = "商品";
	$seller="業者名稱";
	$address="服務(面交)地址";
			if($data['Transport'] == 1){
	$seller1 = "業者";
	$product = "服務";
	$seller="車行或名號";
	$address="服務(位置)地址";


				$taxi_company = array("", "公司車行", "個人車行", "計程車合作社", "其他");
				$taxi_sex = array("", "男性", "女性", "其他性別");
				$empty = (($data['Empty'] == 2 || $data['Empty'] == 3) ? "載運中" : "空車");
				$sql = "SELECT IFNULL(COUNT(*), 0) AS Comments FROM logComment WHERE transactionNo = '{$data['No']}'";
				$r1 = mysql_query($sql) or die(mysql_error());
				list($comments) = mysql_fetch_row($r1);
			$detail = <<<EOD
															<table cellpadding="0" cellspacing="0">
																<tr style="height:30px">
																	<td style="width:100px;text-align:center; background:#FFAA73">{$empty}</td>
																	<td style="width:100px;text-align:center; background:#FF7510">{$taxi_sex[$data['taxi_sex']]}</td>
																</tr>
																<tr style="height:2px"></tr>
																<tr style="height:30px">
																	<td style="width:100px;text-align:center; background:#FFAA73">行車{$data['taxi_exp']}年</td>
																	<td style="width:100px;text-align:center; background:#FF7510">車齡{$data['taxi_age']}年</td>
																</tr>
																<tr style="height:2px"></tr>
																<tr style="height:30px">
																	<td style="width:100px;text-align:center; background:#FFAA73">{$city}</td>
																	<td style="width:100px;text-align:center; background:#FF7510; font-size:11pt">車號{$data['taxi_plate']}</td>
																</tr>
															</table>
			
EOD;
			}




			if($data['Transport'] == 1)
				$data['Seller'] = $data['Seller'] . "(" . $taxi_company[$data['taxi_company']] . ")";


			$dis = (($data['Latitude'] > 0) ? "距離：{$data['KM']}公里" : "");
			$counts = 0;
			$info = "<table>";
			$info = (($data['Seller'] != "") ? "<tr><td valign='top' nowrap align='right'>業者名稱：</td><td valign='top'>" . $data['Seller'] : "") . "</td></tr>";
			$info .= (($data['Url'] != "") ? "<tr><td valign='top' nowrap align='right'>業者網站：</td><td valign='top'><a href='" . $data['Url'] . "' target='_blank'>" . $data['Url'] : "") . "</a></td></tr>";
			$info .= (($data['Phone'] != "") ? "<tr><td valign='top' nowrap align='right'>聯絡電話：</td><td valign='top'>" . $data['Phone'] : "") . "</td></tr>";
			$info .= (($data['Receipt'] != "") ? "<tr><td valign='top' nowrap align='right'>發票或收據：</td><td valign='top'>" . $receipt[$data['Receipt']] : "") . "</td></tr>";
			
			if($data['event'] == 1){
				$info .= "<tr><td valign='top' nowrap align='right'>活動時間：</td><td valign='top'>" . $data['event_date'] . "&nbsp;&nbsp;&nbsp;&nbsp;" . (($data['event_start']!="") ? $data['event_start'] : "") . (($data['event_end']!="") ? "～" . $data['event_end'] : "") . "</td></tr>";
			}
			else{
				$info .= (($data['openHours'] != "") ? "<tr><td valign='top' nowrap align='right'>營業時間：</td><td valign='top'>" . $data['openHours'] : "") . "</td></tr>";
			}
			
			if($data['event'] == 1){
				$info .= (($data['Address'] != "") ? "<tr><td valign='top' nowrap align='right'>活動地址：</td><td valign='top'>" . $data['Address'] : "") . "</td></tr>";
			}
			else{
				$info .= (($data['Address'] != "") ? "<tr><td valign='top' nowrap align='right'>服務地址：</td><td valign='top'>" . $data['Address'] : "") . "</td></tr>";
			}
			$info .= (($data['About'] != "") ? "<tr><td valign='top' nowrap align='right'>其他資訊：</td><td valign='top'>" . str_replace("\n", "<br>", $data['About']) : "") . "</td></tr>";
			$info .= (($data['Map'] != "") ? "<tr><td valign='top' nowrap align='right'>店家位置圖：</td><td valign='top'><img src='./upload/" . basename($data['Map']) . "' style='width:488px; height:300px'>" : "") . "</td></tr>";
			//$info .= "</table>";


if($data['L1'] > 0 && $data['L2'] > 0){
	if($data['M1'] > 0 && $data['Activity'] == 0 && $data['mobile'] == 1){
		$position = $data['Seller'] . (($data['Address1'] != "") ? "<br>" . $data['Address1'] : "") . "<br>(" . number_format($data['L1'], 2) . ", " . number_format($data['L2'], 2) . ")";
	}
	else{
		$position = $data['Seller'] . (($data['Address'] != "") ? "<br>" . $data['Address'] : "") . "<br>(" . number_format($data['L1'], 2) . ", " . number_format($data['L2'], 2) . ")";
	}

$map = <<<EOD

							<tr>
								<td style="text-align:left; font-size:14pt; padding-top:8px; padding-bottom:8px">
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr>
											<td style="text-align:left">目前服務位置圖</td>
											<td style="text-align:right">
												<input type="button" value="商家位置更新">
												<input type="button" value="商家遞送路徑">
												<input type="button" value="買家前往路徑">
											</td>
										</tr>
									</table></td>
							</tR>
							<tr>
								<td><div id='map' style='width:612px; height:300px; text-align:center; vertical-align:middle'></div></td>
							</tR>
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
				html += name + "<br/>";
				html += address + "<br/>";
				html += "</div>";          	
				marker.openInfoWindowHtml(html);
			});
			return marker;
		}
</script>
<script type="text/javascript"> 
	/*
	var map = new GMap(document.getElementById("map"));
	map.addControl(new GLargeMapControl());
	map.centerAndZoom(new GPoint({$data['L2']},  {$data['L1']}), 2);
	var myLocation = new GMarker(new GPoint({$data['L2']}, {$data['L1']}));
	map.addOverlay(myLocation);
	myLocation.openInfoWindowHtml("{$position}");
 */
		function setCenter(){
			var map = new google.maps.Map2(document.getElementById("map"));
			map.addControl(new GLargeMapControl());
			map.setCenter(new google.maps.LatLng({$data['L1']},  {$data['L2']}), 13);
			var myLocation = new GMarker(new google.maps.LatLng({$data['L1']},  {$data['L2']}));
			map.addOverlay(myLocation);
			myLocation.openInfoWindowHtml("<div style='width:300px; height:60px'>{$position}</div>");
		}
</script>

EOD;


}

			$photos = '<li class="J_ECPM"><img alt="吉達資訊圖片輪播一" src="./upload/' . $data['Photo'] . '" style="width:396px; height:248px"></li>';
			$photos .= (($data['Slide'] == 1 && $data['Slide2'] != "") ? '<li class="J_ECPM"><img alt="吉達資訊圖片輪播二" src="' . $data['Slide2'] . '" style="width:396px; height:248px"></li>' : "");
			$photos .= (($data['Slide'] == 1 && $data['Slide3'] != "") ? '<li class="J_ECPM"><img alt="吉達資訊圖片輪播三" src="' . $data['Slide3'] . '" style="width:396px; height:248px"></li>' : "");
			$photos .= (($data['Slide'] == 1 && $data['Slide4'] != "") ? '<li class="J_ECPM"><img alt="吉達資訊圖片輪播四" src="' . $data['Slide4'] . '" style="width:396px; height:248px"></li>' : "");
			switch($data["Restrict"]){
				case 1:
					$restrict = "每人不限交易次數，購量與限用數量";
					break;
				case 2:
					$restrict = "每人不限交易次數與購量，但每人到店限用 {$data['maxUse']}</font> 張";
					break;
				case 3:
					$restrict = "每人限制一次</font>交易次數，限購 {$data['maxBuy']}</font> 張，每人到店限用 {$data['maxUse']}</font> 張";
					break;
				case 4:
					$restrict = "每人限制一次</font>交易次數，限購 {$data['maxBuy']}</font> 張，不限每人使用數量";
					break;
			}
			$buy_info = "購買詳情請參考<br>以下之說明介紹";
			$buy_btn = <<<EOD
				<td style="color:white; background:url('./images/btn_190_disabled_stop.jpg'); height:47px; background-repeat:no-repeat; background-position:center center; text-align:center">
					<span style="font-size:16pt; font-weight:bold"></span>
				</td>
EOD;
			if($data['Cashflow'] == 1){
				$buy_btn = <<<EOD
					<td style="color:white; background:url('./images/btn_190.png'); height:47px; background-repeat:no-repeat; background-position:center center; text-align:center">
						<span style="font-size:16pt; font-weight:bold; cursor:pointer;">立即買</span>
					</td>
EOD;
				if($data['Deliver'] == 0){
					$hours = str_replace("\n", "<br>", $data['Hours']);
					$memo = str_replace("\n", "<br>", $data['Memo']);
					$coupon=<<<EOD
					<tr style="height:22px"></tr>
					<tr>
						<td style="cursor:pointer;text-align:left; background:url('./images/green_bar_down.gif'); background-repeat:no-repeat; background-position:center center; height:40px; width:612px; padding-left:10px; font-size:16pt; font-weight:bold" onClick="Switch('3');" align='left' id="b3">憑證使用須知</td>
					</tr>
					<tr>
						<td id="p3" style="display:none; text-align:left; padding-left:10px; padding-top:10px" align='left'>
							<table>
								<tr>
									<td style="text-align:right" nowrap valign="top">兌換期間：</td>
									<td style="text-align:left" valign="top">{$data['dateValidate']}</font> 至 {$data['dateExpire']}</font>，需 {$data['daysBeforeReserve']}</font> 天前預約並告知使用團購憑證，否則恕無法提供服務，敬請配合。</td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">使用時段：</td>
									<td style="text-align:left" valign="top">{$hours}</font></td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">使用張數：</td>
									<td style="text-align:left" valign="top">{$restrict}
									</td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">使用方法：</td>
									<td style="text-align:left" valign="top">本憑證不限本人使用，為保障您的權益，持手機簡訊或列印email團購憑證使用，到店由店員抄寫憑證之消費碼與兌換時間，此消費行為始得生效。
									</td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">其他說明：</td>
									<td style="text-align:left" valign="top">{$memo}</font></td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">問題聯絡：</td>
									<td style="text-align:left" valign="top">本活動相關問題，請於週一至週六 9:00AM ~ 6:00PM 致電 InTimeGo 客服(03)5904710，其他適用於所有團購的一般注意事項請參考<a href="javascript:void(0)" style="cursor:text">電子商務服務條款</a>。
									</td>
								</tr>
							</table>
						</td>
					</tr>
EOD;
				}
				if($data['Cashflow'] == 1){
					$special = "";
					if($data['Special1'] != ""){
						$special .= <<<EOD
							<tr>
								<td style="text-align:right" nowrap valign="top">(1).</td>
								<td style="text-align:left" valign="top">{$data['Special1']}</font></td>
							</tr>
EOD;
					}
					if($data['Special2'] != ""){
						$special .= <<<EOD
							<tr>
								<td style="text-align:right" nowrap valign="top">(2).</td>
								<td style="text-align:left" valign="top">{$data['Special2']}</font></td>
							</tr>
EOD;
					}
					if($data['Special3'] != ""){
						$special .= <<<EOD
							<tr>
								<td style="text-align:right" nowrap valign="top">(3).</td>
								<td style="text-align:left" valign="top">{$data['Special3']}</font></td>
							</tr>
EOD;
					}
					if($data['Special4'] != ""){
						$special .= <<<EOD
							<tr>
								<td style="text-align:right" nowrap valign="top">(4).</td>
								<td style="text-align:left" valign="top">{$data['Special4']}</font></td>
							</tr>
EOD;
					}
					if($data['Special5'] != ""){
						$special .= <<<EOD
							<tr>
								<td style="text-align:right" nowrap valign="top">(5).</td>
								<td style="text-align:left" valign="top">{$data['Special5']}</font></td>
							</tr>
EOD;
					}
					$special=<<<EOD
					<tr style="height:22px"></tr>
					<tr>
						<td style="cursor:pointer;text-align:left; background:url('./images/green_bar_down.gif'); background-repeat:no-repeat; background-position:center center; height:40px; width:612px; padding-left:10px; font-size:16pt; font-weight:bold" onClick="Switch('4');" align='left' id="b4">好康特色</td>
					</tr>
					<tr>
						<td id="p4" style="display:none; text-align:left; padding-left:10px; padding-top:10px" align='left'>
							<table>{$special}
							</table>
						</td>
					</tr>
EOD;
				}

				$activity_join = "0人購買";
			}

			$code = $data['Member'] . str_pad($data['No'], 5, "0", STR_PAD_LEFT);
			$share = <<<EOD
				<tr>
					<td style="padding-top:22px;text-align:right" align='right'>
					<div style="float:left; line-height:30px">服務代碼：<font color=>{$code}</font></div>
					<table border=0 align="right">	
						<tr>
							<tD><!--div class="fb-like" data-href="http://{$WEB_HOST}/" data-send="false" data-layout="button_count" data-width="150" data-show-faces="false"></div--></td>
							<tD>分享朋友：</td>
							<tD><SCRIPT language=javascript> 
				var pro_url=location.href;
				var pro_name=document.title;
				facebook_show_image(); 
				</SCRIPT></td>
							<td>　加入收藏追蹤：</td>
							<td><img src="./images/plus.png" border="0"></td>
						</tr>
					</table>
					
					
					
					</td>
				</tr>
EOD;
		
			$activity_timer = "不限時";
			$left = 0;//strtotime($data['dateClose']) - time();
			if($data['Duration'] == 1){
				$left = $data['daysOnSale'] * 24 * 60 * 60;
			}
			if($data['Cashflow'] == 1 && $data['Deliver'] == 0){
				$close = date('Y-m-d', strtotime($data['dateExpire'] . "-" . $data['daysBeforeReserve'] . " day")) . "23:59:59";
				$left = strtotime($close) - time();
			}
			if($data['Activity'] == 1){
				$left = strtotime($data['activity_end'] . " 23:59:59") - time();
			}
			$activity_ratio = (($data['Amount'] == 0) ? "不限量": "總量 " . $data['Quota']);

			if($left > 0){
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
			}

			if($data['Activity'] == 1){
//				$data['Price1']=0;
				$activity_join = "0 人參加";
				$activity_ratio = "機率" . round(100 / $data['activity_per']) . "%";
				$activity_holder = "<tr><td valign='top' nowrap align='right'>主辦單位名稱：</td><td valign='top'><font color=black>" . $data['activity_holder'] . "</td></tr>";
				$activity_holder .= "<tr><td valign='top' nowrap align='right'>主辦單位郵件：</td><td valign='top'><font color=black>" . $data['activity_email'] . "</td></tr>";
				$f = fetchUrl("https://graph.facebook.com/" . $data['activity_page']);
				$p = json_decode($f);
				
				//$activity_draw = "採用系統隨機抽獎方式，抽獎結果將公佈本站與主辦FaceBook。";
				//$activity_draw = "由主辦單位決定抽獎方式及結果公佈方式。";
				$activity_draw = "";
				$activity_draw .= (($data['activity_min'] != "" && $data['activity_min'] != 0) ? "若沒有超過最低 {$data['activity_min']}</font> 人門檻，則不提供獎項。<br>" : "");
				$activity_draw .= (($data['activity_per'] != "" && $data['activity_per'] != 0) ? "參加人數每超過 {$data['activity_per']}</font> 人，就提供一個抽獎獎項，直到抽獎數量用完為止。<br>" : "");
				$activity_draw .= "採用系統隨機抽獎方式, 抽獎結果將公佈於本站 [會員獲利公告], 本站也會主動發出email告知得獎者。";

				$buy_info = "<font style='font-size:10pt'>請閱讀說明內容並完成[加入粉絲團步驟]，再按下[參加活動]</font>";
				$buy_btn = <<<EOD
					<td style="color:white; background:url('./images/btn_190.png'); height:47px; background-repeat:no-repeat; background-position:center center; text-align:center">
						<span style="font-size:16pt; font-weight:bold; cursor:pointer;">參加活動</span>
					</td>
EOD;
				$activity_info = str_replace("\n", "<br>", $data['activity_info']);
				$activity_page = '<div class="fb-like-box" data-href="' . $p->{'link'} . '" data-width="292" data-show-faces="false" data-stream="false" data-header="true"></div>';
				$share=<<<EOD
							<tr>
								<td style="padding-top:22px;text-align:left" align='left'>
					<table style="border:solid 5px #669900; width:100%">
						<tr>
							<td rowspan="3" style="width:100px; text-align:center"><img src="./images/join_group_word.gif"></td>
							<td style="height:40px; width:50px; text-align:center"><img src="./images/number1.gif"></td>
							<td style="text-align:left">
								<table><Tr><td>請先登入FaceBook：</td><td><img src="./images/fb_login.gif"></td></tr></table>
							</td>
						</tr>
						<tr>
							<td style="height:40px; width:50px; text-align:center"><img src="./images/number2.gif"></td>
							<td style="text-align:left">
									<table cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td>按讚加入粉絲團：</td>
											<td>{$activity_page}</td>
										</tr>
									</table>
							</td>
						</tr>
						<tr>
							<td style="height:41px; width:50px; text-align:center"><img src="./images/number3.gif"></td>
							<td style="text-align:left">
								<table><Tr><td>留言推薦，分享朋友：</td><td>
									<table cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td><img src="./images/icops_1.gif"></td>
											<td><img src="./images/icops_5.gif"></td>
										</tr>
									</table>
								</td></tr></table></td>
						</tr>
					</table>
								</td>
							</tr>

					<tr style="height:22px"></tr>
					<tr>
						<td style="cursor:pointer;text-align:left; background:url('./images/green_bar_down.gif'); background-repeat:no-repeat; background-position:center center; height:40px; width:612px; padding-left:10px; font-size:16pt; font-weight:bold" onClick="Switch('8');" align='left' id="b8">活動參與須知</td>
					</tr>
					<tr>
						<td id="p8" style="display:none; text-align:left; padding-left:10px; padding-top:10px" align='left'>
							<table>
								<tr>
									<td style="text-align:right" nowrap valign="top">活動期間：</td>
									<td style="text-align:left" valign="top">{$data['activity_start']}</font> 至 {$data['activity_end']}</font></td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">公佈日期：</td>
									<td style="text-align:left" valign="top">{$data['activity_ann']}</font></td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">抽獎數量：</td>
									<td style="text-align:left" valign="top">{$data['activity_quota']}</font>
									</td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">抽獎方式：</td>
									<td style="text-align:left" valign="top">{$activity_draw}
									</td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">抽獎說明：</td>
									<td style="text-align:left" valign="top">{$activity_info}</font></td>
								</tr>
							</table>
						</td>
					</tr>

EOD;
			}
			$city = (($data['M1'] > 0 && $data['mobile'] == 1) ? $data['Area1'] : $data['City']);
			$city = (($data['Status2'] == 2) ? "XX" : $city);
			$city = (($data['Deliver'] == 0) ? $city : "宅配");
			$map = (($data['Deliver'] == 0) ? $map : "");

			$price = (($data['Price'] > 0) ? "$" . ($data['Price']) : " --");
			$sell = "$" . ($data['Price1']);
			$save = $data['Price'] - $data['Price1'];
			$save = "$" . (($save > 0) ? number_format($save) : " --");
	
			if($data['Transport'] == 1){
				$discount = (($data['taxi_discount']) ? "{$data['taxi_discount']}折":"");
			}
			else{
				if($data['price_mode'] == 1){
					$discount = "折扣 --";
				}
				else{
					$discount = (float)(number_format(($data['Price1'] / $data['Price'])*10,1));
					if($discount <= 0){
						$discount = "免費商品";
					}
					else if($discount >= 10){
						$discount = "折扣 --";
					}
					else{
						$d = explode(".", strval($discount));
						$discount = "". $d[0] . "";
						if(sizeof($d) > 1){
							$discount .= ".". $d[1] . "";
						}
						$discount = $discount . "折";
					}
				}
			}


if($data['Transport'] == 1){
		$price_info = <<<EOD
			<span style="font-size:20pt; color:red; font-family:Arial">{$data['taxi_discount']}</span>
EOD;
}
else{
	if($data['price_mode'] == 0){
		$price_info = <<<EOD
			<span style="font-size:20pt; color:red; font-family:Arial">{$sell}</span> <span style="font-size:10pt; color:black; font-family:Arial">省{$save}</span>
EOD;
	}
	else{
		$price_info = <<<EOD
			<span style="font-size:20pt; color:red; font-family:Arial">{$data['price_info']}</span>
EOD;
	}
}


			$WEB_CONTENT .= <<<EOD
				<tr>
					<td align='center' style="padding-top:12px">
						<table width="620" height="242" cellpadding="0" cellspacing="0" border=0 align='center'>
							<tr><td align='left'><div style='text-align:left; font-weight:bold; line-height:22px; color:#666666'><font style="color:#F74521">【{$data['Name']}】</font>{$data['Description']}</div></td>
							</tr>
							<tr>
								<td>

									<table border=0 cellpadding="0" cellspacing="0" width="620">
										<tr>
											<td align='left' style="padding-top:12px; width:396px" valign="top">
											<div class="j j_Slide loading">
												<ol class="tb-slide-list">{$photos}</ol>
											</div><!--div style="height:248px; width:396px; overflow:hidden;border:solid 1px #99CCFF"><img src="./upload/{$data['Photo']}" border='0' style="height:248px; width:396px; "></div-->
											</td>
											<td align='right' style="padding-top:12px;width:200px" valign="top">
												<table cellpadding="0" cellspacing="0" border="0" align='right'>
													<tr>
														<td style="width:200px; text-align:center; vertical-align:bottom" valign="bottom">
															<table align="center" width="100%" cellpadding="0" cellspacing="0" style="border:solid 5px #99CCFF">
																<tr style="height:40px">
																	<td>{$price_info}</td>
																</tR>
																<tr style="height:47px">{$buy_btn}</td>
																</tr>
																<tr style="height:55px">
																	<td style="font-size:14px; height:47px; text-align:left; padding:5px" align="left">{$buy_info}</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr style="height:8px"></td>
													<tr>
														<td style="width:200px">{$detail}
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>

								</td>
							</tr>
							<tr style="display:none">
								<td align='left' style="padding-top:12px;">
									<div style="height:248px; width:396px; overflow:hidden;border:solid 1px red"><img src="./upload/{$data['Photo']}" border='0' style="height:248px; width:396px; "></div>
								</td>
							</tr>{$share}{$activity}{$coupon}{$special}
							<tr style="height:22px"></tr>
							<tr>
								<td style="cursor:pointer;text-align:left; background:url('./images/green_bar_down.gif'); background-repeat:no-repeat; background-position:center center; height:40px; width:612px; padding-left:10px; font-size:16pt; font-weight:bold" onClick="Switch('1');" align='left' id="b1">服務&資訊說明</td>
							</tr>
							<tr>
								<td id="p1" style="display:none; text-align:left; padding-left:10px; padding-top:10px" align='left'>
									{$data['Intro']}
								</td>
							</tr>
							<tr style="height:22px"></tr>
							<tr>
								<td style="cursor:pointer;text-align:left; background:url('./images/green_bar_down.gif'); background-repeat:no-repeat; background-position:center center; height:40px; width:612px; padding-left:10px; font-size:16pt; font-weight:bold" onClick="Switch('2');" align='left' id="b2">業者({$holder})資訊</td>
							</tr>
							<tr>
								<td id="p2" style="display:none; text-align:left; padding-left:10px; padding-top:10px" align='left'><table>{$activity_holder}{$info}</table>
									
								</td>{$map}
							</tr>
							<tr>
								<td style="padding-top:22px;text-align:left" align='left'>
									<table cellpadding="0" cellpadding="0"><tr><td>訂閱電子報：</td><td><input type="text" style="width:400px" name="email" id="email"></td><td><input type="button" value="訂閱" onClick="Subscribe('{$data['No']}')"></td></tr></table>
								</td>
							</tr>
							<tr>
								<td style="padding-top:22px;text-align:left" align='left'>
								♦商家 {$data['userName']}</font><a href="javascript:void(0);">(金流交易評價：{$data['Rate']})</a>;&nbsp; 					
								♦<a href="javascript:sellerProduct('{$data['Member']}');">商家其它服務</a>;&nbsp; 
								♦<a href="javascript:void(0);">詢問商家問題</a>
								♦<a href="javascript:void(0);">發表本服務評論</a>
								</td>
							</tr>
							<tr>
								<td style="text-align:left; line-height:40px" align='left'>
									<table cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td><img src="./images/pencil-1.png" style="height:24px"></td>
											<td style="padding-left:5px">最新評論文章</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="text-align:left" align='left'>
									<table style="width:100%">
										<tr>
											<td style="width:100px; line-height:22px; background:#f7f7f7;text-align:center">作者</td>
											<td style="; line-height:22px; background:#f7f7f7;text-align:center">留言內容</td>
								<td style="width:120px; line-height:30px; background:#b5b2b5;text-align:center">評分 (0 av)</td>
											<td style="width:120px; line-height:22px; background:#f7f7f7;text-align:center">留言時間</td>
										</tr>
										{$rating}
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
EOD;
		}

$WEB_CONTENT .= "</table>";


include './include/db_close.php';

include 'search.php';
include 'template3.php';
?>

<?if ($data['Slide'] == 1){?>
<script language="javascript" type="text/javascript">
$(function() {
var $o = $(".j_Slide");
	var ks = $o.find("ol").Oslide({
		slidetag:$o.find("ol>li"),
		easing:"easeInOutCirc",
		speed:450
	});
var $p = $(".j_Slide1");
	var ks = $p.find("ol").Oslide({
		slidetag:$p.find("ol>li"),
		btntag:$p.find(".handel"),
		direct:'right',
		easing:"easeInOutCirc"
	});
});
</script>
<?}?>
<script language="javascript">
	var counts = 0;
	function Switch(x){
		counts ++;
		$('#p'+x).toggle();
		$('#b'+x).css({background: "url('./images/green_bar_" + ((counts % 2 == 1) ? "up" : "down") + ".gif')"});
	}
	function addFavorite(xNo){
		var iForm = document.iForm;
		if(xNo){
			iForm.product.value = xNo;
			iForm.action = "add_favorite.php";
			iForm.submit();
		}
	}
	function Subscribe(xNo){
		var iForm = document.iForm;
		if(!$("#email").val()){
			alert("請輸入您的電子郵件信箱!");
			return ;
		}
		if(xNo){
			iForm.product.value = xNo;
			iForm.email.value = $("#email").val();
			iForm.action = "add_subscribe.php";
			iForm.submit();
		}
	}
</script></script><script type="text/javascript">
  google.load("maps", "2",{"other_params":"sensor=true"});
  google.setOnLoadCallback(setCenter);
</script>

