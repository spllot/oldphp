<?php
include './include/session.php';

include './include/db_open.php';




$sql = "SELECT * FROM Page WHERE useFor = 'PGE_CONTACT'";
$result = mysql_query($sql) or die(mysql_error());

$area_list = "";
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_AREA' ORDER BY Sort");
while($rs=mysql_fetch_array($result)){
	$area_list .= "<option value='" . $rs['No'] . "'" . (($_REQUEST['area'] == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}


if ($_SESSION['member']['isAdmin'] == 1){
	$btn = '<input type="button" value="刪除" onClick="Delete();">';
}

$area = $_REQUEST['area'];
$result = mysql_query("SELECT * FROM Supplier WHERE 1=1 " . (($area != "") ? " AND (Area='$area' OR Area='0')":"") . " ORDER BY dateAdd DESC") or die(mysql_error());

$num = mysql_num_rows($result);
$pagesize  = 10;
$pages = ceil($num / $pagesize);
$pageno = $_REQUEST['pageno'];
if($pageno == "" || $pageno > $pages){$pageno = 1;}


$list .= "<table cellpadding='0' cellspacing='0' border='0' width='610px' align='center'>";
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($data = mysql_fetch_array($result)){
			if ($_SESSION['member']['isAdmin'] == 1){
				$chk = "<td style='width:20px;vertical-align:top'><input type='checkbox' name='no' value='{$data['No']}'></td>";
			}
			$service = str_replace("\n", "<br>", $data['Service']);
			$memo = str_replace("\n", "<br>", $data['Memo']);
			$list .= <<<EOD
				<tr>
			{$chk}
				<td style="text-align:left; padding-bottom:20px;vertical-align:top; font-size:10pt">
				<font style="">行號：</font><font style="color:blue">{$data['Name']}</font><Br>
				<font style="">服務事項：</font><font style="color:blue">{$service}</font><Br>
				<font style="">服務說明(計費方式)：</font><font style="color:blue"><br>{$memo}</font><Br>
				<font style="">聯絡方式：</font><font style="color:blue">{$data['Contact']}</font><Br>
					</td>
				</tr>
EOD;
		}
		else{
			break;
		}
	}
	$list .= "<tr>";
	$list .= "	<td style='padding: 10px;' colspan='2'>";
	$list .= "		<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">";
    $list .= "           <tr>";
	if($pageno > 1){
		$list .= "             <td style='width:74px; height:25px; text-align:center; background-image:url(./images/btn_100_black.jpg1); background-repeat:no-repeat; background-position: center center'>";
		$list .= "				<a href=\"javascript:" . (($pageno > 1) ? "setPage(" . ($pageno - 1) . ")" : "void(0)"). ";\" style='list; text-decoration:underline'>上一頁</a>";
	}
	else{
		$list .= "             <td style='width:74px; height:25px; text-align:left; padding-left:10px;'>&nbsp;";
	}
	$list .= "			  </td>";
    $list .= "             <td align=\"center\" nowrap><table><tr>";
	for($i=0; $i<$pages; $i++){
		$p = "<div style='width:18px; height:18px; border:solid 0px black; line-height:18px'>" . ($i+1) . "</div>";
		if(($i+1)==$pageno){
			$list .= "<td style='text-decoration:underline; width:20px; list; text-align:center'>" . $p . "</td>";		
		}
		else{
			$list .= "<td onClick=\"javascript:setPage(" . ($i+1) . ");\" style='cursor:pointer; list; text-decoration:none; width:20px; text-align:center'>" . $p . "</td>";		
		}
	}
	$list .= "			</tr></table></td>";
	if($pageno < $pages){
		$list .= "			<td style='width:74px; height:25px; text-align:center; background-image:url(./images/btn_100_black.jpg1); background-repeat:no-repeat; background-position: center center'>";
		$list .= "				<a href=\"javascript:" . (($pageno < $pages) ? "setPage(" . ($pageno + 1) . ")" : "void(0)") . ";\" style='list; text-decoration:underline'>下一頁</a>";
	}
	else{
		$list .= "             <td style='width:74px; height:25px; text-align:left; padding-left:10px;'>&nbsp;";
	}
	$list .= "			</td>";
	$list .= "			</tr>";
	$list .= "		</table>";
	$list .= "	</td>";
	$list .= "</tr>";
}
else{
	$list .= "<tr>";
	$list .= "	<td style='padding: 10px; text-align:center' colspan='2'>查無資料";
	$list .= "	</td>";
	$list .= "</tr>";
}
$list .= "</table>";







