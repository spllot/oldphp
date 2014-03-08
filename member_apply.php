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

$result = mysql_query("SELECT * FROM Config") or die(mysql_error());
while($rs=mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}

$result = mysql_query("SELECT IFNULL(COUNT(*), 0) FROM AD WHERE Member=0 OR (Member > 0 AND dateExpire > CURRENT_TIMESTAMP)") or die(mysql_error());
list($ad_counts) = mysql_fetch_row($result);


$result = mysql_query("SELECT * FROM Member WHERE userID = '" . $_SESSION['member']['userID'] . "'");
$data = mysql_fetch_array($result);

$subscribe = (($data['Subscribe'] == 1) ? " CHECKED":"");
$result = mysql_query("SELECT * FROM Catalog WHERE USEFOR='TYPE_AREA' ORDER BY Sort");
while($rs = mysql_fetch_array($result)){
	$area_list .= "<option value='{$rs['No']}'" . (($rs['No'] == $data['subscribeArea']) ? " SELECTED":"") . ">{$rs['Name']}</option>";
}


$sql = "SELECT DISTINCT LEFT(dateLogin, 10) FROM logLogin WHERE userID = '" . $_SESSION['member']['userID'] . "' AND Year(dateLogin) = '" . date('Y') . "' AND Month(dateLogin) = '" . date('n') . "'";
//echo $sql;
$result = mysql_query($sql);
$days = mysql_num_rows($result);
$result = mysql_query("SELECT IFNULL(COUNT(*), 0)FROM Member WHERE Referral = '" . $_SESSION['member']['Phone'] . "'");
$rs = mysql_fetch_row($result);
$referral = $rs[0];
$left = 0;
$max = 0;
$use = 0;
$curr = date('Y-m');

$result = mysql_query("SELECT * FROM Config WHERE ID='$curr'");
if($rs = mysql_fetch_array($result)){
	$max = $rs['YN'];
}

$result = mysql_query("SELECT IFNULL(COUNT(*), 0) FROM Blog WHERE dateSubmited LIKE '$curr%'");
if($rs = mysql_fetch_row($result)){
	$use = $rs[0];
}

$left = $max - $use;

$b_disabled = (($left <=0) ? " disabled" : "");

$result = mysql_query("SELECT * FROM Blog WHERE dateSubmited LIKE '$curr%' AND userID = '" . $_SESSION['member']['userID'] . "'");
if(mysql_num_rows($result) > 0){
	$blog = mysql_fetch_array($result);
	if($blog['dateConfirmed'] != "0000-00-00 00:00:00"){
		$score = <<<EOD
		本月部落格行銷文章總得分 (1分=1點儲值金)：{$blog['Earn']}</font><br>
		(得分：行銷說服力：{$blog['S1']}</font>，表達能力：{$blog['S2']}</font>，豐富性：{$blog['S3']}</font>，推薦人數：{$blog['S4']}</font>，回應人數：{$blog['S5']}</font>)
EOD;
	}
	else{
		$score = <<<EOD
		本月部落格行銷文章總得分 (1分=1點儲值金)：審核中</font><br>
EOD;
	}
	$b_disabled = " disabled";
}
else{
	$score = <<<EOD
		本月部落格行銷文章總得分 (1分=1點儲值金)：尚未申請</font><br>
EOD;
}

$result = mysql_query("SELECT IFNULL(SUM(Amount), 0) FROM logTransaction WHERE useFor=6 AND Owner='" . $_SESSION['member']['userID'] . "'");
list($money_blog) = mysql_fetch_row($result);
$result = mysql_query("SELECT IFNULL(SUM(Amount), 0) FROM logTransaction WHERE useFor=7 AND Owner='" . $_SESSION['member']['userID'] . "'");
list($money_share) = mysql_fetch_row($result);
$result = mysql_query("SELECT IFNULL(SUM(Amount), 0) FROM logTransaction WHERE Owner='" . $_SESSION['member']['userID'] . "'");
list($money_total) = mysql_fetch_row($result);





include './admin/ad2_usefor.php';



