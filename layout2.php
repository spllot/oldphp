<?php
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::setURL("./", "window.top");
	exit;
}

$ip = ((getenv(HTTP_X_FORWARDED_FOR)) ?  getenv(HTTP_X_FORWARDED_FOR) :  getenv(REMOTE_ADDR));
	include './include/db_open.php';
if(!empty($_SESSION['member']) && substr($_SESSION['member']['dateLogin'], 0, 10) != date('Y-m-d')){
	mysql_query("UPDATE Member SET Days = Days + 1 WHERE userID = '" . $_SESSION['member']['userID'] . "'");
    $_SESSION['member']['dateLogin'] = date('Y-m-d H:i:s');
			mysql_query("UPDATE Member SET Level = 2 WHERE userID = '$userid' AND Days >= 15  AND Days <30");
			mysql_query("UPDATE Member SET Level = 3 WHERE userID = '$userid' AND Days >= 30  AND Days <60");
			mysql_query("UPDATE Member SET Level = 4 WHERE userID = '$userid' AND Days >= 60  AND Days <120");
			mysql_query("UPDATE Member SET Level = 5 WHERE userID = '$userid' AND Days >= 120  AND Days <240");
			mysql_query("UPDATE Member SET Level = 6 WHERE userID = '$userid' AND Days >= 240  AND Days <480");
			mysql_query("UPDATE Member SET Level = 7 WHERE userID = '$userid' AND Days >= 480  AND Days <960");
			mysql_query("UPDATE Member SET Level = 8 WHERE userID = '$userid' AND Days >= 960");
	$sql = "INSERT INTO logLogin(dateLogin, userID, Status, ipLogin) VALUES (CURRENT_TIMESTAMP, '" . $_SESSION['member']['userID'] . "', '1', '$ip')";
	mysql_query($sql) or die (mysql_error());
}
	$result = mysql_query("SELECT * FROM Config");
	while($rs = mysql_fetch_array($result)){
		$_CONFIG[$rs['ID']] = $rs['YN'];
	}
	include './include/db_close.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<META NAME="description" content="InTimeGo即時服務網可偵測區域動態或靜態的商務行為，建立真正即時雙向互動的買賣商務模式;即購平台係針對一般傳統商店、行動商店 (包含攤販、行動販售車、補貨物流車...等)、運輸工具、以及人力與活動，進行即時搜尋服務的網路平台，對普羅大眾而言，這是一個能拉近供需距離與創造服務價值的網站，可以為買賣雙方創造更為便利與快速的商業關係。
 "><meta name="keywords" content="即時服務, 物流查詢, 運輸共乘, 即時人力, 即時活動, 安全監護, 雲端服務"/>
<title>InTimeGo即時服務-服務即得 快速便利</title>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-37407627-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script><link type="text/css" href="js/themes/base/ui.all.css" rel="stylesheet" />
<link type="text/css" href="style.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="js/jquery.colorbox.css" media="screen" />
<script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAAwnI081IB-1YQtn3DiExB7hQAaOLpgxbI1qAjGGRql3xj-qYYohQaZIKM_4qi4nCHaMvKs5cHNkkAWA"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.colorbox.js"></script>
<script type="text/javascript" src="js/jquery.blockUI.js"></script>
<script type="text/javascript" src="js/zip.js?20121126"></script>
<script type="text/javascript" src="js/ajaxupload.js"></script>
<script language="javascript" type="text/javascript" src="js/My97DatePicker/WdatePicker.js"></script>
<body bgcolor="#e4e4e4" topmargin="0" leftmargin="0" bottommargin="0" rightmargin="0" margin="0" style="background:url('./images/body9.jpg'); background-position:center center">
<center>
<?
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Member WHERE userID = '" . $_SESSION['member']['userID'] . "'");
$member = mysql_fetch_array($result);
$balance=0;
$trust = 0;
$result = mysql_query("SELECT COALESCE(SUM(Amount), 0) as Amount FROM logTransaction WHERE Owner='" . $_SESSION['member']['userID'] . "'");
if($rs=mysql_fetch_array($result)){
	$balance = $rs['Amount'];
}
$result = mysql_query("SELECT COALESCE(SUM(Quality), 0) as Amount FROM logRating WHERE Owner='" . $_SESSION['member']['No'] . "'");
if($rs=mysql_fetch_array($result)){
	$trust = $rs['Amount'];
}

