<?php
$type = $_REQUEST['type'];
$x = $_REQUEST['x'];
switch($type){
	case 'transfer':
		$usefor = "TYPE_TPT";
		break;
	case 'hr':
		$usefor = "TYPE_JOB";
		break;
	case 'event':
		$usefor = "TYPE_ACT";
		break;
	default:
		$usefor = "TYPE_PRO";
		break;
}
if($usefor != ""){
	include './include/db_open.php';
	$result = mysql_query("SELECT * FROM Catalog WHERE Parent='0' AND useFor='$usefor' ORDER BY Sort") or die(mysql_error());
	while($rs=mysql_fetch_array($result)){
		echo "<option value='{$rs['No']}'" . (($x==$rs['No']) ? " SELECTED" : "") . ">{$rs['Name']}</option>";
	}
	include './include/db_close.php';
}
?>