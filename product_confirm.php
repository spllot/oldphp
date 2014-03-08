<?php
require_once './class/javascript.php';
require_once './class/tools.php';
$code = $_REQUEST["code"];
$email = $_REQUEST["email"];
$no = $_REQUEST["no"];
include("./include/db_open.php");
JavaScript::setCharset("UTF-8");
if ($code == "" || $email == ""|| $no == ""){
    JavaScript::Alert("輸入欄位不足!!");
    exit;
}//if
else{
	$sql = "SELECT Product.* FROM Product INNER JOIN Member ON Member.No=Product.Member WHERE Member.userID = '$email' AND Product.Code = binary'$code' AND Product.No='$no' AND Product.Status = '5'";
	$result = mysql_query($sql) or die (mysql_error());
	if($rs=mysql_fetch_array($result)){
		$sql = "UPDATE Product SET Status = 2, dateConfirm=CURRENT_TIMESTAMP, Code='' WHERE No ='" . $no . "'";
		mysql_query($sql) or die("資料庫錯誤：" . mysql_error());

		if($rs['Mode'] == 2 && $rs['Deliver'] == 0){
			$result1 = mysql_query("SELECT * FROM Product WHERE Status=2 AND Mode=2 AND Deliver=0 AND dateClose >= CURRENT_TIMESTAMP AND Member='" . $rs['Member'] . "' ORDER BY Sort, dateApprove DESC") or die(mysql_error());
			$i=0;
			while($rs1=mysql_fetch_array($result1)){
				$i++;
				mysql_query("UPDATE Product SET Sort='" . $i . "' WHERE No='" . $rs1['No'] . "'") or die(mysql_error());
			}
		}
	    JavaScript::Alert("您的商品提案已通過!!");
	}
	else{
	    JavaScript::Alert("資料錯誤!!");
	}
}//else
JavaScript::setURL("./", "window");
include("./include/db_close.php");
?>