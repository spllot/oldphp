<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
require_once '../class/javascript.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->account_income][1])){exit("權限不足!!");}
$tab = $_REQUEST['tab'];
$Y = $_REQUEST['Y'];
$M = $_REQUEST['M'];
$D = $_REQUEST['D'];
$payby=array(
	"無",
	"信用卡(3%)",
	"Web ATM",
	"ATM轉帳(0.5%)",
	"儲值金"
);
if($Y != "" && $M != "" && $D != ""){
	$sql = "SELECT *, Billing.No AS bNo,  DATEDIFF(CURDATE(), dateGenerate) AS Days FROM logBilling INNER JOIN Billing ON logBilling.No=Billing.logNo";
	$sql .= " WHERE Y='$Y' AND M='$M' AND D='$D' AND Apply=1 and logBilling.Refund=0";
	$sql .= " ORDER BY paymentID DESC";
	$result=mysql_query($sql) or die (mysql_error());
	$num=mysql_num_rows($result);
	if ($num>0){
		include '../classes/PHPExcel.php';
		include '../classes/PHPExcel/Writer/Excel2007.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$col = 0;
		$row=1; 
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "台灣里單號");$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "訂單編號");$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "請款源");$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "金額");$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "付款方式");$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "請款額");$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "手續費");$col++;
		$row++;
		$amount = 0;
		$total = 0;
		$fee = 0;
		while($rs = mysql_fetch_array($result)){
			$col=0;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['paymentID'], PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['orderID'], PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['Reason'], PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['Amount'], PHPExcel_Cell_DataType::TYPE_NUMERIC);$col++;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($payby[$rs['payBy']], PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['Total'], PHPExcel_Cell_DataType::TYPE_NUMERIC);$col++;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['Fee'], PHPExcel_Cell_DataType::TYPE_NUMERIC);$col++;
			$row++;
			$amount += $rs['Amount'];
			$total += $rs['Total'];
			$fee += $rs['Fee'];
		}
		
		$col=0;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);$col++;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);$col++;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);$col++;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($amount, PHPExcel_Cell_DataType::TYPE_NUMERIC);$col++;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);$col++;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($total, PHPExcel_Cell_DataType::TYPE_NUMERIC);$col++;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($fee, PHPExcel_Cell_DataType::TYPE_NUMERIC);$col++;

		$objPHPExcel->getActiveSheet()->setTitle('請款對帳檔');
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		$filename = "apply_" . date('Ymd') . ".xls";
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");;
		header("Content-Disposition: attachment;filename=$filename ");
		header("Content-Transfer-Encoding: binary ");
		$objWriter->save("php://output");
	}
	else{
		JavaScript::setCharset("UTF-8");
		JavaScript::Alert("無請款項目!");
	}
}
else{
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("輸入欄位不足!");
}
include '../include/db_close.php';
?>