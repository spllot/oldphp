<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->seller_export][1])){exit("權限不足!!");}
$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);

$tab = (($_REQUEST['tab'] != "") ? $_REQUEST['tab'] : 5);
$type  = $_REQUEST['type'];
$date = date('Y-m-01');
$menu = array();

$result = mysql_query("SELECT * FROM Config") or die(mysql_error());
while($rs=mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}



for($i=0; $i<6; $i++){
	$tmp = date("Y-m", strtotime($date . "-" . (5-$i) . " month"));
	$menu["seller_export.php?tab=" . $i . "&type=$type"] = $tmp;
	if($tab==$i) $curr = $tmp;
}

list($Y, $M) = explode("-", $curr);

$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($menu, $tab);




$sql = "SELECT DISTINCT Seller, (SELECT Name FROM Member WHERE No=Billing.Seller) AS Name, (SELECT ID FROM logReceipt WHERE Y='$Y' AND M='$M' AND Seller=Billing.Seller) AS Receipt, IFNULL((SELECT Type FROM logReceipt WHERE Y='$Y' AND M='$M' AND Seller=Billing.Seller), 2) AS Type, (SELECT COUNT(*) FROM sellerExport WHERE Y='$Y' AND M='$M' AND Seller=Billing.Seller) AS Transfer FROM Billing INNER JOIN logBilling ON logBilling.No=Billing.logNo";
$sql .= " WHERE logBilling.Y='$Y' AND logBilling.M='$M' AND logBilling.Refund=0 AND Billing.Apply=1";
$sql .= (($type != "") ? " AND orderID LIKE '%-$type-%'":"");
$sql .= " ORDER BY Seller";
//echo $sql;
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
$pagging = new Pagging($totalpage, $pageno);
$counts = 0;
$total = 0;
$fee = 0;



$page->addJSFile("../js/jquery.min.js");
$page->addJSFile("../js/seller_export_admin.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"mForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"memberlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"Y\" value=\"$Y\">");
$page->addContent("<input type=\"hidden\" name=\"M\" value=\"$M\">");
$page->addContent("<input type=\"hidden\" name=\"D\" value=\"$D\">");
$page->addContent("<input type=\"hidden\" name=\"tab\" value=\"$tab\">");


$list = "";
$days = 0;

	$list .= "<TR><TD>";
	$list .= "<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">";
	$list .= "<TR>";
	$list .= "<TH class=\"grid_heading\" width=70 rowspan='2'>商品賣家</TH>";
	$list .= "<TH class=\"grid_heading\" width=80 rowspan='2'>商品類別<br>(代碼利潤)</TH>";
	$list .= "<TH class=\"grid_heading\" rowspan='2'>品名</TH>";
	$list .= "<TH class=\"grid_heading\" width=50 rowspan='2'>售價</TH>";
	$list .= "<TH class=\"grid_heading\" width=70 rowspan='2'>商品訂單<br>總數</TH>";
	$list .= "<TH class=\"grid_heading\" width=208 colspan='4'>訂單狀態數</TH>";
	$list .= "<TH class=\"grid_heading\" width=70 rowspan='2'>本月銷售<br>請款數</TH>";
	$list .= "<TH class=\"grid_heading\" width=70 rowspan='2'>累計銷售<br>請款數</TH>";
	$list .= "<TH class=\"grid_heading\" width=50 rowspan='2'>總計</TH>";
	$list .= "<TH class=\"grid_heading\" rowspan='2'>總計匯款<br>/5%營收</TH>";
	$list .= "<TH class=\"grid_heading\" rowspan='2'>匯款<input style='display:none' type='checkbox' class='transfer' name='transfer[]' value='' onClick=\"checkTransfer();\"><br>/發票申請<br><input type='checkbox' class='receipt' name='receipt[]' value='' onClick=\"checkReceipt();\" style='display:none'></TH>";
	$list .= "</TR>";
	$list .= "<TR>";
	$list .= "<TH class=\"grid_heading\" width=50>已取消</TH>";
	$list .= "<TH class=\"grid_heading\" width=50>已退款</TH>";
	$list .= "<TH class=\"grid_heading\" width=50>已完成</TH>";
	$list .= "<TH class=\"grid_heading\" width=50>待消費</TH>";
	$list .= "</TR>";
	$payby=array(
		"無",
		"信用卡(3%)",
		"Web ATM",
		"ATM轉帳(0.5%)",
		"儲值金"
	);

