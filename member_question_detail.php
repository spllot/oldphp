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

$sql = "SELECT *, (SELECT Name FROM Member WHERE userID = Contact.EMail) AS sName FROM Contact WHERE EMail = '" . $_SESSION['member']['userID'] . "' AND No = '$no'";
$result = mysql_query($sql) or die(mysql_error());
$names = array("", "網站問題詢問", "網站建議事項", "商家合作諮詢");
if($rs = mysql_fetch_array($result)){
	mysql_query("UPDATE Contact SET Status = 1 where `To` = '" . $_SESSION['member']['userID'] . "' AND No = '$no'");
	$content = "";
	switch($rs['Catalog']){
		case 1:
			$content .= "姓名：" . $rs['Name'] . "<br>";
			$content .= "電子郵件：" . $rs['EMail'] . "<br>";
			$content .= "問題：" . $rs['Content'] . "";
			break;
		case 2:
			$content .= "姓名：" . $rs['Name'] . "<br>";
			$content .= "電子郵件：" . $rs['EMail'] . "<br>";
			$content .= "建議：" . $rs['Content'] . "";
			break;
		case 3:
			$content .= "商家(商品)名稱：" . $rs['Name'] . "<br>";
			$content .= "網站介紹：" . $rs['Intro'] . "<br>";
			$content .= "電子郵件：" . $rs['EMail'] . "<br>";
			$content .= "聯絡人：" . $rs['Contact'] . "<br>";
			$content .= "聯絡電話：" . $rs['Phone'] . "";
			break;
	}
	$date_reply = (($rs['dateReplied'] == '0000-00-00 00:00:00') ? "尚未" : $rs['dateReplied']);
	echo <<<EOD
		<table width="100%">
			<tr>
				<td nowrap style="width:100px; text-align:right; background:#f3f3f3;">諮詢日期：</td>
				<td>{$rs['dateSubmited']}</td>
			</tr>
			<tr>
				<td nowrap style="width:100px; text-align:right; background:#f3f3f3;">問題選項：</td>
				<td>{$names[$rs['Catalog']]}</td>
			</tr>
			<tr>
				<td nowrap style="width:100px; text-align:right; background:#f3f3f3;" valign="top">內　　容：</td>
				<td>{$content}</td>
			</tr>
			<tr>
				<td nowrap style="width:100px; text-align:right; background:#f3f3f3;" valign="top">回　　覆：</td>
				<td>{$rs['Reply']}</td>
			</tr>
			<tr>
				<td nowrap style="width:100px; text-align:right; background:#f3f3f3;" valign="top">回覆日期：</td>
				<td>{$date_reply}</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="button" class="btn" value="刪除" onClick="Delete();" style="display:none"> &nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" class="btn" value="關閉" onClick="Close();">
				</td>
			</tr>
		</table>
EOD;
}

include './include/db_close.php';
?>
<form name="dForm" action="member_question_delete.php" target="iAction">
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