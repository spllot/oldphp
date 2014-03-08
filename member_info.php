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
$result = mysql_query("SELECT *, (SELECT IFNULL(SUM(Amount),0) FROM logStock WHERE logStock.Owner=Member.userID) AS Stock FROM Member WHERE userID = '" . $_SESSION['member']['userID'] . "'");
$data = mysql_fetch_array($result);

$subscribe = (($data['Subscribe'] == 1) ? " CHECKED":"");
$result = mysql_query("SELECT * FROM Catalog WHERE USEFOR='TYPE_AREA' ORDER BY Sort");
while($rs = mysql_fetch_array($result)){
	$area_list .= "<option value='{$rs['No']}'" . (($rs['No'] == $data['subscribeArea']) ? " SELECTED":"") . ">{$rs['Name']}</option>";
}


$sql = "SELECT DISTINCT LEFT(dateLogin, 10) FROM logLogin WHERE Status=1 AND userID = '" . $_SESSION['member']['userID'] . "'";// AND Year(dateLogin) = '" . date('Y') . "' AND Month(dateLogin) = '" . date('n') . "'";
//echo $sql;
$result = mysql_query($sql);
$days = mysql_num_rows($result);
//$days = $data['Days'];

$result = mysql_query("SELECT IFNULL(COUNT(*), 0)FROM Member WHERE Referral = '" . $_SESSION['member']['Phone'] . "' AND Phone <> '" . $_SESSION['member']['Phone'] . "'");
$rs = mysql_fetch_row($result);
$referral = $rs[0];

$result = mysql_query("SELECT IFNULL(COUNT(*), 0)FROM Member WHERE Referral = '" . $_SESSION['member']['Phone'] . "' AND Phone <> '" . $_SESSION['member']['Phone'] . "' AND No IN (SELECT Member FROM Product WHERE dateApprove <> CURRENT_TIMESTAMP)");
$rs = mysql_fetch_row($result);
$create = $rs[0];


$left = 0;
$max = 0;
$use = 0;
$curr = date('Y-m');

$result = mysql_query("SELECT * FROM Config WHERE ID='$curr'");
if($rs = mysql_fetch_array($result)){
	$max = $rs['YN'];
}

$result = mysql_query("SELECT IFNULL(COUNT(*), 0) FROM Blog WHERE dateSubmited LIKE '$curr%'");
if($rs = mysql_fetch_row($result)){
	$use = $rs[0];
}

$left = $max - $use;

$b_disabled = (($left <=0) ? " disabled" : "");

$result = mysql_query("SELECT * FROM Blog WHERE dateSubmited LIKE '$curr%' AND userID = '" . $_SESSION['member']['userID'] . "'");
if(mysql_num_rows($result) > 0){
	$blog = mysql_fetch_array($result);
	if($blog['dateConfirmed'] != "0000-00-00 00:00:00"){
		$score = <<<EOD
		本月部落格行銷文章總得分 (1分=1點儲值金)：{$blog['Earn']}</font><br>
		(得分：行銷說服力：{$blog['S1']}</font>，表達能力：{$blog['S2']}</font>，豐富性：{$blog['S3']}</font>，推薦人數：{$blog['S4']}</font>，回應人數：{$blog['S5']}</font>)
EOD;
	}
	else{
		$score = <<<EOD
		本月部落格行銷文章總得分 (1分=1點儲值金)：審核中</font><br>
EOD;
	}
	$b_disabled = " disabled";
}
else{
	$score = <<<EOD
		本月部落格行銷文章總得分 (1分=1點儲值金)：尚未申請</font><br>
EOD;
}

$result = mysql_query("SELECT IFNULL(SUM(Amount), 0) FROM logTransaction WHERE useFor=6 AND Owner='" . $_SESSION['member']['userID'] . "'");
list($money_blog) = mysql_fetch_row($result);
$result = mysql_query("SELECT IFNULL(SUM(Amount), 0) FROM logTransaction WHERE useFor=7 AND Owner='" . $_SESSION['member']['userID'] . "'");
list($money_share) = mysql_fetch_row($result);
$result = mysql_query("SELECT IFNULL(SUM(Amount), 0) FROM logTransaction WHERE Owner='" . $_SESSION['member']['userID'] . "'");
list($money_total) = mysql_fetch_row($result);



