<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("�z�|���n�J!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
else if($_SESSION['member']['Seller'] != 2){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("�ӽЦ�����a��; �ݰ��n�X�ʧ@; �M��~�i���`�ϥ�[�ڬO��a]�\��!");
	JavaScript::Redirect("./member_form.php");
	exit;
}
$name=$_POST['name'];

@unlink(getcwd() . "/upload/thumb_".$name);
@unlink(getcwd() . "/upload/".$name);
/*


include '../include/db_open.php';
$sql = "DELETE FROM Photos WHERE Path = '$name'";
mysql_query($sql) or die (mysql_error());
include '../include/db_close.php';
*/
?>