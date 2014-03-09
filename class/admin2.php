<?php
require_once 'template.php';
class Admin extends Template{
    var $heading = "";

    function setHeading($menu, $selected=0){ 
        $newHeading = '<table class="table_heading" cellpadding="0" cellspacing="0">';
        $newHeading .= '                        <tr>';
		$i=0;
		foreach($menu as $k=>$v){
			$newHeading .= '                            <td' . (($i == $selected) ?  ' class="td_heading"' :  ' class="td_heading_bg"') . ' style="padding:2px"><a href="' . $k . '" style="color:white; text-decoration:none">' . $v . '</a></td>';
			$newHeading .= '							<td style="width:10px">&nbsp;</td>';
			$i++;
        }
		$newHeading .= '                        </tr>';
        $newHeading .= '                    </table>';
		
		
		$this->heading = $newHeading;


    }//setHeading
	
    function toString(){
        return <<<EOD
<HTML>
    <HEAD>
        <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <TITLE></TITLE>
        <LINK href="../css/content_admin.css" rel="stylesheet" type="text/css">
			<script language="javascript" src="../js/common_admin.js"></script>
        $this->jsFile
    </HEAD>
    <BODY>
        <center>
            <TABLE class="table_main" cellpadding="0" cellspacing="0">
                    <TR>
                        <TD class="main_top"></TD>
                    </TR>
                    <TR>
                        <TD class="main_heading">$this->heading
                        </TD>
                    </TR>
                    <TR>
                        <TD class="main_space">
                        </TD>
                    </TR>
                    <TR>
                        <TD class="main_content">
                            <table class="table_content">
                                <tr>
                                    <td class="td_content">
                                        $this->content
                                    </td>
                                </tr>
                            </table>
                        </TD>
                    </TR>
                    <TR>
                        <TD class="main_footer"><!--FOOTER-->
                           
                        </TD>
                    </TR>
            </table>
        </center>
    </BODY>
</HTML>
EOD;
    }//toString()
}//Shop

?>