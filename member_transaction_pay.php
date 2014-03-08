<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
exit;
include 'member_transaction_tab.php';
$WEB_CONTENT = <<<EOD

<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center">
		<br><Br>
			<center>
			<form name="iForm" method="post">
			<table border=0>
				<tr>
					<td style="text-align:right" nowrap>儲值金匯入金額：</td>
					<td style="text-align:left; color:gray;"><input type="text" style="width:245px" name="amount" maxlength="10" value="" onKeyUp="setList();"><font color=red>*</font></td>
				</tr>
				<tr>
					<td style="text-align:right" nowrap>手機：</td>
					<td style="text-align:left"><input type="text" style="width:245px" name="phone" maxlength="10" value="{$_SESSION['member']['Phone']}"><font color=red>*</font>
					</td>
				</tr>
				<tr>
					<td style="text-align:right" nowrap>付款方式：</td>
					<td style="text-align:left">
					<select name="payby" onChange="setList();" style="width:250px">
						<option value="1">信用卡(Visa)，需加收3%金流手續費</option>
						<option value="2">Web ATM，需加收2%金流手續費</option>
						<option value="3">ATM轉帳，需加收0.5%金流手續費</option>
					</select>
					<font color=red>*</font></td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top:10px">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:120px; border-bottom:solid 2px gray; ">商品名稱</td>
							<td style="width:120px; border-bottom:solid 2px gray; ">單價</td>
							<td style="width:120px; border-bottom:solid 2px gray; ">數量</td>
							<td style="width:120px; border-bottom:solid 2px gray; ">總計</td>
							<td style="width:120px; border-bottom:solid 2px gray; ">需付<br>含金流手續費</td>
						</tr>
						<tr style="height:40px">
							<td style="; border-bottom:solid 2px gray">購買儲值金</td>
							<td style="; border-bottom:solid 2px gray"><div id="price">0</div></td>
							<td style="; border-bottom:solid 2px gray">1</td>
							<td style="; border-bottom:solid 2px gray"><div id="subtotal">0</div></td>
							<td style="; border-bottom:solid 2px gray"><div id="total">0</div></td>
						</tr>
					</table>
					</td>
				</tr>
				<tr><td></td>
					<td align="left" style="text-align:left; padding-top:10px; padding-bottom:10px">
						<input type="checkbox" name="agree" value="1">我已閱讀並願意遵守<a href="javascript:parent.Dialog('policy.php')">電子商務服務條款</a>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center" style="text-align:cente; padding-top:10px; padding-bottom:10px"><input type="button" value="儲值現金購買" onClick="Save();" class="btn"></td>
				</tr>
				<tr>
					<td colspan="2" align="left" style="text-align:left; padding-top:10px; padding-bottom:10px; ">

			<Table align="left" width="660">
				<tr>
					<td valign="top" width=60>[說明]：</td>
					<td valign="top" width=20>(1)</td>
					<td valign="top">金流系統購買儲值金, 須先扣繳金流手續費, 當您使用儲值金消費時,則不會再扣繳金流手續費。</td>
				</tR>
				<tr>
					<td></td>
					<td valign="top" width=20>(2)</td>
					<td valign="top">若您選擇使用ATM 轉帳，您將會經由E-mail 收到一組專屬的虛擬帳號，而您必須在期限內使用此帳號進行轉帳，轉帳期限為訂購當天晚上12點前，若超過時限轉帳就會失效。</td>
				</tR>
			</table>	
					</td>
				</tr>
			</table>	
			</center>	
			</form>
		</td>
	</tr>
</table>


EOD;

include 'template2.php';
?>
<script language="javascript">
var iForm  = document.iForm;
function Save(){
	if(!iForm.amount.value){
		alert("請輸入儲值金匯入金額!");
		iForm.amount.focus();
	}
	else if(!iForm.phone.value){
		alert("請輸入手機!");
		iForm.phone.focus();
	}
	else if(!iForm.payby.value){
		alert("請選擇付款方式!");
	}
	else if(!iForm.agree.checked){
		alert("請閱讀並同意遵守電子商務服務條款!");
	}
	else{
		iForm.target="iAction";
		iForm.action="member_transaction_pay_save.php";
		iForm.submit();
	}

}

function setList(){
	if(isNaN(iForm.amount.value)){
		alert("請輸入數字!");
		iForm.amount.value = "";
	}
	else if(parseInt(iForm.amount.value, 10) > 0){
		var fee=1;
		switch (iForm.payby.options.selectedIndex){
		case 0:
			fee = 1 + 0.03;
			break;
		case 1:
			fee = 1 + 0.02;
			break;
		case 2:
			fee = 1 + 0.005;
			break;
		}
		$("#price").html("$" + iForm.amount.value);
		$("#subtotal").html("$" + iForm.amount.value);
		$("#total").html("$" + Math.ceil(iForm.amount.value*fee));
	}
}

</script>
