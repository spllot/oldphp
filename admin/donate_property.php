<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->donate][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["mno"], 0);
$pageno = Tools::parseInt2( $_REQUEST['pageno'], 1);
$page->setHeading($_MODULE->nameOf($_MODULE->donate));
include("../include/db_open.php");
if ($no > 0){
    $result=mysql_query("SELECT * FROM Donate WHERE No = $no");
    if(($num=mysql_num_rows($result))==1){
        $rs = mysql_fetch_array($result);
    }//if
}//if
include("../include/db_close.php");
$html = <<<EOD
<form name="iForm" method="post" action="donate_save.php">
<input type="hidden" name="no" value="$no">
<input type="hidden" name="pageno" value="$pageno">
<table>
	<tr>
		<td class="html_label_required">名稱：</td><Td><input type="text" name="name" style="width:300px" value="{$rs['Name']}"></td>
	</tr>
	<tr>
		<td class="html_label_required">銀行：</td><Td><input type="text" name="bank" style="width:300px" value="{$rs['Bank']}"></td>
	</tr>
	<tr>
		<td class="html_label_required">分行：</td><Td><input type="text" name="branch" style="width:300px" value="{$rs['Branch']}"></td>
	</tr>
	<tr>
		<td class="html_label_required">帳號：</td><Td><input type="text" name="account" style="width:300px" value="{$rs['Account']}"></td>
	</tr>
	<tr>
		<td colspan="2"><hr>
                <table width="100%">
                    <tr>
                        <td align="center" width="50%"><input type="button" value="確定" onClick="Save();"></td>
                        <td align="center" width="50%"><input type="reset" value="取消" onclick="window.location.href='donate.php?pageno=$pageno';"></td>
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
        if (!iForm.name.value){
            alert("請輸入名稱!");
            iForm.name.focus();
        }
		else if(!iForm.bank.value){
			alert("請輸入銀行!");
			iForm.bank.focus();
		}
		else if(!iForm.branch.value){
			alert("請輸入銀行!");
			iForm.branch.focus();
		}
		else if(!iForm.account.value){
			alert("請輸入帳號!");
			iForm.account.focus();
		}
        else{
            iForm.submit();
        }
    }//Save
</script>