<?
ini_set("session.save_path", "../tmp/");
session_start();

//$expireTime = 60*60*24;
//session_set_cookie_params($expireTime);
require_once '../class/javascript.php';
if ($_SESSION['admin'] == ""){
	JavaScript::setCharset("UTF-8");
    JavaScript::Alert("您尚未登入系統!!");
    JavaScript::setURL("login.php", "top.main");
	exit;
}//if
?>