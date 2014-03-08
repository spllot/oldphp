<?php
include './include/session.php';
include './include/db_open.php';
$no = $_REQUEST['id'];
$sql = "SELECT * FROM Product WHERE Status = 2 AND No = '$no'";
$result = mysql_query($sql) or die(mysql_error());
if($rs = mysql_fetch_array($result)){
	if($rs['Mode'] == 2 && $rs['Deliver'] == 1){
		$url = "product5_detail.php?no=" . $no;
	}
	if($rs['Mode'] == 2 && $rs['Deliver'] == 0){
		$url = "product4_detail.php?no=" . $no;
	}
}
?>
<?if($url != "") {?>
<script language="javascript">window.location.href="<?=$url?>";</script>
<?}?>