<?
ini_set("session.save_path", $_SERVER['DOCUMENT_ROOT'] . "/tmp/"); 
session_start();
require_once './class/javascript.php';
JavaScript::setCharset("big5");
if ($_SESSION['userid'] == ""){
    JavaScript::Alert("您尚未登入系統!!");
    JavaScript::Redirect("./", "window.top");
	exit;
}//if
else{
	include './include/db_open.php';
	mysql_query("UPDATE Member SET lastMove = CURRENT_TIMESTAMP WHERE userID = '" . $_SESSION['userid'] . "'") or die(JavaScript::Alert(addslashes(mysql_error())));
	include './include/db_close.php';
}
?>