
<table width="100%" style="height:95px; width:100%" cellpadding="0" cellspacing="0" border=0>
	<tr>
		<td style="text-align:left; width:328px"><a href="./"><img src="./upload/<?=$_CONFIG['logo']?>" border="0"></a></td>
		<td style="width:400px; text-align:right" align="right">
		<?php if($_CONFIG['showimg1'] == "Y") {?>
			<a href="<?=(($_CONFIG['link1'] != "") ? $_CONFIG['link1'] : "javascript:void(0)")?>" target="_blank"><img src="/upload/<?=$_CONFIG['ad_picpath1']?>" style="width:360px; height:83px" border="0"></a>
		<?php }?>
		</td>
		<td style="text-align:right;padding:0px"><div id="userinfo">&nbsp;</div></td>
	</tr>
</table>
<script language="javascript">
function setUserInfo(){
	$("#userinfo").load("userinfo.php?<?=time()?>");
}

function showMenu(){
	var p = $("#member_link").position();
	$("#menu").show();
//	$("#member_link").css({"background": "#ffffcc", "border": "solid 0px gray", "border-bottom": "solid 0px #ffffcc"});
	$("#menu").css({"top": p.top-5, "left": p.left-$("#menu").width()+$("#member_link").width()+8 });
}

function hideMenu(){
	$("#menu").hide();
//	$("#member_link").css({"background": "transparent", "border": "solid 1px transparent", "border-bottom": "solid 1px transparent"});
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
</script>