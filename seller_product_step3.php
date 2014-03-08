<?php
include './include/session.php';
require_once './class/javascript.php';
require_once getcwd() . '/class/facebook.php';

if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}


$fb_login = '<fb:login-button autologoutlink="true" scope="manage_pages"></fb:login-button></p>';
//print_r($me);

/*
if($me){
	$fb_logout = '<fb:login-button autologoutlink="true"></fb:login-button>';
	$fb_login = <<<EOD
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>&nbsp;</td>
				<td style="color:white;font-size:13px;text-align:right"><img src="https://graph.facebook.com/{$fb_uid}/picture"></td>
				<td style="width:10px"></td>
				<td valign="bottom" style="color:white; font-size:10pt"><div style="text-align:left; overflow:hidden">{$me['name']}</div>$fb_logout</td>
			</tr>
		</table>
EOD;
}
*/



$no = $_REQUEST['no'];
$option = $_REQUEST['option'];

$pageno = $_REQUEST['pageno'];
$pics_counts = 0;
$maps_counts = 0;
$_SESSION['cashflow'] = 1;
$_SESSION['PRODUCT'] = $no;
$_SESSION['UPLOAD_COUNTS'] = 0;
$op = "none";
$price_mode0 = " CHECKED";

include './include/db_open.php';
$result = mysql_query("SELECT * FROM Config WHERE 1=1");
while($rs=mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}

$result = mysql_query("SELECT * FROM logUpload WHERE Member='" . $_SESSION['member']['No'] . "' AND Product = 0");
while($rs = mysql_fetch_array($result)){
	mysql_query("DELETE FROM logUpload  WHERE Member='" . $_SESSION['member']['No'] . "' AND No = '" . $rs['No'] . "'");
	@unlink("./upload/" . $rs['Path']);
}

$data['activity_email'] = $_SESSION['member']['userID'];
if($no != "" && $no>0){
	$agree = " CHECKED";
	$sql = "SELECT *, IFNULL((SELECT COUNT(*) FROM logUpload WHERE logUpload.Product=Product.No), 0) AS UPLOAD_COUNTS FROM Product WHERE No = '$no' AND Member='" . $_SESSION['member']['No'] . "'";
	$result = mysql_query($sql) or die(mysql_error());
	if($data=mysql_fetch_array($result)){
		$used = (($data['Used'] == 1) ? " checked" : "");
		$sale = (($data['Sale'] == 1) ? " checked" : "");
		$allnew = (($data['Allnew'] == 1) ? " checked" : "");
		$taxi_discount = (($data['taxi_discount'] != "") ? $data['taxi_discount'] : "");
		$taxi_company1 = (($data['taxi_company'] == 1) ? " checked" : "");
		$taxi_company2 = (($data['taxi_company'] == 2) ? " checked" : "");
		$taxi_company3 = (($data['taxi_company'] == 3) ? " checked" : "");
		$taxi_company4 = (($data['taxi_company'] == 4) ? " checked" : "");
		if($option == 5){
		}
			$employer0 = (($data['employer'] == 0) ? " checked" : "");
			$employer1 = (($data['employer'] == 1) ? " checked" : "");

		$taxi_sex1 = (($data['taxi_sex'] == 1) ? " checked" : "");
		$taxi_sex2 = (($data['taxi_sex'] == 2) ? " checked" : "");
		$taxi_sex3 = (($data['taxi_sex'] == 3) ? " checked" : "");
		$_SESSION['cashflow'] = $data['Cashflow'];
		$op = (($data['Cashflow'] == 1) ? "none" : "");
		$_SESSION['UPLOAD_COUNTS'] = $data['UPLOAD_COUNTS'];
		$price_mode0 = (($data['price_mode'] == 0) ? " CHECKED" : "");
		$price_mode1 = (($data['price_mode'] == 1) ? " CHECKED" : "");
		if($data['Transport'] == 1) {$option = 4;}
		if($data['hr'] == 1) {$option = 5;}
		if($data['event'] == 1) {$option = 6;}


		$photos = <<<EOD
			<div id='pic{$pics_counts}' style='float:left'><input type='hidden' name='pic' value="{$data['Photo']}">
				<img name='pics' src="./upload/thumb_{$data['Photo']}" style="width:396px; height:248px">
			</div>
EOD;
			$pics_counts++;
		if($data['Map']!= ""){
			$maps .=<<<EOD
				<div id='map{$maps_counts}' style='float:left'><input type='hidden' name='map' value="{$data['Map']}">
					<img name='maps' src="./upload/thumb_{$data['Map']}" style='width:480px; height:240px' >
				</div>
EOD;
			$maps_counts++;
		}
	}
}
else{
	$data['dateValidate'] = date('Y-m-d');
}


$subject2 = "商品名稱";
$description = "商品簡介";
$subject3 = "商品圖示";

switch($option){
	case 2:
		$o_title = "本地/宅配服務_商品建置";
		$usefor= "TYPE_PRO";
		break;
	case 3:
		$o_title = "本地/宅配服務_粉絲推廣服務建置";
		$usefor= "TYPE_PRO";
		$data['Activity'] = 1;
		break;
	case 4:
		$o_title = "本地服務_即時運輸/共乘服務建置";
		$usefor= "TYPE_TPT";
		$data['Transport'] = 1;
		$o_display = "none";
		break;
	case 5:
		$o_title = "本地服務_即時人力服務建置";
		$usefor= "TYPE_JOB";
		$data['hr'] = 1;
		$o_display = "none";
		$subject2 = "人力服務名稱";
		$description = "人力服務簡介";
		$subject3 = "人力服務圖示";
		$o_memo = '<div style="float:left; color:gray">(如無上傳圖片，則顯示本站公用圖片)</div>';
		if($no == ""){
				$photos = <<<EOD
			<div id='pic{$pics_counts}' style='float:left'><input type='hidden' name='pic' value="product_0000000000005.JPG">
				<img name='pics' src="./upload/thumb_product_0000000000005.JPG" style="width:396px; height:248px">
			</div>
EOD;
		}
		break;
	case 6:
		$o_title = "本地服務_即時活動服務建置";
		$usefor= "TYPE_ACT";
		$data['event'] = 1;
		$o_display = "none";
		$subject2 = "活動服務名稱";
		$description = "活動服務簡介";
		$subject3 = "活動服務圖示";
		$o_memo = '<div style="float:left; color:gray">(如無上傳圖片，則顯示本站公用圖片)</div>';
		if($no == ""){
				$photos = <<<EOD
			<div id='pic{$pics_counts}' style='float:left'><input type='hidden' name='pic' value="product_0000000000006.JPG">
				<img name='pics' src="./upload/thumb_product_0000000000006.JPG" style="width:396px; height:248px">
			</div>
EOD;
		}
		break;
	default:
		$o_title = "本地/宅配服務_商品建置";
		$usefor= "TYPE_PRO";
		break;
}


$_SESSION['cashflow'] = 0;/*關閉金流*/
$cashflow0 = (($_SESSION['cashflow'] == 0) ? " checked" : "");
$cashflow1 = (($_SESSION['cashflow'] == 1) ? " checked" : "");
$activity_draw1 = (($data['activity_draw'] == 1) ? " checked" : "");
$activity_draw2 = (($data['activity_draw'] == 2) ? " checked" : "");


	$catalog_list2 = "";
	if($data['Catalog'] > 0){
		$result1 = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_PRO' AND Parent='{$data['Catalog']}' ORDER BY Sort");
		while($rs1=mysql_fetch_array($result1)){
			$catalog_list2 .= "<option value='" . $rs1['No'] . "'" . (($data['Catalog2'] == $rs1["No"] ) ? " SELECTED" : "") . ">" . $rs1["Name"] . "</option>";
		}
	}
	$catalog_list3 = "";
	if($data['Catalog2'] > 0){
		$result1 = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_PRO' AND Parent='{$data['Catalog2']}' ORDER BY Sort");
		while($rs1=mysql_fetch_array($result1)){
			$catalog_list3 .= "<option value='" . $rs1['No'] . "'" . (($data['Catalog3'] == $rs1["No"] ) ? " SELECTED" : "") . ">" . $rs1["Name"] . "</option>";
		}
	}




include './include/db_close.php';

