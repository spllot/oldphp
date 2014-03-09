<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
require_once '../class/form.php';
require_once getcwd() . '/../class/facebook.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->propose][1])){exit("權限不足!!");}
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

$page = new Admin();
$no = Tools::parseInt2($_REQUEST["mno"], 0);
$tab = (($_REQUEST['tab'] != "") ? $_REQUEST['tab'] : 0);
$menu = array(
	'propose.php?tab=0' =>'本地團購',
	'propose.php?tab=1' =>'宅配團購',
	'propose.php?tab=2' =>'本地服務',
	'propose.php?tab=3' =>'宅配服務',
	'propose.php?tab=4' =>'商品粉絲',
	'propose.php?tab=5' =>'運輸服務',
	'propose.php?tab=6' =>'人力服務',
	'propose.php?tab=7' =>'活動服務',
);

$page->setHeading($menu, $tab);
include("../include/db_open.php");
$pics_counts = 0;
$maps_counts = 0;
if ($no > 0){
    $result=mysql_query("SELECT * FROM Product WHERE No = '$no'");
	if($data=mysql_fetch_array($result)){
		$used = (($data['Used'] == 1) ? "是" : "否");
		$sale = (($data['Sale'] == 1) ? "是" : "否");
		$cashflow = (($data['Cashflow'] == 1) ? "使用" : "不使用");
		$slide = "不使用";
		if($data['Slide'] == 1){
			$slide = "使用：<br>";
			$slide .= "圖二網址：" . $data['Slide2'] . "<br>";
			$slide .= "圖三網址：" . $data['Slide3'] . "<br>";
			$slide .= "圖四網址：" . $data['Slide4'] . "<br>";
		}
		$photos .=<<<EOD
			<div id='pic{$pics_counts}' style='float:left'><input type='hidden' name='pic' value="{$data['Photo']}">
				<img name='pics' src="../upload/thumb_{$data['Photo']}" style='width:396px; height:248px' >
			</div>
EOD;
			$pics_counts++;
		if($data['Map']!= ""){
			$maps .=<<<EOD
				<div id='map{$maps_counts}' style='float:left'><input type='hidden' name='map' value="{$data['Map']}">
					<img name='maps' src="../upload/thumb_{$data['Map']}"  style='width:488px; height:290px'>
				</div>
EOD;
			$maps_counts++;
		}
	}
}//if
$mode = (($data['Mode'] == 1) ? "團購商品" : "廉售商品");
$deliver = (($data['Deliver'] == 1) ? "宅配" : "到店");
switch($data['Receipt']){
	case 1:
		$receipt = "可以提供發票";
		break;
	case 2:
		$receipt = "可以提供收據";
		break;
	case 3:
		$receipt = "都無法提供";
		break;
}
$result = mysql_query("SELECT * FROM Catalog WHERE useFor = 'TYPE_AREA' AND No = '" . $data['Area'] . "'");
if($rs=mysql_fetch_array($result)){
	$area = $rs['Name'];
}
$result = mysql_query("SELECT * FROM Catalog WHERE useFor = 'TYPE_PRO' AND No = '" . $data['Catalog'] . "'");
if($rs=mysql_fetch_array($result)){
	$catalog = $rs['Name'];
}
$result = mysql_query("SELECT * FROM Catalog WHERE useFor = 'TYPE_COM' AND No = '" . $data['Type'] . "'");
if($rs=mysql_fetch_array($result)){
	$type = $rs['Name'];
}
$donate = "無";
$result = mysql_query("SELECT * FROM Donate WHERE  No = '" . $data['isDonate'] . "' ORDER BY Name");
if($rs=mysql_fetch_array($result)){
	$donate = $rs['Name'];
}


$daysonsale = (($data['Duration'] == "0") ? "不限時" : $data['daysOnSale']." 天");
$quota = (($data['Amount'] == "0") ? "不限量" : $data['Quota']);

