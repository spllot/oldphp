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
$result = mysql_query("SELECT * FROM Config WHERE ID='fee3'");
if($rs = mysql_fetch_array($result)){
	$fee3 = $rs['YN'];
}
$result = mysql_query("SELECT IFNULL(SUM(Amount), 0) FROM logTransaction WHERE Owner='" . $_SESSION['member']['userID'] . "'");
list($money_total) = mysql_fetch_row($result);

include './include/db_close.php';

$btn = '<input type="button" value="您的會員等級未達' . $exp_level . '級無法申請匯出" disabled class="btn">';

if($_SESSION['member']['Level'] >= $exp_level){
	$btn = '<input type="button" value="匯出申請(需同意下列說明)" onClick="Save();" class="btn" style="">';
}
if($money_total < 1000){
	$btn = '<input type="button" value="您的儲值金餘額('. $money_total . ')不足，無法申請匯出" disabled class="btn">';
}

include 'member_transaction_tab.php';
$WEB_CONTENT = <<<EOD

<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td><br><Br><center>
<form name="iForm" method="post" target="iAction" action="member_transaction_exp_save.php">
<input type="hidden" name="latitude" value="">
<table>
	<tr>
		<td style="text-align:right" nowrap>匯款帳號：</td>
		<td style="text-align:left">此為會員得到儲值金之收款之帳號</td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>銀行名稱：</td>
		<td style="text-align:left"><input type="text" style="width:235px" name="bank" value="{$_SESSION['member']['Bank']}"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>分支行名稱：</td>
		<td style="text-align:left"><input type="text" style="width:235px" name="branch" value="{$_SESSION['member']['Branch']}"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>用戶帳號：</td>
		<td style="text-align:left"><input type="text" style="width:235px" name="account" value="{$_SESSION['member']['Account']}"><font color=red>*</font></td>
	</tr>
	<tr>
		<td style="text-align:right" nowrap>儲值金匯出金額：</td>
		<td style="text-align:left"><input type="text" style="width:235px" name="amount" value=""><font color=red>*</font></td>
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
				<td style="vertical-align:top; color:gray" valign="top" align="left">會員存款帳號非永豐銀行客戶，儲值金匯出需另外扣除$ {$fee3}</font>元匯費，所以注意匯出之金額須加上此匯費，否則將從金額中扣除</td>
			</tr>
			<tr>
				<td></td>
				<td style="vertical-align:top; color:gray" >(2).</td>
				<td style="vertical-align:top; color:gray"  align="left">本站匯款日固定於每月的第一個星期一</font>處理，但可能因連續假期，而有誤差而遞延匯款</td>
			</tr>
			<tr>
				<td></td>
				<td style="vertical-align:top; color:gray" >(3).</td>
				<td style="vertical-align:top; color:gray"  align="left">會員等級到達{$exp_level}</font>級以上者，可以將所得之儲值金點數以現金匯出，會員等級未達{$exp_level}</font>級以上者，儲值金點數僅作為抵用商品消費之用</td>
			</tr>
			<tr>
				<td></td>
				<td style="vertical-align:top; color:gray" >(4).</td>
				<td style="vertical-align:top; color:gray"  align="left">匯出之金額不得低於1000</font>元，以減少本站作業程序</td>
			</tr>
			<tr>
				<td></td>
				<td style="vertical-align:top; color:gray" >(5).</td>
				<td style="vertical-align:top; color:gray"  align="left">為保障會員資訊安全，凡申請儲值金匯出動作，系統皆會發出電子郵件確認信後，由會員確認後方可生效。</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>		</center>
		</td>
	</tr>
</table>


EOD;

include 'template2.php';
?>
<script language="javascript">parent.setUserInfo();</script>
<script language="javascript">
function Save(){
	if(!iForm.bank.value){
		alert("請輸入銀行名稱!");
		iForm.bank.focus();
	}
	else if(!iForm.branch.value){
		alert("請輸入分支行名稱!");
		iForm.branch.focus();
	}
	else if(!iForm.account.value){
		alert("請輸入用戶帳號!");
		iForm.account.focus();
	}
	else if(!iForm.amount.value){
		alert("請輸入匯出金額!");
		iForm.amount.focus();
	}
	else if(isNaN(iForm.amount.value)){
		alert("匯出金額請輸入數字!");
		iForm.amount.focus();
	}
	else if(parseInt(iForm.amount.value, 10) < 1000){
		alert("匯出之金額不得低於1000元!");
		iForm.amount.focus();
	}
	else{
		if(confirm("確定要送出?\n\n基於安全理由，系統將發確認信至您的信箱，您必須點選信件中的確認網址才能完成申請動作!")){
			iForm.submit();
		}
	}
}

</script>
