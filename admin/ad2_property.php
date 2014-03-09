<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
include './ad2_usefor.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->ad2][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["mno"], 0);
$pageno = Tools::parseInt2( $_REQUEST['pageno'], 1);
$usefor = $_REQUEST["usefor"];
$catalog = $_REQUEST["catalog"];
$page->setHeading($_MODULE->nameOf($_MODULE->ad2));
foreach($usefors as $value => $text){
	$ad2 .= "<option value='$value'>$text</option>";
}
include("../include/db_open.php");
$sort = 0;
$result = mysql_query("SELECT MAX(Sort) FROM AD2 WHERE useFor = '$usefor' AND catalog = '$catalog'");
if (($num = mysql_num_rows($result)) == 1){
   $record = mysql_fetch_row($result) or die (mysql_error());
//   $sort = $record[0] + 1;
}//if
if ($no > 0){
    $result=mysql_query("SELECT Caption, Url, Icon, Catalog, useFor, Src, Link, Sort, Discount, Member, AD2.Days, dateExpire, dateSubmit, Member.Name, Member.userID, Cost FROM AD2 LEFT OUTER JOIN Member ON AD2.Member = Member.No WHERE AD2.No = $no");
    if(($num=mysql_num_rows($result))==1){
        list($caption, $url, $icon, $catalog, $usefor, $src, $link, $sort, $discount, $member, $days, $dateexpire, $datesubmit, $name, $userid, $cost) = mysql_fetch_row($result);
		$src1= (($src==1) ? " CHECKEd":"");
		$src2= (($src==2) ? " CHECKEd":"");
    }//if
}//if
$pics = "/images/ad_none.png";
$ad_picpath = (($icon != "") ? basename($icon):"/images/ad_none.png");
if($src == 1){
	$pics = (($link != "") ? $link : "/images/ad_none.png");
}
if($src == 2){
	$pics = (($icon != "") ? "/upload/".basename($icon) : "/images/ad_none.png");
}
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_PRO' AND Parent=0");
$catalog_list = "";
while($rs=mysql_fetch_array($result)){
	if($catalog == ""){$catalog = $rs['No'];}
	$catalog_list .= "<option value='" . $rs['No'] . "'" . (($catalog == $rs["No"] ) ? " SELECTED" : "") . ">" . $rs["Name"] . "</option>";
}

foreach($usefors as $value => $text){
	if($usefor == ""){$usefor = $value;}
	$use_list .= "<option value=\"$value\"" . (($value == $usefor) ? " SELECTED" : "") . ">$text</option>";
}

include("../include/db_close.php");
$page->addJSFile("/js/jquery.js");
$page->addJSFile("/js/ajaxupload.js");

