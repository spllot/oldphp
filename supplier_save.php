<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';

$area=$_REQUEST['area'];
$service=$_REQUEST['s'];
$name=$_REQUEST['name'];
$memo=$_REQUEST['memo'];
$contact=$_REQUEST['contact'];

JavaScript::setCharset("UTF-8");
if($area != "" && $service != "" && $name != "" && $memo != "" && $contact != ""){
	include './include/db_open.php';
	$sql = "INSERT INTO Supplier(Area, Service, Name, Memo, Contact, dateAdd) VALUES ('$area', '$service', '$name', '$memo', '$contact', CURRENT_TIMESTAMP)";
	mysql_query($sql) or die (mysql_error());
	JavaScript::Alert("資料已送出，謝謝您!");
	JavaScript::Execute("parent.iContent.location.href='contact.php'");
	JavaScript::Execute("parent.$.fn.colorbox.close();");
	include './include/db_close.php';
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
