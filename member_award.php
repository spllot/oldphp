<?php
include './include/session.php';

include './include/db_open.php';
$result = mysql_query("SELECT * FROM Page WHERE useFor = 'PGE_REFERRAL'");
$data = mysql_fetch_array($result);



include './include/db_close.php';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo—會員註冊</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?
	echo $data['Content'];

?>
