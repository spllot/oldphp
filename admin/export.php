<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->export][1])){exit("權限不足!!");}

$group = Tools::parseInt2($HTTP_POST_VARS['group'], 0);
$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->export));
$page->addcontentFile("../html/export.html");
$page->show();
?>
