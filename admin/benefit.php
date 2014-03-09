<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->benefit][1])){exit("權限不足!!");}
$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->benefit));
include '../include/db_open.php';
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_AREA'");


$type = (($_REQUEST['type'] == "") ? "4" : $_REQUEST['type']);
$area = explode(",", $_REQUEST['area_list']);
$catalog = explode(",", $_REQUEST['catalog_list']);


$i=0;
$area_list = "<table>";
while($rs=mysql_fetch_array($result)){
	if($i % 7 == 0){$area_list .= "<tr>";}
	$area_list .= "<td><input type='checkbox' value='" . $rs['No'] . "' name='area'" . ((in_array($rs['No'], $area)) ? " checked" : "") . ">" . $rs["Name"] . "</td>";
	if($i % 7 == 6){$area_list .= "</tr>";}
	$i++;
}
$area_list .= "</table>";

$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_PRO' AND Parent=0");
$catalog_list = "";
$i=0;
$catalog_list = "<table>";
while($rs=mysql_fetch_array($result)){
	if($i % 5 == 0){$catalog_list .= "<tr>";}
	$catalog_list .= "<td><input type='checkbox' value='" . $rs['No'] . "' name='catalog'" . ((in_array($rs['No'], $catalog)) ? " checked" : "") . ">" . $rs["Name"] . "</td>";
	if($i % 5 == 4){$catalog_list .= "</tr>";}
	$i++;
}
$catalog_list .= "</table>";



include '../include/db_close.php';


$html = <<<EOD
<script type="text/javascript" src="../js/jquery.min.js"></script>

	<form name="iForm" method="post" action="benefit_list.php">
	<input type="hidden" name="p" value="{$_REQUEST['p']}">
	<input type="hidden" name="area_list">
	<input type="hidden" name="catalog_list">
<table>
	<tr>
		<td class="html_label_required">類別：</td><Td>
			<input type="radio" name="type" value="0" style="display:none">
			<input type="radio" name="type" value="1" onClick="chkArea()">到店團購
			<input type="radio" name="type" value="2" onClick="chkArea()">宅配團購
			<input type="radio" name="type" value="3" onClick="chkArea()">到店廉售
			<input type="radio" name="type" value="4" checked onClick="chkArea()">宅配廉售
		</td>
	</tr>
	<tr>
		<td class="html_label_required">分類：</td><Td>
		{$catalog_list}
		</td>
	</tr>
	<tr id="area" style="display:none">
		<td class="html_label_required">地區：</td><Td>
		{$area_list}
		</td>
	</tr>
	<tr>
		<td colspan="2"><hr>
			<table width="100%">
				<tr>
					<td align="center"><input type="button" value="下一步" onClick="Save();"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
	</form>



EOD;
$page->addContent($html);
$page->show();
JavaScript::Execute("iForm.type[$type].checked = true;");
?>
<script language="javascript">
	function chkArea(){
		var iForm = document.iForm;
		if(iForm.type[2].checked || iForm.type[4].checked){
			$('#area').hide();
		}
		else{
			$('#area').show();
		}
	}
	function Save(){
		var iForm = document.iForm;
		iForm.area_list.value = "";
		iForm.catalog_list.value="";
		for(var i=0; i<iForm.area.length; i++){
			if(iForm.area[i].checked){
				iForm.area_list.value += iForm.area[i].value + ",";
			}
		}
		for(var i=0; i<iForm.catalog.length; i++){
			if(iForm.catalog[i].checked){
				iForm.catalog_list.value += iForm.catalog[i].value + ",";
			}
		}
		if(iForm.area_list.value){
			iForm.area_list.value = iForm.area_list.value.substring(0, iForm.area_list.value.length-1);
		}
		if(iForm.catalog_list.value){
			iForm.catalog_list.value = iForm.catalog_list.value.substring(0, iForm.catalog_list.value.length-1);
		}
		if(!iForm.catalog_list.value){
			alert("請選擇分類!");
		}
		else if((iForm.type[1].checked || iForm.type[3].checked) && !iForm.area_list.value){
			alert("請選擇地區!");
		}
		else{
			if(iForm.type[2].checked || iForm.type[4].checked){iForm.area_list.value = '';}
			iForm.submit();
		}
	}

</script>
<script language="javascript">chkArea()</script>
