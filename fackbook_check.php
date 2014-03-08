<?php
require_once getcwd() . '/class/facebook.php';
$feedback['time']=time();
$feedback['counts']=$_REQUEST['counts'];
$feedback['login'] = 'N';
if($me){
	$feedback['login'] = 'Y';
}
echo json_encode($feedback);
?>