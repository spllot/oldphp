<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
else if($_SESSION['member']['Seller'] != 2){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("申請成為賣家後; 需做登出動作; 然後才可正常使用[我是賣家]功能!");
	JavaScript::Redirect("./member_form.php");
	exit;
}
include './include/db_open.php';
include 'seller_logs_tab.php';

$deliver=1;
$product = $_REQUEST['product'];
$status = $_REQUEST['status'];

$sql = "SELECT DISTINCT Product.No, Product.Name FROM Product INNER JOIN Orders ON Orders.Product = Product.No WHERE Product.Deliver=$deliver AND Orders.Seller='" . $_SESSION['member']['No'] . "' ORDER BY Name";
$result = mysql_query($sql) or die(mysql_error());
while($rs=mysql_fetch_array($result)){
	if($product == ""){
//		$product = $rs['No'];
	}
	$product_list .= "<option value='{$rs['No']}'" . (($product == $rs['No'])?" SELECTED":"") . ">{$rs['Name']}</option>";
}




$counts_t = 0;//全部
$counts_c = 0;//已取消
$counts_r = 0;//已退款
$counts_d = 0;//已完成
$sql = "SELECT ifnull(SUM(Amount), 0) FROM Orders WHERE Orders.Deliver=$deliver AND Orders.Seller = '" . $_SESSION['member']['No'] . "'" . (($product != "") ? " AND Orders.Product='$product'" : "");
$result = mysql_query($sql) or die(mysql_error());
if($rs = mysql_fetch_array($result))
	$counts_t = $rs[0];

$sql = "SELECT ifnull(SUM(Amount), 0) FROM Orders WHERE Orders.Deliver=$deliver AND Orders.Seller = '" . $_SESSION['member']['No'] . "' AND Status=3" . (($product != "") ? " AND Orders.Product='$product'" : "");
$result = mysql_query($sql) or die(mysql_error());
if($rs = mysql_fetch_array($result))
	$counts_c = $rs[0];

$sql = "SELECT ifnull(SUM(Items.Amount), 0) FROM Orders INNER JOIN Items ON Items.orderID=Orders.ID WHERE Orders.Deliver=$deliver AND Orders.Seller = '" . $_SESSION['member']['No'] . "' AND Items.Refund = 1 AND Items.dateReturn <> '0000-00-00 00:00:00'" . (($product != "") ? " AND Orders.Product='$product'" : "");
$result = mysql_query($sql) or die(mysql_error());
if($rs = mysql_fetch_array($result))
	$counts_r = $rs[0];

$sql = "SELECT ifnull(SUM(Items.Amount), 0) FROM Orders INNER JOIN Items ON Items.orderID=Orders.ID WHERE Orders.Deliver=$deliver AND Orders.Seller = '" . $_SESSION['member']['No'] . "' AND Orders.Status = 1 AND dateShipped<>'0000-00-00 00:00:00' AND datediff(Now(), dateShipped) > 10 AND Items.Refund = 0" . (($product != "") ? " AND Orders.Product='$product'" : "");
$result = mysql_query($sql) or die(mysql_error());
if($rs = mysql_fetch_array($result))
	$counts_d = $rs[0];




$sql = "SELECT *, IFNULL((SELECT SUM(Amount) FROM Refund WHERE orderID=Orders.ID), 0) AS Refund, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), '0') AS A1, IFNULL((SELECT payBy FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), 0) AS P1, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 0) AS A2, IFNULL((SELECT payBy FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 4) AS P2 FROM Orders WHERE Seller = '" . $_SESSION['member']['No'] . "' AND Deliver=$deliver AND Product='$product'";

$sql = "SELECT Orders.*, Items.No AS itemNo, Items.Amount AS Amount2, Items.Refund, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), '0') AS A1, IFNULL((SELECT payBy FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), 0) AS P1, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 0) AS A2, IFNULL((SELECT payBy FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 4) AS P2, Items.Transfer, Items.Expire FROM Orders INNER JOIN Items ON Orders.ID=Items.orderID WHERE Seller = '" . $_SESSION['member']['No'] . "' AND Deliver=$deliver AND Items.Amount > 0";

$sql .= (($product != "") ? " AND Orders.Product='$product'" : "");

