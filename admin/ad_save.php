<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->ad][1])){exit("權限不足!!");}
$no = $_REQUEST["no"];
$caption = $_REQUEST["caption"];
$url = $_REQUEST["url"];
$country = $_REQUEST["country"];
$icon = basename($_REQUEST["ad_picpath"]);
$sortOrder = $_REQUEST['sort'];
$src = $_REQUEST['src'];
$link = $_REQUEST['link'];
$usefor = "BANNER";
//print_r($_REQUEST);
if ($caption != ""){
    include("../include/db_open.php");
    if ($no > 0){
        $sql = "UPDATE AD SET Src='$src', Link='$link', Url='$url', Icon='$icon', Caption = '$caption', Sort = '$sortOrder', useFor = '$usefor', Country='$country' Where No = '$no'";
        mysql_query($sql) or die("MySQL Failed: " . mysql_error());
    }//if
    else{
		$sql = "INSERT INTO AD (Src, Link, Url, Icon, Caption, useFor, Sort, Country) VALUES ('$src', '$link', '$url', '$icon', '$caption', '$usefor', '$sortOrder', '$country')";
        mysql_query($sql) or die("MySQL Failed: " . mysql_error() . $sql);
		mysql_query("UPDATE AD SET Sort = Sort + 1 WHERE useFor='$usefor'") or die(mysql_error());
	}//else
    include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Redirect("ad.php?pageno=$pageno");
?>