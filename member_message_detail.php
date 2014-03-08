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
$no = $_REQUEST['no'];

$sql = "SELECT *, (SELECT Name FROM Member WHERE userID = Message.Sender) AS sName FROM Message WHERE `To` = '" . $_SESSION['member']['userID'] . "' AND No = '$no'";
$result = mysql_query($sql) or die(mysql_error());
if($rs = mysql_fetch_array($result)){
	mysql_query("UPDATE Message SET Status = 1 where `To` = '" . $_SESSION['member']['userID'] . "' AND No = '$no'");
	echo <<<EOD
		<table width="100%">
			<tr>
				<td nowrap style="width:100px; text-align:right; background:#f3f3f3;">寄件日期：</td>
				<td>{$rs['dateSent']}</td>
			</tr>
			<tr>
				<td nowrap style="width:100px; text-align:right; background:#f3f3f3;">寄&nbsp;&nbsp;件&nbsp;&nbsp;人：</td>
				<td>{$rs['sName']}</td>
			</tr>
			<tr>
				<td nowrap style="width:100px; text-align:right; background:#f3f3f3;">標　　題：</td>
				<td>{$rs['Subject']}</td>
			</tr>
			<tr>
				<td nowrap style="width:100px; text-align:right; background:#f3f3f3;" valign="top">內　　容：</td>
				<td>{$rs['Content']}</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
		<input type="button" class="btn" value="刪除" onClick="Delete();" style="width:100px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" class="btn" value="關閉" onClick="Close();" style="width:100px">
					<Table width="100%" style="display:none">
						<tr>
							<td width="50%" style="text-align:center"><input type="button" class="btn" value="刪除" onClick="Delete();" style="width:100px"></td>
							<td width="50%" style="text-align:center"><input type="button" class="btn" value="關閉" onClick="Close();" style="width:100px"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
EOD;
}

include './include/db_close.php';
?>
<form name="dForm" action="member_message_delete.php" target="iAction">
	<input type="hidden" name="no" value="<?=$no?>">
</form>
<script language='javascript'>
function Delete(){
	document.dForm.submit();
}

function Close(){
	parent.$.fn.colorbox.close();
}

</script>