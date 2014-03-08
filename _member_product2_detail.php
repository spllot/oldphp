<?php
require_once getcwd() . '/class/facebook.php';
include './include/db_open.php';
$no = $_REQUEST['no'];
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}
$scroll_rate=$_CONFIG['scroll'];


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





$sql = "SELECT SUM(Rating) AS R, Count(*) AS C FROM logComment WHERE Question=0 AND transactionNo = '$no'";
$result = mysql_query($sql) or die(mysql_error());
$av=0;
if($rs=mysql_fetch_array($result)){
	if($rs['C'] > 0){
		$av = number_format(round($rs['R']/$rs['C'], 1), 1);
	}
}





$sql = "SELECT *, (SELECT Nick FROM Member WHERE No = logComment.rateBy) AS rName FROM logComment WHERE transactionNo = '$no' order by dateRated desc";
$result = mysql_query($sql) or die(mysql_error());
$bg = array("#FFFFFF", "#FFFFFF");
$i=0;
while($rs = mysql_fetch_array($result)){
	$content = str_replace("\n", "<br>", $rs['Content']);
	$reply = str_replace("\n", "<br>", $rs['Reply']);
	$datereply=(($rs['dateReplied']=="0000-00-00 00:00:00") ? "尚未":$rs['dateReplied']);
	$bgcolor=$bg[$i%2];
	$name = $rs['rName'];
	if($rs['Question'] == 1){
		$rating .= <<<EOD
			<tr style="background:{$bgcolor}; height:40px">
				<td style="text-align:center" rowspan="2">{$name}</td>
				<td style="text-align:left; color:blue">詢問：{$content}</td>
				<td></td>
				<td style="text-align:center; font-size:12px">{$rs['dateRated']}</td>
			</tr>
			<tr style="background:{$bgcolor}; height:40px">
				<td style="text-align:left; color:blue">賣家回覆：{$reply}</td>
				<td></td>
				<td style="text-align:center; font-size:12px">{$datereply}</td>
			</tr>
			<tr style="height:11px"></tr>
EOD;
	}
	else{
		$rat = number_format($rs['Rating'], 1);
		$start = my_round($rs['Rating']) . 'stars.gif';
		$rating .= <<<EOD
			<tr style="background:{$bgcolor}; height:40px">
				<td style="text-align:center">{$name}</td>
				<td style="text-align:left; color:blue">評論：{$content}</td>
				<td style="color:#E7711B; text-align:center">{$rat}&nbsp;&nbsp;<img src='./images/{$start}'></td>
				<td style="text-align:center; font-size:12px">{$rs['dateRated']}</td>
			</tr>
			<tr style="height:11px"></tr>
EOD;
	}
	$i++;
	if($i >= 3){
		break;
	}
}
	if(mysql_num_rows($result) > 3){
		$rating .= <<<EOD
			<tr><td colspan="4" align="right"><a href="javascript:parent.Dialog('comment_more.php?product={$rs['transactionNo']}');">more></a></td></tr>
EOD;
	}

$sql = "SELECT *, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Product='$no' AND Member='" . $_SESSION['member']['No'] . "'), 0) AS Amounts, IFNULL((SELECT COUNT(*) FROM Orders WHERE Product='$no' AND Member='" . $_SESSION['member']['No'] . "'), 0) AS Buy, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.No=logCoupon.couponNo WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, IFNULL((SELECT count(*) FROM Coupon WHERE Status = 1 AND Product=Product.No), 10000) AS coupon_used, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Address1 FROM Member WHERE No = Product.Member) AS Address1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude) AS L1, IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude) AS L2, IFNULL((SELECT SUM(Quality) FROM logRating WHERE Owner = Product.Member), 0) as Rate, (SELECT Nick FROM Member WHERE Member.No = Product.Member) AS userName, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM FROM Product WHERE Status = 2 AND Mode = 1 AND Deliver = 1 AND dateClose >= CURRENT_TIMESTAMP AND No = '$no' ORDER BY KM";
$result = mysql_query($sql) or die(mysql_error());
$num = mysql_num_rows($result);
$data = mysql_fetch_array($result);
$seller=$data['Member'];
//if($data['Activity'] == 1){$data['Price1'] =0;}
$discount = (float)(number_format(($data['Price1'] / $data['Price'])*10,1));
			if($discount <= 0){
				$discount = "免費";
			}
			else if($discount >= 10){
				$discount = "折扣 --";
			}
			else{
				$discount = $discount . "折";
			}
