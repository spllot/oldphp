<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->permission][1])){exit("權限不足!!");}

$group = Tools::parseInt2($HTTP_POST_VARS['group'], 0);
$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->permission));
$page->addJSFile("../js/permission_admin.js");
include '../include/db_open.php';
if($group == 0){
	$result = mysql_query("SELECT No, Name FROM Catalog WHERE useFor = 'GROUP' ORDER BY Sort") or die (mysql_error());
	list($no, $name) = mysql_fetch_row($result);
	$group = $no;
}

$result = mysql_query("SELECT Module FROM Permission WHERE groupNo = '$group'") or die (mysql_error());
$itemlist = ",";

while(list($module) = mysql_fetch_row($result)){
	$itemlist .= $module . ",";
}

$page->addContent("<table width=\"98%\">");
$page->addContent("	<form name=\"iForm\" method=\"post\">");
$page->addContent("	<input type=\"hidden\" name=\"itemlist\">");
$page->addContent("	<tr>");
$page->addContent("		<td>");
$page->addContent("			<table cellpadding=\"0\" cellspacing=\"5\" border=\"0\" width=\"100%\">");
$page->addContent("				<tr>");
$page->addContent("					<td>群組：");
$page->addContent("						<select name=\"group\" onChange=\"iForm.action='permission.php';iForm.target=''; iForm.submit()\">");
$page->addContent("						</select>");
$page->addCOntent("					</td>");
$page->addContent("					<td align=\"right\">");
$page->addContent("					</td>");
$page->addContent("				</tr>");
$page->addContent("			</table>");
$page->addContent("		</td>");
$page->addContent("	</tr>");
$page->addContent("	<tr>");
$page->addContent("		<td>");

$g .= "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"5\">";
$i = 0;
foreach($_MODULE->apps as $app_id => $app_info){
	$g .= ((($i % 4) == 0) ? "<tr>" : "");
	$g .= "<td width=\"25%\" valign=\"top\" style=\"border: solid 1px bordercolorlight=\"#99CCFF\"\">";
	$g .= "<table height=\"100%\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">\n";
	$g .= "	<tr><td width=\"20\" class=\"grid_header\" height=25><input name=\"system_" . $i . "\" type=\"checkbox\" value=\"\" onClick=\"checkModule('" . $i . "');\"></td><td class=\"grid_header\"><a href=\"javascript:void(0)\" onCLick=\"iForm.system_" . $i . ".click();\" style=\"color: blue; text-decoration: none\">" . $app_info[0] . "</a></td></tr>";
	$g .= "	<tr><td>&nbsp;</td><td><table>";
	for($j=0; $j<sizeof($app_info[1]); $j++)
		$g .= "<tr><td><input name=\"module_" . $i . "_" . $j . "\" type=\"checkbox\" value=\"". $_MODULE->modules[$app_info[1][$j]][1] . "\"" . ((strrpos($itemlist, "," . $_MODULE->modules[$app_info[1][$j]][1] . ",") > -1) ? " CHECKED" : "") . "></td><td><a href=\"javascript:void(0)\" onCLick=\"iForm.module_" . $i . "_" . $j . ".click();\" style=\"color: blue; text-decoration: none\">" . $_MODULE->modules[$app_info[1][$j]][0] . "</a></td></tr>";
	$g .= "	</table></td></tr>";
	$g .= "</table>";
	$g .= "</td>";
	$g .= ((($i % 4) == 3) ? "</tr>" : "");
	$i++;
}
$g .= ((($i % 4) != 0) ? "</tr>" : "");
$g .= "</table>";

$page->addContent($g);
$page->addContent("</td>");
$page->addContent("	</tr>");
$page->addContent("	<tr>");
$page->addContent("		<td align=\"center\"><input type=\"button\" value=\"儲存\" onClick=\"Save();\"></td>");
$page->addContent("	</tr>");
$page->addContent("	</form>");
$page->addContent("</table>");
$page->addContent("<iframe name=\"iAction\" width=\"100%\" height=\"100\" style='display:none'></iframe>");

$page->show();

$result = mysql_query("SELECT No, Name FROM Catalog WHERE useFor = 'GROUP' ORDER BY Sort") or die (mysql_error());
while(list($no, $name) = mysql_fetch_row($result)){
	JavaScript::addCombo("iForm.group", $no, $name);
}

include '../include/db_close.php';
JavaScript::setValue("iForm.group", $group);
?>