$trust = (($_SESSION['member']['Seller']==2) ? "，賣家評價：<font color=red>" . number_format($trust) . "</font>" : "");
include './include/db_close.php';
?>
<script language="javascript">
var selected = null;
function mClk(x, url){
	if(selected){
		selected.style.backgroundImage="url('./images/" + selected.id + "_clk.gif')";
	}
	selected = x;
	selected.style.backgroundImage="url('./images/" + selected.id + ".gif')";
	if(url)
		iAcontent.location.href=url;
}


</script>
<table width="960" style="background:#98cd01; width:960px; border-right:solid 13px #98cd01" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<table width="100%" style="height:95px; width:100%" cellpadding="0" cellspacing="0">
				<tr>
					<td style="text-align:left"><a href="./"><img src="./upload/<?=$_CONFIG['logo']?>" border="0"></a></td>
					<td style="text-align:right;padding:0px"><div id="userinfo"></div></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table style="width:100%" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:200px;" cellpadding="0" cellspacing="0" valign="top">
						<table width="100%" style="background:#ffffff">
							<tr>
								<td style="background:#5f5f5f; line-height:30px; color:white; font-weight:bold">用戶中心</td>
							</tr>
							<tr>
								<td>
									<table width="100%">
										<tr>
											<td style="text-align:left; border-bottom:solid 1px gray; font-weight:bold" align="left">我的帳戶</td>
										</tr>
										<tr>
											<td style="text-align:center" align="center">
												<table width="100%" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td style="text-align:center" align="center"><div id="m1" style="height:25px; background:url('./images/m1_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'member_info.php');"></div></td>
													</tr>
													<tr>
														<td style="text-align:center" align="center"><div id="m2" style="height:25px; background:url('./images/m2_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'member_data.php');"></div></td>
													</tr>
													<tr>
														<td style="text-align:center" align="center"><div id="m3" style="height:25px; background:url('./images/m3_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'member_apply.php');"></div></td>
													</tr>
													<tr>
														<td style="text-align:center" align="center"><div id="m4" style="height:25px; background:url('./images/m4_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, '');"></div></td>
													</tr>
													<tr>
														<td style="text-align:center" align="center"><div id="m5" style="height:25px; background:url('./images/m5_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'member_transaction.php');"></div></td>
													</tr>
													<tr>
														<td style="text-align:center" align="center"><div id="m6" style="height:25px; background:url('./images/m6_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'member_message.php');"></div></td>
													<tr>
														<td style="text-align:center" align="center"><div id="m7" style="height:25px; background:url('./images/m7_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'member_question.php');"></div></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table width="100%">
										<tr>
											<td style="text-align:left; border-bottom:solid 1px gray; font-weight:bold" align="left">我是買家</td>
										</tr>
										<tr>
											<td style="text-align:center" align="center">
												<table width="100%" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td style="text-align:center" align="center"><div id="m8" style="height:25px; background:url('./images/m8_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'orders.php');"></td>
													</tr>
													<tr>
														<td style="text-align:center" align="center"><div id="m9" style="height:25px; background:url('./images/m9_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'member_favorite.php');"></div></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table width="100%">
										<tr>
											<td style="text-align:left; border-bottom:solid 1px gray; font-weight:bold" align="left">我是業者(提案者)</td>
										</tr>
										<tr>
											<td style="text-align:center" align="center">
												<table width="100%" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td style="text-align:center" align="center"><div id="m11" style="height:25px; background:url('./images/m11_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'seller_product_step1.php');"></div></td>
													</tr>
													<tr>
														<td style="text-align:center" align="center"><div id="m10" style="height:25px; background:url('./images/m10_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'seller_status.php');"></div></td>
													</tr>
												<?if($member['Seller'] == 2){?>
													<tr>
														<td style="text-align:center" align="center"><div id="m12" style="height:25px; background:url('./images/m12_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'seller_logs.php');"></div></td>
													</tr>
													<tr>
														<td style="text-align:center" align="center"><div id="m13" style="height:25px; background:url('./images/m13_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'seller_orders.php');"></div></td>
													</tr>
													<tr>
														<td style="text-align:center" align="center"><div id="m14" style="height:25px; background:url('./images/m14_clk.gif'); background-repeat:no-repeat; background-position:center center; cursor:pointer" onClick="mClk(this, 'seller_transfer.php');"></div></td>
													</tr>
												<?}else if($member['Seller'] == 3){?>
													<tr>
														<td style="text-align:center" align="center">&nbsp;</td>
													</tr>
												<?}?>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<td style="background:white; border:solid 5px #5f5f5f; text-align:center" align="center" valign="top"><iframe style="width:100%; height:600px;border:0px none" name="iAcontent" scrolling="auto" frameborder="0" marginwidth="0" marginheight="0"></iframe></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table width="960" style="background:#98cd01; width:960px;" cellpadding="0" cellspacing="0">
	<tr>
		<td style="font-size:11pt; text-align:center; background:#D6CF00; height:60px; padding:5px; color:red">
		吉達資訊科技股份有限公司 版權所有&copy 2012 All Rights Reserved.
		<div style="text-align:center; padding-top:8px">本站建議使用IE8.0以上版本瀏覽器</div>
		</td>
	</tr>
