<?php
include './include/session.php';
include './include/db_open.php';
include 'profit_tab.php';

$pageno = $_REQUEST['pageno'];


$result = mysql_query("SELECT Member.Nick, Blog.Subject, Blog.toStock, Blog.sPrice, Blog.Url, Blog.Earn, Blog.dateConfirmed FROM Blog INNER JOIN Member ON Member.userID=Blog.userID WHERE transactionNo <> 0 ORDER BY dateConfirmed DESC") or die(mysql_error());
$num = mysql_num_rows($result);
$pagesize  = 20;
$pages = ceil($num / $pagesize);
$pageno = $_REQUEST['pageno'];
if($pageno == "" || $pageno > $pages){$pageno = 1;}
if ($num>0){
	mysql_data_seek($result,($pageno-1)*$pagesize);
	for ($i = 0; $i < $pagesize; $i++) {
		if($rs=mysql_fetch_array($result)){
//			$max = $num - $pagesize * ($pageno-1);
			$url = substr($rs['Url'], 0, 20) . ((strlen($rs['Url']) > 20) ? "..." : "");
			$date = substr($rs['dateConfirmed'], 0, 10);
			$border = "; border-bottom: solid " . (($i<$max-1) ? "1": "2") . "px gray";
			$earn = (($rs['toStock'] == 0) ? "儲值金：" . $rs['Earn'] : "股數：" . round($rs['Earn']*3/$rs['sPrice']));
			$list .= <<<EOD
				<tr>
					<td style="padding:5px; text-align:center{$border}; font-size:10pt" nowrap>{$date}</td>
					<td style="padding:5px; text-align:center{$border}; font-size:10pt">{$rs['Subject']}</td>
					<td style="padding:5px; text-align:center{$border}; font-size:10pt"><a href="{$rs['Url']}" target="blank">{$url}</a></td>
					<td style="padding:5px; text-align:center{$border}; font-size:10pt" nowrap>{$rs['Nick']}</td>
					<td style="padding:5px; text-align:center{$border}; font-size:10pt" nowrap>{$earn}</td>
				</tr>
EOD;
		}
		else{
			break;
		}
	}

	$pageinfo = "		<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">";
    $pageinfo .= "           <tr>";
	if($pageno > 1){
		$pageinfo .= "             <td style='width:74px; height:25px; text-align:center; background-image:url(./images/btn_100_black.jpg1); background-repeat:no-repeat; background-position: center center'>";
		$pageinfo .= "				<a href=\"javascript:" . (($pageno > 1) ? "setPage(" . ($pageno - 1) . ")" : "void(0)"). ";\" style='color:white; text-decoration:underline'>上一頁</a>";
	}
	else{
		$pageinfo .= "             <td style='width:74px; height:25px; text-align:left; padding-left:10px;'>&nbsp;";
	}
	$pageinfo .= "			  </td>";
    $pageinfo .= "             <td align=\"center\" nowrap><table><tr>";
	for($i=0; $i<$pages; $i++){
		$p = "<div style='width:18px; height:18px; border:solid 0px black; line-height:18px'>" . ($i+1) . "</div>";
		if(($i+1)==$pageno){
			$pageinfo .= "<td style='text-decoration:underline; width:20px; color:black; text-align:center'>" . $p . "</td>";		
		}
		else{
			$pageinfo .= "<td onClick=\"javascript:setPage(" . ($i+1) . ");\" style='cursor:pointer; color:black; text-decoration:none; width:20px; text-align:center'>" . $p . "</td>";		
		}
	}
	$pageinfo .= "			</tr></table></td>";
	if($pageno < $pages){
		$pageinfo .= "			<td style='width:74px; height:25px; text-align:center; background-image:url(./images/btn_100_black.jpg1); background-repeat:no-repeat; background-position: center center'>";
		$pageinfo .= "				<a href=\"javascript:" . (($pageno < $pages) ? "setPage(" . ($pageno + 1) . ")" : "void(0)") . ";\" style='color:white; text-decoration:underline'>下一頁</a>";
	}
	else{
		$pageinfo .= "             <td style='width:74px; height:25px; text-align:left; padding-left:10px;'>&nbsp;";
	}
	$pageinfo .= "			</td>";
	$pageinfo .= "			</tr>";
	$pageinfo .= "		</table>";
}
else{
	$list .= <<<EOD
		<tr>
			<td colspan="4" style="text-align:center">查無資料</td>
		</tr>
EOD;
}
$WEB_CONTENT = <<<EOD
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td style="padding:10px; text-align:center; font-size:14pt">會員獲利公告</td>
	</tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center">
		<center>
		<table width="95%" cellpadding="0" cellspacing="0" border="0">
			<tr style="height:10px"></tr>
			<tr>
				<td style="width:100px;padding:2px; border-bottom:solid 2px gray; text-align:center; background:#b5b2b5; height:25px; font-size:10pt" nowrap>發放日期</td>
				<td style="padding:2px; border-bottom:solid 2px gray; text-align:center; background:#b5b2b5; height:25px; font-size:10pt">文章主題</td>
				<td style="width:200pxpadding:2px; border-bottom:solid 2px gray; text-align:center; background:#b5b2b5; height:25px; font-size:10pt">文章網址</td>
				<td style="width:80px;padding:2px; border-bottom:solid 2px gray; text-align:center; background:#b5b2b5; height:25px; font-size:10pt" nowrap>會員</td>
				<td style="width:100px;padding:2px; border-bottom:solid 2px gray; text-align:center; background:#b5b2b5; height:25px; font-size:10pt" nowrap>獎勵所得</td>
			</tr>
			{$list}
		</table>
		</center>
		</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center; padding-top:10px;">{$pageinfo}</td>
	</tr>
</table>

<br>
<br>
<br>

EOD;




include './include/db_close.php';
include 'template.php';
?>
