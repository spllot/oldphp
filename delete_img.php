<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
else if($_SESSION['member']['Seller'] != 2){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("申請成為賣家後; 需做登出動作; 然後才可正常使用[我是賣家]功能!");
	JavaScript::Redirect("./member_form.php");
	exit;
}
$name=$_POST['name'];

@unlink(getcwd() . "/upload/thumb_".$name);
@unlink(getcwd() . "/upload/".$name);
/*


include '../include/db_open.php';
$sql = "DELETE FROM Photos WHERE Path = '$name'";
mysql_query($sql) or die (mysql_error());
include '../include/db_close.php';
*/
?>