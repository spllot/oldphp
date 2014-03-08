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

</script><?

$url = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];
$current = substr(basename($_SERVER['PHP_SELF']), 0, 8);
switch($current){
	case 'product1':
		$tab = 1;
		break;
	case 'product2':
		$tab = 2;
		break;
	case 'product4':
		$tab = 4;
		break;
	case 'product5':
		$tab = 5;
		break;
}




$url = "index.php?tab=$tab&url=".urlencode($url);;
?>
<script language="javascript">
if(!parent.iContent){
	window.location.href="<?=$url?>";
}
</script>
<link rel="image_src" type="image/jpeg" href="<?=$fb_thumb?>">
<link type="text/css" href="js/themes/base/ui.all.css" rel="stylesheet" />
<link type="text/css" href="style.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="js/jquery.colorbox.css" media="screen" />
<script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAAwnI081IB-1YQtn3DiExB7hQAaOLpgxbI1qAjGGRql3xj-qYYohQaZIKM_4qi4nCHaMvKs5cHNkkAWA"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jjquery.blockUI.js"></script>
<script type="text/javascript" src="js/zip.js?20121126"></script>
<script type="text/javascript" src="js/jquery.colorbox.js"></script>
<script language="javascript" src="./js/facebook_show.js"></script>
<script src="./js/oslide.js" language="javascript" type="text/javascript"></script>
<script src="./js/easing.js" language="javascript" type="text/javascript"></script>
<script language="javascript" src="./js/scrollbar.js"></script>
<script language="javascript" src="./js/scrollbar2.js"></script>
<style type="text/css">
body,div,ul,ol,li,h1,h2,h3,h4,h5,h6{margin:0;padding:0;}
img{border:none;}
a,a:link,a:visited{color:blue;text-decoration:none;cursor:pointer;}
a:hover{color:#008b00;text-decoration:underline;}
.hd h3{font-size:14px;color:#444444;padding-left:10px;color:#fff;line-height:30px;}

.loading{background:#f9fdff url('./images/T16WJqXaXeXXXXXXXX-32-32.gif') no-repeat 50% 50%;}
.j_Slide{margin:0px 0px;border:5px #99CCFF solid;width:396px;height:248px;overflow:hidden;position:relative;-webkit-border-radius:4px;}
.j_Slide .tb-slide-list{position:absolute;width:99999px;}
.j_Slide .tb-slide-list li{height:300px;}
.j_Slide .tb-slide-list li img{width:470px;height:300px;}


	.lbtn{position:absolute;z-index:3;bottom:3px;right:5px;}
	.lbtn li{float:left;line-height:21px;width:24px;padding:0 2px;}
	.lbtn li a,.lbtn li a:link,.lbtn li a:active,.lbtn li a:visited{display:block;text-align:center;background:url('./images/lbtnA_bg.gif') no-repeat left 50%;line-height:21px;font-family:"Arial Unicode MS";}
	.lbtn li.hover a,.lbtn li.hover a:link,.lbtn li.hover a:active,.lbtn li.hover a:visited	{color:#fff;font-weight:bold;font-family:Verdana;text-decoration:none;background:url('./images/lbtnA_bg_hover.gif') no-repeat left top;}
	
.j_Slide1{width:432px;height:152px;overflow:hidden;margin:30px 50px;}
.j_Slide1 .tb-slide-list{position:absolute;width:99999px;}
.j_Slide1 .bd{overflow:hidden;}
.j_Slide1 .tb-slide-list li img{width:200px;height:100px;padding:8px;overflow:hidden;}
.j_Slide1 .hd{height:30px;position:relative;background:#666;-webkit-border-top-left-radius:4px;-webkit-border-top-right-radius:4px;}
.j_Slide1 .bd{position:relative;border:1px #000 solid;height:116px;}
.j_Slide1 .hd .handel{position:absolute;right:5px;top:6px;}
.j_Slide1 .hd .handel li{float:left;}
.j_Slide1 .hd .handel li a,.j_Slide1 .hd .handel li a:link,.j_Slide1 .hd .handel li a:active,.j_Slide1 .hd .handel li a:visited{text-decoration:none;padding:2px 4px;}
.j_Slide1 .hd .handel li.hover a,.j_Slide1 .hd .handel li.hover a:link,.j_Slide1 .hd .handel li.hover a:active,.j_Slide1 .hd .handel li.hover a:visited{color:#fff;}
.direct-left{width:99999px;}
.direct-left li{float:left;}
.direct-right{}
.direct-right li{float:right;}


</style>
<div id="fb-root"></div>
<?
$current = basename($_SERVER['PHP_SELF']);

$ip = ((getenv(HTTP_X_FORWARDED_FOR)) ?  getenv(HTTP_X_FORWARDED_FOR) :  getenv(REMOTE_ADDR));
?>
<body topmargin="0" leftmargin="0" bottommargin="0" rightmargin="0" margin="0" style="background:#FFFFFF">
<table cellpadding="0" cellspacing="0" border="0" width="670">
	<tr>
		<td style="color:white;" bgcolor="#525552"><?=$search_bar?></td>
	</tr>
	<tr>
		<td><?=$WEB_CONTENT?></td>
	</tr>
</table>
<iframe name="iAction" width="100%" height="100" style="display<?=(($ip == "114.33.118.9") ? "1" : "")?>:none"></iframe>
<script language="javascript">
window.parent.document.body.scrollTop = 0;
window.parent.document.documentElement.scrollTop = 0;
</script>
<script language="javascript">
window.parent.document.title = "InTimeGo 即時服務";
</script>
<script language="javascript">
parent.hideSearch();
</script>