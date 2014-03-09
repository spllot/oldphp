<?php
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<LINK href="../css/banner_admin.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/banner_admin.js"></script>
<body leftmargin="0" topmargin="0">
<table width="100%" height="99" cellpadding="0" cellspacing="0" border="0" background="../images/_banner_bg.jpg">
<tr>
	<td width="468" rowspan="2" background="../images/banner_logo.jpg" style="text-align:center">
		<table style="height:78px; width:468px" border="0">
			<tr>
				<td style="width:30px">&nbsp;</td>
				<td style="background1:white; text-align1:center; width1:250px;padding1:5px; filter1: Alpha(Opacity=50);">
					
				</td>
				<td style="width:px">&nbsp;</td>
			</tr>
		</table>
	</td>
	<td valign="top" width="59" height="56"><img src="../images/banner_banner.jpg"></td>
	<td align="right" nowrap valign="top">
		<table cellpadding="2" cellspacing="2" border="0" width="100%">
			<tr>
				<td align="left" width="50%"><td>
				<td align="right" width="50%"><img src="../images/banner_admin.jpg">
                    <!--a href="logout.php"><font size=-1 color="red">登出</font></a-->
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr height="43">
	<td colspan="2" nowrap align="right">
    <?
    require_once '../class/system.php';
    echo $_MODULE->toMenu();
    ?>
    </td>
</tR>
</table>
</body>