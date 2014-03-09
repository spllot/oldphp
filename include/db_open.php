<?php
require_once 'config.php';

$db_link = mysql_pconnect($DB_HOST, $DB_USER, $DB_PASS) or die ("MySQL Failed: " . mysql_error());
mysql_select_db($DB_NAME, $db_link) or die ("MySQL Failed: " . mysql_error());
mysql_query("SET NAMES UTF8");
//mysql_query("SET time_zone = '+8:00'");
date_default_timezone_set('Asia/Taipei');
mysql_query("UPDATE Member SET lastMove=CURRENT_TIMESTAMP WHERE userID='" . $_SESSION['member']['userID'] . "'") or die(mysql_error());




?>