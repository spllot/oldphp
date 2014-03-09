<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/tools.php';
require_once '../class/system.php';
require_once '../class/pagging.php';
require_once '../class/javascript.php';

if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->seller_msg][1])){exit("權限不足!!");}

$tab = (($_REQUEST['tab'] != "") ? $_REQUEST['tab'] : 0);
$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$pagesize = 10;
$usefor = $_REQUEST['usefor'];

$menu = array(
	"seller_msg.php?tab=0" => "買家問題回覆",
	"seller_msg.php?tab=1" => "賣家問題回覆",
);





if($usefor == ""){
//	$usefor = '1';
}

$page = new Admin();
//$page->setHeading($_MODULE->nameOf($_MODULE->seller_msg));
$page->setHeading($menu, $tab);



$page->addJSFile("../js/common_admin.js");
$page->addJSFile("../js/seller_msg_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"iForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"itemlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"itemno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"sort\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"tab\" value=\"$tab\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("			<select name=usefor onChange=\"iForm.action='';iForm.submit();\">");
$page->addContent("				<option value=''>所有問題</option>");
$page->addContent("				<option value='1'" . (($usefor == "1") ? " SELECTED" : "") . ">出貨問題</option>");
$page->addContent("				<option value='2'" . (($usefor == "2") ? " SELECTED" : "") . ">退貨問題</option>");
$page->addContent("				<option value='3'" . (($usefor == "3") ? " SELECTED" : "") . ">退款問題</option>");
$page->addContent("				<option value='4'" . (($usefor == "4") ? " SELECTED" : "") . ">貨品問題</option>");
$page->addContent("				<option value='5'" . (($usefor == "5") ? " SELECTED" : "") . ">付款問題</option>");
$page->addContent("				<option value='6'" . (($usefor == "6") ? " SELECTED" : "") . ">保固問題</option>");
$page->addContent("				<option value='7'" . (($usefor == "7") ? " SELECTED" : "") . ">發票問題</option>");
$page->addContent("				<option value='8'" . (($usefor == "8") ? " SELECTED" : "") . ">匯款問題</option>");
$page->addContent("				<option value='9'" . (($usefor == "9") ? " SELECTED" : "") . ">其他問題</option>");
$page->addContent("			</select>");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
if($tab == 0){
	$page->addContent("            <input name=\"btnDelete\" type=\"button\" class=\"command\" value=\"傳信給賣家\" onClick=\"Forward();\">");
}
else{
}

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
if($tab == 0){
	$page->addContent("<TH class=\"grid_heading\" width=\"150\">賣家商品與資訊</TH>");
	$page->addContent("<TH class=\"grid_heading\">買家問題詢問內容</TH>");
}
else{
	$page->addContent("<TH class=\"grid_heading\" width=\"150\">商品名稱</TH>");
	$page->addContent("<TH class=\"grid_heading\">賣家問題詢問內容</TH>");
}
$page->addContent("<TH class=\"grid_heading\" width=\"150\">回覆</TH>");
$page->addContent("<TH class=\"grid_heading\" width=\"60\">編輯</TH>");
$page->addContent("</TR>");
include '../include/db_open.php';

$sql = "SELECT Help.*, Product.Deliver, Product.Mode, (SELECT Nick FROM Member WHERE No=Help.Seller) AS sName, (SELECT userID FROM Member WHERE No=Help.Seller) AS sEMail, (SELECT Nick FROM Member WHERE No=Help.Member) AS mName, (SELECT userID FROM Member WHERE No=Help.Member) AS mEMail FROM Help INNER JOIN Product ON Product.No = Help.Product WHERE 1=1 " . (($usefor!="") ? " AND Help.Catalog = '$usefor'" : "") . " AND isSeller='$tab' ORDER BY dateSubmited DESC";
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
			if($rs['Mode'] == 1){
				if($rs['Deliver'] == 0){
					$tab=1;
				}
				else{
					$tab=2;
				}
			}
			else{
				if($rs['Deliver'] == 0){
					$tab=4;
				}
				else{
					$tab=5;
				}
			}

			$reply = (($rs['dateForward'] != "0000-00-00 00:00:00") ? "轉寄給賣家<br>" : "");
			$reply .= (($rs['dateReplied'] != "0000-00-00 00:00:00") ? $rs['dateReplied'] : "");
			$reply = (($reply == "") ? "尚未" : $reply);

			$page->addContent("<TR>");
            $page->addContent("<TD class=\"grid_check\"><input type=\"checkbox\" name=\"no\" value=\"{$rs['No']}\"></TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
			$page->addContent("<TD class=\"grid_center\">{$rs['dateSubmited']}</TD>");
			$page->addContent("<TD class=\"grid_left\" style='font-size:12px; padding:2px'>");
			$page->addContent("<a href='http://{$WEB_HOST}/product" . $tab . "_detail.php?no=" . $rs['Product'] . "' target='_blank'>" . $rs['pName'] . "</a><br>");
			if($tab == 0){
				$page->addContent("暱稱：" . $rs['sName'] . "<br>");
				$page->addContent("電子郵件：" . $rs['sEMail'] . "<br>");
			}
			$page->addContent("</TD>");
			$page->addContent("<TD class=\"grid_left\" style='font-size:12px; padding:2px'>");
			$page->addContent("暱稱：" . $rs['mName'] . "<br>");
			$page->addContent("電子郵件：" . $rs['mEMail'] . "<br>");
			$page->addContent("問題：" . $rs['Content']);
			$page->addContent("</TD>");
			$page->addContent("<TD class=\"grid_center\">" . $reply . "</TD>");
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
<script language="javascript">
	function Forward(){
		iForm.itemlist.value = getList();
		if (iForm.itemlist.value){
			if (confirm("確定要轉寄所選項目?")){
				iForm.action = "seller_msg_forward.php";
				iForm.submit();
			}//if
		}//if
		else{
			alert("尚未選取!!");
		}//else
	}
</script>