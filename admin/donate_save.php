<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->donate][1])){exit("權限不足!!");}
$no = $_REQUEST["no"];
$name = $_REQUEST["name"];
$bank = $_REQUEST["bank"];
$branch = $_REQUEST["branch"];
$account = $_REQUEST["account"];
if ($name != "" && $bank != "" && $branch != "" && $account != ""){
    include("../include/db_open.php");
    if ($no != "" && $no > 0){
            $sql = "UPDATE Donate SET Name='$name', Bank='$bank', Branch='$branch', Account='$account', dateUpdate = CURRENT_TIMESTAMP, updateBy='" . $_SESSION['admin'] . "' Where No = '$no'";
            mysql_query($sql) or die("MySQL Failed: " . mysql_error());
    }//if
    else{
            $sql = "INSERT INTO Donate (Name, Bank, Branch, Account, dateCreate, createBy, dateUpdate, updateBy) VALUES ('$name', '$bank', '$branch', '$account', CURRENT_TIMESTAMP, '" . $_SESSION['admin'] . "', CURRENT_TIMESTAMP, '" . $_SESSION['admin'] . "')";
            mysql_query($sql) or die("MySQL Failed: " . mysql_error() . $sql);
  
	}//else
    //echo $sql;
	include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Redirect("donate.php?pageno=$pageno");
?>