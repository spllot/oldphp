<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

include 'member_question_tab.php';
include './include/db_open.php';

$sql = "SELECT * FROM Contact WHERE EMail = '" . $_SESSION['member']['userID'] . "' order by dateSubmited desc";
$result = mysql_query($sql);
$num = mysql_num_rows($result);
$pagesize  = 16;
$pages = ceil($num / $pagesize);
$pageno = $_REQUEST['pageno'];
if($pageno == "" || $pageno > $pages){$pageno = 1;}
$names = array("", "網站問題詢問", "網站建議事項", "商家合作諮詢");
if( $num > 0 ) {
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($rs = mysql_fetch_array($result)){
			$date = substr($rs['dateSubmited'], 0, 10);
			$status = (($rs['dateReplied'] == '0000-00-00 00:00:00') ? "未回覆" : "已回覆");
			$list .= <<<EOD
				<tr>
					<td style="; text-align:center">{$date}</td>
					<td style="text-align:left"><a href="javascript:parent.Dialog('member_question_detail.php?no={$rs['No']}')">{$names[$rs['Catalog']]}</a></td>
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
		<td style="color:gray; padding-left:10px; line-height:30px; line-height:50px">[註]: “我的問題諮詢”係來自前台[商家合作/客服中心]之提問回應。</td>
	</tr>
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td style="background:#000000; color:#FFFFFF; width:150px; text-align:center">日期</td>
					<td style="background:#000000; color:#FFFFFF; text-align:center">問題選項</td>
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