$types=array(
	'a11'	=>	"到店團購<br>(a11-15%)",
	'a00'	=>	"到店團購<br>(a00-15%)",
	'b11'	=>	"宅配團購<br>(b11-15%)",
	'b00'	=>	"宅配團購<br>(b00-15%)",
	'c11'	=>	"到店廉售<br>(c11-5%)",
	'c10'	=>	"到店廉售<br>(c10-5%)",
	'd00'	=>	"宅配廉售<br>(d00-5%)",
	'd01'	=>	"宅配廉售<br>(d01-5%)",
	'd10'	=>	"宅配廉售<br>(d10-5%)",
	'd11'	=>	"宅配廉售<br>(d11-5%)",
);

$receipts = array("", "捐贈", "紙本", "電子");

if ($num>0){
	while($record = mysql_fetch_array($result)){
		$sql = "SELECT DISTINCT Product, Name, SUBSTRING(orderID, 10, 3) AS Type, Price, logNo FROM Billing INNER JOIN logBilling ON logBilling.No=Billing.logNo";
		$sql .= " WHERE logBilling.Y='$Y' AND logBilling.M='$M' AND logBilling.Refund=0";
		$sql .= (($type != "") ? " AND orderID LIKE '%-$type-%'":"");
		$sql .= " ORDER BY Product";
		$result1=mysql_query($sql) or die(mysql_error());
		$rows = 0;
		$list1 = "";
		$list2 = "";
		$a_total = 0;
		$i_total = 0;
		if(mysql_num_rows($result1) > 0){
			while($rs1 = mysql_fetch_array($result1)){
				$sql = "SELECT (SELECT SUM(Num) FROM Billing WHERE Product='" . $rs1['Product'] . "' AND logNo < {$rs1['logNo']} ) AS Summary, SUM(Total) AS Total, SUM(Fee) AS Fee, SUM(Num) AS Num, D, SUM(Amounts) AS Amounts, SUM(Cancels) AS Cancels, SUM(Waitings) AS Waitings, SUM(Refunds) AS Refunds, SUM(Completes) AS Completes FROM Billing INNER JOIN logBilling ON logBilling.No=Billing.logNo";
				$sql .= " WHERE logBilling.Y='$Y' AND logBilling.M='$M' AND logBilling.Refund=0 AND Billing.Product='" . $rs1['Product'] . "'";
				$sql .= (($type != "") ? " AND orderID LIKE '%-$type-%'":"");
				$sql .= " GROUP BY D ORDER BY D";
				$result2=mysql_query($sql) or die(mysql_error());
				$list3 = "";
				$amount = 0;
				$income = 0;
				$amounts = 0;
				$cancels = 0;
				$refunds = 0;
				$completes = 0;
				$waitings = 0;
				$num = 0;
				$sum = 0;
				while($rs2 =mysql_fetch_array($result2)){
					$amount += $rs2['Num'] * $rs1['Price'];//$rs2['Total'];				
					$num += $rs2['Num'];
					$amounts += $rs2['Amounts'];
					$cancels += $rs2['Cancels'];
					$refunds += $rs2['Refunds'];
					$completes += $rs2['Completes'];
					$waitings += $rs2['Waitings'];
				}
				$income = ceil($amount * 0.05);
				$amount -= $income;
				$sum = $num + $rs2['Summary'];
				$a_total += $amount;
				$i_total += $income;
				if($rows == 0){
					$list1 .= "<TD class=\"grid_center\">{$types[$rs1['Type']]}</TD>";
					$list1 .= "<TD class=\"grid_center\">{$rs1['Name']}</TD>";
					$list1 .= "<TD class=\"grid_center\">" . number_format($rs1['Price']) . "</TD>";
					$list1 .= "<TD class=\"grid_center\">{$amounts}</TD>";
					$list1 .= "<TD class=\"grid_center\">{$cancels}</TD>";
					$list1 .= "<TD class=\"grid_center\">{$refunds}</TD>";
					$list1 .= "<TD class=\"grid_center\">{$completes}</TD>";
					$list1 .= "<TD class=\"grid_center\">{$waitings}</TD>";
					$list1 .= "<TD class=\"grid_center\">{$num}</TD>";
					$list1 .= "<TD class=\"grid_center\">{$sum}</TD>";
//					$list1 .= "<TD class=\"grid_center\"><font color='red'>{$amount}</font><br>/ <font color='blue'>{$income}</font></TD>";
					$list1 .= "<TD class=\"grid_center\">" . number_format($num * $rs1['Price']) . "</TD>";
				}
				else{
					$list2 .= "<tr>";
					$list2 .= "<TD class=\"grid_center\">{$types[$rs1['Type']]}</TD>";
					$list2 .= "<TD class=\"grid_center\">{$rs1['Name']}</TD>";
					$list2 .= "<TD class=\"grid_center\">" . number_format($rs1['Price']) . "</TD>";
					$list2 .= "<TD class=\"grid_center\">{$amounts}</TD>";
					$list2 .= "<TD class=\"grid_center\">{$cancels}</TD>";
					$list2 .= "<TD class=\"grid_center\">{$refunds}</TD>";
					$list2 .= "<TD class=\"grid_center\">{$completes}</TD>";
					$list2 .= "<TD class=\"grid_center\">{$waitings}</TD>";
					$list2 .= "<TD class=\"grid_center\">{$num}</TD>";
					$list2 .= "<TD class=\"grid_center\">{$sum}</TD>";
//					$list2 .= "<TD class=\"grid_center\"><font color='red'>{$amount}</font><br> / <font color='blue'>{$income}</font></TD>";
					$list2 .= "<TD class=\"grid_center\">" . number_format($num * $rs1['Price']) . "</TD>";
					$list2 .= "</tr>";
				}
				$rows ++;
			}
		}
		if($record['Receipt'] != ""){
			$receipt = $record['Receipt'];
		}
		else{
			$receipt = "<input type='checkbox' class='receipt' name='receipt[]' value='{$record['Seller']}' onClick='setReceipt({$record['Seller']});'>";
		}

		$f = $_CONFIG['fee3'];
		$p = 0;
		$info = "<Br> (";
		if($record['Type'] == 2){
			$p = $_CONFIG['fee1'];
			$info .= "扣郵費, ";
		}
		$info .= "扣匯費)";

		$a_total -= $p;
		$a_total -= $f;
		$counts ++;
		$total += $a_total;
		$earn += $i_total;


		if($record['Transfer'] >0){
			$transfer="<input type='checkbox' class='transfer' name='transfer[]' value='{$record['Seller']}' checked disabled>";
		}
		else{
			$transfer="<input type='checkbox' class='transfer' name='transfer[]' value='{$record['Seller']}' onClick='setTransfer(this, {$record['Seller']}, $p, $f);'>";
		}

		$list .= "<TR>";
		$list .= "<TD rowspan='$rows' class=\"grid_center\" style='color:blue; cursor:pointer' onClick=\"showReceipt('{$record['Seller']}');\">{$record['Name']}</TD>";
		$list .= $list1;
		$list .= "<TD rowspan='$rows' class=\"grid_center\"><font color='red' id='total{$record['Seller']}'>" . $a_total ." </font><font color='red'>" . $info . "</font><br> / <font color='blue' id='fee{$record['Seller']}'>" . $i_total . "</font></TD>";
		$list .= "<TD rowspan='$rows' class=\"grid_center\">匯款申請<br>{$transfer}<br><br>發票號碼({$receipts[$record['Type']]})<br><span id='receipt{$record['Seller']}'>{$receipt}</span></TD>";
		$list .= "</TR>";
		$list .= $list2;
	}//while
	$list .= "</TABLE></TD></TR>";
}//if

