<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->benefit][1])){exit("權限不足!!");}
$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->benefit));
$type = $_REQUEST['type'];
$area = $_REQUEST['area_list'];
$catalog = $_REQUEST['catalog_list'];
$p = $_REQUEST['p'];
$content = '<table width="100%" cellspacing="10" cellpadding="5">';
$ad_picpath2 = "http://{$WEB_HOST}/images/none.png";
if($p != ""){
	include '../include/db_open.php';


	$result = mysql_query("SELECT * FROM Config");
	while($rs = mysql_fetch_array($result)){
		$_CONFIG[$rs['ID']] = $rs['YN'];
	}
	$ad_picpath2 = "http://{$WEB_HOST}/upload/" . $_CONFIG['ad_picpath2'];
	if($_CONFIG['imgurl2'] != ""){
		$ad_picpath2 = $_CONFIG['imgurl2'];
	}


	if($area != ""){
		$sql = "SELECT * FROM Catalog WHERE No IN ($area)";
		$result = mysql_query($sql);
		while($rs=mysql_fetch_array($result)){
			$aa .= $rs['Name'] . ",";
		}
		if(strlen($aa) > 0){$aa = substr($aa, 0, strlen($aa) - 1);}
	}
	$sql = "SELECT ifnull( count(*) , 0 ) AS counts FROM Member WHERE Subscribe = 1";
	$sql .= (($area != "") ? " AND subscribeArea IN ($area)" : "");
	
	$result = mysql_query($sql) or die (mysql_error());
	list($counts1) = mysql_fetch_row($result);



	$product = explode(",", $p);
	for($i=0; $i<sizeof($product); $i++){
		$result = mysql_query("SELECT *, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City FROM Product where No ='" . $product[$i] . "'");
		if($data =mysql_fetch_array($result)){
			$price = "$" . ($data['Price']);
			$sell = "$" . ($data['Price1']);
			$save = "$" . ($data['Price'] - $data['Price1']);
			$save = "$" . (($save > 0) ? number_format($save) : " --");
			$sell = (($data['Price1'] > 0) ? "$" . number_format($data['Price1']) : "免費");
			
			$price_info = "<font style=\"font-family:Arial; font-size:16pt; font-weight:bold; color:red\">{$sell}</font>&nbsp;<font style=\"font-fmaily:Arial: font-size:10pt; color:#CCCCCC; text-decoration:line-through\">{$price}</font>";

			if($data['Transport'] == 1){
				$discount = (($data['taxi_discount']) ? "{$data['taxi_discount']}折":"");
				$price_info = "<font style=\"font-family:Arial; font-size:16pt; font-weight:bold; color:red\">{$discount}</font>";
			}
			else{
				if($data['price_mode'] == 1){
					$discount = "折扣 --";
					$price_info = "<font style=\"font-family:Arial; font-size:16pt; font-weight:bold; color:red\">{$data['price_info']}</font>";
				}
				else{
					$discount = (float)(number_format(($data['Price1'] / $data['Price'])*10,1));
					if($discount <= 0){
						$discount = "免費商品";
					}
					else if($discount >= 10){
						$discount = "折扣 --";
					}
					else{
						$d = explode(".", strval($discount));
						$discount = "". $d[0] . "";
						if(sizeof($d) > 1){
							$discount .= ".". $d[1] . "";
						}
						$discount = $discount . "折";
					}
				}
			}

			
			$counts = 0;
			$used = (($data['Used'] == 1) ? "(中古品)" : "");
			if($data['Mode'] == 1){
				if($data['Deliver'] == 0){
					$type=1;
				}
				if($data['Deliver'] == 1){
					$type=2;
				}
			}
			if($data['Mode'] == 2){
				if($data['Deliver'] == 0){
					$type=4;
				}
				if($data['Deliver'] == 1){
					$type=5;
				}
			}
			$url = "http://{$WEB_HOST}/product{$type}_detail.php?no={$data['No']}";
			if($i%3 == 0){$content .= "<tr>";}
			$description = mb_substr($data['Description'], 0, 35, 'utf8') . ((mb_strlen($data['Description'], 'utf8') > 35) ? "…" : "");
			$name = mb_substr($data['Name'], 0, 12, 'utf8') . ((mb_strlen($data['Name'], 'utf8') > 12) ? "…" : "") ;
			$content .= <<<EOD
			<td style="background:#FFFFFF; vertical-align:top" align="center">
				<table cellpadding="0" cellspacing="0" align="center" border=0 width="248">
					<tr>
						<td align="center" style="padding-top:5px"><a href="$url"><img src="http://{$WEB_HOST}/upload/{$data['Photo']}" style="width:220px; height:138px" style="border:solid 1px gray" width="220" height="138"></a></td>
					</tr>
					<tr>
						<td colspan="2" style="color:red; font-size:14px; line-height:20px; padding-left:10px; padding-right:10px;text-align:left" align="left"><div style="height:20px; overflow:hidden; text-align:left">【{$name}】{$discount}</div></td>
					</tr>
					<tr>
						<td colspan="2" style="font-size:12px; line-height:20px; padding-left:10px; padding-right:10px;text-align:left" align="left"><div style="height:40px; overflow:hidden; text-align:left">{$description}</div></td>
					</tr>
					<tr>
						<td style="padding-top:10px; padding-bottom:10px; padding-left:10px; padding-right:10px">
							<table width="100%">
								<tr>
									<td style="text-align:left" align="left">{$price_info}</td>
									<td style="text-align:center; width:100px;background:url('http://{$WEB_HOST}/images/btn_detail.jpg');color:white; height:25px; background-repeat:no-repeat"><a href="$url" style="color:white; text-decoration:underline">購買詳情</a></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
EOD;
			if($i%3 == 2){$content .= "</tr>";}
		}
	}
	include '../include/db_close.php';

}
$content .= "</table>";

