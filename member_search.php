<?php
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_AREA' ORDER BY Sort");
$area_list = "";
while($rs=mysql_fetch_array($result)){
	$area_list .= "<option value='" . $rs['No'] . "'" . (($_REQUEST['area'] == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_COM' ORDER BY Sort");
$type_list = "";
while($rs=mysql_fetch_array($result)){
	$type_list .= "<option value='" . $rs['No'] . "'" . (($_REQUEST['type'] == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_PRO' AND Parent=0 ORDER BY Sort");
$catalog_list = "";
while($rs=mysql_fetch_array($result)){
	$catalog_list .= "<option value='" . $rs['No'] . "'" . (($_REQUEST['catalog'] == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}
include './include/db_close.php';


if($tab == 2 || $tab ==5){
	$hide = ";display:none";
}
if($tab == 2){
	$hide_type = ";display:none";
}

if($tab == 4){
	$type_all = "<option value='all'" . (($_REQUEST['type'] == "all") ? " SELECTED":"") . ">所有到店商品</option>";
	$activity = "<option value='used'" . (($_REQUEST['type'] == "used") ? " SELECTED":"") . ">中古貨販售</option>";
	$activity .= "<option value='sale'" . (($_REQUEST['type'] == "sale") ? " SELECTED":"") . ">即期貨販售</option>";
	$activity .= "<option value='activity'" . (($_REQUEST['type'] == "activity") ? " SELECTED":"") . ">商品粉絲抽獎活動</option>";
}
if($tab == 5){
	$type_all = "<option value='all'" . (($_REQUEST['type'] == "all") ? " SELECTED":"") . ">所有宅配商品</option>";
	$activity = "<option value='used'" . (($_REQUEST['type'] == "used") ? " SELECTED":"") . ">中古貨販售</option>";
	$activity .= "<option value='sale'" . (($_REQUEST['type'] == "sale") ? " SELECTED":"") . ">即期貨販售</option>";
	$activity .= "<option value='activity'" . (($_REQUEST['type'] == "activity") ? " SELECTED":"") . ">商品粉絲抽獎活動</option>";
	$type_list = "";
}
$act = str_replace("_detail", "", basename($_SERVER['PHP_SELF']));

$search_bar = <<<EOD
		<table border=0 cellpadding="0" cellspacing="0" width="706" style="height:47px">
			<tr>
				<td align="left">
					<form name="sForm" action="{$act}" STYLE="margin: 0px; padding: 0px;">
					<input type="hidden" name="tab" value="{$tab}">
					<input type="hidden" name="seller" value="{$seller}">
					<input type="hidden" name="pageno" value="{$_REQUEST['pageno']}">
					<table>
						<tr>
							<td style="display:none;color:white; font-size:11pt; line-height:20px{$hide}">地區</td>
							<td style="display:none;color:white; font-size:11pt; line-height:20px{$hide}">
								<select name="area"><option value="">所有地區</option>{$area_list}</select>
							</td>
							<td style="color:white; font-size:11pt; line-height:20px;padding-left:10px{$hide_type}">類型</td>
							<td style="color:white; font-size:11pt; line-height:20px{$hide_type}">
								<select name="type"><option value="">所有類型</option>{$type_all}{$type_list}{$activity}</select>
							</td>
							<td style="color:white; font-size:11pt; line-height:20px;padding-left:10px">分類</td>
							<td style="color:white; font-size:11pt; line-height:20px">
								<select name="catalog"><option value="">所有分類</option>{$catalog_list}</select>
							</td>
							<td style="padding-left:15px">
								<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td id="sort" onMouseOver="btnOver(this)" onMouseOut="btnOut(this)" onClick="Search();" style="background:url(./images/btn_sort.gif); background-repeat:no-repeat; background-position:center center; width:85px; height:27px; cursor:pointer">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					</form>
				</td>
				<td align="right" width="100">
				</td>
			</tr>
			<tr style="height:13px">
				<td style="display:none">
					<img src="./images/btn_location.gif">
					<img src="./images/btn_location_over.gif">
				</td>
			</tr>
		</table>	
		<script language="javascript">
			function Search(){
				var sForm = document.sForm;
				sForm.pageno.value = 1;
				sForm.submit();
			}
			function setPage(xNo){
				var sForm = document.sForm;
				sForm.pageno.value = xNo;
				sForm.submit();
			
			}
			function btnOver(x){
				x.style.backgroundImage="url(./images/btn_" + x.id + "_over.gif)";
			}
			function btnOut(x){
				x.style.backgroundImage="url(./images/btn_" + x.id + ".gif)";
			}
		</script>

EOD;

?>