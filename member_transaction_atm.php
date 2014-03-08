<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

include './include/db_open.php';
$exp_level = 99;
$result = mysql_query("SELECT * FROM Config WHERE ID='exp'");
if($rs = mysql_fetch_array($result)){
	$exp_level = $rs['YN'];
}
$result = mysql_query("SELECT IFNULL(SUM(Amount), 0) FROM logTransaction WHERE Owner='" . $_SESSION['member']['userID'] . "'");
list($money_total) = mysql_fetch_row($result);

include './include/db_close.php';

$btn = '<input type="button" value="匯款回報" class="btn" onClick="Save();">';

include 'member_transaction_tab.php';
$WEB_CONTENT = <<<EOD

<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td>
<br>
<Br>
<center>
<table cellpadding="0" cellspacing="0" style="border:solid 1px gray; width:300px">
	<tr>
		<Td colspan="2" style="text-align:center; padding:2px; background:#eeeeee; border-bottom:solid 1px gray">ATM轉入帳號</td>
	</tr>
	<tr>
		<Td colspan="2" style="text-align:center; color:#ff6600; padding:5px; background:#dfdfff">
			銀行代碼: 822 (中國信託商業銀行)<Br>
			帳號: 034-540194086
		</td>
	</tr>
</table>

</center>
<center>
<br>
<Br>
<form name="iForm" method="post" target="iAction" action="member_transaction_atm_save.php">
<input type="hidden" name="latitude" value="">
<table>
	<tr>
		<Td colspan="2" style="text-align:center">儲值金完成匯款回報</td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>儲值金匯入金額：</td>
		<td style="text-align:left"><input type="text" style="width:235px" name="amount" value=""><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>你的帳戶後五碼：</td>
		<td style="text-align:left"><input type="text" style="width:235px" name="account" value="" maxlength=5><font color=red>*&nbsp;(請看存褶上的帳號)</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>你的銀行代碼：</td>
		<td style="text-align:left"><input type="text" style="width:235px" name="bank" value="" maxlength=3><font color=red>*&nbsp;(銀行代碼只有三碼)</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>匯款日期時間：</td>
		<td style="text-align:left"><input type="text" style="width:235px" id="date" name="date" value=""><font color=red>*&nbsp;(請務必留 月+日+時)</font></td>
	</tr>
	<tr>
		<td colspan="2" align="center" style="padding-top:30px">
		{$btn}
		</td>
	</tr>
	<tr>
		<td colspan="2"><br>
		<table width="600">
			<tr>
				<td style="width:60px;vertical-align:top; color:gray" valign="top">[說明]:</td>
				<td style="width:20px;vertical-align:top; color:gray" valign="top">(1).</td>
				<td style="vertical-align:top; color:gray" valign="top" align="left">若您尚未完成匯款，請勿任意發送 [匯款回報]。</td>
			</tr>
			<tr>
				<td></td>
				<td style="vertical-align:top; color:gray" >(2).</td>
				<td style="vertical-align:top; color:gray"  align="left">購買之儲值金匯款經由網站確認入帳，您的電子郵件將會收到確信件，此時您可以在 [儲值金匯入明細表中] 確認購入金額。</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>		</center>
		</td>
	</tr>
</table>
<br>
<Br>


EOD;

include 'template2.php';
?>
<script language="javascript">parent.setUserInfo();</script>
<script language="javascript">
	$('#date').datetimepicker({
		dateFormat: "yy-mm-dd",
		timeFormat: "HH:mm",
		closeText: '確定'
	});
</script>
<script language="javascript">
function Save(){
	if(!iForm.amount.value){
		alert("請輸入儲值金匯入金額!");
		iForm.amount.focus();
	}
	else if(isNaN(iForm.amount.value)){
		alert("匯入金額請輸入數字!");
		iForm.amount.focus();
	}
	else if(!iForm.account.value){
		alert("請輸入你的帳戶後五碼!");
		iForm.account.focus();
	}
	else if(iForm.account.value.length != 5){
		alert("帳戶請輸入五碼!");
		iForm.account.focus();
	}
	else if(!iForm.bank.value){
		alert("請輸入你的銀行代碼!");
		iForm.bank.focus();
	}
	else if(iForm.bank.value.length != 3){
		alert("銀行代碼請輸入三碼!");
		iForm.bank.focus();
	}
	else if(!iForm.date.value){
		alert("請輸入匯款日期時間!");
		iForm.date.focus();
	}
	else{
		iForm.submit();
	}
}

</script>
