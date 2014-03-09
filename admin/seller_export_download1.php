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
$payby=array(
	"無",
	"信用卡(3%)",
	"Web ATM",
	"ATM轉帳(0.5%)",
	"儲值金"
);
if($Y != "" && $M != ""){
	$sql = "SELECT Name, userID, Bank, bNo, Branch, Account, Total FROM sellerExport INNER JOIN Member ON sellerExport.Seller=Member.No WHERE Y='$Y' AND M='$M'";
	$result=mysql_query($sql) or die (mysql_error());
	$num=mysql_num_rows($result);
	if ($num>0){
		include '../classes/PHPExcel.php';
		include '../classes/PHPExcel/Writer/Excel2007.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$col = 0;
		$row=1; 
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "賣家名稱");$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "銀行名稱");$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "分支行");$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "銀行代號");$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "轉入帳號");$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "轉帳金額");$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr(65 + $col) . "$row", "受款人email");$col++;
		$row++;
		$amount = 0;
		$total = 0;
		$fee = 0;
		while($rs = mysql_fetch_array($result)){
			$col=0;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['Name'], PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['Bank'], PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['Branch'], PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['bNo'], PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['Account'], PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['Total'], PHPExcel_Cell_DataType::TYPE_NUMERIC);$col++;
			$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($rs['userID'], PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$row++;
			$amount += $rs['Amount'];
			$total += $rs['Total'];
			$fee += $rs['Fee'];
		}
		
		$col=0;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);$col++;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);$col++;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);$col++;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);$col++;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);$col++;
		$objPHPExcel->getActiveSheet()->getCell(chr(65 + $col) . "$row")->setValueExplicit($total, PHPExcel_Cell_DataType::TYPE_NUMERIC);$col++;

		$objPHPExcel->getActiveSheet()->setTitle('銀行匯款資料檔');
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		$filename = "bank_" . date('Ymd') . ".xls";
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
		JavaScript::Alert("無匯款項目!");
	}
}
else{
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("輸入欄位不足!");
}
include '../include/db_close.php';
?>