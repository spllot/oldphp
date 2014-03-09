<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->templates][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["mno"], 0);
$pageno = Tools::parseInt2( $_REQUEST['pageno'], 1);
$page->setHeading($_MODULE->nameOf($_MODULE->templates));
include("../include/db_open.php");
if ($no > 0){
    $result=mysql_query("SELECT * FROM Template WHERE No = $no");
    if(($num=mysql_num_rows($result))==1){
        $rs = mysql_fetch_array($result);
    }//if
}//if
include("../include/db_close.php");
$html = <<<EOD
<form name="iForm" method="post" action="templates_save.php">
<input type="hidden" name="no" value="$no">
<input type="hidden" name="pageno" value="$pageno">
<table>
	<tr>
		<td class="html_label">程式代碼：</td><Td>{$rs['ID']}</td>
	</tr>
	<tr style="display:none">
		<td class="html_label_required">類型：</td><Td>
			<input type="radio" name="type" value="1">簡訊範本
			<input type="radio" name="type" value="2">Email範本
		</td>
	</tr>
	<tr>
		<td class="html_label_required">標題：</td><Td><input type="text" name="subject" style="width:600px" value="{$rs['Subject']}"></td>
	</tr>
	<tr>
		<td class="html_label">Email範本：</td><Td><textarea name="content" style="width:600px; height:200px">{$rs['Content']}</textarea></td>
	</tr>
	<tr>
		<td class="html_label">站內信息範本：</td><Td><textarea name="message" style="width:600px; height:100px">{$rs['Message']}</textarea></td>
	</tr>
	<tr>
		<td class="html_label">簡訊範本：</td><Td><textarea name="sms" style="width:600px; height:50px">{$rs['SMS']}</textarea></td>
	</tr>
	<tr>
		<td colspan="2"><hr>
                <table width="100%">
                    <tr>
                        <td align="center" width="50%"><input type="button" value="確定" onClick="Save();"></td>
                        <td align="center" width="50%"><input type="reset" value="取消" onclick="history.back();"></td>
                    </tr>
                </table>
		</td>
	</tr>
</table>
	</form>



EOD;
$page->addContent($html);
$page->show();
?>
<script language="javascript">
    function Save(){
        if (!iForm.subject.value){
            alert("請輸入標題!");
            iForm.subject.focus();
        }
        else{
            iForm.submit();
        }
    }//Save
</script>