$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("            商品類別：<select name='type' onChange='Search();'><option value=''>全部類別</option>");
$page->addContent("            <option value='a11'" . (($type=="a11") ? " SELECTED":"") . ">到店團購(a11-15%)</option>");
$page->addContent("            <option value='a00'" . (($type=="a00") ? " SELECTED":"") . ">到店團購(a00-15%)</option>");
$page->addContent("            <option value='b11'" . (($type=="b11") ? " SELECTED":"") . ">宅配團購(b11-15%)</option>");
$page->addContent("            <option value='b00'" . (($type=="b00") ? " SELECTED":"") . ">宅配團購(b00-15%)</option>");
$page->addContent("            <option value='c11'" . (($type=="c11") ? " SELECTED":"") . ">到店廉售(c11-5%)</option>");
$page->addContent("            <option value='c10'" . (($type=="c10") ? " SELECTED":"") . ">到店廉售(c10-5%)</option>");
$page->addContent("            <option value='d00'" . (($type=="d00") ? " SELECTED":"") . ">宅配廉售(d00-5%)</option>");
$page->addContent("            <option value='d01'" . (($type=="d01") ? " SELECTED":"") . ">宅配廉售(d01-5%)</option>");
$page->addContent("            <option value='d10'" . (($type=="d10") ? " SELECTED":"") . ">宅配廉售(d10-5%)</option>");
$page->addContent("            <option value='d11'" . (($type=="d11") ? " SELECTED":"") . ">宅配廉售(d11-5%)</option>");
$page->addContent("				</select>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"銀行匯款資料檔\" onClick=\"Download1();\" style='width:90px'>");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"發票處理資料檔\" onClick=\"Download2();\" style='width:90px'>");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("        匯款賣家數目：<span id='counts' style='display: inline-block; width:30px; text-align:left'>" . $counts . "</span>&nbsp;匯款總計：<span id='total' style='display: inline-block; width:60px; text-align:left'>" . $total . "</span>&nbsp;營收利潤總計：<span id='fee' style='display: inline-block; width:50px; text-align:left'>" . $earn . "</span>");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("<TR><TD align='left'>");

