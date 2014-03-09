<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->user][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["mno"], 0);
$pageno = Tools::parseInt2( $_REQUEST['pageno'], 1);
$page->setHeading($_MODULE->nameOf($_MODULE->user));
$page->addcontentFile("../html/user_property.html");
$page->show();
if ($no > 0){
    include("../include/db_open.php");
    $result=mysql_query("SELECT No, userID, userName, EMail, dateLastLogin, ipLastLogin FROM Admin WHERE No = $no");
    if(($num=mysql_num_rows($result))==1){
        list($no, $userid, $username, $email, $datelastlogin, $iplastlogin) = mysql_fetch_row($result);
        JavaScript::setValue("iForm.no", $no);
        JavaScript::setValue("iForm.userid", $userid);
        JavaScript::setValue("iForm.username", $username);
        JavaScript::setValue("iForm.email", $email);
        JavaScript::setValue("iForm.datelastlogin", $datelastlogin);
        JavaScript::setValue("iForm.iplastlogin", $iplastlogin);
    }//if
	$result = mysql_query("SELECT groupNo FROM groupMap WHERE userID = '" . $userid . "'");
	while(list($groupno) = mysql_fetch_row($result)){
		$group .= $groupno . ",";
	}
    include("../include/db_close.php");
}//if
JavaScript::setValue("iForm.pageno", $pageno);
?>
<script language="javascript">
    iAction.location.href = "user_group_list.php?group=<?=$group?>";
</script>