<?php
require_once '../class/tools.php';
$w = Tools::parseInt2($_REQUEST['w'], 240);
$h = Tools::parseInt2($_REQUEST['h'], 160);
?>
<html style="width:300; height:350">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>上傳圖片</title>
    <style>
		body{
			overflow: hidden;
			background-color: "#CCCCCC";
			margin-left: 0pt;
			margin-top: 0pt;
		}
	</style>
	<body topmargin="0" leftmargin="0">
		<table width="100%" height="100%">
			<form name="iForm" method="post" action="upload_image_go.php" enctype="multipart/form-data" target="iAction2"><input type="hidden" name="MAX_FILE_SIZE" value="500000" />
			<input type="hidden" name="w" value="<?php print $w;?>">
			<input type="hidden" name="h" value="<?php print $h;?>">
			<tr>
				<td style="border: solid 1px gray">
					<img name="currImage" src="../images/blank.gif" width="100%" height="100%">
				</td>
			</tr>
			<tr height="50">
				<td>
					<input type="file" style="width:100%" name="imgurl" onChange="chgImage();"><br>
					<font color="red">(不可大於500kb)</font>
				</td>
			</tr>
			<tr height="30">
				<td>
					<table width="100%">
						<tr>
							<td width="50%" align="center">
								<input name="btnUpload" type="button" value="上傳" onClick="Upload();">
							</td>
							<td width="50%" align="center">
								<input name="btnCancel" type="button" value="取消" onClick="Cancel();">
							</td>
						</tr>
					</table>
				</td>
			</tr>
			</form>
			<iframe name="iAction2" width="0" height="0"></iframe>
		</table>
	</body>
</html>

<script language="javascript">
	var imgExtension1 = ".jpg, .gif, .png"
	var imgExtension2 = ".jpeg";
	function isImageFile(new_file){
		if (new_file){
			var new_ext1 = new_file.toLowerCase().substring(new_file.length - 4, new_file.length);
			var new_ext2 = new_file.toLowerCase().substring(new_file.length - 5, new_file.length);
			if ((imgExtension1.indexOf(new_ext1) > -1) || (imgExtension2 == new_ext2)){
				return true;
			}
		}
		return false;
	}
	
	function chgImage(){
		if(isImageFile(iForm.imgurl.value)){
			document['currImage'].src = iForm.imgurl.value;
		}
		else{
			alert("請選擇 GIF / JPG / JEPG / PNG 檔案!!");
			iForm.reset();
		}
	}

	function Cancel(){
		window.close();
	}

	function Upload(){
		if (iForm.imgurl.value){
			if(isImageFile(iForm.imgurl.value)){
				iForm.btnUpload.disabled = true;
				iForm.btnCancel.disabled = true;
				iForm.submit();
			}
			else{
				alert("請選擇 GIF / JPG / JEPG / PNG 檔案!!");
				iForm.reset();
			}
		}//if
		else{
			alert("請先選擇要上傳的圖片檔案!!");
		}
	}
</script>