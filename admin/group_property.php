<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->group][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["mno"], 0);
$pageno = Tools::parseInt2( $_REQUEST['pageno'], 1);
$page->setHeading($_MODULE->nameOf($_MODULE->group));
$page->addcontentFile("../html/group_property.html");
$page->show();
$sort = 0;
include("../include/db_open.php");
$result = mysql_query("SELECT MAX(Sort) FROM Catalog WHERE useFor = 'GROUP'");
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
        $result=mysql_query("SELECT userID, userName FROM Admin WHERE userID in (SELECT userID FROM groupMap WHERE groupNo = '$no') ORDER BY userID");
		while(list($userid, $username) = mysql_fetch_row($result)){
			JavaScript::addCombo("iForm.users", $userid, $userid . "(" . $username . ")");
		}
    }//if
}//if
JavaScript::setValue("iForm.sort", $sort);
JavaScript::setValue("iForm.pageno", $pageno);
include("../include/db_close.php");
?>
