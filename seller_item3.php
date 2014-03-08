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
			<td style="display:none;width:20px; line-height:30px; background:#b5b2b5;text-align:center"><input type='checkbox' name='p' value="" onClick="checkAll();"></td>
			<td style="width:120px; line-height:30px; background:#b5b2b5;text-align:center">前台顯示優先權</td>
			<td style="display:none;width:100px;; line-height:30px; background:#b5b2b5;text-align:center">優惠憑證設置</td>
			<td style="; line-height:30px; background:#b5b2b5;text-align:center">商品/活動名稱</td>
			<td style="width:60px;; line-height:30px; background:#b5b2b5;text-align:center">排序</td>
		</tr>

EOD;
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}
$type = 4;
$sql = "SELECT * FROM Product WHERE Status IN(2, 6) AND dateClose >= CURRENT_TIMESTAMP AND Member='" . $_SESSION['member']['No'] . "'";
switch($type){
	case 1:
		$deliver = "";
		$sql .= " AND Mode = 1 AND Deliver = 0";
		break;
	case 2:
		$deliver = ";display:none";
		$sql .= " AND Mode = 1 AND Deliver = 1";
		break;
	case 3:
		$deliver = "";
		$sql .= " AND Mode = 2 AND Deliver = 0";
		break;
	case 4:
		$deliver = ";display:none";
		$sql .= " AND Mode = 2 AND Deliver = 1";
		break;
}




$sql .= " ORDER BY Sort";


$result = mysql_query($sql) or die(mysql_error());


$num = mysql_num_rows($result);
$i=0;
$item = "";
while($rs=mysql_fetch_array($result)){   
	$i++;
	$item .= $rs['No'].",";
	$date1 = date("Y-m-d", strtotime($rs['dateApprove']));
	$date2 = date("m-d", strtotime($rs['dateClose']));

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
	$url = "product{$type1}_detail.php?no={$rs['No']}";
	$img = "";
	$chk = "<input type='checkbox' name='p' value=\"{$rs['No']}\">";
	if($rs['Activity'] == 1){
		$chk = "<img src='./images/deny.png' title='不可操作'>";
		$cashtype = "商品粉絲活動";
		$date2="依條件自動";
	}
	else if($rs['Cashflow'] == 1){
		$chk = "<img src='./images/deny.png' title='不可操作'>";
		$cashtype = "金流商品";
		$date2="依條件自動";
	}
	else{
		$cashtype = "非金流商品";
		if($rs['coupon_YN'] == 1){
		//	$img = "<img src='./images/email_icon.gif'>";
		}
	}
	$coupon = (($rs['coupon_YN'] == 1) ? "已設置":"");
	$btn = <<<EOD
				<img src="./images/moveup.gif" style="cursor:pointer" onClick="Move({$rs['No']}, -1);">
				<img src="./images/movedown.gif" style="cursor:pointer" onClick="Move({$rs['No']}, +1);">
EOD;
	if($i==1){
	$btn = <<<EOD
				<img src="./images/movedown.gif" style="cursor:pointer" onClick="Move({$rs['No']}, +1);">
EOD;
	}
	if($i==$num){
	$btn = <<<EOD
				<img src="./images/moveup.gif" style="cursor:pointer" onClick="Move({$rs['No']}, -1);">
EOD;
	}
	$items .=<<<EOD
		<tr>
			<td style="display:none">{$chk}</td>
			<td style="text-align:center;">{$rs['Sort']}</td>
			<td style="text-align:center;display:none;">{$coupon}</td>
			<td style="text-align:left">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td><a href="$url" target="_blank">{$rs['Name']}</a>&nbsp;[{$cashtype}]</td>
						<td>{$img}</td>
					</tr>
				</table>
			</td>
			<td style="text-align:center;">{$btn}
			</td>
		</tr>
EOD;
}





if(strlen($item) > 0){
	$item = substr($item, 0, strlen($item)-1);
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
	<tr style="display:none">
		<td style="border-bottom:solid 1px gray; height:40px">{$t}</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center">
		<center>
		<form name="iForm" method="post" target="iAction">
		<input type="hidden" name="item" value="{$item}">
		<input type="hidden" name="product">
		<input type="hidden" name="diff">
		<input type="hidden" name="type" value="{$_REQUEST['type']}">
		<table width="100%" border=0>
			<tr>
				<td style="text-align:left; padding:5px">
					<Table align="left" border=0>
						<tr>
							<td valign="top" width=40 style="color:gray">[註]：</td>
							<td valign="top" align="left" style="color:gray">依據[類型][分類]搜尋之結果，賣家商品顯示於前台表列，乃依據以下優先權設定順序。</td>
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
function Move(x, diff){
    var tStr = "";
    for (var i=1; i<iForm.p.length; i++){
        tStr += iForm.p[i].value + ",";
    }//for
	if (tStr.length > 0){
		tStr = tStr.substring(0, tStr.length - 1);
	}//if
	//iForm.item.value = tStr;
	iForm.product.value = x;
	iForm.diff.value = diff;
	iForm.action = "seller_item3_move.php";
	iForm.submit();
}
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

function setCoupon(){
	iForm.item.value = getList(iForm.p);
	if(iForm.item.value.length >0){
		var items = iForm.item.value.split(",");
		if(items.length == 1){
			parent.Dialog("seller_item_coupon.php?product=" + iForm.item.value);
		}
		else{
			alert("只能選擇一個商品進行憑證設定/查詢!");
		}
	}
	else{
		alert("請先選取!");
	}
}

function Reset(){
	iForm.item.value = getList(iForm.p);
	if(iForm.item.value.length >0){
		var items = iForm.item.value.split(",");
		if(items.length == 1){
			if(confirm("確定要將憑證重置?")){
				iForm.action = "seller_item_reset.php";
				iForm.submit();
			}
		}
		else{
			alert("只能選擇一個商品進行憑證重置!");
		}
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

function Close(){
	parent.$.fn.colorbox.close();
}
</script>
