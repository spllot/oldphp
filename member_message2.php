<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

include 'member_message_tab.php';

include './include/db_open.php';

$sql = "SELECT *, (SELECT Name FROM Member WHERE userID = Message.Sender) AS sName FROM Message WHERE Sender = 'service@intimego.com' AND `To` = '" . $_SESSION['member']['userID'] . "' AND Type = '1' order by dateSent desc";
$result = mysql_query($sql);
$num = mysql_num_rows($result);
$pagesize  = 16;
$pages = ceil($num / $pagesize);
$pageno = $_REQUEST['pageno'];
if($pageno == "" || $pageno > $pages){$pageno = 1;}
if( $num > 0 ) {
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($rs = mysql_fetch_array($result)){
			$date = substr($rs['dateSent'], 0, 10);
			$status = (($rs['Status'] == 0) ? "未讀取" : "已讀取");
			$list .= <<<EOD
				<tr>
					<td style="; text-align:center">{$date}</td>
					<td style="; text-align:center">{$rs['sName']}</td>
					<td style="text-align:left"><a href="javascript:parent.Dialog('member_message_detail.php?no={$rs['No']}')">{$rs['Subject']}</a></td>
					<td style="; text-align:center">$status</td>
				</tr>
EOD;
		}
		else{
			break;
		}
	}
}
else{
	$list = <<<EOD
		<tr>
			<td colspan="5" style="color:gray; text-align:center">查無資料</td>
		</tr>
EOD;
}




include './include/db_close.php';


$WEB_CONTENT = <<<EOD

<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td style="color:gray; padding-left:10px; line-height:30px; line-height:50px">[註]: “站內訊息通知”為網站發佈給一般會員之私人/系統/公共訊息。</td>
	</tr>
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td style="background:#000000; color:#FFFFFF; width:150px; text-align:center">日期</td>
					<td style="background:#000000; color:#FFFFFF; width:100px; text-align:center">寄件者</td>
					<td style="background:#000000; color:#FFFFFF; text-align:center">標題</td>
					<td style="background:#000000; color:#FFFFFF; width:80px; text-align:center">狀態</td>
				</tr>
				$list
			</table>
		</td>
	</tr>
	<tr>
		<td></td>
	</tr>
</table>


EOD;

include 'template2.php';
?>

