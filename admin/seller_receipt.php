<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->seller_receipt][1])){exit("權限不足!!");}
$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$keyword = $_REQUEST["keyword"];
$area = $_REQUEST["area"];
$type = $_REQUEST["type"];
$catalog = $_REQUEST["catalog"];



$tab = (($_REQUEST['tab'] != "") ? $_REQUEST['tab'] : 0);
$menu = array(
	'seller_receipt.php?tab=0' =>'二聯式',
	'seller_receipt.php?tab=1' =>'三聯式',
);

$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($menu, $tab);

$sql = "SELECT * FROM Receipt WHERE 1=1";
switch($tab){
	case 0:
		$sql .= " AND Type = 2";
		break;
	case 1:
		$sql .= " AND Type = 3";
		break;
}

$sql .= " ORDER BY dateCreate DESC";
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
$pagging = new Pagging($totalpage, $pageno);

$page->addJSFile("../js/seller_receipt_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"mForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"memberlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"itemno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"mno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"sort\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"tab\" value=\"$tab\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("            票號起始碼：<input type=\"text\" name=\"start\" style='width:100px' maxlength='10'>");
$page->addContent("            張數：<input type=\"text\" name=\"total\" style='width:50px'>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"新增\" onClick=\"Save();\">");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");
$page->addContent("<TR>");
$page->addContent("<TH class=\"grid_heading\" width=200>票號起始碼</TH>");
$page->addContent("<TH class=\"grid_heading\">張數</TH>");
$page->addContent("<TH class=\"grid_heading\">已用張數</TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=100>刪除</TH>");
$page->addContent("</TR>");
$status = array("草稿", "審核中", "已審核", "退回", "已下架", "待確認", "已審核");
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($record = mysql_fetch_array($result)){
			$page->addContent("<TR>");
			$page->addContent("<TD class=\"grid_center\">{$record['Start']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Total']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Counts']}</TD>");
			$page->addContent("<TD class=\"grid_center\">" . (($record['Total'] == $record['Counts']) ? "<input type='button' value='刪除' onClick=\"Delete('" . $record['No'] . "');\">" : "&nbsp;") . "</TD>");
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

function Save(){
	if(!mForm.start.value){
		alert("請輸入票號起始碼!");
		mForm.start.focus();
	}
	else if(mForm.start.value.length != 10){
		alert("票踸起始碼請輸入10碼!");
		mForm.start.focus();
	}
	else if(!mForm.total.value){
		alert("請輸入數量!");
		mForm.total.focus();
	}
	else{
		mForm.action="seller_receipt_save.php";
		mForm.submit();
	}

}

function Delete(xNo){
	if(confirm("確定要刪除?")){
		mForm.memberlist.value = xNo;
		mForm.action="seller_receipt_delete.php";
		mForm.submit();
	}
}

</script>