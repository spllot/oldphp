<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';

$page = new Admin();
$page->setHeading("變更密碼");
$page->addcontentFile("../html/chgpass_admin.html");
$page->show();
?>