$fb_title = "【{$data['Name']}】{$discount}";
$fb_desc = $data['Description'];
$fb_thumb = "http://{$WEB_HOST}/upload/{$data['Photo']}";

//echo $sql;
$WEB_CONTENT = <<<EOD
<style>
div.fb_dialog_advanced+div.fb_dialog_advanced {
	top:50px !important;
}
</style>
<div id="fb-root"></div>
<script>
			window.fbAsyncInit = function() {
			  FB.init({
				appId      : '223714571074260',
				status     : true, 
				cookie     : true,
				xfbml      : true,
				oauth      : true
			  });
			  FB.Event.subscribe('auth.login', function() {
				//window.location.reload();
//				 var time=new date(); when=time.getTime();
//				$("#activity").load("facebook_activity.php?no={$no}&time=" + Math.floor(Math.random()*11));
//				$("#fb_login").load("facebook_login.php");
//				checkStep('{$no}');
			  });
			  FB.Event.subscribe('auth.logout', function() {
				//window.location.reload();
//				$("#activity").load("facebook_activity.php?no={$no}&time=" + Math.floor(Math.random()*11));
//				$("#fb_login").load("facebook_login.php");
//				checkStep('{$no}');
			  });
			 FB.Event.subscribe('edge.create',
				function(response) {
//					$("#activity").load("facebook_activity.php?no={$no}&time=" + Math.floor(Math.random()*11));
//					checkStep('{$no}');
				}
			);
		};
		(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/zh_TW/all.js#xfbml=1&appId=223714571074260";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


</script>
<script>
	function move_to_top( value ){
		$(".fb_dialog").each(function(index) {
			if($(this).css("top")!='-10000px')
			{
				$(this).css("top", '50px' );
				window.parent.document.body.scrollTop = 0;
				window.parent.document.documentElement.scrollTop = 0;
			}
		});
		setTimeout( ('move_to_top("'+value+'");'), 1250);
	}
	
	function postToFeed() {
		var caption = encodeURI("http://{$WEB_HOST}/product5_detail.php?no={$no}") ;

        var obj = {
			method: 'feed',
			link: 'http://{$WEB_HOST}/product5_detail.php?no={$no}',
            picture: '{$fb_thumb}',
            name: '{$fb_title}',
//            caption: caption,
            description: '{$fb_desc}'
		};

        function callback(response) {
//			$("#activity").load("facebook_activity.php?no={$no}&time=" + Math.floor(Math.random()*11));
//			checkStep('{$no}');
			window.parent.document.body.scrollTop = 0;
			window.parent.document.documentElement.scrollTop = 0;
			if(response){
				$.post(
					'fackbook_share.php',
					{
						no: '{$no}'
					},
					function(data)
					{
						eval("var response = " + data);
						if(response.success == "1"){
							iFacebook.location.reload();
						}
					}
				);
			}
        }

		FB.Canvas.scrollTo(0,0);
        FB.ui(obj, callback);
		$(".fbProfileBrowserResult").ready( function(){
			t = setTimeout ( ('move_to_top("'+50+'")'), 1250 );
		});
      }
</script>

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
<table cellpadding='0' cellspacing='0' border='0' width='660' style='background:white'>
EOD;
if ($num>0){
		if($data){
			$activity_join = "N.A.";
			$activity_ratio = "N.A.";
			$activity_timer = "N.A.";
			$activity_ratio = "限量 --";
			$activity_join = "已售 --";
			$activity_timer = "--時--分--秒";
			$receipt = array("", "可以提供發票", "可以提供收據", "都無法提供");
			$price = "$" . number_format($data['Price']);
			$sell = "$" . number_format($data['Price1']);
			$save = $data['Price'] - $data['Price1'];
			$save = "$" . (($save > 0) ? number_format($save) : " --");

			$dis = (($data['Latitude'] > 0) ? "距離：{$data['KM']}公里" : "");
			$counts = 0;
			$info = (($data['Seller'] != "") ? "<tr><td valign='top' nowrap align='right'>業者名稱：</td><td valign='top'>" . $data['Seller'] : "") . "</td></tr>";
			$info .= (($data['Url'] != "") ? "<tr><td valign='top' nowrap align='right'>業者網站：</td><td valign='top'><a href='" . $data['Url'] . "' target='_blank'>" . $data['Url'] : "") . "</a></td></tr>";
			$info .= (($data['Phone'] != "") ? "<tr><td valign='top' nowrap align='right'>聯絡電話：</td><td valign='top'>" . $data['Phone'] : "") . "</td></tr>";
			$info .= (($data['Receipt'] != "") ? "<tr><td valign='top' nowrap align='right'>發票或收據：</td><td valign='top'>" . $receipt[$data['Receipt']] : "") . "</td></tr>";
//			$info .= (($data['openHours'] != "") ? "<tr><td valign='top' nowrap align='right'>營業時間：</td><td valign='top'>" . $data['openHours'] : "") . "</td></tr>";
//			$info .= (($data['Address'] != "") ? "<tr><td valign='top' nowrap align='right'>服務地址：</td><td valign='top'>" . $data['Address'] : "") . "</td></tr>";
			$info .= (($data['About'] != "") ? "<tr><td valign='top' nowrap align='right'>其他資訊：</td><td valign='top'>" . str_replace("\n", "<br>", $data['About']) : "") . "</td></tr>";




//			$info .= (($data['Map'] != "") ? "<tr><td valign='top' nowrap align='right'>店家位置圖：</td><td valign='top'><img src='./upload/" . basename($data['Map']) . "' style='width:488px; height:300px'>" : "") . "</td></tr>";


			$info .= <<<EOD
				<tr style="display:none">
					<td valign='top' nowrap align='right'>店家位置圖：</td>
					<td align="center" valign="middle"></td>
				</tr>
EOD;


if($data['L1'] > 0 && $data['L2'] > 0){
	if($data['M1'] > 0 && $data['mobile'] == 1){
		$position = $data['Seller'] . (($data['Address1'] != "") ? "<br>" . $data['Address1'] : "") . "<br>(" . number_format($data['L1'], 2) . ", " . number_format($data['L2'], 2) . ")";
	}
	else{
		$position = $data['Seller'] . (($data['Address'] != "") ? "<br>" . $data['Address'] : "") . "<br>(" . number_format($data['L1'], 2) . ", " . number_format($data['L2'], 2) . ")";
	}

$map = <<<EOD

							<tr style="display:none">
								<td style="text-align:left; font-size:14pt; padding-top:8px; padding-bottom:8px">目前服務位置圖</td>
							</tR>
							<tr style="display:none">
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
	var map = new GMap(document.getElementById("map"));
	map.addControl(new GLargeMapControl());
	map.centerAndZoom(new GPoint({$data['L2']},  {$data['L1']}), 2);
	var myLocation = new GMarker(new GPoint({$data['L2']}, {$data['L1']}));
	map.addOverlay(myLocation);
	myLocation.openInfoWindowHtml("{$position}");
 
</script>
<script language="javascript"> 
//	var marker = createMarker(new GPoint( {$data['Longitude']},   {$data['Latitude']}), "{$data['Seller']}", "", "{$data['Address']}", "");
//	map.addOverlay(marker);
</script>

EOD;


}
			$holder = "商家";
			if($data['Activity'] == 1){
				$holder="商家";
			}
			$buy_info = "購買詳情請參考<br>以下之說明介紹";
			$photos = '<li class="J_ECPM"><img alt="吉達資訊圖片輪播一" src="./upload/' . $data['Photo'] . '" style="width:396px; height:248px"></li>';
			$photos .= (($data['Slide'] == 1 && $data['Slide2'] != "") ? '<li class="J_ECPM"><img alt="吉達資訊圖片輪播二" src="' . $data['Slide2'] . '" style="width:396px; height:248px"></li>' : "");
			$photos .= (($data['Slide'] == 1 && $data['Slide3'] != "") ? '<li class="J_ECPM"><img alt="吉達資訊圖片輪播三" src="' . $data['Slide3'] . '" style="width:396px; height:248px"></li>' : "");
			$photos .= (($data['Slide'] == 1 && $data['Slide4'] != "") ? '<li class="J_ECPM"><img alt="吉達資訊圖片輪播四" src="' . $data['Slide4'] . '" style="width:396px; height:248px"></li>' : "");
			$discount = (float)(number_format(($data['Price1'] / $data['Price'])*10,1));
			if($discount <= 0){
				$discount = "免費";
			}
			else if($discount >= 10){
				$discount = "折扣 --";
			}
			else{
				$discount = $discount . "折";
			}
			$c_url = "<a href=\"javascript:parent.Dialog2('comment.php?product={$data['No']}');\">";
			$q_url = "<a href=\"javascript:parent.Dialog2('question.php?product={$data['No']}');\">";
			if(empty($_SESSION['member'])){
				$c_url = "<a href=\"member_login.php?url=" . urlencode($_SERVER['PHP_SELF'] . "?no={$data['No']}&area=$area&type={$_REQUEST['type']}&catalog={$_REQUEST['catalog']}") . "\">";
				$q_url = "<a href=\"member_login.php?url=" . urlencode($_SERVER['PHP_SELF'] . "?no={$data['No']}&area=$area&type={$_REQUEST['type']}&catalog={$_REQUEST['catalog']}") . "\">";
			}
			$city = (($data['M1'] > 0 && $data['mobile'] == 1) ? $data['Area1'] : $data['City']);
			$city = (($data['Status2'] == 2) ? "XX" : $city);
			
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
			$buy_btn = <<<EOD
				<td style="color:white; background:url('./images/btn_190_disabled_stop.jpg'); height:47px; background-repeat:no-repeat; background-position:center center; text-align:center">
					<span style="font-size:16pt; font-weight:bold"></span>
				</td>
EOD;
			if($data['Cashflow'] == 1){
				$onclick = ((!empty($_SESSION['member'])) ? "parent.Dialog('buynow.php?id={$data['No']}');":"window.location.href='member_login.php?url=" . urlencode("product2_detail.php?no={$data['No']}") . "';");
				$onclick = ((!empty($_SESSION['member'])) ? "window.location.href='buynow.php?id={$data['No']}';":"window.location.href='member_login.php?url=" . urlencode("buynow.php?id={$data['No']}") . "';");

				if(!empty($_SESSION['member']) && $data["Restrict"] == 3 && $data['Buy'] > 0){
					$onclick = "alert('每人只限購買一次!');";
				}
				if(!empty($_SESSION['member']) && $data['Amount'] > 0){
					$result = mysql_query("SELECT SUM(Items.Amount) FROM Items INNER JOIN Orders ON Orders.ID = Items.orderID WHERE Items.Refund=0 AND Orders.Status <> 3 AND Orders.Product='$no'") or die(mysql_error());
					list($sold) = mysql_fetch_row($result);
					if($data['Quota'] <= $sold){
						$onclick = "alert('已售完!');";
					}
				}

				$buy_btn = <<<EOD
					<td style="color:white; background:url('./images/btn_190.png'); height:47px; background-repeat:no-repeat; background-position:center center; text-align:center">
						<span style="font-size:16pt; font-weight:bold; cursor:pointer;" onClick="{$onclick}">立即買</span>
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
									<td style="text-align:left" valign="top">{$data['dateValidate']}</font> 至 {$data['dateExpire']}</font>，需 {$data['daysBeforeReserve']}</font> {$data['daysUnit']}前預約並告知使用團購憑證，否則恕無法提供服務，敬請配合。</td>
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
									<td style="text-align:left" valign="top">本活動相關問題，請於週一至週六 9:00AM ~ 6:00PM 致電 InTimeGo 客服(03)5904710，其他適用於所有團購的一般注意事項請參考<a href="javascript:parent.Dialog('policy.php')">電子商務服務條款</a>。
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

				$activity_join = $data['Sold'] . "人購買";
				$left = strtotime($data['dateClose']) - time();
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

				$activity_ratio = (($data['Amount'] == 0) ? "不限量": "總量 " . ($data['Quota']-$data['Solds']));
				$activity_ratio = "總量 " . ($data['Quota']-$data['Solds']);

			}
			$coupon_quota = $data['coupon_quota'] - $data['coupon_used'];
			if($data['coupon_YN'] == 1 && $coupon_quota > 0){
				$activity_ratio = (($data['Amount'] == 0) ? "不限量": "總量 " . ($data['Quota']-$data['Solds']));
				$activity_join = $data['Coupon'] . "人索取";
				
				$buy_info = "索取詳情請參考<br>以下之說明介紹";
				$coupon_info = str_replace("\n", "<br>", $data['coupon_info']);
				if($coupon_quota > 0){
					$onclick= "parent.Dialog('coupon.php?id={$data['No']}');";
				}
				else{
					$onclick="alert('優惠憑證已發送完畢!');";
				}
				$buy_btn = <<<EOD
					<td style="color:white; background:url('./images/btn_190.png'); height:47px; background-repeat:no-repeat; background-position:center center; text-align:center">
						<span style="font-size:16pt; font-weight:bold; cursor:pointer;" onClick="$onclick">優惠憑證發送</span>
					</td>
EOD;
				
				$coupon=<<<EOD
					<tr style="height:22px"></tr>
					<tr>
						<td style="cursor:pointer;text-align:left; background:url('./images/green_bar_down.gif'); background-repeat:no-repeat; background-position:center center; height:40px; width:612px; padding-left:10px; font-size:16pt; font-weight:bold" onClick="Switch('3');" align='left' id="b3">優惠活動說明</td>
					</tr>
					<tr>
						<td id="p3" style="display:none; text-align:left; padding-left:10px; padding-top:10px" align='left'>
							<table>
								<tr>
									<td style="text-align:right" nowrap valign="top">優惠活動：</td>
									<td style="text-align:left" valign="top">{$coupon_info}</font></td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">剩餘數量：</td>
									<td style="text-align:left" valign="top">只剩 {$coupon_quota}</font> 位優惠，請儘速把握。</td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">使用方法：</td>
									<td style="text-align:left" valign="top">本憑證不限本人使用，為保障您的權益，持手機簡訊或列印email優惠憑證使用，到店由店員抄寫憑證之消費碼與兌換時間，此消費行為始得生效。
									</td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">問題聯絡：</td>
									<td style="text-align:left" valign="top">本活動相關問題詢問，可以參考 [業者(商家)資訊]諮詢，或於頁面底端 [詢問賣家問題] 之處，直接向賣家詢問您的問題。
									</td>
								</tr>
							</table>
						</td>
					</tr>
EOD;
			}


			
			
			$f_type = (($data['Broadcast'] == 1) ? "1" : "");
			$code = $data['Member'] . str_pad($data['No'], 5, "0", STR_PAD_LEFT);
			$share = <<<EOD
				<tr>
					<td style="padding-top:22px;text-align:right" align='right'>
					<div style="float:left; line-height:30px">服務代碼：<font color=>{$code}</font></div>
					<table border=0 align="right">	
						<tr>
							<tD><!--div class="fb-like" data-href="http://{$WEB_HOST}/product5_detail.php?no={$no}" data-send="false" data-layout="button_count" data-width="150" data-show-faces="false"></div--></td>
							<tD>分享朋友：</td>
							<tD><SCRIPT language=javascript> 
				var pro_url=location.href;
				var pro_name=document.title;
				facebook_show_display{$f_type}(); 
				</SCRIPT></td>
							<td>　加入收藏追蹤：</td>
							<td><img src="./images/plus.png" border="0" style="cursor:pointer" onClick="addFavorite('{$data['No']}');"></td>
						</tr>
					</table>
					
					
					
					</td>
				</tr>
EOD;
			
			
			$question = <<<EOD
				<tr>
					<td style="padding-top:22px;text-align:left" align='left'>
						<table cellpadding="0" cellpadding="0"><tr><td>訂閱電子報：</td><td><input type="text" style="width:400px" name="email" id="email"></td><td><input type="button" value="訂閱" onClick="Subscribe('{$data['No']}')"></td></tr></table>
					</td>
				</tr>
				<tr>
					<td style="padding-top:22px;text-align:left" align='left'>
				♦商家 {$data['userName']}</font><a href="javascript:parent.Dialog('seller_trust.php?id={$data['Member']}');">(金流交易評價：{$data['Rate']})</a>;&nbsp; 
					
				♦<a href="javascript:sellerProduct('{$data['Member']}');">商家其它服務</a>;&nbsp; 
					
				♦{$q_url}詢問商家問題</a>
				♦{$c_url}發表本服務評論</a>
					</td>
				</tr>
				<tr>
					<td style="text-align:left; line-height:40px" align='left'>
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td><img src="./images/pencil-1.png" style="height:24px"></td>
								<td style="padding-left:5px">最新詢問與評論文章</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style="text-align:left" align='left'>
						<table style="width:100%" border="0">
							<tr>
								<td style="width:100px; line-height:30px; background:#b5b2b5;text-align:center">作者</td>
								<td style="; line-height:30px; background:#b5b2b5;text-align:center">留言內容</td>
								<td style="width:120px; line-height:30px; background:#b5b2b5;text-align:center">評分 ({$av} av)</td>
								<td style="width:120px; line-height:30px; background:#b5b2b5;text-align:center">留言時間</td>
							</tr>
							<tr style="height:11px"></tr>
							{$rating}
						</table>
					</td>
				</tr>
EOD;
			
			

			$activity_join = $data['Sold'] . " 人購買";
			if($data['Activity'] == 1){
				//$data['Price1']=0;
				$activity_join = $data['Joins'] . " 人參加";
				if($data['activity_per'] > 0)
					$activity_ratio = "機率" . round(100 / $data['activity_per']) . "%";
				else if($data['Joins'] > 0)
					$activity_ratio = "機率" . round(100 / $data['Joins']) . "%";
				$left = strtotime($data['activity_end'] . " 23:59:59") - time();
//				echo $left;
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
						<span style="font-size:16pt; font-weight:bold; cursor:pointer;" onClick="Join('{$no}');">參加活動</span>
					</td>
EOD;

$url = "http://{$WEB_HOST}/";//product5_detail.php?no={$no}"
if($data['Mode'] == 2){
	if($data['Deliver'] == 0){
		$url .= "product4_detail.php?no={$no}";
	}
	else{
		$url .= "product5_detail.php?no={$no}";
	}
}
else if($data['Mode'] == 1){
	if($data['Deliver'] == 0){
		$url .= "product1_detail.php?no={$no}";
	}
	else{
		$url .= "product2_detail.php?no={$no}";
	}
}


$f = fetchUrl("https://graph.facebook.com/" . $data['activity_page']);
$p = json_decode($f);
$activity_page = '<div class="fb-like-box" data-href="' . $p->{'link'} . '" data-width="292" data-show-faces="false" data-stream="false" data-header="true"></div>';

$fb_login = '<fb:login-button autologoutlink="true" scope="email"></fb:login-button>';
$step1="number1.gif";
$step2="number2.gif";
$step3="number3.gif";

$facebook_share = <<<EOD
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td><img src="./images/icops_1.gif"></td>
				<td><img src="./images/icops_5.gif" style="cursor:pointer" onClick="postToFeed();"></td>
			</tr>
		</table>
EOD;



if($me){
	$step1 = "tick.gif";	
	try {
	  $likes = $facebook->api("/me/likes/{$data['activity_page']}");
	  if( !empty($likes['data']) )
		  $step2="tick.gif";
	} catch (FacebookApiException $e) {
	  error_log($e);
	}
	
	$links = $facebook->api("/me/feed?fields=link");
	
	for($i=0; $i<sizeof($links['data']); $i++){
		if($links['data'][$i]['link'] == $url){
		  $step3="tick.gif";
		  //$facebook_share = "已分享";
		  break;
		}
	}
}

$facebook_activity = <<<EOD
	<table style="border:solid 5px #669900; width:100%">
		<tr>
			<td rowspan="3" style="width:100px; text-align:center"><img src="./images/join_group_word.gif"></td>
			<td style="height:40px; width:50px; text-align:center"><img name='number1' src="./images/{$step1}"></td>
			<td style="text-align:left">
				<table border=0>
					<tr>
						<td nowrap>請先登入FaceBook：</td>
						<td nowrap><div id="fb_login"></div></td>
						<td style="padding-left:10px">{$fb_login}</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="height:40px; width:50px; text-align:center"><img name='number2' src="./images/{$step2}"></td>
			<td style="text-align:left">
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td nowrap>按讚加入粉絲團：</td>
							<td>{$activity_page}</td>
						</tr>
					</table>
			</td>
		</tr>
		<tr>
			<td style="height:41px; width:50px; text-align:center"><img name='number3' src="./images/{$step3}"></td>
			<td style="text-align:left">
				<table><Tr><td nowrap>留言推薦，分享朋友：</td><td>{$facebook_share}
				</td></tr></table></td>
		</tr>
	</table>
EOD;

				$activity_info = str_replace("\n", "<br>", $data['activity_info']);
				$activity_page = '<div class="fb-like-box" data-href="' . $p->{'link'} . '" data-width="292" data-show-faces="false" data-stream="false" data-header="true"></div>';
				$share=<<<EOD
							<tr style="display:none">
								<td style="padding-top:22px;text-align:left" align='left'>
									{$facebook_activity1}
								</td>
							</tr>
							<tr>
								<td style="padding-top:22px;text-align:left" align='left'><iframe style="width:610px; height:186px;border:solid 5px #669900" name="iFacebook" src="facebook_activity.php?no=$no"scrolling="no" frameborder="0" marginwidth="0" marginheight="0"></iframe></td>
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
								<tr>
									<td style="text-align:right" nowrap valign="top"></td>
									<td style="text-align:left" valign="top">關於贈獎活動內容如有任何疑問, 可以直接將問題詢問到主辦者信箱, 或由頁面下方[詢問主辦問題]聯繫之。</td>
								</tr>					
							</table>
						</td>
					</tr>

EOD;
				$question1 = <<<EOD
					<tr style="display:none">
						<td style="padding-top:22px;text-align:left" align='left'>
							<table cellpadding="0" cellpadding="0"><tr><td>訂閱電子報：</td><td><input type="text" style="width:400px" name="email" id="email"></td><td><input type="button" value="訂閱" onClick="Subscribe('{$data['No']}')"></td></tr></table>
						</td>
					</tr>
					<tr>
						<td style="padding-top:22px;text-align:left" align='left'>
					♦商家 {$data['userName']}</font><a href="javascript:parent.Dialog('seller_trust.php?id={$data['Member']}');">(金流交易評價：{$data['Rate']})</a>;&nbsp; 
						
					♦<a href="javascript:sellerProduct('{$data['Member']}');">商家其它服務</a>
						
					<span style="display:none">;&nbsp; ♦{$c_url}發表本服務評論</a></span>
						</td>
					</tr>
					<tr>
						<td style="padding-top:22px;text-align:left">
							<!--div style="width:612px; height:300px; overflow:auto"></div-->
							<div class="fb-comments" data-href="http://{$WEB_HOST}/product5_detail.php?no={$no}" data-num-posts="3" data-width="612" data-order-by="reverse_time" reverse=1 simple=1></div>
							<div style="text-align:right"><a href="javascript:parent.Dialog1('facebook_comment.php?no={$no}', 560);">檢視所有留言</a></div>
						</td>
					</tr>
EOD;
					$result = mysql_query("SELECT * FROM logActivity WHERE Product='$no' ORDER BY dateJoined DESC") or die(mysql_error());
					$num = mysql_num_rows($result);
					$faces = "";
					if($num > 0){
						while($rs = mysql_fetch_array($result)){
							try{$naitik = $facebook->api('/' . $rs['fbID']);} catch (FacebookApiException $e) {}
							$serial = str_pad($num, 5, "0", STR_PAD_LEFT);
							$faces .= <<<EOD
								<div style="float:left;">
									<div style="font-size:10px; text-align:center; width:60px; height:16px; overflow:hidden">No.{$serial}</div>
									<div style="width:60px; height:54px; overflow:hidden; text-align:center">
									<a href='http://www.facebook.com/profile.php?id={$rs['fbID']}' target='_blank'>
										<img src="https://graph.facebook.com/{$rs['fbID']}/picture" alt="{$naitik['name']}" style="border:solid 1px #E7E7E7; padding:1px" border='0'>
									</a>
									</div>
									<div style="font-size:10px; width:60px; height:16px; overflow:hidden; text-align:center">{$naitik['name']}</div>
								</div>
EOD;
							$num --;
						}
					}
					else{
						$faces = "<div style='height:50px; line-height:50px; color:red; text-align:center'>目前尚無人參加!</div>";
					}
					$facebook_join = <<<EOD
					<tr><td style="padding-top:22px">
						<table style="border:solid 1px #cccccc" width="100%">
							<tr>
								<td style="background:#cccccc; padding:2px; text-align:left">已參加這個活動的人有 {$data['Joins']} 位：</td>
							</tr
							<tr>
								<td>{$faces}</td>
							</tr
							<tr>
								<td>{$facebook_pagging}</td>
							</tr>
						</table>
					</td></tr>
EOD;
			}

			
			
			
			
			
			
			
			$save = $data['Price'] - $data['Price1'];
			$save = "$" . (($save > 0) ? number_format($save) : " --");
			$sell = "$" . number_format($data['Price1']);
			$discount = (float)(number_format(($data['Price1'] / $data['Price'])*10,1));	
			if($discount <= 0){
				$discount = "免費";
			}
			else if($discount >= 10){
				$discount = "折扣 --";
			}
			else{
				$discount = $discount . "折";
			}	
			
			$city = "- - -";
			
			$city = "宅配";
			$WEB_CONTENT .= <<<EOD
				<tr>
					<td align='center' style="padding-top:12px">
						<table width="620" height="242" cellpadding="0" cellspacing="0" border=0 align='center'>
							<tr><td align='left'><div style='text-align:left; line-height:22px; color:black'><font style="color:#F74521">【{$data['Name']}】</font>{$data['Description']}</div></td>
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
											<td align='right' style="padding-top:12px;width:200px;" valign="top">
												<table cellpadding="0" cellspacing="0" border="0" align='right'>
													<tr>
														<td style="width:200px; text-align:center; vertical-align:bottom" valign="bottom">
															<table align="center" width="100%" cellpadding="0" cellspacing="0" style="border:solid 5px #99CCFF">
																<tr style="height:40px">
																	<td><span style="font-size:20pt; color:red; font-family:Arial">{$sell}</span> <span style="font-size:10pt; color:black; font-family:Arial">省{$save}</span>
																	</td>
																</tR>
																<tr style="height:47px">{$buy_btn}</tr>
																<tr style="height:55px">
																	<td style="font-size:14px; height:47px; text-align:left; padding:5px" align="left">{$buy_info}</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr style="height:8px"></td>
													<tr>
														<td style="width:200px">
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
																	<td style="width:100px;text-align:center; background:#FF7510; font-size:14px"><div id="timer{$data['No']}">{$activity_timer}</div></td>
																</tr>
															</table>{$js_count}
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
							</tr>
							<tr style="display:none">
								<td align='left' valign='top' aling='left' style="padding-top:12px;" colspan="2">
									<table cellpadding="0" cellspacing="0" border="0" align='left'>
										<tr>
											<td style="width:290px">
												<table cellpadding="0" cellspacing="0">
													<tr style="height:30px">
														<td style="width:145px;text-align:center; background:#FFAA73">原價{$price}</td>
														<td style="width:145px;text-align:center; background:#FF7510">{$data['Discount']}折</td>
													</tr>
													<tr style="height:2px"></tr>
													<tr style="height:30px">
														<td style="width:145px;text-align:center; background:#FFAA73">- - -</td>
														<td style="width:145px;text-align:center; background:#FF7510">- - -</td>
													</tr>
													<tr style="height:2px"></tr>
													<tr style="height:30px">
														<td style="width:145px;text-align:center; background:#FFAA73">{$data['City']}</td>
														<td style="width:145px;text-align:center; background:#FF7510">- - -</td>
													</tr>
												</table>
											</td>
											<td width="20">&nbsp;</td>
											<td style="width:300px; text-align:center; vertical-align:bottom" valign="bottom">
												<table align="center" width="100%" cellpadding="0" cellspacing="0">
													<tr>
														<td style="color:white; background:url('./images/btn_300_disabled.png'); height:47px; background-repeat:no-repeat; background-position:center center; text-align:center">
															<span style="font-size:16pt; font-weight:bold">立即買</span>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:12pt">單價{$sell} (省{$save})</span>
														</td>
													</tr>
													<tr>
														<td style="font-size:14px; height:47px">購買詳情請參考以下之說明介紹</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>{$share}{$activity}{$coupon}{$special}
							<tr style="height:22px"></tr>
							<tr>
								<td style="cursor:pointer;text-align:left; background:url('./images/green_bar_down.gif'); background-repeat:no-repeat; background-position:center center; height:40px; width:612px; padding-left:10px; font-size:16pt; font-weight:bold" onClick="Switch('1');" align='left' id="b1">服務&資訊說明</td>
							</tr>
							<tr>
								<td><div id="p1" style="display:none; text-align:left; padding-left:10px; padding-top:10px; height:500px; overflow:auto; border-left:solid 1px #EFEFE7; border-bottom:solid 1px #EFEFE7; border-right:solid 1px #EFEFE7" align='left' onMouseOver="pauseDiv()" onMouseOut="resumeDiv()">
									{$data['Intro']}
									</div>
								</td>
							</tr>
							<tr style="height:22px"></tr>
							<tr>
								<td style="cursor:pointer;text-align:left; background:url('./images/green_bar_down.gif'); background-repeat:no-repeat; background-position:center center; height:40px; width:612px; padding-left:10px; font-size:16pt; font-weight:bold" onClick="Switch('2');" align='left' id="b2">業者({$holder})資訊</td>
							</tr>
							<tr>
								<td id="p2" style="display:none; text-align:left; padding-left:10px; padding-top:10px" align='left'><table>{$activity_holder}{$info}</table>
									
								</td>
							</tr>{$map}
							{$facebook_join}
							{$question}

							<tr><td>&nbsp;</td></tr>
						</table>
					</td>
				</tr>
EOD;
		}
}

$WEB_CONTENT .= "</table>";


include './include/db_close.php';
?>