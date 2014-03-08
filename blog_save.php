<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

$subject = $_REQUEST['subject'];
$tostock = $_REQUEST['tostock'];
$url = trim($_REQUEST['url']);
$recommend = trim($_REQUEST['recommend']);
$reply = trim($_REQUEST['reply']);
JavaScript::setCharset("UTF-8");
if($subject != "" && $url != ""){
	include './include/db_open.php';

	$left = 0;
	$max = 0;
	$use = 0;
	$curr = date('Y-m');
	$result = mysql_query("SELECT * FROM Config WHERE ID='{$curr}S'");
	if($rs = mysql_fetch_array($result)){
		$price = $rs['YN'];
	}


	$result = mysql_query("SELECT * FROM Blog WHERE dateSubmited LIKE '$curr%' AND userID = '" . $_SESSION['member']['userID'] . "'");
	if(mysql_num_rows($result) == 0){
		$result = mysql_query("SELECT * FROM Config WHERE ID='$curr'");
		if($rs = mysql_fetch_array($result)){
			$max = $rs['YN'];
		}

		$result = mysql_query("SELECT IFNULL(COUNT(*), 0) FROM Blog WHERE dateSubmited LIKE '$curr%'");
		if($rs = mysql_fetch_row($result)){
			$use = $rs[0];
		}

		$left = $max - $use;

		if($left > 0){
			$result = mysql_query("SELECT * FROM Blog WHERE Url = '$url'");
			if(mysql_num_rows($result) == 0){
				$sql = "insert into Blog SET sPrice='$price', toStock='$tostock', userID = '" . $_SESSION['member']['userID'] . "', Subject = '$subject', Recommend='$recommend', Reply='$reply', Url='$url', dateSubmited=CURRENT_TIMESTAMP";
				$result = mysql_query($sql) or die (mysql_error());
			}
			else{
				JavaScript::Alert("對不起, 你的文章已重複, 故本次應徵失效, 若有問題請向客服中心聯絡!");
			}
			include './include/db_close.php';
		}
		else{
			JavaScript::Alert("本月部落格文章徵求數已額滿!");
		}
	}
	else{
		JavaScript::Alert("每位會員應徵部落格行銷文章徵求, 每月僅能一次!");
	}
	JavaScript::Execute("window.parent.location.reload();");
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