$date = date('Y-m-d');
$local = (($_REQUEST['type'] == 1 || $_REQUEST['type'] == 3) ? $aa : "全國宅配");
$linke2 =(($_CONFIG['link2'] != "") ? $_CONFIG['link2'] : "javascript:void(0)");
$content = <<<EOD
<center>
<table style="border:solid 1px gray; width:920px; background:#98cd01" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="left">
			<table style="width:100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="329"><a href="http://{$WEB_HOST}" target="_blank"><img src="http://{$WEB_HOST}/upload/{$_CONFIG['logo']}" alt="InTimeGo即購網" border="0" style="width:329px; height:96px"></a></td>
					<td align="center"><a href="$link2" target="_blank"><img src="$ad_picpath2" style="width:590px; height:96px" border="0" width="590" height="96"></a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="text-align:right; background:#525552; color:white; line-height:22px; font-size:12px; padding-right:10px">{$date} 好康訊息-{$local}</td>
	</tR>
	<tr>
		<td style="border:solid 10px #525252; border-top:none">
		{$content}		
		</td>
	</tR>
	<tr>
		<td style="font-size:12px; text-align:center; padding:5px; color:white;padding-right:10px">
		吉達資訊科技股份有限公司 版權所有&copy 2012 All Rights Reserved.
		</td>
	</tr>
</table>
</center>


EOD;















$html = <<<EOD
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script language="Javascript1.2"> 
  _editor_url = "../js/";
	var win_ie_ver = parseFloat(navigator.appVersion.split("MSIE")[1]);
	if (navigator.userAgent.indexOf('Mac') >= 0){
		win_ie_ver = 0;
	}//if
	if (navigator.userAgent.indexOf('Windows CE') >= 0){
		win_ie_ver = 0;
	}//if
	if (navigator.userAgent.indexOf('Opera') >= 0){
		win_ie_ver = 0;
	}//if
	if (win_ie_ver >= 5.5) {
		document.write('<scr' + 'ipt src="' + _editor_url + 'editor.js"');
		document.write(' language="Javascript1.2"></scr' + 'ipt>');
	}//if
	else{
		document.write('<scr'+'ipt>function editor_generate() { return false; }</scr'+'ipt>');
	}//else
</script>

	<form name="iForm" method="post" action="benefit_save.php">
	
		<input type="hidden" name="type" value="{$_REQUEST['type']}">
		<input type="hidden" name="catalog_list" value="$catalog">
		<input type="hidden" name="area_list" value="$area">
		<input type="hidden" name="p" value="$p">

