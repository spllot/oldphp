<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->catalog][1])){exit("權限不足!!");}
$no = $_REQUEST["no"];
$name = $_REQUEST["name"];
$sortOrder = $_REQUEST['sort'];
$usefor = $_REQUEST["usefor"];
$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$cat1 = $_REQUEST["cat1"];
$cat2 = $_REQUEST["cat2"];
if ($name != ""){
    include("../include/db_open.php");
    if ($no != ""){
        $sql ="SELECT * FROM Catalog WHERE Name = '$name' AND useFor='$usefor' AND No <> $no AND Parent='$parent'";
        $result = mysql_query($sql) ;
        if(($num = mysql_num_rows($result))==0){
            $sql = "UPDATE Catalog SET Name = '$name', Sort = '$sortOrder', useFor = '$usefor' Where No = '$no'";
            mysql_query($sql) or die("MySQL Failed: " . mysql_error());
        }//if
        else{
            JavaScript::Alert("顯示名稱重複");
        }//else
    }//if
    else{
        $sql ="SELECT * FROM Catalog WHERE Name = '$name' AND useFor='$usefor' AND Parent='$parent'";
        $result = mysql_query($sql) or die("MySQL Failed: " . mysql_error() . $sql);
        if(($num = mysql_num_rows($result))==0){
            $sql = "INSERT INTO Catalog (Name, useFor, Sort, Parent) VALUES ('$name', '$usefor', '$sortOrder', '$parent')";
            mysql_query($sql) or die("MySQL Failed: " . mysql_error() . $sql);
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
JavaScript::Redirect("catalog.php?pageno=$pageno&usefor=$usefor&parent=$parent&cat1=$cat1&cat2=$cat2");
?>