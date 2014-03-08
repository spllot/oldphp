<?php
include './include/db_open.php';
if($seller != ""){
	$result = mysql_query("SELECT * FROM Member WHERE No='$seller'") or die(mysql_error());
	if($rs=mysql_fetch_array($result)){
		$seller_name = $rs['Nick'];
	}
}

include './include/db_close.php';
?>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
		<td id="tab1" class="tab" style="width:100px;border-right:solid 3px #98cd01; cursor:pointer; background:url('./images/tab1.gif'); background-repeat:no-repeat; background-position:center center" onClick="javascript:void(0);mClk(1, this, 'member_product1.php?seller=<?=$seller?>');" onMouseOver="mOvr(1, this)" onMouseOut="mOut(1, this);">&nbsp;</td>
		<td id="tab2" class="tab" style="width:100px;border-right:solid 3px #98cd01; cursor:pointer; background:url('./images/tab2.gif'); background-repeat:no-repeat; background-position:center center" onClick="javascript:void(0);mClk(2, this, 'member_product2.php?seller=<?=$seller?>');" onMouseOver="mOvr(2, this)" onMouseOut="mOut(2, this);">&nbsp;</td>
		<td class="tab_marguee" style="border-right:solid 3px #98cd01" align="center">
			<div style="color:red; line-height:30px; width:310px;overflow:hidden; border:solid 0px gray; font-size:13pt">
				商家：【<?=$seller_name?>】的商品/服務集合
			</div>
		</td>
		<td id="tab4" class="tab" style="width:100px;border-right:solid 3px #98cd01; cursor:pointer; background:url('./images/tab4.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(4, this, 'member_product4.php?seller=<?=$seller?>');" onMouseOver="mOvr(4, this)" onMouseOut="mOut(4, this);">&nbsp;</td>
		<td  id="tab5"class="tab" style="width:100px; cursor:pointer; background:url('./images/tab5.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(5, this, 'member_product5.php?seller=<?=$seller?>');" onMouseOver="mOvr(5, this)" onMouseOut="mOut(5, this);">&nbsp;</td>
	</tr>
</table>
<?
$tab = (($_REQUEST['tab'] != "") ? $_REQUEST['tab'] : "4");
?>
<script language="javascript">
	document.getElementById('tab<?=$tab?>').click();
</script>
<div style="display:none">
<img src="./images/tab1.gif">
<img src="./images/tab1_over.gif">
<img src="./images/tab1_selected.gif">
<img src="./images/tab2.gif">
<img src="./images/tab2_over.gif">
<img src="./images/tab2_selected.gif">
<img src="./images/tab4.gif">
<img src="./images/tab4_over.gif">
<img src="./images/tab4_selected.gif">
<img src="./images/tab5.gif">
<img src="./images/tab5_over.gif">
<img src="./images/tab5_selected.gif">
</div>