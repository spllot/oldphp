<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
include './ad_usefor.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->ad][1])){exit("權限不足!!");}
$page = new Admin();
$no = Tools::parseInt2($_REQUEST["mno"], 0);
$pageno = Tools::parseInt2( $_REQUEST['pageno'], 1);
$usefor = $_REQUEST["usefor"];
$page->setHeading($_MODULE->nameOf($_MODULE->ad));
foreach($usefors as $value => $text){
	$ad .= "<option value='$value'>$text</option>";
}
include("../include/db_open.php");
$sort = 0;
$result = mysql_query("SELECT MAX(Sort) FROM AD WHERE useFor = '$usefor'");
if (($num = mysql_num_rows($result)) == 1){
   $record = mysql_fetch_row($result) or die (mysql_error());
//   $sort = $record[0] + 1;
}//if
if ($no > 0){
    $result=mysql_query("SELECT Caption, Url, Icon, Country, Src, Link, Sort, Member, AD.Days, dateExpire, dateSubmit, Member.Name, Member.userID, Cost FROM AD LEFT OUTER JOIN Member ON AD.Member = Member.No WHERE AD.No = $no");
    if(($num=mysql_num_rows($result))==1){
        list($caption, $url, $icon, $country, $src, $link, $sort, $member, $days, $dateexpire, $datesubmit, $name, $userid, $cost) = mysql_fetch_row($result);
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


include("../include/db_close.php");
$page->addJSFile("/js/jquery.js");
$page->addJSFile("/js/ajaxupload.js");

$WEB_CONTENT = <<<EOD
	$init
    <table cellpadding="0" cellspacing="0" border="0">
        <form name="iForm" action="ad_save.php" method="post">
        <input type="hidden" name="no" value="$no">
		<input type="hidden" name="pageno" value="$pageno">
		<input type="hidden" name="usefor" value="BANNER">
        <tr>
            <td>
                <table>
					<tr style="display:none">
						<td class="html_label_required">地區：</td>
						<Td>
							<input type="radio" name="country" value="1" checked>中國 &nbsp;&nbsp;
							<input type="radio" name="country" value="2">台灣
						</td>
					</tr>
                    <tr>
                        <td class="html_label_required">廣告名稱：</td>
                        <td align="left"><input type="text" name="caption" style="width:600px" value="$caption"></td>
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
										<div style="float:left">(189x114)</div>
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
									<td colspan="2" style="padding-left:24px"><div id="cbox"><img src="{$pics}" width="189" height=114 title="圖片"></div></td>
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
                        <td align="center" width="50%"><input type="reset" value="取消" onclick="history.back();"></td>
                    </tr>
                </table>
            </td>
        </tr>
        </form>
    </table>
EOD;

$page->addContent($WEB_CONTENT);


$page->show();
if($country==2)
	JavaScript::Execute("iForm.country[1].checked= true");
?>
<script language="javascript">
	function setSrc(){
		if(iForm.src[0].checked){
			if(iForm.link.value){
				$('#cbox').html("<img src='"+iForm.link.value+"' width='189' height='114'>");
			}
			else{
				$('#cbox').html("<img src='/images/ad_none.png' width='189' height='114'>");
			}
		}
		if(iForm.src[1].checked){
			if(iForm.ad_picpath.value){
				$('#cbox').html("<img src='/upload/"+iForm.ad_picpath.value+"' width='189' height='114'>");
			}
			else{
				$('#cbox').html("<img src='/images/ad_none.png' width='189' height='114'>");
			}
		}
	}

    function Save(){
        if (!iForm.caption.value){
            alert("請輸入廣告名稱!");
            iForm.caption.focus();
        }
		else if(!iForm.url.value){
			alert("請輸入連結網址!");
			iForm.url.focus();
		}
		else if(!iForm.src[0].checked && !iForm.src[1].checked){
			alert("請選擇圖片來源!");
		}
		else if(iForm.src[0].checked && !iForm.link.value){
			alert("請輸入圖片連結!");
		}
		else if(iForm.src[1].checked && !iForm.ad_picpath.value){
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
					"fname": 	'ad_BANNER_'+d.getTime()+"."+ext,
					'ext':  	ext
				});
			} else {					
				alert('上傳錯誤訊息: 只允許上傳 image 圖檔 (jpg,png,jpeg,gif)');
				return false;				
			}		
		},
		onComplete : function(file, response){
			$('#ad_picpath').val(response);
			$('#cbox').html("<img src='/upload/"+response+"' width='189' height='114'>");
			document.getElementById("loading").style.display="none";
		}
	});
});
</script>

