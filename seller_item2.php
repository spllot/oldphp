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
			<td style="width:20px; line-height:30px; background:#b5b2b5;text-align:center"><input type='checkbox' name='p' value="" onClick="checkAll();"></td>
			<td style="width:120px; line-height:30px; background:#b5b2b5;text-align:center">前台列表優先權</td>
			<td style="width:100px;; line-height:30px; background:#b5b2b5;text-align:center">優惠訊息設置</td>
			<td style="; line-height:30px; background:#b5b2b5;text-align:center">商品&服務名稱</td>
			<td style="width:100px;; line-height:30px; background:#b5b2b5;text-align:center">服務代碼</td>
			<td style="width:60px;; line-height:30px; background:#b5b2b5;text-align:center">排序</td>
		</tr>

EOD;
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}

$type = 3;
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

	if($rs['hr'] == 1){
		if($rs['employer'] == 1){
			$cashtype="人力徵求";
		}
		else{
			$cashtype="人力待徵";
		}
	}

	if($rs['Transport'] == 1){
		$cashtype="運輸";
	}
	if($rs['event'] == 1){
		$cashtype="活動";
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
	$code = $rs['Member'] . str_pad($rs['No'], 5, "0", STR_PAD_LEFT);
	$items .=<<<EOD
		<tr>
			<td>{$chk}</td>
			<td style="text-align:center;">{$rs['Sort']}</td>
			<td style="text-align:center;">{$coupon}</td>
			<td style="text-align:left">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td><a href="$url" target="_blank">{$rs['Name']}</a>&nbsp;[{$cashtype}]</td>
						<td>{$img}</td>
					</tr>
				</table>
			</td>
			<td style="text-align:center;">{$code}</td>
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
							<td valign="top" width=20 style="color:gray">(1).</td>
							<td valign="top" align="left" style="color:gray">前台列表優先權設定:「本地服務」依據[分類]選項之搜尋結果，每一商家之同類商品/服務只能陳列一個於前台列表，所以商家需預先設定其商品/服務優先權，以呈現於買家所搜尋之列表之中。</td>
						</tR>
						<tr style="{$deliver}">
							<td valign="top" width=40 style="color:gray">　　</td>
							<td valign="top" width=20 style="color:gray">(2).</td>
							<td valign="top" align="left" style="color:gray">點選<a href="{$_CONFIG['urlE']}" target="_blank">優惠訊息(簡訊/郵件)&前台列表優先權設定說明</a>，可以了解其使用方法與功能。</td>
						</tR>
					</table>				
				</td>
				<td style="text-align:right; vertical-align:bottom" width="200">
					<table align="right" border=0>
						<tr>
							<td></td>
							<td>&nbsp;&nbsp;</td>
							<td></td>
						</tr>
						<tr style="{$deliver}">
							<td><input type="button" value="優訊設定/查詢" onClick="setCoupon();"></td>
							<td>&nbsp;&nbsp;</td>
							<td><input type="button" value="優訊重置" onClick="Reset();"></td>
						</tr>
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
	iForm.action = "seller_item2_move.php";
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
			if(confirm("確定要將優惠訊息重置?")){
				iForm.action = "seller_item_reset.php";
				iForm.submit();
			}
		}
		else{
			alert("只能選擇一個商品進行惠訊息重置!");
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
