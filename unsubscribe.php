<?php
include './include/session.php';
require_once './class/javascript.php';
require_once './class/tools.php';
JavaScript::setCharset("UTF-8");
$email = $_REQUEST['email'];

if(!Tools::checkEMail($email)){JavaScript::Alert("EMail格式錯誤!");exit;}
include './include/db_open.php';
if($email != ""){
	mysql_query("DELETE FROM Subscribe WHERE EMail= '$email'");
	JavaScript::Alert('已取消訂閱!');
}
JavaScript::Redirect("./");
include './include/db_close.php';
?>