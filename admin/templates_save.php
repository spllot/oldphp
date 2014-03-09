<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->templates][1])){exit("權限不足!!");}
$no = $_REQUEST["no"];
$subject = $_REQUEST["subject"];
$content = $_REQUEST["content"];
$message = $_REQUEST["message"];
$sms = $_REQUEST["sms"];
if ($subject != ""){
    include("../include/db_open.php");
    if ($no != "" && $no > 0){
            $sql = "UPDATE Template SET Subject = '$subject', Content = '$content', SMS='$sms', Message='$message' Where No = '$no'";
            mysql_query($sql) or die("MySQL Failed: " . mysql_error());
    }//if
    else{
            $sql = "INSERT INTO Template (Subject, Content) VALUES ('$subject', '$content')";
            mysql_query($sql) or die("MySQL Failed: " . mysql_error() . $sql);
    }//else
    include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Redirect("templates.php?pageno=$pageno");
?>