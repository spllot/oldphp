<?php
$current = basename($_SERVER["SCRIPT_NAME"]);
$deliver = (($_REQUEST['deliver']=="") ? "0":$_REQUEST['deliver']);
$c1=(($deliver=="0") ? "t_selected":"t");
$c2=(($deliver=="1") ? "t_selected":"t");
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}

$showimg = (($_CONFIG['showimg7'] == "Y") ? (($_CONFIG['imgurl7'] != "") ? "<center><img src='{$_CONFIG['imgurl7']}' style='width:700px; margin-bottom:10px'></center>" : "<center><img src='./upload/{$_CONFIG['ad_picpath7']}' style='width:700px; margin-bottom:10px'></center>") : "");
$tab = <<<EOD
<style>
.t{
	border:solid 1px gray;
	cursor:pointer;
	width:354px;
}

.t_selected{
	border-top:solid 1px gray;
	border-left:solid 1px gray;
	border-right:solid 1px gray;
	width:354px;
}

.t_space{
	border-bottom:solid 1px gray;
	width:10px;
}
</style>
{$showimg}
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="t_space">&nbsp;</td>
		<td class="$c1" onClick="window.location.href='seller_transfer.php?deliver=0'" align='center'>本地商品</td>
		<td class="t_space">&nbsp;</td>
		<td class="$c2" onClick="window.location.href='seller_transfer.php?deliver=1'" align='center'>宅配商品</td>
		<td class="t_space">&nbsp;</td>
	</tr>
</table>
EOD;

?>