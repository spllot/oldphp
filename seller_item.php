<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

$types = array("", "本地團購", "宅配團購", "本地服務", "宅配服務");
$type = (($_REQUEST['type'] != "") ? $_REQUEST['type'] : 3);



$t = "<table style='width:100%'><tr>";
for($i=1; $i<sizeof($types); $i++){
	if($type == "") $type = $i;
	$t .= "<td style='text-align:center; width:25%'><input type='radio' name='type' value='" . $i . "'" . (($i==$type) ? " CHECKED" : "") . " onClick=\"window.location.href='seller_item.php?type=" . $i . "';\">" . $types[$i] . "</td>";

}
$t .= "</tr></table>";
$display = (($type==2 || $type==4) ? "none" : "1");

$items = <<<EOD
	<table width="100%">
		<tr>
			<td style="width:20px; line-height:30px; background:#b5b2b5;text-align:center"><input type='checkbox' name='p' value="" onClick="checkAll();"></td>
			<td style="width:110px; line-height:30px; background:#b5b2b5;text-align:center; display:{$display}">服務位罝設定</td>
			<td style="width:140px; line-height:30px; background:#b5b2b5;text-align:center">提案/下架日期</td>
			<td style="; line-height:30px; background:#b5b2b5;text-align:center">商品&服務名稱</td>
			<td style="; line-height:30px; background:#b5b2b5;text-align:center">分類</td>
		</tr>

EOD;
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}








$today = date('Y-m-d');
$sql = "SELECT *, DATEDIFF(dateClose,'$today') AS days FROM Product WHERE Status IN(2, 6) AND dateClose >= CURRENT_TIMESTAMP AND Member='" . $_SESSION['member']['No'] . "'";
switch($type){
	case 1:
		$deliver = ";display:block";
		$sql .= " AND Mode = 1 AND Deliver = 0";
		break;
	case 2:
		$deliver = ";display:none";
		$sql .= " AND Mode = 1 AND Deliver = 1";
		break;
	case 3:
		$deliver = ";display:block";
		$sql .= " AND Mode = 2 AND Deliver = 0";
		break;
	case 4:
		$deliver = ";display:none";
		$sql .= " AND Mode = 2 AND Deliver = 1";
		break;
}




$sql .= " ORDER BY dateClose DESC";


$result = mysql_query($sql) or die(mysql_error());


