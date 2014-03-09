<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->queue][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["mno"], 0);
$userid = $_REQUEST["userid"];
$pageno = Tools::parseInt2( $_REQUEST['pageno'], 1);
$page->setHeading($_MODULE->nameOf($_MODULE->queue));
if ($no > 0){
    include("../include/db_open.php");
    $result=mysql_query("SELECT * FROM queueEMail WHERE No = $no");
    $rs = mysql_fetch_array($result);
    include("../include/db_close.php");
}//if
$content = <<<EOD
    <table cellpadding="0" cellspacing="0" border="0">
        <form name="iForm" action="queue.php">
        <input type="hidden" name="no" value="$no">
        <input type="hidden" name="userid" value="$userid">
		<input type="hidden" name="pageno" value="$pageno">
        <tr>
            <td>
                <table width="100%">
                    <tr>
                        <td align="left" width="100%"><input type="reset" value="回上一頁" onclick="Cancel();"></td>
                    </tr>
                </table><hr>
            </td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td class="html_label_generated">排程日期：</td>
                        <td align="left">{$rs['dateRequested']}</td>
                    </tr>
                    <tr>
                        <td class="html_label_generated">收件人：</td>
                        <td align="left">{$rs['Recipient']}</td>
                    </tr>
                    <tr>
                        <td class="html_label_generated">標題：</td>
                        <td align="left">{$rs['Subject']}</td>
                    </tr>
                    <tr>
                        <td class="html_label_generated" valign="top">內容：</td>
                        <td align="left" bgcolor=white>{$rs['Content']}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td><hr>
                <table width="100%">
                    <tr>
                        <td align="left" width="100%"><input type="reset" value="回上一頁" onclick="Cancel();"></td>
                    </tr>
                </table>
            </td>
        </tr>
        </form>
    </table>
<script language="javascript">
function Cancel(){
	iForm.submit();
}


</script>

EOD;
$page->addContent($content);
$page->show();
?>