include 'seller_product_tab.php';


$btns = <<<EOD
	<input type="button" class="btn" value="設定存成草稿" onClick="Draft();">
	<input type="button" class="btn" value="預覽草稿" onClick="Preview();">
	<input type="button" class="btn" value="送出審核/更新" onClick="Save();">
EOD;

if(in_array($data['Status'], array(1, 2))){
	$btns = "<input type=\"button\" class=\"btn\" value=\"上一頁\" onClick=\"window.location.href='seller_product.php?mode=2&deliver={$data['Deliver']}'\">";
}
$slide = (($data['Slide'] == 1) ? " CHECKED" : "");

$fans = "<input type='checkbox' name='activity' value='1' onClick='setFans();'" . (($data['Activity'] == 1) ? " CHECKED":"") . " >商品粉絲抽獎活動";
$fans .= "&nbsp;&nbsp;&nbsp;&nbsp;";
$fans .= "<input type='checkbox' name='transport' value='1' onClick='setTransport();'" . (($data['Transport'] == 1) ? " CHECKED":"") . ">運輸服務(含計程車)";

$transport_check_cashflow = "iForm.cashflow[0].disabled = false;";
if($_SESSION['member']['Seller'] != 2){// || $_CONFIG['cashflow'] == "N"){
	$dis_cashflow = " disabled";
	$cashflow1 = "";
	$transport_check_cashflow = "";
}


$WEB_CONTENT = <<<EOD

		<script language="javascript" src="./ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="./ckeditor/adapters/jquery.js"></script>	
		<script language="javascript" src="./ckfinder/ckfinder.js"></script>
		<LINK href="./ckeditor/_samples/sample.css" rel="stylesheet" type="text/css">
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
/**/
		  FB.Event.subscribe('auth.login', function() {
			//window.location.reload();
			//$("#facebook_page").load("facebook_page.php?page={$data['activity_page']}");
		  });
		  FB.Event.subscribe('auth.logout', function() {
			//window.location.reload();
			//$("#facebook_page").load("facebook_page.php?page={$data['activity_page']}");
		  });
 
		};
        (function(d){
           var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
           js = d.createElement('script'); js.id = id; js.async = true;
           js.src = "//connect.facebook.net/zh_TW/all.js";
           d.getElementsByTagName('head')[0].appendChild(js);
         }(document));
      </script>    