while($rs=mysql_fetch_array($result)){   
	$date1 = date("Y-m-d", strtotime($rs['dateApprove']));
	$date2 = date("m-d", strtotime($rs['dateClose'])) . "下架";

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

	if($rs['Activity'] == 1){
		$chk = "<img src='./images/deny.png' title='不可操作'>";
		$cashtype = "抽獎";
		$date2="依條件自動下架";
	}
	else if($rs['Cashflow'] == 1){
		$chk = "<img src='./images/deny.png' title='不可操作'>";
		$cashtype = "金流商品";
		$date2="依條件自動下架";
	}
	else{
		if($rs['days'] <= 14){
		}
		else{
			$chk = "<img src='./images/deny.png' title='下架前兩周才可延長上架'>";
		}
		$chk = "<input type='checkbox' name='p' value=\"{$rs['No']}\">";
		$cashtype = "非金流商品";
		if($rs['coupon_YN'] == 1){
			//$img = "<img src='./images/email_icon.gif'>";
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

	
	
	if($rs['dateClose'] == '9999-12-31 23:59:59') $date2 = "暫無下架日期";

	$usefor = "TYPE_PRO";
	if($rs['Transport']==1){
		$usefor = "TYPE_TPT";
	}
	if($rs['hr']==1){
		$usefor = "TYPE_JOB";
	}
	if($rs['event']==1){
		$usefor = "TYPE_ACT";
	}


	$result1 = mysql_query("SELECT * FROM Catalog WHERE useFor='$usefor' AND Parent=0 ORDER BY Sort");
	$catalog_list = "";
		while($rs1=mysql_fetch_array($result1)){
		$catalog_list .= "<option value='" . $rs1['No'] . "'" . (($rs['catalog'] == $rs1["No"] ) ? " SELECTED" : "") . ">" . $rs1["Name"] . "</option>";
	}


	$catalog_list2 = "";
	if($rs['Catalog'] > 0){
		$result1 = mysql_query("SELECT * FROM Catalog WHERE useFor='$usefor' AND Parent='{$rs['Catalog']}' ORDER BY Sort");

		while($rs1=mysql_fetch_array($result1)){
			$catalog_list2 .= "<option value='" . $rs1['No'] . "'" . (($rs['Catalog2'] == $rs1["No"] ) ? " SELECTED" : "") . ">" . $rs1["Name"] . "</option>";
		}
	}
	$catalog_list3 = "";
	if($rs['Catalog2'] > 0){
		$result1 = mysql_query("SELECT * FROM Catalog WHERE useFor='$usefor' AND Parent='{$rs['Catalog2']}' ORDER BY Sort");

		while($rs1=mysql_fetch_array($result1)){
			$catalog_list3 .= "<option value='" . $rs1['No'] . "'" . (($rs['Catalog3'] == $rs1["No"] ) ? " SELECTED" : "") . ">" . $rs1["Name"] . "</option>";
		}
	}

	$disabled2 = " disabled";
	$disabled3 = " disabled";
	
	if($catalog_list2 != ""){
		$disabled2 = "";
	}
	if($catalog_list3 != ""){
		$disabled3 = "";
	}
	$mobile = (($rs['mobile'] == 1) ? " CHECKED" : "");
	$event = "";
	if($rs['event'] == 1){
		$event = <<<EOD
	<br>
							<input type="text" id="event_date{$rs['No']}" style="width:100px" onClick="WdatePicker();" value="{$rs['event_date']}" onChange="setEvent({$rs['No']});">&nbsp;&nbsp;<br>
							<select id="event_start{$rs['No']}" onChange="setEvent({$rs['No']});">
								<option value="">開始時間</option>
								<option value="00:00">00:00</option>
								<option value="01:00">01:00</option>
								<option value="02:00">02:00</option>
								<option value="03:00">03:00</option>
								<option value="04:00">04:00</option>
								<option value="05:00">05:00</option>
								<option value="06:00">06:00</option>
								<option value="07:00">07:00</option>
								<option value="08:00">08:00</option>
								<option value="09:00">09:00</option>
								<option value="10:00">10:00</option>
								<option value="11:00">11:00</option>
								<option value="12:00">12:00</option>
								<option value="13:00">13:00</option>
								<option value="14:00">14:00</option>
								<option value="15:00">15:00</option>
								<option value="16:00">16:00</option>
								<option value="17:00">17:00</option>
								<option value="18:00">18:00</option>
								<option value="19:00">19:00</option>
								<option value="21:00">21:00</option>
								<option value="22:00">22:00</option>
								<option value="23:00">23:00</option>
							</select><script language='javascript'>$("#event_start{$rs['No']}").val("{$rs['event_start']}");</script> 
							～
							<select id="event_end{$rs['No']}" onChange="setEvent({$rs['No']});">
								<option value="">結束時間</option>
								<option value="01:00">01:00</option>
								<option value="02:00">02:00</option>
								<option value="03:00">03:00</option>
								<option value="04:00">04:00</option>
								<option value="05:00">05:00</option>
								<option value="06:00">06:00</option>
								<option value="07:00">07:00</option>
								<option value="08:00">08:00</option>
								<option value="09:00">09:00</option>
								<option value="10:00">10:00</option>
								<option value="11:00">11:00</option>
								<option value="12:00">12:00</option>
								<option value="13:00">13:00</option>
								<option value="14:00">14:00</option>
								<option value="15:00">15:00</option>
								<option value="16:00">16:00</option>
								<option value="17:00">17:00</option>
								<option value="18:00">18:00</option>
								<option value="19:00">19:00</option>
								<option value="21:00">21:00</option>
								<option value="22:00">22:00</option>
								<option value="23:00">23:00</option>
								<option value="24:00">24:00</option>
							</select><script language='javascript'>$("#event_end{$rs['No']}").val("{$rs['event_end']}");</script>
EOD;
	}
	$items .=<<<EOD
		<tr>
			<td>{$chk}</td>
			<td style="text-align:center;display:{$display}">
				<input type="checkbox" name="mobile" value="1" onClick="setMobile(this, {$rs['No']});"{$mobile}>可移動
			</td>
			<td style="text-align:center; color:blue"> $date1<br>/ ({$date2})</td>
			<td style="text-align:left">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td><a href="$url" target="_blank">{$rs['Name']}</a> [{$cashtype}]{$event}
						</td>
						<td>{$img}</td>
					</tr>
				</table>
			</td>
			<td style="text-align:left; width:124px">
				<select id='catalog_{$rs['No']}' name="catalog{$rs['No']}" style="width:130px" onChange="getCat2('{$rs['No']}'); setCatalog('{$rs['No']}');"><option value="">請選擇</option>{$catalog_list}</select><br>
				<select id='catalog2_{$rs['No']}' style="width:130px" name="catalog2{$rs['No']}" onChange="getCat3('{$rs['No']}'); setCatalog('{$rs['No']}');"{$disabled2}><option value="">請選擇</option>{$catalog_list2}</select><br>
				<select id='catalog3_{$rs['No']}' style="width:130px" name="catalog3{$rs['No']}" onChange="setCatalog('{$rs['No']}');"{$disabled3}><option value="">請選擇</option>{$catalog_list3}</select>
				<script language='javascript'>iForm.catalog{$rs['No']}.value='{$rs['Catalog']}';</script>
			</td>
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
		<td style="border-bottom:solid 1px gray; height:40px">{$t}</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center">
		<center>
		<form name="iForm" method="post" target="iAction">
		<input type="hidden" name="item">
		<input type="hidden" name="type" value="{$_REQUEST['type']}">
		<table width="100%" border=0>
			<tr>
				<td style="text-align:left; padding:5px">
					<Table align="left">
						<tr>
							<td valign="top" width=40 style="color:gray">[註]：</td>
							<td valign="top" align="left" style="color:gray">會員可以在此頁面變更適當的服務分類，並延長或下架前台的非金流服務。金流商品將依據設定自動下架，如欲強制下架金流商品，請聯絡網管人員處理。</td>
						</tR>
					</table>				
				</td>
				<td style="text-align:right;" width="200">
					<table align="right" border=0>
						<tr>
							<td><input type="button" value="強制下架" onClick="setClose();"></td>
							<td>&nbsp;&nbsp;</td>
							<td><input type="button" value="延長上架" onClick="Extend();" disabled></td>
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
function setEvent(xNo){
	$.post(
		'seller_item_event.php',
		{
			no: xNo,
			event_date: $("#event_date" + xNo).val(),
			event_start:$("#event_start" + xNo).val(),
			event_end: $("#event_end" + xNo).val()
		},
		function(data)
		{
		}
	);	
}
function setMobile(x, xNo){
	$.post(
		'seller_item_mobile.php',
		{
			no: xNo,
			mobile: ((x.checked) ? 1: 0)
		},
		function(data)
		{
		}
	);	
}

function getCat2(xProduct){
	$("#catalog2_"+xProduct).html('<option value="">請選擇</option>');
	$("#catalog3_"+xProduct).html('<option value="">請選擇</option>');
	$("#catalog3_"+xProduct).attr('disabled', true)
	$.post(
		'get_catalog.php',
		{
			no: $("#catalog_"+xProduct).val()
		},
		function(data)
		{
			$("#catalog2_"+xProduct).html('<option value="">請選擇</option>' + data);
			if($("#catalog2_"+xProduct + " option").length > 1){
				$("#catalog2_"+xProduct).attr('disabled', false)
			}
			else{
				$("#catalog2_"+xProduct).attr('disabled', true)
			}
		}
	);	
}
function getCat3(xProduct){
	$("#catalog3_"+xProduct).html('<option value="">請選擇</option>');
	$.post(
		'get_catalog.php',
		{
			no: $("#catalog2_"+xProduct).val()
		},
		function(data)
		{
			$("#catalog3_"+xProduct).html('<option value="">請選擇</option>' + data);
			if($("#catalog3_"+xProduct + " option").length > 1){
				$("#catalog3_"+xProduct).attr('disabled', false)
			}
			else{
				$("#catalog3_"+xProduct).attr('disabled', true)
			}
		}
	);	
}
function setCatalog(xProduct){
	$.post(
		'seller_item_catalog.php',
		{
			product: xProduct,
			catalog: $("#catalog_"+xProduct).val(),
			catalog2: $("#catalog2_"+xProduct).val(),
			catalog3: $("#catalog3_"+xProduct).val()
		},
		function(data)
		{
			//alert(data);
		}
	);
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
