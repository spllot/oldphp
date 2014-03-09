<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
require_once '../class/admin.php';
$page = new Admin();

$page->addJSFile("../js/common_admin.js");
$page->addJSFile("../js/forum_admin.js");

$page->setHeading("系統訊息");
$page->addContent("請由左邊選單選擇所要操作的子功能");
$page->show();
?>