<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td style="text-align:center" align="center">
			<form name="iForm" method="post" action="sller_product_step3_save.php" target="iAction">
			<input type="hidden" name="status" value="">
			<input type="hidden" name="no" value="$no">
			<div>
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">{$o_title}</td>
				</tr>
				<tr>
					<td style="text-align:left"><input type="checkbox" name="agree" value="1"{$agree}>我已閱讀並願意遵守<a href="javascript:parent.Dialog('policy2.php')">電子商務服務條款</a>
					</td>
				</tr>
			</table>
			</div>

			<div id="option">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">服務選項設定</td>
				</tr>
				<tr>
					<td style="text-align:left;display:{$o_display}">
						<input type="radio" name="deliver" value="0" checked onClick="setDeliver();">本地 (鄰近面交/鄰近外送/遞送到府，…)
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="deliver" value="1" onClick="setDeliver();">宅配
					</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr style="display:none">
								<td colspan="2" style="color:gray; text-align:left">[註]. 計程車服務選項建議：[分類]>>運輸/車輛/車品; [類型]>>即時消費型商品。</td>
							</tr>
							<tr>
								<td style="text-align:left; display:none">地區：<select name="area"><option value="">請選擇</option></select></td>
								<td style="text-align:left" colspan="2">分類：<select name="catalog" style="width:150px" onChange="getCat2();"><option value="">請選擇</option></select>
									<select id="catalog2" name="catalog2" style="width:150px" onChange="getCat3();" disabled><option value="">請選擇</option>{$catalog_list2}</select>
									<select id="catalog3" name="catalog3" style="width:150px" disabled><option value="">請選擇</option>{$catalog_list3}</select>
					<script language='javascript'>
						function getCat2(){
							$("#catalog2").html('<option value="">請選擇</option>');
							$("#catalog3").html('<option value="">請選擇</option>');
							document.iForm.catalog3.disabled = true;
							$.post(
								'get_catalog.php',
								{
									no: document.iForm.catalog.value
								},
								function(data)
								{
									$("#catalog2").html('<option value="">請選擇</option>' + data);
									if(document.iForm.catalog2.options.length > 1){
										document.iForm.catalog2.disabled = false;
									}
									else{
										document.iForm.catalog2.disabled = true;
									}
								}
							);	
						}
						function getCat3(){
							$("#catalog3").html('<option value="">請選擇</option>');
							$.post(
								'get_catalog.php',
								{
									no: document.iForm.catalog2.value
								},
								function(data)
								{
									$("#catalog3").html('<option value="">請選擇</option>' + data);
									if(document.iForm.catalog3.options.length > 1){
										document.iForm.catalog3.disabled = false;
									}
									else{
										document.iForm.catalog3.disabled = true;
									}
								}
							);	
						}
					</script>
								</td>
							</tr>
							<tr id="deliver7">
								<td style="text-align:left">服務模式：<select name="type" onChange="setDaysBeforeReserve(this);" style="width:150px"><option value="">請選擇</option></select></td>
								<td style="text-align:left;display:none" id="daysbeforereserve"><!--&nbsp;&nbsp;預約需在使用前：<input type="text" name="daysbeforereserve" style="width:30px" value="{$data['daysBeforeReserve']}">天--></td>
							</tr>
							<tr style="display:none">
								<td colspan="2" style="text-align:left; padding-top:10px; padding-bottom:10px">{$fans}
								<input type="hidden" name="hr" value="{$data['hr']}">
								<input type="hidden" name="event" value="{$data['event']}">
								</td>
							</tr>
							<tr style="display:{$o_display}">
								<td colspan="2" style="text-align:left">
								
								<table align="left">
									<tr id="cashflow1">
										<td><input type="radio" value="1" name="cashflow" onClick="setOption();"{$cashflow1}{$dis_cashflow}  disabled><td>
										<td>使用站內金流及服務系統<td>
									</tr>
									<tr id="cashflow2">
										<td><input type="radio" value="0" name="cashflow" onClick="setOption();"{$cashflow0}><td>
										<td>不使用站內金流及服務系統<td>
									</tr>
									<tr id="op" style="display:{$op}">
										<td><td>
										<td>
											<table>
												<tr>
													<td style="text-align:left" nowrap><input type="checkbox" value="1" name="allnew"{$allnew} onClick="chkNew();">全新貨品</td>	
													<td style="text-align:left" nowrap><input type="checkbox" value="1" name="used"{$used} onClick="chkUsed();">中古貨販售</td>											
													<td style="text-align:left" nowrap><input type="checkbox" value="1" name="sale"{$sale} onClick="chkSale();">即期貨販售</td>
													<td style="text-align:left"></td>
												</tr>
											</table>
										<td>
									</tr>
								</table>
								</td>
							</tr>
						</table>
						<script language="javascript">
			var iForm = document.iForm;
			function setTransport(){
				if(iForm.transport.checked){
					iForm.cashflow[0].disabled = true;
					iForm.cashflow[1].checked = true;
					iForm.allnew.checked = false;
					iForm.used.checked = false;
					iForm.sale.checked = false;
					$("#op").hide();
					$("#taxi").show();
					$("#sale").hide();
					$("#subject1").html("服務標題說明設定");
					$("#subject2").html("運輸服務名稱");
					$("#description").html("服務簡介");
					$("#subject3").html("車輛圖示");
					$("#info1").html("服務&資訊說明");
					$("#seller1").html("業者");
					$("#seller2").html("車行或名號");
					$("#seller3").html("請填寫車行名稱，無車行者可填寫個人名號。");
					$("#seller4").html("服務地址：");
					$("#seller5").show();
					$("#seller6").hide();
					$("#phone_memo").hide();
				}
				else{		
					{$transport_check_cashflow}
					$("#op").show();
					$("#taxi").hide();
					$("#sale").show();
					$("#subject1").html("服務標題說明設定");
					$("#subject2").html("{$subject2}");
					$("#description").html("{$description}");
					$("#subject3").html("{$subject3}");
					$("#info1").html("服務&資訊說明");
					$("#seller1").html("業者");
					$("#seller2").html("業者名稱");
					$("#seller3").html("業者若無店面，可以不需填寫。");
					$("#seller4").html("服務地址：<br>(面交地點)&nbsp;&nbsp;");
					$("#seller5").hide();
					$("#seller6").show();
					$("#phone_memo").show();
				}
			}

			function setFans(){
				if(iForm.activity.checked){
					$(".activity").show();
					$("#activity").show();
					$("#setting").html("服務資訊");
					$("#description").html("活動簡介");
					$("#activity_seller").html("商家");
					$("#coupon").hide();
					$("#special").hide();
					$(".cashflow0").hide();
					$("#cashflow1").hide();
					$("#cashflow2").hide();
					$("#deliver7").show();
					iForm.cashflow[1].checked=true;
					setOption();
				}
				else{
					$("#cashflow1").show();
					$(".activity").hide();
					$("#activity").hide();
					$("#setting").html("服務資訊");
					$("#description").html("商品簡介");
					$("#activity_seller").html("商家");
					setOption();
					/*
					*/
					if(iForm.deliver[0].checked){
						$("#deliver7").show();
					}
					else{
						$("#deliver7").hide();
					}
				}
			}
			function setDeliver(){
				if(iForm.deliver[0].checked){
					iForm.transport.disabled = false;
					if(iForm.cashflow[0].checked){
						$("#coupon").show();
						$(".cashflow0").hide();
					}
					else{
						$("#coupon").hide();
						$(".cashflow0").hide();
					}
					$("#deliver1").show();
					$("#deliver2").show();
					$("#deliver6").show();
					$("#deliver0").show();

					$("#deliver7").show();
					/*
					if(iForm.activity.checked){
						$("#deliver7").hide();
					}
					else{
						$("#deliver7").show();
					}
					*/

				}
				else if(iForm.deliver[1].checked){
					iForm.transport.checked = false;
					iForm.transport.disabled = true;
					if(iForm.cashflow[0].checked){
						$(".cashflow0").show();
					}
					else{
						$(".cashflow0").hide();
					}
					$("#coupon").hide();
					$("#deliver1").hide();
					$("#deliver2").hide();
					$("#deliver6").hide();
					$("#deliver0").hide();
					$("#deliver7").hide();
				}
			}
			function chkCashFlow(){
				if(iForm.used.checked){
					if(!iForm.cashflow.checked)
						iForm.cashflow.checked = true;
					iForm.sale.checked = false;
				}
			}
			function chkSale(){
				if(iForm.sale.checked){
					iForm.used.checked = false;
					iForm.allnew.checked = false;
				}
			}
			function chkNew(){
				if(iForm.allnew.checked){
					iForm.sale.checked = false;
					iForm.used.checked = false;
				}
			}
			function chkUsed(){
				if(iForm.used.checked){
					iForm.sale.checked = false;
					iForm.allnew.checked = false;
				}
			}
			function setDaysBeforeReserve(){
				/*
				var x=iForm.type;
				if(x.value && x.options[x.options.selectedIndex].text.indexOf("預約") > -1){
					$("#daysbeforereserve").show()
				}
				else{
					$("#daysbeforereserve").hide();
					iForm.daysbeforereserve.value = "";
				}
				*/
			}
			function setOption(){
				var c = "";
				if(iForm.cashflow[0].checked){
					$("#op").hide();
					c = 1;
					$(".cashflow").show();
					if(iForm.deliver[0].checked){
						$("#coupon").show();
						$(".cashflow0").hide();
					}
					else{
						$("#coupon").hide();
						$(".cashflow0").show();
					}
				}
				else{
					$(".cashflow0").hide();
					$("#coupon").hide()
					$("#op").show();
					c = 0;
					$(".cashflow").hide();
				}
				$.post(
					'set_cashflow.php',
					{
						cashflow:c
					},
					function(data)
					{
						if(data){
							alert(data);
						}
					}
				);
			}
						</script>
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
					<td style="text-align:left;">
						<input type="checkbox" name="isdonate" value="0" onClick="setDonate();">愛心義賣商品，請點選此項目
					</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:left">愛心帳號：<select name="donate"><option value="">請選擇</option></select></td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>

			<script language="javascript">
			function setDonate(){
				if(iForm.used.checked){
					if(!iForm.cashflow.checked)
					iForm.cashflow.checked = true;
				}
			}
			</script>
			<div id="sale">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom"><span id="setting">服務資訊</span>設定</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table border=0>
							<tr>
								<td colspan="7">
								<input type="radio" name="price_mode" value="0"{$price_mode0} onClick="setPriceMode();">數字標價
								&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="radio" name="price_mode" value="1"{$price_mode1} onClick="setPriceMode();">文字說明標示
								</td>
							</tr>
							<tr class="price_mode0" style="display:none">
								<td style="text-align:left; padding-left:20px" nowrap colspan="7">原價：
								<input type="text" name="price" style="width:50px" value="{$data['Price']}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;賣價：<input type="text" name="price1" style="width:50px" value="{$data['Price1']}">
								<span style="display:none">折扣：
								<input type="text" name="discount" style="width:50px" value="{$data['Discount']}">折</span>
							</tr>
							<tr class="price_mode0" style="display:none">
								<td style="color:gray; padding-left:20px" colspan="7">
									<table>
										<tr>
											<td style="vertial-align:top; text-align:right; font-size:11pt; color:gray" align="right" valign="top" nowrap>[註]:</td>
											<td style="text-align:left; font-size:11pt; color:gray" align="left">建議提供比平日原價更為優惠之賣價回饋消費者。當原價與賣價相同時，前台商品將不標示折扣數。</td>
										</tr>
									</table>

								 </td>
							</tr>
							<tr class="price_mode1" style="display:none">
								<td style="text-align:left; padding-left:20px" nowrap colspan="7">計價方式：<input type="text" name="price_info" style="width:120px" value="{$data['price_info']}" maxlength="6"></td>
							</tr>
							<tr class="price_mode1" style="display:none">
								<td style="color:gray; padding-left:20px" colspan="6">
									<table>
										<tr>
											<td style="vertial-align:top; text-align:right; font-size:11pt; color:gray" align="right" valign="top" nowrap>[註]:</td>
											<td style="text-align:left; font-size:11pt; color:gray" align="left">限填入六個文字，Ex.隨意打賞、$250/時、每日一千元、…</td>
										</tr>
									</table>
								 </td>
							</tr>
							<tr id="employer" style="display:none">
								<td colspan="7">
								<input type="radio" name="employer" value="0"{$employer0}>我是應徵者
								&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="radio" name="employer" value="1"{$employer1}>我是雇用者
								</td>
							</tr>
							<script language='javascript'>
								function setPriceMode(){
									if(document.iForm.price_mode[0].checked){
										$(".price_mode0").show();
										$(".price_mode1").hide();
									}
									if(document.iForm.price_mode[1].checked){
										$(".price_mode0").hide();
										$(".price_mode1").show();
									}
								}
							</script>
							<tr class="activity">
								<td style="text-align:left" colspan="6">
									<div style="float:left">選擇我的FaceBook粉絲團：</div>
									<div style="float:left" id="facebook_page">{$data['activity_name']}</div>
									<div>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="請選擇…" onCLick="selectPage();">
										<input type="hidden" name="activity_page" id="activity_page" value="{$data['activity_page']}">
										<input type="hidden" name="activity_name" id="activity_name" value="{$data['activity_name']}">
									</div>
								</td>
							</tr>
							<tr class="cashflow0">
								<td style="text-align:right" nowrap>販售時間：</td><td colspan="3" style="text-align:left">
									<input type="radio" name="duration" value=0>不限時</td>
							</tr>
							<tr class="cashflow0">
								<td style="text-align:right"></td><td colspan="3" style="text-align:left">
									<input type="radio" name="duration" value=1><input type="text" name="daysonsale" style="width:50px" value="">天</td>
							</tr>
							<tr class="cashflow0">
								<td></td>
								<td colspan="3" style="text-align:left">
									<Table align="left" border=0>
										<tr>
											<td valign="top" width=45 style="color:gray">[說明]:</td>
											<td valign="top" style="color:gray">廉售”宅配”商品販售時間可以選擇”不限期”(意即商品不下架), 也可以選擇設定販售天數, 設定之販售天數將自商品審核通過日起算, 最長販售時間為 180 天, 最短為 30 天; 商品達到販售時間之設定, 則商品將會自動下架。</td>
										</tR>
										<tr style="display:none">
											<td valign="top" width=45 style="color:gray">[說明]:</td>
											<td valign="top" width=20 style="color:gray">(1)</td>
											<td valign="top">達到廉售商品販售時間之設定, 則商品將會自動下架。</td>
										</tR>
										<tr style="display:none">
											<td></td>
											<td valign="top" width=20 style="color:gray">(2)</td>
											<td valign="top" style="color:gray">廉售商品設定販售天數, 自商品審核通過日起算, 最長販售時間為 180 天, 最短為 30 天。</td>
										</tR>
									</table>	
								</td>
							</tr>
							<tr class="cashflow">
								<td style="text-align:right">販售總量：</td><td colspan="3" style="text-align:left">
									<input type="radio" name="amount" value=0>不限量</td>
							</tr>
							<tr class="cashflow">
								<td style="text-align:right"></td><td colspan="3" style="text-align:left">
									<input type="radio" name="amount" value=1><input type="text" name="quota" style="width:50px" value=""></td>
							</tr>
							<tr class="cashflow">
								<td style="text-align:right"></td><td colspan="3" style="text-align:left; color:gray">
									[說明]: 達到商品販售之數量, 則商品將會自動下架。</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
				<tr style="display:none">
					<td style="text-align:left">
					<table>
						<tr>
							<td style="vertial-align:top; text-align:right; font-size:11pt" align="right" valign="top" nowrap style="color:gray">[說明]</td>
							<td style="text-align:left; font-size:11pt" align="left" style="color:gray">務請賣家知悉團購門檻量已預設定為0，故沒有團購失敗的運作，團購案一經<br>開啟，所購得知商品即可依消費[類型]之定義使用。</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			</div>
			<div id="taxi" style="display:none">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">服務資訊設定</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:right" nowrap>計價方式：</td>
								<td style="text-align:left" valign="top"><input type="text" name="taxi_discount" maxlength="6" style="width:120px" value="{$taxi_discount}"></td>
							</tr>
							<tr>
								<td></td>
								<td style="text-align:left">
									<table>
										<tr>
											<td style="vertial-align:top; text-align:right; font-size:11pt; color:gray" align="right" valign="top" nowrap>[註]</td>
											<td style="text-align:left; font-size:11pt; color:gray" align="left">
											限填入六個文字，Ex.隨意打賞、$250/時、每日一千元、…</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>商家車行：</td>
								<td style="text-align:left" valign="top">
									<input type="radio" name="taxi_company" value="1"{$taxi_company1}>公司車行&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="taxi_company" value="2"{$taxi_company2}>個人車行&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="taxi_company" value="3"{$taxi_company3}>計程車合作社&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="taxi_company" value="4"{$taxi_company4}>其他
								</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>車牌號碼：</td>
								<td style="text-align:left" valign="top">
									<input type="text" name="taxi_plate" style="width:100px" value="{$data['taxi_plate']}">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									車齡：<input type="text" name="taxi_age" style="width:50px" value="{$data['taxi_age']}">年
								</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>駕駛人性別：</td>
								<td style="text-align:left" valign="top">
									<input type="radio" name="taxi_sex" value="1"{$taxi_sex1}>男性&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="taxi_sex" value="2"{$taxi_sex2}>女性&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="taxi_sex" value="3"{$taxi_sex3}>其他性別
								</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>行車年資：</td>
								<td style="text-align:left" valign="top"><input type="text" name="taxi_exp" style="width:50px" value="{$data['taxi_exp']}">年</td>
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
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom"><span id="subject1">服務標題說明設定</span></td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:right" nowrap><span id="subject2">{$subject2}</span>：<br>(建議12字內)&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td style="text-align:left" valign="top">【<input type="text" name="name" style="width:430px" value="{$data['Name']}">】</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top"><span id="description">{$description}</span>：<br>(200字內)&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td style="text-align:left"><textarea name="description" style="width:450px; height:150px" maxlength=200>{$data['Description']}</textarea></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap><span id="subject3">{$subject3}</span>：</td>
								<td style="text-align:left">
									<div style="float:left"><input type="image" id="pics_upload" src="images/icon_upld.png" /></div>
									{$o_memo}
									<div style="font-size:10pt; display:none" id="loading">
										<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="../images/loader_light_blue.gif" style="width:60px; height:20px"></td><td style='font-size:10pt'>&nbsp;上傳中，請待候…</td></tr></table>
									</div>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<div id="pics">{$photos}</div>
								</td>
							</tr>
							<tr>
								<td></td>
								<td style="text-align:left">
									<table>
										<tr>
											<td style="vertial-align:top; text-align:right; font-size:11pt; color:gray" align="right" valign="top" nowrap>[說明]</td>
											<td style="text-align:left; font-size:11pt; color:gray" align="left">
											為避免頁面開啟速度變慢，上傳圖片的檔案限制於250KB以內; 建議圖片可降低成396(水平) x 248(垂直)解析度，降低解析度的做法請連結說明<a href="{$_CONFIG['urlD']}" target="_blank">如何降低圖片解析度</a>; 本處上傳之圖片將用於商品陳列頁面，以及商品介紹頁面兩處。</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="left">
								<table>
									<tr>
										<td><input type="checkbox" name="slide" value="1"{$slide}></td>
										<td colspan="2">服務說明頁面圖框設定四圖檔輪播功能</td>
									</tr>
									<tr>
										<td></td>
										<td nowrap>圖二網址：</td>
										<td><input type="text" style="width:450px" name="slide2" value="{$data['Slide2']}"></td>
									</tr>
									<tr>
										<td></td>
										<td nowrap>圖三網址：</td>
										<td><input type="text" style="width:450px" name="slide3" value="{$data['Slide3']}"></td>
									</tr>
									<tr>
										<td></td>
										<td nowrap>圖四網址：</td>
										<td><input type="text" style="width:450px" name="slide4" value="{$data['Slide4']}"></td>
									</tr>
									<tr>
										<td></td>
										<td colspan="2" align="left" style="color:gray">
										
											<table>
												<tr>
													<td style="vertial-align:top; text-align:right; font-size:11pt; color:gray" align="right" valign="top" nowrap>[註]: </td>
													<td style="text-align:left; font-size:11pt; color:gray" align="left">若無圖片網址連結輸入， 請勿勾選"服務說明頁面圖框設定四圖檔輪播功能"; 圖片網址來源可以來自”服務&資訊說明”所上傳之圖片，網址填寫範例：http://www.intimego.com/…。</td>
												</tr>
											</table>
										
										</td>
									</tr>
								</table>
								
								</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<div id="activity">
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
								<td style="text-align:left"><input type="text" name="activity_start" style="width:100px" onClick="WdatePicker();" value="{$data['activity_start']}"> 至 <input type="text" name="activity_end" style="width:100px" onClick="WdatePicker();" value="{$data['activity_end']}" onChange="iForm.activity_ann.value=this.value"></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left; font-size:11pt;">
									<Table align="left" border=0>
										<tr>
											<td valign="top" width=45 style="color:gray">[說明]:</td>
											<td valign="top" style="color:gray">設定之活動天數將自活動審核通過日起算，最長活動時間為30天，最短為3天；商品達到活動時間之設定，則活動將會自動下架。</td>
										</tR>
									</table>									
								</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">公佈日期：</td>
								<td style="text-align:left"><input type='text' name="activity_ann" style="width:100px; border:none; border-bottom:solid 2px gray" value="{$data['activity_ann']}" readonly></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left; font-size:11pt;" style="color:gray">[說明]: 獲獎公佈日期自動設為活動結束日</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">抽獎數量：</td>
								<td style="text-align:left"><input type='text' style="width:50px" name="activity_quota" value="{$data['activity_quota']}"></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">抽獎方式：</td>
								<td style="text-align:left">
								若沒有超過最低<input type="text" name="activity_min" style="width:50px" value="{$data['activity_min']}">人門檻，則不提供獎項。<br>
								參加人數每超過<input type="text" name="activity_per" style="width:50px" value="{$data['activity_per']}">人, 就提供一個抽獎項, 直到抽獎數量用完為止。 <font  style="color:gray">(註: 當抽獎數量超過1個以上時, 此項目必需填寫)</font>
								<br>
								<input type="hidden" name="activity_draw" value="1">採用系統隨機抽獎方式, 抽獎結果將公佈於本站 [會員獲利公告], 本站也會主動發出email告知得獎者。<br>
								</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">抽獎說明：</td>
								<td style="text-align:left"><textarea name="activity_info" style="width:500px; height:150px">{$data['activity_info']}</textarea></td>
							</tr>
							<tr style="display:none">
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left; font-size:11pt;" style="color:gray">[註]:選擇由主辦單位決定抽獎方式者，必須填寫抽獎說明</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<div id="coupon">
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
								<td style="text-align:left"><input type="text" name="datevalidate" style="width:100px" onClick="WdatePicker();" value="{$data['dateValidate']}"> 至 <input type="text" name="dateexpire" style="width:100px" onClick="WdatePicker();" value="{$data['dateExpire']}"></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left; font-size:11pt;">
									<Table align="left" border=0>
										<tr>
											<td valign="top" width=45 style="color:gray">[說明]:</td>
											<td valign="top" style="color:gray">達成兌換期間之終止日, 則商品自動下架; 商品若需在兌換終止日前下架, 需聯絡網站管人員做下架處理。</td>
										</tR>
									</table>									
								</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">使用時段：</td>
								<td style="text-align:left"><textarea name="hours" style="width:500px; height:50px">{$data['Hours']}</textarea></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left; font-size:11pt;color:gray">Ex:凡預約週一至週五每日4:00PM前使用者，平假日皆可使用</td>
							</tr>
							<tr>
								<td style="text-align:left" nowrap colspan="2">購買與使用張數：</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left">
									<input type="radio" name="restrict" value="1">每人不限交易次數，購量與限用數量<br>
									<input type="radio" name="restrict" value="2">每人不限交易次數與購量，但每人到店限用 <input type="text" name="use2" style="width:40px"> 張<br>
									<input type="radio" name="restrict" value="3">每人限制一次交易次數，限購 <input type="text" name="buy3" style="width:40px"> 張，每人到店限用 <input type="text" name="use3" style="width:40px"> 張<br>
									<input type="radio" name="restrict" value="4">每人限制一次交易次數，限購 <input type="text" name="buy4" style="width:40px"> 張，不限每人使用數量
								</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">其他說明：</td>
								<td style="text-align:left"><textarea name="memo" style="width:500px; height:150px">{$data['Memo']}</textarea></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left; font-size:11pt;color:gray">Ex:不得跨店使用；不得與其他優惠合併使用；如無法兌換現金及找零；<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;加贈課程現金抵用券750元</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<div id="special" class="cashflow">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom">好康特色</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td style="text-align:right;width:100px" nowrap valign="top">(1).</td>
								<td style="text-align:left"><textarea name="special1" style="width:500px; height:50px">{$data['Special1']}</textarea></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">(2).</td>
								<td style="text-align:left"><textarea name="special2" style="width:500px; height:50px">{$data['Special2']}</textarea></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">(3).</td>
								<td style="text-align:left"><textarea name="special3" style="width:500px; height:50px">{$data['Special3']}</textarea></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">(4).</td>
								<td style="text-align:left"><textarea name="special4" style="width:500px; height:50px">{$data['Special4']}</textarea></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap valign="top">(5).</td>
								<td style="text-align:left"><textarea name="special5" style="width:500px; height:50px">{$data['Special5']}</textarea></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left; font-size:11pt;color:gray">[說明]：至少需填寫一項, 每項100字內</td>
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
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom"><span id="info1">服務&資訊說明</span></td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr>
								<td><textarea id="intro" name="intro" class="mceEditor" style="width:600px;height:400px">{$data['Intro']}</textarea></td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td align="left">
						<Table align="left" width="600">
							<tr>
								<td valign="top" width=40 style="color:gray">[註]：</td>
								<td valign="top" width=20 style="color:gray">(1).</td>
								<td valign="top" align="left" style="color:gray">為了圖文編輯的美觀考量， 商品圖片請自行拖曳適當之大小，儘量勿超出編輯頁面之寬度。 編輯所加入之圖片，若圖片位置出現在編輯頁頂端時，僅需剪下該圖片， 再貼到適當位置即可。文字編輯器若無法編輯文字時，可以在Wordpad編輯完成後再貼上文字。</td>
							</tR>
							<tr>
								<td></td>
								<td valign="top" width=20 style="color:gray">(2).</td>
								<td valign="top" align="left" style="color:gray">服務&資訊說明內容，可告知消費者是否提供商品遞送到府服務，若商家可提供商品遞送服務時，說明內容需載明買家需如何與商家聯絡確認 (電話/簡訊/…)。</td>
							</tR>
							<tr>
								<td></td>
								<td valign="top" width=20 style="color:gray">(3).</td>
								<td valign="top" align="left" style="color:gray">請連結<a href="{$_CONFIG['urlA']}" target="_blank">如何以簡單方式拍攝商品照</a>, 讓您了解如何自行DIY拍攝美麗商品照; 請連結<a href="{$_CONFIG['urlB']}" target="_blank">如何連結圖片網址在網站上貼圖</a>, 讓您了解網路圖片如何貼到自己的商品提案頁面上; 請連結<a href="{$_CONFIG['urlC']}" target="_blank">如何上傳圖片並連結圖片網址</a>, 讓您了解自己如何用最簡單的方式上傳圖片至免費空間, 並獲取網路圖片之網址。</td>
							</tR>
						</table>	
					</td>
				</tr>
			</table>
			</div>
			<div id="seller">
			<table align="center" width="624">
				<tr>
					<td style="text-align:left; font-weight:bold; font-size:14pt; height:40px; border-bottom:solid 2px gray; vertical-align:bottom"><span id="seller1">業者</span>(<span id="activity_seller">商家</span>)資訊說明</td>
				</tr>
				<tr>
					<td style="text-align:left">
						<fieldset>
						<table>
							<tr class="activity">
								<td style="text-align:right" nowrap>主辦單位名稱：</td>
								<td style="text-align:left"><input type="text" name="activity_holder" style="width:480px" value="{$data['activity_holder']}"></td>
							</tr>
							<tr class="activity">
								<td style="text-align:right" nowrap>主辦單位郵件：</td>
								<td style="text-align:left"><input type="text" name="activity_email" style="width:480px" value="{$data['activity_email']}"></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap><span id="seller2">業者名稱</span>：</td>
								<td style="text-align:left"><input type="text" name="seller" style="width:480px" value="{$data['Seller']}"></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left; font-size:11pt; color:gray">[說明]:<span id="seller3">業者若無店面，可以不需填寫。</span></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>業者網站：</td>
								<td style="text-align:left"><input type="text" name="url" style="width:480px" value="{$data['Url']}"></td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left; font-size:11pt; color:gray">Ex:http://{$WEB_HOST}，可以不需填寫。</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>聯絡電話：</td>
								<td style="text-align:left"><input type="text" name="phone" style="width:480px" value="{$data['Phone']}"></td>
							</tr>
							<tr style="" id="phone_memo">
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left; font-size:11pt; color:gray">[註]:無法提供聯絡電話者，可以鍵入"NA"。</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap>發票或收據：</td>
								<td style="text-align:left">
									<input type="radio" name="receipt" value="1">可以提供發票&nbsp;
									<input type="radio" name="receipt" value="2">可以提供收據&nbsp;
									<input type="radio" name="receipt" value="3">都無法提供
								</td>
							</tr>
							<tr>
								<td style="text-align:right" nowrap></td>
								<td style="text-align:left; font-size:11pt; ">
									<table>
										<tr>
											<td style="vertial-align:top; text-align:right; font-size:11pt; color:gray" align="right" valign="top" nowrap>[註]: </td>
											<td style="text-align:left; font-size:11pt; color:gray" align="left">
											業者若能提供發票或收據, 可以增加一些買家購買的意願。
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr id="event">
								<td style="text-align:right" nowrap>活動時間：</td>
								<td style="text-align:left" valign="top">
									<input type="text" name="event_date" style="width:100px" onClick="WdatePicker();" value="{$data['event_date']}">&nbsp;&nbsp;
									<select name="event_start">
										<option value="">開始時間</option>
										<option value="00:00">00:00</option>
										<option value="01:00">01:00</option>
										<option value="02:00">02:00</option>
										<option value="03:00">03:00</option>
										<option value="04:00">04:00</option>
										<option value="05:00">05:00</option>
										<option value="06:00">06:00</option>
										<option value="07:00">07:00</option>
										<option value="08:00">08:00</option>
										<option value="09:00">09:00</option>
										<option value="10:00">10:00</option>
										<option value="11:00">11:00</option>
										<option value="12:00">12:00</option>
										<option value="13:00">13:00</option>
										<option value="14:00">14:00</option>
										<option value="15:00">15:00</option>
										<option value="16:00">16:00</option>
										<option value="17:00">17:00</option>
										<option value="18:00">18:00</option>
										<option value="19:00">19:00</option>
										<option value="21:00">21:00</option>
										<option value="22:00">22:00</option>
										<option value="23:00">23:00</option>
									</select><script language='javascript'>iForm.event_start.value="{$data['event_start']}";</script> 
									～
									<select name="event_end">
										<option value="">結束時間</option>
										<option value="01:00">01:00</option>
										<option value="02:00">02:00</option>
										<option value="03:00">03:00</option>
										<option value="04:00">04:00</option>
										<option value="05:00">05:00</option>
										<option value="06:00">06:00</option>
										<option value="07:00">07:00</option>
										<option value="08:00">08:00</option>
										<option value="09:00">09:00</option>
										<option value="10:00">10:00</option>
										<option value="11:00">11:00</option>
										<option value="12:00">12:00</option>
										<option value="13:00">13:00</option>
										<option value="14:00">14:00</option>
										<option value="15:00">15:00</option>
										<option value="16:00">16:00</option>
										<option value="17:00">17:00</option>
										<option value="18:00">18:00</option>
										<option value="19:00">19:00</option>
										<option value="21:00">21:00</option>
										<option value="22:00">22:00</option>
										<option value="23:00">23:00</option>
										<option value="24:00">24:00</option>
									</select><script language='javascript'>iForm.event_end.value="{$data['event_end']}";</script>
								</td>
							</tr>
							<tr id="deliver1">
								<td style="text-align:right" nowrap>服務時間：<br>(聯絡時間)&nbsp;&nbsp;</td>
								<td style="text-align:left" valign="top"><input type="text" name="openhours" style="width:480px" value="{$data['openHours']}"></td>
							</tr>
							<tr id="deliver2">
								<td style="text-align:right" nowrap valign="top"><span id="seller4">服務地址：<br>(面交地點)&nbsp;&nbsp;</span></td>
								<td style="text-align:left" valign="top"><input type="text" name="address" style="width:480px" onblur="getLatitude(this);" value="{$data['Address']}">
									<div id="seller5" style="text-align:left">
										<table align="left">
											<tr>
												<td style="vertial-align:top; text-align:right; font-size:11pt; color:gray" align="right" valign="top" nowrap>[註]: </td>
												<td style="text-align:left; font-size:11pt; color:gray" align="left">
												不便提供服務地址者, 可以不需填寫。
												</td>
											</tr>
										</table>
									</div>
									<div id="seller6" style="text-align:left">
										<table align="left">
											<tr>
												<td style="vertial-align:top; text-align:right; font-size:11pt; color:gray" align="right" valign="top" nowrap>[註]: </td>
												<td style="text-align:left; font-size:11pt; color:gray" align="left">
												若業者並無實體消費店面且無必要公開住址時，[服務地址]可以填寫為會面之地標地址，填寫範例，ex.台北市北平西路3號 (會面地址:臺灣鐵路管理局)。
												</td>
											</tr>
										</table>
									</div>								
								</td>
							</tr>

							<tr>
								<td style="text-align:right" nowrap>其他資訊：</td>
								<td style="text-align:left"><textarea name="about" style="width:480px; height:100px">{$data['About']}</textarea></td>
							</tr>
							<tr style="display:none">
								<td style="text-align:right" nowrap>店家位置圖：</td>
								<td style="text-align:left">
									<div style="float:left">
										<table cellpadding="0" cellspacing="0" border="0">
											<tr>
												<td><input type="image" id="maps_upload" src="images/icon_upld.png" /></td>
												<td style="text-align:left; font-size:11pt" style="color:gray">[註]可不上傳圖片</td>
											</tR>
										</table>
										
									</div>
									<div style="font-size:10pt; display:none" id="loading2">
										<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="../images/loader_light_blue.gif"></td><td style='font-size:10pt'>&nbsp;上傳中，請待候…</td></tr></table>
									</div>
								</td>
							</tr>
							<tr style="display:none">
								<td></td>
								<td>
									<div id="maps">{$maps}</div>
								</td>
							</tr>
							<tr style="display:none">
								<td></td>
								<td style="text-align:left">
									<table>
										<tr>
											<td style="vertial-align:top; text-align:right; font-size:11pt" align="right" valign="top" nowrap style="color:gray">[說明]</td>
											<td style="text-align:left; font-size:11pt" align="left" style="color:gray">
											圖片上傳前請儘可能裁切成488(水平) x 290(垂直)解析度，<br>
											圖片上傳後，系統將自動調整其圖框大小與重新取樣解析度<br>
											此上傳圖片，作為手機版網頁之使用
										</tr>
									</table>
								</td>
							</tr>
						</table><input type="hidden" name="latitude" value="({$data['Latitude']}, {$data['Longitude']})">

						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<table align="center" width="624">
				<tr>
					<td style="text-align:center; font-weight:bold; font-size:14pt; height:100px; line-height:100px">$btns</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
