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

$area=$_REQUEST['area'];
$type=$_REQUEST['type'];
$catalog=$_REQUEST['catalog'];
$catalog2=$_REQUEST['catalog2'];
$catalog3=$_REQUEST['catalog3'];
$seller=$_REQUEST['seller'];


switch($type){
	case 'transfer':
		$usefor = "TYPE_TPT";
		break;
	case 'hr':
		$usefor = "TYPE_JOB";
		break;
	case 'event':
		$usefor = "TYPE_ACT";
		break;
	default:
		$usefor = "TYPE_PRO";
		break;
}




$result = mysql_query("SELECT * FROM Catalog WHERE useFor='$usefor' AND Parent=0 ORDER BY Sort");
$catalog_list = "";
while($rs=mysql_fetch_array($result)){
	$catalog_list .= "<option value='" . $rs['No'] . "'" . (($catalog == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}
$disabled2= " disabled";
$disabled3= " disabled";
if($catalog != ""){
	$result = mysql_query("SELECT * FROM Catalog WHERE useFor='$usefor' AND Parent=$catalog ORDER BY Sort");
	$catalog_list2 = "";
	while($rs=mysql_fetch_array($result)){
		$catalog_list2 .= "<option value='" . $rs['No'] . "'" . (($catalog2 == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
	}
	$disabled2= "";
}

if($catalog2 != ""){
	$result = mysql_query("SELECT * FROM Catalog WHERE useFor='$usefor' AND Parent=$catalog2 ORDER BY Sort");
	$catalog_list3 = "";
	while($rs=mysql_fetch_array($result)){
		$catalog_list3 .= "<option value='" . $rs['No'] . "'" . (($catalog3 == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
	}
	$disabled3= "";
}



$tab = 4;
$result = mysql_query("SELECT * FROM Member WHERE No='$seller'") or die(mysql_error());
$member = mysql_fetch_array($result);






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
<title>InTimeGo 即時服務-<?=$member['Nick']?>的商品/服務集合</title>
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

//	function getType(){
//	}
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
		getType(n);
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
		window.location.href = url;
		/*if(n==4)
			iContent.style.height=2826+"px";
		if(n==5)
			iContent.style.height=1000+"px";*/
		$("#type").val("");
		getType(n);
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
$bg_main = "#FDD3F7";


?>





<!-- InstanceEndEditable -->
</head>
<body topmargin="0" leftmargin="0" bottommargin="0" rightmargin="0" margin="0" onload="MM_preloadImages('images/green_bar_up.gif','images/green_bar_down.gif');" style="background:url('images/body9.jpg'); background-position:center center">
<a name="top"></a>
<center>
<table width="960" height="29" style="background:#ccccff; background-image:url('./images/bg_top.png'); width:960px height:29px;" cellpadding="0" cellspacing="0">
	<tr>
    	<td style="background:url('images/top_02.jpg'); background-repeat:no-repeat; width:516px; height:29px; background-position:left center;">
			<div id="userinfo" style="width:849px; height:29px; line-height:29px; line-height:29px; overflow:hidden; border:solid 0px gray; color:#c12436; font-family:新細明體; font-size:14px; text-align:center"></div>
		</td>
    	<!--td style="background:url('images/btn_logout.gif'); background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>
    	<td style="background:url('images/btn_member.gif'); background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td-->
    	<td style="width:111px; height:29px; text-align:right"><a href="./"><img src="./images/btn_home.gif" border=0 onMouseOver="this.src='./images/btn_home_over.gif';" onMouseOut="this.src='./images/btn_home.gif';"></a></td>
    </tr>
</table>
</center>

<center>
<table width="960" height="114" style="background:<?=$bg_main?>;background-image:url('images/bg_content3.png'); background-repeat:no-repeat; background-position:center bottom; width:960px; height:114px;" cellpadding="0" cellspacing="0">
	<tr>
    	<td style="width:368px; height:96px; text-align:center"><a href="./"><img src="<?=$src_logo?>" style="width:368px; height:96px" border=0/></a></td>
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
                    <td id="tab4" class="tab" style="height:52px; width:108px; cursor:pointer; background:url('./images/tab4.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(4, this, 'member_product4.php?seller=<?=$seller?>');" onMouseOver="mOvr(4, this)" onMouseOut="mOut(4, this);" title="“本地”意義: 包含 鄰近面交/鄰近外送/遞送到府，…">&nbsp;</td>
                    <td id="tab5" class="tab" style="height:52px; width:108px; cursor:pointer; background:url('./images/tab5.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(5, this, 'member_product5.php?seller=<?=$seller?>');" onMouseOver="mOvr(5, this)" onMouseOut="mOut(5, this);">&nbsp;</td>
                    <td id="tab1" class="tab" style="height:52px; width:108px; cursor:pointer; background:url('./images/tab1.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(1, this, 'member_product1.php?seller=<?=$seller?>');" onMouseOver="mOvr(1, this)" onMouseOut="mOut(1, this);" title="“本地”意義: 包含 鄰近面交/鄰近外送/遞送到府，…">&nbsp;</td>
                    <td id="tab2" class="tab" style="height:52px; width:108px; cursor:pointer; background:url('./images/tab2.gif'); background-repeat:no-repeat; background-position:center center" onClick="mClk(2, this, 'member_product2.php?seller=<?=$seller?>');" onMouseOver="mOvr(2, this)" onMouseOut="mOut(2, this);">&nbsp;</td>
                    <td style="height:52px; padding-left:26px;">
                        <div id='marquee' style="width:450px; height:30px; line-height:30px; margin-top:5px; overflow:hidden; color:red; text-align:center">
                            商家：【<a href="member_intro.php?seller=<?=$seller?>" style="color:red"><?=$member['Nick']?></a>】的商品/服務集合
                        </div>
                    </td>
                </tr>
            </table>
            <div style="height:7px"></div>
        </td>
    </tr>
	<tr>
    	<td style="height:45px; background:url('images/bg_content2.png'); background-repeat:repeat-y; background-position:center center; text-align:center; vertical-align:middle">
			<form name="sForm" action="member_product4.php" STYLE="margin: 0px; padding: 0px;">
			<input type="hidden" name="seller" value="<?=$seller?>">
			<input type="hidden" name="tab" value="<?=$tab?>">
			<input type="hidden" name="pageno" value="<?=$pageno?>">
        	<table cellpadding="0" cellspacing="0" border="0">
            	<tr>
                	<td style="width:20px">&nbsp;</td>
                	<td style="width:61px; height:45px"><img src="images/top_34.jpg" /></td>
                	<td style="width:; height:45px; text-align:left">
						<select style="width:132px" id="type" name="type" onChange="getCat1('');">
							<option value="">所有類型</option>
						</select>
					</td>
                	<td style="width:62px; height:45px"><img src="images/top_36.jpg" /></td>
                	<td style="width:142px; height:45px; text-align:left">
						<select style="width:132px" id="catalog" name="catalog" onChange="getCat2('');">
							<option value="">所有分類</option><?=$catalog_list?>
						</select>
					</td>
                	<td style="width:142px; height:45px; text-align:left">
						<select style="width:132px" id="catalog2" name="catalog2" onChange="getCat3('');"<?=$disabled2?>>
							<option value="">所有分類</option><?=$catalog_list2?>
						</select>
					</td>
                	<td style="width:150px; height:45px; text-align:left">
						<select style="width:132px" id="catalog3" name="catalog3"<?=$disabled3?>>
							<option value="">所有分類</option><?=$catalog_list3?>
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
								<td id="location" style="background-repeat:no-repeat; background-position:center center; width:85px; height:27px; cursor:pointer; line-height:27px">&nbsp;</td>
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

<center>
<table width="960" style="background:<?=$bg_main?>; width:960px;" cellpadding="0" cellspacing="0">
	<tr>
    	<td style="background:url('images/bg_content2.png'); background-repeat:repeat-y; background-position:center center; vertical-align:top" valign="top">
        	<table cellpadding="0" cellspacing="0" border="0" style="width:960px" align="center">
            	<tr>
                	<td style="padding-left:30px; vertical-align:top; padding-right:10px">
                    <!-- InstanceBeginEditable name="content" -->
					
<?php

include './_member_product4.php';

?>

<table cellpadding="0" cellspacing="0" border="0" width="670">
	<tr>
		<td><?=$WEB_CONTENT?></td>
	</tr>
	<tr>
		<td style="height:224px;"><Br><?include 'ad.php';?></td>
	</tr>
</table>


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
function setUserInfo(){
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
		sForm.pageno.value = 1;
		sForm.submit();
	}
}
function setPage(xNo){
	var sForm = document.sForm;
	sForm.pageno.value = xNo;
	sForm.submit();

}
function btnOver(x){
	x.style.backgroundImage="url(./images/btn_" + x.id + "_over.gif)";
}
function btnOut(x){
	x.style.backgroundImage="url(./images/btn_" + x.id + ".gif)";
}


function getType(x){
	$.post(
		'get_type.php',
		{
			tab: x,
			type: "<?=$_REQUEST['type']?>"
		},
		function(data)
		{
			$("#type").html('<option value="">所有類型</option>' + data);
		}
	);	
}

function getCat1(x){
	$("#catalog").html('<option value="">所有分類</option>');
	$("#catalog2").html('<option value="">所有分類</option>');
	$("#catalog3").html('<option value="">所有分類</option>');
	document.sForm.catalog2.disabled = true;
	document.sForm.catalog3.disabled = true;
	$.post(
		'get_catalog0.php',
		{
			type: $("#type").val(),
			x: x
		},
		function(data)
		{
			$("#catalog").html('<option value="">所有分類</option>' + data);
		}
	);	
}
function getCat2(x){
	$("#catalog2").html('<option value="">所有分類</option>');
	$("#catalog3").html('<option value="">所有分類</option>');
	document.sForm.catalog2.disabled = true;
	document.sForm.catalog3.disabled = true;
	$.post(
		'get_catalog2.php',
		{
			no: $("#catalog").val(),
			x: x
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
function getCat3(x){
	$("#catalog3").html('<option value="">所有分類</option>');
	document.sForm.catalog3.disabled = true;
	$.post(
		'get_catalog2.php',
		{
			no: $("#catalog2").val(),
			x: x
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
</script>

<script language="javascript">
setTab(<?=$tab?>);
//getType(<?=$tab?>);
//getCat1('<?=$catalog?>');
//getCat2('<?=$catalog2?>');
//getCat3('<?=$catalog3?>');
</script>

<script language="javascript">$("#userinfo").load("userinfo3.php");</script>