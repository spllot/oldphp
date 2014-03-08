<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}


$WEB_CONTENT = <<<EOD
<center>
<table border=0>
	<tr>
		<td style="border-bottom:solid 1px gray; line-height:40px; text-align:left; font-weight:bold">變更密碼</td>
	</tr>
	<tr>
		<td>
<form name="form1" method="post" target="iAction" action="member_passwd_save.php">
<input type="hidden" name="latitude" value="">
<table>
	<tr>
		<td style="text-align:right" nowrap>請輸入舊密碼：</td>
		<td style="text-align:left; color:gray;"><input type="password" style="width:120px" name="pass1" maxlength="10" value=""><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>請輸入新密碼：</td>
		<td style="text-align:left; color:gray;"><input type="password" style="width:120px" name="pass2" maxlength="10" value=""><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>請確認新密碼：</td>
		<td style="text-align:left; color:gray;"><input type="password" style="width:120px" name="pass3" maxlength="10" value=""><font color=red>*</font></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><hr>
			<input type="button" value="變更" onClick="Save();" class="btn">
		</td>
	</tr>
</table>
</form>
		</td>
	</tr>
</table>
</center>
<script language="javascript">

function Save(){
	if(!form1.pass1.value){
		alert("請輸入舊密碼!");
		form1.pass1.focus();
	}
	else if(!form1.pass2.value){
		alert("請輸入新密碼!");
		form1.pass2.focus();
	}
	else if(form1.pass2.value!=form1.pass3.value){
		alert("新密碼不相符，請確認!");
		form1.pass3.focus();
	}
	else{
		form1.submit();
	}
}
</script>
EOD;

include 'template2.php';
?>

