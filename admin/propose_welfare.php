<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
require_once("../class/javascript.php");
require_once("../class/tools.php");
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->propose][1])){exit("權限不足!!");}
$no = $_REQUEST["no"];
$welfare = $_REQUEST['welfare'];
if ($no <> ""){
		include("../include/db_open.php");
		
		$sql = "UPDATE Product SET welfare='$welfare' WHERE No='$no'";
		$result = mysql_query($sql) or die(mysql_error());

		include("../include/db_close.php");
}//if
?>