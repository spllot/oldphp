<?php
$uploadfile = $_POST['dir'] . $_POST['fname'];
if (($_FILES['userfile']['size']) > (3 * 1024 * 1024) || $_FILES['userfile']['size'] <=0) {
	echo $_FILES['userfile']['size'] ;
	exit();
}

if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
	if($_POST['width'] && $_POST['height']){
		/*
		$image = new Imagick($uploadfile);
		$image->cropThumbnailImage($_POST['width'], $_POST['height']);
		$image->setImagePage(0, 0, 0, 0);
		$thumbnail->writeImage($uploadfile);
		*/
	}
	echo $_POST['fname'];
}

else {
  echo "error";
}
?>