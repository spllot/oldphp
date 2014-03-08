<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include './include/db_open.php';
$no = $_REQUEST['no'];
$result = mysql_query("SELECT * FROM Project WHERE No='$no'");
$rs = mysql_fetch_array($result);

include './include/db_close.php';
?>

<title>InTimeGo—<?=$rs['Subject']?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<?php

echo (($rs['Url'] != "") ? "網址：<a href='{$rs['Url']}' target='_blank'>{$rs['Url']}</a><br>":"");
echo $rs['Content'];


?>
