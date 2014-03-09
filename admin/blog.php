<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->blog][1])){exit("權限不足!!");}
$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$tab = (($_REQUEST['tab'] != "") ? $_REQUEST['tab'] : 5);
$year = (($_REQUEST['year'] != "") ? $_REQUEST['year'] : date('Y'));
$month = (($_REQUEST['month'] != "") ? $_REQUEST['month'] : date('j'));

$date = date('Y-m-01');
$menu = array();





for($i=0; $i<6; $i++){
	$tmp = date("Y-m", strtotime($date . "-" . (5-$i) . " month"));
	$menu["blog.php?tab=" . $i] = $tmp;
	if($tab==$i) $curr = $tmp;
}



$amount = 0;
$result = mysql_query("SELECT * FROM Config WHERE ID='$curr'");
if($rs=mysql_fetch_array($result)){
	$amount = $rs['YN'];
}
$result = mysql_query("SELECT * FROM Config WHERE ID='{$curr}S'");
if($rs=mysql_fetch_array($result)){
	$price = $rs['YN'];
}



$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($menu, $tab);

$sql = "SELECT * FROM Blog WHERE 1=1 AND dateSubmited LIKE '$curr%'";
$sql .= " ORDER BY dateConfirmed";

$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
$pagging = new Pagging($totalpage, $pageno);

$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"mForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"memberlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"itemno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"mno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"sort\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"tab\" value=\"$tab\">");
$page->addContent("<input type=\"hidden\" name=\"curr\" value=\"$curr\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
if($tab == 5){
$page->addContent("            目前每股價格：<input type='text' name='price' style='width:60px' value='$price'>&nbsp;<input type=\"button\" class=\"command\" value=\"確定\" onClick=\"Config2();\"><br>");
}
$page->addContent("            {$curr}部落格行銷文章徵求數設定：");
if($tab == 5){
	$page->addContent("            <input type=\"text\" name=\"amount\" value=\"$amount\" style='width:60px'>");
	$page->addContent("            <input type=\"button\" class=\"command\" value=\"確定\" onClick=\"Config();\">");
}
else{
	$page->addContent("$amount");
}
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");
$page->addContent("<tr style=''>");
$page->addContent("<Td class=\"grid_heading\" colspan='6' style='text-align:center; height:30px;line-height:30px'>{$curr}部落格行銷文章應徵者列表</Td>");
$page->addContent("<Td class=\"grid_heading\" colspan='6' style='text-align:center; height:30px;line-height:30px'>{$curr}部落格行銷文章評分</Td>");
$page->addContent("</tr>");
$page->addContent("<TR>");
$page->addContent("<TH class=\"grid_heading\" style='font-size:10pt'>會員郵件</TH>");
$page->addContent("<TH class=\"grid_heading\" width=60 style='font-size:10pt'>送出時間</TH>");
$page->addContent("<TH class=\"grid_heading\" style='font-size:10pt'>文章主題</TH>");
$page->addContent("<TH class=\"grid_heading\" style='font-size:10pt'>文章網址</TH>");
$page->addContent("<TH class=\"grid_heading\" style='font-size:10pt; width:50px'>推薦</TH>");
$page->addContent("<TH class=\"grid_heading\" style='font-size:10pt; width:50px'>回應</TH>");
$page->addContent("<TH class=\"grid_heading\" width='70' style='font-size:10pt'>行銷說服力<br>(0-150)</TH>");
$page->addContent("<TH class=\"grid_heading\" width='60' style='font-size:10pt'>表達能力<br>(0-100)</TH>");
$page->addContent("<TH class=\"grid_heading\" width='60' style='font-size:10pt'>豐富性<br>(0-100)</TH>");
$page->addContent("<TH class=\"grid_heading\" width='60' style='font-size:10pt'>推薦人數<br>(0-150)</TH>");
$page->addContent("<TH class=\"grid_heading\" width='70' style='font-size:10pt'>回應人數<br>(上限:20)x5</TH>");
$page->addContent("<TH class=\"grid_heading\" width='80' style='font-size:10pt'>得分</TH>");
$page->addContent("</TR>");
$status = array("草稿", "審核中", "已審核", "退回", "已下架", "待確認", "已審核");
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($record = mysql_fetch_array($result)){
			$page->addContent("<TR>");
			$page->addContent("<TD class=\"grid_center\" style='font-size:10pt'>{$record['userID']}</TD>");
			$page->addContent("<TD class=\"grid_center\" style='font-size:10pt'>" . substr($record['dateSubmited'], 0, 10) . "</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Subject']}</TD>");
			$page->addContent("<TD class=\"grid_left\"><a href='{$record['Url']}' target='_blank'>" . substr($record['Url'], 0, 20) . "...</a></TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Recommend']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['Reply']}</TD>");
			$page->addContent("<TD class=\"grid_center\">" . (($record['dateConfirmed'] == "0000-00-00 00:00:00") ? "<input name='S1{$record['No']}' type='text' style='width:50px' onKeyUp=\"setTotal('{$record['No']}');\">" : $record['S1']) . "</TD>");
			$page->addContent("<TD class=\"grid_center\">" . (($record['dateConfirmed'] == "0000-00-00 00:00:00") ? "<input name='S2{$record['No']}'  type='text' style='width:50px' onKeyUp=\"setTotal('{$record['No']}');\">" : $record['S2']) . "</TD>");
			$page->addContent("<TD class=\"grid_center\">" . (($record['dateConfirmed'] == "0000-00-00 00:00:00") ? "<input name='S3{$record['No']}'  type='text' style='width:50px' onKeyUp=\"setTotal('{$record['No']}');\">" : $record['S3']) . "</TD>");
			$page->addContent("<TD class=\"grid_center\">" . (($record['dateConfirmed'] == "0000-00-00 00:00:00") ? "<input name='S4{$record['No']}'  type='text' style='width:50px' onKeyUp=\"setTotal('{$record['No']}');\">" : $record['S4']) . "</TD>");
			$page->addContent("<TD class=\"grid_center\">" . (($record['dateConfirmed'] == "0000-00-00 00:00:00") ? "<input name='S5{$record['No']}'  type='text' style='width:50px' onKeyUp=\"setTotal('{$record['No']}');\">" : $record['S5']) . "</TD>");
			$page->addContent("<TD class=\"grid_center\">" . (($record['dateConfirmed'] == "0000-00-00 00:00:00") ? "<table><tr><Td><div id='earn{$record['No']}' style='color:red'>0</div></td><td><input type='button' value='送出' onClick='Save(\"{$record['No']}\")'></td></tr></table>" : $record['Earn']) . "</TD>");
			$page->addContent("</TR>");
		}//if
		else
			break;
	}//for
	$page->addContent("</TABLE></TD></TR></form>");
    $page->addContent("<TR><TD>" . $pagging->toString() . "</TD></TR>");
}//if
else{
	$page->addContent("</TABLE></TD></TR></form>");
	$page->addContent("<TR><TD class=\"grid_nodata\">目前無資料</TD></TR>");
}//else
$page->addContent("</TABLE>");
include("../include/db_close.php");
$page->show();
?>
<form name="sForm" method="post" action="blog_confirm.php">
	<input type="hidden" name="tab" value="<?=$tab?>">
	<input type="hidden" name="curr" value="<?=$curr?>">
	<input type="hidden" name="no">
	<input type="hidden" name="S1">
	<input type="hidden" name="S2">
	<input type="hidden" name="S3">
	<input type="hidden" name="S4">
	<input type="hidden" name="S5">
