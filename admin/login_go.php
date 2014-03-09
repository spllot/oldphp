<?php
ini_set("session.save_path", $_SERVER['DOCUMENT_ROOT'] . "/tmp/"); 
//$expireTime = 60*60*24;
//session_set_cookie_params($expireTime);
session_start();
require_once '../class/javascript.php';
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");

if (empty($HTTP_POST_VARS)){
	JavaScript::Alert("輸入欄位不足!!");
	JavaScript::Redirect("login.php");
}//if
else{
	$user = strtolower(trim($HTTP_POST_VARS["userid"]));
	$pass = trim($HTTP_POST_VARS["passwd"]);  
	if ($user==""){
    	JavaScript::Alert("輸入欄位不足!!");
		JavaScript::Redirect("login.php");
	}//if
	else{
		include("../include/db_open.php");
		$sql = "SELECT userName FROM Admin WHERE userID = '$user' AND userPass = PASSWORD('$pass')";
		$result = mysql_query($sql) or die (mysql_error());
		if(($num=mysql_num_rows($result))==1){
            $record = mysql_fetch_row($result);
            $_SESSION['admin'] = $user;
            $_SESSION['adminname'] = $record[0];
			$result = mysql_query("SELECT Module FROM Permission WHERE groupNo IN (SELECT groupNo FROM groupMap WHERE userID = '$user')") or die(mysql_error());
			$_SESSION['permit'] = ",";
			while(list($module) = mysql_fetch_row($result)){
				$_SESSION['permit'] .= $module . ",";
			}

			$ip = ((getenv(HTTP_X_FORWARDED_FOR)) ?  getenv(HTTP_X_FORWARDED_FOR) :  getenv(REMOTE_ADDR));
			mysql_query("UPDATE Admin SET dateLastLogin = CURRENT_TIMESTAMP, ipLastLogin = '$ip' WHERE userID = '$user'");
//            JavaScript::setURL("home.php", "top.main");
				JavaScript::Redirect("home.php");
		}//if
		else{
            JavaScript::Alert("登入失敗");
			JavaScript::Redirect("login.php");
        }//else
		include("../include/db_close.php");
	}//else
}//else
?>