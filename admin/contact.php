<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/tools.php';
require_once '../class/system.php';
require_once '../class/pagging.php';
require_once '../class/javascript.php';

if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->buyer_msg][1])){exit("權限不足!!");}

$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$pagesize = 10;
$usefor = $_REQUEST['usefor'];

if($usefor == "")
	$usefor = '1';

$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->buyer_msg));
$page->addJSFile("../js/common_admin.js");
$page->addJSFile("../js/contact_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"iForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"itemlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"itemno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"sort\" value=\"\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("			<select name=usefor onChange=\"iForm.action='';iForm.submit();\">");
$page->addContent("				<option value='1'" . (($usefor == "1") ? " SELECTED" : "") . ">網站問題詢問</option>");
$page->addContent("				<option value='2'" . (($usefor == "2") ? " SELECTED" : "") . ">網站建議事項</option>");
$page->addContent("				<option value='3'" . (($usefor == "3") ? " SELECTED" : "") . ">商家合作諮詢</option>");
$page->addContent("			</select>");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("            <input name=\"btnDelete\" type=\"button\" class=\"command\" value=\"刪除\" onClick=\"Delete();\">");
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
$page->addContent("<TH class=\"grid_heading\" width=\"150\">聯絡選項</TH>");
$page->addContent("<TH class=\"grid_heading\">內容</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"150\">回覆</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"60\">編輯</TH>");
$page->addContent("</TR>");
include '../include/db_open.php';

$sql = "SELECT * FROM Contact WHERE Catalog = '$usefor' ORDER BY dateSubmited DESC";
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
if($pageno > $totalpage){$pageno = 1;}
$pagging = new Pagging($totalpage, $pageno);
$names = array("", "網站問題詢問", "網站建議事項", "商家合作諮詢");
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($rs = mysql_fetch_array($result)){
			$page->addContent("<TR>");
            $page->addContent("<TD class=\"grid_check\"><input type=\"checkbox\" name=\"no\" value=\"{$rs['No']}\"></TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
			$page->addContent("<TD class=\"grid_center\">{$rs['dateSubmited']}</TD>");
			$page->addContent("<TD class=\"grid_center\">" . $names[$rs['Catalog']] . "</TD>");
			$page->addContent("<TD class=\"grid_left\" style='font-size:12px; padding:2px'>");
			switch($rs['Catalog']){
				case 1:
					$page->addContent("姓名：" . $rs['Name'] . "<br>");
					$page->addContent("電子郵件：" . $rs['EMail'] . "<br>");
					$page->addContent("問題：" . $rs['Content'] . "");
					break;
				case 2:
					$page->addContent("姓名：" . $rs['Name'] . "<br>");
					$page->addContent("電子郵件：" . $rs['EMail'] . "<br>");
					$page->addContent("建議：" . $rs['Content'] . "");
					break;
				case 3:
					$page->addContent("商家(商品)名稱：" . $rs['Name'] . "<br>");
					$page->addContent("網站介紹：" . $rs['Intro'] . "<br>");
					$page->addContent("電子郵件：" . $rs['EMail'] . "<br>");
					$page->addContent("聯絡人：" . $rs['Contact'] . "<br>");
					$page->addContent("聯絡電話：" . $rs['Phone'] . "");
					break;
			}
			$page->addContent("</TD>");
			$page->addContent("<TD class=\"grid_center\">" . (($rs['dateReplied'] == "0000-00-00 00:00:00") ? "尚未" : $rs['dateReplied']) . "</TD>");
			$page->addContent("<TD class=\"grid_center\"><A HREF=\"javascript:Edit('{$rs['No']}')\"><img src=\"../images/edit.gif\" border=\"0\"></A></TD>");
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
include '../include/db_close.php';
$page->show();
JavaScript::setValue("iForm.usefor", $usefor);
?>