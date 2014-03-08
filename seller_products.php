<?php
include './include/db_open.php';
$tab = (($_REQUEST['tab']!= "") ? $_REQUEST['tab'] : 4);

$id = $_REQUEST['id'];
$result = mysql_query("SELECT * FROM Member WHERE No='$id'");
$member = mysql_fetch_array($result);

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


if($tab == 2 || $tab ==5){
	$hide = ";display:none";
}
if($tab == 2){
	$hide_type = ";display:none";
}

if($tab == 4){
	$type_all = "<option value='all'" . (($_REQUEST['type'] == "all") ? " SELECTED":"") . ">所有到店商品</option>";
	$activity = "<option value='activity'" . (($_REQUEST['type'] == "activity") ? " SELECTED":"") . ">商品粉絲抽獎活動</option>";
}
if($tab == 5){
	$type_all = "<option value='all'" . (($_REQUEST['type'] == "all") ? " SELECTED":"") . ">所有宅配商品</option>";
	$activity = "<option value='activity'" . (($_REQUEST['type'] == "activity") ? " SELECTED":"") . ">商品粉絲抽獎活動</option>";
	$type_list = "";
}
$search_bar = <<<EOD
		<table border=0 cellpadding="0" cellspacing="0" width="706">
			<tr>
				<td align="left">
					<form name="sForm" action="{$act}" STYLE="margin: 0px; padding: 0px;">
					<input type="hidden" id="tab" name="tab" value="{$tab}">
					<input type="hidden" id="pageno" name="pageno" value="{$_REQUEST['pageno']}">
					<table>
						<tr>
							<td style="color:white; font-size:11pt; line-height:20px{$hide}" id="area_label">地區</td>
							<td style="color:white; font-size:11pt; line-height:20px{$hide}" id="area_select">
								<select name="area" id="area"><option value="">所有地區</option>{$area_list}</select>
							</td>
							<td style="color:white; font-size:11pt; line-height:20px;padding-left:10px" id="type_label" class="type">類型</td>
							<td style="color:white; font-size:11pt; line-height:20px" id="type_select" class="type">
								<select name="type" id="type"><option value="">所有類型</option>{$type_all}{$type_list}{$activity}</select>
							</td>
							<td style="color:white; font-size:11pt; line-height:20px;padding-left:10px">分類</td>
							<td style="color:white; font-size:11pt; line-height:20px">
								<select name="catalog" id="catalog"><option value="">所有分類</option>{$catalog_list}</select>
							</td>
							<td style="padding-left:15px">
								<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td id="sort" onMouseOver="btnOver(this)" onMouseOut="btnOut(this)" onClick="Search(1);" style="background:url(./images/btn_sort.gif); background-repeat:no-repeat; background-position:center center; width:85px; height:27px; cursor:pointer">&nbsp;</td>
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
		</table>
		<div id="option1" style="display:none"><option value="">所有類型</option>{$type_all}{$type_list}{$activity}</div>
		<div id="option2" style="display:none"></div>
		<div id="option4" style="display:none"><option value="">所有類型</option>{$type_all}{$type_list}{$activity}</div>
		<div id="option5" style="display:none"><option value="">所有類型</option><option value='all'>所有宅配商品</option>{$activity}</div>
EOD;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<META NAME="description" content="InTimeGo即時服務網可偵測區域所有動態或靜態的商務行為，建立真正即時雙向互動的買賣商務模式;即購平台係針對一般傳統商店、行動商店 (包含攤販、行動販售車、補貨物流車...等)、運輸工具、以及人力與活動，進行即時搜尋服務的網路平台，對普羅大眾而言，這是一個能拉近供需距離與創造服務價值的網站，可以為買賣雙方創造更為便利與快速的商業關係。
 ">
 <meta name="keywords" content="即時服務, 物流查詢, 運輸共乘, 即時人力, 即時活動, 安全監護, 雲端服務"/>
<title>InTimeGo 即時服務-賣家<?=$member['Nick']?>的商品/服務集合</title>
<link type="text/css" href="js/themes/base/ui.all.css" rel="stylesheet" />
<link type="text/css" href="style.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="js/jquery.colorbox.css" media="screen" />
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.blockUI.js"></script>
<script type="text/javascript" src="js/jquery.colorbox.js"></script>
<script language="javascript" src="./js/facebook_show.js"></script>
<script src="./js/oslide.js" language="javascript" type="text/javascript"></script>
<script src="./js/easing.js" language="javascript" type="text/javascript"></script>
<script language="javascript" src="./js/scrollbar.js"></script>
<style>
.tab1{
	color:black;
	height:40px;
	cursor:pointer;
}

.tab1_over{
	color:red;
	height:40px;
	cursor:pointer;
}
.tab1_selected{
	height:40px;
	color:white;
	cursor:pointer;
}

.tab1_space{
	width:20px;
}
</style>
<body style="margin:0">
<?

