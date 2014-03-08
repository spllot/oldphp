<?php
$no = $_REQUEST['no'];
$x = $_REQUEST['x'];

if($no != ""){
	include './include/db_open.php';
	$result = mysql_query("SELECT * FROM Catalog WHERE Parent='$no' ORDER BY Sort") or die(mysql_error());
	while($rs=mysql_fetch_array($result)){
		echo "<option value='{$rs['No']}'" . (($x==$rs['No']) ? " SELECTED" : "") . ">{$rs['Name']}</option>";
	}
	include './include/db_close.php';
}
?>