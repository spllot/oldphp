<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->epaper][1])){exit("權限不足!!");}
$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->epaper));

include '../include/db_open.php';

$sql = "SELECT ifnull( count( DISTINCT EMail ) , 0 ) AS counts FROM Subscribe";
$result = mysql_query($sql) or die (mysql_error());
list($counts) = mysql_fetch_row($result);
include '../include/db_close.php';



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

	<form name="iForm" method="post" action="epaper_save.php" enctype="multipart/form-data">
<table>
	<tr style="display:none">
		<td class="html_label_required">發送目標：</td><Td>
			<input type="radio" name="to" value="1" checked onClick="showRecipients();">全部會員
			<input type="radio" name="to" value="2" onClick="showRecipients();">買家會員
			<input type="radio" name="to" value="3" onClick="showRecipients();">賣家會員
			<input type="radio" name="to" value="4" onClick="showRecipients();">自組郵件
		</td>
	</tr>
	<tr id="recipients" style="display:none"><td class="html_label_required">收件人：</td>
		<td><textarea name="recipients" style="width:600px; height:100px"></textarea></td>
	</tr>
	<tr>
		<td class="html_label_generated">訂閱人數：</td><Td>{$counts}</td>
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
		<td class="html_label_required">標題：</td><Td><input type="text" name="subject" style="width:600px"></td>
	</tr>
	<tr>
		<td class="html_label_required">內容：</td><Td><textarea name="content" style="width:600px; height:200px"></textarea></td>
	</tr>
	<tr>
		<td colspan="2"><hr>
			<table width="100%">
				<tr>
					<td align="center"><input type="button" value="送出" onClick="Save();"></td>
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
editor_generate('content');
</script>
<script language="javascript">
	function showRecipients(){
		if(iForm.to[3].checked){
			$("#recipients").show();
		}
		else{
			$("#recipients").hide();
		}
	}
	function Save(){
		var iForm = document.iForm;
		if(iForm.to[3].checked && !iForm.recipients.value){
			alert("請輸入收件人!");
			iForm.recipients.focus();
		}
		else if(!iForm.subject.value){
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