$pics = "/images/ad_none.png";
$ad_picpath = (($icon != "") ? basename($icon):"/images/ad_none.png");
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_PRO' AND Parent=0");
$catalog_list = "";
while($rs=mysql_fetch_array($result)){
	if($catalog == ""){$catalog = $rs['No'];}
	$catalog_list .= "<option value='" . $rs['No'] . "'" . ((1 == 2 ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}

foreach($usefors as $value => $text){
	if($usefor == ""){$usefor = $value;}
	$use_list .= "<option value=\"$value\"" . ((1 == 2) ? " SELECTED" : "") . ">$text</option>";
}




$ad_disabled = (($ad_counts >= $_CONFIG['admax1']) ? " DISABLED" : "");

include './include/db_close.php';
$WEB_CONTENT = <<<EOD
<center>
<table border=0>
	<tr>
		<td style="border-bottom:solid 1px gray; line-height:40px; text-align:left; font-weight:bold">申請與設定資訊</td>
	</tr>
	<tr>
		<td style="border-bottom:solid 1px gray">
		<form name="iForm" method="post" target="iAction" action="member_info_save.php">
			<table>
				<tr style="height:30px">
					<td colspan="2" style="text-align:left" align="left"><input type="checkbox" name="subscribe" value="1"{$subscribe}>
			願意收到"即購網"相關好康資訊，所在地：<select name="subscribe_area" style="width:130px"><option value="">--請選擇--</option>{$area_list}</select><font color=red>*</font></td>
				</tr>
				<tr style="height:30px">
					<td colspan="2" style="text-align:left" align="left">
					
					訂閱電子報 (填寫e-mail)：
					
					<input type="text" name="email" style="width:260px">
					
					 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="訂　　閱" onClick="Subscribe();"></td>
				</tr>
				<tr style="height:30px">
					<td colspan="2" style="text-align:left" align="left">本月部落格行銷文章徵求，尚有{$left}篇等待徵求 (請參考：<a href="javascript:parent.Dialog('blog.php');">徵求辦法</a>) &nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" value="我要發表" onClick="Blog();"{$b_disabled}></td>
				</tr>
			</table>
		</form>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td style="border-bottom:solid 1px gray; line-height:40px; text-align:left; font-weight:bold">廣告申購</td>
	</tr>
	<tr>
		<td>
			<Table align="center" width="660" border=0>
				<tr>
					<td valign="top" width=40 style="color:gray">[註]：</td>
					<td valign="top" align="left" style="color:gray">以日計價, 請購買儲值金扣抵，前台右側廣告&#36;{$_CONFIG['adfee1']}/日，前台下方分類廣告&#36;{$_CONFIG['adfee2']}/日。 <br>
					按下“確定申請”按鍵之後，系統立即將您所購買之廣告上線。
					</td>
				</tR>
			</table>
		</td>
	</tr>
	<tr>
		<td style="border-bottom:solid 1px gray">
		<form name="aForm" method="post" action="member_apply_ad.php" target="iAction">
		<input type="hidden" name="total">
		<input type="hidden" name="fee1" value="{$_CONFIG['adfee1']}">
		<input type="hidden" name="fee2" value="{$_CONFIG['adfee2']}">
		<table>
			<tr>
				<td>購買方案：</td>
				<td style="text-align:left">
					<input type="radio" name="fee" value="1" onClick="setTotal();"{$ad_disabled}>前台右側廣告&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="fee" value="2" onClick="setTotal();">前台下方分類廣告
				</td>
			</tr>
			<tr>
				<td>廣告名稱：</td>
				<td style="text-align:left"><input type="text" name="name" style="width:550px"></td>
			</tr>
			<tr>
				<td>連結網址：</td>
				<td style="text-align:left"><input type="text" name="url" style="width:550px"></td>
			</tr>
			<tr>
				<td></td>
				<td style="text-align:left; color:gray">Ex. 網址範例：http://www.intimego.com/...。</td>
			</tr>
			<tr id="dis" style="display:none">
				<td>折　　數：</td>
				<td style="text-align:left">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td style="text-align:left; width:140px"><input type="text" name="discount" style="width:50px">折</td>
							<td>類別：</td>
							<td style="text-align:left; width:140px ">
								<select name="usefor">
									<option value="">請選擇</option>{$use_list}
								</select>
							</td>
							<td>分類：</td>
							<td style="text-align:left">
								<select name="catalog">
									<option value="">請選擇</option>{$catalog_list}
								</select>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td valign="top">圖片來源：</td>
				<td style="text-align:left">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<table>
									<tr>
										<td>
											<div style="float:left"><input type="image" id="upload" src="./images/icon_upld.png" /></div>
											<div style="float:left">(189x114)</div>
											
												<br>
											<input type="hidden" name="ad_picpath" id="ad_picpath" value="" />
										</td>
									</tr>
									<tr>
										<td colspan="2" style="padding-left:24px">
											<div id="cbox"><img src="{$pics}" width="189" height=114 title="圖片"></div>
											<div style="font-size:10pt; display:none" id="loading">
												<table cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td><img src="./images/loader_light_blue.gif"></td>
													</tr>
												</table>
											</div>
										</td>
									</tR>
								</table>
							</td>
							<td>
								<fieldset>
								<table align="center">
									<Tr>
										<td>廣告日數：</td>
										<td style="text-align:left"><input type="text" name="days" style="width:50px" onKeyUp="setTotal();"></td>
										<td style="padding-left:20px;">抵付儲值金</td>
										<td><div style="width:50px; color:blue" id="total">&#36;0</div></td>
									</tr>
									<Tr>
										<td colspan="4" style="text-align:right"><Br><input type="button" value="確定申請" onClick="Apply();"></td>
									</tr>
								</table>
								</fieldset>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</form>
		</td>
	</tr>
</table><br><br><Br>
</center>
EOD;
include 'template2.php';
?>
<script language="javascript">
var iForm = document.iForm;
var aForm = document.aForm;
var total = 0;
function Subscribe(){
	iForm.submit();
}

function Blog(){
	parent.Dialog('blog_add.php');
}
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
function Apply(){
	if(!aForm.fee[0].checked && !aForm.fee[1].checked){
		alert("請選擇購買方案!");
	}
	else if(!aForm.name.value){
		alert("請輸入廣告名稱!");
		aForm.name.focus();
	}
	else if(!aForm.url.value){
		alert("請輸入連結網址!");
		aForm.url.focus();
	}
	else if(aForm.fee[1].checked && !aForm.discount.value){
		alert("請輸入折數!");
		aForm.discount.focus();
	}
	else if(aForm.fee[1].checked && !isNumber(aForm.discount.value)){
		alert("折數請輸入數字!");
		aForm.discount.focus();
	}
	else if(aForm.fee[1].checked && !aForm.usefor.value){
		alert("請選擇類別!");
		aForm.usefor.focus();
	}
	else if(aForm.fee[1].checked && !aForm.catalog.value){
		alert("請選擇分類!");
		aForm.catalog.focus();
	}
	else if(!aForm.ad_picpath.value){
		alert("請上傳圖片!");
	}
	else if(!aForm.days.value){
		alert("請輸入廣告日數!");
		aForm.days.focus();
	}
	else{
		if(confirm("確定要廣告申購，系統將自您的帳戶扣除 " + aForm.total.value + " 儲值金!")){
			aForm.submit();
		}
	}

}
function setTotal(){
	if(aForm.fee[0].checked){
		$("#dis").hide();
	}
	if(aForm.fee[1].checked){
		$("#dis").show();
	}

	if(aForm.days.value && aForm.fee[0].checked){
		total = parseInt(aForm.days.value, 10) * parseInt(aForm.fee1.value, 10);
	}
	else if(aForm.days.value && aForm.fee[1].checked){
		total = parseInt(aForm.days.value, 10) * parseInt(aForm.fee2.value, 10);
	}
	else{
		total = 0;	
	}
	aForm.total.value = total;
	$("#total").html("&#36;" + total);
}
</script>
<script language="javascript">
$(function() {
	new AjaxUpload('#upload', {
		action: 'upload_img250.php',
		onSubmit : function(file , ext){
			if (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext)){
				var d = new Date();
				var curr_hour = d.getHours();
				var curr_min = d.getMinutes();
				var curr_sec = d.getSeconds();
				document.getElementById("loading").style.display="block";
				this.setData({
					'dir':  	"upload/",
					"fname": 	'ad2_apply_'+d.getTime()+"."+ext,
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
				$('#ad_picpath').val(response);
				$('#cbox').html("<img src='/upload/"+response+"' width='189' height='114'>");
				document.getElementById("loading").style.display="none";
			}
			document.getElementById("loading").style.display="none";
		}
	});
});
</script>