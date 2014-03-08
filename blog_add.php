<?php
include './include/session.php';
include './include/db_open.php';

$left = 0;
$max = 0;
$use = 0;
$curr = date('Y-m');

$result = mysql_query("SELECT * FROM Config WHERE ID='$curr'");
if($rs = mysql_fetch_array($result)){
	$max = $rs['YN'];
}
$result = mysql_query("SELECT * FROM Config WHERE ID='{$curr}S'");
if($rs = mysql_fetch_array($result)){
	$price = $rs['YN'];
}

$result = mysql_query("SELECT IFNULL(COUNT(*), 0) FROM Blog WHERE dateSubmited LIKE '$curr%'");
if($rs = mysql_fetch_row($result)){
	$use = $rs[0];
}

$left = $max - $use;




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>InTimeGo—<?=date('n')?>月部落格行銷文章應徵</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<center>
<table style="width:750px" cellpadding="5" cellspacing="0">
	<tr style="height:30px">
		<td style="text-align:left; font-weight:bold; color:white; background:gray" align="left"><?=date('n')?>月部落格行銷文章應徵</td>
	</tr>
	<tr>
		<td>
		<?
	$result = mysql_query("SELECT * FROM Blog WHERE dateSubmited LIKE '$curr%' AND userID = '" . $_SESSION['member']['userID'] . "'");
	if(mysql_num_rows($result) == 0){

		if($left > 0){
			echo <<<EOD
			<form name="rForm" method="post" action="blog_save.php" target="iAction">
				<input type="hidden" name="product" value="$id">
			<table>
				<tr>
					<td align="right">文章主題：</td>
					<td align="left"><input type="text" style="width:600px" name="subject"></td>
				</tr>
				<tr>
					<td align="right">文章網址：</td>
					<td align="left"><input type="text" style="width:600px" name="url"></td>
				</tr>
				<tr>
					<td align="right">有效推薦人數：</td>
					<td align="left"><input type="text" style="width:600px" name="recommend"></td>
				</tr>
				<tr>
					<td align="right">有效回應人數：</td>
					<td align="left"><input type="text" style="width:600px" name="reply"></td>
				</tr>
				<tr>
					<td></td>
					<td style="text-align:left"><input type="checkbox" name="tostock" value="1">同意以行銷文章所得入股, 目前每股 <font color=blue>{$price}</font> 元  (註: 行銷文章所得乘以三倍為入股金)</td>
				</tr>
			</table>
			</form>
EOD;
		}
		else{
			echo "本月部落格文章徵求數已額滿";
		}
	}
	else{
		echo "每位會員應徵部落格行銷文章徵求, 每月僅能一次";
	}


include './include/db_close.php';

		?>
		</td>
	</tr>
	<tr>
		<td align="center">
			<input type="button" value="取消" onClick="$.fn.colorbox.close();" style="width:90px; height:30px">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<? if($left>0){?>
			<input type="button" value="送出" onClick="Save();" style="width:90px; height:30px">
		<?}?>
		</td>
	</tr>
</table>

</center>

<script language="javascript">
	function Save(){
		var rForm = document.rForm;
		if(!rForm.subject.value){
			alert("請輸入文章主題!");
			rForm.subject.focus();
		}
		else if(!rForm.url.value){
			alert("請輸入文章網址!");
			rForm.url.focus();
		}
		else if(!rForm.recommend.value){
			alert("請輸入有效推薦人數!");
			rForm.recommend.focus();
		}
		else if(!rForm.reply.value){
			alert("請輸入有效回應人數!");
			rForm.reply.focus();
		}
		else{
			rForm.submit();
			$.fn.colorbox.close();
		}
	}
</script>