<?php
include './include/session.php';
include './class/tools.php';
$email = $_REQUEST['email'];
$area = $_REQUEST['area'];
include './include/db_open.php';
$feedback['err'] = 0;
if($email != "" && $area != ""){
	if(Tools::checkEMail($email)){
		mysql_query("INSERT INTO Subscribe(EMail, Product, dateAdded, Area) VALUES('$email', '', CURRENT_TIMESTAMP, '$area')");
	}
	else{
		$feedback['err'] = 1;
	}
}
else{
	$feedback['err'] = 2;
}
include './include/db_close.php';
echo json_encode($feedback);
?>
