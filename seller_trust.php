<?php
include './include/session.php';

include './include/db_open.php';
for($i=-1; $i<2; $i++){
	$trust['T'.$i] = 0;
	$month['T'.$i] = 0;
}
$result = mysql_query("SELECT *, (SELECT COUNT(*) FROM Product WHERE Member = Member.No AND Status = 2 AND dateClose > CURRENT_TIMESTAMP) AS Products FROM Member WHERE No='" . $_REQUEST['id'] . "'");
$member  = mysql_fetch_array($result);
$result = mysql_query("SELECT Quality FROM logRating WHERE Owner='" . $_REQUEST['id'] . "'");
while($rs=mysql_fetch_array($result)){
	$trust['T'.$rs['Quality']] ++;
}
$result = mysql_query("SELECT Quality FROM logRating WHERE Owner='" . $_REQUEST['id'] . "' AND dateRated >= DATE_ADD(NOW(), INTERVAL -30 DAY)");
while($rs=mysql_fetch_array($result)){
	$month['T'.$rs['Quality']] ++;
}
include './include/db_close.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo—會員升級標準</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<center>
<table style="width:660px" cellpadding="5" cellspacing="0">
	<tr style="height:30px">
		<td style="text-align:left; font-weight:bold; color:white; background:gray" align="left">商家 <?=$member['Nick']?> 服務評價積分</td>
	</tr>
	<tr style="height:70px">
		<td align="left">商家服務評價積分為買家對商家買賣信任度與穩定度之指標, 買家完成商品交易後, 系統將發mail 給買家, 進行對商家之評價, 此商家服務評價積分之高低, 表示商家一個月及半年內所維持的服務本質, 若買家超過一星期未做評價, 則系統自動給予商家優良之評價。</td>
	</tr>
	<tr style="height:30px">
		<td style="text-align:left" align="left">商家<?=$member['Nick']?> 目前販售商品數: <?=$member['Products']?></td>
	</tr>
	<tr style="height:30px">
		<td style="text-align:left" align="left">買家給商家 <?=$member['Nick']?> 的評價 = <?=$trust['T1']?> x1 + <?=$trust['T0']?> x0 + <?=$trust['T-1']?> x(-1) = <?=number_format($trust['T1']*1 + $trust['T0']*0 + $trust['T-1'] * -1)?></td>
	</tr>
	<tr><Td>&nbsp;</td></tr>
	<tr>
		<td align="left">
			<table style="width:400px" cellpadding="0" cellspacing="0" border="0">
				<tr style="height:30px">
					<td style="font-weight:bold;padding:5; border:solid 1px black; border-width: 1 1 1 1; text-align:center">評價項目</td>
					<td style="font-weight:bold;padding:5; border:solid 1px black; border-width: 1 1 1 0; text-align:center">一個月內</td>
					<td style="font-weight:bold;padding:5; border:solid 1px black; border-width: 1 1 1 0; text-align:center">全部紀錄</td>
				</tr>
				<tr style="height:30px">
					<td style="font-weight:bold;padding:5; border:solid 1px black; border-width: 0 1 1 1; text-align:center">優良(+1)</td>
					<td style="padding:5; border:solid 1px black; border-width: 0 1 1 0; text-align:center"><?=number_format($month['T1'])?></td>
					<td style="padding:5; border:solid 1px black; border-width: 0 1 1 0; text-align:center"><?=number_format($trust['T1'])?></td>
				</tr>
				<tr style="height:30px">
					<td style="font-weight:bold;padding:5; border:solid 1px black; border-width: 0 1 1 1; text-align:center">普通(0)</td>
					<td style="padding:5; border:solid 1px black; border-width: 0 1 1 0; text-align:center"><?=number_format($month['T0'])?></td>
					<td style="padding:5; border:solid 1px black; border-width: 0 1 1 0; text-align:center"><?=number_format($trust['T0'])?></td>
				</tr>
				<tr style="height:30px">
					<td style="font-weight:bold;padding:5; border:solid 1px black; border-width: 0 1 1 1; text-align:center">待改進(-1)</td>
					<td style="padding:5; border:solid 1px black; border-width: 0 1 1 0; text-align:center"><?=number_format($month['T-1'])?></td>
					<td style="padding:5; border:solid 1px black; border-width: 0 1 1 0; text-align:center"><?=number_format($trust['T-1'])?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><Td>&nbsp;</td></tr>
	<tr>
		<td align="left"><input type="button" value="確定" onClick="$.fn.colorbox.close();" style="width:120px"></td>
	</tr>
</table>

</center>