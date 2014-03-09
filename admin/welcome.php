<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
require_once '../class/form.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->welcome][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["itemno"], 0);
$type1 = Tools::parseInt2($_REQUEST["type"],0);
$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$usefor1 = $_REQUEST['usefor'];
$page->setHeading($_MODULE->nameOf($_MODULE->welcome));
$page->addJSFile("/js/jquery.js");
$page->addJSFile("/js/ajaxupload.js");
include("../include/db_open.php");

    $result=mysql_query("SELECT No, Subject, Content, useFor, (SELECT YN FROM Config WHERE ID='welcome_pic') as welcome_pic FROM Page WHERE useFor='WELCOME'");
    if(($num=mysql_num_rows($result))==1){
        list($no, $subject, $content, $usefor, $welcome_pic) = mysql_fetch_row($result);
    }//if
$init .= <<<EOD
<script type="text/javascript" src="/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
/**/

	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		skin : "o2k7",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",

		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|,bullist,numlist,outdent,indent,blockquote,|,forecolor,backcolor,|,code,preview",
		theme_advanced_buttons2 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,link,unlink,anchor,image,media,cleanup,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		content_css : "tinymce.css",

		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>

EOD;

$pic = <<<EOD

							<div style="float:left"><input type="image" id="upload0" src="../images/icon_upld.png" /></div>
							<div style="float:left">(350x322)</div>
							<div style="font-size:10pt; display:none" id="loading0">
								<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td><img src="../images/loader_light_blue.gif"></td>
										<td style='font-size:10pt'>&nbsp;上傳中，請待候…</td>
									</tr>
								</table>
							</div><br>
							<input type="hidden" name="logo" id="logo" value="{$welcome_pic}" />
							<div id="cbox0"><img name="pic0" src="/images/none.png" title="圖片" style="width:350; height:322px"></div>


EOD;


$page->addContent("    $init<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">");
$page->addContent("        <form name=\"iForm\" action=\"welcome_save.php\" method=\"post\">");
$page->addContent("        <input type=\"hidden\" name=\"itemno\" value=\"$no\">");
$page->addContent("        <input type=\"hidden\" name=\"no\" value=\"$no\">");
$page->addContent("        <input type=\"hidden\" name=\"fields\" value=\"0\">");
$page->addContent("        <input type=\"hidden\" name=\"usefor\" value=\"$usefor1\">");
$page->addContent("		<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("        <tr>");
$page->addContent("            <td>");
$page->addContent("                <table id=\"table_page\">");
$page->addContent("                    <tr style=\"display:none\">");
$page->addContent("                        <td class=\"html_label_required\">代碼：</td>");
$page->addContent("                        <td align=\"left\">$usefor</td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr style=\"display:none\">");
$page->addContent("                        <td class=\"html_label_required\">標題：</td>");
$page->addContent("                        <td align=\"left\"><input type='text' name='subject' style='width:600px' value='$subject'></td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_required\">內容：</td>");
$page->addContent("                        <td align=\"left\"><textarea style='width:600px; height:150px' name='content' id='content'>$content</textarea></td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_required\">圖片：</td>");
$page->addContent("                        <td align=\"left\">{$pic}</td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr style=\"display:none\">");
$page->addContent("                        <td class=\"html_label_generated\">建立日期：</td>");
$page->addContent("                        <td align=\"left\"><input type=\"text\" name=\"datecreate\" value=\"$datecreate\" style=\"width:350px; background-color:transparent; border-width: 0 0 0 0; color:gray\"></td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr style=\"display:none\">");
$page->addContent("                        <td class=\"html_label_generated\">建立人員：</td>");
$page->addContent("                        <td align=\"left\"><input type=\"text\" name=\"createby\" value=\"$createby\" style=\"width:350px; background-color:transparent; border-width: 0 0 0 0; color:gray\"></td>");
$page->addContent("                    </tr>");
$page->addContent("                </table>");
$page->addContent("            </td>");
$page->addContent("        </tr>");
$page->addContent("        <tr>");
$page->addContent("            <td><hr>");
$page->addContent("                <table width=\"100%\">");
$page->addContent("                    <tr>");
$page->addContent("                        <td align=\"center\" width=\"50%\"><input type=\"button\" value=\"確定\" onClick=\"Save();\"></td>");
$page->addContent("                    </tr>");
$page->addContent("                </table>");
$page->addContent("            </td>");
$page->addContent("        </tr>");
$page->addContent("        </form>");
$page->addContent("    </table>");

$page->show();



?>

<script language="javascript">
	function Save(){
        if(tinyMCE.get('content').getContent()==""){
			alert("請輸入內容!");
		}
        else{
            iForm.submit();
        }
    }//Save

	function Cancel(){
		iForm.action = "page.php";
		iForm.submit();
	}
</script>
<script language="javascript">
$(function() {
	new AjaxUpload('#upload0', {
		action: 'uploadfile.php',
		onSubmit : function(file , ext){
			if (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext)){
				var d = new Date();
				var curr_hour = d.getHours();
				var curr_min = d.getMinutes();
				var curr_sec = d.getSeconds();
				document.getElementById("loading0").style.display="block";
				this.setData({
					'dir':  	"../upload/",
					"fname": 	'WELCoME_'+d.getTime()+"."+ext,
					'ext':  	ext,
					'width': 350,
					'height': 322
				});
			} else {					
				alert('上傳錯誤訊息: 只允許上傳 image 圖檔 (jpg,png,jpeg,gif)');
				return false;				
			}		
		},
		onComplete : function(file, response){
			alert(response);
			$('#logo').val(response);
			$('#cbox0').html("<img src='/upload/"+response+"' style='width:350; height:322px'>");
			document.getElementById("loading0").style.display="none";
		}
	});
});
</script>

<?php
if($pic != "")
	JavaScript::Execute("document['pic0'].src='/upload/$welcome_pic';");
	
include("../include/db_close.php");

?>