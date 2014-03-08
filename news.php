<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
include './include/db_open.php';
$no = $_REQUEST['no'];

$result = mysql_query("SELECT * FROM News WHERE No='$no'") or die(mysql_error());
$data = mysql_fetch_array($result);

echo "<h3>{$data['Subject']}</h3>";
echo $data['Content'];

include './include/db_close.php';
?>