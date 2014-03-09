<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
require_once '../class/form.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->buyer_msg][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["itemno"], 0);
$type1 = Tools::parseInt2($_REQUEST["type"],0);
$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$usefor1 = $_REQUEST['usefor'];
$page->setHeading($_MODULE->nameOf($_MODULE->buyer_msg));
include("../include/db_open.php");

if ($no > 0){
    $result=mysql_query("SELECT * FROM Contact WHERE No = '$no'");
    if(($num=mysql_num_rows($result))==1){
        $rs = mysql_fetch_array($result);
    }//if
}//if

$names = array("", "網站問題詢問", "網站建議事項", "商家合作諮詢");
$page->addContent("    $init<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">");
$page->addContent("        <form name=\"iForm\" action=\"contact_save.php\" method=\"post\">");
$page->addContent("        <input type=\"hidden\" name=\"itemno\" value=\"$no\">");
$page->addContent("        <input type=\"hidden\" name=\"no\" value=\"$no\">");
$page->addContent("        <input type=\"hidden\" name=\"fields\" value=\"0\">");
$page->addContent("        <input type=\"hidden\" name=\"usefor\" value=\"$usefor1\">");
$page->addContent("		<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("        <tr>");
$page->addContent("            <td>");
$page->addContent("                <table id=\"table_page\">");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_generated\">日期：</td>");
$page->addContent("                        <td align=\"left\">{$rs['dateSubmited']}</td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_generated\">聯絡選項：</td>");
$page->addContent("                        <td align=\"left\">{$names[$rs['Catalog']]}</td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_generated\">內容：</td>");
$page->addContent("                        <td align=\"left\">");
			switch($rs['Catalog']){
				case 1:
					$page->addContent("姓名：" . $rs['Name'] . "<br>");
					$page->addContent("電子郵件：" . $rs['EMail'] . "<br>");
					$page->addContent("問題：" . $rs['Content'] . "");
					break;
				case 2:
					$page->addContent("姓名：" . $rs['Name'] . "<br>");
					$page->addContent("電子郵件：" . $rs['EMail'] . "<br>");
					$page->addContent("建議：" . $rs['Content'] . "");
					break;
				case 3:
					$page->addContent("商家(商品)名稱：" . $rs['Name'] . "<br>");
					$page->addContent("網站介紹：" . $rs['Intro'] . "<br>");
					$page->addContent("電子郵件：" . $rs['EMail'] . "<br>");
					$page->addContent("聯絡人：" . $rs['Contact'] . "<br>");
					$page->addContent("聯絡電話：" . $rs['Phone'] . "");
					break;
			}
			$page->addContent("</td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_required\">回覆：</td>");
$page->addContent("                        <td align=\"left\"><textarea style='width:600px; height:300px' name='reply' id='reply'>{$rs['Reply']}</textarea></td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_generated\">回覆日期：</td>");
$page->addContent("                        <td align=\"left\">" . (($rs['dateReplied'] == "0000-00-00 00:00:00") ? "尚未" : $rs['dateReplied']) . "</td>");
$page->addContent("                    </tr>");
$page->addContent("                </table>");
$page->addContent("            </td>");
$page->addContent("        </tr>");
$page->addContent("        <tr>");
$page->addContent("            <td><hr>");
$page->addContent("                <table width=\"100%\">");
$page->addContent("                    <tr>");
$page->addContent("                        <td align=\"center\" width=\"50%\"><input type=\"button\" value=\"回覆\" onClick=\"Save();\"" . (($rs['dateReplied'] == "0000-00-00 00:00:00") ? "" : " disabled") . "></td>");
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
        if(!iForm.reply.value){
			alert("請輸入回覆內容!");
		}
        else{
            iForm.submit();
        }
    }//Save

	function Cancel(){
		iForm.action = "contact.php";
		iForm.submit();
	}
</script>

<?php

include("../include/db_close.php");

?>