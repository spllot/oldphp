<?php
		$status = "";

		$service = "<a href='javascript:parent.Dialog(\"orders_help.php?id={$data['ID']}\");'>問題詢問</a>";
		$service1 = "<a href='javascript:parent.Dialog(\"orders_help2.php?id={$data['ID']}\");'>問題詢問</a>";

		switch($data['Status']){//0.未付款, 1.已付款, 2.退訂, 3.取消
			case 0:
				$status = "待付款";
				break;
			case 1:
				if($data['Refund'] == 1){
					$status = "退訂中";
					if($data['dateRefund'] != "0000-00-00 00:00:00"){
						$status = "已退款";
					}
					$status .= (($data['Transfer'] == 1) ? "<br>(" . (($data['Expire'] == 1) ? "到期":"") . "轉儲值)" :"");
					$service1 .= "<br><a href='javascript:parent.Dialog(\"orders_refund_reason.php?no={$data['itemNo']}\");'>退訂問題</a>";

				}
				else{
					if($data['Deliver'] == 1){
						if($data['dateShipped'] == "0000-00-00 00:00:00"){
							$status = "待發貨";
							$service .= "<br><a href='javascript:parent.Dialog(\"orders_refund.php?id={$data['ID']}\");'>退訂申請</a>";
						}
						else{
							$trial = date("Y-m-d", strtotime($data['dateShipped'] . " +10 days")) . "23:59:59";
							if($now > $trial){
								$status = "已完成";
							}
							else{
								$status = "待鑑賞";
								$service .= "<br><a href='javascript:parent.Dialog(\"orders_refund.php?id={$data['ID']}\");'>退訂申請</a>";
							}
						}
					}
					else{
						if($data['dateUse'] != '0000-00-00 00:00:00'){
							$status = "已完成";
						}
						else if($data['dateShipped'] == "0000-00-00 00:00:00"){
							$status = "待鑑賞";
						}
						else{
							$trial = date("Y-m-d", strtotime($data['dateShipped'] . " +7 days")) . "23:59:59";
							if($now > $trial){
								$status = "待消費";
							}
							else{
								$status = "待鑑賞";
								$service .= "<br><a href='javascript:parent.Dialog(\"orders_refund.php?id={$data['ID']}\");'>退訂申請</a>";
							}
						}
					}
				}
				break;
			case 2:
				$status = "退訂中";
				if($data['dateRefund'] != "0000-00-00 00:00:00"){
					$status = "已退款";
				}
				$status .= (($data['Transfer'] == 1) ? "<br>(" . (($data['Expire'] == 1) ? "到期":"") . "轉儲值)" :"");
				$service1 .= "<br><a href='javascript:parent.Dialog(\"orders_refund_reason.php?no={$data['itemNo']}\");'>退訂問題</a>";
				break;
			case 3:
				$status = "已取消";
				break;
		}

?>