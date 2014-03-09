<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->propose][1])){exit("權限不足!!");}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html style="height:250px">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<center>
<form name="iForm" method="post" target="iAction" action="propose_refuse_save.php">
<input type="hidden" name="memberlist" value="">
<table>
	<tr>
		<td style="text-align:right">退回理由：</td>
		<td style="text-align:left"><textarea name="reason" style="width:300px; height:150px"></textarea></td>
	</tr>
</table>
<hr>
<input type="button" value="確定退回" onClick="Refuse();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="取消" onClick="window.close();">
</form>
<iframe name="iAction" style="width:100%; height:50px;"></iframe>
</center>
<script language="javascript">
	iForm.memberlist.value =  window.dialogArguments;
</script>
<script language="javascript">
	function Refuse(){
		if(iForm.reason.value){
			iForm.submit();
		}
		else{
			alert("請輸入退回理由!");
		}
	}
</script>