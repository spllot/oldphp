<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

include 'seller_product_tab.php';

include './include/db_open.php';
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}
include './include/db_close.php';

if($_SESSION['member']['Seller'] != 2){
	$dis_cashflow = " disabled";
}

$WEB_CONTENT = <<<EOD

<table style="width:100%" cellpadding="0" cellspacing="0" border=0>
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td style="text-align:center" align="center">
		<center>
			<table width="600">
				<tr>
					<td style="border-bottom:solid 2px gray; height:40px; line-height:40px; font-weight:bold; font-size:12pt">選擇新商品&服務提案類別</td>
				</tr>
				<tr>
					<td>
						<form name="iForm" method="post">
						<table align="center">
							<tr style="height:40px; text-align:left">
								<td><input type="radio" name="mode" vlaue="1" disabled>本地/宅配團購_商品建置(金流服務)</td>
							</tr>
							<tr style="height:40px; text-align:left">
								<td><input type="radio" name="mode" value="2">本地/宅配服務_商品建置(金流/非金流服務)</td>
							</tr>
							<tr style="height:40px; text-align:left">
								<td><input type="radio" name="mode" value="3" disabled>本地/宅配服務_粉絲推廣服務建置(非金流服務)</td>
							</tr>
							<tr style="height:40px; text-align:left">
								<td><input type="radio" name="mode" value="4">本地服務_即時運輸/共乘服務建置(非金流服務)</td>
							</tr>
							<tr style="height:40px; text-align:left">
								<td><input type="radio" name="mode" value="5">本地服務_即時人力服務建置(非金流服務)</td>
							</tr>
							<tr style="height:40px; text-align:left">
								<td><input type="radio" name="mode" value="6">本地服務_即時活動服務建置(非金流服務)</td>
							</tr>
						</table>
						</form>
					</td>
				</tr>
				<tr>
					<td style="border-top:solid 2px gray">&nbsp;</td>
				</tr>
				<tr>
					<td style="color:gray">
					</td>
				</tr>
				<tr>
					<td style="text-align:center"><input type="button" value="確定" class="btn" onClick="Save();" style="width:85px"></td>
				</tr>
				<tr>
					<td>
						<br>
						<table>
							<tr>
								<td style="vertial-align:top; text-align:right; font-size:11pt; color:gray" align="right" valign="top" nowrap>說明(1).</td>
								<td style="text-align:left; font-size:11pt; color:gray" align="left">即購網一般會員可以直接建置「非金流商品」服務，「非金流商品」上架販售與成交不會被本站抽取任何費用，但其帳款需商家本身向商品買家收取，無法得到本站收款服務，唯有成為金流賣家會員方可在本站建置「金流商品」，得到本站刷卡及虛擬帳號收款服務。</td>
							</tr>
							<tr>
								<td style="vertial-align:top; text-align:right; font-size:11pt; color:gray" align="right" valign="top" nowrap>說明(2).</td>
								<td style="text-align:left; font-size:11pt; color:gray" align="left">關於新的商品&服務提案填寫說明，可以連結參考<a href="{$_CONFIG['urlF']}" target="_blank">新商品&服務提案說明</a>。</td>
							</tr>
						</table> 
					</td>
				</tr>
			</table>
 		</td>
	</tr>
</table>


EOD;

include 'template2.php';
?>

<script language="javascript">
function Save(){
	if(!iForm.mode[0].checked && !iForm.mode[1].checked && !iForm.mode[2].checked && !iForm.mode[3].checked && !iForm.mode[4].checked && !iForm.mode[5].checked){
		alert("請選擇商品類別!");
	}
	else{
		window.location.href="seller_product_step" + ((iForm.mode[0].checked) ? "2" : "3") + ".php?option=" + $('input[name=mode]:checked').val();
	}
}
</script>