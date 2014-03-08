<?php
include './include/session.php';

include './include/db_open.php';
$area_list = "";
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_AREA' ORDER BY Sort");
while($rs=mysql_fetch_array($result)){
	$area_list .= "<option value='" . $rs['No'] . "'" . (($_REQUEST['area'] == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}

include './include/db_close.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
body{
	font-size:10pt;
}

</style>
<center>
<form name="iForm" method="post" action="supplier_save.php">
<input type="hidden" name="s">
<table style="width:660px" cellpadding="5" cellspacing="0">
	<tr style="height:30px">
		<td style="text-align:left; font-weight:bold; color:white; background:gray" align="left">協力合作廠商服務資訊輸入</td>
	</tr>
	<tr>
		<td align="left"><b>服務地區：</b><select id="area" name="area"><option value='0'>全國</option><?=$area_list?></select></td>
	</tr>
	<tr>
		<td align="left"><b>服務事項：</b><Br>
		<input type="checkbox" value="攝影" name="service">攝影&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="checkbox" value="文案" name="service">文案&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="checkbox" value="海報" name="service">海報&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="checkbox" value="其他" name="service">其他
		</td>
	</tr>
	<tr>
		<td align="left"><b>行號：</b><Br>
		<input type="text" name="name" style="width:600px">
		
		</td>
	</tr>
	<tr>
		<td align="left"><b>服務說明(計費方式)：</b><Br>
		<textarea style="width:600px; height:100px" name="memo"></textarea>
		</td>
	</tr>
	<tr>
		<td align="left"><b>聯絡方式：</b><Br>
		<input type="text" name="contact" style="width:600px">
		</td>
	</tr>
	<tr><Td>&nbsp;</td></tr>
	<tr>
		<td align="center"><input type="button" value="送出" onClick="Save();" style="width:120px"></td>
	</tr>
</table>
</form>
</center>

<script language="javascript">
function Save(){
	iForm.s.value = "";
	for(var i=0; i<iForm.service.length; i++){
		if(iForm.service[i].checked){
			iForm.s.value += iForm.service[i].value + ",";
		}
	}
	if(iForm.s.value.length > 0)
		iForm.s.value = iForm.s.value.substring(0, iForm.s.value.length-1);
	if(!iForm.s.value){
		alert("請勾選服務事項!");
	}
	else if(!iForm.name.value){
		alert("請輸入行號!");
	}
	else if(!iForm.memo.value){
		alert("請輸入服務說明(計費方式)!");
	}
	else if(!iForm.contact.value){
		alert("請輸入聯絡方式!");
	}
	else{
		iForm.submit();
	}
}


</script>