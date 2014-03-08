<?php
include '/home/intimego/www/include/db_open.php';
$today = date('Y-m-d');
$result = mysql_query("SELECT *, (SELECT COUNT(*) FROM logActivity WHERE Product=Product.No) AS Joins FROM Product WHERE dateApprove <> '0000-00-00 00:00:00' AND Activity=1 AND activity_end < '$today' AND Draw = '0000-00-00 00:00:00'")or die(mysql_error());
//activity_page  activity_start  activity_end  activity_ann  activity_min  activity_per  activity_draw activity_quota
while($data=mysql_fetch_array($result)){
//	echo $data['Joins'] . "<br>";
//	echo $data['activity_min'] . "<br>";
	if ($data['Joins'] >= $data['activity_min']){
		$max = $data['activity_quota'] * $data['activity_per'];
		$quota = floor($data['Joins'] / $data['activity_per']);
		$quota = (($quota < $data['activity_quota']) ? $quota : $data['activity_quota']);
		$win = array();
//		echo $quota . "<br>";
		if($quota > 0){
			for($i=0; $i<$quota; $i++){
				$r = rand(1, $max);
				while(in_array($r, $win)){
					$win[$i] = $r;
				}
			}

			$result1 = mysql_query("SELECT * FROM logActivity WHERE Product='" . $data['No'] . "' ORDER BY dateJoined LIMIT $max");
			$i=0;
			$j=0;
			while($rs=mysql_fetch_array($result1)){
				$i++;
				if(in_array($i, $win)){
					$j++;
					$sql = "UPDATE logActivity SET Win=$j WHERE No='" . $rs['No'] . "'";
//					echo $sql . "<br>";
					mysql_query($sql) or die(mysql_error());
				}
			}
		}
	}
	$sql = "UPDATE Product SET Draw = CURRENT_TIMESTAMP WHERE No='" . $data['No'] . "'";
//	echo $sql . "<br>";
	mysql_query($sql) or die(mysql_error());
}
include '/home/intimego/www/include/db_close.php';
?>