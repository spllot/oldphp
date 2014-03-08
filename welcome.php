<?php
include './include/session.php';

include './include/db_open.php';
$result = mysql_query("SELECT * FROM Config WHERE YN = 'Y' AND ID='welcome'");
if(mysql_num_rows($result) > 0){
	$result = mysql_query("SELECT *, (SELECT YN FROM Config WHERE ID='welcome_pic') as welcome_pic FROM Page WHERE useFor = 'WELCOME'");
	$data = mysql_fetch_array($result);
	$_SESSION['WELCOME'] = date('Y-m-d H:i:s');
}

$result = mysql_query("SELECT * FROM Catalog WHERE USEFOR='TYPE_AREA' ORDER BY Sort");
while($rs = mysql_fetch_array($result)){
	$area_list .= "<option value='{$rs['No']}'" . (($rs['No'] == $data['subscribeArea']) ? " SELECTED":"") . ">{$rs['Name']}</option>";
}

include './include/db_close.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo—首頁訊息</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<center>
<form name="iForm" method="post" action="welcome_save.php">
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td colspan="2" style="text-align:center; padding-bottom:10px;">
			<div style="height:110px; width:615px; overflow:hidden; border:solid 2px gray;">
				<div style="margin-top:-20px"><?php echo $data['Content'];?></div>
			</div>
		</td>
	</tr>
	<Tr>
		<td>
			<div style="width:300px; height:260px; border:solid 2px gray; background:#FFFFFF; background-position:bottom center; background-repeat:no-repeat; overflow:hidden">
				<div style="height:33px"></div>
				<div style="height:194px; background:#C3FEFC">
					<table cellpadding="0" cellspacing="0" style="width:100%">
						<tr>
							<td style="text-align:center; font-weight:bold; height:40px; line-height:40px">訂閱即購網相關好康資訊</td>
						</tr>
						<tr>
							<td style="background:#3390D6; height:94px">
								<table>
									<tr>
										<td style="color:white; height:30px">Email:</td>
										<td><input type="text" style="width:200px" name="email" value="請填寫您的Email" onClick="setEMail();" onBlur="resetEMail();"></td>
									</tr>
									<tr>
										<td style="color:white; height:30px">地區:</td>
										<td style="text-align:left"><select name="area"><option value="">-請選擇所在城市-</option><?php echo $area_list;?></select></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="text-align:center; height:60px; line-height:60px"><img src="./images/a_menubutton_z11.gif" style="cursor:pointer"  onClick="Subscribe();"></td>
						</tr>
					</table>					
				</div>
				<div style="height:33px; text-align:center; padding-top:10px"><img src="/images/Slogan.JPG" style="width:280px"></div>
			</div>
		</td>
		<td style="padding-left:10px"><img src="./upload/<?php echo $data['welcome_pic'];?>" style="width:300px; height:260px; border:solid 2px gray"></td>
	</tR>
</table>
</form>
</center>
<script language="javascript">
function setEMail(){
	if(document.iForm.email.value == "請填寫您的Email"){
		document.iForm.email.value = "";
	}
}

function resetEMail(){
	if(document.iForm.email.value == ""){
		document.iForm.email.value = "請填寫您的Email";
	}
}

function Subscribe(){
	if(!document.iForm.email.value || document.iForm.email.value == "請填寫您的Email"){
		alert("請填寫您的Email!");
	}
	else if(!document.iForm.area.value){
		alert("請選擇所在城市!");
	}
	else{
		$.post(
			'welcome_save.php',
			{
				email: document.iForm.email.value,
				area:  document.iForm.area.value
			},
			function(data)
			{
				eval("var response = " + data);
				if(response.err == "1"){
					alert("Email格式錯誤!");
				}
				else if(response.err == "2"){
					alert("輸入欄位不足!");
				}
				else{
					alert("訂閱成功!謝謝您");
					$.fn.colorbox.close();
				}
			}
		);	
	}
}

</script>