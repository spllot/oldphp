<?php
include './include/session.php';
include './class/javascript.php';
include './class/tools.php';
$no = $_REQUEST['item'];
$email = $_REQUEST['email1'];
$phone = $_REQUEST['phone'];
JavaScript::setCharset("UTF-8");

//print_r($_REQUEST);
if($no != "" && ($email != "" || $phone != "")){
	include './include/db_open.php';

	if($email != ""){
		if(!Tools::checkEMail($email)){JavaScript::Alert("電子郵件格式錯誤!!");exit;}
	}
	if($phone != ""){
		if(!Tools::checkCellPhone($phone)){JavaScript::Alert("手機號碼格式錯誤!!");exit;}
	}

	$coupon_quota=0;
	$balance=0;
	
	if($email != ""){
		$result = mysql_query("SELECT * FROM logCoupon INNER JOIN Coupon ON Coupon.Serial=logCoupon.Serial WHERE logCoupon.Product='$no' AND (logCoupon.EMail='$email') AND logCoupon.couponNo IN (SELECT No FROM Coupon WHERE Product='$no')") or die(mysql_error());
		if(mysql_num_rows($result) > 0){
			JavaScript::Alert("電子郵件已有此商品的憑證發送記錄!!");
			exit;
		}
	}
	if($phone != ""){
		$result = mysql_query("SELECT * FROM logCoupon INNER JOIN Coupon ON Coupon.Serial=logCoupon.Serial WHERE logCoupon.Product='$no' AND (logCoupon.Phone='$phone') AND logCoupon.couponNo IN (SELECT No FROM Coupon WHERE Product='$no')") or die(mysql_error());
		if(mysql_num_rows($result) > 0){
			JavaScript::Alert("手機號碼已有此商品的憑證發送記錄!!");
			exit;
		}
	}

	$result = mysql_query("SELECT *, IFNULL((SELECT count(*) FROM Coupon WHERE Status = 1 AND Product=Product.No), 10000) AS coupon_used, (SELECT userID FROM Member WHERE No=Product.Member) AS userID FROM Product WHERE No = '$no'") or die(mysql_error());
	if($product=mysql_fetch_array($result)){
	}
	else{
		exit();
	}

	$result = mysql_query("SELECT COALESCE(SUM(Amount), 0) as Amount FROM logTransaction WHERE Owner=(SELECT userID FROM Member WHERE No='" . $product['Member'] . "')");
	if($rs=mysql_fetch_array($result)){
		$balance = $rs['Amount'];
	}

	$coupon_quota = $product['coupon_quota'] - $product['coupon_used'];

	$result = mysql_query("SELECT * FROM Config");
	while($rs = mysql_fetch_array($result)){
		$_CONFIG[$rs['ID']] = $rs['YN'];
	}

	if($product['coupon_YN']==1){
		if($coupon_quota > 0){
			if($balance >= $_CONFIG['coupon']){
				$result = mysql_query("SELECT * FROM Coupon WHERE Product='$no' AND Status = 0 ORDER BY Sort ASC") or die(mysql_error());
				if($coupon=mysql_fetch_array($result)){
					mysql_query("UPDATE Coupon SET Status = 1 WHERE No='" . $coupon['No'] . "'") or die(mysql_error());
					$cost=0;
					if($phone != ""){
						$cost = $_CONFIG['coupon'];
					}
					mysql_query("INSERT INTO logCoupon SET Product = '{$coupon['Product']}', EMail='$email', Phone='$phone', dateSent=CURRENT_TIMESTAMP, Member='{$product['Member']}', Serial='{$coupon['Serial']}', Cost='$cost', couponNo='" . $coupon['No'] . "'") or die(msyql_error());
					mysql_query("INSERT INTO logTransaction(Owner, `Date`, Amount, Memo, useFor) VALUES ('" . $product['userID'] . "', CURRENT_TIMESTAMP, '-{$_CONFIG['coupon']}', '" . $coupon['Serial'] . "', '13')") or die(msyql_error());

					if($email != ""){
						$m_subject = "InTimeGo即購網[{$product['Name']}]優惠憑證";
						$m_recipient = $email;

						if($product['Cashflow'] == 1){
							$bg="proven_paper_new.JPG";
							$h = 683;
						}
						else{
							$bg="proven_paper.jpg";
							$h = 448;
						}
						if($coupon['Serial'] != ""){
							$serial = <<<EOD
											<tr>
												<td valign="top" align="right" nowrap>憑證序號：</td>
												<td valign="top" align="left">{$coupon['Serial']}<td>
											</tr>
EOD;
						}

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
												<td valign="top" align="left">{$product['Name']}<td>
											</tr>{$serial}
											<tr>
												<td valign="top" align="right" nowrap>優惠活動：</td>
												<td valign="top" align="left">{$product['coupon_info']}<td>
											</tr>
											<tr>
												<td valign="top" align="right" nowrap>使用方法：</td>
												<td valign="top" align="left">本憑證不限本人使用，為保障您的權益，持手機簡訊或列印email優惠憑證使用，到店由店員抄寫憑證之消費碼與兌換時間，此消費行為始得生效。<td>
											</tr>
											<tr>
												<td valign="top" align="right" nowrap>店家住址：</td>
												<td valign="top" align="left">{$product['Address']}<td>
											</tr>
											<tr>
												<td valign="top" align="right" nowrap>店家電話：</td>
												<td valign="top" align="left">{$product['Phone']}<td>
											</tr>
											<tr>
												<td style="padding-top:20px" colspan="2">
													InTimeGo即購網 祝您 消費愉快<br>
													<a href="http://{$WEB_HOST}">{$WEB_HOST}</a><br>
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
						$m_memo = "即購網優惠憑證通知信";
						$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
						mysql_query($sql) or die (mysql_error());
					}

					if($phone != ""){
						$info = mb_substr($product['coupon_info'], 0, 45, 'utf8') . ((mb_strlen($product['coupon_info'], 'utf8') > 45) ? "…" : "") ;
						$txt = "{$product['coupon_name']}" . (($coupon['Serial'] != "") ? ";憑證{$coupon['Serial']}" : ""). ";{$info};即購網";
						$sms_phone = $phone;
						$sms_content = $txt;
						include './sms_send.php';
					}
					JavaScript::Alert("憑證已送出!");
				}
			}
			else{
				JavaScript::Alert("商家儲值點數不足，無法發送!");
			}
		}
		else{
			JavaScript::Alert("已發送完畢!");
		}
	}
	else{
		JavaScript::Alert("該商品未設定優惠憑證發送或已發送完畢!");
	}
	include './include/db_close.php';
}
else{
	JavaScript::Alert("輸入欄位不足!");
}
JavaScript::Execute("parent.dialogClose();");
?>
