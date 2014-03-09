<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->benefit][1])){exit("權限不足!!");}
$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->benefit));
$html = <<<EOD
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script language="Javascript1.2"> 
  _editor_url = "../js/";
	var win_ie_ver = parseFloat(navigator.appVersion.split("MSIE")[1]);
	if (navigator.userAgent.indexOf('Mac') >= 0){
		win_ie_ver = 0;
	}//if
	if (navigator.userAgent.indexOf('Windows CE') >= 0){
		win_ie_ver = 0;
	}//if
	if (navigator.userAgent.indexOf('Opera') >= 0){
		win_ie_ver = 0;
	}//if
	if (win_ie_ver >= 5.5) {
		document.write('<scr' + 'ipt src="' + _editor_url + 'editor.js"');
		document.write(' language="Javascript1.2"></scr' + 'ipt>');
	}//if
	else{
		document.write('<scr'+'ipt>function editor_generate() { return false; }</scr'+'ipt>');
	}//else
</script>

	<form name="iForm" method="post" action="epaper_save.php" enctype="multipart/form-data">
<table>
	<tr style="display:none">
		<td class="html_label_required">發送目標：</td><Td>
			<input type="radio" name="to" value="1" checked onClick="showRecipients();">全部會員
			<input type="radio" name="to" value="2" onClick="showRecipients();">買家會員
			<input type="radio" name="to" value="3" onClick="showRecipients();">賣家會員
			<input type="radio" name="to" value="4" onClick="showRecipients();">自組郵件
		</td>
	</tr>
	<tr id="recipients" style="display:none"><td class="html_label_required">收件人：</td>
		<td><textarea name="recipients" style="width:600px; height:100px"></textarea></td>
	</tr>
	<tr>
		<td class="html_label_required">標題：</td><Td><input type="text" name="subject" style="width:600px"></td>
	</tr>
	<tr>
		<td class="html_label_required">內容：</td><Td><textarea name="content" style="width:600px; height:200px"></textarea></td>
	</tr>
	<tr>
		<td colspan="2"><hr>
			<table width="100%">
				<tr>
					<td align="center"><input type="button" value="送出" onClick="Save();"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
	</form>



EOD;
$page->addContent($html);
$page->show();
?>
<script language="javascript">
editor_generate('content');
</script>
<script language="javascript">
	function showRecipients(){
		if(iForm.to[3].checked){
			$("#recipients").show();
		}
		else{
			$("#recipients").hide();
		}
	}
	function Save(){
		var iForm = document.iForm;
		if(iForm.to[3].checked && !iForm.recipients.value){
			alert("請輸入收件人!");
			iForm.recipients.focus();
		}
		else if(!iForm.subject.value){
			alert("請輸入標題!");
			iForm.subject.focus();
		}
		else if(!iForm.content.value){
			alert("請輸入內容!");
		}
		else if(confirm("確定要發送?")){
			iForm.submit();
		}
	}

</script>
