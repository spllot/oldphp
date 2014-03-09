<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/tools.php';
require_once '../class/system.php';
require_once '../class/javascript.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->page][1])){exit("權限不足!!");}
$no =  Tools::parseInt2($_REQUEST["no"],0);
$code = $_REQUEST["code"];
$content = $_REQUEST["content"];
$url = $_REQUEST['url'];
if(($code != "" || $no != "") && $content != ""){
	include ("../include/db_open.php");
	if($no != ""){
		mysql_query("UPDATE Project SET Url='$url', Content='$content' WHERE No = '$no'");
	}
	else{
		$result = mysql_query("SELECT * FROM Project WHERE Code='$code'") or die(mysql_error());
		if(mysql_num_rows($result) > 0){
			JavaScript::Alert("專案代碼重複!!");
		}
		else{
			mysql_query("INSERT INTO Project SET Code='$code', Url='$url', Content='$content'");
		}
	}
	include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
//echo $sql;
JavaScript::Redirect("page.php?pageno=$pageno&usefor=$usefor");
?>