$cashflow1 = (($data['Cashflow'] == "1") ? "" : "none");
$coupon = (($data['Cashflow'] == "1" && $data['Deliver'] == "0") ? "" : "none");
switch($data["Restrict"]){
	case 1:
		$restrict = "每人不限交易次數，購量與限用數量";
		break;
	case 2:
		$restrict = "每人不限交易次數與購量，但每人到店限用 {$data['maxUse']} 張";
		break;
	case 3:
		$restrict = "每人限制一次交易次數，限購 {$data['maxBuy']} 張，每人到店限用 {$data['maxUse']} 張";
		break;
	case 4:
		$restrict = "每人限制一次交易次數，限購 {$data['maxBuy']} 張，不限每人使用數量";
		break;
}
$holder = "商家";
$activity = "none";
if($data['Activity'] == 1){
//	$data['Price1'] = 0;
	$holder = "商家";
	$activity = "block";
	if($data['activity_draw'] == 1)
		$activity_draw = "採用系統隨機抽獎方式，抽獎結果將公佈本站與主辦FaceBook。";
	if($data['activity_draw'] == 2)
		$activity_draw = "由主辦單位決定抽獎方式及結果公佈方式。";
	$activity_yn = "商品粉絲抽獎活動";
	$not_activity = "none";
	$f = fetchUrl("https://graph.facebook.com/" . $data['activity_page']);
	$p = json_decode($f);
	$activity_page = '<div class="fb-like-box" data-href="' . $p->{'link'} . '" data-width="292" data-show-faces="false" data-stream="false" data-header="true">';
}

if($data['Transport'] == 1){
	$discount = $data['taxi_discount'] . "折";
	$taxi_hide = "none";
	$taxi_show = "block";
	$name="計程車名稱";
	$description="服務簡介";
	$pic="車輛圖示";
	$seller="車行或名號";
	$address="服務地址";
	$seller1 = "業者";
	$product = "服務";
}
else{
	$name="商品名稱";
	$description="商品簡介";
	$pic="商品圖示";
	$seller="業者名稱";
	$address="服務地址";
	$seller1 = "業者";
	$product = "商品";
	$taxi_hide = "block";
	$taxi_show = "none";

	if($data['price_mode'] == 0){
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
	}

}
$extra = ((strpos($type, "預約") > -1) ? "&nbsp;&nbsp;預約需在使用前：{$data['daysBeforeReserve']}{$data['daysUnit']}" : "");

$taxi_company = array("", "公司車行", "個人車行", "計程車合作社", "其他");
$taxi_sex = array("", "男性", "女性", "其他性別");



