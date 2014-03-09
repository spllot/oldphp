<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/tools.php';
require_once '../class/system.php';
require_once '../class/pagging.php';
require_once '../class/javascript.php';

if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->news][1])){exit("權限不足!!");}

$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$pagesize = 10;

$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->news));
$page->addJSFile("../js/common_admin.js");
$page->addJSFile("../js/news_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"iForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"itemlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"itemno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"sort\" value=\"\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
include '../include/db_open.php';
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("            <input name=\"btnDelete\" type=\"button\" class=\"command\" value=\"刪除\" onClick=\"Delete();\">");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"新增\" onClick=\"New();\">");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");
$page->addContent("<TR>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=20><input type=\"checkbox\" name=\"no\" value=\"\" onClick=\"checkAll(iForm.no)\"></TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=50>編號</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"150\">日期</TH>");
$page->addContent("<TH class=\"grid_heading\">標題</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"60\">編輯</TH>");
$page->addContent("</TR>");

$sql = "SELECT No, Date, Subject FROM News WHERE 1=1 ORDER BY Date DESC";
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
if($pageno > $totalpage){$pageno = 1;}
$pagging = new Pagging($totalpage, $pageno);

if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if(list($no, $date, $subject) = mysql_fetch_row($result)){
			$page->addContent("<TR>");
            $page->addContent("<TD class=\"grid_check\"><input type=\"checkbox\" name=\"no\" value=\"$no\"></TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
			$page->addContent("<TD class=\"grid_center\">".$date."</TD>");
			$page->addContent("<TD class=\"grid_left\">&nbsp;$subject</TD>");
			$page->addContent("<TD class=\"grid_center\"><A HREF=\"javascript:Edit('$no')\"><img src=\"../images/edit.gif\" border=\"0\"></A></TD>");
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
$page->addContent("</TABLE><Br>");




include '../include/db_close.php';
$page->show();
?>
<script language='javascript'>

</script>