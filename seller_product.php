<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

$mode = $_REQUEST['mode'];
$deliver = $_REQUEST['deliver'];
$activity = (($_REQUEST['activity'] == "") ? "0" : $_REQUEST['activity']);;
$transport = (($_REQUEST['transport'] == "") ? "0" : $_REQUEST['transport']);;
$hr = (($_REQUEST['hr'] == "") ? "0" : $_REQUEST['hr']);;
$event = (($_REQUEST['event'] == "") ? "0" : $_REQUEST['event']);;
include './include/db_open.php';
$count1 = 0;
$count2 = 0;
$count3 = 0;
$count4 = 0;
$count5 = 0;
$count6 = 0;
$count7 = 0;
$count8 = 0;


$result = mysql_query("SELECT COUNT(*) FROM Product WHERE Mode = 1 AND Deliver=0 AND Activity=0 AND Transport=0 AND hr=0 AND event=0 AND  Member='" . $_SESSION['member']['No'] . "'");
list($count1)=mysql_fetch_row($result);

$result = mysql_query("SELECT COUNT(*) FROM Product WHERE Mode = 1 AND Deliver=1 AND Activity=0 AND Transport=0 AND hr=0 AND event=0 AND  Member='" . $_SESSION['member']['No'] . "'");
list($count2)=mysql_fetch_row($result);

$result = mysql_query("SELECT COUNT(*) FROM Product WHERE Mode = 2 AND Deliver=0 AND Activity=0 AND Transport=0 AND hr=0 AND event=0 AND  Member='" . $_SESSION['member']['No'] . "'");
list($count3)=mysql_fetch_row($result);

$result = mysql_query("SELECT COUNT(*) FROM Product WHERE Mode = 2 AND Deliver=1 AND Activity=0 AND Transport=0 AND hr=0 AND event=0 AND  Member='" . $_SESSION['member']['No'] . "'");
list($count4)=mysql_fetch_row($result);

$result = mysql_query("SELECT COUNT(*) FROM Product WHERE Activity=1 AND  Member='" . $_SESSION['member']['No'] . "'");
list($count5)=mysql_fetch_row($result);

$result = mysql_query("SELECT COUNT(*) FROM Product WHERE Transport=1 AND  Member='" . $_SESSION['member']['No'] . "'");
list($count6)=mysql_fetch_row($result);

$result = mysql_query("SELECT COUNT(*) FROM Product WHERE hr=1 AND  Member='" . $_SESSION['member']['No'] . "'");
list($count7)=mysql_fetch_row($result);

$result = mysql_query("SELECT COUNT(*) FROM Product WHERE event=1 AND  Member='" . $_SESSION['member']['No'] . "'");
list($count8)=mysql_fetch_row($result);




$sql = "SELECT * FROM Product WHERE Member='" . $_SESSION['member']['No'] . "'";
$sql .= (($mode !="") ? " AND Mode = '$mode'" : "");
$sql .= (($deliver != "") ? " AND Deliver='$deliver'" : "");
$sql .= (($activity != "") ? " AND Activity='$activity'" : "");
$sql .= (($transport != "") ? " AND Transport='$transport'" : "");
$sql .= (($event != "") ? " AND event='$event'" : "");
$sql .= (($hr != "") ? " AND hr='$hr'" : "");
$sql .= " ORDER BY dateUpdate DESC";

$option = "<input type='radio' name='option' value='1' onClick=\"window.location.href='seller_product.php?option=1&deliver=0&mode=1';\"" . (($mode == 1 && $deliver==0) ? " CHECKED":"") . ">本地團購($count1)&nbsp;&nbsp;";
$option .= "<input type='radio' name='option' value='2' onClick=\"window.location.href='seller_product.php?option=2&deliver=1&mode=1';\"" . (($mode == 1 && $deliver==1) ? " CHECKED":"") . ">宅配團購($count2)&nbsp;&nbsp;";
$option .= "<input type='radio' name='option' value='3' onClick=\"window.location.href='seller_product.php?option=3&deliver=0&mode=2';\"" . (($mode == 2 && $deliver==0) ? " CHECKED":"") . ">本地服務($count3)&nbsp;&nbsp;";
$option .= "<input type='radio' name='option' value='4' onClick=\"window.location.href='seller_product.php?option=4&deliver=1&mode=2';\"" . (($mode == 2 && $deliver==1) ? " CHECKED":"") . ">宅配服務($count4)&nbsp;&nbsp;";
$option .= "<br><input type='radio' name='option' value='5' onClick=\"window.location.href='seller_product.php?option=5&activity=1';\"" . (($activity == 1) ? " CHECKED":"") . ">商品粉絲抽獎($count5)&nbsp;&nbsp;";
$option .= "<input type='radio' name='option' value='6' onClick=\"window.location.href='seller_product.php?option=6&transport=1';\"" . (($transport == 1) ? " CHECKED":"") . ">運輸服務($count6)&nbsp;&nbsp;";

