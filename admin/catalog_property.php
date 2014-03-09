<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
include './catalog_usefor.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->catalog][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["mno"], 0);
$pageno = Tools::parseInt2( $_REQUEST['pageno'], 1);
$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$usefor = $_REQUEST["usefor"];
$cat1 = $_REQUEST["cat1"];
$cat2 = $_REQUEST["cat2"];
$page->setHeading($_MODULE->nameOf($_MODULE->catalog));
$page->addcontentFile("../html/catalog_property.html");
$page->show();
foreach($usefors as $value => $text){
	JavaScript::addCombo("iForm.usefor", $value, $text);
}
JavaScript::setValue("iForm.usefor", $usefor);
include("../include/db_open.php");
if($cat1 != ""){
	$result = mysql_query("SELECT Name FROM Catalog WHERE No='$cat1'") or die(mysql_error());
	list($cat1_name)=mysql_fetch_row($result);
}
if($cat2 != ""){
	$result = mysql_query("SELECT Name FROM Catalog WHERE No='$cat2'") or die(mysql_error());
	list($cat2_name)=mysql_fetch_row($result);
}



$sort = 0;
$result = mysql_query("SELECT MAX(Sort) FROM Catalog WHERE useFor = '$usefor' AND Parent='$parent'");
if (($num = mysql_num_rows($result)) == 1){
   $record = mysql_fetch_row($result) or die (mysql_error());
   $sort = $record[0] + 1;
}//if
if ($no > 0){
    $result=mysql_query("SELECT Name, Sort FROM Catalog WHERE No = $no");
    if(($num=mysql_num_rows($result))==1){
        list($name, $sort) = mysql_fetch_row($result);
        JavaScript::setValue("iForm.no", $no);
        JavaScript::setValue("iForm.name", $name);
    }//if
}//if
include("../include/db_close.php");
JavaScript::setValue("iForm.sort", $sort);
JavaScript::setValue("iForm.pageno", $pageno);
JavaScript::setValue("iForm.parent", $parent);
JavaScript::setValue("iForm.cat1", $cat1);
JavaScript::setValue("iForm.cat2", $cat2);
JavaScript::setValue("iForm.cat1_name", $cat1_name);
JavaScript::setValue("iForm.cat2_name", $cat2_name);
?>
