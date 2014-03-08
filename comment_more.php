<?php
include './include/session.php';
include './include/db_open.php';
$id = $_REQUEST['product'];

$sql = "SELECT SUM(Rating) AS R, Count(*) AS C FROM logComment WHERE Question=0 AND transactionNo = '$id'";
$result = mysql_query($sql) or die(mysql_error());
$av=0;
if($rs=mysql_fetch_array($result)){
	if($rs['C'] > 0){
		$av = number_format(round($rs['R']/$rs['C'], 1), 1);
	}
}




$sql = "SELECT *, (SELECT Name FROM Product WHERE No = logComment.transactionNo) AS pName, (SELECT Nick FROM Member WHERE No = logComment.rateBy) AS rName FROM logComment WHERE transactionNo = '$id' order by dateRated desc";
$result = mysql_query($sql) or die(mysql_error());



$product = <<<EOD

									<table style="width:100%">
										<tr>
											<td style="width:100px; line-height:22px; background:#b5b2b5;text-align:center">作者</td>
											<td style="; line-height:22px; background:#b5b2b5;text-align:center">留言內容</td>
								<td style="width:120px; line-height:30px; background:#b5b2b5;text-align:center">評分 ({$av} av)</td>
											<td style="width:120px; line-height:22px; background:#b5b2b5;text-align:center">留言時間</td>
										</tr>
										<tr style="height:11px"></tr>
EOD;
$bg = array("#FFFFFF", "#FFFFFF");
$i=0;
while($rs = mysql_fetch_array($result)){
	$content = str_replace("\n", "<br>", $rs['Content']);
	$reply = str_replace("\n", "<br>", $rs['Reply']);
	$datereply=(($rs['dateReplied']=="0000-00-00 00:00:00") ? "尚未":$rs['dateReplied']);
	$bgcolor=$bg[$i%2];
	$name = mb_substr($rs['rName'], 0, 1, "UTF-8") . str_pad("", mb_strlen($rs['rName'], "UTF-8")-1 , "X");
	$name = $rs['rName'];
	if($rs['Question'] == 1){
		$product .= <<<EOD
			<tr style="background:{$bgcolor}; height:40px">
				<td style="text-align:center" rowspan="2">{$name}</td>
				<td style="text-align:left; color:blue">詢問：{$content}</td>
				<td></td>
				<td style="text-align:center; font-size:12px">{$rs['dateRated']}</td>
			</tr>
			<tr style="background:{$bgcolor}; height:40px">
				<td style="text-align:left; color:blue">商家回覆：{$reply}</td>
				<td></td>
				<td style="text-align:center; font-size:12px">{$datereply}</td>
			</tr>
			<tr style="height:11px"></tr>
EOD;
	}
	else{
		$rat = number_format($rs['Rating'], 1);
		$start = my_round($rs['Rating'], 0) . 'stars.gif';
		$product .= <<<EOD
			<tr style="background:{$bgcolor}; height:40px">
				<td style="text-align:center">{$name}</td>
				<td style="text-align:left; color:blue">評論：{$content}</td>
				<td style="color:#E7711B; text-align:center">{$rat}&nbsp;&nbsp;<img src='./images/{$start}'></td>
				<td style="text-align:center; font-size:12px">{$rs['dateRated']}</td>
			</tr>
			<tr style="height:11px"></tr>
EOD;
	}
	$i++;
}

$product .= "</table>";

$content = str_replace("\n", "<br>", $product['Content']);
switch($product['Quality']){
	case -1:
		$rate = "待改進(-1)";
		break;
	case 0:
		$rate = "普通(0)";
		break;
	case 1:
		$rate = "優良(+1)";
		break;
}
include './include/db_close.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo—檢視商品詢問與評論內容</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<center>
<table style="width:660px" cellpadding="5" cellspacing="0">
	<tr style="height:30px">
		<td style="text-align:left; font-weight:bold; color:white; background:gray" align="left">檢視商品詢問與評論內容</td>
	</tr>
	<tr>
		<td>
		<?=$product?>
		</td>
	</tr>
	<tr>
		<td align="center"><hr>
			<input type="button" value="關閉" onClick="parent.$.fn.colorbox.close();" style="width:90px; height:30px">
		</td>
	</tr>
</table>

</center>

<script language="javascript">
	function Save(){
		var rForm = document.rForm;
		if(!rForm.rate[0].checked && !rForm.rate[1].checked && !rForm.rate[2].checked){
			alert("請勾選評價!");
		}
		else if(!rForm.content.value){
			alert("請輸入留言內容!");
			rForm.content.focus();
		}
		else{
			rForm.submit();
		}
	}
</script>