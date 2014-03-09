<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->search][1])){exit("權限不足!!");}

$group = Tools::parseInt2($HTTP_POST_VARS['group'], 0);
$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->search));
$page->addJSFile("../js/search_admin.js");
include '../include/db_open.php';

$form = <<<EOD
	<form name="iForm" action="search_redirect.php" method="post" target="_blank">
	<table style="border: solid 1px #FFCCFF; vertical-align:top">
		<tr>
			<th style="width:450px; background-color:#FFCCFF">請輸入會員E-mail帳號</th>
		</tr>
		<tr>
			<td align="center"><input type="text" style="width:400px;" name="email"></td>
		</tr>
		<tr>
			<td align="center"><input type="button" value="查看會員資訊內容" onClick="Save();"></td>
		</tr>
	</table>
	</form>
<script language="javascript">
function Save(){
	if(!iForm.email.value){
		alert("請輸入會員E-mail帳號!");
		iForm.email.focus();
	}
	else{
		iForm.submit();
	}
}
</script>
EOD;
$page->addContent($form);

$page->show();

include '../include/db_close.php';
?>