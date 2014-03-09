<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->log_export][1])){exit("權限不足!!");}

$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$userid = $HTTP_GET_VARS["userid"];
$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($_MODULE->nameOf($_MODULE->log_export));

$sql = "SELECT *, (SELECT Name FROM Member WHERE userID=logExport.Member) AS Name, (SELECT Phone FROM Member WHERE userID=logExport.Member) AS Phone , (SELECT SUM(Amount) FROM logTransaction WHERE Memo=logExport.ID AND useFor IN (11)) AS Pay, (SELECT SUM(Amount) FROM logTransaction WHERE Owner=logExport.Member) AS Balance FROM logExport WHERE 1=1 AND Status = 0";
if ($userid <> ""){
    $sql .= " AND Member = '$userid'";
}//if
$sql .= " ORDER BY dateRequest DESC";
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
$pagging = new Pagging($totalpage, $pageno);
$usefor = array("", "信用卡儲值", "WEBATM", "虛擬轉帳");
$page->addJSFile("../js/log_export_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"mForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"memberlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"mno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"amount\" value=\"\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("            請輸入會員Email：<input type=\"text\" name=\"userid\" value=\"$userid\" style='width:200px'>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"查詢\" onClick=\"Search();\">");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"取消申請\" onClick=\"Deny();\">");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"下載匯款資料\" onClick=\"Download();\" style='width:90px'>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"設為已匯出\" onClick=\"exportAll();\" style='width:90px'>");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");
$page->addContent("<TR>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=20><input type=\"checkbox\" name=\"memberno\" value=\"\" onClick=\"checkAll(mForm.memberno)\"></TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=50>編號</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">申請單號</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">申請日期</TH>");
$page->addContent("<TH class=\"grid_heading\">會員資料</TH>");
$page->addContent("<TH class=\"grid_heading\">匯出資料</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">申請金額</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:80px\">手續費</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:90px\">應匯出總額</TH>");
$page->addContent("<TH class=\"grid_heading\" style=\"width:130px\">匯出處理</TH>");
$page->addContent("</TR>");
if ($num>0){
//	mysql_data_seek($result,($pageno-1)*$pagesize);
//	for ($i = 0; $i < $pagesize; $i++) {
		while($record = mysql_fetch_array($result)){
			$page->addContent("<TR>");
            $page->addContent("<TD class=\"grid_check\"><input type=\"checkbox\" name=\"memberno\" value=\"$record[0]\"></TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['ID']}</TD>");
            $page->addContent("<TD class=\"grid_center\">" . str_replace(" ", "<br>", $record['dateRequest']) . "</TD>");
			$page->addContent("<TD class=\"grid_left\">{$record['Member']}<br>{$record['Name']}<br>{$record['Phone']}</TD>");
			$page->addContent("<TD class=\"grid_left\">{$record['Bank']}<br>{$record['Branch']}<br>{$record['Account']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Amount']}</TD>");
			$page->addContent("<TD class=\"grid_center\">-{$record['Fee']}</TD>");
			$page->addContent("<TD class=\"grid_center\">" . ($record['Amount']-$record['Fee']) . "</TD>");
			$page->addContent("<TD class=\"grid_center\"><input type='button' value='設為已匯出' onClick=\"Export('{$record[0]}');\"></TD>");
			$page->addContent("</TR>");
		}//if
//		else
//			break;
//	}//for
//	$page->addContent("</TABLE></TD></TR></form>");
//    $page->addContent("<TR><TD>" . $pagging->toString() . "</TD></TR>");
}//if
else{
	$page->addContent("</TABLE></TD></TR></form>");
	$page->addContent("<TR><TD class=\"grid_nodata\">目前無資料</TD></TR>");
}//else
$page->addContent("</TABLE>");
include("../include/db_close.php");
$page->show();
?>
<iframe name='iAction' style="width:100%; height:100px;display:none"></iframe>
<script language="javascript">
function Download(){
    mForm.memberlist.value = getList();
    if (mForm.memberlist.value){
		mForm.target = "iAction";
		mForm.action="log_export_list.php";
		mForm.submit();
    }//if
    else{
        alert("尚未選取!!");
    }//else
}

function Deny(){
    mForm.memberlist.value = getList();
    if (mForm.memberlist.value){
        if (confirm("確定要取消所選項目?\n\n取消申請會將事先扣除的儲值金(含手續費)退回該會員帳戶!")){
            mForm.action = code + "_refund.php";
			mForm.target = "iAction";
            mForm.submit();
        }//if
    }//if
    else{
        alert("尚未選取!!");
    }//else
}

function exportAll(){
    mForm.memberlist.value = getList();
    if (mForm.memberlist.value){
		if(confirm("確定要將所選項目設為已匯出!")){
			mForm.target = "iAction";
			mForm.action="log_export_saveall.php";
			mForm.submit();
		}
    }//if
    else{
        alert("尚未選取!!");
    }//else
}

function Export(xNo){
	if(confirm("確定要設為已匯出!")){
		mForm.mno.value = xNo;
		mForm.target = "iAction";
		mForm.action="log_export_save.php";
		mForm.submit();
	}
}
</script>