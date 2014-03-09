<?php
include '../include/auth_admin.php';

require_once '../class/admin.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->member][1])){exit("權限不足!!");}
include("../class/tools.php");
include("../include/db_open.php");
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$keyword = $_REQUEST["keyword"];
$status = $_REQUEST["status"];
$level = $_REQUEST["level"];
$seller = $_REQUEST["seller"];
$page = new Admin();
$sort = $_REQUEST['sort'];
$order = $_REQUEST['order'];
$page->addJSFile("../js/common.js");
$page->setHeading($_MODULE->nameOf($_MODULE->member));
if($sort == ""){$sort = "dateLogin";}
if($order == ""){$order = "DESC";}




$sql = "SELECT * FROM Member WHERE 1=1 ";
$sql .= (($status != "") ? " AND Status = '$status'" : "");
$sql .= (($level != "") ? " AND Level = '$level'" : "");
$sql .= (($seller != "") ? " AND Seller = '$seller'" : "");
if ($keyword <> ""){
    $sql .= " AND (userID LIKE '%$keyword%' OR Name LIKE '%$keyword%' OR Phone LIKE '%$keyword%')";
}//if
//echo $sql;
$sql .= " ORDER BY $sort $order";
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$pagesize = 50;
$totalpage = ceil($num / $pagesize);
$pagging = new Pagging($totalpage, $pageno);