$content = <<<EOD
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/zh_TW/all.js#xfbml=1&appId=223714571074260";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<form name="iForm" method="post">
	<input type="hidden" name="area" value="{$_REQUEST['area']}">
	<input type="hidden" name="catalog" value="{$_REQUEST['catalog']}">
	<input type="hidden" name="type" value="{$_REQUEST['type']}">
	<input type="hidden" name="pageno" value="{$_REQUEST['pageno']}">
	<input type="hidden" name="keyword" value="{$_REQUEST['keyword']}">
	<input type="hidden" name="memberlist" value="{$_REQUEST['mno']}">
	<input type="hidden" name="tab" value="{$tab}">
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
		<td style="text-align:center" align="center">
			<div>
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">類別</td>
				</tr>
				<tr>
					<td>$mode</td>
				</tr>
			</table>
			</div>

			<div id="option">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">服務選項設定</td>
				</tr>
				<tr>
					<td style="text-align:left;">$deliver
					</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:left" id="deliver6">地區：{$area}&nbsp;&nbsp;分類：{$catalog}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$activity_yn}</td>
							</tr>
							<tr id="deliver0" style="display:{$not_activity}">
								<td style="text-align:left">類型：{$type}</td>
								<td style="text-align:left;">{$extra}</td>
							</tr>
							<tr style="display:{$taxi_hide}">
								<td>全新貨品販售：{$allnew}</td>
							</tr>
							<tr style="display:{$taxi_hide}">
								<td>中古貨販售：{$used}</td>
							</tr>
							<tr style="display:{$taxi_hide}">
								<td>即期貨販售：{$sale}</td>
							</tr>
							<tr>
								<td>站內金流：{$cashflow}</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<div id="donate" style="display:none">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">愛心商品</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:left">愛心帳號：$donate</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<div id="sale" style="display:{$taxi_hide}">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">服務資訊設定</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:right">原價：</td><td style="text-align:left">{$data['Price']}</td>
								<td style="text-align:right">售價：</td><td style="text-align:left">{$data['Price1']}</td>
								<td style="text-align:right">折扣：</td><td style="text-align:left">{$discount}</td>
							</tr>
							<tr style="display:{$cashflow1}">
								<td style="text-align:right">販售時間：</td><td colspan="3" style="text-align:left">{$daysonsale}</td>
							</tr>
							<tr style="display:{$cashflow1}">
								<td style="text-align:right">可販售總量：</td><td colspan="3" style="text-align:left">{$quota}</td>
							</tr>
							<tr style="display:{$activity}">
								<td style="text-align:right">粉絲團：</td><td colspan="3" style="text-align:left">{$activity_page}</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<div id="taxi" style="display:{$taxi_show}">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">服務資訊設定</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:right">優惠方案：</td><td colspan="3" style="text-align:left">{$discount}</td>
							</tr>
							<tr>
								<td style="text-align:right">商家車行：</td><td colspan="3" style="text-align:left">{$taxi_company[$data['taxi_company']]}</td>
							</tr>
							<tr>
								<td style="text-align:right">車牌號碼：</td><td style="text-align:left">{$data['taxi_plate']}</td>
								<td style="text-align:right; padding-left:40px">車齡：</td><td style="text-align:left">{$data['taxi_age']}年</td>
							</tr>
							<tr>
								<td style="text-align:right">駕駛人性別：</td><td colspan="3" style="text-align:left">{$taxi_sex[$data['taxi_sex']]}</td>
							</tr>
							<tr>
								<td style="text-align:right">車行年資：</td><td colspan="3" style="text-align:left">{$data['taxi_exp']}年</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<div id="subject">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">{$product}標題說明設定</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:right" nowrap>{$name}：</td>
								<td style="text-align:left">【{$data['Name']}】</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">{$description}：</td>
								<td style="text-align:left"><textarea name="description" style="width:450px; height:150px" maxlength=200>{$data['Description']}</textarea></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">{$pic}：</td>
								<td style="text-align:left"><div id="pics">{$photos}</div>
								</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">服務說明頁面圖框設定四圖檔輪播功能：</td>
								<td style="text-align:left">{$slide}
								</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<div id="activity" style="display:{$activity}">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">活動參與須知</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:right" nowrap>活動期間：</td>
								<td style="text-align:left">{$data['activity_start']} 至 {$data['activity_end']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>公佈日期：</td>
								<td style="text-align:left">{$data['activity_ann']}</td>
							</tr>
							<tr>
								<td style="text-align:left" nowrap colspan="2">抽獎數量：{$data['activity_quota']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>抽獎方式：</td>
								<td style="text-align:left">
										若沒有超過最低 {$data['activity_min']}人門檻，則不提供獎項。<br>
										參加人數每超過 {$data['activity_per']} 人，就提供一個抽獎獎項，直到抽獎數量用完為止。<br>
										
								</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>抽獎說明：</td>
								<td style="text-align:left">{$data['activity_info']}</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<div id="coupon" style="display:{$coupon}">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">憑證使用設定</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:right" nowrap>兌換期間：</td>
								<td style="text-align:left">{$data['dateValidate']} 至 {$data['dateExpire']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>使用時段：</td>
								<td style="text-align:left">{$data['Hours']}</td>
							</tr>
							<tr>
								<td style="text-align:left" nowrap colspan="2">購買與使用張數：</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left">{$restrict}
								</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>其他說明：</td>
								<td style="text-align:left">{$data['Memo']}</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<div id="special" style="display:none">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">好康特色/商品資料說明</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:right;width:100px" nowrap valign="top">(1).</td>
								<td style="text-align:left">{$data['Special1']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">(2).</td>
								<td style="text-align:left">{$data['Special2']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">(3).</td>
								<td style="text-align:left">{$data['Special3']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">(4).</td>
								<td style="text-align:left">{$data['Special4']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">(5).</td>
								<td style="text-align:left">{$data['Special5']}</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<div id="info">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">服務&資訊說明</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td>{$data['Intro']}</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<div id="seller">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">{$seller1}($holder)資訊說明</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr style="display:{$activity}">
								<td style="text-align:right" nowrap>主辦單位名稱：</td>
								<td style="text-align:left">{$data['activity_holder']}</td>
							</tr>
							<tr style="display:{$activity}">
								<td style="text-align:right" nowrap>主辦單位郵件：</td>
								<td style="text-align:left">{$data['activity_email']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>{$seller}：</td>
								<td style="text-align:left">{$data['Seller']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>業者網站：</td>
								<td style="text-align:left">{$data['Url']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>聯絡電話：</td>
								<td style="text-align:left">{$data['Phone']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>發票或收據：</td>
								<td style="text-align:left">$receipt
								</td>
							</tr>
							<tr id="deliver1">
								<td style="text-align:right" nowrap>營業時間：</td>
								<td style="text-align:left">{$data['openHours']}</td>
							</tr>
							<tr id="deliver2">
								<td style="text-align:right" nowrap>{$address}：</td>
								<td style="text-align:left">{$data['Address']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>其他資訊：</td>
								<td style="text-align:left"><textarea name="about" style="width:450px; height:200px">{$data['About']}</textarea></td>
							</tr>
						</table>

						</fieldset>
					</td>
				</tr>
			</table>
			</div>
		</td>
	</tr>
</table>

</form>
EOD;



$content=<<<EOD
<table width="100%">
	<tr>
		<td align="left">
			<input type="button" value="下架" onClick="Delete();">
			<input type="button" value="退回" onClick="Refuse();">
			<input type="button" value="通過" onClick="Approve();">
			<input type="button" value="回上一頁" onClick="Back();">
		</td>
	</tr>
	<tr>
		<td align="left">$content</td>
	</tr>
	<tr>
		<td align="left">
			<input type="button" value="下架" onClick="Delete();">
			<input type="button" value="退回" onClick="Refuse();">
			<input type="button" value="通過" onClick="Approve();">
			<input type="button" value="回上一頁" onClick="Back();">
		</td>
	</tr>
</table>
EOD;
$page->addContent($content);
$page->show();




									
									
									
include("../include/db_close.php");
?>
<script language="javascript">
function Delete(){
	if(confirm("確定要下架?")){
		document.iForm.action="propose_delete.php";
		document.iForm.submit();
	}
}

function Refuse1(){
	document.iForm.action="propose_refuse.php";
	document.iForm.submit();
}

function Refuse(){
	var w = window.showModalDialog("propose_refuse_setreason.php", iForm.memberlist.value, 'status:no');
	if(w){
		window.location.href="propose.php?tab=<?=$_REQUEST['tab']?>&pageno=<?=$_REQUEST['pageno']?>&area=<?=$_REQUEST['area']?>&type=<?=$_REQUEST['type']?>&catalog=<?=$_REQUEST['catalog']?>&keyword=<?=$_REQUEST['keyword']?>";
	}
}

function Approve(){
	document.iForm.action="propose_approve.php";
	document.iForm.submit();
}

function Back(){
	document.iForm.action="propose.php";
	document.iForm.submit();
}

</script>