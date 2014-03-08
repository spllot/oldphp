<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';

JavaScript::setCharset("UTF-8");
if(empty($_SESSION['member'])){
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
$date= $_REQUEST['date'];
$no = $_REQUEST['product'];// => 82
$phone = $_REQUEST['phone'];// => 0916184808
$payby = $_REQUEST['payby'];// => 1
$name = $_REQUEST['name'];// => 卓小男
$address = $_REQUEST['address'];// => 彰化縣福興鄉鎮平村阿力街1號
$title = $_REQUEST['title'];// => 
$unino = $_REQUEST['unino'];// => 
$use = $_REQUEST['use'];// => 780
$amount = $_REQUEST['amount'];// => 1
$agree = $_REQUEST['agree'];// => 1
$receipt = $_REQUEST['receipt'];// =
$zip = $_REQUEST['rzip'];// =
if($no != "" && $phone != "" && $amount != ""){
	if($agree == "1"){
		include './include/db_open.php';

		$sql = "SELECT *, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Product='$no' AND Member='" . $_SESSION['member']['No'] . "'), 0) AS Amounts, IFNULL((SELECT COUNT(*) FROM Orders WHERE Product='$no' AND Member='" . $_SESSION['member']['No'] . "'), 0) AS Buy, IFNULL((SELECT COUNT(*) FROM logCoupon INNER JOIN Coupon ON Coupon.No=logCoupon.couponNo WHERE logCoupon.Product=Product.No), 0) AS Coupon, IFNULL((SELECT COUNT(*) FROM Orders WHERE Orders.Product=Product.No AND Orders.Status <> 3), 0) AS Sold, IFNULL((SELECT SUM(Amount) FROM Orders WHERE Orders.Product=Product.No), 0) AS Solds, IFNULL((SELECT COUNT(*) FROM logActivity WHERE logActivity.Product=Product.No), 0) AS Joins, IFNULL((SELECT count(*) FROM Coupon WHERE Status = 1 AND Product=Product.No), 10000) AS coupon_used, (SELECT Name FROM Catalog WHERE Catalog.No = (SELECT Area1 FROM Member WHERE No=Product.Member)) AS Area1, (SELECT Address1 FROM Member WHERE No = Product.Member) AS Address1, (SELECT Latitude1 FROM Member WHERE No = Product.Member) AS M1, IF(Product.Activity = 0 AND (SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude) AS L1, IF(Product.Activity = 0 AND (SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude) AS L2, IFNULL((SELECT SUM(Quality) FROM logRating WHERE Owner = Product.Member), 0) as Rate, (SELECT Nick FROM Member WHERE Member.No = Product.Member) AS userName, (SELECT Name FROM Catalog WHERE Catalog.No = Product.Area) AS City, getDistance(IF((SELECT Latitude1 FROM Member WHERE No = Product.Member) > 0, (SELECT Latitude1 FROM Member WHERE No = Product.Member), Product.Latitude), IF((SELECT Longitude1 FROM Member WHERE No = Product.Member) > 0,(SELECT Longitude1 FROM Member WHERE No = Product.Member), Product.Longitude), '" . $_SESSION['Latitude'] . "', '" . $_SESSION['Longitude'] . "') AS KM FROM Product WHERE Status = 2 AND dateClose >= CURRENT_TIMESTAMP AND No = '$no' AND Cashflow=1 ORDER BY KM";
		$result = mysql_query($sql) or die(mysql_error());
		$data = mysql_fetch_array($result);

		//檢查數量
		if($data['Amount'] > 0){
			if($data['Quota'] <= $data['Solds']){
				JavaScript::Alert("已售完!");
				exit;
			}
			if($data['Quota'] < ($data['Solds'] + $amount)){
				JavaScript::Alert("剩餘數量不足，請修正購買數量!");
				exit;
			}
		}

		//檢查購買次數限制
		if($data['Cashflow'] == 1 && $data['Deliver'] == 0 && ($data["Restrict"] == 3 || $data["Restrict"] == 4)){
			if($data['Buy'] > 0){
				JavaScript::Alert("每人只限購買一次!");
				exit;
			}
			if(($data['Amounts'] + $amount) > $data['maxBuy']){
				JavaScript::Alert("每人只限購買{$data['maxBuy']}張!");
				exit;
			}
		}

		$balance=0;
		$result = mysql_query("SELECT COALESCE(SUM(Amount), 0) as Amount FROM logTransaction WHERE Owner='" . $_SESSION['member']['userID'] . "'");
		if($rs=mysql_fetch_array($result)){
			$balance = $rs['Amount'];
		}
		if($balance < $use){
			$use = $balance;
		}
		$result = mysql_query("");
		$result = mysql_query("SELECT * FROM Product WHERe No='$no'") or die(mysql_error());
		if($product = mysql_fetch_array($result)){
			$total = $amount * $product['Price1'] - $use;
			$prefix = str_replace("-", "", substr($date, 0, 10));//date('Ymd');
			if($product['Mode'] == 1){
				if($product['Deliver'] == 0){
					if($product['Broadcast'] == 1){
						$prefix .= "-a00-";
					}
					else{
						$prefix .= "-a11-";
					}
				}
				else{
					if($product['Broadcast'] == 1){
						$prefix .= "-b00-";
					}
					else{
						$prefix .= "-b11-";
					}
				}
			}
			else{
				if($product['Deliver'] == 0){
					if($product['Amount'] == 0){
						$prefix .= "-c10-";
					}
					else{
						$prefix .= "-c11-";
					}
				}
				else{
					if($product['Duration'] == 0){
						if($product['Amount'] == 0){
							$prefix .= "-d00-";
						}
						else{
							$prefix .= "-d01-";
						}
					}
					else{
						if($product['Amount'] == 0){
							$prefix .= "-d10-";
						}
						else{
							$prefix .= "-d11-";
						}
					}
				}
			}
			$result = mysql_query("SELECT ID FROM Orders WHERE ID LIKE '%$prefix%' ORDER BY ID DESC");
			if(mysql_num_rows($result) >0){
				list($curr_id)=mysql_fetch_row($result);
				$curr_no = (int)substr($curr_id, -4);
				$oid = $prefix . substr("000" . ($curr_no + 1), -4);
			}
			else{
				$oid = $prefix . "0001";
			}
			$status = 0;//(($total >0) ? 1 : 0);//0.未付款, 1.已付款, 2.已取消
			$sql = "INSERT INTO Orders set ID='$oid', Member='" . $_SESSION['member']['No'] . "', Seller='" . $product['Member'] . "', Product='" . $product['No'] . "', Deliver='" . $product['Deliver'] . "', pName='" . $product['Name'] . "', Price='" . $product['Price1'] . "', Amount='$amount', Total='" . $amount * $product['Price1'] . "', Name='$name', Phone='$phone', Zip='$zip', Address='$address', Referral='$referral', Receipt='$receipt', uniNo='$unino', Title='$title', dateSubmited='$date', Status='$status'";
			mysql_query($sql) or die(mysql_error());
			
			$sql = "INSERT INTO Items set orderID='$oid', Sort=0, Amount='$amount'";
			mysql_query($sql) or die(mysql_error());

			
			
			if($product['Deliver'] == 0){
				$char = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
				$sql = "SELECT * FROM logCertify WHERE Product='" . $product['No'] . "' ORDER BY Serial DESC LIMIT 1";
				$result1 = mysql_query($sql) or die(mysql_error());
				if($certify = mysql_fetch_array($result1)){
					$prefix = substr($certify['Serial'], 0, 1);
					$curr_no = (int)substr($certify['Serial'], 1, 4)+1;
				}
				else{
//					$result1 = mysql_query("SELECT DISTINCT Product FROM logCertify WHERE Seller='" . $product['Member'] . "' ORDER BY Product") or die(mysql_error());
					$result1 = mysql_query("SELECT YN FROM Config WHERE ID='serial'") or die(mysql_error());
					list($num) = mysql_fetch_row($result1);
					$num ++;
					mysql_query("UPDATE Config SET YN='$num' WHERE ID='serial'") or die(mysql_error());
					$prefix = $char[$num%26];
					$curr_no = 1;
				}
				$certify="";
				for($i=0; $i<$amount; $i++){						
					$serial = $prefix . substr("000" . ($curr_no + $i), -4) . Tools::newCode(5);
//					echo "<br>" . $serial;
					mysql_query("INSERT INTO logCertify SET Seller='" . $product['Member'] . "', orderID='$oid', Sort='$i', Serial='$serial', Product='" . $product['No'] . "'") or die (mysql_error());
					$certify .= $serial;
					if($i < $amount -1){
						$certify .= ",";
					}
				}
				$sql = "UPDATE Items set Certify='$certify' WHERE orderID='$oid' AND Sort=0";
				mysql_query($sql) or die(mysql_error());
			}

			if($use > 0){
//				mysql_query("INSERT INTO logTransaction(Owner, `Date`, Amount, Memo, useFor) VALUES ('" . $_SESSION['member']['userID'] . "', CURRENT_TIMESTAMP, '-{$use}', '$oid', '4')") or die(msyql_error());

				$prefix = "P" . date('ymd');
				$result = mysql_query("SELECT ID FROM Payment WHERE ID LIKE '%$prefix%' ORDER BY ID DESC") or die (mysql_error());
				if(mysql_num_rows($result) >0){
					list($curr_id)=mysql_fetch_row($result);
					$curr_no = (int)substr($curr_id, -4);
					
					$id = $prefix . substr("000" . ($curr_no + 1), -4);
				}
				else{
					$id = $prefix . "0001";
				}
				$fee=0;
				$complete = 1;
				$sql = "insert into Payment(ID, Member, Phone, payBy, Amount, Fee, dateSubmited, datePaid, Complete, Memo) VALUES('$id', '" . $_SESSION['member']['userID'] . "', '$phone', '4', '$use', '$fee', '$date', '$date', '$complete', '$oid')";
				mysql_query($sql) or die (mysql_error());
			}

			if($total > 0){
				switch($payby){
					case 1:
						$fee = ceil($total * 0.03);
						$select_paymethod=1;
						break;
					case 2:
						$fee = ceil($total * 0.02);
						$select_paymethod=4;
						break;
					case 3:
						$fee = ceil($total * 0.005);
						$select_paymethod=2;
						$complete = 1;
						break;
				}
				$fee = 0;

				$prefix = "P" . date('ymd');
				$result = mysql_query("SELECT ID FROM Payment WHERE ID LIKE '%$prefix%' ORDER BY ID DESC") or die (mysql_error());
				if(mysql_num_rows($result) >0){
					list($curr_id)=mysql_fetch_row($result);
					$curr_no = (int)substr($curr_id, -4);
					$id = $prefix . substr("000" . ($curr_no + 1), -4);
				}
				else{
					$id = $prefix . "0001";
				}
				$complete=0;
				$sql = "insert into Payment(ID, Member, Phone, payBy, Amount, Fee, dateSubmited, Complete, Memo) VALUES('$id', '" . $_SESSION['member']['userID'] . "', '$phone', '$payby', '$total', '$fee', '$date', '$complete', '$oid')";
				mysql_query($sql) or die (mysql_error());

				$mid = "3231";
				$txid = $id;
				$msg = $v1 . "|" . $mid . "|" . $txid . "|" . $total . "|" . $v2;
				$verify = md5($msg);
				$cemail = $_SESSION['member']['userID'];
				$cname = $_SESSION['member']['Name'];
				$caddress = $_SESSION['member']['Address'];
				$ctel = $_SESSION['member']['Phone'];
				$amount += $fee;
				$v1 = "030302f0a32277e1244b5dd15bd9ad5b";
				$v2 = "a5b3b9c9650e8bda2d143794e183e49e";
				$msg = $v1 . "|" . $mid . "|" . $txid . "|" . $total . "|" . $v2;
				$verify = md5($msg);

				JavaScript::Alert("訂單已送出，系統將導到台灣里付款系統，請依網頁指示進行付款動作!");
				JavaScript::Execute("parent.Payment('$oid')");
			}
			else{
				mysql_query("UPDATE Orders SET Status=1, datePaid='$date' WHERE ID='$oid'") or die(mysql_error());
				mysql_query("UPDATE Orders SET dateShipped='$date' WHERE ID='$oid' AND Deliver=0") or die(mysql_error());
				
				if($product['Deliver'] == 0){
				
					$result = mysql_query("SELECT Member.userID, Member.Name, Orders.Phone FROM Orders INNER JOIN Member ON Orders.Member=Member.No WHERE Orders.ID = '$oid'") or die(mysql_error());
					$member = mysql_fetch_array($result);

					$sql = "SELECT Product.*, logCertify.No AS lNo, logCertify.Serial FROM logCertify INNER JOIN Product ON logCertify.Product=Product.No WHERE Product.Deliver=0 AND Product.Cashflow=1 AND logCertify.orderID='$oid'";
					echo $sql;
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
												<td style="text-align:left" valign="top">{$hours}</font></td>
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
				JavaScript::Alert("訂單已送出!");
				JavaScript::Execute("parent.goOrders({$product['Deliver']})");
			}
		}
		else{
			JavaScript::Alert("找不到商品資料!");
		}
		include './include/db_close.php';
	}
	else{
		JavaScript::Alert("請仔細閱讀電子商務服務條款，並勾選願意遵守!");
	}
}
else{
	JavaScript::Alert("輸入欄位不足!");
}



?>