<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
require_once '../class/admin.php';
$page = new Admin();

$page->addJSFile("../js/common_admin.js");
$page->addJSFile("../js/forum_admin.js");

$page->setHeading("功能製作中…");
$page->addContent("<img src=\"../images/construction_pic.gif\">");
$page->show();
?>