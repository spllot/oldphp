<?php
ini_set("session.save_path", $_SERVER['DOCUMENT_ROOT'] . "/tmp/"); 
//$expireTime = 60*60*24;
//session_set_cookie_params($expireTime);
session_start();
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<LINK href="../css/menu_admin.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/menu_admin.js"></script>
<body>
<center>
	<table class="section" cellpadding="0" cellspacing="0">
		<tr>
			<th class="header">
                <table cellpadding="0" cellspacing="0">
                <tr>
                <td><img src="../images/pointer.gif"></td>
                <th class="header" valign="bottom"> &nbsp;登入資訊</th>
                </tr>
               </table>
			</th>
		</tr>
		<tr>
			<td class="content">
				<table class="menu" cellpadding="0" cellspacing="0">
					<tr>
						<td class="logininfo">
                        <?
                            echo "[ " . $_SESSION['admin'] . " ]<br>";
                            echo $_SESSION['adminname'];
                        ?><br>
                        <a href="logout.php"><font size=-1 color="red">登出</font></a>
                         |
                        <a href="#" onClick="parent.content.location.href='chgpass.php';"><font size=-1 color="blue">變更密碼</font></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br>
	<table class="section" cellpadding="0" cellspacing="0">
		<tr>
			<th class="header">
                <table cellpadding="0" cellspacing="0">
                <tr>
                <td><img src="../images/pointer.gif"></td>
                <th class="header" valign="bottom"> &nbsp;功能選單</th>
                </tr>
               </table>
			</th>
		</tr>
        <tr>
			<td class="content">
    <?
    $module = trim($HTTP_GET_VARS["module"]);
    require_once '../class/system.php';
    echo $_MODULE->toSubMenu($module, $_SESSION['permit'], $_SESSION['admin']);
    ?>
            <!--
                <script language="javascript">
                    setMenu();
                    showMenu('<?=$module?>');
                </script>
            -->
			</td>
		</tr>

	</table>
	<br>
</center>
</body>