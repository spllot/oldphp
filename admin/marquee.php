<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
require_once '../class/form.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->marquee][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["itemno"], 0);
$type1 = Tools::parseInt2($_REQUEST["type"],0);
$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$usefor1 = $_REQUEST['usefor'];
$page->setHeading($_MODULE->nameOf($_MODULE->marquee));
include("../include/db_open.php");

    $result=mysql_query("SELECT No, Subject, Content, useFor FROM Page WHERE useFor='MARQUEE'");
    if(($num=mysql_num_rows($result))==1){
        list($no, $subject, $content, $usefor) = mysql_fetch_row($result);
    }//if


$page->addContent("<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">");
$page->addContent("        <form name=\"iForm\" action=\"marquee_save.php\" method=\"post\">");
$page->addContent("        <input type=\"hidden\" name=\"itemno\" value=\"$no\">");
$page->addContent("        <input type=\"hidden\" name=\"no\" value=\"$no\">");
$page->addContent("        <input type=\"hidden\" name=\"fields\" value=\"0\">");
$page->addContent("        <input type=\"hidden\" name=\"usefor\" value=\"$usefor1\">");
$page->addContent("		<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("        <tr>");
$page->addContent("            <td>");
$page->addContent("                <table id=\"table_page\">");
$page->addContent("                    <tr style=\"display:none\">");
$page->addContent("                        <td class=\"html_label_generated\">代碼：</td>");
$page->addContent("                        <td align=\"left\">$usefor</td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_generated\">預覽：</td>");
$page->addContent("                        <td align=\"left\"><div id='marquee' style=\"; height:35px; background:url('/images/tab_marquee.png'); width:320px; padding-left:5px; padding-right:5px; padding-top:5px; padding-bottom:5px; line-height:35px\">$content</div></td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_required\">程式碼：</td>");
$page->addContent("                        <td align=\"left\"><textarea style='width:600px; height:300px' name='content' id='content' onKeyUp='setPreview();'>$content</textarea></td>");
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

	function setPreview(){
		document.getElementById("marquee").innerHTML = document.iForm.content.value;
	}

	function Save(){
        if(iForm.content.value==""){
			alert("請輸入程式碼!");
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