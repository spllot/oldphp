<?php
include './include/session.php';
$seller = $_REQUEST['no'];

include './include/db_open.php';



if(!$_SESSION['WELCOME']){
	$result = mysql_query("SELECT * FROM Config WHERE YN = 'Y' AND ID='welcome'");
	if(mysql_num_rows($result) > 0){
		$welcome = true;
	}
}

$sql = "SELECT *, getDistance(Product.Latitude, Product.Longitude, '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM FROM Product WHERE Status = 2 ORDER BY KM";
$result = mysql_query($sql) or die(mysql_error());

$num = mysql_num_rows($result);
$pagesize  = 10;
$pages = ceil($num / $pagesize);
$pageno = $_REQUEST['pageno'];
if($pageno == "" || $pageno > $pages){$pageno = 1;}

include './include/db_close.php';

//include 'member_search.php';
//include 'member_layout.php';
$url = (($_REQUEST['url']) ? $_REQUEST['url'] : "member_product4.php?seller=$seller");

if(!$welcome){
}
echo "<script language='javascript'>window.location.href='$url';</script>";
?>