<div id="map" style="display:none"></div>

EOD;
include 'template2.php';
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Catalog WHERE useFor = 'TYPE_AREA' ORDER BY Sort");
while($rs=mysql_fetch_array($result)){
	JavaScript::addCombo("iForm.area", $rs['No'], $rs['Name']);
}
$result = mysql_query("SELECT * FROM Catalog WHERE useFor = '$usefor' AND Parent=0 ORDER BY Sort");
while($rs=mysql_fetch_array($result)){
	JavaScript::addCombo("iForm.catalog", $rs['No'], $rs['Name']);
}
$result = mysql_query("SELECT * FROM Catalog WHERE useFor = 'TYPE_COM' ORDER BY Sort");
while($rs=mysql_fetch_array($result)){
	JavaScript::addCombo("iForm.type", $rs['No'], $rs['Name']);
}
$result = mysql_query("SELECT * FROM Donate ORDER BY Name");
while($rs=mysql_fetch_array($result)){
	JavaScript::addCombo("iForm.donate", $rs['No'], $rs['Name']);
}

include './include/db_close.php';
JavaScript::setValue("iForm.area", $data["Area"]);
JavaScript::setValue("iForm.catalog", $data["Catalog"]);
JavaScript::setValue("iForm.type", $data["Type"]);
if($data["Deliver"] == 1){
	JavaScript::Execute("iForm.deliver[1].checked = true");
}
if($data["isDonate"] > 0){
	JavaScript::Execute("iForm.isdonate.checked = true");
	JavaScript::Execute("iForm.donate.value = '" . $data["isDonate"] . "'");
}
switch($data["Receipt"]){
	case 1:
		JavaScript::Execute("iForm.receipt[0].checked = true");
		break;
	case 2:
		JavaScript::Execute("iForm.receipt[1].checked = true");
		break;
	case 3:
		JavaScript::Execute("iForm.receipt[2].checked = true");
		break;
}


