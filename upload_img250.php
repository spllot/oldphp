<?php
include './include/session.php';
require_once './class/javascript.php';

if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
$uploadfile = $_POST['dir'] . $_POST['fname'];
if (($_FILES['userfile']['size']) > (250 * 1024) || $_FILES['userfile']['size'] <=0) {
	echo "err1";//$_FILES['userfile']['size'] ;
	exit();
}
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {

	$new_file = getcwd() . "/upload/" . basename($uploadfile);
	list($width, $height, $type, $attr) = getimagesize($new_file);

	$org_file   = getcwd() . "/upload/" . basename($uploadfile);
	$thumb_file = getcwd() . "/upload/thumb_" . basename($uploadfile);

	$w = 480; $h = 360;
	$thumbnail = new Imagick($org_file);
	$thumbnail->thumbnailImage($w, $h);
	$thumbnail->writeImage($thumb_file);

/*
	$new_file = getcwd() . "/upload/" . basename($uploadfile);
	list($width, $height, $type, $attr) = getimagesize($new_file);

	$w = 480; $h = 360;
	$size = $w . "x" . $h;
	$org_file   = getcwd() . "/upload/" . basename($uploadfile);
	$thumb_file = getcwd() . "/upload/thumb_" . basename($uploadfile);
	$exec_str="/usr/local/bin/convert '-geometry' $size $org_file $thumb_file";
	exec($exec_str);
*/
	echo  basename($uploadfile);
}else {
	echo "err2";
}
?>