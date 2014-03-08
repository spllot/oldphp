<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
JavaScript::setCharset("UTF-8");
$userid = $_REQUEST['email'];
$userpass = $_REQUEST['pass1'];
$captcha = $_REQUEST['captcha'];
$url = (($_REQUEST['url'] != "") ? $_REQUEST['url'] : "product4.php");


if($userid != "" && $userpass != "" && $captcha != ""){
	if($captcha != $_SESSION['security_code']){JavaScript::Alert("驗證碼錯誤!!");JavaScript::Execute("window.location.href = 'member_login.php';");exit;}
	include './include/db_open.php';
	$result = mysql_query("SELECT *, (SELECT COUNT(*) FROM Admin WHERE EMail=Member.userID) AS isAdmin FROM Member WHERE userID = '$userid' AND userPass = binary'$userpass'") or die (mysql_error());
	$ip =  Tools::getRemoteIP();
	$login_status = 0;
	if(mysql_num_rows($result) == 1){
		$rs = mysql_fetch_array($result);
		
		if(substr($rs['dateLogin'], 0, 10) != date('Y-m-d')){
			mysql_query("UPDATE Member SET Days = Days + 1 WHERE userID = '$userid'");
		    $_SESSION['member']['dateLogin'] = date('Y-m-d H:i:s');
		}
		
		if($rs["Status"] == 1){
			$_SESSION['member'] = $rs;


			if($_SESSION['member']['Status2'] == 1){
				$sql = "UPDATE Member SET Latitude1=latitude_web, Longitude1=longitude_web, Address1=address_web, Area1=area_web WHERE userID = '" . $_SESSION['member']['userID'] . "'";
				mysql_query($sql) or die(mysql_error());
			}
			if($_SESSION['member']['Status2'] == 2){
				$sql = "UPDATE Member SET dateUpdate=CURRENT_TIMESTAMP, updateBy = 'LOGIN', Area1='XX', Address1='', Latitude1=latitude_app, Longitude1=longitude_app WHERE userID = '" . $_SESSION['member']['userID'] . "'";
				mysql_query($sql) or die (mysql_error());
			}

/*
			if($_SESSION['member']['Status2'] == 2){
				$sql = "UPDATE Member SET dateUpdate=CURRENT_TIMESTAMP, updateBy = 'LOGIN', Area1='XX', Address1='', Latitude1='{$_SESSION['Latitude']}', Longitude1='{$_SESSION['Longitude']}' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
				mysql_query($sql) or die (mysql_error());
			}
			if($_SESSION['member']['Status2'] == 1){
				$sql = "UPDATE Member SET dateUpdate=CURRENT_TIMESTAMP, updateBy = 'LOGIN', Area1='XX', Address1='', Latitude1='{$_SESSION['Latitude']}', Longitude1='{$_SESSION['Longitude']}' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
				mysql_query($sql) or die (mysql_error());
			}
*/

			$login_status = 1;
			mysql_query("UPDATE Member SET dateLogin = CURRENT_TIMESTAMP, ipLogin = '$ip' WHERE userID = '$userid'");
			$_SESSION['Latitude'] = $rs['Latitude0'];
			$_SESSION['Longitude'] = $rs['Longitude0'];
			$_SESSION['Address'] = $rs['Address0'];


			$result = mysql_query("SELECT IFNULL(COUNT(*), 0) FROM Member WHERE Referral = '" . $_SESSION['member']['Phone'] . "'");
			$rs = mysql_fetch_row($result);
			$referral = $rs[0];

			$result = mysql_query("SELECT IFNULL(COUNT(*), 0) FROM Member WHERE Referral = '" . $_SESSION['member']['Phone'] . "' AND No IN (SELECT Member FROM Product WHERE dateApprove <> CURRENT_TIMESTAMP)");
			$rs = mysql_fetch_row($result);
			$create = $rs[0];



			mysql_query("UPDATE Member SET Level = 2 WHERE userID = '$userid' AND (Days + $referral + $create) >= 15  AND (Days + $referral + $create) <30");
			mysql_query("UPDATE Member SET Level = 3 WHERE userID = '$userid' AND (Days + $referral + $create) >= 30  AND (Days + $referral + $create) <60");
			mysql_query("UPDATE Member SET Level = 4 WHERE userID = '$userid' AND (Days + $referral + $create) >= 60  AND (Days + $referral + $create) <120");
			mysql_query("UPDATE Member SET Level = 5 WHERE userID = '$userid' AND (Days + $referral + $create) >= 120  AND (Days + $referral + $create) <240");
			mysql_query("UPDATE Member SET Level = 6 WHERE userID = '$userid' AND (Days + $referral + $create) >= 240  AND (Days + $referral + $create) <480");
			mysql_query("UPDATE Member SET Level = 7 WHERE userID = '$userid' AND (Days + $referral + $create) >= 480  AND (Days + $referral + $create) <960");
			mysql_query("UPDATE Member SET Level = 8 WHERE userID = '$userid' AND (Days + $referral + $create) >= 960");

			switch($_SESSION['member']['Status1']){
				case 1:
					mysql_query("UPDATE Product SET Status = 2 WHERE Status=6 AND Deliver=0 AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
					break;
				case 2:
					mysql_query("UPDATE Product SET Status = 6 WHERE Status=2 AND Deliver=0 AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
					break;
				case 3:
					mysql_query("UPDATE Product SET Status = 2, Empty='{$_SESSION['member']['Empty']}' WHERE Status=6 AND Deliver=0 AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
					break;
			}


			JavaScript::Execute("parent.setUserInfo();");
			
			JavaScript::Execute("window" . ((substr(basename($url), 0, 7) == "member_")? ".parent":"") . ".location.href = '{$url}';");
		}
		else if($rs["Status"] == 2){
			$login_status = 3;
//			JavaScript::Execute("parent.Warning();");
			JavaScript::Alert("警告!\\n系統偵測您有不當申請帳號現象，本站僅許可每一會員申請一個帳號，如果您已經符合規範而被凍結帳號，請到網站首頁 [商家合作/客服中心]>>[網站問題詢問] 回覆該帳號完全合法並可接受管理者驗證資料，讓管理者替您解除帳號凍結!");
			JavaScript::Execute("window.location.href = 'member_login.php';");
		}
		else{
			$login_status = 2;
			JavaScript::Alert("帳號尚未啟用!!");
			JavaScript::Execute("window.location.href = 'member_guild.php?email=$userid';");
		}
	}
	else{
		JavaScript::Alert("登入失敗：帳號或密碼錯誤!!");
		JavaScript::Execute("window.location.href = 'member_login.php';");
	}
	$sql = "INSERT INTO logLogin(dateLogin, userID, Status, ipLogin) VALUES (CURRENT_TIMESTAMP, '$userid', '$login_status', '$ip')";
	mysql_query($sql) or die (mysql_error());
	
	include './include/db_close.php';
}
else{
	JavaScript::Alert("輸入欄位不足!!");
	JavaScript::Execute("window.location.href = 'member_login.php';");
}
?>
