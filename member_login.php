<?php
include './include/session.php';
require_once './class/javascript.php';

$tab = 3;

if(!empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您已登入!");
	JavaScript::setURL("./", "window.parent");
}
$WEB_CONTENT = <<<EOD
<table cellpadding="0" cellspacing="0" border="0" style="width:100%; background:white; height:560px">
	<tr>
		<td style="vertical-align:top; text-align:center" valign="top" align="center">

<center>
<table border=0>
	<tr>
		<td style="border-bottom:solid 1px gray; line-height:40px; text-align:left; font-weight:bold">會員登入</td>
	</tr>
	<tr>
		<td>
<form name="iForm" method="post" action="member_login_save.php">
<input type="hidden" name="url" value="{$_REQUEST['url']}">
<table>
	<tr>
		<td style="text-align:right" nowrap>E-mail帳號：</td>
		<td style="text-align:left"><input type="text" style="width:300px" name="email"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>密碼：</td>
		<td style="text-align:left"><input type="password" style="width:300px" name="pass1"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>驗證碼：</td>
		<td style="text-align:left">
			<input type="text" style="width:300px" name="captcha"><font color=red>*</font>
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
			<input type="button" value="確  定" onClick="Save();" class="btn" style="width:150px">
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:left"><a href="member_forgot.php">忘記密碼?</a> </td>
	</tr>
</table>
</form>
		</td>
	</tr>
</table>
</center>
		</td>
	<tr>
</table>
EOD;
include 'template.php';
?>

<script type="text/javascript" language="javascript">
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
function check_email(email){
	var len = email.length;
	for(var i=0;i<len;i++){
		var c= email.charAt(i);
		if(!((c>="A"&&c<="Z")||(c>="a"&&c<="z")||(c>="0"&&c<="9")||(c=="-")||(c=="_")||(c==".")||(c=="@")))
			return "您的電子郵件地址只能是數字,英文字母及'-','_'等符號,其他的符號都不能使用 !\n";
	}
	if((email.indexOf("@")==-1)||(email.indexOf("@")==0)||(email.indexOf("@")==(len-1)))
		return "您的電子郵件地址不合法 !\n";
	if((email.indexOf("@")!=-1)&&(email.substring(email.indexOf("@")+1,len).indexOf("@")!=-1))
		return "您的電子郵件地址不合法 !\n";
	if((email.indexOf(".")==-1)||(email.indexOf(".")==0)||(email.lastIndexOf(".")==(len-1)))
		return "您的電子郵件地址不完全 !\n";
	return "";
}
function Save(){
	var err = check_email(iForm.email.value);
	if(!iForm.email.value){
		alert("請輸入電子郵件!");
		iForm.email.focus();
	}
	else if(err != ""){
		alert(err);
		iForm.email.focus();
	}
	else if(!iForm.pass1.value){
		alert("請設定密碼!");
		iForm.pass1.focus();
	}
	else if(!iForm.captcha.value){
		alert("請輸入驗證碼!");
		iForm.captcha.focus();
	}
	else{
		iForm.submit();
	}
}
</script>