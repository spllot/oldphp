<?
ini_set("session.save_path", $_SERVER['DOCUMENT_ROOT'] . "/tmp/"); 
//$expireTime = 60*60*24;
//session_set_cookie_params($expireTime);
//ini_set("session.cache_expire", "$expireTime");
session_start();
?>
<html>
	<head>
	</head>
<body>
<?
echo $_SERVER['DOCUMENT_ROOT'] . "/tmp/" . "<br>";
echo Date('Y-m-d H:i:s') . "<br>";
echo $_SESSION['admin'];
?>
<script>
<!--

var limit="0:60"

if (document.images){
	var parselimit=limit.split(":");
	parselimit=parselimit[0]*60+parselimit[1]*1;
}

function beginrefresh(){
	if (!document.images)
		return
	if (parselimit==1)
		window.location.reload()
	else{ 
		parselimit-=1;
		curmin=Math.floor(parselimit/60);
		cursec=parselimit%60;
		if (curmin!=0)
			curtime=curmin+" minutes and "+cursec+" seconds left until page refresh!";
		else
			curtime=cursec+" seconds left until page refresh!";
//		window.status=curtime
		setTimeout("beginrefresh()",1000);
	}
}
window.onload=beginrefresh;
//-->
</script>
</body>
</html>