JavaScript::Execute("setDeliver();");
JavaScript::Execute("setDaysBeforeReserve();");


switch($data["Duration"]){
	case 0:
		JavaScript::Execute("iForm.duration[0].checked = true");
		break;
	case 1:
		JavaScript::Execute("iForm.duration[1].checked = true");
		JavaScript::setValue("iForm.daysonsale", $data['daysOnSale']);
		break;
}
switch($data["Amount"]){
	case 0:
		JavaScript::Execute("iForm.amount[0].checked = true");
		break;
	case 1:
		JavaScript::Execute("iForm.amount[1].checked = true");
		JavaScript::setValue("iForm.quota", $data['Quota']);
		break;
}

switch($data["Restrict"]){
	case 1:
		JavaScript::Execute("iForm.restrict[0].checked = true");
		break;
	case 2:
		JavaScript::Execute("iForm.restrict[1].checked = true");
		JavaScript::setValue("iForm.use2", $data['maxUse']);
		break;
	case 3:
		JavaScript::Execute("iForm.restrict[2].checked = true");
		JavaScript::setValue("iForm.use3", $data['maxUse']);
		JavaScript::setValue("iForm.buy3", $data['maxBuy']);
		break;
	case 4:
		JavaScript::Execute("iForm.restrict[3].checked = true");
		JavaScript::setValue("iForm.buy4", $data['maxBuy']);
		break;
}