<table>
	<tr>
		<td class="html_label_generated">訂閱人數：</td><Td>{$counts1}</td>
	</tr>
	<tr>
		<td class="html_label_required">排程日期：</td><Td>
		<input type="text" name="date" style="width:80px" onClick="WdatePicker();">
		<select name="hour">
			<option value="00">00</option>
			<option value="01">01</option>
			<option value="02">02</option>
			<option value="03">03</option>
			<option value="04">04</option>
			<option value="05">05</option>
			<option value="06">06</option>
			<option value="07">07</option>
			<option value="08">08</option>
			<option value="09">09</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="12">12</option>
			<option value="13">13</option>
			<option value="14">14</option>
			<option value="15">15</option>
			<option value="16">16</option>
			<option value="17">17</option>
			<option value="18">18</option>
			<option value="19">19</option>
			<option value="20">20</option>
			<option value="21">21</option>
			<option value="22">22</option>
			<option value="23">23</option>
		</select>點
		<select name="min">
			<option value="00">00</option>
			<option value="01">01</option>
			<option value="02">02</option>
			<option value="03">03</option>
			<option value="04">04</option>
			<option value="05">05</option>
			<option value="06">06</option>
			<option value="07">07</option>
			<option value="08">08</option>
			<option value="09">09</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="12">12</option>
			<option value="13">13</option>
			<option value="14">14</option>
			<option value="15">15</option>
			<option value="16">16</option>
			<option value="17">17</option>
			<option value="18">18</option>
			<option value="19">19</option>
			<option value="20">20</option>
			<option value="21">21</option>
			<option value="22">22</option>
			<option value="23">23</option>
			<option value="24">24</option>
			<option value="25">25</option>
			<option value="26">26</option>
			<option value="27">27</option>
			<option value="28">28</option>
			<option value="29">29</option>
			<option value="30">30</option>
			<option value="31">31</option>
			<option value="32">32</option>
			<option value="33">33</option>
			<option value="34">34</option>
			<option value="35">35</option>
			<option value="36">36</option>
			<option value="37">37</option>
			<option value="38">38</option>
			<option value="39">39</option>
			<option value="40">40</option>
			<option value="41">41</option>
			<option value="42">42</option>
			<option value="43">43</option>
			<option value="44">44</option>
			<option value="45">45</option>
			<option value="46">46</option>
			<option value="47">47</option>
			<option value="48">48</option>
			<option value="49">49</option>
			<option value="50">50</option>
			<option value="51">51</option>
			<option value="52">52</option>
			<option value="53">53</option>
			<option value="54">54</option>
			<option value="55">55</option>
			<option value="56">56</option>
			<option value="57">57</option>
			<option value="58">58</option>
			<option value="59">59</option>
		</select>分
		</td>
	</tr>
	<tr>
		<td class="html_label_required">標題：</td><Td><input type="text" name="subject" style="width:840px"></td>
	</tr>
	<tr>
		<td class="html_label_required" style="display:none">內容：</td><Td style="display:none"><textarea name="content" style="width:860px; height:300px">$content</textarea></td>
	</tr>
	<tr>
		<td class="html_label_generated" valign="top">預覽內容：</td>
		<td><div style="width:860px; height:400px; overflow:scroll; background:white">$content</div></td>
	</tr>
	<tr>
		<td colspan="2"><hr>
			<table width="100%">
				<tr>
					<td width="50%" align="center"><input type="button" value="上一步" onClick="Cancel();"><td>
					<td width="50%" align="center"><input type="button" value="發送" onClick="Save();"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
	</form>



EOD;
$page->addContent($html);
$page->show();

$y = date('Y-m-d');
$h = date('H');
$m = date('i');

JavaScript::setValue("iForm.date", $y);
JavaScript::setValue("iForm.hour", $h);
JavaScript::setValue("iForm.min", $m);



?>
<script language="javascript">
//editor_generate('content');
</script>
<script language="javascript">
	function Cancel(){
		var iForm = document.iForm;
		iForm.action="benefit_list.php";
		iForm.submit();
	}
	function Save(){
		var iForm = document.iForm;
		if(!iForm.subject.value){
			alert("請輸入標題!");
			iForm.subject.focus();
		}
		else if(!iForm.content.value){
			alert("請輸入內容!");
		}
		else if(confirm("確定要發送?")){
			iForm.submit();
		}
	}

</script>
