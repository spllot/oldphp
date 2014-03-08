<?php
$tab = $_REQUEST['tab'];
$type_list = "";
$type_other= "";
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_COM' ORDER BY Sort");
while($rs=mysql_fetch_array($result)){
	if($rs['Name'] != "其它")
		$type_list .= "<option value='" . $rs['No'] . "'" . (($_REQUEST['type'] == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
	else
		$type_other .= "<option value='" . $rs['No'] . "'" . (($_REQUEST['type'] == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}
include './include/db_close.php';
if($tab == 1){
	$type_all = "<option value='all'" . (($_REQUEST['type'] == "all") ? " SELECTED":"") . ">商品販售服務區</option>";
}
if($tab == 2){
	$type_all = "<option value='all'" . (($_REQUEST['type'] == "all") ? " SELECTED":"") . ">商品販售服務區</option>";
	$type_list = "";
	$type_other = "";
}
if($tab == 4){
	$type_all = "<option value='all'" . (($_REQUEST['type'] == "all") ? " SELECTED":"") . ">商品販售服務區</option>";
	$activity .= "<option value='activity'" . (($_REQUEST['type'] == "activity") ? " SELECTED":"") . ">粉絲推廣服務區</option>";
	$activity .= "<option value='transfer'" . (($_REQUEST['type'] == "transfer") ? " SELECTED":"") . ">即時運輸服務區</option>";
	$activity .= "<option value='hr'" . (($_REQUEST['type'] == "hr") ? " SELECTED":"") . ">即時人力服務區</option>";
	$activity .= "<option value='event'" . (($_REQUEST['type'] == "event") ? " SELECTED":"") . ">即時活動服務區</option>";
	$activity .= "<option value='welfare'" . (($_REQUEST['type'] == "welfare") ? " SELECTED":"") . ">*公益(愛心)商品</option>";
	$activity .= "<option value='free'" . (($_REQUEST['type'] == "free") ? " SELECTED":"") . ">*零元(免費)商品</option>";
	$activity .= "<option value='allnew'" . (($_REQUEST['type'] == "allnew") ? " SELECTED":"") . ">*全新貨品販售</option>";
	$activity .= "<option value='used'" . (($_REQUEST['type'] == "used") ? " SELECTED":"") . ">*中古貨品販售</option>";
	$activity .= "<option value='sale'" . (($_REQUEST['type'] == "sale") ? " SELECTED":"") . ">*即期貨品販售</option>";
}
if($tab == 5){
	$type_all = "<option value='all'" . (($_REQUEST['type'] == "all") ? " SELECTED":"") . ">商品販售服務區</option>";
	$activity .= "<option value='activity'" . (($_REQUEST['type'] == "activity") ? " SELECTED":"") . ">粉絲推廣服務區</option>";
	$activity .= "<option value='welfare'" . (($_REQUEST['type'] == "welfare") ? " SELECTED":"") . ">*公益(愛心)商品</option>";
	$activity .= "<option value='free'" . (($_REQUEST['type'] == "free") ? " SELECTED":"") . ">*零元(免費)商品</option>";
	$activity .= "<option value='allnew'" . (($_REQUEST['type'] == "allnew") ? " SELECTED":"") . ">*全新貨品販售</option>";
	$activity .= "<option value='used'" . (($_REQUEST['type'] == "used") ? " SELECTED":"") . ">*中古貨品販售</option>";
	$activity .= "<option value='sale'" . (($_REQUEST['type'] == "sale") ? " SELECTED":"") . ">*即期貨品販售</option>";
	$type_list = "";
	$type_other = "";
}


echo $type_all . $activity . $transfer . $type_other;
?>