<?php
ini_set("session.save_path", $_SERVER['DOCUMENT_ROOT'] . "/tmp/"); 
session_start();
require_once("../class/javascript.php");
$_SESSION['admin'] = "";
$_SESSION['adminname'] = "";
$_SESSION['permit'] = "";
JavaScript::setURL("index.html", "top")
?>