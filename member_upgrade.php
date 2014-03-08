<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Page WHERE useFor='PGE_UPGRADE'");
$rs = mysql_fetch_array($result);


include './include/db_close.php';
?>

<title>InTimeGo—<?=$rs['Subject']?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?=$rs['Content']?>
