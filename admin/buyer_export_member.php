<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->buyer_export][1])){exit("權限不足!!");}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html style="height:250px">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php


$no=$_REQUEST['no'];
$result = mysql_query("SELECT * FROM Member WHERE No='$no'") or die(mysql_error());
if($member=mysql_fetch_array($result)){
	echo <<<EOD
		<center>
		<table>
			<tr>
				<td style="text-align:right">手機號碼：</td>
				<td style="text-align:left">{$member['Phone']}</td>
			</tr>
			<tr>
				<td style="text-align:right">EMail/帳號：</td>
				<td style="text-align:left">{$member['userID']}</td>
			</tr>
			<tr>
				<td style="text-align:right">商家姓名：</td>
				<td style="text-align:left">{$member['Name']}</td>
			</tr>
			<tr>
				<td style="text-align:right">郵遞區號：</td>
				<td style="text-align:left">{$member['rZip']}</td>
			</tr>
			<tr>
				<td style="text-align:right">商家地址：</td>
				<td style="text-align:left">{$member['rAddress']}</td>
			</tr>
			<tr style="display:none">
				<td style="text-align:right">發票類別：</td>
				<td style="text-align:left">電子發票</td>
			</tr>
			<tr>
				<td style="text-align:right">統一編號：</td>
				<td style="text-align:left">{$member['uniNo']}</td>
			</tr>
			<tr>
				<td style="text-align:right">發票抬頭：</td>
				<td style="text-align:left">{$member['rName']}</td>
			</tr>
		</table>
		<hr>
		<input type="button" value="關閉" onClick="window.close();">
		</center>
EOD;
}
?>