$WEB_CONTENT = <<<EOD
<table cellpadding="0" cellspacing="0" border="0" style="width:100%; background:white; height:560px">
	<tr>
		<td style="vertical-align:top; text-align:center; padding-bottom:0px" valign="top" align="center">
			<table width="100%" border=0>
				<tr style="height:22px"></tr>
				<tr>
					<td style="text-align:center; font-size:14pt; border-bottom:solid 1px gray; padding-bottom:10px" align="center">商家合作/客服中心</td>
				</tr>
				<tr style="height:22px"></tr>
				<tr>
					<td style="text-align:center" align="center">
					<form name="iForm" method="post" action="contact_save.php" target="iAction">
						<table border=0 align="center">
							<tr>
								<td align="right">聯絡選項：</td>
								<td align="left">
									<select name="catalog" onChange="chgCatalog(this);" style="width:405px">
										<option value="1">網站問題詢問</option>
										<option value="2">網站建議事項</option>
										<option value="3">商家合作諮詢</option>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right"><div id="namebox">姓名：</div></td>
								<td align="left" style="color:red"><input name="name" type="text" style="width:400px" value="">*</td>
							</tr>
							<tr class="cat3" style="display:none">
								<td align="right">網站介紹：</td>
								<td align="left" style="color:red"><input name="intro" type="text" style="width:400px" value=""></td>
							</tr>
							<tr>
								<td align="right">電子郵件：</td>
								<td align="left" style="color:red"><input name="email" type="text" style="width:400px" value="">*</td>
							</tr>
							<tr class="cat3" style="display:none">
								<td align="right">聯絡人：</td>
								<td align="left" style="color:red"><input name="contact" type="text" style="width:400px" value="">*</td>
							</tr>
							<tr class="cat3" style="display:none">
								<td align="right">聯絡電話：</td>
								<td align="left" style="color:red"><input name="phone" type="text" style="width:400px" value="">*</td>
							</tr>
							<tr class="cat1">
								<td align="right" valign="top"><div id="textbox">問題：</div></td>
								<td align="left" valign="top" style="color:red"><textarea name="content" style="width:400px; height:100px"></textarea>*</td>
							</tr>
							<tr>
								<td colspan="2" style="padding-top:15px;"><input type="button" value="送出" onClick="Save();" class="btn" style="width:200px"></td>
							</tr>
						</table>
					</form>
					</td>
				</tr>
				<tr>
					<td style="text-align:center; line-height:30px" align="center">
					客服專線：(03)5904710
					(週一至週五 09:00 ~ 18:00)<br>
					客服信箱：service@intimego.com
					</tr>
				</td>
				<tr>
					<td style="text-align:center" align="center">
					<br><br>
					<form name="sForm">
					<input type="hidden" name="pageno" value="{$pageno}">
					<input type="hidden" name="item" value="">
					<Table align="center" width="90%" style="background:#B2B2B2" cellpadding=2 cellspacing=2>
						<tr>
							<td style="background:#808080; color:white; height:30px; lin-height:30px">
								協力合作廠商參考資訊(攝影、文案、海報)
							</td>
						</tr>
						<tr>
							<td style="background:white; height:30px; line-height:30px">
							<table width="100%" border=0 cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:60px">地區：</td>
									<td style="width:90px; text-align:left"><select id="area" name="area">
									<option value="">所有地區</option>{$area_list}
								</select></td>
									<td style="width:60px"><input type="button" value="搜尋" onClick="Search();"></td>
									<td style="">&nbsp;</td>
									<td style="width:60px">{$btn}</td>
									<td style="width:60px"><input type="button" value="新增" onClick="New();"></td>
								</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td style="background:white; height:22px; line-height:22px; text-align:center">
							{$list}
							</td>
						</tr>
					</table>
					</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
