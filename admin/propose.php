<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->propose][1])){exit("權限不足!!");}
$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$keyword = $_REQUEST["keyword"];
$area = $_REQUEST["area"];
$type = $_REQUEST["type"];
$catalog = $_REQUEST["catalog"];



$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_AREA' ORDER BY Sort");
while($rs=mysql_fetch_array($result)){
	$areas .= "<option value='" . $rs['No'] . "'" . (($area == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_COM' ORDER BY Sort");
while($rs=mysql_fetch_array($result)){
	$types .= "<option value='" . $rs['No'] . "'" . (($type == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_PRO' AND Parent=0 ORDER BY Sort");
while($rs=mysql_fetch_array($result)){
	$catalogs .= "<option value='" . $rs['No'] . "'" . (($catalog == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}


$tab = (($_REQUEST['tab'] != "") ? $_REQUEST['tab'] : 0);
$menu = array(
	'propose.php?tab=0' =>'本地團購',
	'propose.php?tab=1' =>'宅配團購',
	'propose.php?tab=2' =>'本地服務',
	'propose.php?tab=3' =>'宅配服務',
	'propose.php?tab=4' =>'商品粉絲',
	'propose.php?tab=5' =>'運輸服務',
	'propose.php?tab=6' =>'人力服務',
	'propose.php?tab=7' =>'活動服務',
);

$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($menu, $tab);

$sql = "SELECT *, (SELECT Name FROM Member WHERE No = Product.Member) AS MName FROM Product WHERE Status <> 0";
$sql .= (($area != "") ? " AND Area = '$area'" : "");
$sql .= (($type != "") ? " AND Type = '$type'" : "");
$sql .= (($catalog != "") ? " AND Catalog = '$catalog'" : "");
switch($tab){
	case 0:
		$sql .= " AND Mode = 1 AND Deliver = 0 AND Activity = 0 AND Transport = 0 AND hr=0 AND event=0";
		break;
	case 1:
		$sql .= " AND Mode = 1 AND Deliver = 1 AND Activity = 0 AND Transport = 0 AND hr=0 AND event=0";
		break;
	case 2:
		$sql .= " AND Mode = 2 AND Deliver = 0 AND Activity = 0 AND Transport = 0 AND hr=0 AND event=0";
		break;
	case 3:
		$sql .= " AND Mode = 2 AND Deliver = 1 AND Activity = 0 AND Transport = 0 AND hr=0 AND event=0";
		break;
	case 4:
		$sql .= " AND Activity = 1";
		break;
	case 5:
		$sql .= " AND Transport = 1";
		break;
	case 6:
		$sql .= " AND hr = 1";
		break;
	case 7:
		$sql .= " AND event = 1";
		break;
	default:
		$sql .= " AND 1=2";
		break;
}



$sql .= " ORDER BY dateUpdate DESC";
$result=mysql_query($sql) or die (mysql_error());
$num=mysql_num_rows($result);
$totalpage = ceil($num / $pagesize);
$pagging = new Pagging($totalpage, $pageno);

$page->addJSFile("../js/propose_admin.js");
$page->addJSFile("../js/jquery.min.js");
$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"mForm\" method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"memberlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"itemno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"mno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"sort\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"tab\" value=\"$tab\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("            <select name='area'><option value=''>所有地區</option>$areas</select>");
$page->addContent("            <select name='type'><option value=''>所有類型</option>$types</select>");
$page->addContent("            <select name='catalog'><option value=''>所有類別</option>$catalogs</select>");
$page->addContent("            <input type=\"text\" name=\"keyword\" value=\"$keyword\">");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"查詢\" onClick=\"Search();\">");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
//$page->addContent("            <input type=\"button\" class=\"command\" value=\"調整順序\" onClick=\"Resort();\">");
$page->addContent("            <input name=\"btnDelete2\" type=\"button\" class=\"command\" value=\"下架\" onClick=\"Delete2();\">");
$page->addContent("            <input name=\"btnDelete\" type=\"button\" class=\"command\" value=\"退回\" onClick=\"setRefuse();\">");
$page->addContent("            <input type=\"button\" class=\"command\" value=\"通過\" onClick=\"Approve();\">");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent("<TABLE width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">");
$page->addContent("<TR>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=20><input type=\"checkbox\" name=\"memberno\" value=\"\" onClick=\"checkAll(mForm.memberno)\"></TH>");
$page->addContent("<TH class=\"grid_heading\" WIDTH=50>編號</TH>");
$page->addContent("<TH class=\"grid_heading\" width=120>更新日期</TH>");
$page->addContent("<TH class=\"grid_heading\" width=100>會員</TH>");
$page->addContent("<TH class=\"grid_heading\">商品名稱</TH>");
if($tab == 2 || $tab == 3 || $tab == 4)
	$page->addContent("<TH class=\"grid_heading\" width=80>公益商品</TH>");
if($tab != 5)
	$page->addContent("<TH class=\"grid_heading\">原價</TH>");
$page->addContent("<TH class=\"grid_heading\" width=50>折扣</TH>");
$page->addContent("<TH class=\"grid_heading\"colspan='2'>狀態</TH>");
$page->addContent("<TH class=\"grid_heading\" width=70>退回原因</TH>");
$page->addContent("</TR>");
$status = array("草稿", "審核中", "已審核", "退回", "已下架", "待確認", "已審核");
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($record = mysql_fetch_array($result)){
			$discount = "&nbsp;";
			if($record['Transport'] == 1){
				$dis = $record['taxi_discount'];
			}
			else{
				if($record['price_mode'] == 0){
					$discount = (float)(number_format(($record['Price1'] / $record['Price'])*10,1));
					if($discount <= 0){
						$discount = "免費";
					}
					else if($discount >= 10){
						$discount = "";
					}
					else{
						$discount = (float)(number_format($discount, 2));
					}
				}
				$dis = $discount;//$record['Discount'];
				//$dis = (float)(number_format(($record['Price1'] / $record['Price'])*10,1));
			}
			if($record['Mode'] == 1){
				if($record['Deliver'] == 0){
					$type1=1;
				}
				if($record['Deliver'] == 1){
					$type1=2;
				}
			}
			if($record['Mode'] == 2){
				if($record['Deliver'] == 0){
					$type1=4;
				}
				if($record['Deliver'] == 1){
					$type1=5;
				}
			}
			$url = "../product_preview.php?no={$record['No']}";
			//if($record['Activity'] == 1)
			//	$dis = 0;
			$page->addContent("<TR>");
            $page->addContent("<TD class=\"grid_check\">" . ((1 == 1) ? "<input type=\"checkbox\" name=\"memberno\" value=\"$record[0]\">" : "-") . "</TD>");
			$page->addContent("<TD class=\"grid_no\">".(($pageno-1)*$pagesize + $i + 1)."</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['dateUpdate']}</TD>");
			$page->addContent("<TD class=\"grid_center\">{$record['MName']}</TD>");
			$page->addContent("<TD class=\"grid_left\">{$record['Name']}</TD>");
			if($tab == 2 || $tab == 3 || $tab == 4)
				$page->addContent("<TD class=\"grid_center\">" . (($record['Cashflow'] == 0) ? "<input type='checkbox' id=welfare{$record['No']} name='welfare' value='1'" . (($record['welfare'] == 1) ? " CHECKED" : "") . " onClick=\"setWelfare('{$record['No']}');\">" : "&nbsp;") . "</TD>");
			if($tab != 5)
				$page->addContent("<TD class=\"grid_center\">" . (($record['price_mode'] == 0) ? $record['Price'] : $record['price_info']) . "</TD>");
			$page->addContent("<TD class=\"grid_center\">" . $dis . "</TD>");
			$page->addContent("<TD class=\"grid_center\" width=80><A HREF=\"javascript:Edit('{$record['No']}')\">" . $status[$record['Status']] . "</A>(字)</TD>");
			$page->addContent("<TD class=\"grid_center\" width=80><A HREF=\"$url\" target=\"_blank\">" . $status[$record['Status']] . "</A>(圖)</TD>");
			$page->addContent("<TD class=\"grid_center\"><A HREF=\"javascript:Reason('{$record['No']}')\"><img src='../images/edit.gif' border=0></A></TD>");
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
<script language="javascript">
	function setWelfare(xNo){
		var welfare = 0;
		if($("#welfare" + xNo).attr("checked")){
			welfare = 1;
		}
		$.post(
		'propose_welfare.php',
		{
			no: xNo,
			welfare: welfare
		},
		function(data)
		{
		}
	);	
	
	}
	
	function Reason(xNo){
		window.showModalDialog("propose_refuse_reason.php?no="+xNo);
	}
	
	function setRefuse(){
		var t = getList();
		if(t.length > 0){
			var w = window.showModalDialog("propose_refuse_setreason.php", t, 'status:no');
			if(w){
				window.location.reload();
			}
		}
		else{
			alert("尚未選取!!");
		}
	}

</script>