switch($status){
	case 1://待付款
		$sql .= " AND Orders.Status = 0";
		break;
	case 2://待發貨
		$sql .= " AND Orders.Status = 1 AND dateShipped='0000-00-00 00:00:00' AND Items.Refund = 0";
		break;
	case 3://待鑑賞
		$sql .= " AND Orders.Status = 1 AND dateShipped<>'0000-00-00 00:00:00' AND datediff(Now(), dateShipped) <= 10 AND Items.Refund = 0 ";
		break;
	case 4://待消費
		$sql .= " AND Orders.Status = 1 AND ID IN (SELECT orderID FROM logCertify WHERE dateUse = '0000-00-00 00:00:00') AND dateShipped<>'0000-00-00 00:00:00' AND datediff(Now(), dateShipped) > 7 AND Items.Refund = 0";
		break;
	case 5://已完成
//		$sql .= " AND Orders.Status = 1 AND ID IN (SELECT orderID FROM logCertify WHERE dateUse <> '0000-00-00 00:00:00') AND Items.Refund = 0";
		$sql .= " AND Orders.Status = 1 AND dateShipped<>'0000-00-00 00:00:00' AND datediff(Now(), dateShipped) > 10 AND Items.Refund = 0 ";
		break;
	case 6://退訂中
		$sql .= " AND Items.Refund = 1 AND Items.dateReturn = '0000-00-00 00:00:00'";
		break;
	case 7://已退款
		$sql .= " AND Items.Refund = 1 AND Items.dateReturn <> '0000-00-00 00:00:00'";
		break;
	case 8://已取消
		$sql .= " AND Orders.Status = 3";
		break;
}
$sql .= " ORDER BY dateSubmited DESC, Items.Sort";

$result = mysql_query($sql) or die(mysql_error());
$num = mysql_num_rows($result);
$pagesize  = 10;
$pages = ceil($num / $pagesize);
$pageno = $_REQUEST['pageno'];
if($pageno == "" || $pageno > $pages){$pageno = 1;}
$payby=array(
	"無",
	"信用卡(Visa)",
	"Web ATM",
	"ATM轉帳",
	"儲值金"
);
$status_list = "<option value=''>所有訂單</option>";
$status_list .= "<option value='1'" . (($status=="1")?" SELECTED":"") . ">待付款</option>";
if($deliver=="1")
	$status_list .= "<option value='2'" . (($status=="2")?" SELECTED":"") . ">待發貨</option>";
$status_list .= "<option value='3'" . (($status=="3")?" SELECTED":"") . ">待鑑賞</option>";
if($deliver=="0")
	$status_list .= "<option value='4'" . (($status=="4")?" SELECTED":"") . ">待消費</option>";
$status_list .= "<option value='5'" . (($status=="5")?" SELECTED":"") . ">已完成</option>";
$status_list .= "<option value='6'" . (($status=="6")?" SELECTED":"") . ">退訂中</option>";
$status_list .= "<option value='7'" . (($status=="7")?" SELECTED":"") . ">已退款</option>";
$status_list .= "<option value='8'" . (($status=="8")?" SELECTED":"") . ">已取消</option>";


$WEB_CONTENT = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
$now = date('Y-m-d H:i:s');
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($data = mysql_fetch_array($result)){
			$left = $data['Amount'] - $data['Refund'];
			include 'orders_status.php';
			$WEB_CONTENT .= <<<EOD
				<tr>
					<td style="padding-bottom:20px">
						<table style="width:100%; background:#606060; border:solid 2px #606060" cellpadding="1" cellspacing="1">
							<tr>
								<td style="color:white; text-align:center; background:#909090; width:150px">訂單編號</td>
								<td style="color:white; text-align:center; background:#909090">內容</td>
								<td style="color:white; text-align:center; background:#909090; width:100px">數量</td>
								<td style="color:white; text-align:center; background:#909090; width:100px">訂單狀態</td>
								<td style="color:white; text-align:center; background:#909090; width:100px">客服</td>
							</tr>
							<tr>
								<td rowspan="2" style="background:white; text-align:center"><a href="javascript:parent.Dialog('seller_logs_buyer2.php?id={$data['ID']}');">{$data['ID']}</a></td>
								<td rowspan="2" style="background:white; text-align:left; padding:2px">{$data['pName']}</td>
								<td rowspan="2" style="background:white; text-align:center">{$data['Amount2']}</td>
								<td rowspan="2" style="background:white; text-align:center">{$status}</td>
								<td rowspan="2" style="background:white; text-align:center">{$service1}</td>
							</tr>
						</table>
					</td>
				</tr>
EOD;
		}
		else{
			break;
		}
	}
	$WEB_CONTENT .= "<tr>";
	$WEB_CONTENT .= "	<td style='padding: 10px;'>";
	$WEB_CONTENT .= "		<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">";
    $WEB_CONTENT .= "           <tr>";
	if($pageno > 1){
		$WEB_CONTENT .= "             <td style='width:74px; height:25px; text-align:center; background-image:url(./images/btn_100_black.jpg1); background-repeat:no-repeat; background-position: center center'>";
		$WEB_CONTENT .= "				<a href=\"javascript:" . (($pageno > 1) ? "setPage(" . ($pageno - 1) . ")" : "void(0)"). ";\" style='text-decoration:underline'>上一頁</a>";
	}
	else{
		$WEB_CONTENT .= "             <td style='width:74px; height:25px; text-align:left; padding-left:10px;'>&nbsp;";
	}
	$WEB_CONTENT .= "			  </td>";
    $WEB_CONTENT .= "             <td align=\"center\" nowrap><table><tr>";
	for($i=0; $i<$pages; $i++){
		$p = "<div style='width:18px; height:18px; border:solid 0px black; line-height:18px'>" . ($i+1) . "</div>";
		if(($i+1)==$pageno){
			$WEB_CONTENT .= "<td style='text-decoration:underline; width:20px; text-align:center'>" . $p . "</td>";		
		}
		else{
			$WEB_CONTENT .= "<td onClick=\"javascript:setPage(" . ($i+1) . ");\" style='cursor:pointer; text-decoration:none; width:20px; text-align:center'>" . $p . "</td>";		
		}
	}
	$WEB_CONTENT .= "			</tr></table></td>";
	if($pageno < $pages){
		$WEB_CONTENT .= "			<td style='width:74px; height:25px; text-align:center; background-image:url(./images/btn_100_black.jpg1); background-repeat:no-repeat; background-position: center center'>";
		$WEB_CONTENT .= "				<a href=\"javascript:" . (($pageno < $pages) ? "setPage(" . ($pageno + 1) . ")" : "void(0)") . ";\" style='text-decoration:underline'>下一頁</a>";
	}
	else{
		$WEB_CONTENT .= "             <td style='width:74px; height:25px; text-align:left; padding-left:10px;'>&nbsp;";
	}
	$WEB_CONTENT .= "			</td>";
	$WEB_CONTENT .= "			</tr>";
	$WEB_CONTENT .= "		</table>";
	$WEB_CONTENT .= "	</td>";
	$WEB_CONTENT .= "</tr>";
}
else{
	$WEB_CONTENT .= <<<EOD
		<tr>
			<td style="color:gray; text-align:center; border:solid 1px gray; background:#eeeeee; padding:5px">查無訂單
			</td>
		</tr>
EOD;
}
$WEB_CONTENT .= "</table>";






