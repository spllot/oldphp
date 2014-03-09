<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->ip][1])){exit("權限不足!!");}
$ip = $_REQUEST["ip"];
if ($ip != ""){
    include("../include/db_open.php");
	$ips = explode("\n", $ip);
	for($i=0; $i<sizeof($ips); $i++){
		$sql = "SELECT * FROM Deny WHERE IP = '" . $ips[$i] . "'";
		$result = mysql_query($sql) or die("MySQL Failed: " . mysql_error());
		if(($num = mysql_num_rows($result))==0){
			mysql_query("INSERT INTO Deny(IP) VALUES('" . $ips[$i] . "')");
		}//if
		else{
		}//else
	}
    include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Redirect("ip.php?pageno=$pageno&keyword=$keyword");
?>