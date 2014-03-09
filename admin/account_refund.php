<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->account_refund][1])){exit("權限不足!!");}
$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);

$tab = (($_REQUEST['tab'] != "") ? $_REQUEST['tab'] : 23);
$Y = $_REQUEST['Y'];
$M = $_REQUEST['M'];
$D = $_REQUEST['D'];
$date = date('Y-m-01');

$w = date('w');

$date = date("Y-m-d", strtotime(date('Y-m-d') . "-" . ($w-3) . " day"));

$menu = array();
for($i=0; $i<24; $i++){
	$time = strtotime($date . "-" . (23-$i) . " week");
	$tmp = date("n/j", $time);
	$menu["?tab=" . $i] = $tmp;
	if($tab==$i){
		$curr = $tmp;
		list($Y, $M, $D) = explode("-", date("Y-m-d", $time));
	}
}


if($tab != 23){
	$gen = " DISABLED";
}
else{
	$result = mysql_query("SELECT * FROM logBilling WHERE Y='$Y' AND M='$M' AND D='$D' and Refund=1") or die(mysql_error());
	if(mysql_num_rows($result) > 0){
		$gen = " DISABLED";
	}
}




$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($menu, $tab);


$sql = "SELECT *, Billing.No AS bNo,  DATEDIFF(CURDATE(), dateGenerate) AS Days, (SELECT Amount FROM Orders WHERE Orders.ID=Billing.orderID) AS A, (SELECT Amount FROM Orders WHERE Orders.ID=Billing.orderID) AS T, IFNULL((SELECT Amount FROM Payment WHERE Memo=Billing.orderID AND payBy = 4), 0) AS Prepaid1, IFNULL((SELECT Amount FROM Payment WHERE Memo=Billing.orderID AND payBy <> 4), 0) AS Paid1 , IFNULL((SELECT payBy FROM Payment WHERE Memo=Billing.orderID AND payBy <> 4), 0) as payBy1 FROM logBilling INNER JOIN Billing ON logBilling.No=Billing.logNo";
$sql .= " WHERE Y='$Y' AND M='$M' AND D='$D' and logBilling.Refund=1";
$sql .= " ORDER BY paymentID DESC";
//echo $sql;
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
$pagging = new Pagging($totalpage, $pageno);
$counts = 0;
$total = 0;
$fee = 0;



$page->addJSFile("../js/jquery.min.js");
$page->addJSFile("../js/account_refund_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"mForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"memberlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"Y\" value=\"$Y\">");
$page->addContent("<input type=\"hidden\" name=\"M\" value=\"$M\">");
$page->addContent("<input type=\"hidden\" name=\"D\" value=\"$D\">");
$page->addContent("<input type=\"hidden\" name=\"tab\" value=\"$tab\">");


$list = "";
$days = 0;

