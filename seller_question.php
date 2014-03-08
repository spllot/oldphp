<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

$types = array("", "到店團購", "宅配團購", "到店廉售", "宅配廉售");
$type = (($_REQUEST['type'] != "") ? $_REQUEST['type'] : 3);
$t = "<table style='width:100%'><tr>";
for($i=1; $i<sizeof($types); $i++){
	if($type == "") $type = $i;
	$t .= "<td style='text-align:center; width:25%'><input type='radio' name='type' value='" . $i . "'" . (($i==$type) ? " CHECKED" : "") . " onClick=\"window.location.href='seller_item.php?type=" . $i . "';\">" . $types[$i] . "</td>";

}
$t .= "</tr></table>";

$items = <<<EOD
	<table width="100%">
		<tr>
			<td style="width:20px; line-height:30px; background:#b5b2b5;text-align:center; display:none"><input type='checkbox' name='p' value="" onClick="checkAll();"></td>
			<td style="width:100px; line-height:30px; background:#b5b2b5;text-align:center">日期</td>
			<td style="width:200px; line-height:30px; background:#b5b2b5;text-align:center">商品&服務名稱</td>
			<td style="; line-height:30px; background:#b5b2b5;text-align:center">詢問內容</td>
			<td style="width:100px; line-height:30px; background:#b5b2b5;text-align:center">回覆日期</td>
			<td style="width:40px; line-height:30px; background:#b5b2b5;text-align:center">回覆</td>
		</tr>

EOD;
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}

$sql = "SELECT logComment.*, Product.Deliver, Product.Name, Product.Mode, Product.Deliver, Member.Nick, Member.userID FROM logComment INNER JOIN Product ON Product.No = logComment.transactionNo INNER JOIN Member ON Member.No=logComment.rateBy  WHERE Owner='" . $_SESSION['member']['No'] . "' AND Question = 1 AND Product.dateClose >= CURRENT_TIMESTAMP ORDER BY dateRated DESC";


$result = mysql_query($sql) or die(mysql_error());



while($rs=mysql_fetch_array($result)){   
	$date1 = substr($rs['dateRated'], 0, 10);
	$date2 = (($rs['dateReplied']=="0000-00-00 00:00:00") ? "尚未" : substr($rs['dateReplied'], 0, 10));

	if($rs['Mode'] == 1){
		if($rs['Deliver'] == 0){
			$type1=1;
		}
		if($rs['Deliver'] == 1){
			$type1=2;
		}
	}
	if($rs['Mode'] == 2){
		if($rs['Deliver'] == 0){
			$type1=4;
		}
		if($rs['Deliver'] == 1){
			$type1=5;
		}
	}
	$url = "product{$type1}_detail.php?no={$rs['transactionNo']}";

	$chk = "<input type='checkbox' name='p' value=\"{$rs['No']}\">";
	$items .=<<<EOD
		<tr>
			<td style="; display:none">{$chk}</td>
			<td style="text-align:center; color:blue">$date1</td>
			<td style="text-align:left">【<a href="$url" target="_blank">{$rs['Name']}</a>】</td>
			<td style="text-align:left">暱稱：{$rs['Nick']}<br>電子郵件：{$rs['userID']}<br>問題：{$rs['Content']}</td>
			<td style="text-align:center">{$date2}</td>
			<td style="text-align:center"><a href="javascript:parent.Dialog('seller_question_reply.php?no={$rs['No']}');"><img src="./images/edit.gif" border="0"></a></td>
		</tr>
EOD;
}









include './include/db_close.php';
$items .= "</table>";


include 'seller_data_tab.php';





$WEB_CONTENT = <<<EOD
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center">
		<center>
		<form name="iForm" method="post" target="iAction">
		<input type="hidden" name="item">
		<input type="hidden" name="type" value="{$_REQUEST['type']}">
		<table width="100%">
			<tr>
				<td style="text-align:left; padding:5px">
					<Table align="left" border=0>
						<tr>
							<td valign="top" width=40 style="color:gray">[註]：</td>
							<td valign="top" align="left" style="color:gray">[商品＆服務詢問回覆]係來自商品或服務說明頁面下方的[詢問商家問題]之處，商家需在此回覆買家詢問的問題。</td>
						</tR>
					</table>				
				</td>
			</tr>
			<tr>
				<td colspan="2">{$items}</td>
			</tr>
		</table>
		</form>
		</center>
	

		</td>
	</tr>
</table>


EOD;
include 'template2.php';
?>

<script language="javascript">
var iForm = document.iForm;
function checkAll(){
	for(var i=1; i<iForm.p.length; i++){
		iForm.p[i].checked = iForm.p[0].checked;
	}
}

function getList(xField){
    var tStr = "";
    for (var i=1; i<xField.length; i++){
        if (xField[i].checked){
            tStr += xField[i].value + ",";
        }//if
    }//for
	if (tStr.length > 0){
		tStr = tStr.substring(0, tStr.length - 1);
	}//if
    return tStr;
}//getList

function setClose(){
	iForm.item.value = getList(iForm.p);
	if(iForm.item.value.length >0){
		iForm.action = "seller_item_close.php";
		iForm.submit();
	}
	else{
		alert("請先選取!");
	}
}

function Extend(){
	iForm.item.value = getList(iForm.p);
	if(iForm.item.value.length >0){
		iForm.action = "seller_item_extend.php";
		iForm.submit();
	}
	else{
		alert("請先選取!");
	}
}

</script>