$option .= "<input type='radio' name='option' value='7' onClick=\"window.location.href='seller_product.php?option=7&hr=1';\"" . (($hr == 1) ? " CHECKED":"") . ">人力服務($count7)&nbsp;&nbsp;";
$option .= "<input type='radio' name='option' value='8' onClick=\"window.location.href='seller_product.php?option=8&event=1';\"" . (($event == 1) ? " CHECKED":"") . ">活動服務($count8)&nbsp;&nbsp;";


$result = mysql_query($sql);


$price = (($transport == 1) ? '':'<td style="width:50px; color: white; background:#000000; text-align:center">原價</td>');
$list =<<<EOD
	<form name="iForm" method="post">
	<input type="hidden" name="no" value="">
	<input type="hidden" name="mode" value="$mode">
	<input type="hidden" name="deliver" value="$deliver">
	<input type="hidden" name="activity" value="$activity">
	<input type="hidden" name="transport" value="$transport">
	<input type="hidden" name="hr" value="$hr">
	<input type="hidden" name="event" value="$event">
	<input type="hidden" name="itemlist" value="">
	<table width="100%">
		<tr>
			<td style="width:50px; text-align:center; background:#000000"><input name="itemno" type="checkbox" onClick="checkAll();"></td>
			<td style="width:; color: white; background:#000000; text-align:center">商品&服務名稱</td>
			{$price}
			<td style="width:50px; color: white; background:#000000; text-align:center">折扣</td>
			<td style="width:120px; color: white; background:#000000; text-align:center">更新日期</td>
			<td style="width:50px; color: white; background:#000000; text-align:center">狀態</td>
		</tR>
EOD;
$status = array("草稿", "審核中", "已審核", "退回", "已下架", "待確認", "已審核");
while($rs = mysql_fetch_array($result)){
	$chk = (($rs['Status'] == 0) ? "<input type=\"checkbox\" value=\"{$rs['No']}\" name=\"itemno\">" : "<img src='./images/deny.png' title='不可刪除'>");
	$chk = (($rs['Status'] != 2) ? "<input type=\"checkbox\" value=\"{$rs['No']}\" name=\"itemno\">" : "<img src='./images/deny.png' title='不可刪除'>");
	$dis = (float)(number_format($rs['Discount'], 1));
	$dis = (($rs['Transport'] == 0) ? "{$dis}折" : (($rs['taxi_discount'] > 0 ) ? "{$rs['taxi_discount']}折":"無"));
//	if($rs['Activity'] == 1)
//		$dis = 0;
	$price = (($rs['Transport'] == 0) ? "<td>{$rs['Price']}</td>" : "");
	$list .=<<<EOD
		<tr>
			<td style="; text-align:center">$chk</td>
			<td><a href="javascript:Edit('{$rs['No']}');">{$rs['Name']}</a></td>
			{$price}
			<td style="text-align:center">{$dis}</td>
			<td style="font-size:10pt; text-align:center">{$rs['dateUpdate']}</td>
			<td style="; text-align:center">{$status[$rs['Status']]}</td>
		</tR>

EOD;

}
$list .= "</table></form>";
include './include/db_close.php';
include 'seller_product_tab.php';
$WEB_CONTENT = <<<EOD

<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td>{$option}</td>
	</tr>
	<tr>
		<td style="text-align:left; padding-left:5px; padding-top:10px"><input type="button" value="刪除" onClick="Delete();"></td>
	</tR>
	<tr>
		<td>$list</td>
	</tr>
</table>


EOD;

include 'template2.php';
?>
<script language="javascript">
var code = "seller_product";
function checkAll(){
	for(var i=1; i<iForm.itemno.length; i++){
		iForm.itemno[i].checked= iForm.itemno[0].checked;
	}
}
function Delete(){
    iForm.itemlist.value = getList();
    if (iForm.itemlist.value){
        if (confirm("確定要刪除所選項目?")){
			iForm.target="iAction";
            iForm.action = code + "_delete.php";
            iForm.submit();
        }//if
    }//if
    else{
        alert("尚未選取!!");
    }//else
}//Delete

function New(){
    iForm.action = code + "_property.php";
    iForm.submit();
}//New

function Edit(xNo){
    if(xNo){
        iForm.no.value = xNo;
        iForm.action = code + "_step<?=(($mode==1) ? "2":"3")?>.php";
        iForm.submit();
    }//if
}//Edit

function getList(){
    var tStr = "";
    for (var i=1; i<iForm.itemno.length; i++){
        if (iForm.itemno[i].checked){
            tStr += iForm.itemno[i].value + ",";
        }//if
    }//for
	if (tStr.length > 0){
		tStr = tStr.substring(0, tStr.length - 1);
	}//if
    return tStr;
}//getList

function jumpPage(){
    iForm.pageno.value = pagging.pageno.value
    iForm.action = code + ".php";
    iForm.submit();
}//jumpPage

function nextPage(){
    if (pagging.pageno.selectedIndex < pagging.pageno.options.length - 1){
        pagging.pageno.selectedIndex ++;
        jumpPage();
    }//if
}//nextPage

function prevPage(){
    if (pagging.pageno.selectedIndex > 0){
        pagging.pageno.selectedIndex--;
        jumpPage();
    }//if
}//prevPage
</script language="javascript">
