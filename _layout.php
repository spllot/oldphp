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
$current = basename($_SERVER['PHP_SELF']);

$src_logo = (($_CONFIG['imgurl0'] != "") ? $_CONFIG['imgurl0'] : "/upload/" . $_CONFIG['logo']);
$img_banner = (($_CONFIG['showimg1'] == "Y") ? "<img src=\"" . (($_CONFIG['imgurl1'] != "") ? $_CONFIG['imgurl1']:"/upload/" . $_CONFIG['ad_picpath1']) . "\" style=\"width:590px; height:96px\" border=0/>" : "");

if($_CONFIG['link1'] != ""){
	$img_banner = "<a href='{$_CONFIG['link1']}' target='_blank'>{$img_banner}</a>";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:fb="http://www.facebook.com/2008/fbml"><!-- InstanceBegin template="/Templates/layout.dwt" codeOutsideHTMLIsLocked="true" --> 

	<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="keywords" content="二手書,團購,團購網,廉售,便宜,二手貨,即購網,台灣"/>
<meta name="description" content="<?=$fb_desc?>"/>
<meta property="og:title" content="<?=$fb_title?>" />
<meta property="og:description" content="<?=$fb_desc?>" />
<meta property="og:image" content="<?=$fb_thumb?>" />
<title>InTimeGo 即購網 <?=$fb_title?></title>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-37407627-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<link rel="image_src" type="image/jpeg" href="<?=$fb_thumb?>">
<link type="text/css" href="js/themes/base/ui.all.css" rel="stylesheet" />
<link type="text/css" href="style.css?<?=time()?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="js/jquery.colorbox.css" media="screen" />
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.blockUI.js"></script>
<script type="text/javascript" src="js/zip.js?20121126"></script>
<script type="text/javascript" src="js/jquery.colorbox.js"></script>
<script language="javascript" src="js/facebook_show.js"></script>
<script src="js/oslide.js" language="javascript" type="text/javascript"></script>
<script src="js/easing.js" language="javascript" type="text/javascript"></script>
<script language="javascript" src="js/scrollbar.js"></script>

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
<script language="javascript">
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
<!-- InstanceBeginEditable name="header" -->
<?php
$bg_main = "#98cd01";

?>





<!-- InstanceEndEditable -->
</head>
<body topmargin="0" leftmargin="0" bottommargin="0" rightmargin="0" margin="0" onload="MM_preloadImages('images/green_bar_up.gif','images/green_bar_down.gif');" style="background:url('images/body9.jpg'); background-position:center center">
<center>
<table width="960" height="29" style="background:#ccccff; width:960px height:29px;" cellpadding="0" cellspacing="0">
	<tr>
    	<td style="background:url('images/top_02.jpg'); background-repeat:no-repeat; width:294px; height:29px; background-position:center center;"><div style="width:294px; height:29px; overflow:hidden; line-height:29px">&nbsp;</div></td>
    	<td style="background:url('images/top_03.jpg'); background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>
    	<td style="background:url('images/top_04.jpg'); background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>
    	<td style="background:url('images/top_05.jpg'); background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>
    	<td style="background:url('images/top_06.jpg'); background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>	
    	<td style="background:url('images/top_07.jpg'); background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>
    	<td style="background:url('images/top_08.jpg'); background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>
    </tr>
</table>
</center>

<center>
<table width="960" height="114" style="background:<?=$bg_main?>;background-image:url('images/bg_content1.png'); background-repeat:no-repeat; background-position:center bottom; width:960px; height:114px;" cellpadding="0" cellspacing="0">
	<tr>
    	<td style="width:329px; height:96px; text-align:center"><a href="./"><img src="<?=$src_logo?>" style="width:329px; height:96px" border=0/></a></td>
    	<td style="width:631px; height:96px; text-align:center"><?=$img_banner?></td>
    </tr>
	<tr>
    	<td style="height:18px; font-size:8px">&nbsp;</td>
    </tr>
</table>
</center>
<center>
<table width="960" style="background:<?=$bg_main?>; width:960px; height:116px" cellpadding="0" cellspacing="0">
	<tr>
    	<td style="height:59px; background:url('images/bg_search.png'); background-repeat:no-repeat; background-position:center top;">
            <table cellpadding="0" cellspacing="0" border="0" style="width:960px; height:52px">
                <tr>
					<td style="height:52px; width:12px"></td>
                    <td id="tab1" class="tab" style="height:52px; width:108px; cursor:pointer; background:url('./images/tab1.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(1, this, 'product1.php');" onMouseOver="mOvr(1, this)" onMouseOut="mOut(1, this);">&nbsp;</td>
                    <td id="tab2" class="tab" style="height:52px; width:108px; cursor:pointer; background:url('./images/tab2.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(2, this, 'product2.php');" onMouseOver="mOvr(2, this)" onMouseOut="mOut(2, this);">&nbsp;</td>
                    <td id="tab4" class="tab" style="height:52px; width:108px; cursor:pointer; background:url('./images/tab4.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(4, this, 'product4.php');" onMouseOver="mOvr(4, this)" onMouseOut="mOut(4, this);">&nbsp;</td>
                    <td id="tab5" class="tab" style="height:52px; width:108px; cursor:pointer; background:url('./images/tab5.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(5, this, 'product5.php');" onMouseOver="mOvr(5, this)" onMouseOut="mOut(5, this);">&nbsp;</td>
                    <td style="height:52px; padding-left:26px;">
                        <div id='marquee' style="width:450px; height:30px; line-height:30px; margin-top:5px; overflow:hidden">
                            <?
                            include 'include/db_open.php';
                            $result = mysql_query("SELECT * FROM Page WHERE useFor = 'MARQUEE'");
                            $i = 0;
                            if($rs = mysql_fetch_array($result)){
                                $i++;
                                echo $rs['Content'];
                            }
                            include 'include/db_close.php';
                            ?>
                        </div>
                    </td>
                </tr>
            </table>
            <div style="height:7px"></div>
        </td>
    </tr>
	<tr>
    	<td style="height:45px; background:url('images/bg_content2.png'); background-repeat:repeat-y; background-position:center center; text-align:center; vertical-align:middle">
        	<table cellpadding="0" cellspacing="0" border="0">
            	<tr>
                	<td style="width:20px">&nbsp;</td>
                	<td style="width:61px; height:45px"><img src="images/top_34.jpg" /></td>
                	<td style="width:; height:45px; text-align:left"><select style="width:132px"></select></td>
                	<td style="width:62px; height:45px"><img src="images/top_36.jpg" /></td>
                	<td style="width:142px; height:45px; text-align:left"><select style="width:132px"></select></td>
                	<td style="width:142px; height:45px; text-align:left"><select style="width:132px"></select></td>
                	<td style="width:150px; height:45px; text-align:left"><select style="width:132px"></select></td>
                	<td style="width:135px; height:45px; text-align:left"><img src="images/btn_sort.gif" /></td>
                	<td style="width:115px; height:45px; text-align:left"><img src="images/btn_location.gif" /></td>
                </tr>
            </table>
        </td>
    </tr>
	<tr>
    	<td style="height:12px; background:url('images/bg_content2.png'); background-repeat:repeat-y; background-position:center center;">
        	<div style="height:12px; background:url('images/line_search.png'); background-repeat:no-repeat; background-position: center top" />
        	</div>
        </td>
    </tr>
</table>
</center>

<center>
<table width="960" style="background:<?=$bg_main?>; width:960px;" cellpadding="0" cellspacing="0">
	<tr>
    	<td style="background:url('images/bg_content2.png'); background-repeat:repeat-y; background-position:center center; vertical-align:top" valign="top">
        	<table cellpadding="0" cellspacing="0" border="0" style="width:960px" align="center">
            	<tr>
                	<td style="padding-left:30px; vertical-align:top">
                    <!-- InstanceBeginEditable name="content" -->
					<iframe style="width:100%; height:2826px;border:0px none" name="iContent"  scrolling="no" frameborder="0" marginwidth="0" marginheight="0"></iframe>





                    <!-- InstanceEndEditable -->
                    </td>
                	<td style="width:250px;vertical-align:top; text-align:left">
                    	<div style="width:222px; height:292px; background:url('images/bg_news.png'); background-repeat:no-repeat; background-position:center center">
                        </div>
                        
                        <div style="width:222px; margin-top:5px">
                        	<table cellpadding="0" cellspacing="0" border="0" style="width:222px">
                            	<tr>
                                	<td style="width:222px; height:43px;"><img src='images/ad_header.png' style="width:222px; height:43px;"/></td>
                                </tr>
                            	<tr>
                                	<td style="width:222px; background:url('images/bg_ad.png'); background-repeat: repeat-y; background-position: center center">&nbsp;
                                    
                                    
                                    
                                    </td>
                                </tr>
                                	<td style="width:222px; height:16px;"><img src='images/ad_footer.png' style="width:222px; height:16px;"/></td>
                            </table>
                        </div>
                        
                        <div style="width:222px; margin-top:5px; margin-bottom:10px">
                        	<table cellpadding="0" cellspacing="0" border="0" style="width:222px">
                            	<tr>
                                	<td style="width:222px; height:43px; background:url('images/bg_facebook.png'); background-repeat:no-repeat; background-position:center top"></td>
                                </tr>
                            	<tr>
                                	<td style="width:222px; background:url('images/bg_ad.png'); background-repeat: repeat-y; background-position: center center; padding-left:8px; padding-right:10px">
							<div style="height:7px"></div>
                        	<div class="fb-like-box" data-href="http://www.facebook.com/pages/%E9%9D%92%E5%89%B5%E5%AD%B8%E9%99%A2/40800648 9218583#!/jidatech" data-width="204" data-height="90" data-show-faces="true" data-stream="false" data-header="false" style="background:url('images/bg_fblike.png'); background-repeat:no-repeat; background-position:center center"></div>
							<div style="height:7px"></div>
							<div class="fb-like-box" data-href="http://www.facebook.com/profile.php?id=100004483538678#!/pages/%E8%A1%8C%E5%8B%95%E 5%95%86%E5%BA%97%E6%8E%A8%E5%BB%A3%E8%81%AF%E7%9B%9F/352800161464181" data-width="204" data-height="90" data-show-faces="false" data-stream="false" data-header="true" style="background:url('images/bg_fblike.png'); background-repeat:no-repeat; background-position:center center"></div>
							<div style="height:7px"></div>
                            <div class="fb-like-box" data-href="http://www.facebook.com/pages/Intimego%E5%8D%B3%E8%B3%BC%E5%95%86%E5%93%81%E8%B3%87%E8%A8%8A%E7%B6%B2/166098723547596" data-width="204" data-height="90" data-show-faces="false" data-stream="false" data-header="true" style="background:url('images/bg_fblike.png'); background-repeat:no-repeat; background-position:center center"></div>
                                    </td>
                                </tr>
                                	<td style="width:222px; height:16px;"><img src='images/ad_footer.png' style="width:222px; height:16px;"/></td>
                            </table>
                        
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</center>
<center>
<table width="960" style="background:<?=$bg_main?>; width:960px;" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td style="font-size:12px; line-height:20px; text-align:center; background:#D6CF00; background-image:url('images/bg_footer.png'); height:83px; color:#993300; backgrround-repeat:no-repeat; background-position:center center; vertical-align:middle;">
        <div style="height:12px"></div>
        即購網客服信箱 service@intimego.com <br />
		吉達資訊科技股份有限公司 版權所有&copy;2012 All Rights Reserved<br />
		本站建議使用IE8.0或更新版本之瀏覽器
		</td>
	</tr>
</table>
</center>
<div style="display:none">
<img src="images/btn_location.gif">
<img src="images/btn_location_over.gif">
<img src="images/btn_sort.gif">
<img src="images/btn_sort_over.gif">
<img src="images/menu1.gif">
<img src="images/menu1_over.gif">
<img src="images/menu1_selected.gif">
<img src="images/menu2.gif">
<img src="images/menu2_over.gif">
<img src="images/menu2_selected.gif">
<img src="images/menu3.gif">
<img src="images/menu3_over.gif">
<img src="images/menu3_selected.gif">
<img src="images/menu4.gif">
<img src="images/menu4_over.gif">
<img src="images/menu4_selected.gif">
<img src="images/bar1_001.png" width="1" height="1" border="0">
<img src="images/bar1_002.png" width="1" height="1" border="0">
<img src="images/tab_selected_red.png" width="1" height="1" border="0">
</div>
</body>
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
<?php
//$welcome = 1;
if($welcome){
	echo <<<EOD
    $(document).ready(function() {  
        $.fn.colorbox({href:"welcome.php", opacity: 0.5, open:true, width:670, height:460});  
    });  
EOD;
}
?>
</script>
<script language="javascript">setUserInfo();</script>
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
<script language="javascript">setTimeout("Move()", 60000);</script><!-- InstanceEnd -->