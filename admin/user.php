<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->user][1])){exit("權限不足!!");}

$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$userid = $HTTP_GET_VARS["userid"];
$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($_MODULE->nameOf($_MODULE->user));

$sql = "SELECT No, userID, userName, dateLastLogin, ipLastLogin FROM Admin WHERE userID != '" . $_USER->adminID . "'";
if ($userid <> ""){
    $sql .= " AND userID LIKE '$userid%'";
}//if
$sql .= " ORDER BY userID";
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
$pagging = new Pagging($totalpage, $pageno);

$page->addJSFile("../js/user_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"mForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"memberlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"mno\" value=\"\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
//$page->addContent("            <input type=\"text\" name=\"userid\" value=\"$userid\">");
//$page->addContent("            <input type=\"button\" class=\"command\" value=\"查詢\" onClick=\"Search();\">");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"刪除\" onClick=\"Delete();\">");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"新增\" onClick=\"New();\">");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");
$page->addContent("<TR>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=20><input type=\"checkbox\" name=\"memberno\" value=\"\" onClick=\"checkAll(mForm.memberno)\"></TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=50>編號</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:100px\">帳號</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:100px\">姓名</TH>");
$page->addContent("<TH class=\"grid_heading\">群組</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:120px\">登入日期</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:120px\">登入IP</TH>");
//$page->addContent("<TH class=\"grid_heading\" width=\"50\">權限</TH>");
$page->addContent("</TR>");
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($record = mysql_fetch_row($result)){
			$page->addContent("<TR>");
            $page->addContent("<TD class=\"grid_check\"><input type=\"checkbox\" name=\"memberno\" value=\"$record[0]\"></TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
			$page->addContent("<TD class=\"grid_left\"><A HREF=\"javascript:Edit('$record[0]')\">$record[1]</A></TD>");
            $page->addContent("<TD class=\"grid_left\">$record[2]</TD>");
			$gResult = mysql_query("SELECT Name FROM Catalog INNER JOIN groupMap ON Catalog.No = groupMap.groupNo WHERE useFor = 'GROUP' AND groupMap.userID = '$record[1]' ORDER BY Catalog.Sort") or die (mysql_error());
			$group = "";
			while(list($gname) = mysql_fetch_row($gResult)){
				$group .= $gname . ", ";
			}
            $page->addContent("<TD class=\"grid_left\">" . (($group=="")?"&nbsp;":$group) . "</TD>");
            $page->addContent("<TD class=\"grid_center\">" . (($record[3]=="0000-00-00 00:00:00")?"<font style='color:red; font-size:11pt'>尚未登入</font>":$record[3]) . "</TD>");
            $page->addContent("<TD class=\"grid_center\">" . (($record[4]=="")?"<font style='color:red; font-size:11pt'>尚未登入</font>":$record[4]) . "</TD>");
//            $page->addContent("<TD class=\"grid_center\"><a href=\"javascript:setPermission('$record[0]', '$record[3]')\"><img src=\"../images/lock.gif\" border=\"0\"></a></TD>");
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