</form>

<script language="javascript">
var mForm = document.mForm;
var sForm = document.sForm;
function Config2(){
	var mForm = document.mForm;
	if(!mForm.price.value){
		alert("請輸入目前每股價格");
		mForm.price.focus();
	}
	else{
		mForm.action="blog_config2.php";
		mForm.submit();
	}

}
function Config(){
	var mForm = document.mForm;
	if(!mForm.amount.value){
		alert("請輸入部落格行銷文章徵求數");
		mForm.amount.focus();
	}
	else{
		mForm.action="blog_config.php";
		mForm.submit();
	}

}

function getNum(x){

	var r = parseInt(x, 10);
	if(isNaN(r)){
		return 0;
	}
	return r;
}

function setTotal(x){
	var S1 = getNum(eval("mForm.S1" + x).value);
	var S2 = getNum(eval("mForm.S2" + x).value);
	var S3 = getNum(eval("mForm.S3" + x).value);
	var S4 = getNum(eval("mForm.S4" + x).value);
	var S5 = getNum(eval("mForm.S5" + x).value);
	if(S1 > 150){
		alert("行銷說服力得分不可超過150!");
		eval("mForm.S1" + x).value = "";
		eval("mForm.S1" + x).focus();
		S1 = 0;
	}
	else if(S2 > 100){
		alert("表達能力得分不可超過100!");
		eval("mForm.S2" + x).value = "";
		eval("mForm.S2" + x).focus();
		S2 = 0;
	}
	else if(S3 > 100){
		alert("豐富性得分不可超過100!");
		eval("mForm.S3" + x).value = "";
		eval("mForm.S3" + x).focus();
		S3 = 0;
	}
	else if(S4 > 150){
		alert("推薦人數得分不可超過150!");
		eval("mForm.S4" + x).value = "";
		eval("mForm.S4" + x).focus();
		S4 = 0;
	}
	else if(S5 > 100){
		alert("回應人數得分不可超過100!");
		eval("mForm.S5" + x).value = "";
		eval("mForm.S5" + x).focus();
		S5 = 0;
	}
	document.getElementById("earn" + x).innerHTML =  S1 + S2 + S3 + S4 + S5 ;
}

function Save(x){
	sForm.no.value = x;
	sForm.S1.value = eval("mForm.S1" + x).value;
	sForm.S2.value = eval("mForm.S2" + x).value;
	sForm.S3.value = eval("mForm.S3" + x).value;
	sForm.S4.value = eval("mForm.S4" + x).value;
	sForm.S5.value = eval("mForm.S5" + x).value;

	if(!sForm.S1.value || !sForm.S2.value || !sForm.S3.value || !sForm.S4.value || !sForm.S5.value){
		alert("請填寫得分!");
	}
	else if(getNum(sForm.S1.value) > 150){
		alert("行銷說服力得分不可超過150");
	}
	else if(getNum(sForm.S2.value) > 100){
		alert("表達能力得分不可超過100");
	}
	else if(getNum(sForm.S3.value) > 100){
		alert("豐富性得分不可超過100");
	}
	else if(getNum(sForm.S4.value) > 150){
		alert("推薦人數得分不可超過150");
	}
	else if(getNum(sForm.S5.value) > 100){
		alert("回應人數得分不可超過100");
	}
	else{
		if(confirm("確定要送出?")){
			sForm.submit();
		}
	}
}
</script>