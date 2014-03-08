<?php
ini_set("session.save_path", "./tmp/");
session_start();
require_once './class/tools.php';
$w = Tools::parseInt2($_REQUEST['w'], 240);
$h = Tools::parseInt2($_REQUEST['h'], 160);
?>
<html style="width:300; height:170">
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
		<table width="100%" height="">
			<form name="iForm" method="post" action="upload_image_go.php" enctype="multipart/form-data" target="iAction2"><input type="hidden" name="MAX_FILE_SIZE" value="250000" />
			<input type="hidden" name="w" value="<?php print $w;?>">
			<input type="hidden" name="h" value="<?php print $h;?>">
			<tr>
				<td style="border: solid 1px gray; color:red; text-align:center; height:50px; line-height:50px">
					<?php
					include './include/db_open.php';
					$result = mysql_query("SELECT * FROM Config");
					while($rs=mysql_fetch_array($result)){
						$_CONFIG[$rs['ID']] = $rs['YN'];
					}
					include './include/db_close.php';

					if($_SESSION['cashflow'] == 1 && $_CONFIG['pics1'] >0 && $_SESSION['UPLOAD_COUNTS'] < $_CONFIG['pics1']){
						$btn = '<input name="btnUpload" type="button" value="上傳" onClick="Upload();">';
						echo "可上傳" . ($_CONFIG['pics1']-$_SESSION['UPLOAD_COUNTS']) . "張圖片";		
					}
					else if($_SESSION['cashflow'] == 0 && $_CONFIG['pics2'] >0 && $_SESSION['UPLOAD_COUNTS'] < $_CONFIG['pics2']){
						$btn = '<input name="btnUpload" type="button" value="上傳" onClick="Upload();">';
						echo "可上傳" . ($_CONFIG['pics2']-$_SESSION['UPLOAD_COUNTS']) . "張圖片";		
					}
					else{
						$btn = '<input name="btnUpload" type="button" value="無法上傳" disabled>';
						echo "已超過可上傳數量";					
					}
					?>
				</td>
			</tr>
			<tr height="50">
				<td>
					<input type="file" style="width:100%" name="imgurl" onChange="chgImage();"><br>
					<font color="red">(不可大於250KB)</font>
				</td>
			</tr>
			<tr height="30">
				<td>
					<table width="100%">
						<tr>
							<td width="50%" align="center">
								<?=$btn?>
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
//			document['currImage'].src = iForm.imgurl.value;
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