$complete = 0;
$refund = 0;

$result = mysql_query("SELECT IFNULL(SUM(Amount), 0) FROM Orders WHERE Status=1 AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
list($complete)=mysql_fetch_row($result);


$result = mysql_query("SELECT IFNULL(SUM(Amount), 0) FROM Items WHERE Refund=1 AND orderID IN (SELECT ID FROM Orders WHERE Status=1 AND Member='" . $_SESSION['member']['No'] . "')") or die(mysql_error());
//echo "SELECT IFNULL(SUM(Amount), 0) FROM Items WHERE Refund=1 AND orderID IN (SELECT ID FROM Orders WHERE Status=1 AND Member='" . $_SESSION['member']['No'] . "')";
list($refund)=mysql_fetch_row($result);

$ss = $days + $referral + $create;


$result = mysql_query("SELECT *, datediff(dateExpire, CURRENT_TIMESTAMP) AS L FROM AD WHERE datediff(dateExpire, CURRENT_TIMESTAMP) >=-7 AND Member = '" . $_SESSION['member']['No'] ."' ORDER BY dateExpire DeSC");
while($rs=mysql_fetch_array($result)){
	$left = (($rs['L'] > 0) ? "剩餘：<font color=blue>" . $rs['L'] . "</font>天" : "<font color=blue>已播畢</font>");
	$ads .= "<font color=blue>" . substr($rs['dateSubmit'], 0, 10) . "</font>　前台右側廣告：抵付儲值金 <font color=blue>&#36;{$rs['Cost']}</font>，日數：<font color=blue>{$rs['Days']}</font>天，{$left}<br>";
}
$result = mysql_query("SELECT *, datediff(dateExpire, CURRENT_TIMESTAMP) AS L FROM AD2 WHERE datediff(dateExpire, CURRENT_TIMESTAMP) >=-7 AND Member = '" . $_SESSION['member']['No'] ."' ORDER BY dateExpire DeSC");
while($rs=mysql_fetch_array($result)){
	$left = (($rs['L'] > 0) ? "剩餘：<font color=blue>" . $rs['L'] . "</font>天" : "<font color=blue>已播畢</font>");
	$ads .= "<font color=blue>" . substr($rs['dateSubmit'], 0, 10) . "</font>　前台下方分類廣告：抵付儲值金 <font color=blue>&#36;{$rs['Cost']}</font>，日數：<font color=blue>{$rs['Days']}</font>天，{$left}<br>";
}
if(strlen($ads) > 0){
	$ad = <<<EOD
		<tr>
			<td colspan="2" style="border-bottom:solid 1px gray;text-align:left; padding-top:20px" align="left">
				$ads					
			</td>
		</tr>
EOD;
}
 

$result = mysql_query("SELECT *, (SELECT COUNT(*) FROM Member WHERE Referral = Project.Code) AS N1, (SELECT COUNT(*) FROM Member WHERE Referral = Project.Code AND No IN (SELECT Member FROM Product WHERE dateApprove <> '0000-00-00 00:00:00')) AS N2 FROM Project WHERE Code = '" . $_SESSION['member']['Referral'] . "' ORDER BY Code") or die(mysql_error());
if(mysql_num_rows($result) > 0){
	while($rs = mysql_fetch_array($result)){
		$project .= "<br>(專案代碼與說明：<a href=\"javascript:parent.Dialog('project.php?no={$rs['No']}');\">{$rs['Code']}</a>，專案會員數：<a href=\"javascript:parent.Dialog('project_referral.php?no={$rs['No']}');\">{$rs['N1']}</a>人，已建置商品之會員與編號：<a href=\"javascript:parent.Dialog('project_referral_create.php?no={$rs['No']}');\">{$rs['N2']}</a>人)";
		
	}
}

$curr = $date = date('Y-m');

$result = mysql_query("SELECT * FROM Config WHERE ID='{$curr}S'");
if($rs=mysql_fetch_array($result)){
	$price = $rs['YN'];
}

include './include/db_close.php';
$WEB_CONTENT = <<<EOD
<center>
<table border=0>
	<tr>
		<td style="border-bottom:solid 1px gray; line-height:40px; text-align:left; font-weight:bold">帳戶總覽</td>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
					<td colspan="2" style="text-align:left; padding-top:20px" align="left">歡迎您，{$_SESSION['member']['Nick']}</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:left" align="left">上次登入時間：<font color=blue>{$_SESSION['member']['dateLogin']}</font></td>
				</tr>
				<tr>
					<td colspan="2" style="border-bottom:solid 1px gray;text-align:left" align="left">上次登入IP：<font color=blue>{$_SESSION['member']['ipLogin']}</font></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:left; padding-top:20px" align="left">會員優惠等級：<font color=blue>{$_SESSION['member']['Level']}</font>等級，總數值：<font color=blue>{$ss}</font> (請參考：<a href="javascript:parent.Dialog('member_upgrade.php');">會員升級標準</a>)</td>
				</tr>
				<tr>
					<td colspan="2" style="border-bottom:solid 1px gray;text-align:left" align="left">(登入天數：<font color=blue>{$days}</font>天，會員介紹數：<a href="javascript:parent.Dialog('member_referral.php')">{$referral}</a>人，已建置商品之會員與編號：<a href="javascript:parent.Dialog('member_referral_create.php')">{$create}</a>人){$project}</td>
				</tr>
				
				<tr>
					<td colspan="2" style="border-bottom:solid 1px gray;text-align:left; padding-top:20px" align="left">
					$score
					</td>
				</tr>
				{$ad}
				<tr>
					<td colspan="2" style="text-align:left; padding-top:20px" align="left">目前每月可傳播商品數：<font color=blue>xx</font>商品</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:left" align="left">目前未結儲值金之商品介紹個數 (紅利10%)：<a href="javascript:parent.Dialog('member_bonus_product.php')">xx</font></a></td>
				</tr>
				<tr>
					<td colspan="2" style="border-bottom:solid 1px gray;text-align:left" align="left">目前未結儲值金之傳播商品數目：<a href="javascript:parent.Dialog('member_bonus_buy.php')">xx</font></a></td>
				</tr>
				<tr style="display:none">
					<td colspan="2" style="text-align:left; padding-top:20px" align="left">本月傳播商品文案個數 (紅利5%)：xx</font></td>
				</tr>
				<tr style="display:none">
					<td colspan="2" style="text-align:left" align="left">Blog行銷儲值金累積點數: {$money_blog}</font> 元</td>
				</tr>
				<tr style="display:none">
					<td colspan="2" style="text-align:left" align="left">商品傳播儲值金累積點數: {$money_share}</font> 元</td>
				</tr>
				<tr>
					<td colspan="2" style="border-bottom:solid 1px gray;text-align:left; padding-top:20px" align="left">金流商品成交記錄數：<font color=blue>{$complete}</font>，金流商品退貨記錄數：<font color=blue>{$refund}</font></td>
				</tr>
				<tr>
					<td colspan="2" style="border-bottom:solid 1px gray;text-align:left; padding-top:20px" align="left">儲值金點數：<font color=blue>{$money_total}</font>元</td>
				</tr>

				<tr>
					<td colspan="2" style="border-bottom:solid 1px gray;text-align:left; padding-top:20px" align="left">股數：<font color=blue>{$data['Stock']}</font>&nbsp;&nbsp;&nbsp;&nbsp;(目前每股<font color=blue>{$price}</font>元)</td>
				</tr>

				<tr>
					<td style="text-align:left; width:30px; color:gray" align="left">註：</td>
					<td style="text-align:left; color:gray" align="left">
						部落格行銷&傳播商品獲得儲值金的方法，請參考商品首頁「全民賺好康」說明。
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br>
</center>
EOD;
include 'template2.php';
?>
<script language="javascript">
function Blog(){
	parent.Dialog('blog_add.php');
}

</script>