?>

<script language="javascript">setOption();setFans();setTransport();setPriceMode();//$("#facebook_page").load("facebook_page.php?page=<?=$data['activity_page']?>");</script>
<script language="javascript">
	function getLatitude(x){
		if(x.value){
			if (GBrowserIsCompatible()) {
				var map = new google.maps.Map2(document.getElementById("map"));
				var geocoder = new google.maps.Geocoder();
				map.addControl(new GSmallMapControl());
				geocoder.geocode({ address: x.value }, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        var loc = results[0].geometry.location;
						iForm.latitude.value = loc.lat() + "," + loc.lng();
                    }
                    else
                    {
						alert('Google Maps 找不到該地址，將無法計算距離！');
                    }
				});
		   }
		}
		else{
			iForm.latitude.value = "";
		}
	}
</script>
<script language="javascript">
var pics_counts = <?=$pics_counts?>;
var maps_counts = <?=$maps_counts?>;

$(function(){
	new AjaxUpload('#pics_upload', {
		action: 'upload_img250.php',
		onSubmit : function(file , ext){
			if (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext)){
				var d = new Date().getTime();
				document.getElementById("loading").style.display="block";
				this.setData({
					'dir':  	"upload/",
					"fname": 	"product_" + d + "." + ext,
					'ext':  	ext
				});
			} else {					
				alert('上傳錯誤訊息: 只允許上傳 image 圖檔 (jpg,png,jpeg,gif)');
				return false;				
			}		
		},
		onComplete : function(file, response){
			if(response == "err1"){
				alert('圖片不可大於250KB!');
			}
			else if(response == "err2"){
				alert('上傳失敗，請重新上傳!');
			}
			else{
				var pic = "<div id='pic" + pics_counts + "' style='float:left'><input type='hidden' name='pic' value='" + response + "'>";
				pic += "<img name='pics' src='upload/thumb_"+response+"' style='width:396px; height:248px'>";
				pic += "</div>";
				$('#pics').html(pic);
				pics_counts ++;
			}
			document.getElementById("loading").style.display="none";
		}
	});
	new AjaxUpload('#maps_upload', {
		action: 'upload_img2.php',
		onSubmit : function(file , ext){
			if (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext)){
				var d = new Date().getTime();
				document.getElementById("loading2").style.display="block";
				this.setData({
					'dir':  	"upload/",
					"fname": 	"maps_" + d + "." + ext,
					'ext':  	ext
				});
			} else {					
				alert('上傳錯誤訊息: 只允許上傳 image 圖檔 (jpg,png,jpeg,gif)');
				return false;				
			}		
		},
		onComplete : function(file, response){
			var pic = "<div id='map" + maps_counts + "' style='float:left'><input type='hidden' name='map' value='" + response + "'>";
			pic += "<img name='maps' src='upload/thumb_"+response+"' style='width:488px; height:290px'>";
			pic += "</div>";
			$('#maps').html(pic);
			document.getElementById("loading2").style.display="none";
			maps_counts ++;
		}
	});
});

