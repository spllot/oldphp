<?php
include './include/session.php';

$product = $_REQUEST['product'];
$catalog = $_REQUEST['catalog'];
$catalog2 = $_REQUEST['catalog2'];
$catalog3 = $_REQUEST['catalog3'];

include './include/db_open.php';

$sql = "UPDATE Product SET Catalog='$catalog', Catalog2='$catalog2', Catalog3='$catalog3' WHERE No='$product' AND Member='" . $_SESSION['member']['No'] . "'";
mysql_query($sql) or die(mysql_error());

//echo $sql;
include './include/db_close.php';

?>