<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
require_once '../class/form.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->page][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["itemno"], 0);
$type1 = Tools::parseInt2($_REQUEST["type"],0);
$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$usefor1 = $_REQUEST['usefor'];
$page->setHeading($_MODULE->nameOf($_MODULE->page));
include("../include/db_open.php");

if ($no > 0){
    $result=mysql_query("SELECT No, Subject, Content, useFor FROM Page WHERE No = '$no'");
    if(($num=mysql_num_rows($result))==1){
        list($no, $subject, $content, $usefor) = mysql_fetch_row($result);
    }//if
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


$page->addContent("    $init<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">");
$page->addContent("        <form name=\"iForm\" action=\"page_save.php\" method=\"post\">");
$page->addContent("        <input type=\"hidden\" name=\"itemno\" value=\"$no\">");
$page->addContent("        <input type=\"hidden\" name=\"no\" value=\"$no\">");
$page->addContent("        <input type=\"hidden\" name=\"fields\" value=\"0\">");
$page->addContent("        <input type=\"hidden\" name=\"usefor\" value=\"$usefor1\">");
$page->addContent("		<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("        <tr>");
$page->addContent("            <td>");
$page->addContent("                <table id=\"table_page\">");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_required\">代碼：</td>");
$page->addContent("                        <td align=\"left\">$usefor</td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_required\">標題：</td>");
$page->addContent("                        <td align=\"left\"><input type='text' name='subject' style='width:600px' value='$subject'></td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_required\">內容：</td>");
$page->addContent("                        <td align=\"left\"><textarea style='width:600px; height:300px' name='content' id='content'>$content</textarea></td>");
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
$page->addContent("                        <td align=\"center\" width=\"50%\"><input type=\"reset\" value=\"取消\" onclick=\"Cancel();\"></td>");
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
        if (!iForm.subject.value){
            alert("請輸入標題!!");
             iForm.subject.focus();
        }
		else if(tinyMCE.get('content').getContent()==""){
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

<?php

include("../include/db_close.php");

?>