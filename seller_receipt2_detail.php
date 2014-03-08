<?php
$coupon=$_REQUEST['coupon'];
$id=$_REQUEST['id'];
include './include/db_open.php';
if($coupon == "1"){
	$result = mysql_query("SELECT logReceiptSMS.Type, logReceiptSMS.Title, logReceiptSMS.Amount, Year(logReceiptSMS.dateCreate) AS Year, Month(logReceiptSMS.dateCreate) AS Month, Day(logReceiptSMS.dateCreate) AS Day, Member.rName, Member.rAddress, Member.uniNo FROM logReceiptSMS INNER JOIN Member ON Member.No=logReceiptSMS.Member WHERE ID='$id'") or die(mysql_error());
	$receipt=mysql_fetch_array($result);
	$item = "優惠憑證簡訊費用";
}
else{
	$result = mysql_query("SELECT logReceipt.Type, logReceipt.Title, logReceipt.Amount, Year(logReceipt.dateCreate) AS Year, Month(logReceipt.dateCreate) AS Month, Day(logReceipt.dateCreate) AS Day, Member.rName, Member.rAddress, Member.uniNo FROM logReceipt INNER JOIN Member ON Member.No=logReceipt.Seller WHERE ID='$id'") or die(mysql_error());
	$receipt=mysql_fetch_array($result);
	$item = "商品販售手續費";
}

if($receipt['Title'] == 1){
	$unino = $receipt['uniNo'];
	$address = $receipt['rAddress'];
}
$digit=array("零", "壹", "貳", "參", "肆", "伍", "陸", "柒", "捌", "玖");

$amount = str_pad($receipt['Amount'], 8, "0", STR_PAD_LEFT);

for($i=0; $i<strlen($amount); $i++){
	$num[$i] = $digit[$amount[$i]];
}

include './include/db_close.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo—發票明細</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
body{
	font-size:12px;
	color:#6666ff;
}

</style>
<table style="width:660px; height:400px; border: solid 2px #6666ff" cellpadding="0" cellspacing="0" align='center'>
	<tr>
		<td style="text-align:center; font-size:12px">
			<span style="font-weight:bold; font-size:14px">吉達資訊科技股份有限公司
			&nbsp;&nbsp;&nbsp;&nbsp;
			
			電子計算機統一發票</span>
		</td>
	</tr>
	<tr>
		<td style="padding-left:5px; padding-right:5px">
			<table width="100%">
				<tr>
					<td width="50%" style="text-align:left; font-size:12px">
						發票號碼：<span style="color:black"><?=$id?></span><br>
						買&nbsp;&nbsp;受&nbsp;&nbsp;人：<span style="color:black"><?=$receipt['rName']?></span><br>
						統一編號：<span style="color:black"><?=$unino?></span><br>
						地　　址：<span style="color:black"><?=$address?></span><br>
					</td>
					<td width="50%" style="text-align:right; vertical-align:bottom; font-size:12px">
					<table cellpadding="0" cellspacing="0" border="0" align="right">
						<tr>
							<td>中華民國</td>
							<td style="width:40px; text-align:center; color:black"><?=$receipt['Year']?></td>
							<td>年</td>
							<td style="width:20px; text-align:center; color:black"><?=$receipt['Month']?></td>
							<td>月</td>
							<td style="width:20px; text-align:center; color:black"><?=$receipt['Day']?></td>
							<td>日</td>  
						</tr>
					</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding-left:5px; padding-right:5px; vertical-align:top">
			<table cellpadding="1" cellspacing="1" style="background:#6666ff; width:100%" align="center">
				<tr>
					<td style="background:#FFFFFF" align="center" colspan="2">品　　　名</td>
					<td style="background:#FFFFFF; width:60px" align="center">數　量</td>
					<td style="background:#FFFFFF; width:60px" align="center">單　價</td>
					<td style="background:#FFFFFF; width:60px" align="center">金　額</td>
					<td style="background:#FFFFFF; width:150px" align="center">備　註</td>
				</tr>
				<tr>
					<td style="background:#FFFFFF; vertical-align:top; color:black" colspan="2"><?=$item?></td>
					<td style="background:#FFFFFF; vertical-align:top; color:black">&nbsp;</td>
					<td style="background:#FFFFFF; vertical-align:top; color:black">&nbsp;</td>
					<td style="background:#FFFFFF; vertical-align:top; color:black; text-align:right; padding:2px"><?=$receipt['Amount']?></td>
					<td style="background:#FFFFFF; height:100px">&nbsp;</td>
				</tr>
				<tr>
					<td style="background:#FFFFFF" colspan="4" align="center">銷　售　額　合　計</td>
					<td style="background:#FFFFFF; color:black; text-align:right; padding:2px"><?=$receipt['Amount']?></td>
					<td style="background:#FFFFFF" align="center">營業人統一發票專用章</td>
				</tr>
				<tr>
					<td style="background:#FFFFFF" rowspan="2" align="center">營　業　稅</td>
					<td style="background:#FFFFFF; width:60px" align="center">應　稅</td>
					<td style="background:#FFFFFF; width:60px" align="center">零 稅 率</td>
					<td style="background:#FFFFFF; width:60px" align="center">免　稅</td>
					<td style="background:#FFFFFF">&nbsp;</td>
					<td style="background:#FFFFFF;" rowspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td style="background:#FFFFFF; color:black; text-align:center">V</td>
					<td style="background:#FFFFFF; color:black">&nbsp;</td>
					<td style="background:#FFFFFF; color:black">&nbsp;</td>
					<td style="background:#FFFFFF; color:black">&nbsp;</td>
				</tr>
				<tr>
					<td style="background:#FFFFFF" colspan="4" align="center">總　　　　　　　計</td>
					<td style="background:#FFFFFF; color:black; text-align:right; padding:2px"><?=$receipt['Amount']?></td>
				</tr>
				<tr>
					<td style="background:#FFFFFF" colspan="5">
					<table>
						<tr>
							<td nowrap>總計新台幣<br>(中文大寫)</td>
							<td style="width:40px; color:black; text-align:center"><?=$num[0]?></td>
							<td>仟<br>萬</td>
							<td style="width:40px; color:black; text-align:center"><?=$num[1]?></td>
							<td>佰<br>萬</td>
							<td style="width:40px; color:black; text-align:center"><?=$num[2]?></td>
							<td>拾<br>萬</td>
							<td style="width:40px; color:black; text-align:center"><?=$num[3]?></td>
							<td>萬</td>
							<td style="width:40px; color:black; text-align:center"><?=$num[4]?></td>
							<td>仟</td>
							<td style="width:40px; color:black; text-align:center"><?=$num[5]?></td>
							<td>佰</td>
							<td style="width:40px; color:black; text-align:center"><?=$num[6]?></td>
							<td>拾</td>
							<td style="width:40px; color:black; text-align:center"><?=$num[7]?></td>
							<td nowrap>元整</td>
						</tr>
					</table>
					
					</td>
				</tr>
			</table>		
		</td>
	</tr>
	<tr style="display:none">
		<td style="text-align:left; font-size:12px; padding-left:5px; padding-right:5px">
		*應稅、零稅、免稅之銷售額應分別開立統一發票，並應於各該欄位打「V」<br>
		本發票-台北市稅捐稽徵處91年03月01日北市稽大安(甲)字第09160866000號函核淮使用
		</td>
	</tr>
</table>