if ($num>0){
	$list .= "<TR><TD>";
	$list .= "<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">";
	$list .= "<TR>";
	$list .= "<TH class=\"grid_heading\" width=90>台灣里單號</TH>";
	$list .= "<TH class=\"grid_heading\" width=120>訂單編號</TH>";
	$list .= "<TH class=\"grid_heading\">品名</TH>";
	$list .= "<TH class=\"grid_heading\" width=50>數量</TH>";
	$list .= "<TH class=\"grid_heading\" width=50>退數</TH>";
	$list .= "<TH class=\"grid_heading\" width=50>貨檢</TH>";
	$list .= "<TH class=\"grid_heading\" >轉儲值<br>原因</TH>";
	$list .= "<TH class=\"grid_heading\" width=80>金額</TH>";
	$list .= "<TH class=\"grid_heading\" colspan='2'>退款額</TH>";
	$list .= "<TH class=\"grid_heading\" >退款申請<br><input type='checkbox' class='apply' name='apply[]' value='' onClick=\"checkAll();\"></TH>";
	$list .= "<TH class=\"grid_heading\" width=40>還原<br>凍結</TH>";
	$list .= "</TR>";
	$payby=array(
		"無",
		"信用卡(3%)",
		"Web ATM",
		"ATM轉帳(0.5%)",
		"儲值金"
	);
	while($record = mysql_fetch_array($result)){
		if($record['Apply'] == 1){
			$counts += $record['Num'];
			$total += $record['Total'];
			$fee += $record['Fee'];
		}
	
		if($date_generate == ""){
			$date_generate = substr($record['dateGenerate'], 0, 10);
		}

		$result1= mysql_query("SELECT * FROM Orders WHERE ID='" . $record['orderID'] . "'") or die(mysql_error());
		$order=mysql_fetch_array($result1);
		$ship_status = "N/A";
		if($order['Deliver'] == 1){
			$ship_status = "";
			if($order['dateShipped'] != "0000-00-00 00:00:00"){
				$ship_status .= "已出貨<br>";
				if($order['dateReturn'] != "0000-00-00 00:00:00"){
					$ship_status .= "已回貨";
				}
			}
			else{
				$ship_status .= "未出貨";
			}
		}


		$list .= "<TR>";
		$list .= "<TD class=\"grid_center\" rowspan='2' style='color:blue'>{$record['paymentID']}</TD>";
		$list .= "<TD class=\"grid_center\" rowspan='2' style='color:blue'>{$record['orderID']}</TD>";
		$list .= "<TD class=\"grid_center\" rowspan='2'>{$record['Name']}</TD>";
		$list .= "<TD class=\"grid_center\" rowspan='2' style='color:blue'>{$record['A']}</TD>";
		$list .= "<TD class=\"grid_center\" rowspan='2' style='color:blue'><span id='num{$record['bNo']}'>{$record['Num']}</span></TD>";
		$list .= "<TD class=\"grid_center\" rowspan='2' style='color:blue'>{$ship_status}</TD>";
		$list .= "<TD class=\"grid_center\" rowspan='2' style='color:blue'>&nbsp;{$record['Reason']}</TD>";
		$list .= "<TD class=\"grid_center\" style='color:blue'>{$record['Amount']}</TD>";
		$list .= "<TD class=\"grid_center\" style='color:red' width=80><span id='total{$record['bNo']}'>" . ($record['Total'] + $record['Fee']) . "</span><span id='fee{$record['bNo']}' style='display:none'>{$record['Fee']}</span></TD>";
		$list .= "<TD class=\"grid_center\" width=80>{$payby[$record['payBy1']]}</TD>";
		$list .= "<TD class=\"grid_center\" rowspan='2'>";
		if($record['Reason'] == ""){
			$list .= "<input type='checkbox' class='apply' name='apply[]' value='{$record['bNo']}'" . (($record['Apply'] == 1) ? " CHECKED":"") . " onClick=\"setTotal();\"" . (($record['Lock'] == 1) ? " disabled" : "") . ">";
		}
		else{	
			$list .= "<input type='button' value='儲值退款' class='transfer'" . (($record['Transfer'] == 1) ? " disabled":"") . " onClick=\"setTransfer(this, {$record['bNo']});\">";		
		}
		$list .= "</TD>";
		
		$list .= "<TD class=\"grid_center\" rowspan='2'>";
		if($record['Reason'] == ""){
			$list .= "<input type='checkbox' name='lock[]' value='{$record['bNo']}'" . (($record['Lock'] == 0 || $record['Days'] > 3) ? " disabled" : "") . ">";
		}
		else{
			$list .= "&nbsp;";
		}
		$list .= "</TD>";
		$list .= "</TR>";
		$list .= "<TR>";
		$list .= "<TD class=\"grid_center\" style='color:blue'>{$record['Prepaid']}</TD>";
		$list .= "<TD class=\"grid_center\" style='color:green'>{$record['Total0']}</TD>";
		$list .= "<TD class=\"grid_center\">儲值金</TD>";
		$list .= "</TR>";
	}//while
	$list .= "</TABLE></TD></TR>";
}//if

$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"產生退款申請書\" onClick=\"Generate();\" style='width:100px'{$gen}>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"電子對帳檔\" onClick=\"Download();\" style='width:80px'>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"資料凍結/還原\" onClick=\"Lock();\" style='width:80px'>");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("        退款單號數目：<span id='counts' style='display: inline-block; width:30px; text-align:left'>" . $counts . "</span>&nbsp;退款總計：<span id='total' style='display: inline-block; width:60px; text-align:left'>" . $total . "</span>&nbsp;手續費總計：<span id='fee' style='display: inline-block; width:50px; text-align:left'>" . $fee . "</span>");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
if ($num>0){
	$page->addContent("<TR class=\"grid_toolbar\"><TD>");
	$page->addContent("<font size=+1>退款對帳單</font>(產生日期：{$date_generate}，訂單編號數：{$num})");
	$page->addContent("</TD></TR>");
}
$page->addContent($list);
$page->addContent("</form></TABLE>");

include("../include/db_close.php");
$page->show();
?>
<iframe name="iAction" style="width:100%; height:100px; display:none"></iframe>
<script language="javascript">
	function setTransfer(x, no){
		if(confirm("儲值退款會立即將退款額轉入該會員帳戶的儲值金!")){
			$.post(
				'account_refund_transfer.php',
				{
					no: no
				},
				function(data)
				{
	//					alert(data);
					x.disabled = true;
				}
			);
		}
	}
	function Generate(){
		if(confirm("確定要產生退款申請書?")){
			mForm.target="";
			mForm.action="account_refund_gen.php";
			mForm.submit();
		}
	}

	function setTotal(){
		var total = 0;
		var fee = 0;
		var counts = 0;
        $('.apply').each(function(){                
			if(this.value && this.checked){
				total += parseInt($("#total" + this.value).html(), 10)-parseInt($("#fee" + this.value).html(), 10);
				fee += parseInt($("#fee" + this.value).html(), 10);
				counts += parseInt($("#num" + this.value).html(), 10);
			}
        });
		$("#total").html(total);
		$("#fee").html(fee);
		$("#counts").html(counts);
	}
	function checkAll(){
		var e = $('.apply')[0].checked;
        $('.apply').each(function(){
			if(!this.disabled){
				this.checked = e;
			}
        });
		setTotal();
	}

	function Download(){
		mForm.target="iAction";
		mForm.action="account_refund_download.php";
		mForm.submit();
	}

	function Lock(){
		var total = parseInt($("#total").html(), 10);
//		alert(total);
		if(total > 0){
			if(confirm("確定要凍結資料?")){
				mForm.target="";
				mForm.action="account_refund_lock.php";
				mForm.submit();
			}
		}
		else{
			alert("請先勾選請款項目!");
		}
	}

</script>