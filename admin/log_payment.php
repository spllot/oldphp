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
$userid = $_REQUEST["userid"];
$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($_MODULE->nameOf($_MODULE->log_payment));

$sql = "SELECT logATM.*, Member.userID, Member.Name FROM logATM INNER JOIN Member ON Member.No=logATM.Member WHERE 1=1";
if ($userid <> ""){
    $sql .= " AND Member.userID = '$userid'";
}//if
$sql .= " ORDER BY logATM.No DESC";
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
$page->addContent("<input type=\"hidden\" name=\"status\" value=\"\">");
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
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">回報時間</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"\">會員編號</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">姓名</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:70px\">儲值金額</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:90px\">帳戶後五碼</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">銀行代碼</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">匯款時間</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:90px\">儲值匯款<br>確認</TH>");
$page->addContent("</TR>");
$payby=array(
	"",
	"信用卡(Visa)",
	"Web ATM",
	"ATM轉帳",
	"儲值金"
);
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($record = mysql_fetch_array($result)){
			switch($record['Status']){
				case 0:
					$status = "<input type='button' value='匯款確認' onClick='setStatus(1, {$record['No']})'><br>";
					$status .= "<input type='button' value='未匯款' onClick='setStatus(2, {$record['No']})'>";
					break;
				case 1:
					$status = "已確認";
					break;
				case 2:
					$status = "未匯款";
					break;
			}
			$page->addContent("<TR>");
            $page->addContent("<TD class=\"grid_check\"><input type=\"checkbox\" name=\"memberno\" value=\"$record[0]\"></TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
            $page->addContent("<TD class=\"grid_center\" style=''>" . str_replace(" ", "<br>", $record['dateLog']) . "</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['userID']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Name']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Amount']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Account']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Bank']}</TD>");
			$page->addContent("<TD class=\"grid_center\">" . str_replace(" ", "<br>", $record['dateTrans']) . "</TD>");
            $page->addContent("<TD class=\"grid_center\" style='padding:2px'>" . $status . "</TD>");
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
function setStatus(xStatus, xNo){
	if (confirm("確定" + ((xStatus==1) ? "要做匯款確認" : "要設為未匯款") + "?")){
		mForm.mno.value = xNo;
		mForm.status.value = xStatus;
		mForm.action = code + "_status.php";
		mForm.submit();
	}
}
function Purge(){
	if (confirm("確定要刪除所有未付款項目?")){
		mForm.action = code + "_purge.php";
		mForm.submit();
	}
}
</script>