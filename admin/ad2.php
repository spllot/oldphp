<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
include './ad2_usefor.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->ad2][1])){exit("權限不足!!");}
$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$usefor = $_REQUEST["usefor"];
$catalog = $_REQUEST["catalog"];

$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($_MODULE->nameOf($_MODULE->ad2));


$page->addJSFile("../js/ad2_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"iForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"memberlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"itemno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"mno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"sort\" value=\"\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("				<select name=\"usefor\" onChange=\"iForm.action='ad2.php';iForm.submit();\">");
foreach($usefors as $value => $text){
	if($usefor == ""){$usefor = $value;}
	$page->addContent("			<option value=\"$value\"" . (($value == $usefor) ? " SELECTED" : "") . ">$text</option>");
}
$page->addContent("				</select>");
$page->addContent("				<select name=\"catalog\" onChange=\"iForm.action='ad2.php';iForm.submit();\">");
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_PRO' AND Parent=0");
while($rs=mysql_fetch_array($result)){
	if($catalog == ""){$catalog = $rs['No'];}
	$page->addContent("			<option value=\"" . $rs['No'] . "\"" . (($rs['No'] == $catalog) ? " SELECTED" : "") . ">" . $rs['Name'] . "</option>");
}
$page->addContent("				</select>");
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
$page->addContent("<TH class=\"grid_heading\" width=\"100\">類別</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"100\">分類</TH>");
$page->addContent("<TH class=\"grid_heading\">廣告名稱</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"50\">順序</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"100\">排序</TH>");
$page->addContent("</TR>");
$sql = "SELECT AD2.No, Caption, Sort, (SELECT Name FROM Catalog WHERE Catalog.No = AD2.Catalog) AS Catalog, useFor, Member, AD2.Days, dateExpire, Member.Name, Member.userID FROM AD2 LEFT OUTER JOIN Member ON AD2.Member = Member.No WHERE useFor = '$usefor' AND Catalog='$catalog'";
$sql .= " AND (AD2.Member=0 OR (AD2.Member > 0 AND dateExpire > CURRENT_TIMESTAMP))";
$sql .= " ORDER BY dateSubmit DESC, Sort";
//echo $sql;
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
$pagging = new Pagging($totalpage, $pageno);
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($record = mysql_fetch_row($result)){
			$page->addContent("<TR>");
            $page->addContent("<TD class=\"grid_check\"><input type=\"checkbox\" name=\"memberno\" value=\"$record[0]\"></TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
			$page->addContent("<TD class=\"grid_center\">" . $usefors[$record[4]] . "</TD>");
			$page->addContent("<TD class=\"grid_center\">" . $record[3] . "</TD>");
			$page->addContent("<TD class=\"grid_left\">&nbsp;<A HREF=\"javascript:Edit('$record[0]')\">$record[1]</A>" . (($record[5] > 0) ? " (" . $record[8] . ", " . $record[9] . ")":"") . "</TD>");
			/*			*/

			$page->addContent("<TD class=\"grid_center\">");
			if($record[5] > 0){
				$page->addContent($record[6] . "天");
			}
			else{
				$page->addContent("<input type=\"text\" name=\"sort_" . $record[0] . "\" style=\"width:50px\" value=\"" . $record[2] . "\">");
			}
			$page->addContent("</TD>");

			$page->addContent("<TD class=\"grid_center\">");
			if($record[5] > 0){
				$page->addContent(substr($record[7], 0, 10));
			}
			else{
				$page->addContent("<A HREF=\"javascript:gSort('" . $record[0] . "', -1)\"><img src=\"../images/moveup.gif\" border=\"0\" alt=\"上移\"></A>");
				$page->addContent("<A HREF=\"javascript:gSort('" . $record[0] . "', 1)\"><img src=\"../images/movedown.gif\" border=\"0\" alt=\"下移\"></A>");
			}
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