</table>
</center>

<div style="display:none">
<img src="./images/m1.gif">
<img src="./images/m1_clk.gif">
<img src="./images/m2.gif">
<img src="./images/m2_clk.gif">
<img src="./images/m3.gif">
<img src="./images/m3_clk.gif">
<img src="./images/m4.gif">
<img src="./images/m4_clk.gif">
<img src="./images/m5.gif">
<img src="./images/m5_clk.gif">
<img src="./images/m6.gif">
<img src="./images/m6_clk.gif">
<img src="./images/m7.gif">
<img src="./images/m7_clk.gif">
<img src="./images/m8.gif">
<img src="./images/m8_clk.gif">
<img src="./images/m9.gif">
<img src="./images/m9_clk.gif">
<img src="./images/m10.gif">
<img src="./images/m10_clk.gif">
<img src="./images/m11.gif">
<img src="./images/m11_clk.gif">
<img src="./images/m12.gif">
<img src="./images/m12_clk.gif">
<img src="./images/m13.gif">
<img src="./images/m13_clk.gif">
<img src="./images/m14.gif">
<img src="./images/m14_clk.gif">
</div>
<form name="payForm" method="post" action="https://www.twv.com.tw/openpay/pay.php">
 <input type="hidden" name="version" value="2.1">
 <input type="hidden" name="mid" value="">
 <input type="hidden" name="txid" value="">
 <input type="hidden" name="amount" value="">
 <input type="hidden" name="mode" value="1">
 <input type="hidden" name="iid" value="0">
 <input type="hidden" name="verify" value="">
 <input type="hidden" name="cname" value="">
 <input type="hidden" name="caddress" value="">
 <input type="hidden" name="ctel" value="">
 <input type="hidden" name="cemail" value="">
 <input type="hidden" name="expire_date" value="">
 <input type="hidden" name="select_paymethod" value="">
 <input type="hidden" name="language" value="tchinese">
 <input type="hidden" name="charset" value="UTF-8">
 <input type="hidden" name="return_url" value="http://{$WEB_HOST}/writeoff.php">
</form>
<script language="javascript">
	function openPay(mid, txid, amount, verify, cname, caddress, ctel, cemail, select_paymethod, expire_date){
		var payForm = document.payForm;
		payForm.mid.value = mid;
		payForm.txid.value = txid;
		payForm.amount.value = amount;
		payForm.verify.value = verify;
		payForm.cname.value = cname;
		payForm.caddress.value = caddress;
		payForm.ctel.value = ctel;
		payForm.cemail.value = cemail;
		payForm.expire_date.value = expire_date;
//		alert(expire_date);
		payForm.select_paymethod.value = select_paymethod;
		payForm.submit();
	}
	function Dialog1(url, h){
		$.fn.colorbox({opacity: 0.5, width:800, height:h, href:url, iframe:true});
	}
	function Dialog(url){
		$.fn.colorbox({opacity: 0.5, width:800, maxHeight:560, href:url});
	}
	function dialogClose(){
		$.fn.colorbox.close();
	}
</script>
<script type="text/javascript">
$(function() {
	$("a.prompt").colorbox({
		opacity: 0.5, 
		width:800, 
		maxHeight:560,
		onClosed:function(){window.location.reload();} 
	});
});
</script>
<script language="javascript">
	function setUserInfo(){
		$("#userinfo").load("userinfo2.php");
	}
	function Move(){
		$.post(
			'move.php',
			{
				action: 'moving'
			},
			function(data)
			{
				setTimeout("Move()", 60000);
			}
		);		
	}
</script>

<script language="javascript">setUserInfo();</script>
<script language="javascript">setTimeout("Move()", 60000);</script>
