<?php
include './include/session.php';
require_once './class/tools.php';
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
include 'seller_orders_tab.php';

$sql = "SELECT * FROM logReceipt WHERE Seller='" . $_SESSION['member']['No'] . "' AND datediff(Now(), concat( Y,'-', M,'-01')) <= 210 ORDER BY dateCreate DESC";
$result = mysql_query($sql) or die(mysql_error());
while($rs=mysql_fetch_array($result)){
	$receipt[$rs['Y'] . "-" . $rs['M']] = $rs;	
}



$date = date('Y-m-01');
$today = date('Y-m-d');

if($today >=  date('Y-m-05')){
	$curr = date("Y-m", strtotime($date . "+1 month"));
	$month[$curr] = $curr;
}

for($i=6; $i>0; $i--){
	$curr = date("Y-m", strtotime($date . "-" . (6-$i) . " month"));
	$month[$curr] = $curr;
}

$WEB_CONTENT = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
$i=0;
foreach($month as $curr){
	$i++;
	if($i > 6)
		break;

//	$type2 = (($today >= $curr."-05") ? "<img src='./images/checked.gif'>" : "<input type='checkbox' value='1' name='type'>");
//	$title = "";
	$date_create = "";

	if(!$receipt[$curr]){
		$receipt[$curr]['Type'] = 2;
		$receipt[$curr]['Title'] = 0;
	}
	
	$type1 = (($today >= $curr."-05") ? "" : "<input type='checkbox' value='1' name='type' onClick='setType(this);'><input type='hidden' name='curr' value='{$curr}'>");
	$type2 = (($today >= $curr."-05") ? "" : "<input type='checkbox' value='2' name='type' onClick='setType(this);'>");
	$type3 = (($today >= $curr."-05") ? "" : "<input type='checkbox' value='3' name='type' onClick='setType(this);'>");
	$title = (($today >= $curr."-05") ? "" : "<input type='checkbox' value='1' name='title' onClick='setTitle();'>");
	
	switch($receipt[$curr]['Type']){
		case 1:
			$type1 = (($today >= $curr."-05") ? "<img src='./images/checked.gif'>" : "<input type='checkbox' value='1' name='type' checked onClick='setType(this);'><input type='hidden' name='curr' value='{$curr}'>");
			break;
		case 2:
			$type2 = (($today >= $curr."-05") ? "<img src='./images/checked.gif'>" : "<input type='checkbox' value='2' name='type' checked onClick='setType(this);'>");
			break;
		case 3:
			$type3 = (($today >= $curr."-05") ? "<img src='./images/checked.gif'>" : "<input type='checkbox' value='3' name='type' checked onClick='setType(this);'>");
			break;
	}

	if($receipt[$curr]['Title'] == 1){
		$title = (($today >= $curr."-05") ? "<img src='./images/checked.gif'>" : "<input type='checkbox' value='1' name='title' checked onClick='setTitle();'>");
	}
	$date_create = substr($receipt[$curr]['dateCreate'], 0, 10);
	if($date_create == "0000-00-00"){$date_create="";}
	$WEB_CONTENT .= <<<EOD
	<tr>
		<td style="padding-bottom:20px">
			<table style="width:100%; background:#606060; border:solid 2px #606060" cellpadding="1" cellspacing="1">
				<tr>
					<td style="color:white; text-align:center; background:#909090;" rowspan="4">{$curr}月初<br>發票開立<br>設定</td>
					<td style="color:white; text-align:center; background:#909090;" colspan="3">發票類型</td>
					<td style="color:white; text-align:center; background:#909090;" rowspan="2">加上抬頭<br>與統編</td>
					<td style="color:white; text-align:center; background:#909090;" rowspan="2">發票開立日期<br>&電子發票</td>
				</tr>
				<tr>
					<td style="color:white; text-align:center; background:#909090;">捐贈慈善</td>
					<td style="color:white; text-align:center; background:#909090">紙本寄送</td>
					<td style="color:white; text-align:center; background:#909090;">電子發票</td>
				</tr>
				<tr>
					<td style="color:white; text-align:center; background:#ffffff;" rowspan='2'>{$type1}</td>
					<td style="color:white; text-align:center; background:#ffffff;" rowspan='2'>{$type2}</td>
					<td style="color:white; text-align:center; background:#ffffff;" rowspan='2'>{$type3}</td>
					<td style="color:white; text-align:center; background:#ffffff;" rowspan='2'>{$title}</td>
					<td style="color:black; text-align:center; background:#ffffff; height:20px">{$date_create}</td>
				</tr>
				<tr>
					<td style="color:black; text-align:center; background:#ffffff; height:20px"><a href="javascript:parent.Dialog('seller_receipt2_detail.php?id={$receipt[$curr]['ID']}')">{$receipt[$curr]['ID']}</a></td>
				</tr>
			</table>
		</td>
	</tr>
EOD;
}
$WEB_CONTENT .= "</table>";


