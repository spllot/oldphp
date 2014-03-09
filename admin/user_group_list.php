<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->user][1])){exit("權限不足!!");}
$group = $_REQUEST["group"];  
?>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<body leftmargin="0" topmargin="0">
<form name="lForm">
<?php
include '../include/db_open.php';
$result=mysql_query("SELECT No, Name FROM Catalog WHERE useFor = 'GROUP' ORDER BY Sort") or die (mysql_error());
echo "<table>";
$i = 0;
$grouplist = ",";
while(list($no, $name) = mysql_fetch_row($result)){
	echo "<tr>";
	echo "<td><input type=\"checkbox\" name=\"gp\" value=\"$no\"";
    if (strpos("," . $group, "," . $no . ",") > -1){
		echo " CHECKED";
	}
	echo "></td><td><a href=\"javascript:void(0)\" onClick=\"lForm.gp[$i].click();\" style=\"text-decoration:none; color:black\">$name</a></td>";
	echo "</tr>";
	$i ++;
	$grouplist .= $no . ",";
}
echo "</table>";
include '../include/db_close.php';
?>
<input type="hidden" name="grouplist" value="<?php echo $grouplist?>">
</form>
