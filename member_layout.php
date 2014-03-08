<?php
$ip = ((getenv(HTTP_X_FORWARDED_FOR)) ?  getenv(HTTP_X_FORWARDED_FOR) :  getenv(REMOTE_ADDR));
include './include/db_open.php';
if(!empty($_SESSION['member']) && substr($_SESSION['member']['dateLogin'], 0, 10) != date('Y-m-d')){
	mysql_query("UPDATE Member SET Days = Days + 1 WHERE userID = '" . $_SESSION['member']['userID'] . "'");
    $_SESSION['member']['dateLogin'] = date('Y-m-d H:i:s');
			mysql_query("UPDATE Member SET Level = 2 WHERE userID = '$userid' AND Days >= 15  AND Days <30");
			mysql_query("UPDATE Member SET Level = 3 WHERE userID = '$userid' AND Days >= 30  AND Days <60");
			mysql_query("UPDATE Member SET Level = 4 WHERE userID = '$userid' AND Days >= 60  AND Days <120");
			mysql_query("UPDATE Member SET Level = 5 WHERE userID = '$userid' AND Days >= 120  AND Days <240");
			mysql_query("UPDATE Member SET Level = 6 WHERE userID = '$userid' AND Days >= 240  AND Days <480");
			mysql_query("UPDATE Member SET Level = 7 WHERE userID = '$userid' AND Days >= 480  AND Days <960");
			mysql_query("UPDATE Member SET Level = 8 WHERE userID = '$userid' AND Days >= 960");
	$sql = "INSERT INTO logLogin(dateLogin, userID, Status, ipLogin) VALUES (CURRENT_TIMESTAMP, '" . $_SESSION['member']['userID'] . "', '1', '$ip')";
	mysql_query($sql) or die (mysql_error());
}
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}





include './include/db_close.php';
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="keywords" content="即時服務, 物流查詢, 運輸共乘, 即時人力, 即時活動, 安全監護, 雲端服務"/>
<meta name="description" content="<?=$fb_desc?>"/>
<meta property="og:title" content="<?=$fb_title?>" />
<meta property="og:description" content="<?=$fb_desc?>" />
<meta property="og:image" content="<?=$fb_thumb?>" />
<title>InTimeGo 即時服務 <?=$fb_title?></title>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-37407627-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script><link rel="image_src" type="image/jpeg" href="<?=$fb_thumb?>">
<link type="text/css" href="js/themes/base/ui.all.css" rel="stylesheet" />
<link type="text/css" href="style.css?<?=time()?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="js/jquery.colorbox.css" media="screen" />
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jjquery.blockUI.js"></script>
<script type="text/javascript" src="js/zip.js?20121126"></script>
<script type="text/javascript" src="js/jquery.colorbox.js"></script>
<script language="javascript" src="./js/facebook_show.js"></script>
<script src="./js/oslide.js" language="javascript" type="text/javascript"></script>
<script src="./js/easing.js" language="javascript" type="text/javascript"></script>
<script language="javascript" src="./js/scrollbar.js"></script>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/zh_TW/all.js#xfbml=1&appId=223714571074260";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<script language="javascript">
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
</script>

<?
$current = basename($_SERVER['PHP_SELF']);

?>

<script language="javascript">
/**/

	var selected = "";
	var select1 = null;
	function setTab(n){
		if(n != selected){
			if(selected){
				document.getElementById("tab"+selected).style.backgroundImage = "url('./images/tab" + selected + ".gif')";
			}
			selected = n;
			document.getElementById("tab"+n).style.backgroundImage = "url('./images/tab" + n + "_selected.gif')";
		}
		
	}
	function mClk(n, x, url){
		//團購建置中
		if(n == 1 || n == 2) return;
		if(selected){
			document.getElementById("tab"+selected).style.backgroundImage = "url('./images/tab" + selected + ".gif')";
		}
		if(select1){
			select1.className=select1.className.replace("_selected", "");
			select1 = null;
		}
		selected = n;
		document.getElementById("tab"+n).style.backgroundImage = "url('./images/tab" + n + "_selected.gif')";
		iContent.location.href = url;
	}
	function mOvr(n, x){
		if(n != selected){
			document.getElementById("tab"+n).style.backgroundImage = "url('./images/tab" + n + "_over.gif')";
		}
	}
	function mOut(n, x){
		if(n != selected){
			document.getElementById("tab"+n).style.backgroundImage = "url('./images/tab" + n + ".gif')";
		}
	}



	function mOvr1(x, n){
		if(x != select1){
			x.className = "menu" + n + "_over";
		}
	}
	function mOut1(x, n){
		if(x != select1){
			x.className = "menu" + n;
		}
	}
	function mCli1(x, url, n){
		if(select1){
			select1.className=select1.className.replace("_selected", "");
		}
		if(selected){
			document.getElementById("tab"+selected).style.backgroundImage = "url('./images/tab" + selected + ".gif')";
			selected = "";
		}
		select1 = x;
		select1.className = "menu" + n + "_selected";
		iContent.location.href=url;
	}