if($member > 0){
	$buyer = <<<EOD
                    <tr>
                        <td class="html_label_generated">申購會員：</td><td>{$name}, {$userid}</td>
					</tr>
                    <tr>
                        <td class="html_label_generated">天　　數：</td><td>{$days}天</td>
					</tr>
                    <tr>
                        <td class="html_label_generated">申購日期：</td><td>{$datesubmit}</td>
					</tr>
                    <tr>
                        <td class="html_label_generated">截止日期：</td><td>{$dateexpire}</td>
					</tr>
                    <tr>
                        <td class="html_label_generated">儲&nbsp;&nbsp;值&nbsp;&nbsp;金：</td><td>{$cost}</td>
					</tr>

EOD;

}
$WEB_CONTENT = <<<EOD
	$init
    <table cellpadding="0" cellspacing="0" border="0">
        <form name="iForm" action="ad2_save.php" method="post">
        <input type="hidden" name="no" value="$no">
		<input type="hidden" name="pageno" value="$pageno">
        <tr>
            <td>
                <table>
					<tr>
						<td class="html_label_required">類別：</td>
						<Td>
							<select name="usefor">
								<option value="">請選擇</option>{$use_list}
							</select>
						</td>
					</tr>
					<tr>
						<td class="html_label_required">分類：</td>
						<Td>
							<select name="catalog">
								<option value="">請選擇</option>{$catalog_list}
							</select>
						</td>
					</tr>
                    <tr>
                        <td class="html_label_required">廣告名稱：</td>
                        <td align="left"><input type="text" name="caption" style="width:600px" value="$caption"></td>
                    </tr>
                    <tr>
                        <td class="html_label_required">折數：</td>
                        <td align="left"><input type="text" name="discount" style="width:50px" value="$discount">折</td>
                    </tr>
                    <tr>
                        <td class="html_label_required">連結網址：</td>
                        <td align="left"><input type="text" name="url" style="width:600px" value="$url"></td>
                    </tr>
                    <tr>
                        <td class="html_label_required">圖片來源：</td>
                        <td align="left">
							<table>
								<tr>
									<td><input type="radio" name="src" value="1"{$src1} onClick="setSrc();">連結</td>
									<td><input type="text" name="link" style="width:540px" value="$link" onChange="setSrc();"></td>
								</tr>
								<tr>
									<td valign="top"><input type="radio" name="src" value="2"{$src2} onClick="setSrc();">上傳</td>
									<td>
										<div style="float:left"><input type="image" id="upload" src="../images/icon_upld.png" /></div>
										<div style="float:left">(139x77)</div>
										<div style="font-size:10pt; display:none" id="loading">
											<table cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td><img src="../images/loader_light_blue.gif"></td>
													<td style='font-size:10pt'>&nbsp;上傳中，請待候…</td>
												</tr>
											</table>
										</div><br>
										<input type="hidden" name="ad_picpath" id="ad_picpath" value="{$ad_picpath}" />
									</td>
								</tr>
								<tr>
									<td colspan="2" style="padding-left:24px"><div id="cbox"><img src="{$pics}" width="139" height=77 title="圖片"></div></td>
								</tR>
							</table>



						</td>
                    </tr>
					{$buyer}
                    <tr style="display:none">
                      <td class="html_label_required">排列順序：</td>
                        <td align="left"><input type="text" name="sort" value="$sort" style="width:50px;"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td><hr>
                <table width="100%">
                    <tr>
                        <td align="center" width="50%"><input type="button" value="確定" onClick="Save();"></td>
                        <td align="center" width="50%"><input type="reset" value="取消" onclick="Cancel();"></td>
                    </tr>
                </table>
            </td>
        </tr>
        </form>
    </table>
EOD;

$page->addContent($WEB_CONTENT);


$page->show();
?>
<script language="javascript">
	function setSrc(){
		if(iForm.src[0].checked){
			if(iForm.link.value){
				$('#cbox').html("<img src='"+iForm.link.value+"' width='139' height='77'>");
			}
			else{
				$('#cbox').html("<img src='/images/ad_none.png' width='139' height='77'>");
			}
		}
		if(iForm.src[1].checked){
			if(iForm.ad_picpath.value){
				$('#cbox').html("<img src='/upload/"+iForm.ad_picpath.value+"' width='139' height='77'>");
			}
			else{
				$('#cbox').html("<img src='/images/ad_none.png' width='139' height='77'>");
			}
		}
	}
    function Cancel(){
		iForm.action="ad2.php";
        iForm.submit();
    }//Save
    function Save(){
        if (!iForm.caption.value){
            alert("請輸入廣告名稱!");
            iForm.caption.focus();
        }
		else if(!iForm.url.value){
			alert("請輸入連結網址!");
			iForm.url.focus();
		}
		else if(!iForm.ad_picpath.value){
			alert("請上傳圖片!");
		}
        else{
            iForm.submit();
        }
    }//Save
$(function() {
	new AjaxUpload('#upload', {
		action: 'uploadfile.php',
		onSubmit : function(file , ext){
			if (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext)){
				var d = new Date();
				var curr_hour = d.getHours();
				var curr_min = d.getMinutes();
				var curr_sec = d.getSeconds();
				document.getElementById("loading").style.display="block";
				this.setData({
					'dir':  	"../upload/",
					"fname": 	'ad2_BANNER_'+d.getTime()+"."+ext,
					'ext':  	ext
				});
			} else {					
				alert('上傳錯誤訊息: 只允許上傳 image 圖檔 (jpg,png,jpeg,gif)');
				return false;				
			}		
		},
		onComplete : function(file, response){
			$('#ad_picpath').val(response);
			$('#cbox').html("<img src='/upload/"+response+"' width='139' height='77'>");
			document.getElementById("loading").style.display="none";
		}
	});
});
</script>

