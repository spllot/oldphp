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


$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_PRO' AND Parent=0 ORDER BY Sort");
$catalog_list = "";
while($rs=mysql_fetch_array($result)){
	$catalog_list .= "<option value='" . $rs['No'] . "'" . (($_REQUEST['catalog'] == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
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

	function getType(){
		$.post(
			'get_type.php',
			{
				tab: selected,
				type: $("#type").val()
			},
			function(data)
			{
				$("#type").html('<option value="">所有類型</option>' + data);
			}
		);	
	}
	function setTab(n){
		if(n != selected){
			if(selected){
				document.getElementById("tab"+selected).style.backgroundImage = "url('./images/tab" + selected + ".gif')";
			}
			selected = n;
			document.getElementById("tab"+n).style.backgroundImage = "url('./images/tab" + n + "_selected.gif')";
		}
		if(n == 4 || n == 1){
			$("#location").show();
		}
		else{
			$("#location").hide();
		}
		getType();
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
		/*if(n==4)
			iContent.style.height=6180+"px";
		if(n==5)
			iContent.style.height=1000+"px";*/
		$("#type").val("");
		getType();
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
<div id="left_menu_1" style="position:absolute;top:18px;display:none;"><img style="position:absolute;z-index:1px;" src='./images/search_help.gif' width=152 height=304/><table border=0 style="position:absolute;z-index:2px;width:128px;"><tr><td height=258>&nbsp;</td></tr><tr><td height=28 align=right><span onclick="hidesearchtip();" style="cursor:pointer;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td></tr></table></div>
<div id="left_menu_2" style="position:absolute;top:340px;display:none;"><img style="position:absolute;z-index:1px;" src='./images/user_info.gif' width=131 height=431/><table cellpadding=0 cellspacing=0 border=0 style="position:absolute;z-index:2px;width:131px;"><tr><td height=50 >&nbsp;</td></tr><tr><td height=320 id="userinfodiv" style="padding-top:10px;padding-right:14px;padding-left:18px;padding-bottom:0px;">&nbsp;</td></tr><tr><td height=41 align=right valign=top><span onclick="hideuserinfo();" style="height:41px;cursor:pointer;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td></tr></table></div>
<div id="right_menu_1" style="position:fixed;top:125px;display:none;"><img style="position:absolute;z-index:1px;" src='./images/quick_icon.gif' width=130 height=433/><table cellpadding=0 cellspacing=0 border=0 style="position:absolute;z-index:2px;width:128px;"><tr><td height=50 >&nbsp;</td></tr><tr><td height=330 id="quickicondiv">&nbsp;</td></tr><tr><td height=38><div onclick="quickicontop();" style="cursor:pointer;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td></tr></table></div>
<a name="top"></a>
<center><!--top_tab-->
<table width="960" height="29" style="background:#ccccff; background-image:url('./images/bg_top.png'); width:960px height:29px;" cellpadding="0" cellspacing="0">
	<tr>
    	<td style="background:url('images/top_02.jpg'); background-repeat:no-repeat; width:516px; height:29px; background-position:left center;">
			<div id="userinfo" style="width:516px; height:29px; line-height:29px; line-height:29px; overflow:hidden; border:solid 0px gray; color:#c12436; font-family:新細明體; font-size:14px; text-align:center"></div>
		</td>
    	<!--td style="background:url('images/btn_logout.gif'); background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>
    	<td style="background:url('images/btn_member.gif'); background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td-->
    	<td class="menu1" id="menu1" onClick="mCli1(this, 'new.php', 1);" onMouseOver="mOvr1(this, 1);" onMouseOut="mOut1(this, 1);"style="background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>
    	<td class="menu2" id="menu2" onClick="mCli1(this, 'award.php', 2);" onMouseOver="mOvr1(this, 2);" onMouseOut="mOut1(this, 2);"style="background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>	
    	<td class="menu3" id="menu3" onClick="mCli1(this, 'contact.php', 3);" onMouseOver="mOvr1(this, 3);" onMouseOut="mOut1(this, 3);"style="background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>
    	<td class="menu4" id="menu4" onClick="mCli1(this, 'profit.php', 4);" onMouseOver="mOvr1(this, 4);" onMouseOut="mOut1(this, 4);"style="background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>
    </tr>
</table>
</center>

<center><!--logo-->
<table width="960" height="114" style="background:<?=$bg_main?>;background-image:url('images/bg_content1.png'); background-repeat:no-repeat; background-position:center bottom; width:960px; height:114px;" cellpadding="0" cellspacing="0">
	<tr>
    	<td style="width:368px; height:96px; text-align:center"><a href="./"><img src="<?=$src_logo?>" style="width:368px; height:96px" border=0/></a></td>
    	<td style="width:631px; height:96px; text-align:center"><?=$img_banner?></td>
    </tr>
	<tr>
    	<td style="height:18px; font-size:8px">&nbsp;</td>
    </tr>
</table>
</center>
<center><!--search tab-->
<table width="960" style="background:<?=$bg_main?>; width:960px; height:116px" cellpadding="0" cellspacing="0">
	<tr>
    	<td style="height:59px; background:url('images/bg_search.png'); background-repeat:no-repeat; background-position:center top;">
            <table cellpadding="0" cellspacing="0" border="0" style="width:960px; height:52px">
                <tr>
					<td style="height:52px; width:12px"></td>
                    <td id="tab4" class="tab" style="height:52px; width:108px; cursor:pointer; background:url('./images/tab4.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(4, this, 'product4.php');" onMouseOver="mOvr(4, this)" onMouseOut="mOut(4, this);" title="“本地”之交易服務包含[買家到店]/[遞送到府]/[商店外送]/[定點面交],…">&nbsp;</td>
                    <td id="tab5" class="tab" style="height:52px; width:108px; cursor:pointer; background:url('./images/tab5.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(5, this, 'product5.php');" onMouseOver="mOvr(5, this)" onMouseOut="mOut(5, this);">&nbsp;</td>
                    <td id="tab1" class="tab" style="height:52px; width:108px; cursor:pointer; background:url('./images/tab1.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(1, this, 'product1.php');" onMouseOver="mOvr(1, this)" onMouseOut="mOut(1, this);" title="“本地”之交易服務包含[買家到店]/[遞送到府]/[商店外送]/[定點面交],…">&nbsp;</td>
                    <td id="tab2" class="tab" style="height:52px; width:108px; cursor:pointer; background:url('./images/tab2.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(2, this, 'product2.php');" onMouseOver="mOvr(2, this)" onMouseOut="mOut(2, this);">&nbsp;</td>
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
			<form name="sForm" action="" STYLE="margin: 0px; padding: 0px;" target="iContent">
			<input type="hidden" name="tab" value="">
			<input type="hidden" name="pageno" value="">
        	<table cellpadding="0" cellspacing="0" border="0" id="search">
            	<tr>
                	<td style="width:20px">&nbsp;</td>
                	<td style="width:61px; height:45px"><img src="images/top_34.jpg" /></td>
                	<td style="width:; height:45px; text-align:left">
						<select style="width:132px" id="type" name="type" onChange="getCat1();">
							<option value="">所有類型</option>
						</select>
					</td>
                	<td style="width:62px; height:45px"><img src="images/top_36.jpg" /></td>
                	<td style="width:142px; height:45px; text-align:left">
						<select style="width:132px" id="catalog" name="catalog" onChange="getCat2();">
							<option value="">所有分類</option><?=$catalog_list?>
						</select>
					</td>
                	<td style="width:142px; height:45px; text-align:left">
						<select style="width:132px" id="catalog2" name="catalog2" onChange="getCat3();" disabled>
							<option value="">所有分類</option>
						</select>
					</td>
                	<td style="width:150px; height:45px; text-align:left">
						<select style="width:132px" id="catalog3" name="catalog3" disabled>
							<option value="">所有分類</option>
						</select>
					</td>
                	<td style="width:135px; height:45px; text-align:left">
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td id="sort" onMouseOver="btnOver(this)" onMouseOut="btnOut(this)" onClick="Search();" style="background:url(./images/btn_sort.gif); background-repeat:no-repeat; background-position:center center; width:85px; height:27px; cursor:pointer">&nbsp;</td>
							</tr>
						</table>
					</td>
                	<td style="width:115px; height:45px; text-align:left">
						<table cellpadding="0" cellspacing="0" border="0" width="85" height="27">
							<tr>
								<td id="location" onClick="window.iContent.location.href='member_location.php?tab=<?=tab?>';" onMouseOver="btnOver(this)" onMouseOut="btnOut(this)" style="{$hide}; background:url(./images/btn_location.gif); background-repeat:no-repeat; background-position:center center; width:85px; height:27px; cursor:pointer; line-height:27px" title="請在此輸入買家的搜尋位置">&nbsp;</td>
							</tr>
						</table>
					</td>
                </tr>
            </table>
			</form>
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

<center><!--product-->
<table width="960" style="background:<?=$bg_main?>; width:960px;" cellpadding="0" cellspacing="0">
	<tr>
    	<td style="background:url('images/bg_content2.png'); background-repeat:repeat-y; background-position:center center; vertical-align:top" valign="top">
        	<table cellpadding="0" cellspacing="0" border="0" style="width:960px" align="center">
            	<tr>
                	<td style="padding-left:30px; vertical-align:top">
                    <!-- InstanceBeginEditable name="content" -->
					<iframe style="border:1px solid red;width:100%; height:6180px;border:0px none" name="iContent"  scrolling="no" frameborder="0" marginwidth="0" marginheight="0"></iframe>





                    <!-- InstanceEndEditable -->
                    </td>
                	<td style="width:250px;vertical-align:top; text-align:left">
                    	<div style="width:222px; height:292px; background:url('images/bg_news.png'); background-repeat:no-repeat; background-position:center center"><div style="height:43px"></div>
							<?php
							include './include/db_open.php';
							$result = mysql_query("SELECT * FROM News ORDER BY Date DESC LIMIT 4") or die(mysql_error());
							$num = mysql_num_rows($result);
							$i=0;
							while($rs = mysql_fetch_array($result)){
								$i++;
								echo "<div style='margin-left:9px; width:197px; height:28px; line-height:28px; overflow:hidden; font-size:14px; border-bottom: dashed " . (($i < $num) ? "1" : "0") . "px gray'>&nbsp;<img src='./images/icon_news.png'>&nbsp;<a href=\"javascript:Dialog1('news.php?no={$rs['No']}', 500);\" title='{$rs['Subject']}' style='text-decoration:none; color:black'>{$rs['Subject']}</a></div>";
							}
							include './include/db_close.php';
							for($j=$i; $j<4; $j++){
								echo "<div style='margin-left:9px; width:197px;'>&nbsp;</div>";
							}
							?>
							<div style='margin-left:9px; width:120px; margin-top:5px'>
							<a href="<?=$_CONFIG['link3']?>" target="_blank"><img src="<?=(($_CONFIG['imgurl3'] != "") ? $_CONFIG['imgurl3'] : "/upload/" . $_CONFIG['ad_picpath3'])?>" border="0" style="width:202px; height:57px"></a>
							</div>
							<div style='margin-left:9px; width:120px; margin-top:5px'>
							<a href="<?=$_CONFIG['link8']?>" target="_blank"><img src="<?=(($_CONFIG['imgurl8'] != "") ? $_CONFIG['imgurl8'] : "/upload/" . $_CONFIG['ad_picpath8'])?>" border="0" style="width:202px; height:57px"></a>
							</div>

                        </div>
                        
                        <div style="width:222px; margin-top:5px">
                        	<table cellpadding="0" cellspacing="0" border="0" style="width:222px">
                            	<tr>
                                	<td style="width:222px; height:43px;"><img src='images/ad_header.png' style="width:222px; height:43px;"/></td>
                                </tr>
                            	<tr>
                                	<td style="width:222px; background:url('images/bg_ad.png'); background-repeat: repeat-y; background-position: center center; padding-right:3px">
                                    <?
								include './include/db_open.php';
								$result = mysql_query("SELECT * FROM AD WHERE Member=0 OR (AD.Member > 0 AND dateExpire > CURRENT_TIMESTAMP) order by dateSubmit DESC, Sort");
								$ad = "<center><table width='190' align='center' cellpadding=0 cellspacing=0>";
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
									$ad .= "	<td align='center' style='padding-top:12px;" . (($i<$num) ? "; padding-bottom:3px":"") . "'><a href='{$rs['Url']}' target='_blank' style='text-decoration:none'><img src='$pics' border='0' style='width:189px; height:114px; border:solid 0px gray;' border=0></a></td>";
									$ad .= "</tr>";
									$ad .= "<tr>";
									$ad .= "	<td align='left' style='padding-bottom:12px; border-bottom: dashed " . (($i<$num) ? "1" : "0") . "px gray'><a href='{$rs['Url']}' style='color:#858585; text-decoration:none' target='_blank'>{$rs['Caption']}</a></td>";
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
                        	<div class="fb-like-box" data-href="http://www.facebook.com/pages/%E9%9D%92%E5%89%B5%E5%AD%B8%E9%99%A2/40800648 9218583#!/jidatech" data-width="204" data-height="90" data-show-faces="true" data-stream="false" data-header="false" style="background:url('images/bg_fblike.png'); background-repeat:no-repeat; background-position:center center; width:204px; height:90px; overflow:hidden"></div>
							<div style="height:7px"></div>
							<div class="fb-like-box" data-href="http://www.facebook.com/profile.php?id=100004483538678#!/pages/%E8%A1%8C%E5%8B%95%E 5%95%86%E5%BA%97%E6%8E%A8%E5%BB%A3%E8%81%AF%E7%9B%9F/352800161464181" data-width="204" data-height="90" data-show-faces="false" data-stream="false" data-header="true" style="background:url('images/bg_fblike.png'); background-repeat:no-repeat; background-position:center center; width:204px; height:90px; overflow:hidden"></div>
							<div style="height:7px"></div>
                            <div class="fb-like-box" data-href="http://www.facebook.com/pages/Intimego%E5%8D%B3%E8%B3%BC%E5%95%86%E5%93%81%E8%B3%87%E8%A8%8A%E7%B6%B2/166098723547596" data-width="204" data-height="90" data-show-faces="false" data-stream="false" data-header="true" style="background:url('images/bg_fblike.png'); background-repeat:no-repeat; background-position:center center; width:204px; height:90px; overflow:hidden"></div>
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
        InTimeGo客服信箱 service@intimego.com <br />
		吉達資訊科技股份有限公司 版權所有&copy;2012 All Rights Reserved<br />
		本站最佳瀏覽解析度為1280x768 , 並建議使用IE8.0或更新版本之瀏覽器
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
<script language="javascript">
function showSearch(){
	$("#search").show();
}

function hideSearch(){
	$("#search").hide();
}


function setUserInfo(){
	$("#userinfo").load("userinfo.php?<?=time()?>");
}

function showMenu(){
	var p = $("#member_link").position();
	$("#menu").show();
	$("#menu").css({"top": p.top-7, "left": p.left-$("#menu").width()+$("#member_link").width()+8+6 });
}

function hideMenu(){
	$("#menu").hide();
}

function Login(){
	iContent.location.href = "member_login.php";
	if(selected){
		document.getElementById("tab"+selected).style.backgroundImage = "url('./images/tab" + selected + ".gif')";
		selected = '';
	}
	if(select1){
		select1.className=select1.className.replace("_selected", "");
		select1 = null;
	}
}


function Logout(){
	iContent.location.href = "member_logout.php";
	if(selected){
		document.getElementById("tab"+selected).style.backgroundImage = "url('./images/tab" + selected + ".gif')";
		selected = '';
	}
	if(select1){
		select1.className=select1.className.replace("_selected", "");
		select1 = null;
	}
}

function Register(){
	iContent.location.href = "member_register.php";
	if(selected){
		document.getElementById("tab"+selected).style.backgroundImage = "url('./images/tab" + selected + ".gif')";
		selected = '';
	}
	if(select1){
		select1.className=select1.className.replace("_selected", "");
		select1 = null;
	}
}

function Search(){
	if(selected != ""){
		var sForm = document.sForm;
		sForm.action = "product" + selected + ".php";
		sForm.pageno.value = 1;
		sForm.submit();
	}
}
function setPage(xNo){
	var sForm = document.sForm;
	sForm.action = "product" + selected + ".php";
	sForm.pageno.value = xNo;
	sForm.submit();

}
function btnOver(x){
	x.style.backgroundImage="url(./images/btn_" + x.id + "_over.gif)";
}
function btnOut(x){
	x.style.backgroundImage="url(./images/btn_" + x.id + ".gif)";
}

function getCat1(){
	$("#catalog").html('<option value="">所有分類</option>');
	$("#catalog2").html('<option value="">所有分類</option>');
	$("#catalog3").html('<option value="">所有分類</option>');
	document.sForm.catalog2.disabled = true;
	document.sForm.catalog3.disabled = true;
	$.post(
		'get_catalog0.php',
		{
			type: $("#type").val()
		},
		function(data)
		{
			$("#catalog").html('<option value="">所有分類</option>' + data);
		}
	);	
}
function getCat2(){
	$("#catalog2").html('<option value="">所有分類</option>');
	$("#catalog3").html('<option value="">所有分類</option>');
	document.sForm.catalog2.disabled = true;
	document.sForm.catalog3.disabled = true;
	$.post(
		'get_catalog2.php',
		{
			no: $("#catalog").val()
		},
		function(data)
		{
			$("#catalog2").html('<option value="">所有分類</option>' + data);
			if(document.sForm.catalog2.options.length > 1){
				document.sForm.catalog2.disabled = false;
			}
		}
	);	
}
function getCat3(){
	$("#catalog3").html('<option value="">所有分類</option>');
	document.sForm.catalog3.disabled = true;
	$.post(
		'get_catalog2.php',
		{
			no: $("#catalog2").val()
		},
		function(data)
		{
			$("#catalog3").html('<option value="">所有分類</option>' + data);
			
			if(document.sForm.catalog3.options.length > 1){
				document.sForm.catalog3.disabled = false;
			}
		}
	);	
}
function hidesearchtip()
{
	$("#left_menu_1").hide();
	$("#left_menu_2")[0].style.top = "125px";
}
function hideuserinfo()
{
	$("#left_menu_2").hide();
}
function quickicontop()
{
	$('html, body').scrollTop(0);
}
function searchCate(v)
{
	var arr = new Array();
	arr[0] = "all";
	arr[1] = "activity";
	arr[2] = "transfer";
	arr[3] = "hr";
	arr[4] = "event";
	$("#type").val(v);
	getCat1();
	Search();
}
function setquickicontop()
{
	/*if($(document).scrollTop()==0)
		$("#right_menu_1")[0].style.top = 125 + "px";
	else
		$("#right_menu_1")[0].style.top = 0 + $(document).scrollTop()+"px";*/
}
setInterval("setquickicontop()", 1);
var total_width = $( window ).width();
var left_bound = (total_width - 960)/2;
$("#left_menu_1")[0].style.left = (left_bound-152+10)+"px";
$("#left_menu_2")[0].style.left = (left_bound-131-11)+"px";
var html = "";
html += "<table border=0 width='100%' height='330' cellpadding=0 cellspacing=0 >";
html += "<tr><td valign=middle><a class='userinfolink' href='http://dev.intimego.com/training_12.html' target='_blank'>InTimeGo在生活上提供哪些便利。[必讀]</a></td></tr>";
html += "<tr><td valign=middle><a class='userinfolink' href='http://dev.intimego.com/training_08.html' target='_blank'>如何申請成為會員。</a></td></tr>";
html += "<tr><td valign=middle><a class='userinfolink' href='http://dev.intimego.com/training_06.html' target='_blank'>我的商品&服務如何提案。</a></td></tr>";
html += "<tr><td valign=middle><a class='userinfolink' href='http://dev.intimego.com/training_07.html' target='_blank'>我的行動服務如何設置。</a></td></tr>";
html += "<tr><td valign=middle><a class='userinfolink' href='http://dev.intimego.com/training_05.html' target='_blank'>商品&服務行銷功能如何設置。</a></td></tr>";
html += "<tr><td valign=middle><a class='userinfolink' href='http://dev.intimego.com/training_11.html' target='_blank'>行動APP之功能介紹。</a></td></tr>";
html += "</table>";
$("#userinfodiv").html(html);
$("#right_menu_1")[0].style.left = (left_bound+960+11)+"px";
html = "";
html += "<table border=0 width='100%' height='100%' cellspacing=5>";
html += "<tr><td><div style='height:48px;cursor:pointer;font-size:20px;' onclick=\"searchCate('all');\" title='商品販售服務區' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td></tr>";
html += "<tr><td><div style='height:48px;cursor:pointer;font-size:20px;' onclick=\"searchCate('activity');\" title='粉絲推廣服務區' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td></tr>";
html += "<tr><td><div style='height:48px;cursor:pointer;font-size:20px;' onclick=\"searchCate('transfer');\" title='即時運輸服務區' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td></tr>";
html += "<tr><td><div style='height:48px;cursor:pointer;font-size:20px;' onclick=\"searchCate('hr');\" title='即時人力服務區' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td></tr>";
html += "<tr><td><div style='height:48px;cursor:pointer;font-size:20px;' onclick=\"searchCate('event');\" title='即時活動服務區' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td></tr>";
html += "<tr><td><div style='height:48px;font-size:24px;' title=''>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td></tr>";
html += "</table>";
$("#quickicondiv").html(html);

var curr_url = top.location.href;
if(curr_url)
{
	var last_char = curr_url.substring(curr_url.length-1);
	if(last_char=="/")
	{
		$("#left_menu_1").show();
		$("#left_menu_2").show();
		$("#right_menu_1").show();
	}else{
		$("#left_menu_1").hide();
		$("#left_menu_2").hide();
		$("#right_menu_1").hide();
	}
}

</script>

<script language="javascript">setUserInfo(); //getType();</script>
