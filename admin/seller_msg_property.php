<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
require_once '../class/form.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->seller_msg][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["itemno"], 0);
$type1 = Tools::parseInt2($_REQUEST["type"],0);
$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$usefor1 = $_REQUEST['usefor'];
$tab = $_REQUEST['tab'];
$menu = array(
	"seller_msg.php?tab=0" => "買家問題回覆",
	"seller_msg.php?tab=1" => "賣家問題回覆",
);




$page->setHeading($menu, $tab);
include("../include/db_open.php");

if ($no > 0){
	 $sql = "SELECT Help.*, Product.Deliver, Product.Mode, (SELECT Nick FROM Member WHERE No=Help.Seller) AS sName, (SELECT userID FROM Member WHERE No=Help.Seller) AS sEMail, (SELECT Nick FROM Member WHERE No=Help.Member) AS mName, (SELECT userID FROM Member WHERE No=Help.Member) AS mEMail FROM Help INNER JOIN Product ON Product.No = Help.Product WHERE Help.No = '$no'";
	$result=mysql_query($sql);
    if(($num=mysql_num_rows($result))==1){
        $rs = mysql_fetch_array($result);
    }//if
}//if

if($rs['Mode'] == 1){
	if($rs['Deliver'] == 0){
		$tab1=1;
	}
	else{
		$tab1=2;
	}
}
else{
	if($rs['Deliver'] == 0){
		$tab1=4;
	}
	else{
		$tab1=5;
	}
}
$reply = (($rs['dateForward'] != "0000-00-00 00:00:00") ? "轉寄給賣家<br>" : "");
$reply .= (($rs['dateReplied'] != "0000-00-00 00:00:00") ? $rs['dateReplied'] : "");
$reply = (($reply == "") ? "尚未" : $reply);

$names = array("", "網站問題詢問", "網站建議事項", "商家合作諮詢");
$page->addContent("    $init<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">");
$page->addContent("        <form name=\"iForm\" action=\"seller_msg_save.php\" method=\"post\">");
$page->addContent("        <input type=\"hidden\" name=\"itemno\" value=\"$no\">");
$page->addContent("        <input type=\"hidden\" name=\"no\" value=\"$no\">");
$page->addContent("        <input type=\"hidden\" name=\"fields\" value=\"0\">");
$page->addContent("        <input type=\"hidden\" name=\"usefor\" value=\"$usefor1\">");
$page->addContent("        <input type=\"hidden\" name=\"tab\" value=\"$tab\">");
$page->addContent("		<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("        <tr>");
$page->addContent("            <td>");
$page->addContent("                <table id=\"table_page\">");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_generated\" style='width:150px'>日期：</td>");
$page->addContent("                        <td align=\"left\">{$rs['dateSubmited']}</td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
if($tab == 0){
	$page->addContent("                        <td class=\"html_label_generated\" style='width:150px'>賣家商品與資訊：</td>");
	$page->addContent("                        <td align=\"left\" style='font-size:12px; padding:2px'>");
	$page->addContent("<a href='http://{$WEB_HOST}/product" . $tab1 . "_detail.php?no=" . $rs['Product'] . "' target='_blank'>" . $rs['pName'] . "</a><br>");
	$page->addContent("暱稱：" . $rs['sName'] . "<br>");
	$page->addContent("電子郵件：" . $rs['sEMail'] . "<br>");
	$page->addContent("							</td>");
}
else{
	$page->addContent("                        <td class=\"html_label_generated\" style='width:150px'>商品名稱：</td>");
	$page->addContent("                        <td align=\"left\" style='font-size:12px; padding:2px'>");
	$page->addContent("<a href='http://{$WEB_HOST}/product" . $tab1 . "_detail.php?no=" . $rs['Product'] . "' target='_blank'>" . $rs['pName'] . "</a><br>");
	$page->addContent("							</td>");
}
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
if($tab == 0){
	$page->addContent("                        <td class=\"html_label_generated\" style='width:150px'>買家問題詢問內容：</td>");
	$page->addContent("                        <td align=\"left\" style='font-size:12px; padding:2px'>");
	$page->addContent("暱稱：" . $rs['mName'] . "<br>");
	$page->addContent("電子郵件：" . $rs['mEMail'] . "<br>");
	$page->addContent("問題：" . $rs['Content']);
	$page->addContent("							</td>");
}
else{
	$page->addContent("                        <td class=\"html_label_generated\" nowrap>賣家問題詢問內容：</td>");
	$page->addContent("                        <td align=\"left\" style='font-size:12px; padding:2px'>");
	$page->addContent("暱稱：" . $rs['mName'] . "<br>");
	$page->addContent("電子郵件：" . $rs['mEMail'] . "<br>");
	$page->addContent("問題：" . $rs['Content']);
	$page->addContent("							</td>");
}
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_required\" style='width:150px'>回覆：</td>");
$page->addContent("                        <td align=\"left\"><textarea style='width:600px; height:300px' name='reply' id='reply'>{$rs['Reply']}</textarea></td>");
$page->addContent("                    </tr>");
$page->addContent("                    <tr>");
$page->addContent("                        <td class=\"html_label_generated\" style='width:150px'>回覆日期：</td>");
$page->addContent("                        <td align=\"left\">" . $reply . "</td>");
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
		iForm.action = "seller_msg.php";
		iForm.submit();
	}
</script>

<?php

include("../include/db_close.php");

?>