</script>
<body topmargin="0" leftmargin="0" bottommargin="0" rightmargin="0" margin="0" onload="MM_preloadImages('images/green_bar_up.gif','images/green_bar_down.gif');" style="background:url('./images/body9.jpg'); background-position:center center">
<center>
<table width="960" style="background:#98cd01; width:960px; border-right:solid 13px #98cd01" cellpadding="0" cellspacing="0">
	<tr>
		<td><?include 'banner.php';?></td>
	</tr>
	<tr>
		<td>
			<table style="width:100%" cellpadding="0" cellspacing="0">
				<tr>
					<td style="vertical-align:top" valign="top">
						<table style="width:100%" style="width:100%; height:100%" cellpadding="0" cellspacing="0">	
							<tr>
								<td><?include 'member_tab.php';?></td>
							</tr>
							<tr>
								<td style="background:#525552; height:13px;"></td>
							</tr>
							<tr>
								<td style="; background:#525552; border-left:solid 13px #525552; border-right:solid 13px #525552; border-bottom:solid 13px #525552; text-align:center; vertical-align:top" align="center" valign="top"><iframe style="width:100%; height:2834px;border:0px none" name="iContent"  scrolling="no" frameborder="0" marginwidth="0" marginheight="0"></iframe></td>
							</tr>
							<tr style="display:none">
								<td style="padding:13px; height:77px;"><iframe style="width:100%; height:77px;border:0px none; background:#98cd01" name="iAD"  scrolling="no" frameborder="0" marginwidth="0" marginheight="0"></iframe></td>
							</tr>

						</table>
					</td>
					<td style="width:215px; background:white; vertical-align:top" valign="top">
						<table style="width:100%; height:100%" width="100%" height="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td style="background:#98cd01; vertical-align:top; padding-top:0px; padding-left:13px; padding-bottom:13px; height:108px">
									<table width="100%" cellspacing="0" cellpadding="0" border="0">
										<tr>
											<td class="menu1" id="menu1" onClick="mCli1(this, 'new.php', 1);" onMouseOver="mOvr1(this, 1);" onMouseOut="mOut1(this, 1);" style="">&nbsp;</td>
										</tr>
										<tr>
											<td class="menu2" id="menu2" onClick="mCli1(this, 'award.php', 2);" onMouseOver="mOvr1(this, 2);" onMouseOut="mOut1(this, 2);">&nbsp;</td>
										</tr>
										<tr>
											<td class="menu3" id="menu3" onClick="mCli1(this, 'contact.php', 3);" onMouseOver="mOvr1(this, 3);" onMouseOut="mOut1(this, 3);">&nbsp;</td>
										</tr>
										<tr>
											<td class="menu4" id="menu4" onMouseOver="mOvr1(this, 4);" onMouseOut="mOut1(this, 4);">&nbsp;</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="padding-top:13px;"><a href="<?=$_CONFIG['link3']?>" target="_blank"><img src="/upload/<?=$_CONFIG['ad_picpath3']?>" border="0" style="width:189px; height:57px"></a></td>
							</tr>
							<tr>
								<td style="padding-top:13px;background:white; text-align:center; " align="center">
								
								<?
								include './include/db_open.php';
								$result = mysql_query("SELECT * FROM AD WHERE Member=0 OR (AD.Member > 0 AND dateExpire > CURRENT_TIMESTAMP) order by dateSubmit DESC, Sort");
								$ad = "<center><table width='215' align='center' cellpadding=0 cellspacing=0>";
								$i=0;
								$num = mysql_num_rows($result);
								while($rs = mysql_fetch_array($result)){
									$i++;
									$pics = "/images/none.png";
									if($rs['Src'] == 1){
										$pics = $rs['Link'];
									}

									if($rs['Src'] == 2){
										$pics = "/upload/{$rs['Icon']}";
									}
									$ad .= "<tr>";
									$ad .= "	<td align='center' style='padding-left:13px; padding-right:13px'><a href='{$rs['Url']}' style='color:#F74521' target='_blank'>{$rs['Caption']}</a></td>";
									$ad .= "</tr>";
									$ad .= "<tr>";
									$ad .= "	<td align='center' style='padding-left:13px; padding-right:13px" . (($i<$num) ? "; padding-bottom:13px":"") . "'><a href='{$rs['Url']}' target='_blank'><img src='$pics' border='0' style='width:189px; height:114px'></a></td>";
									$ad .= "</tr>";
								}
								$ad .= "</table></center>";
								include './include/db_close.php';

								?>
