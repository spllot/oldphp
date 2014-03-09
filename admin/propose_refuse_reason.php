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
<?php


$no=$_REQUEST['no'];
$result = mysql_query("SELECT * FROM Product WHERE No='$no'") or die(mysql_error());
if($member=mysql_fetch_array($result)){
	echo <<<EOD
		<center>
		<table>
			<tr>
				<td style="text-align:right">退回理由：</td>
				<td style="text-align:left"><textarea style="width:300px; height:150px">{$member['Reason']}</textarea></td>
			</tr>
		</table>
		<hr>
		<input type="button" value="關閉" onClick="window.close();">
		</center>
EOD;
}
?>