include './include/db_close.php';

$tab_name = (($deliver==0) ? "到店消費" : "宅配");


$memo = "待收款&#8594;等待客戶匯款; 待鑑賞&#8594;匯款完成，等待商品評鑑期結束; 待消費&#8594;等待客戶使用到店憑證;已完成&#8594;確立商品完成交易程序; 退訂中&#8594;商品評鑑期七日內退訂進行中; 已退款&#8594;已完成退貨退款程序";

if($deliver == "1"){
	$memo = "待收款&#8594;等待客戶匯款; 待發貨&#8594;等待宅配商品發貨; 待鑑賞&#8594;等待商品評鑑期結束(宅配商品預計送貨3日抵達, 故由出貨日起算十日內為”待鑑賞”期間); 已完成&#8594;確立商品完成交易程序; 退訂中&#8594;商品評鑑期內退訂進行中; 已退款&#8594;已完成退貨退款程序; 已取消&#8594;訂單成立無效";
}

 


$WEB_CONTENT = <<<EOD
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center">
<br><Br>

		<center>
		<table border=0 width="700">
			<tr>
				<td align="left">
					<form name="sForm">
					<input type="hidden" name="deliver" value="$deliver">
					<input type="hidden" name="pageno" value="$pageno">
					<table style="width:100%" cellpadding="0" cellspacing="0" border="0">
						<Tr>
							<td style="text-align:left; width:50%">
								<table>
									<Tr>
										<Td>品名資訊</td>
										<Td>
											<select name="product"><option value=''>所有品名</option>{$product_list}</select>
										</td>
									</tr>
								</table>
							</td>
							<td style="text-align:right; width:50%">
								<table align="right">
									<Tr>
										<Td>訂單狀態</td>
										<Td>
											<select name="status">{$status_list}</select>
										</td>
										<Td><input type="button" value="查詢" onClick="Search();"></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
			<tr>
				<td align="left">
					<table>
						<Tr>
							<Td valign="top" nowrap style="color:gray">[說明]：</td>
							<Td valign="top" style="color:gray">{$memo}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="left">
					<table>
						<Tr>
							<td>{$tab_name}商品交易記錄<span style="color:gray; font-size:10pt"> ({$counts_t}</font>商品數處理，{$counts_c}</font>已取消，{$counts_r}</font>已退款，{$counts_d}</font>已完成)</span></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="left">{$WEB_CONTENT}</td>
			</tr>
			<tr style="height:20px"><td>&nbsp;</td></tr>
		</table>

		
		</center>
	

		</td>
	</tr>
</table>

<br>
<br>
<br>
EOD;

include 'template2.php';
?>


<script language="javascript">
function Search(){
	sForm.submit();
}
function setPage(xNo){
	var sForm = document.sForm;
	sForm.pageno.value = xNo;
	sForm.submit();
}
function Close(){
	parent.$.fn.colorbox.close();
}
</script>