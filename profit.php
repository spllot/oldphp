<?php
include './include/session.php';
require_once getcwd() . '/class/facebook.php';
function fetchUrl($url){
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_TIMEOUT, 20);
     $retData = curl_exec($ch);
     curl_close($ch); 
 
     return $retData;
}

include './include/db_open.php';
include 'profit_tab.php';
$pageno = $_REQUEST['pageno'];
$result = mysql_query("SELECT *, (SELECT COUNT(*) FROM logActivity WHERE Product=Product.No) AS Joins FROM Product WHERE dateApprove <> '0000-00-00 00:00:00' AND Activity=1 AND Draw <> '0000-00-00 00:00:00' ORDER BY Draw DESC") or die(mysql_error());
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
			$date = substr($rs['Draw'], 0, 10);
			$f = fetchUrl("https://graph.facebook.com/" . $rs['activity_page']);
			$p = json_decode($f);
			$link = "<a target='_blank' href='" . $p->{'link'} . "'>" . $p->{'name'} . "</a>";//'<div class="fb-like-box" data-href="' . $p->{'link'} . '" data-width="" data-show-faces="false" data-stream="false" data-header="true"></div>';
			$border = "; border-bottom: solid " . (($i<$max-1) ? "1": "2") . "px gray";
			$url = substr($rs['activity_email'], 0, 20) . ((strlen($rs['activity_email']) > 20) ? "..." : "");
			if ($rs['Joins'] >= $rs['activity_min']){
				$result1 = mysql_query("SELECT * FROM logActivity WHERE Win=1 AND Product='" . $rs['No'] . "' ORDER BY dateJoined") or die(mysql_error());
				while($rs1=mysql_fetch_array($result1)){
					try{$naitik = $facebook->api('/' . $rs1['fbID']);} catch (FacebookApiException $e) {}
					$winner .= "<a href='http://www.facebook.com/profile.php?id={$rs1['fbID']}' target='_blank'>{$naitik['name']}</a><br>";
				}
			}
			else{
				$winner = "無得獎者，參與人數未達門檻";
			}
			
			$list .= <<<EOD
				<tr>
					<td style="padding:5px; text-align:center{$border}; font-size:10pt; width:80px" nowrap>{$date}</td>
					<td style="padding:5px; text-align:center{$border}; font-size:10pt">{$rs['Name']}</td>
					<td style="padding:5px; text-align:center{$border}; font-size:10pt">{$link}</td>
					<td style="padding:5px; text-align:center{$border}; font-size:10pt; width:120px">{$rs['activity_quota']}<br>{$winner}</td>
					<td style="padding:5px; text-align:center; font-size:9pt{$border}">{$url}</td>
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
			<td colspan="6" style="text-align:center">查無資料</td>
		</tr>
EOD;
}

$WEB_CONTENT = <<<EOD
<div id="fb-root"></div>
<script>
			window.fbAsyncInit = function() {
			  FB.init({
				appId      : '223714571074260',
				status     : true, 
				cookie     : true,
				xfbml      : true,
				oauth      : true
			  });
			  FB.Event.subscribe('auth.login', function() {
				window.location.reload();
			  });
			  FB.Event.subscribe('auth.logout', function() {
				window.location.reload();
			  });
			 FB.Event.subscribe('edge.create',
				function(response) {
					window.location.reload();
				}
			);
		};
		(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/zh_TW/all.js#xfbml=1&appId=223714571074260";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


</script><table style="width:100%" cellpadding="0" cellspacing="0">
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
				<td style="padding:2px; border-bottom:solid 2px gray; text-align:center; font-size:10pt; background:#b5b2b5; width:80px; height:25px" nowrap>開獎日期</td>
				<td style="padding:2px; border-bottom:solid 2px gray; text-align:center; font-size:10pt; background:#b5b2b5; height:25px" nowrap>活動商品名稱</td>
				<td style="padding:2px; border-bottom:solid 2px gray; text-align:center; font-size:10pt; background:#b5b2b5; height:25px">推廣粉絲團</td>
				<td style="padding:2px; border-bottom:solid 2px gray; text-align:center; font-size:10pt; background:#b5b2b5; width:120px; height:25px" nowrap>抽獎數量 / 得獎者</td>
				<td style="padding:2px; border-bottom:solid 2px gray; text-align:center; font-size:10pt; background:#b5b2b5; height:25px" nowrap>主辦聯絡郵件</td>
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
