<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
include './catalog_usefor.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->catalog][1])){exit("權限不足!!");}
$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$parent = Tools::parseInt2($_REQUEST["parent"], 0);

$usefor = $_REQUEST["usefor"];
$cat1 = $_REQUEST["cat1"];
$cat2 = $_REQUEST["cat2"];

$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($_MODULE->nameOf($_MODULE->catalog));


$page->addJSFile("../js/catalog_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"iForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"memberlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"itemno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"mno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"sort\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"parent\" value=\"$parent\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");

$page->addContent("				<select name=\"usefor\" onChange=\"iForm.action='catalog.php'; iForm.parent.value=''; iForm.cat1.value=''; iForm.cat2.value=''; iForm.submit();\">");
foreach($usefors as $value => $text){
	if($usefor == ""){$usefor = $value;}
	$page->addContent("			<option value=\"$value\"" . (($value == $usefor) ? " SELECTED" : "") . ">$text</option>");
}
$page->addContent("				</select>");

if($usefor == "TYPE_PRO" || $usefor == "TYPE_TPT" || $usefor == "TYPE_JOB" || $usefor == "TYPE_ACT"){
	
	$page->addContent("				=><select name='cat1' onChange=\"iForm.cat2.value=''; Subcat();\"><option value=''>{$usefors[$usefor]}</option>");
	$result = mysql_query("SELECT * FROM Catalog WHERE useFor='$usefor' AND Parent=0 ORDER BY Sort") or die(mysql_error());
	while($rs=mysql_fetch_array($result)){
		$page->addContent("				<option value='{$rs['No']}'" . (($cat1 == $rs['No']) ? " SELECTED" : ""). ">{$rs['Name']}</option>");
	}
	$page->addContent("				</select>");
	$page->addContent("				<select name='cat2' onChange=\"Subcat();\"><option value=''>{$usefors[$usefor]}</option>");
	if($cat1 != ""){
		$result = mysql_query("SELECT * FROM Catalog WHERE useFor='$usefor' AND Parent='$cat1' ORDER BY Sort") or die(mysql_error());
		while($rs=mysql_fetch_array($result)){
			$page->addContent("				<option value='{$rs['No']}'" . (($cat2 == $rs['No']) ? " SELECTED" : ""). ">{$rs['Name']}</option>");
		}
	}
	$page->addContent("				");
	$page->addContent("				</select>");
}
else{
	$page->addContent("				<input type='hidden' name='cat1'><input type='hidden' name='cat2'>");
}


//$page->addContent("            <input type=\"button\" class=\"command\" value=\"查詢\" onClick=\"Search();\">");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"調整順序\" onClick=\"Resort();\">");
$page->addContent("            <input name=\"btnDelete\" type=\"button\" class=\"command\" value=\"刪除\" onClick=\"Delete();\"" . (($_USER->isAdmin($_SESSION['admin']))? "" :  "") . ">");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"新增\" onClick=\"New();\">");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");
$page->addContent("<TR>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=20><input type=\"checkbox\" name=\"memberno\" value=\"\" onClick=\"checkAll(iForm.memberno)\"></TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=50>編號</TH>");
$page->addContent("<TH class=\"grid_heading\">分類名稱</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"50\">順序</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"100\">排序</TH>");
$page->addContent("</TR>");
$sql = "SELECT No, Name, Sort FROM Catalog WHERE useFor = '$usefor' AND Parent='$parent'";
$sql .= " ORDER BY Sort";
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
if($pageno > $totalpage)
	$pageno = 1;


$pagging = new Pagging($totalpage, $pageno);
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($record = mysql_fetch_row($result)){
			$page->addContent("<TR>");
            $page->addContent("<TD class=\"grid_check\"><input type=\"checkbox\" name=\"memberno\" value=\"$record[0]\"></TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
			$page->addContent("<TD class=\"grid_left\"><A HREF=\"javascript:Edit('$record[0]')\">$record[1]</A></TD>");
			$page->addContent("<TD class=\"grid_left\"><input type=\"text\" name=\"sort_" . $record[0] . "\" style=\"width:50px\" value=\"" . $record[2] . "\"></TD>");
			$page->addContent("<TD class=\"grid_center\">");
			$page->addContent("<A HREF=\"javascript:gSort('" . $record[0] . "', -1)\"><img src=\"../images/moveup.gif\" border=\"0\" alt=\"上移\"></A>");
			$page->addContent("<A HREF=\"javascript:gSort('" . $record[0] . "', 1)\"><img src=\"../images/movedown.gif\" border=\"0\" alt=\"下移\"></A>");
			$page->addContent("</TD>");
			$page->addContent("</TR>");
		}//if
		else
			break;
	}//for
	$page->addContent("</TABLE></TD></TR></form>");
    $page->addContent("<TR><TD>" . $pagging->toString() . "</TD></TR>");
}//if
else{
	$page->addContent("</TABLE></TD></TR></form>");
	$page->addContent("<TR><TD class=\"grid_nodata\">目前無資料</TD></TR>");
}//else
$page->addContent("</TABLE>");
include("../include/db_close.php");
$page->show();
?>
<script language='javascript'>
	function Subcat(){
		iForm.parent.value = '';
		if(iForm.cat1.value){
			iForm.parent.value = iForm.cat1.value;
		}
		if(iForm.cat2.value){
			iForm.parent.value = iForm.cat2.value;
		}
		iForm.submit();
	}
</script>