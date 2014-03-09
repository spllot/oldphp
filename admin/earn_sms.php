<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->earn_sms][1])){exit("權限不足!!");}
$pagesize = 10;
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$tab = (($_REQUEST['tab'] != "") ? $_REQUEST['tab'] : 5);
$year = (($_REQUEST['year'] != "") ? $_REQUEST['year'] : date('Y'));
$month = (($_REQUEST['month'] != "") ? $_REQUEST['month'] : date('n'));

$date = date('Y-m-01');


$menu = array();

for($i=0; $i<6; $i++){
	$tmp = date("Y-m", strtotime($date . "-" . (5-$i) . " month"));
	$menu["earn_sms.php?tab=" . $i] = $tmp;
	if($tab==$i){
		$curr = $tmp;
	}
}



$amount = 0;
$result = mysql_query("SELECT * FROM Config");
while($rs=mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}


$result = mysql_query("SELECT COUNT(*) FROM logCertify WHERE dateSent LIKE '$curr%'") or die(mysql_error());
list($certify) = mysql_fetch_row($result);
$result = mysql_query("SELECT COUNT(*) AS Counts, SUM(Cost) AS Costs, Cost FROM logCoupon WHERE dateSent LIKE '$curr%' AND Cost > 0 GROUP BY Cost ") or die(mysql_error());

$coupon=0;
$earn = 0;
$memo = "";
$num = mysql_num_rows($result);
$i=0;
while($rs=mysql_fetch_array($result)){
	$i++;
	$coupon += $rs['Counts'];
	$earn += $rs['Costs'];
	$memo .= $rs['Counts'] . "(" . $rs['Cost'] . "點)";
	if($i < $num){
		$memo .= ", ";
	}
}
$earn -= $certify;
$earn -= $coupon;
//$earn = $_CONFIG['coupon'] * $coupon - $certify - $coupon;

$earn_all = 0;
$result = mysql_query("SELECT COUNT(*) FROM logCertify WHERE dateSent < '{$curr}-31 23:59:59'") or die(mysql_error());
list($l) = mysql_fetch_row($result);
$result = mysql_query("SELECT Count(*), SUM(Cost) FROM logCoupon WHERE Cost > 0 AND dateSent < '{$curr}-31 23:59:59'") or die(mysql_error());
list($c, $earn_all) = mysql_fetch_row($result);
$earn_all -= $c;
$earn_all -= $l;


$memo = (($memo=="") ? "0" : $memo);

$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($menu, $tab);

if($tab == 5){
	$list = <<<EOD
                    <tr>
                        <td class="html_label_required" style="width:300px">優惠憑證扣點：</td>
                        <td align="left">
							<input type="text" name="coupon" value="{$_CONFIG['coupon']}" style="width:50px"><br><font style="color:red;">(每發一筆"優惠憑證送出"，扣除商家儲值點數)</font>
						</td>
                    </tr>
                    <tr>
                        <td class="html_label_required" style="width:300px">簡訊點數補充：</td>
                        <td align="left">
							<input type="text" name="sms" style="width:50px">
						</td>
                    </tr>
                    <tr>
                        <td class="html_label_generated" style="width:300px">最新簡訊剩餘點數：</td>
                        <td align="left">{$_CONFIG['sms']}</td>
                    </tr>
EOD;
	$btn = <<<EOD
        <tr>
            <td><hr>
                <table width="100%">
                    <tr>
                        <td align="center" width="100%"><input type="button" value="確定" onClick="Save();"></td>
                    </tr>
                </table>
            </td>
        </tr>
EOD;
}

$WEB_CONTENT = <<<EOD
<center>
     <table cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td>
               <table>{$list}
                    <tr>
                        <td class="html_label_generated" style="width:300px">{$curr}月商品簡訊消耗數(-1/簡訊)：</td>
                        <td align="left" style="width:360px">{$certify}</td>
                    </tr>
                    <tr>
                        <td class="html_label_generated" style="width:300px">{$curr}月優惠憑證簡訊消耗數(-1/簡訊)：</td>
                        <td align="left">{$memo}</td>
                    </tr>
                    <tr>
                        <td class="html_label_generated" style="width:300px">{$curr}月簡訊利潤淨額：</td>
                        <td align="left">{$earn}</td>
                    </tr>
                    <tr>
                        <td class="html_label_generated" style="width:300px">累計簡訊利潤淨額：</td>
                        <td align="left">{$earn_all}</td>
                    </tr>
				</table>
            </td>
        </tr>{$btn}
    </table>
</center>
EOD;

$page->addContent("<TABLE class=\"grid\" CELLPADDING=0 CELLSPACING=0 BORDER=0><form name=\"mForm\" action='earn_sms_save.php' method=\"post\">");
$page->addContent("<input type=\"hidden\" name=\"pageno\" value=\"$pageno\">");
$page->addContent("<input type=\"hidden\" name=\"memberlist\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"itemno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"mno\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"sort\" value=\"\">");
$page->addContent("<input type=\"hidden\" name=\"tab\" value=\"$tab\">");
$page->addContent("<input type=\"hidden\" name=\"curr\" value=\"$curr\">");
$page->addContent("<TR class=\"grid_toolbar\"><TD>");
$page->addContent("<table width=\"100%\">");
$page->addContent("    <tr>");
$page->addContent("        <td width=\"50%\" align=\"left\" nowrap>");
$page->addContent("        </td>");
$page->addContent("        <td width=\"50%\" align=\"right\" nowrap>");
$page->addContent("        </td>");
$page->addContent("    </tr>");
$page->addContent("</table>");
$page->addContent("</TD></TR>");
$page->addContent("<TR><TD>");
$page->addContent($WEB_CONTENT);
$page->addContent("</TD></TR>");
$page->addContent("</TABLE>");
include("../include/db_close.php");
$page->show();
?>
<script language="javascript">

function Save(){
	mForm.submit();
}
</script>