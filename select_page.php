<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html style="height:140px">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>選擇我的FaceBook粉絲團</title>
<table align="center">
	<tr>
		<td><iframe style="width:480px; height:60px" name="facebook" src="select_page_list.php" style="border:0px" frameborder="0"></iframe></td>
	</tr>
	<tr>
		<td style="text-align:center; border-top:solid 2px gray; padding-top:20px">
			<input type="button" value="確定" onClick="Save();">
			&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" value="取消" onClick="Cancel();">
		</td>
	</tr>
</table>
<script language="javascript">
function Cancel(){
	window.returnValue='';
	window.close();
}
function FB(xPage, xName){
	this.page = xPage
	this.name = xName;
}
function Save(){
	if(facebook.activity_page){
		if(facebook.activity_page.value){
			window.returnValue = new FB(facebook.activity_page.options[facebook.activity_page.selectedIndex].value, facebook.activity_page.options[facebook.activity_page.selectedIndex].text);
			window.close();
		}
		else{
			alert("請選擇FaceBook粉絲團!");
		}
	}
	else{
		alert("請登入Facebook!");
	}
}
</script>