$page->addJSFile("../js/member_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"mForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"memberlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"mno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"sort\" value=\"$sort\">");
$page->addContent("<input type=\"hidden\" name=\"order\" value=\"$order\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("<select name='status'><option value=''>狀態</option><option value='0'" . (($status=='0') ? " SELECTED" : "") . ">停用</option><option value='1'" . (($status=='1') ? " SELECTED" : "") . ">啟用</option><option value='2'" . (($status=='2') ? " SELECTED" : "") . ">凍結</option></select>");
$page->addContent("<select name='level'><option value=''>等級</option><option value='1'" . (($level=='1') ? " SELECTED" : "") . ">1</option><option value='2'" . (($level=='2') ? " SELECTED" : "") . ">2</option><option value='3'" . (($level=='3') ? " SELECTED" : "") . ">3</option><option value='4'" . (($level=='4') ? " SELECTED" : "") . ">4</option><option value='5'" . (($level=='5') ? " SELECTED" : "") . ">5</option><option value='6'" . (($level=='6') ? " SELECTED" : "") . ">6</option><option value='7'" . (($level=='7') ? " SELECTED" : "") . ">7</option><option value='8'" . (($level=='8') ? " SELECTED" : "") . ">8</option></select>");
$page->addContent("<select name='seller'><option value=''>賣家</option><option value='0'" . (($seller=='0') ? " SELECTED" : "") . ">未申請</option><option value='1'" . (($seller=='1') ? " SELECTED" : "") . ">審核中</option><option value='2'" . (($seller=='2') ? " SELECTED" : "") . ">已審核</option><option value='3'>已取消</option></select>");
$page->addContent(" ");
$page->addContent("            <input type=\"text\" name=\"keyword\" value=\"$keyword\">");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"查詢\" onClick=\"Search();\">");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("            <input type=\"button\" class=\"command\" style='width:100px' value=\"特別會員設置\" onClick=\"VIP1();\">");
$page->addContent("            <input type=\"button\" class=\"command\" style='width:100px' value=\"特別會員解除\" onClick=\"VIP0();\">");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"手動凍結\" onClick=\"Lock();\">");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"解除凍結\" onClick=\"unLock();\">");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"刪除\" onClick=\"Delete();\">");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");
$page->addContent("<TR>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=20><input type=\"checkbox\" name=\"memberno\" value=\"\" onClick=\"checkAll(mForm.memberno)\"></TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=40>編號</TH>");
$img = "<img src=\"../images/" . strtolower($order) . ".gif\">";
$page->addContent("<TH class=\"grid_heading\" style='cursor:pointer' onClick=\"setSort('userID', '$order')\">電子郵件" . (($sort == "userID") ? $img : "") . "</TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=120 style='cursor:pointer' onClick=\"setSort('Name', '$order')\">姓名 / 暱稱" . (($sort == "Name") ? $img : "") . "</TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=90 style='cursor:pointer' onClick=\"setSort('Phone', '$order')\">手機" . (($sort == "Phone") ? $img : "") . "</TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=100 style='cursor:pointer' onClick=\"setSort('ipLogin ', '$order')\">登入IP" . (($sort == "ipLogin ") ? $img : "") . "</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"120\" style='cursor:pointer' onClick=\"setSort('dateLogin', '$order')\">最後登入" . (($sort == "dateLogin") ? $img : "") . "</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"60\" style='cursor:pointer' onClick=\"setSort('dateConfirm', '$order')\">狀態" . (($sort == "dateConfirm") ? $img : "") . "</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"60\" style='cursor:pointer' onClick=\"setSort('Level', '$order')\">等級" . (($sort == "Level") ? $img : "") . "</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"60\" style='cursor:pointer' onClick=\"setSort('Seller', '$order')\">賣家" . (($sort == "Seller") ? $img : "") . "</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"80\" style='cursor:pointer' onClick=\"setSort('Seller', '$order')\">特別會員" . (($sort == "VIP") ? $img : "") . "</TH>");
$page->addContent("</TR>");
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	$sellers = array("<font color='gray'>未申請</font>", "<font color='green'>審核中</font>", "<font color='blue'>已審核</font>", "<font color='red'>已取消</font>");
	$vip = array("<font color='gray'>未設置</font>", "<font color='blue'>已設置</font>", "<font color='red'>已取消</font>");
	$statuss =  array("<font color=red>停用</font>", "<font color=blue>已啟用</font>", "<font color=red>已凍結</font>");
	for ($i = 0; $i < $pagesize; $i++) {
		if($record = mysql_fetch_array($result)){
			$page->addContent("<TR>");
            $page->addContent("<TD class=\"grid_check\"><input type=\"checkbox\" name=\"memberno\" value=\"$record[0]\"></TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
			$page->addContent("<TD class=\"grid_left\">&nbsp;<A HREF=\"javascript:Edit('" . $record["No"] . "')\">" . $record["userID"] . "</A></TD>");
            $page->addContent("<TD class=\"grid_center\">" . $record["Name"] . " / " . $record["Nick"] . "&nbsp;</TD>");
            $page->addContent("<TD class=\"grid_center\">" . $record["Phone"] . "&nbsp;</TD>");
            $page->addContent("<TD class=\"grid_center\">" . $record["ipLogin"] . "&nbsp;</TD>");
            $page->addContent("<TD class=\"grid_center\">" . (($record["dateLogin"]=="0000-00-00 00:00:00") ? "<font color=red>尚未</font>" : "<font color=blue>" . $record["dateLogin"] . "</font>") . "</TD>");
            $page->addContent("<TD class=\"grid_center\" style=\"font-size:10pt;\">" . $statuss[$record['Status']] . "</TD>");
            $page->addContent("<TD class=\"grid_center\" style=\"font-size:10pt;\">" . $record['Level'] . "</TD>");
            $page->addContent("<TD class=\"grid_center\" style=\"font-size:10pt;\">" . $sellers[$record['Seller']] . "</TD>");
            $page->addContent("<TD class=\"grid_center\" style=\"font-size:10pt;\">" . $vip[$record['VIP']] . "</TD>");
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

function setSort(xSort, xOrder){
	if(xSort == mForm.sort.value){
		mForm.order.value = ((xOrder == "DESC") ? "ASC" : "DESC");
	}
	else{
		mForm.order.value = xOrder;
	}
	mForm.sort.value = xSort;
	Search();
	
}

function VIP0(){
    mForm.memberlist.value = getList();
    if (mForm.memberlist.value){
        if (confirm("確定要解除所選會員?")){
            mForm.action = "member_vip0.php";
            mForm.submit();
        }//if
    }//if
    else{
        alert("尚未選取!!");
    }//else
}//Delete
function VIP1(){
    mForm.memberlist.value = getList();
    if (mForm.memberlist.value){
        if (confirm("確定要設置所選會員?")){
            mForm.action = "member_vip1.php";
            mForm.submit();
        }//if
    }//if
    else{
        alert("尚未選取!!");
    }//else
}//Delete
function Lock(){
    mForm.memberlist.value = getList();
    if (mForm.memberlist.value){
        if (confirm("確定要凍結所選會員?")){
            mForm.action = "member_lock.php";
            mForm.submit();
        }//if
    }//if
    else{
        alert("尚未選取!!");
    }//else
}//Delete

function unLock(){
    mForm.memberlist.value = getList();
    if (mForm.memberlist.value){
        if (confirm("確定要解除凍結所選會員?")){
            mForm.action = "member_unlock.php";
            mForm.submit();
        }//if
    }//if
    else{
        alert("尚未選取!!");
    }//else
}//Delete

</script>