<? if ($_CONFIG['ad'] > 0):?>
<div id="scrollbarDemo" style="height:500px; text-align:center" align="center"><?=$ad?></div>

<SCRIPT type="text/javascript">
var scrollBarControl = new scrollBar();

	function MoveTo(d){
	　scrollBarControl.clear();
	　scrollBarControl.addBar("scrollbarDemo", 215, 500, <?=$_CONFIG['ad']?>, d);
	　scrollBarControl.createScrollBars();
	}

MoveTo("up");
</SCRIPT>
<?else:?>
<div id="scrollbarDemo" style="text-align:center" align="center"><?=$ad?></div>

<?endif;?>

								</td>
							</tr>
							<tr>
								<td style="padding:13px">
								<div class="fb-like-box" data-href="http://www.facebook.com/pages/%E9%9D%92%E5%89%B5%E5%AD%B8%E9%99%A2/40800648 9218583#!/jidatech" data-width="189" data-height="90" data-show-faces="true" data-stream="false" data-header="false" style="border:solid 2px #AAAAAA"></div>
								<div style="height:15px"></div>
								<div class="fb-like-box" data-href="http://www.facebook.com/profile.php?id=100004483538678#!/pages/%E8%A1%8C%E5%8B%95%E 5%95%86%E5%BA%97%E6%8E%A8%E5%BB%A3%E8%81%AF%E7%9B%9F/352800161464181" data-width="189" data-height="90" data-show-faces="false" data-stream="false" data-header="true" style="border:solid 2px #AAAAAA"></div>
								<div style="height:15px"></div><div class="fb-like-box" data-href="http://www.facebook.com/pages/Intimego%E5%8D%B3%E8%B3%BC%E5%95%86%E5%93%81%E8%B3%87%E8%A8%8A%E7%B6%B2/166098723547596" data-width="189" data-height="90" data-show-faces="false" data-stream="false" data-header="true" style="border:solid 2px #AAAAAA"></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table width="960" style="background:#98cd01; width:960px;" cellpadding="0" cellspacing="0">
	<tr>
		<td style="font-size:11pt; text-align:center; background:#D6CF00; height:60px; padding:5px; color:red">
		吉達資訊科技股份有限公司 版權所有&copy 2012 All Rights Reserved.
		<div style="text-align:center; padding-top:8px">本站建議使用IE8.0以上版本瀏覽器</div>
		</td>
	</tr>
	<tr style="display:none">
		<td>
			<img src="./images/bar1_001.png" width="1" height="1" border="0">
			<img src="./images/bar1_002.png" width="1" height="1" border="0">
			<img src="./images/tab_selected_red.png" width="1" height="1" border="0">
		</td>
	</tr>
</table>
</center>

<script type="text/javascript">
$(function() {
	$("a.prompt").colorbox({ opacity: 0.5, width:800, maxHeight:560});	// prompt
	$("a.prompt1").colorbox({ opacity: 0.5, innerWidth:700, innerHeight:400, iframe:true});	// prompt
});
</script>
<script language="javascript">
	function Dialog(url){
		$.fn.colorbox({opacity: 0.5, width:800, maxHeight:560, href:url});
	}
	function Dialog1(url, h){
		$.fn.colorbox({opacity: 0.5, width:800, height:h, href:url, iframe:true});
	}
	function Dialog2(url){
		$.fn.colorbox({opacity: 0.5, width:800, maxHeight:560, href:url, onClosed:function(){iContent.location.reload();}});
	}
	function dialogClose(){
		$.fn.colorbox.close();
	}
</script>
<script type="text/javascript">  
<?if($welcome){?>
    $(document).ready(function() {  
        $.fn.colorbox({href:"welcome.php", opacity: 0.5, open:true, width:800, height:540});  
    });  
<?}?></script>
<script language="javascript">setUserInfo();</script>
<div style="display:none">
<img src="./images/btn_location.gif">
<img src="./images/btn_location_over.gif">
<img src="./images/btn_sort.gif">
<img src="./images/btn_sort_over.gif">
<img src="./images/menu1.gif">
<img src="./images/menu1_over.gif">
<img src="./images/menu1_selected.gif">
<img src="./images/menu2.gif">
<img src="./images/menu2_over.gif">
<img src="./images/menu2_selected.gif">
<img src="./images/menu3.gif">
<img src="./images/menu3_over.gif">
<img src="./images/menu3_selected.gif">
<img src="./images/menu4.gif">
<img src="./images/menu4_over.gif">
<img src="./images/menu4_selected.gif">
</div>
<script language="javascript">
	function Move(){
		$.post(
			'move.php',
			{
				action: 'moving'
			},
			function(data)
			{
				setTimeout("Move()", 60000);
			}
		);		
	}
</script>

<script language="javascript">setTimeout("Move()", 60000);</script>