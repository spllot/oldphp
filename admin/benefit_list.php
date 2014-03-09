<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->benefit][1])){exit("權限不足!!");}
$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->benefit));

$p = $_REQUEST['p'];
$type = $_REQUEST['type'];
$area = $_REQUEST['area_list'];
$catalog = $_REQUEST['catalog_list'];

if($type  != "" && $catalog != ""){
	$sql = "SELECT * FROM Product WHERE 1=1 AND Status IN(2, 6) AND Catalog IN ($catalog) AND dateClose >= CURRENT_TIMESTAMP";
	switch($type){
		case 1:
			$sql .= " AND Mode=1";
			$sql .= " AND Deliver=0";
			break;
		case 2:
			$sql .= " AND Mode=1";
			$sql .= " AND Deliver=1";
			break;
		case 3:
			$sql .= " AND Mode=2";
			$sql .= " AND Deliver=0";
			break;
		case 4:
			$sql .= " AND Mode=2";
			$sql .= " AND Deliver=1";
			break;
	};

	$sql .= (($area !="") ? " AND Area in ($area)" : "");
	$sql .= (($p != "") ? " AND No NOT IN($p)" : "");
}


if($sql != ""){
	include '../include/db_open.php';
	$result = mysql_query($sql) or die(mysql_error());
	while($rs=mysql_fetch_array($result)){
		$discount = (float)(number_format($rs['Discount'],2));
			if($discount <= 0){
				$discount = "免費";
			}
			else if($discount >= 10){
				$discount = "";
			}
			else{
				$discount = $discount . "折";
			}
		$product .= <<<EOD
			<div id="product{$rs['No']}">
				<table style="width:600px">
					<tr id="row{$rs['No']}">
						<td style="width:30px; text-align:center"><input type="checkbox" onClick="setProduct(this)" name="product" value="{$rs['No']}"></td>
						<td style="font-size:11pt">{$rs['Name']}</td>
						<td style="width:60px; text-align:right;font-size:11pt">{$rs['Price1']}</td>
						<td style="width:60px; text-align:center;font-size:11pt">{$discount}</td>
					</tr>
				</table>
			</div>
EOD;
	}
	$ps = explode(",", $p);
	for($i=0; $i<sizeof($ps); $i++){
		$result = mysql_query("SELECT * FROM Product WHERE No = '" . $ps[$i] . "'");
		if($rs=mysql_fetch_array($result)){
			$selected .= <<<EOD
			<div id="selected{$rs['No']}">
				<table style="width:600px">
					<tr id="row{$rs['No']}">
						<td style="width:30px; text-align:center"><input type="checkbox" onClick="setProduct(this)" name="product" value="{$rs['No']}" checked></td>
						<td style="font-size:11pt">{$rs['Name']}</td>
						<td style="width:60px; text-align:right;font-size:11pt">{$rs['Price1']}</td>
						<td style="width:60px; text-align:center;font-size:11pt">{$discount}</td>
						<td id="up{$rs['No']}" style="width:25px"><img src="../images/move.png" border=0 style='cursor:pointer'></td>
					</tr>
				</table>
			</div>

EOD;
		}
	
	}
	include '../include/db_close.php';
}
$html = <<<EOD

<script type="text/javascript" src="../js/jquery.min.js"></script>
 <script type="text/javascript" src="../js/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="../js/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../js/ui/jquery.ui.mouse.js"></script>
<script type="text/javascript" src="../js/ui/jquery.ui.draggable.js"></script>
<script type="text/javascript" src="../js/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="../js/ui/jquery.ui.sortable.js"></script>
<script type="text/javascript" src="../uploadify/swfobject.js"></script>

<form name="iForm" method="post" action="benefit_send.php">
<input type="hidden" name="p" value="$p">
<input type="hidden" name="type" value="$type">
<input type="hidden" name="catalog_list" value="$catalog">
<input type="hidden" name="area_list" value="$area">
<table>
	<tr>
		<td class="html_label_generated">候選商品：</td>
		<td><div id="product" style="width:630px; height:150px; overflow-y:scroll; border:solid 1px gray">{$product}</div></td>
	</tR>
	<tr>
		<td class="html_label_required">已選商品：</td>
		<td><div id="selected">{$selected}</div></td>
	</tR>
	<tr>
		<td colspan="2"><hr>
			<table width="100%">
				<tr>
					<td width="50%" align="center"><input type="button" value="上一步" onClick="Cancel();"><td>
					<td width="50%" align="center"><input type="button" value="下一步" onClick="Save();"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>



EOD;
$page->addContent($html);
$page->show();
?>
<script language="javascript">
	function setProduct(x){
		var id = x.value;
		var counts = $("#selected div").length;
		if(x.checked){
			if(counts < 15){
				$('#selected').append("<div id='selected" + id + "'>" + $('#product'+id).html() + "</div>");
				$('#product' + id).remove();
				$('#row' + id).append("<td id=\"up" + id + "\" style=\"width:25px\"><img src=\"../images/move.png\" border=0 style='cursor:pointer'></td>");
			}
			else{
				x.checked = false;
				alert("最多只能選擇15個商品!");
			}
		}
		else{
			$('#up'+id).remove();
			$('#down'+id).remove();
			$('#product').append("<div id='product" + id + "'>" + $('#selected'+id).html() + "</div>");
			$('#selected' + id).remove();
		}
	}
	function Cancel(){
		var iForm = document.iForm;
		iForm.action="benefit.php";
		iForm.submit();
	}
	function Save(){
		var iForm = document.iForm;
		var tStr = "";
		if(iForm.product){
			if(iForm.product.length){
				for(var i=0; i<iForm.product.length; i++){
					if(iForm.product[i].checked){
						tStr += iForm.product[i].value + ",";
					}
				}
				if(tStr.length >0){
					tStr = tStr.substring(0, tStr.length -1);
				}
			}
			else{
				if(iForm.product.checked){
					tStr = iForm.product.value;
				}
			}
		}
		if(tStr.length >0){
			//alert(tStr);
			iForm.p.value = tStr;
			iForm.submit();
		}
		else{
			alert("請選擇商品!");
		}
	}

</script>

<script language="javascript">
	$(function() {
		$("#selected").sortable({
		});
	});

</script>