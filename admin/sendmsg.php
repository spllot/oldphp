<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->sendmsg][1])){exit("權限不足!!");}
$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->sendmsg));
$html = <<<EOD

<script type="text/javascript" src="../js/jquery.min.js"></script>
	<form name="iForm" method="post" action="sendmsg_save.php">
<table>
	<tr>
		<td class="html_label_required">發送目標：</td><Td>
			<input type="radio" name="to" value="1" checked onClick="showRecipients();">全部會員
			<input type="radio" name="to" value="2" onClick="showRecipients();">買家會員
			<input type="radio" name="to" value="3" onClick="showRecipients();">賣家會員
			<input type="radio" name="to" value="4" onClick="showRecipients();">自組郵件
		</td>
	</tr>
	<tr>
		<td class="html_label_required">類型：</td>
		<Td>
			<input type="radio" name="type" value="1">系統訊息
			<input type="radio" name="type" value="2">公共訊息
			<input type="radio" name="type" value="3" checked>私人訊息
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
		if(!iForm.type[0].checked && !iForm.type[1].checked && !iForm.type[2].checked){
			alert("請選擇類型!");
		}
		else if(iForm.to[3].checked && !iForm.recipients.value){
			alert("請輸入收件人!");
			iForm.recipients.focus();
		}
		else if(!iForm.subject.value){
			alert("請輸入標題!");
			iForm.subject.focus();
		}
		else if(!iForm.content.value){
			alert("請輸入內容!");
			iForm.content.focus();
		}
		else if(confirm("確定要發送?")){
			iForm.submit();
		}
	}

</script>