include './include/db_close.php';
?>
<center>
	<div>
		<table width="100%" border=0 style="background:#98cd01;height:40px;">
			<tr>
				<td style="width:100px;">&nbsp;</td>
				<td style="text-align:center">賣家<?=$member['Nick']?>的商品/服務集合</td>
				<td style="width:100px; text-align:right; padding-right:10px">&nbsp;</td>
			</tr>
		</table>
	</div>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td style="background:#98cd01;height:40px; vertical-align:bottom; text-align:center">
				<table style="" cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td id="tab1" class="tab" style="width:100px;border-right:solid 3px #98cd01; cursor:pointer; background:url('./images/tab1.gif'); background-repeat:no-repeat; background-position:center center" onMouseOver="mmOver(this, 1);" onMouseOut="mmOut(this, 1);" onClick="mmClk(this, 1);">&nbsp;</td>
						<td id="tab2" class="tab" style="width:100px;border-right:solid 3px #98cd01; cursor:pointer; background:url('./images/tab2.gif'); background-repeat:no-repeat; background-position:center center" onMouseOver="mmOver(this, 2);" onMouseOut="mmOut(this, 2);" onClick="mmClk(this, 2);" id="<?=(($tab==2) ? "default":"")?>">&nbsp;</td>
						<td class="tab_marguee" style="border-right:solid 3px #98cd01" align="center">
							<div id='marquee' style="; height:25px; width:310px; padding-left:5px; padding-right:5px; padding-top:5px; padding-bottom:5px; overflow:hidden">
								<?
								include 'include/db_open.php';
								$result = mysql_query("SELECT * FROM Page WHERE useFor = 'MARQUEE'");
								$i = 0;
								if($rs = mysql_fetch_array($result)){
									$i++;
									echo $rs['Content'];
								}
								include 'include/db_close.php';
								?>
							</div>
						</td>
						<td id="tab4" class="tab" style="width:100px;border-right:solid 3px #98cd01; cursor:pointer; background:url('./images/tab4.gif'); background-repeat:no-repeat; background-position:center center" onMouseOver="mmOver(this, 4);" onMouseOut="mmOut(this, 4);" onClick="mmClk(this, 4);" id="<?=(($tab==4) ? "default":"")?>">&nbsp;</td>
						<td id="tab5" class="tab" style="width:100px; cursor:pointer; background:url('./images/tab5.gif'); background-repeat:no-repeat; background-position:center center" onMouseOver="mmOver(this, 5);" onMouseOut="mmOut(this, 5);" onClick="mmClk(this, 5);" id="<?=(($tab==5) ? "default":"")?>">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="background:#525552;padding:10px"><?=$search_bar?></td>
		</tr>
		<tr>
			<td style="background:#525552; padding:10px;padding-right:0px; padding-top:0px;">
			<!--div id="products" style="background:white; height:400px; overflow:auto"></div-->
			<iframe name="pList" style="background:white; width:735px; height:400px; overflow-x:hidden; border:0px none" frameborder="0" marginwidth="0" marginheight="0"></iframe>
			</td>
		</tr>
	</table>
	
</center>

<script language="javascript">
var s = "";
function mmClk(x, y){
	if(s != ""){
		document.getElementById("tab"+s).style.backgroundImage = "url('./images/tab" + s + ".gif')";
	}
	s = y;
	document.getElementById("tab"+y).style.backgroundImage = "url('./images/tab" + y + "_selected.gif')";
	$("#tab").val(y);
	$("#type").html($("#option" + y).html());
	switch(y){
		case 1:
			$(".type").show();
			break;
		case 2:
			$(".type").hide();
			break;
		case 4:
			$(".type").show();
			break;
		case 5:
			$(".type").show();
			break;
	}
	Search(1);
}
function mmOver(x, n){
	if(n != s){
		document.getElementById("tab"+n).style.backgroundImage = "url('./images/tab" + n + "_over.gif')";
	}
}
function mmOut(x, n){
	if(n != s){
		document.getElementById("tab"+n).style.backgroundImage = "url('./images/tab" + n + ".gif')";
	}
}

function btnOver(x){
	x.style.backgroundImage="url(./images/btn_" + x.id + "_over.gif)";
}

function btnOut(x){
	x.style.backgroundImage="url(./images/btn_" + x.id + ".gif)";
}

function Search(x){
	$("#products").html("<center><img src='./images/loading.gif'><br>載入中，請稍待!</center>");
	var t = new Date();
	var url = "";
	url = "seller_products_list.php?pageno=" + x + "&id=<?=$id?>&catalog=" + $('#catalog').val() + "&tab=" + $("#tab").val() + "&time=" + t.getTime();

	if($("#tab").val() == "1" || $("#tab").val() == "4"){
		url += "&area=" + $('#area').val() + "&type=" + $('#type').val();
		$("#area_label").show();
		$("#area_select").show();
//		$("#type_label").show();
//		$("#type_select").show();
	}
	else{
		$("#area_label").hide();
		$("#area_select").hide();
//		$("#type_label").hide();
//		$("#type_select").hide();
	}
	//alert(url);
	//$("#products").load(url);
	pList.location.href=url;
}

function setPage(x){
	Search(x);
}
</script>

<script language="javascript">
	document.getElementById("tab<?=$tab?>").click();
</script>