EOD;

include './include/db_close.php';

include 'template.php';
?>
<script language="javascript">
function setPage(x){
	sForm.pageno.value = x;
	sForm.submit();
}
function Search(){
	window.location.href="?area=" + $("#area").val();
}
function New(){
	parent.Dialog1("supplier.php", 500);
}
function Delete(){
	sForm.item.value = "";
	if(sForm.no){
		if(sForm.no.length){
			for(var i=0; i<sForm.no.length; i++){
				if(sForm.no[i].checked){
					sForm.item.value += sForm.no[i].value + ",";
				}
			}
		}
		else{
			if(sForm.no.checked){sForm.item.value = sForm.no.value+",";}
		}
	}
	if(sForm.item.value.length > 0){
		sForm.item.value = sForm.item.value.substring(0, sForm.item.value.length-1);
	}

	if(sForm.item.value.length > 0){
		if(confirm("確定要刪除所選項目?")){
			sForm.method = "post";
			sForm.action = "supplier_delete.php";
			sForm.submit();
		}
	}
	else{
		alert("尚未選取!");
	}

}


function chgCatalog(x){
	if(x.selectedIndex == 0){
		$(".cat3").hide();
		$(".cat1").show();
		$("#textbox").html("問題：");
		$("#namebox").html("姓名：");
	}
	if(x.selectedIndex == 1){
		$(".cat3").hide();
		$(".cat1").show();
		$("#textbox").html("建議：");
		$("#namebox").html("姓名：");
	}
	if(x.selectedIndex == 2){
		$(".cat3").show();
		$(".cat1").hide();
		$("#namebox").html("商家(商品)名稱：");
	}
}
function Save(){
	var iForm = document.iForm;
	if(!iForm.catalog.value){
		alert("請選擇聯絡選項!");
	}
	else if(iForm.catalog.value == "1" && !iForm.name.value){
		alert("請輸入姓名!");
		iForm.name.focus();
	}
	else if(iForm.catalog.value == "1" && !iForm.email.value){
		alert("請輸入電子郵件!");
		iForm.email.focus();
	}
	else if(iForm.catalog.value == "1" && !iForm.content.value){
		alert("請輸入問題內容!");
		iForm.content.focus();
	}
	else if(iForm.catalog.value == "2" && !iForm.name.value){
		alert("請輸入姓名!");
		iForm.name.focus();
	}
	else if(iForm.catalog.value == "2" && !iForm.email.value){
		alert("請輸入電子郵件!");
		iForm.email.focus();
	}
	else if(iForm.catalog.value == "2" && !iForm.content.value){
		alert("請輸入建議內容!");
		iForm.content.focus();
	}
	else if(iForm.catalog.value == "3" && !iForm.name.value){
		alert("請輸入商家(商品)名稱!");
		iForm.name.focus();
	}
	else if(iForm.catalog.value == "3" && !iForm.email.value){
		alert("請輸入電子郵件!");
		iForm.email.focus();
	}
	else if(iForm.catalog.value == "3" && !iForm.contact.value){
		alert("請輸入聯絡人!");
		iForm.contact.focus();
	}
	else if(iForm.catalog.value == "3" && !iForm.phone.value){
		alert("請輸入聯絡電話!");
		iForm.phone.focus();
	}
	else{
		iForm.submit();
	}
}
</script>