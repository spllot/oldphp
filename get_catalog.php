<?php
$no = $_REQUEST['no'];

if($no != ""){
	include './include/db_open.php';
	$result = mysql_query("SELECT * FROM Catalog WHERE Parent='$no' ORDER BY Sort") or die(mysql_error());
	while($rs=mysql_fetch_array($result)){
		echo "<option value='{$rs['No']}'>{$rs['Name']}</option>";
	}
	include './include/db_close.php';
}
?>