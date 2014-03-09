<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
include './ad_usefor.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->marquee][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["mno"], 0);
$pageno = Tools::parseInt2( $_REQUEST['pageno'], 1);
$usefor = $_REQUEST["usefor"];
$page->setHeading($_MODULE->nameOf($_MODULE->marquee));
foreach($usefors as $value => $text){
	$ad .= "<option value='$value'>$text</option>";
}
include("../include/db_open.php");
$sort = 0;
$result = mysql_query("SELECT MAX(Sort) FROM Marquee WHERE useFor = '$usefor'");
if (($num = mysql_num_rows($result)) == 1){
   $record = mysql_fetch_row($result) or die (mysql_error());
   $sort = $record[0] + 1;
}//if
if ($no > 0){
    $result=mysql_query("SELECT Caption, Url, Icon, Country, Sort FROM Marquee WHERE No = $no");
    if(($num=mysql_num_rows($result))==1){
        list($caption, $url, $icon, $country, $sort) = mysql_fetch_row($result);
    }//if
}//if
$ad_picpath = (($icon != "") ? "/upload/thumb_".basename($icon):"/images/ad_none.png");

include("../include/db_close.php");
$page->addJSFile("/js/jquery.js");
$page->addJSFile("/js/ajaxupload.js");

$WEB_CONTENT = <<<EOD
	$init
    <table cellpadding="0" cellspacing="0" border="0">
        <form name="iForm" action="marquee_save.php" method="post">
        <input type="hidden" name="no" value="$no">
		<input type="hidden" name="pageno" value="$pageno">
		<input type="hidden" name="usefor" value="SLIDE">
        <tr>
            <td>
                <table>
					<tr style="display:none">
						<td class="html_label_required">地區：</td>
						<Td>
							<input type="radio" name="country" value="1" checked>中國 &nbsp;&nbsp;
							<input type="radio" name="country" value="2">台灣
						</td>
					</tr>
                    <tr>
                        <td class="html_label_required">內容：</td>
                        <td align="left"><textarea name="caption" style="width:600px; height:200px">$caption</textarea></td>
                    </tr>
                    <tr style="display:none">
                        <td class="html_label_required">連結網址：</td>
                        <td align="left"><input type="text" name="url" style="width:600px" value="$url"></td>
                    </tr>
                    <tr style="display:none">
                        <td class="html_label_required">圖片：</td>
                        <td align="left">
						<div style="float:left"><input type="image" id="upload" src="../images/icon_upld.png" /></div>
						<div style="float:left">(788x375)</div>
						<div style="font-size:10pt; display:none" id="loading">
							<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td><img src="../images/loader_light_blue.gif"></td>
									<td style='font-size:10pt'>&nbsp;上傳中，請待候…</td>
								</tr>
							</table>
						</div><br>
						<input type="hidden" name="ad_picpath" id="ad_picpath" value="{$ad_picpath}" />
						  <div id="cbox"><img src="{$ad_picpath}" width="162" height=87 title="圖片"></div>
						</td>
                    </tr>
                    <tr>
                      <td class="html_label_required">排列順序：</td>
                        <td align="left"><input type="text" name="sort" value="$sort" style="width:50px;"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td><hr>
                <table width="100%">
                    <tr>
                        <td align="center" width="50%"><input type="button" value="確定" onClick="Save();"></td>
                        <td align="center" width="50%"><input type="reset" value="取消" onclick="history.back();"></td>
                    </tr>
                </table>
            </td>
        </tr>
        </form>
    </table>
EOD;

$page->addContent($WEB_CONTENT);


$page->show();
if($country==2)
	JavaScript::Execute("iForm.country[1].checked= true");
?>
<script language="javascript">
    function Save(){
        if (!iForm.caption.value){
            alert("請輸入內容!");
            iForm.caption.focus();
        }
//		else if(!iForm.url.value){
//			alert("請輸入連結網址!");
//			iForm.url.focus();
//		}
//		else if(!iForm.ad_picpath.value){
//			alert("請上傳圖片!");
//		}
        else{
            iForm.submit();
        }
    }//Save
$(function() {
	new AjaxUpload('#upload', {
		action: 'uploadfile2.php',
		onSubmit : function(file , ext){
			if (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext)){
				var d = new Date();
				var curr_hour = d.getHours();
				var curr_min = d.getMinutes();
				var curr_sec = d.getSeconds();
				document.getElementById("loading").style.display="block";
				this.setData({
					'dir':  	"../upload/",
					"fname": 	'ad_SLIDE_'+d.getTime()+"."+ext,
					'ext':  	ext
				});
			} else {					
				alert('上傳錯誤訊息: 只允許上傳 image 圖檔 (jpg,png,jpeg,gif)');
				return false;				
			}		
		},
		onComplete : function(file, response){
//		alert(response);
			$('#ad_picpath').val(response);
			$('#cbox').html("<img src='/upload/"+response+"' width='162' height='87'>");
			document.getElementById("loading").style.display="none";
		}
	});
});
</script>

