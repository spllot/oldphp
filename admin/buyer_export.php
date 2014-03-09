<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->buyer_export][1])){exit("權限不足!!");}
$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$tab = (($_REQUEST['tab'] != "") ? $_REQUEST['tab'] : 3);
$year = (($_REQUEST['year'] != "") ? $_REQUEST['year'] : date('Y'));
$month = (($_REQUEST['month'] != "") ? $_REQUEST['month'] : date('j'));

$date = date('Y-m-01');
$menu = array();

for($i=0; $i<4; $i++){
	$tmp = explode("-", date("Y-m-n", strtotime($date . "-" . (9-3*$i) . " month")));
	$j = ceil($tmp[2]/3);
	$menu["buyer_export.php?tab=" . $i] = $tmp[0] . " 第{$j}季";;
	if($tab==$i){
		$date1 = date('Y-m-d', strtotime($tmp[0] . '-01-01' . "+" . ($j-1)*3 . " month"));
		$date2 = date('Y-m-d', strtotime($date1 . "+3 month -1 day"));
//		echo $date1 . "~" . $date2;
	}
}






$amount = 0;
$result = mysql_query("SELECT * FROM Config WHERE 1=1");
while($rs=mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}


$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->addJSFile("/js/jquery.js");
$page->setHeading($menu, $tab);
list($Y, $M)=explode("-", date('Y-m'));

if($tab == 3){
	$sql = "(SELECT logCoupon.Member, Member.Name, userID, SUM(Cost) AS Counts, Receipt, 0 AS Type FROM logCoupon INNER JOIN Member ON logCoupon.Member=Member.No WHERE 1=1 AND logCoupon.Phone <> '' AND Receipt = '' GROUP BY userID)
	UNION
	(SELECT logCoupon.Member, Member.Name, userID, SUM(Cost) AS Counts, Receipt, (SELECT Type FROM logReceiptSMS WHERE ID=logCoupon.Receipt) AS Type FROM logCoupon INNER JOIN Member ON logCoupon.Member=Member.No WHERE 1=1 AND logCoupon.Receipt <> '' AND Receipt IN (SELECT ID FROM logReceiptSMS WHERE dateCreate >= '$date1' AND dateCreate <= '$date2')  GROUP BY userID)";
}
else{
	$sql = "SELECT logCoupon.Member, Member.Name, userID, SUM(Cost) AS Counts, Receipt, (SELECT Type FROM logReceiptSMS WHERE ID=logCoupon.Receipt) AS Type FROM logCoupon INNER JOIN Member ON logCoupon.Member=Member.No WHERE 1=1 AND logCoupon.Receipt <> '' AND Receipt IN (SELECT ID FROM logReceiptSMS WHERE dateCreate >= '$date1' AND dateCreate <= '$date2')  GROUP BY userID";
}

$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
$pagging = new Pagging($totalpage, $pageno);

$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"mForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"itemno\" value=\"\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");
$page->addContent("<TR>");
$page->addContent("<TH class=\"grid_heading\">商品賣家</TH>");
$page->addContent("<TH class=\"grid_heading\">會員帳號</TH>");
$page->addContent("<TH class=\"grid_heading\" width=150>金額統計</TH>");
$page->addContent("<TH class=\"grid_heading\" width=150>本月發票申請紀錄</TH>");
$page->addContent("<TH class=\"grid_heading\" width=100>申請發票</TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=150>發票號碼</TH>");
$page->addContent("</TR>");

$types = array("", "捐贈", "紙本", "電子");

if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($record = mysql_fetch_array($result)){
			if($tab == 3){
				$sql = "SELECT * FROM logReceipt WHERE Seller={$record['Member']} AND Y='$Y' AND M='$M'";
				$result1 = mysql_query($sql) or die(mysql_error());
				if($log = mysql_fetch_array($result1)){
					$log_type = "(V)" . $types[$log['Type']];
				}
				else{
					$log_type="&nbsp;";
				}
			}
			
			$page->addContent("<TR>");
			$page->addContent("<TD class=\"grid_center\"><a href=\"javascript:Member({$record['Member']});\">{$record['Name']}</a></TD>");
			$page->addContent("<TD class=\"grid_center\"><a href=\"javascript:Member({$record['Member']});\">{$record['userID']}</a></TD>");
			$page->addContent("<TD class=\"grid_center\">" . $record['Counts'] . "</TD>");
			$page->addContent("<TD class=\"grid_center\">" . (($tab == 3) ? $log_type : "&nbsp;") . "</TD>");
			$page->addContent("<TD class=\"grid_center\" id='i{$i}'>" . (($record['Receipt']!="") ? "V({$types[$record['Type']]})":"<input type='button' value='申請發票' onClick=\"Receipt({$record['Member']}, {$i}, this);\">") . "</TD>");
			$page->addContent("<TD class=\"grid_center\" id='r{$i}'>" . (($record['Receipt']!="") ? $record['Receipt']:"&nbsp;") . "</TD>");
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
	var types = Array("", "捐贈", "紙本", "電子");
	function Member(x){
		window.showModalDialog("buyer_export_member.php?no="+x);
	}
	function Receipt(x, y, z){
			z.disabled = true;
			$.post(
				'buyer_export_receipt.php',
				{
					no: x
				},
				function(data)
				{
//					alert(data);
					eval("var response = " + data);
					if(response.receipt != ""){
						$("#i" + y).html("V("+types[response.type]+")");
						$("#r" + y).html(response.receipt);
					}
					else{
						alert("取號錯誤，請重新整理頁面!");
					}
				}
			);
	}
</script>