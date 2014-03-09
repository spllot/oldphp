<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->user][1])){exit("權限不足!!");}
$no = $_REQUEST["no"];
$userid = strtolower($_REQUEST["userid"]);
$userpass = $_REQUEST["userpass"];
$username = $_REQUEST["username"];
$group = $_REQUEST["group"];
$email = $_REQUEST["email"];
if ($userid != "" && $username != ""){
    include("../include/db_open.php");
    if ($no != ""){
        $sql ="SELECT * FROM Admin WHERE (userID = '$userid' OR userName = '$username') AND No <> $no";
        $result = mysql_query($sql) ;
        if(($num = mysql_num_rows($result))==0){
            $sql = "UPDATE Admin SET userID = '$userid', userName = '$username', EMail = '$email'";
            if ($userpass != ""){
                $sql .= ", userPass = PASSWORD('$userpass')";
            }//if
            $sql .=" WHERE No = '$no'";
            mysql_query($sql) or die("MySQL Failed: " . mysql_error());
			mysql_query("DELETE FROM groupMap WHERE userID = '$userid'") or die("MySQL Failed: " . mysql_error());
			$groups = explode(",", $group);
			for($i = 0; $i < sizeof($groups); $i ++){
				if($group[$i] != ""){
					mysql_query("INSERT INTO groupMap (groupNo, userID) VALUES ('$groups[$i]', '$userid')") or die("MySQL Failed: " . mysql_error());
				}
			}
           //JavaScript::Alert("更新完成");
        }//if
        else{
            JavaScript::Alert("帳號或名稱重複");
        }//else
    }//if
    else{
        if ($userpass != ""){
            $sql = "SELECT * FROM Admin WHERE userID = '$userid' OR userName = '$username'";
            $result = mysql_query($sql) or die("MySQL Failed: " . mysql_error());
            if(($num = mysql_num_rows($result))==0){
                $sql = "INSERT INTO Admin (userID, userName, userPass, EMail) VALUES ('$userid', '$username', PASSWORD('$userpass'), '$email')";
                mysql_query($sql) or die("MySQL Failed: " . mysql_error());
//                JavaScript::Alert("資料已新增");
				$groups = explode(",", $group);
				for($i = 0; $i < sizeof($groups); $i ++){
					if($group[$i] != ""){
						mysql_query("INSERT INTO groupMap (groupNo, userID) VALUES ('$groups[$i]', '$userid')") or die("MySQL Failed: " . mysql_error());
					}
				}
            }//if
			else{
                JavaScript::Alert("帳號或名稱重複");
            }//else
        }//if
        else{
            JavaScript::Alert("輸入欄位不足!!");
        }//else
    }//else
    include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Redirect("user.php?pageno=$pageno");
?>