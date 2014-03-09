<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->seller_export][1])){exit("權限不足!!");}
$no = $_REQUEST['no'];
$Y = $_REQUEST['Y'];
$M = $_REQUEST['M'];

JavaScript::setCharset("UTF-8");
if($no != ""){
	include '../include/db_open.php';
	$result = mysql_query("SELECT *, IFNULL((SELECT Type FROM logReceipt WHERE Seller=Member.No AND Y='$Y' AND M='$M'), 2) AS Type, (SELECT Title FROM logReceipt WHERE Seller=Member.No AND Y='$Y' AND M='$M') AS Title FROM Member WHERE No='$no'") or die(mysql_error());
	$member = mysql_fetch_array($result);
	$receipt = array("", "捐贈慈善", "紙本寄送", "電子發票");
	$title = (($member['Title'] == 1) ? $member['rName'] : "");
	$unino = (($member['Title'] == 1) ? $member['uniNo'] : "");
	echo <<<EOD
		<html style="width:500px; height:400px">
		<script language='javascript' src='../js/zip.js'></script>
		<form name="hForm">
		<table width="100%" height="100%" align="center">
			<Tr>
				<tD>
		<table style="background:#606060; border:solid 2px #606060" cellpadding="1" cellspacing="1" align="center">
			<tr>
				<td style="color:white; text-align:left; background:#909090;">商家聯絡資訊(商家：{$member['Name']})</td>
			</tr>
			<tr>
				<td align="center" style="background:#FFFFFF">
					<table align="center">
						<tr>
							<td align="right">手機號碼：</td>
							<td align="left">{$member['Phone']}</td>
						</tr>
						<tr>
							<td align="right">E-mail/帳號：</td>
							<td align="left">{$member['userID']}</td>
						</tr>
						<tr>
							<td align="right">商家姓名：</td>
							<td align="left">{$member['Name']}</td>
						</tr>
						<tr>
							<td align="right">郵遞區號：</td>
							<td align="left">{$member['rZip']}
								<input type="hidden" name="rzip" value="">
								<select name="county" disabled></select>
								<select name="area" disabled></select>
							</td>
						</tr>
						<tr>
							<td align="right">商家住址：</td>
							<td align="left">{$member['rAddress']}</td>
						</tr>
						<tr>
							<td align="right">發票類別：</td>
							<td align="left">{$receipt[$member['Type']]}</td>
						</tr>
						<tr>
							<td align="right">統一編號：</td>
							<td align="left">{$unino}</td>
						</tr>
						<tr>
							<td align="right">發票抬頭：</td>
							<td>{$title}</td>
						</tr>
						<tr>
							<td align="right">鋃行名稱/分支行：</td>
							<td align="left">{$member['Bank']} / {$member['Branch']}</td>
						</tr>
						<tr>
							<td align="right">匯款帳號：</td>
							<td align="left">{$member['Account']}</td>
						</tr>
					</table>
					<br>
					<input type="button" value="確定" onClick="window.close();">	
					<br><Br>
				</td>
			</tr>
		</table>
				</td>
			</tr>
		</table>
			</form>
<script language="javascript">
genCounty(document.hForm.county);
chgCounty(document.hForm.county, hForm.area);
chgArea(document.hForm.area, hForm.rzip);
document.hForm.rzip.value = "{$member['rZip']}";
setArea(document.hForm.county, hForm.area, hForm.rzip.value);
</script>
EOD;
	include '../include/db_close.php';
}
?>


	