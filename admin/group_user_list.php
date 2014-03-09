<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->group][1])){exit("權限不足!!");}
?>
<body leftmargin="0" topmargin="0">
<form name="lForm">
<?php
include '../include/db_open.php';
$result=mysql_query("SELECT No, userID, userName FROM Admin WHERE userID != '" . $_USER->adminID . "' ORDER BY userID") or die (mysql_error());
echo "<table>";
$i = 0;
$grouplist = ",";
while(list($no, $userid, $username) = mysql_fetch_row($result)){
	echo "<tr>";
	echo "<td><input type=\"checkbox\" name=\"user\" value=\"$userid,$username\"></td><td><a href=\"javascript:void(0)\" onClick=\"lForm.user[$i].click();\" style=\"text-decoration:none; color:black\">$userid($username)</a></td>";
	echo "</tr>";
	$i ++;
	$grouplist .= $no . ",";
}
echo "</table>";
include '../include/db_close.php';
?>
</form>
