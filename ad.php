<?php
//$usefor = $_REQUEST['usefor'];
//$usefor = basename($_SERVER['PHP_SELF']);
//$usefor = strtoupper(substr($usefor, 0, 8));
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}

$sql = "SELECT * FROM AD2 WHERE 1=1";
$sql .= ((substr($usefor, 0, 7) == "PRODUCT") ? " AND useFor='$usefor'" : "");
$sql .= (($_REQUEST['catalog'] != "") ? " AND Catalog = '" . $_REQUEST['catalog'] . "'" : "");
$sql .= " AND (Member=0 OR (Member > 0 AND dateExpire > CURRENT_TIMESTAMP)) order by dateSubmit DESC, Sort";
$result = mysql_query($sql);
$ad2 = "<table width='670' align='center' cellpadding='0' cellspacing='0' border=0>\n";
$ad2 .= "<tr>\n";
$i = 0;
while($rs = mysql_fetch_array($result)){
	$pics = "/images/none.png";
	if($rs['Src'] == 1){
		$pics = $rs['Link'];
	}

	if($rs['Src'] == 2){
		$pics = "/upload/{$rs['Icon']}";
	}
	$tmp = substr(strtolower($rs['Url']), 0, 7);
	
	if(mb_strlen($rs['Caption'], 'utf8') > 9){
		$caption = mb_substr($rs['Caption'], 0, 8, 'utf8') . "…" ;
	}
	else{
		$caption = mb_substr($rs['Caption'], 0, 9, 'utf8');
	}
	$caption = "<div style='text-align:center; line-height:22px; font-weight:bold' title='{$rs['Caption']}'>【{$caption}】</div><table cellpadding=0 cellspacing=0 border=0 width=143><tr><td style='font-size:12pt; font-weight:bold'>↘" . (float)(number_format($rs['Discount'],1)) . "折</td><td style='text-align:right; padding-right:2px'><img src='./images/btn_buy.png' border=0></td></tr></table>";
	
	$ad2 .= "<td align='center' valign='top'>
	<div style='width:143px; height:136px; overflow:hidden; text-align:center; border-right:dashed 0px gray'>
		<div style='width:139px; height:77px; overflow:hidden; border:solid 1px #CCCCCC'><a href='{$rs['Url']}' target='" . (($tmp == "http://") ? "_blank" : "") . "' style='color:#D6CF00'><img src='{$pics}' border='0' style='width:139px; height:77px'></a></div>
		<div style='padding-top: 3px; padding-bottom: 5px; text-align:left'><a href='{$rs['Url']}' target='" . (($tmp == "http://") ? "_blank" : "") . "' style='color:#ca2627; text-decoration:none; font-size:12px'>{$caption}</a></div>
	</div>
	</td>\n";
	$ad2 .= "<td><img src='../images/line_ad2.png'></td>\n";
	$i++;
}

$limit = (($_CONFIG['ad2_auto'] == "") ? '0':$_CONFIG['ad2_auto']);

$sql = "SELECT Product.* FROM Product INNER JOIN Member ON Member.No=Product.Member WHERE Product.Status=2 AND Product.Cashflow=1 AND dateClose > CURRENT_TIMESTAMP";
//echo substr($usefor, 7, 1);
switch(substr($usefor, 7, 1)){
	case 1:
		$sql .= " AND Deliver=0 AND Mode=1";
		break;
	case 2:
		$sql .= " AND Deliver=1 AND Mode=1";
		break;
	case 4:
		$sql .= " AND Deliver=0 AND Mode=2";
		break;
	case 5:
		$sql .= " AND Deliver=1 AND Mode=2";
		break;
}
$sql .= (($_REQUEST['catalog'] != "") ? " AND Product.Catalog = '" . $_REQUEST['catalog'] . "'" : "");
$sql .= " ORDER BY Product.Price1 DESC, Member.Level DESC LIMIT $limit";
//echo $sql;
$result = mysql_query($sql);
while($rs = mysql_fetch_array($result)){
	$discount = (float)(number_format($rs['Discount'],1));
	if($discount <= 0){
		$discount = "免費";
	}
	else if($discount >= 10){
		$discount = "-- 折";
	}
	else{
		$discount = $discount . "折";
	}
	if($rs['Mode'] == 1){
		if($rs['Deliver'] == 0){
			$type=1;
		}
		if($rs['Deliver'] == 1){
			$type=2;
		}
	}
	if($rs['Mode'] == 2){
		if($rs['Deliver'] == 0){
			$type=4;
		}
		if($rs['Deliver'] == 1){
			$type=5;
		}
	}
	$url = "product{$type}_detail.php?no={$rs['No']}";

	if(mb_strlen($rs['Name'], 'utf8') > 9){
		$caption = mb_substr($rs['Name'], 0, 8, 'utf8') . "…" ;
	}
	else{
		$caption = mb_substr($rs['Name'], 0, 9, 'utf8');
	}
	$caption = "<div style='text-align:center; line-height:22px; font-weight:bold' title='{$rs['Name']}'>【{$caption}】</div><table cellpadding=0 cellspacing=0 border=0 width=143><tr><td style='font-size:12pt; font-weight:bold'>↘{$discount}</td><td style='text-align:right; padding-right:2px'><img src='./images/btn_buy.png' border=0></td></tr></table>";
	
	$ad2 .= "<td align='center' valign='top'>
	<div style='width:143px; height:136px; overflow:hidden; text-align:center; border-right:dashed 0px gray'>
		<div style='width:139px; height:77px; overflow:hidden; border:solid 1px #CCCCCC'><a href='{$ur}' target='" . (($tmp == "http://") ? "_blank" : "") . "' style='color:#ca2627; text-decoration:none'><img src='./upload/{$rs['Photo']}' border='0' style='width:139px;'></a></div>
		<div style='padding-top: 3px; padding-bottom: 5px; text-align:left'><a href='{$ur}' target='" . (($tmp == "http://") ? "_blank" : "") . "' style='color:#ca2627; text-decoration:none; font-size:12px'>{$caption}</a></div>
	</div>
	</td>\n";
	$ad2 .= "<td><img src='../images/line_ad2.png'></td>\n";
}



if($i < 6){
	for($j=$i; $j<6; $j++){
		$ad2 .= "<td style='width:139px;'>&nbsp;</td>\n";
	}
}
$ad2 .= "</tr>\n";
$ad2 .= "</table>\n";
include './include/db_close.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script language="javascript" src="./js/scrollbar2.js"></script>
<body topmargin="0" leftmargin="0">
<div style="width:670px; height:224px; background:url('./images/bg_ad2.png');">
	<table style="width:670px; height:224px">
		<tr>
			<td style="padding-top:45px; padding-left:30px; padding-right:30px">
				<div id="scrollbarDemo2" style="width:605px"><?=$ad2?></DIV>
			</td>
		</tr>
	</table>
</div>
</body>
<SCRIPT type="text/javascript">
var scrollBarControl2 = new scrollBar2();

	function MoveTo2(d){
	　scrollBarControl2.clear();
	　scrollBarControl2.addBar("scrollbarDemo2", 605, 140, <?=$_CONFIG['ad2']?>, d);
	　scrollBarControl2.createScrollBars();
	}

MoveTo2("left");
</SCRIPT>