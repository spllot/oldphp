<?php

$module = trim($HTTP_GET_VARS["module"]);
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<frameset cols="150,*" border="0" framespacing="0">
	<frame src="menu.php?module=<?=$module?>" name="menu" marginwidth="0" marginheight="0" frameborder="NO" scrolling="NO" NORESIZE>
	<frame src="content.php" name="content" frameborder="NO">
</frameset>