<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->log_transaction][1])){exit("權限不足!!");}

$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$userid = $HTTP_GET_VARS["userid"];
$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($_MODULE->nameOf($_MODULE->log_transaction));

$sql = "SELECT *, IFNULL((SELECT SUM(Amount) FROM logTransaction WHERE No<L.No AND Owner=L.Owner), 0) AS oBalance, (SELECT Name FROM Member WHERE userID=L.Owner) AS Name, (SELECT Phone FROM Member WHERE userID=L.Owner) AS Phone FROM logTransaction L WHERE 1=1";
if ($userid <> ""){
    $sql .= " AND Owner = '$userid'";
}//if
$sql .= " ORDER BY No DESC";
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
if($pageno > $totalpage)
	$pageno = $totalpage;
$pagging = new Pagging($totalpage, $pageno);
$usefor = array(
	"", 
	"<font color=blue>信用卡儲值</font>", 
	"<font color=blue>WEBATM儲值</font>", 
	"<font color=blue>虛擬轉帳儲值</font>",
	"<font color=red>交易扣款</font>",
	"<font color=blue>交易收款</font>",
	"<font color=blue>部落格行銷得分</font>",
	"<font color=blue>商品傳播得分</font>",
	"<font color=blue>ATM儲值</font>",
	"<font color=blue>退訂轉儲值</font>",
	"<font color=blue>交易退款</font>",
	"<font color=red>儲值金匯出</font>",
	"<font color=red>匯出手續費</font>",
	"<font color=red>優惠憑證發送費用</font>",
	"<font color=red>退款還原</red>",
	"<font color=red>廣告申購</red>"
	);
$page->addJSFile("../js/log_transaction_admin.js");
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
//$page->addContent("            <input type=\"button\" class=\"command\" value=\"刪除\" onClick=\"Delete();\">");
//$page->addContent("            <input type=\"button\" class=\"command\" value=\"新增\" onClick=\"New();\">");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");
$page->addContent("<TR>");
//$page->addContent("<TH class=\"grid_heading\" WIDTH=20><input type=\"checkbox\" name=\"memberno\" value=\"\" onClick=\"checkAll(mForm.memberno)\"></TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=50>編號</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:108px\">交易日期</TH>");
$page->addContent("<TH class=\"grid_heading\">會員</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">姓名</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">電話</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">原有餘額</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">進出金額</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">最新餘額</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:\">說明</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:\">備註</TH>");
$page->addContent("</TR>");
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($record = mysql_fetch_array($result)){
			$page->addContent("<TR>");
//            $page->addContent("<TD class=\"grid_check\"><input type=\"checkbox\" name=\"memberno\" value=\"$record[0]\"></TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
            $page->addContent("<TD class=\"grid_center\" style='font-size:10pt'>{$record['Date']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Owner']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Name']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Phone']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['oBalance']}</TD>");
			$page->addContent("<TD class=\"grid_center\"><font color='" . (($record['Amount']>0) ? "blue":"red") . "'>{$record['Amount']}</font></TD>");
			$page->addContent("<TD class=\"grid_center\">" . ($record['oBalance'] + $record['Amount']) . "</TD>");
			$page->addContent("<TD class=\"grid_center\" style='font-size:10pt'>{$usefor[$record['useFor']]}</TD>");
			$page->addContent("<TD class=\"grid_center\" style='font-size:10pt'>{$record['Memo']}</TD>");
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