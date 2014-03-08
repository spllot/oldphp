<?php
include './include/session.php';
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Catalog WHERE USEFOR='TYPE_AREA' ORDER BY Sort");
while($rs = mysql_fetch_array($result)){
	$area_list .= "<option value='{$rs['No']}'>{$rs['Name']}</option>";
}
include './include/db_close.php';
$tab = 3;
$WEB_CONTENT = <<<EOD
<center>
<table cellpadding="0" cellspacing="0" border="0" style="width:100%; background:white; height:560px">
	<tr>
		<td style="vertical-align:top; text-align:center" valign="top" align="center">

<table border=0>
	<tr>
		<td style="border-bottom:solid 1px gray; line-height:40px; text-align:left; font-weight:bold; padding-left:80px">會員註冊</td>
	</tr>
	<tr>
		<td>
<form name="iForm" method="post" target="iAction" action="member_register_save.php">
<input type="hidden" name="latitude" value="">
<table border=0>
	<tr>
		<td style="text-align:right; padding-left:80px" nowrap>E-mail帳號：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="email"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right; padding-left:80px" nowrap>密碼：</td>
		<td style="text-align:left; color:gray;"><input type="password" style="width:300px" name="pass1" maxlength="20"><font color=red>*</font>&nbsp;&nbsp;6-12個字</td>
	</tr>
	<tr>
		<td style="text-align:right; padding-left:80px" nowrap>確認密碼：</td>
		<td style="text-align:left"><input type="password" style="width:300px" name="pass2" maxlength="20"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right; padding-left:80px" nowrap>手機：</td>
		<td style="text-align:left; color:gray;"><input type="text" style="width:300px" name="phone" maxlength="10"><font color=red>*</font>&nbsp;&nbsp;Ex: 0912345678</td>
	</tr>
	<tr>
		<td style="text-align:right; padding-left:80px" nowrap>真實姓名：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="name"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right; padding-left:80px" nowrap>收貨地址：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="address" onChange="getLatitude(this);"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right; padding-left:80px" nowrap valign="top">介紹人手機：<br>(or 專案代碼)&nbsp;&nbsp;</td>
		<td style="text-align:left; color:gray; padding-right:30px; color:gray" valign="top"><input type="text" style="width:300px" name="referral" maxlength="10">
					<Table align="left">
						<tr>
							<td valign="top" width=60 style="color:gray; text-align:right">[說明]：</td>
							<td valign="top" align="left" style="color:gray">手機號碼範例:0912345678; 專案代碼範例: 001<br>
若您是由會員好友邀請您加入即購網，請輸入好友的手機號碼，您的好友將可獲得會員升級之優惠方案! 若您是參與專案推廣加入即購網，請輸入專案代碼，自己將有機會獲獎喔! 詳情參考"全民賺好康活動"。</td>
						</tR>
					</table>
		</td>
	</tr>
	<tr>
		<td style="text-align:right"></td>
		<td style="text-align:left"><input type="checkbox" name="agree" value="1">我已閱讀並願意遵守<a href="javascript:parent.Dialog('policy.php')">電子商務服務條款</a>
		</td>
	</tr>
	<tr>
		<td style="text-align:right; padding-left:80px" nowrap>驗證碼：</td>
		<td style="text-align:left">
			<input type="text" style="width:120px" name="captcha"><font color=red>*</font>
		</td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap></td>
		<td style="text-align:left">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="CaptchaSecurityImages.php" id="imgCaptcha" /></td>
					<td style="width:10px">&nbsp;</td>
					<td><a href="javascript:void(0);" onclick="RefreshImage('imgCaptcha');"><img src="./images/reload_vista.png" border="0"></a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><hr>
			<input type="button" value="確  定" onClick="Save();" class="btn" style="width:200px">
		</td>
	</tr>
	<tr>
		<td style="color:gray; padding-left:80px; padding-right:80px" colspan="2" align="left"><br>
		
					<!--Table align="left">
						<tr>
							<td valign="top" width=60 style="color:gray; text-align:right">[說明]：</td>
							<td valign="top" align="left" style="color:gray">當您送出”會員註冊”資料後，請至信箱收取會員確認信，點選此確認信後，您的帳號始得生效。</td>
						</tR>
					</table-->
		</td>
	</tr>
</table>
</form>
		</td>
	</tr>
</table><div id="map" style="display:none"></div>
		</td>
	<tr>
</table>
</center>
EOD;
include 'template.php';
?>
<script type="text/javascript" language="javascript">
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
    function RefreshImage(valImageId) {
        var objImage = document.getElementById(valImageId);
		iForm.captcha.value = '';
        if (objImage == undefined) {
            return;
        }
        var now = new Date();
        objImage.src = objImage.src.split('?')[0] + '?x=' + now.toUTCString();
    }
</script>
<script language="javascript">
function check_phone(phone){
	var error = false
	if( phone.length != 10 )
		return false;
	if(phone.substring(0, 2) != "09") return false;
	for( idx = 0 ; idx <phone.length ; idx++ ) {
		if( !( phone.charAt(idx)>= '0' && phone.charAt(idx) <= '9' ) ) {
			error = true;
		break;
		}
	}
	if( error == true )
		return false;
	else
		return true;
}

function check_email(email){
	var len = email.length;
	for(var i=0;i<len;i++){
		var c= email.charAt(i);
		if(!((c>="A"&&c<="Z")||(c>="a"&&c<="z")||(c>="0"&&c<="9")||(c=="-")||(c=="_")||(c==".")||(c=="@")))
			return false;
	}
	if((email.indexOf("@")==-1)||(email.indexOf("@")==0)||(email.indexOf("@")==(len-1)))
		return false;
	if((email.indexOf("@")!=-1)&&(email.substring(email.indexOf("@")+1,len).indexOf("@")!=-1))
		return false;
	if((email.indexOf(".")==-1)||(email.indexOf(".")==0)||(email.lastIndexOf(".")==(len-1)))
		return false;
	return true;
}

function Save(){

	if(!iForm.email.value){
		alert("請輸入E-mail帳號!");
		iForm.email.focus();
	}
	else if(!check_email(iForm.email.value)){
		alert("E-mail帳號格式錯誤!");
		iForm.email.focus();
	}
	else if(!iForm.pass1.value){
		alert("請設定密碼!");
		iForm.pass1.focus();
	}
	else if(iForm.pass1.value != iForm.pass2.value){
		alert("密碼不相符!");
		iForm.pass2.focus();
	}
	else if(!iForm.phone.value){
		alert("請輸入手機!");
		iForm.phone.focus();
	}
	else if(!check_phone(iForm.phone.value)){
		alert("手機格式錯誤!");
		iForm.phone.focus();
	}
	else if(!iForm.name.value){
		alert("請輸入真實姓名!");
		iForm.name.focus();
	}
	else if(!iForm.address.value){
		alert("請輸入收貨地址!");
		iForm.address.focus();
	}
	else if(!iForm.agree.checked){
		alert("請仔細閱讀電子商務服務條款，並勾選願意遵守!");
	}
	else if(!iForm.captcha.value){
		alert("請輸入驗證碼!");
		iForm.captcha.focus();
	}
	else{
		setTimeout("iForm.submit();", 2000);
	}
}
</script>
<script language="javascript">
	google.load("maps", "2",{"other_params":"sensor=true"});
</script>