<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';

$id=$_REQUEST['id'];
$date=$_REQUEST['date'];
//JavaScript::setCharset("UTF-8");
include './include/db_open.php';
mysql_query("UPDATE Orders SET Status = 1, datePaid='$date' WHERE ID='$id'") or die(mysql_error());
mysql_query("UPDATE Orders SET dateShipped='$date' WHERE ID='$id' AND Deliver=0") or die(mysql_error());
mysql_query("UPDATE Payment SET datePaid='$date', Complete=1 WHERE Memo='$id'") or die(mysql_error());
//echo "UPDATE Orders SET Status = 1, datePaid='$date' WHERE ID='$id'";

$result = mysql_query("SELECT Deliver FROM Orders WHERE ID='$id'") or die(mysql_error());

if($product=mysql_fetch_array($result)){
	$oid = $id;
	if($product['Deliver'] == 0){	
		$result = mysql_query("SELECT Member.userID, Member.Name, Orders.Phone FROM Orders INNER JOIN Member ON Orders.Member=Member.No WHERE Orders.ID = '$oid'") or die(mysql_error());
		$member = mysql_fetch_array($result);

		$sql = "SELECT Product.*, logCertify.No AS lNo, logCertify.Serial FROM logCertify INNER JOIN Product ON logCertify.Product=Product.No WHERE Product.Deliver=0 AND Product.Cashflow=1 AND logCertify.orderID='$oid'";

		$result = mysql_query($sql) or die(mysql_error());
		while($certify = mysql_fetch_array($result)){
			$m_subject = "InTimeGo即購網[{$certify['Name']}]到店憑證";
			$m_recipient = $member['userID'];
			$name = $member['Name'];
			$bg="proven_paper_new.JPG";
			$h = 683;
			switch($certify["Restrict"]){
				case 1:
					$restrict = "每人不限交易次數，購量與限用數量";
					break;
				case 2:
					$restrict = "每人不限交易次數與購量，但每人到店限用 {$certify['maxUse']}</font> 張";
					break;
				case 3:
					$restrict = "每人限制一次</font>交易次數，限購 {$certify['maxBuy']}</font> 張，每人到店限用 {$certify['maxUse']}</font> 張";
					break;
				case 4:
					$restrict = "每人限制一次</font>交易次數，限購 {$certify['maxBuy']}</font> 張，不限每人使用數量";
					break;
			}
			$hours = str_replace("\n", "<br>", $certify['Hours']);
			$memo = str_replace("\n", "<br>", $certify['Memo']);

			$m_content = <<<EOD
				<table style="height:$h; width:640px; background:url(http://{$WEB_HOST}/images/{$bg}); background-repeat:no-repeat; background-position:top center" height="$h">
					<tr>
						<td style="height:100px">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:20px; padding-right:20px" valign="top">
							<table width="596">
								<tr>
									<td valign="top" align="right" nowrap>商品名稱：</td>
									<td valign="top" align="left">{$certify['Name']}<td>
								</tr>
								<tr>
									<td valign="top" align="right" nowrap>憑證序號：</td>
									<td valign="top" align="left">{$certify['Serial']}<td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">兌換期間：</td>
									<td style="text-align:left; line-height:20px;" valign="top">{$certify['dateValidate']}</font> 至 {$certify['dateExpire']}</font>，需 {$certify['daysBeforeReserve']}</font> {$certify['daysUnit']}前預約並告知使用團購憑證，否則恕無法提供服務，敬請配合。</td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">使用時段：</td>
									<td style="text-align:left; line-height:20px;" valign="top">{$hours}</font></td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">使用張數：</td>
									<td style="text-align:left; line-height:20px;" valign="top">{$restrict}
									</td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">使用方法：</td>
									<td style="text-align:left; line-height:20px;" valign="top">本憑證不限本人使用，為保障您的權益，持手機簡訊或列印email團購憑證使用，到店由店員抄寫憑證之消費碼與兌換時間，此消費行為始得生效。
									</td>
								</tr>
								<tr>
									<td style="text-align:right" nowrap valign="top">其他說明：</td>
									<td style="text-align:left; line-height:20px;" valign="top">{$memo}</font></td>
								</tr>
								<tr>
									<td valign="top" align="right" nowrap>店家住址：</td>
									<td valign="top" align="left">{$certify['Address']}<td>
								</tr>
								<tr>
									<td valign="top" align="right" nowrap>店家電話：</td>
									<td valign="top" align="left">{$certify['Phone']}<td>
								</tr>
								<tr>
									<td style="padding-top:20px" colspan="2">
										InTimeGo即購網 祝您 消費愉快<br>
										<a href="{$WEB_HOST}">{$WEB_HOST}</a><br>
									</td>
								</tr>
							</table>&nbsp;
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
EOD;
			$m_memo = "即購網到店憑證通知信";
			$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', '$date')";
			mysql_query($sql) or die (mysql_error());
			mysql_query("UPDATE logCertify SET dateSent='$date' WHERE No='" . $certify['lNo'] . "'");
			
			$info = "兌換期間{$certify['dateValidate']}~{$certify['dateExpire']}需{$certify['daysBeforeReserve']}{$certify['daysUnit']}前預約並告知使用團購憑證，否則恕無法提供服務，敬請配合";
			$info = mb_substr($info, 0, 45, 'utf8') . ((mb_strlen($info, 'utf8') > 45) ? "…" : "") ;
			$txt = "{$certify['Name']};到店憑證{$certify['Serial']};{$info};即購網";
			$sms_phone = $member['Phone'];
			$sms_content = $txt;
			include './sms_send.php';
		}				
	}	
}
	
include './include/db_close.php';
?>