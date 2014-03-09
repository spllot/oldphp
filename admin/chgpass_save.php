<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';

$oldpass = $_REQUEST["oldpass"];
$userpass = $_REQUEST["userpass"];
$userpass1 = $_REQUEST["userpass1"];
JavaScript::setCharset("UTF-8");
if ($oldpass == ""){
    JavaScript::Alert("請輸入舊密碼!!");
    JavaScript::Redirect("chgpass.php");
}
else if($userpass == ""){
    JavaScript::Alert("請設定新密碼!!");
    JavaScript::Redirect("chgpass.php");
}
else if ($userpass != $userpass1){
    JavaScript::Alert("請新密碼不相符!!");
    JavaScript::Redirect("chgpass.php");
}
else{
    include("../include/db_open.php");
    $result=mysql_query("SELECT * FROM Admin WHERE userID = '" . $_SESSION["admin"] . "' AND userPass=PASSWORD('$oldpass')");
    if(($num=mysql_num_rows($result))==1){
        mysql_query("UPDATE Admin SET userPass = PASSWORD('$userpass') WHERE userID = '" . $_SESSION["admin"] . "'");
        JavaScript::Alert("密碼已變更，請使用新密碼重新登入!!");
        JavaScript::Redirect("logout.php");
    }//if
    else{
        JavaScript::Alert("舊密碼錯誤!!");
        JavaScript::Redirect("chgpass.php");
    }//else
    include("../include/db_close.php");
}//if

?>
