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




$result = mysql_query("SELECT * FROM Member WHERE No='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
$data = mysql_fetch_array($result);



include './include/db_close.php';
include 'seller_data_tab.php';





$WEB_CONTENT = <<<EOD
<script language="javascript" src="./ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="./ckeditor/adapters/jquery.js"></script>	
<script language="javascript" src="./ckfinder/ckfinder.js"></script>
<LINK href="./ckeditor/_samples/sample.css" rel="stylesheet" type="text/css">

<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center">
		<center>
		<form name="iForm" method="post" target="iAction">
		<table width="100%" border=0>
			<tr style="height:40px">
				<td style="text-align:left" colspan="2">
					<b>商家預告事項：</b>商家在此預告服務訊息，收藏商家服務之會員可以提前得知。&nbsp;&nbsp;
					<input type="button" value="更新內容" onClick="Save2();">
				</td>
			</tr>
			<tr>
				<td align="left" colspan="2">
				<input type="text" style="width:700px; height:50px" maxlength="30" name="warning" value="{$data['warning']}">
				<div style="text-align:left; color:gray">[註]：欄位填字以三十字為限。</div>
				<br><br>
				</td>
			</tr>
			<tr style="height:40px">
				<td style="text-align:left;" colspan="2">
					<b>商家介紹頁面編輯：</b>&nbsp;&nbsp;
					<input type="button" value="更新內容" onClick="Save();">
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:left">
					<textarea id="intro" name="intro">{$data['Intro']}</textarea>
				</td>
			</tr>
			<tr>
				<td align="left" colspan="2">
					<Table align="left" width="700">
						<tr>
							<td valign="top" width=40 style="color:gray">[註]：</td>
							<td valign="top" width=20 style="color:gray">(1).</td>
							<td valign="top" align="left" style="color:gray">為了圖文編輯的美觀考量， 商品圖片請自行拖曳適當之大小，儘量勿超出編輯頁面之寬度。 編輯所加入之圖片，若圖片位置出現在編輯頁頂端時，僅需剪下該圖片， 再貼到適當位置即可。文字編輯器若無法編輯文字時，可以在Wordpad編輯完成後再貼上文字。</td>
						</tR>
						<tr>
							<td></td>
							<td valign="top" width=20 style="color:gray">(2).</td>
							<td valign="top" align="left" style="color:gray">[商家介紹頁面] 經由前台列表連結 [商家其他服務]頁, 點選[商家名稱]即可連結其內容。</td>
						</tR>
					</table>	
				</td>
			</tr>
		</table>
		</form>
		</center>
		</td>
	</tr>
</table>
<Br><br>

EOD;
include 'template2.php';
?>

<script language="javascript">
	var config = {
		width: 700,
		height: 448,
		toolbar:
			[
				['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','Anchor'],
				'/',
				['Styles','Format','Font','FontSize','-','TextColor','BGColor','-','Image','Table','-','Maximize']
			],
			filebrowserBrowseUrl : '/ckfinder/ckfinder.html',
			filebrowserUploadUrl : './ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'
	};

	$(function() {
		$("#intro").ckeditor(config)
	});

</script>
<script language="javascript">
	var iForm = document.iForm;
	function Save(){
		CKEDITOR.instances.intro.updateElement();
		iForm.action = "seller_intro_save.php";
		iForm.submit();
	}
	function Save2(){
		CKEDITOR.instances.intro.updateElement();
		iForm.action = "seller_intro_save2.php";
		iForm.submit();
	}
</script>
