<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->group][1])){exit("權限不足!!");}
$no = $_REQUEST["no"];
$name = $_REQUEST["name"];
$users = explode(",", $_REQUEST["userlist"]);
$sortOrder = $_REQUEST['sort'];
if ($name != ""){
    include("../include/db_open.php");
    if ($no != ""){
        $sql ="SELECT * FROM Catalog WHERE Name = '$name' AND useFor='GROUP' AND No <> $no";
        $result = mysql_query($sql) ;
        if(($num = mysql_num_rows($result))==0){
            $sql = "UPDATE Catalog SET Name = '$name', Sort = '$sortOrder' Where No = '$no'";
            mysql_query($sql) or die("MySQL Failed: " . mysql_error());

			for($i = 0; $i<sizeof($users); $i ++){
				$sql = "DELETE FROM groupMap Where groupNo = '$no' AND userID = '$users[$i]'";
				mysql_query($sql) or die("MySQL Failed: " . mysql_error());
				$sql = "INSERT INTO groupMap(groupNo, userID) VALUES ('$no', '$users[$i]')";
				mysql_query($sql) or die("MySQL Failed: " . mysql_error());
			}
        }//if
        else{
            JavaScript::Alert("顯示名稱重複");
        }//else
    }//if
    else{
        $sql ="SELECT * FROM Catalog WHERE Name = '$name' AND useFor='GROUP'";
        $result = mysql_query($sql) or die("MySQL Failed: " . mysql_error() . $sql);
        if(($num = mysql_num_rows($result))==0){
            $sql = "INSERT INTO Catalog (Name, useFor, Sort) VALUES ('$name', 'GROUP', '$sortOrder')";
            mysql_query($sql) or die("MySQL Failed: " . mysql_error() . $sql);

			$no = mysql_insert_id();
			for($i = 0; $i<sizeof($users); $i ++){
				$sql = "DELETE FROM groupMap Where groupNo = '$no' AND userID = '$users[$i]'";
				mysql_query($sql) or die("MySQL Failed: " . mysql_error());
				$sql = "INSERT INTO groupMap(groupNo, userID) VALUES ('$no', '$users[$i]')";
				mysql_query($sql) or die("MySQL Failed: " . mysql_error());
			}
		}
        else{
            JavaScript::Alert("顯示名稱重複");
        }//else
    }//else
    include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Redirect("group.php?pageno=$pageno");
?>