</script>

<script language="javascript">
	var config = {
		width: 600,
		height: 400,
		toolbar:
			[
				['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','Anchor'],
				'/',
				['Styles','Format','Font','FontSize','-','TextColor','BGColor','-','Image','Table','-','Maximize']
			],
			filebrowserBrowseUrl : '/ckfinder/ckfinder.html',
			filebrowserUploadUrl : './ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'
	};

	$(function() {
		$("#intro").ckeditor(config)
	});

</script>
<script language="javascript">
function selectPage(){
	var d = new Date();
	var w = window.showModalDialog("select_page.php?"+d.getTime(), $("#activity_page").val(), 'status:no');
	if(w){
		$("#activity_page").val(w.page);
		$("#activity_name").val(w.name);
		$("#facebook_page").html(w.name);
	}
}

function getArea(){
	for(var i=1; i<iForm.area.options.length; i++){
		if(iForm.address.value.indexOf(iForm.area.options[i].text)==0){
			iForm.area.options.selectedIndex = i;
			break;
		}
	}
	if(iForm.area.options.selectedIndex == 0)
		iForm.area.options.selectedIndex = iForm.area.options.length-1;
}

function Check(){
	getArea();
	CKEDITOR.instances.intro.updateElement();
	if(iForm.cashflow[1].checked && !iForm.transport.checked && !iForm.allnew.checked && !iForm.used.checked && !iForm.sale.checked){
		iForm.allnew.checked = true;
	}



	if(!iForm.agree.checked){
		alert("請閱讀並同意遵守電子商務服務條款!");
		return false;
	}
	if(!iForm.deliver[0].checked && !iForm.deliver[1].checked){
		alert("請選擇本地或宅配!");
		return false;
	}
//	if(iForm.deliver[0].checked && !iForm.area.value){
//		alert("請選擇地區!");
//		return false;
//	}
	if(!iForm.catalog.value){
		alert("請選擇分類!");
		return false;
	}

	<?php
	if($option == 2 || $option == 3){
	
	?>
	if(iForm.deliver[0].checked && !iForm.activity.checked && !iForm.type.value){
		alert("請選擇服務模式!");
		return false;
	}

	<?}?>
	if(iForm.cashflow[1].checked && !iForm.transport.checked && !iForm.allnew.checked && !iForm.used.checked && !iForm.sale.checked){
		alert("非金流商品請勾選全新商品、中古貨販售或即期貨販售! ");
		return false;
	}
	
	if(iForm.transport.checked && !iForm.taxi_company[0].checked && !iForm.taxi_company[1].checked && !iForm.taxi_company[2].checked && !iForm.taxi_company[3].checked){
		alert("請選擇商家車行!");
		return false;
	}
	if(iForm.transport.checked && !iForm.taxi_plate.value){
		alert("請輸入車牌號碼!");
		return false;
	}
	if(iForm.transport.checked && !iForm.taxi_age.value){
		alert("請輸入車齡!");
		return false;
	}
	if(iForm.transport.checked && !iForm.taxi_sex[0].checked && !iForm.taxi_sex[1].checked && !iForm.taxi_sex[2].checked){
		alert("請選擇駕駛人性別!");
		return false;
	}

	if(iForm.transport.checked && !iForm.taxi_exp.value){
		alert("請輸入行車年資!");
		return false;
	}
	if(!iForm.transport.checked){
		if(iForm.price_mode[0].checked && !iForm.price.value){
			alert("請輸入原價!");
			return false;
		}
		if(iForm.price_mode[0].checked && !iForm.price1.value){
			alert("請輸入賣價!");
			return false;
		}
		var discount = 10;
		var price = parseInt(iForm.price.value, 10);
		var price1 = parseInt(iForm.price1.value, 10);
	//	discount = 10*price1/price;

		if(iForm.price_mode[0].checked && price1 > price){
			alert("賣價不可高於原價!");
			return false;
		}
		if(iForm.price_mode[1].checked && !iForm.price_info.value){
			alert("請輸入計價方式!");
			return false;
		}
	}
	/*
	if(discount > 9){
		alert("折扣必須優於9折，請修正賣價!");
		return false;
	}
	*/
	if(iForm.activity.checked && !iForm.activity_page.value){
		alert("選擇FaceBook粉絲團!");
		return false;
	}

	/*
	if(!iForm.discount.value){
		alert("請輸入折扣!");
		return false;
	}
	if(!iForm.daysonsale.value){
		alert("請輸入販售時間!");
		return false;
	}
	if(isNaN(iForm.daysonsale.value)){
		alert("販售時間請輸入數字!");
		return false;
	}
	if(parseInt(iForm.daysonsale.value, 10) < 1 || parseInt(iForm.daysonsale.value, 10) > 14){
		alert("販售時間請輸入1-14天!");
		return false;
	}
	if(!iForm.quota.value){
		alert("請輸入可販售總量!");
		return false;
	}
	*/
	if(!iForm.name.value){
		if(iForm.transport.checked){
			alert("請輸入計程車名稱!");
		}
		else{
			alert("請輸入<?=$subject2?>!");
		}
		return false;
	}
	if(!iForm.description.value){
		if(iForm.transport.checked){
			alert("請輸入服務簡介!");
		}
		else if(iForm.activity.checked)
			alert("請輸入活動簡介!");
		else
			alert("請輸入商品簡介!");
		return false;
	}

	if(iForm.activity.checked && !iForm.activity_start.value){
		alert("請輸入活動期間!");
		return false;
	}
	if(iForm.activity.checked && !iForm.activity_end.value){
		alert("請輸入活動期間!");
		return false;
	}
	if(iForm.activity.checked && !iForm.activity_ann.value){
		//alert("請輸入公佈日期!");
		//return false;
	}
	if(iForm.activity.checked && !iForm.activity_quota.value){
		alert("請輸入抽獎數量!");
		return false;
	}
	if(iForm.activity.checked && parseInt(iForm.activity_quota.value, 10) > 1 && !iForm.activity_per.value){
		alert("請設定參加人數每超過 n 人，就提供一個抽獎項!");
		return false;
	}
	/*
	if(iForm.activity.checked && !iForm.activity_min.value){
		alert("請輸入抽獎方式!");
		return false;
	}
	if(iForm.activity.checked && !iForm.activity_per.value){
		alert("請輸入抽獎方式!");
		return false;
	}
	if(iForm.activity.checked && !iForm.activity_draw[0].checked && !iForm.activity_draw[1].checked){
		alert("請輸入抽獎方式!");
		return false;
	}
	*/
	if(iForm.activity.checked && !iForm.activity_info.value){
		alert("請輸入抽獎說明!");
		return false;
	}


	if(iForm.cashflow[0].checked && !iForm.duration[0].checked && !iForm.duration[1].checked){
		alert("請設定販售時間!");
		return false;
	}
	if(iForm.cashflow[0].checked && iForm.duration[1].checked && !iForm.daysonsale.value){
		alert("請設定販售時間!");
		return false;
	}
	if(iForm.cashflow[0].checked && iForm.duration[1].checked && (parseInt(iForm.daysonsale.value, 10) > 180 || parseInt(iForm.daysonsale.value, 10) < 30)){
		alert("最長販售時間為 180 天, 最短為 30 天!");
		return false;
	}

	if(iForm.cashflow[0].checked && !iForm.amount[0].checked && !iForm.amount[1].checked){
		alert("請設定販售總量!");
		return false;
	}
	if(iForm.cashflow[0].checked && iForm.amount[1].checked && !iForm.quota.value){
		alert("請設定販售總量!");
		return false;
	}
	/*	*/

	if((iForm.deliver[0].checked && iForm.cashflow[0].checked) && !iForm.datevalidate.value){
		alert("請輸入兌換期間!");
		return false;
	}
	if((iForm.deliver[0].checked && iForm.cashflow[0].checked) && !iForm.dateexpire.value){
		alert("請輸入兌換期間!");
		return false;
	}
	if((iForm.deliver[0].checked && iForm.cashflow[0].checked) && !iForm.hours.value){
		alert("請輸入使用時段!");
		return false;
	}
	if((iForm.deliver[0].checked && iForm.cashflow[0].checked) && !iForm.restrict[0].checked && !iForm.restrict[1].checked && !iForm.restrict[2].checked && !iForm.restrict[3].checked){
		alert("請設定購買與使用張數!");
		return false;
	}
	/*	*/

	if(iForm.cashflow[0].checked && !iForm.special1.value){
		alert("請輸入好康特色/商品資料說明!");
		return false;
	}


	if(iForm.activity.checked && !iForm.activity_holder.value){
		alert("請輸入主辦單位名稱!");
		return false;
	}
	if(iForm.activity.checked && !iForm.activity_email.value){
		alert("請輸入主辦單位郵件!");
		return false;
	}
/*
	if(!iForm.seller.value){
		if(!iForm.transport.checked){
			alert("請輸入賣家/店家!");
			return false;
		}
	}
*/
	if(iForm.transport.checked && !iForm.taxi_discount.value ){
		alert("請輸入計價方式!");
		return false;
	}
	if(iForm.transport.checked && !iForm.phone.value ){
		alert("請輸入聯絡電話!");
		return false;
	}
	if(!iForm.receipt[0].checked && !iForm.receipt[1].checked && !iForm.receipt[2].checked){
		alert("請選擇是否提供發票或收據!");
		return false;
	}
	/*	*/

<?if($option == 6){?>
	if(!iForm.event_date.value){
		alert("請輸入活動日期！");
		return false;
	}
	if(!iForm.event_start.value){
		alert("請選擇開始時間！");
		return false;
	}
	if(!iForm.event_end.value){
		alert("請選擇結束時間！");
		return false;
	}
	if(iForm.event_end.value < iForm.event_start.value){
		alert("結束時間不可早於開始時間！");
		return false;
	}

<?}else{?>
	if(iForm.deliver[0].checked && !iForm.openhours.value){
		alert("請輸入服務時間!");
		return false;
	}
<?}?>	
	if(iForm.deliver[0].checked && !iForm.address.value){
		if(!iForm.transport.checked && iForm.event.value != "1" && iForm.hr.value != "1"){
			alert("請輸入服務地址!");
			return false;
		}
	}
/*
	if(!iForm.about.value){
		alert("請輸入其他資訊!");
		return false;
	}
*/
	return true;
}

