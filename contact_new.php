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
<center>
<table style="width:660px" cellpadding="5" cellspacing="0">
	<tr style="height:30px">
		<td style="text-align:left; font-weight:bold; color:white; background:gray" align="left">協力合作廠商服務資訊輸入</td>
	</tr>
	<tr>
		<td align="left"><b>服務地區：</b><select id="area" name="area"><?=$area_list?></select></td>
	</tr>
	<tr>
		<td align="left"><b>服務事項：</b><Br>
		<input type="checkbox" value="" name="">攝影&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="checkbox" value="" name="">文案&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="checkbox" value="" name="">海報&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="checkbox" value="" name="">其他
		</td>
	</tr>
	<tr>
		<td align="left"><b>行號：</b><Br>
		<input type="text" name="" style="width:400px">
		
		</td>
	</tr>
	<tr>
		<td align="left"><b>服務說明(計費方式)：</b><Br>
		<textarea style="width:400px; height:100px" name=""></textarea>
		</td>
	</tr>
	<tr>
		<td align="left"><b>聯絡方式：</b><Br>
		<input type="text" name="" style="width:400px">
		</td>
	</tr>
	<tr><Td>&nbsp;</td></tr>
	<tr>
		<td align="center"><input type="button" value="送出" onClick="$.fn.colorbox.close();" style="width:120px"></td>
	</tr>
</table>

</center>