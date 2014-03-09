<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->log_payment][1])){exit("權限不足!!");}

$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$userid = $HTTP_GET_VARS["userid"];
$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($_MODULE->nameOf($_MODULE->log_payment));

$sql = "SELECT *, (SELECT Name FROM Member WHERE userID=L.Member) AS Name, (SELECT Phone FROM Member WHERE userID=L.Member) AS Phone FROM Payment L WHERE 1=1";
if ($userid <> ""){
    $sql .= " AND Member = '$userid'";
}//if
$sql .= " ORDER BY No DESC";
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
if($pageno > $totalpage)
	$pageno = $totalpage;

$pagging = new Pagging($totalpage, $pageno);
$page->addJSFile("../js/log_payment_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"mForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"memberlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"mno\" value=\"\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("            請輸入會員Email：<input type=\"text\" name=\"userid\" value=\"$userid\" style='width:200px'>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"查詢\" onClick=\"Search();\">");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"刪除所有未付款項目\" onClick=\"Purge();\" style='width:120px'>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"刪除\" onClick=\"Delete();\">");
//$page->addContent("            <input type=\"button\" class=\"command\" value=\"新增\" onClick=\"New();\">");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");
$page->addContent("<TR>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=20><input type=\"checkbox\" name=\"memberno\" value=\"\" onClick=\"checkAll(mForm.memberno)\"></TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=50>編號</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:90px\">交易單號</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:108px\">交易日期</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:108px\">付款日期</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:108px\">付款方式</TH>");
$page->addContent("<TH class=\"grid_heading\">會員</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">姓名</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">電話</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:70px\">儲值金額</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:60px\">手續費</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:50px\">總計</TH>");
//$page->addContent("<TH class=\"grid_heading\" style=\"width:\">備註</TH>");
$page->addContent("</TR>");
$payby=array(
	"",
	"信用卡(Visa)",
	"Web ATM",
	"ATM轉帳"
);
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($record = mysql_fetch_array($result)){
			$page->addContent("<TR>");
            $page->addContent("<TD class=\"grid_check\"><input type=\"checkbox\" name=\"memberno\" value=\"$record[0]\"></TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['ID']}</TD>");
            $page->addContent("<TD class=\"grid_center\" style='font-size:10pt'>" . $record['dateSubmited'] . "</TD>");
            $page->addContent("<TD class=\"grid_center\" style='font-size:10pt'>" . (($record['datePaid']=="0000-00-00 00:00:00") ? "尚未" : $record['datePaid']) . "</TD>");
            $page->addContent("<TD class=\"grid_center\" style='font-size:10pt'>" . $payby[$record['payBy']] . "</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Member']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Name']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Phone']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Amount']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Fee']}</TD>");
			$page->addContent("<TD class=\"grid_center\">" . ($record['Fee'] + $record['Amount']) . "</TD>");
//			$page->addContent("<TD class=\"grid_center\" style='font-size:10pt'>{$record['Memo']}</TD>");
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
<script language="javascript">

function Purge(){
	if (confirm("確定要刪除所有未付款項目?")){
		mForm.action = code + "_purge.php";
		mForm.submit();
	}
}
</script>