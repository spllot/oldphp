<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->group][1])){exit("權限不足!!");}
?>
<html style="width:250px; height=400px">
    <head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>管理人員清單</title>
    </head>
    <body leftmargin="0" topmargin="0" style="overflow:hidden">
        <table width="100%" height="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" valign="center"><iframe name="iAction" width="100%" height="100%" src="group_user_list.php"></iframe></td>
            </tr>
            <tr>
                <td height="25"><a href="#" onClick="checkAll(true);"><font size="-1" color="blue">全選</font></a> / <a href="#" onClick="checkAll(false);"><font size="-1" color="red">取消全選</font></a></td>
            </tr>
            <tr>
                <td height="30">
                    <table width="100%">
                        <tr>
                            <td width="50%" align="center"><input type="button" name="btnCancel" value="取消" onClick="Cancel();"></td>
                            <td width="50%" align="center"><input type="button" name="btnStart" value="確定" onClick="Save();"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>

<script language="javascript">
	function User(xID, xName){
		this.ID = xID;
		this.Name = xName;
	}//user

    function checkAll(x){
        xObject = eval("iAction.lForm.user");
        if (xObject && xObject.length){
            for (var i=0; i<xObject.length; i++){
                xObject[i].checked = x;
            }//for
        }//if
    }//checkAll

    function Cancel(){
        window.returnValue = "-1";
        window.close();
    }//Cancel

    function Save(){
        var Users = new Array();
        xObject = eval("iAction.lForm.user");
        if (xObject && xObject.length){
            for (var i=0; i<xObject.length; i++){
                if (xObject[i].value && xObject[i].checked){
					var xUser = xObject[i].value.explode(",");
                    Users[Users.length] = new User(xUser[0], xUser[1]);
                }//if
            }//for
        }//if
        window.returnValue = Users; 
        window.close();
    }//Save
</script>