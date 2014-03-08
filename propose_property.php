<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
require_once '../class/form.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->propose][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["mno"], 0);
$tab = (($_REQUEST['tab'] != "") ? $_REQUEST['tab'] : 0);
$menu = array(
	'propose.php?tab=0' =>'到店團購商品審核',
	'propose.php?tab=1' =>'宅配團購商品審核',
	'propose.php?tab=2' =>'到店廉售商品審核',
	'propose.php?tab=3' =>'宅配廉售商品審核',
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
		$cashflow = (($data['Cashflow'] == 1) ? "不使用" : "使用");
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


$extra = ((strpos($type, "預約") > -1) ? "&nbsp;&nbsp;預約需在使用前：{$data['daysBeforeReserve']}{$data['daysUnit']}" : "");
$content = <<<EOD
<form name="iForm" method="post">
	<input type="hidden" name="area" value="{$_REQUEST['area']}">
	<input type="hidden" name="catalog" value="{$_REQUEST['catalog']}">
	<input type="hidden" name="type" value="{$_REQUEST['type']}">
	<input type="hidden" name="pageno" value="{$_REQUEST['pageno']}">
	<input type="hidden" name="keyword" value="{$_REQUEST['keyword']}">
	<input type="hidden" name="memberlist" value="{$_REQUEST['mno']}">
	<input type="hidden" name="tab" value="{$tab}">
</form>
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
								<td style="text-align:left" id="deliver6">地區：{$area}分類：{$catalog}</td>
							</tr>
							<tr id="deliver0">
								<td style="text-align:left">類型：{$type}</td>
								<td style="text-align:left;">{$extra}</td>
							</tr>
							<tr>
								<td>中古貨販售：{$used}</td>
							</tr>
							<tr>
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
			<div id="donate">
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
			<div id="sale">
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
								<td style="text-align:right">售價</td><td style="text-align:left">{$data['Price1']}折</td>
								<td style="text-align:right">折扣：</td><td style="text-align:left">{$data['Discount']}折</td>
							</tr>
							<tr style="display:none">
								<td style="text-align:right">販售時間：</td><td colspan="3" style="text-align:left">{$data['daysOnSale']}天</td>
							</tr>
							<tr style="display:none">
								<td style="text-align:right">可販售總量：</td><td colspan="3" style="text-align:left">{$data['Quota']}</td>
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
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">服務標題說明設定</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:right" nowrap>商品名稱：</td>
								<td style="text-align:left">【{$data['Name']}】</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">商品簡介：</td>
								<td style="text-align:left"><textarea name="description" style="width:450px; height:150px" maxlength=200>{$data['Description']}</textarea></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">商品圖示：</td>
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
			<div id="coupon" style="display:none">
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
								<td style="text-align:left">{$data['dateValidate']}至{$data['dateExpire']}</td>
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
								<td style="text-align:left">
									<input type="radio" name="restrict" value="1">每人不限交易次數，購量與限用數量<br>
									<input type="radio" name="restrict" value="2">每人不限交易次數與購量，但每人到店限用<input type="text" name="use2" style="width:40px">張<br>
									<input type="radio" name="restrict" value="3">每人限制一次交易次數，限購<input type="text" name="buy3" style="width:40px">張，每人到店限用<input type="text" name="use3" style="width:40px">張
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
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">商品資訊介紹</td>
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
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">業者(商家)資訊說明</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:right" nowrap>業者/商家：</td>
								<td style="text-align:left">{$data['Seller']}</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>網站：</td>
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
							<tr>
								<td style="text-align:right" nowrap>賣家自我介紹：</td>
								<td style="text-align:left"><textarea name="about" style="width:450px; height:200px">{$data['About']}</textarea></td>
							</tr>
							<tr id="deliver1">
								<td style="text-align:right" nowrap>營業時間：</td>
								<td style="text-align:left">{$data['openHours']}</td>
							</tr>
							<tr id="deliver2">
								<td style="text-align:right" nowrap>地址：</td>
								<td style="text-align:left">{$data['Address']}</td>
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

function Refuse(){
	document.iForm.action="propose_refuse.php";
	document.iForm.submit();
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