$memo = <<<EOD
<div style='text-align:left; font-weight:bold; padding-top:10px'>發票申請注意事項:</div>
<table>
	<tr>
		<td align=left valign=top>注意事項.(1):</td>
		<td>發票申請程序：請先到[匯款&發票處理]做發票申請，再到[優憑簡訊發票處理]做發票申請，方可使同一賣家可以共開一張發票。</td>
	</tr>
	<tr>
		<td align=left valign=top>注意事項.(2):</td>
		<td>必需完成注意事項.(1)之後，方可按下[發票處理資料檔]按鍵產生檔案。</td>
	</tr>
</table>
EOD;

$page->addContent("</TD></TR>");
$page->addContent($list);
$page->addContent("</form></TABLE>");

$page->addContent($memo);
include("../include/db_close.php");
$page->show();
?>
<iframe name="iAction" style="width:100%; height:100px; display:none"></iframe>
<script language="javascript">
	function checkTransfer(){
		var e = $('.transfer')[0].checked;
        $('.transfer').each(function(){
			if(!this.disabled){
				this.checked = e;
			}
        });
//		setTransfer();
	}
	
	function checkReceipt(){
		var e = $('.receipt')[0].checked;
        $('.receipt').each(function(){
			if(!this.disabled){
				this.checked = e;
			}
        });
	}
	
	function setTransfer(x, y, p, f){
		var total =  parseInt($("#total" + y).html(), 10);
		var fee = parseInt($("#fee" +y).html(), 10);
		$.post(
			'seller_export_transfer_log.php',
			{
				no: y,
				t: total,
				f: fee,
				p: p,
				fee:f,
				Y:'<?=$Y?>',
				M:'<?=$M?>'
			},
			function(data)
			{
				x.disabled = true;
			}
		);		
	}
	
	function setReceipt(x){
		var total =  parseInt($("#total" + x).html(), 10);
		$.post(
			'seller_export_receipt_log.php',
			{
				no: x,
				t: total,
				Y:'<?=$Y?>',
				M:'<?=$M?>'
			},
			function(data)
			{
				$("#receipt" + x).html(data);
			}
		);		
	}
	function Search(){
		mForm.submit();
	}
	
	function showReceipt(xNo){
		window.showModalDialog("seller_export_receipt.php?no="+xNo+"&Y=<?=$Y?>&M=<?=$M?>");
	}
	
	function Download1(){
		mForm.target="iAction";
		mForm.action="seller_export_download1.php";
		mForm.submit();
	}
	function Download2(){
		mForm.target="iAction";
		mForm.action="seller_export_download2.php";
		mForm.submit();
	}

</script>