<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

$userid = $_REQUEST['email'];
$latitude = explode(",", str_replace(array("(", ")", " "), "", $_REQUEST['latitude']));
$name = $_REQUEST['name'];
$nick = $_REQUEST['nick'];
$phone = $_REQUEST['phone'];
$address = $_REQUEST['address'];
$subscribe = $_REQUEST['subscribe'];
$subscribearea = $_REQUEST['subscribe_area'];
$pass1 = $_REQUEST['pass1'];
$pass2 = $_REQUEST['pass2'];
$pass3 = $_REQUEST['pass3'];
$zip = $_REQUEST['rzip'];

JavaScript::setCharset("UTF-8");
if($name != "" && $nick != "" && $phone != "" && $address != ""){
	include './include/db_open.php';

	$sql = "SELECT * FROM Member WHERE userID <> '" . $_SESSION['member']['userID'] . "' AND Phone = '$phone'";
	$result = mysql_query($sql) or die (mysql_error());
	$sql = "SELECT * FROM Member WHERE userID <> '" . $_SESSION['member']['userID'] . "' AND Nick = '$nick'";
	$result1 = mysql_query($sql) or die (mysql_error());
	if(mysql_num_rows($result) > 0){
		JavaScript::Alert("手機已被使用!!");
	}
	else if(mysql_num_rows($result1) > 0){
		JavaScript::Alert("暱稱已被使用!!");
	}
	else{
		if($pass1 != ""){
			if($pass2 != "" && ($pass2==$pass3)){
				if(!Tools::checkPassword($pass2)){JavaScript::Alert("新密碼長度必須為6-12個字!!");exit;}
				$sql = "SELECT * FROM Member WHERE userID = '" . $_SESSION['member']['userID'] . "' AND userPass = binary'$pass1'";
				$result = mysql_query($sql) or die (mysql_error());
				if(mysql_num_rows($result) == 0){
					JavaScript::Alert("舊密碼錯誤!!");
				}
				else{
					$sql = "UPDATE Member SET Zip0='$zip', userPass='$pass2', Name='$name', Nick='$nick', Subscribe='$subscribe', subscribeArea='$subscribearea', dateUpdate=CURRENT_TIMESTAMP, updateBy = '" . $name . "' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
					$_SESSION['member']['Name'] = $name;
					$_SESSION['member']['Nick'] = $nick;
					$_SESSION['member']['Zip0'] = $zip;
					mysql_query($sql) or die (mysql_error());


					$sql = "";
					$msg = "資料已更新，密碼已修改!";
					if($phone != $_SESSION['member']['Phone'] && $address != $_SESSION['member']['Address0']){
						$sql = "UPDATE Member SET Phone='$phone', Address0='$address', Latitude0='" . $latitude[0] . "', Longitude0='" . $latitude[1] . "' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
						$msg = "變更手機及收件地址須經由email確認，請檢查您的信箱並點選確認網址，以便修改您的手機及收件地址\\n其它資料已更新，密碼已修改!";
					}
					else if($phone != $_SESSION['member']['Phone']){
						$sql = "UPDATE Member SET Phone='$phone' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
						$msg = "變更手機須經由email確認，請檢查您的信箱並點選確認網址，以便修改您的手機\\n其它資料已更新，密碼已修改!";
					}
					else if($address != $_SESSION['member']['Address0']){
						$sql = "UPDATE Member SET Address0='$address', Latitude0='" . $latitude[0] . "', Longitude0='" . $latitude[1] . "' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
						$msg = "變更收件地址須經由email確認，請檢查您的信箱並點選確認網址，以便修改您的收件地址\\n其它資料已更新，密碼已修改!";
					}
					
					if($sql != ""){
						$code = Tools::newPassword(10);
						$m_subject = "InTimeGo即購網會員資料修改確認信";
						$m_recipient = $_SESSION['member']['userID'];
						$confirm_url = "http://{$WEB_HOST}/member_data_update.php?email=$m_recipient&code=$code";

						$m_content = <<<EOD
							<table>
								<tr>
									<td>
										親愛的 $name ：
									</td>
								</tr>
								<tr>
									<td>
										此封信件由 InTimeGo即購網(本站) 所發出的。<br>
												本站收到您變更資料的要求，基於網站安全考量，請點選下列網址，以便進行資料修改。<br><br>
										<a href="$confirm_url">$confirm_url</a><br><br>

									</td>
								</tr>
								<tr>
									<td>
										InTimeGo即購網<br>
										http://{$WEB_HOST}<br>
									</td>
								</tr>
							</table>
EOD;
						$sql = sprintf("INSERT INTO Modifier SET Owner='" . $_SESSION['member']['userID'] . "', `SQL`='%s', Code='$code'", urlencode($sql));

						mysql_query($sql) or die (mysql_error());
						$m_memo = "會員資料修改確認信";
						$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
						mysql_query($sql) or die (mysql_error());			
					}

					JavaScript::Alert($msg);
					JavaScript::setURL("member_data.php" ,"window.parent");
				}
			}
			else{
				JavaScript::Alert("新密碼不相符!!");
			}
		}
		else{
			$sql = "UPDATE Member SET Zip0='$zip', Name='$name', Nick='$nick', Subscribe='$subscribe', subscribeArea='$subscribearea', dateUpdate=CURRENT_TIMESTAMP, updateBy = '" . $name . "' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
			$_SESSION['member']['Name'] = $name;
			$_SESSION['member']['Nick'] = $nick;
			$_SESSION['member']['Zip0'] = $zip;
			mysql_query($sql) or die (mysql_error());


			$sql = "";
			$msg = "資料已更新，密碼未修改!";
			if($phone != $_SESSION['member']['Phone'] && $address != $_SESSION['member']['Address0']){
				$sql = "UPDATE Member SET Phone='$phone', Address0='$address', Latitude0='" . $latitude[0] . "', Longitude0='" . $latitude[1] . "' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
				$msg = "變更手機及收件地址須經由email確認，請檢查您的信箱並點選確認網址，以便修改您的手機及收件地址\\n其它資料已更新，密碼未修改!";
				$items = "手機原為：{$_SESSION['member']['Phone']}，改為：{$phone}<br>";
				$items .= "收件地址原為：{$_SESSION['member']['Address0']}，改為：{$address}<br>";
			}
			else if($phone != $_SESSION['member']['Phone']){
				$sql = "UPDATE Member SET Phone='$phone' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
				$msg = "變更手機須經由email確認，請檢查您的信箱並點選確認網址，以便修改您的手機\\n其它資料已更新，密碼未修改!";
				$items = "手機原為：{$_SESSION['member']['Phone']}，改為：{$phone}<br>";
			}
			else if($address != $_SESSION['member']['Address0']){
				$sql = "UPDATE Member SET Address0='$address', Latitude0='" . $latitude[0] . "', Longitude0='" . $latitude[1] . "' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
				$msg = "變更收件地址須經由email確認，請檢查您的信箱並點選確認網址，以便修改您的收件地址\\n其它資料已更新，密碼未修改!";
				$items = "收件地址原為：{$_SESSION['member']['Address0']}，改為：{$address}<br>";
			}

			if($sql != ""){
				$code = Tools::newPassword(10);
				$m_subject = "InTimeGo即購網會員資料修改確認信";
				$m_recipient = $_SESSION['member']['userID'];
				$confirm_url = "http://{$WEB_HOST}/member_data_update.php?email=$m_recipient&code=$code";
				$date = date('Y-m-d H:i:s');
				$m_content = <<<EOD
					<table>
						<tr>
							<td>
								親愛的 $name ：
							</td>
						</tr>
						<tr>
							<td>
								此封信件由 InTimeGo即購網(本站) 所發出的。<br><br>
								本站於 {$date} 收到您變更資料的要求：<br>
								{$items}
								基於網站安全考量，請點選下列網址，以便進行資料修改。<br>
								若此資料並非您所變更，代表您的資料已被駭客入侵，所以請不要點下列網址，建議此時需儘速到網站變更您的密碼<br><br>
								<a href="$confirm_url">$confirm_url</a><br><br>
								
							</td>
						</tr>
						<tr>
							<td>
								InTimeGo即購網<br>
								http://{$WEB_HOST}<br>
							</td>
						</tr>
					</table>
EOD;
				$sql = sprintf("INSERT INTO Modifier SET Owner='" . $_SESSION['member']['userID'] . "', `SQL`='%s', Code='$code'", urlencode($sql));
				mysql_query($sql) or die (mysql_error());
				$m_memo = "會員資料修改確認信";
				$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
				mysql_query($sql) or die (mysql_error());			
			}



			JavaScript::Alert($msg);
			JavaScript::setURL("member_data.php" ,"window.parent");
		}
	}
	
	
	
	
	include './include/db_close.php';
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
