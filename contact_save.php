<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';

$catalog=$_REQUEST['catalog'];
$name=$_REQUEST['name'];
$email=$_REQUEST['email'];
$content=$_REQUEST['content'];
$intro=$_REQUEST['intro'];
$contact=$_REQUEST['contact'];
$phone=$_REQUEST['phone'];

JavaScript::setCharset("UTF-8");
if($catalog != "" && $name != "" && $email != ""){
	if(!Tools::checkEMail($email)){JavaScript::Alert("電子郵件格式錯誤!!");exit;}
	include './include/db_open.php';
	$ip =  Tools::getRemoteIP();
	$sql = "INSERT INTO Contact(Catalog, Name, EMail, Content, Intro, Contact, Phone, dateSubmited, ipSubmited) VALUES ('$catalog', '$name', '$email', '$content', '$intro', '$contact', '$phone', CURRENT_TIMESTAMP, '$ip')";
	mysql_query($sql) or die (mysql_error());
	JavaScript::Alert("謝謝您，我們會盡快為您處理!");
	JavaScript::setURL("contact.php" ,"window.parent");
	include './include/db_close.php';
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
