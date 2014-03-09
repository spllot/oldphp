<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/tools.php';
require_once '../class/system.php';
require_once '../class/pagging.php';
require_once '../class/javascript.php';

if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->page][1])){exit("權限不足!!");}

$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$pagesize = 10;
$usefor = $_REQUEST['usefor'];

if($usefor == "")
	$usefor = 'PGE';

$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->page));
$page->addJSFile("../js/common_admin.js");
$page->addJSFile("../js/page_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"iForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"itemlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"itemno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"sort\" value=\"\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr style='display:none'>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("			<select name=usefor onChange=\"iForm.action='';iForm.submit();\">");
$page->addContent("				<option value='PGE'>網站文案</option>");
$page->addContent("				<option value='NEW'>新手上路</option>");
$page->addContent("				<option value='HOW'>如何付款</option>");
$page->addContent("				<option value='DEV'>配送方式</option>");
$page->addContent("				<option value='CMS'>售後服務</option>");
$page->addContent("				<option value='SZJ'>關於曬自己</option>");
$page->addContent("			</select>");
include '../include/db_open.php';
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
//$page->addContent("            <input name=\"btnDelete\" type=\"button\" class=\"command\" value=\"刪除\" onClick=\"Delete();\">");
//$page->addContent("            <input type=\"button\" class=\"command\" value=\"新增\" onClick=\"New();\">");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");
$page->addContent("<TR>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=20><input type=\"checkbox\" name=\"no\" value=\"\" onClick=\"checkAll(iForm.no)\"></TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=50>編號</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"150\">代碼</TH>");
$page->addContent("<TH class=\"grid_heading\">標題</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"60\">編輯</TH>");
$page->addContent("</TR>");

$sql = "SELECT No, useFor, Subject FROM Page WHERE useFor like '$usefor%' ORDER BY Subject";
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
if($pageno > $totalpage){$pageno = 1;}
$pagging = new Pagging($totalpage, $pageno);

if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if(list($no, $usefor1, $subject) = mysql_fetch_row($result)){
			$page->addContent("<TR>");
            $page->addContent("<TD class=\"grid_check\"><input type=\"checkbox\" name=\"no\" value=\"$no\"></TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
			$page->addContent("<TD class=\"grid_left\">&nbsp;".$usefor1."</TD>");
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


$page->addContent("<form name=\"jForm\" method=\"post\"><TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0>");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"itemlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"itemno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"sort\" value=\"\">");
$page->addContent("<tr><Td style='text-align:left'>");
$page->addContent("<input type='button' value='新增專案' onClick='new_p();'>");
$page->addContent("<input type='button' value='刪除專案' onClick='del_p();'>");
$page->addContent("</td></tr>");
$page->addContent("<tr><Td>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");

$result = mysql_query("SELECT *, (SELECT COUNT(*) FROM Member WHERE Referral = Project.Code) AS N1, (SELECT COUNT(*) FROM Member WHERE Referral = Project.Code AND No IN (SELECT Member FROM Product WHERE dateApprove <> '0000-00-00 00:00:00')) AS N2 FROM Project ORDER BY Code") or die(mysql_error());
if(mysql_num_rows($result) > 0){
	while($rs=mysql_fetch_array($result)){
		$page->addContent("<tr>");
		$page->addContent("<td style='width:50px; text-align:center'><input type='checkbox' name='no' value='{$rs['No']}'></td>");
		$page->addContent("<td style='padding-right:50px'>專案代碼：{$rs['Code']}</td>");
		$page->addContent("<td style='padding-right:50px'>專案說明編輯：<A HREF=\"javascript:edt_p('{$rs['No']}')\"><img src=\"../images/edit.gif\" border=\"0\"></A></td>");
		$page->addContent("<td>(專案會員數：<a href=\"javascript:window.showModalDialog('project_referral.php?no={$rs['No']}');\">{$rs['N1']}</a> 人　已建置商品之會員與編號：<a href=\"javascript:window.showModalDialog('project_referral_create.php?no={$rs['No']}');\">{$rs['N2']}</a> 人)</td>");
		$page->addContent("</tr>");
	}
}
else{
	$page->addContent("<tr><td style='color:red; text-align:center'>查無資料</td></tr>");
}
$page->addContent("</TABLE>");
$page->addContent("</td></tr>");
$page->addContent("</TABLE></form><Br><br>");



include '../include/db_close.php';
$page->show();
JavaScript::setValue("iForm.usefor", $usefor);
?>
<script language='javascript'>
function new_p(){
	edt_p('');
}

function edt_p(xNo){
	document.jForm.itemno.value = xNo;
	document.jForm.action = "page_project.php";
	document.jForm.submit();
}

function del_p(){
	document.jForm.itemlist.value = '';
	if(document.jForm.no){
		if(document.jForm.no.length){
			for(var i=0; i<document.jForm.no.legnth; i++){
				if(document.jForm.no[i].checked){
					document.jForm.itemlist.value += document.jForm.no[i].value + ",";
				}
			}
			if(document.jForm.itemlist.value){
				document.jForm.itemlist.value = document.jForm.itemlist.value.substring(0, document.jForm.itemlist.value.length - 1);
			}
		}
		else{
			if(document.jForm.no.checked){
				document.jForm.itemlist.value = document.jForm.no.value;
			}
		}
	}

	if(document.jForm.itemlist.value){
		if(confirm("確定要刪除所選項目?")){
			document.jForm.action = "page_project_delete.php";
			document.jForm.submit();
		}
	}
	else{
		alert("尚未選取!");
	}
}

</script>