$result = mysql_query("SELECT * FROM logReceiptSMS WHERE Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
while($rs=mysql_fetch_array($result)){
	$receipts .= substr($rs['dateCreate'], 0, 10) . "&nbsp;優惠憑證發票開立金額：<font color=blue>" . "$ " . "{$rs['Amount']}</font>&nbsp;&nbsp;&nbsp;&nbsp;發票號碼：<a href=\"javascript:parent.Dialog('seller_receipt2_detail.php?id={$rs['ID']}&coupon=1')\">{$rs['ID']}</a><br>";

}
include './include/db_close.php';


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
					<table>
						<Tr>
							<Td valign="top" nowrap style="color:gray">[註]：</td>
							<Td valign="top" style="color:gray">
								<table>
									<tr>
										<td style="color:gray" nowrap valign="top">(1)</td>
										<td style="color:gray">發票內容之 [買受人抬頭] 以及 [統一編號], 係參考「賣家資訊中心-我的賣家資訊」所填寫內容, 請自行前往檢查資料是否有誤, 或是需要變更。</td>
									</tr>
									<tr>
										<td style="color:gray" nowrap valign="top">(2)</td>
										<td style="color:gray">本站匯款給賣家前, 賣家須完成發票類型設定, 否則一律設定為紙本發票, 紙本發票寄送須扣除平信郵寄費用, 一旦本站每月月初發票開立完成, 賣家不得於事後要求變更。</td>
									</tr>
									<tr>
										<td style="color:gray" nowrap valign="top">(3)</td>
										<td style="color:gray">電子發票將不直接寄送, 如所開立的電子發票中獎, 本站將以掛號寄送紙本發票給商家 , 郵資費用將自賣家儲值金扣除 (故建議賣家需保留足額之儲值金, 否則須另外匯款)。</td>
									</tr>
									<tr>
										<td style="color:gray" nowrap valign="top">(4)</td>
										<td style="color:gray">發票開立日期之後，透過郵局寄送約 2-7 個工作天內送達，若郵寄日逾 10 天仍未收到，請來信申請補發。</td>
									</tr>
									<tr>
										<td style="color:gray" nowrap valign="top">(5)</td>
										<td style="color:gray">選擇”捐贈慈善”的發票將遞送給 [財團法人創世社會福利基金會]。</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style='text-align:left'><br>{$receipts}<br>
				</td>
			</tr>
			<tr>
				<td align="left">
					<table>
						<Tr>
							<td>商品發票處理選項</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="left">
				<form name="iForm" action="seller_receipt2_save.php" method="post" target="iAction">
				{$WEB_CONTENT}
				</form>
				</td>
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
function setTitle(){
	iForm.submit();
}

function setType(x){
	for(var i=0; i<iForm.type.length; i++){
		if(iForm.type[i] == x){
			iForm.type[i].checked=true;
		}
		else{
			iForm.type[i].checked=false;
		}
	}
	iForm.submit();
}

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

function Reserve(xNo, xItem){
	var k = $("#k" + xNo + "_" + xItem).val();
	var r = $("#r" + xNo + "_" + xItem).val();
	var i = $("#i" + xNo + "_" + xItem).val();
	if(i != ""){
		$.post(
			'seller_orders_verify.php',
			{
				k: k,
				r: r,
				i: i,
				s: xItem
			},
			function(data)
			{
				eval("var response = " + data);
				if(response.verify == "1"){
					$("#v" + xNo + "_" + xItem).html("憑證確認</font>");
					$("#s" + xNo + "_" + xItem).html("已完成");
				}
				if(response.verify == "2"){
					$("#v" + xNo + "_" + xItem).html("<font style='color:red'>憑證錯誤</font></font>");
				}
//				window.location.reload();
			}
		);	
	}
}
</script>