function Save(){
	if(!iForm.pic || !iForm.pic.value){
		if(iForm.transport.checked){
			alert("請上傳車輛圖示!");
		}
		else{
			alert("請上傳商品圖示!");
		}
	}
	else if(iForm.slide.checked && (
		!iForm.slide2.value || !iForm.slide3.value || !iForm.slide4.value
		)){
		alert("使用服務說明頁面圖框設定四圖檔輪播功能，請輸入圖二、圖三、圖四網址!");
	}
	else if(Check() && confirm("確定送出提案不再變更內容! \n本站審核將於三日內完成 , 審核結果將以電子郵件告知提案者; \n為考量網路安全起見 , 提案者需在收到的郵件連結提案內容, 再次確認提案內容是否無誤。")){
		iForm.action = "seller_product_step3_save.php";
		iForm.target = "iAction";
		iForm.status.value = "1";
		setTimeout("iForm.submit();", 2000);
	}
}
function Draft(){
	if(Check()){
		iForm.action = "seller_product_step3_save.php";
		iForm.target = "iAction";
		iForm.status.value = "0";
		setTimeout("iForm.submit();", 2000);
	}
}
function Preview(){
	if(Check()){
		iForm.action = "seller_product_step3_preview.php";
		iForm.target = "_blank";
		setTimeout("iForm.submit();", 2000);
	}
}
</script>

<script language="javascript">
<?php
echo '$("#event").hide();' . "\n";
if($option == 4){
	echo '$("#deliver7").hide();' . "\n";
}
if($option == 5){
	echo '$("#seller4").html("服務地址：");' . "\n";
	echo '$("#seller5").show();' . "\n";
	echo '$("#seller6").hide();' . "\n";
	echo '$("#deliver7").hide();' . "\n";
	echo '$("#employer").show();' . "\n";
}

if($option == 6){
	echo '$("#seller4").html("活動地址：");' . "\n";
	echo '$("#seller5").show();' . "\n";
	echo '$("#seller6").hide();' . "\n";
	echo '$("#deliver7").hide();' . "\n";
	echo '$("#deliver1").hide();' . "\n";
	echo '$("#event").show();' . "\n";
}


?>
</script>
<script language="javascript">
	google.load("maps", "2",{"other_params":"sensor=true"});
</script>