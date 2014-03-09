<?php
class Grid{
	var $columnWidth = array();
	var $Header = array();
	var $Rows = array();
	var $columnStyle = array();
	
	function setColumnWidth($new_width){
		$this->columnWidth = $new_width;
	}

	function setColumnStyle($new_style){
		$this->columnStyle = $new_style;
	}

	function setHeader($new_header){
		$this->Header = $new_header;
	}

	function addRow($new_row){
		$this->Rows[sizeof($this->Rows)] = $new_row;
	}

	function toString(){
		$tStr = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolordark=\"#FFFFFF\" bordercolorlight=\"#99CCFF\">\n";
		$tStr .= " <tr>";
		for($i = 0; $i<sizeof($this->Header); $i++){
			$tStr .= "<th class=\"grid_header\" width=\"" . $this->columnWidth[$i] . "\">" . $this->Header[$i] . "</th>";
		}
		$tStr .= " </tr>";
		
		for($i = 0; $i<sizeof($this->Rows); $i++){
			$tStr .= "<tr>";
			for($j = 0; $j < sizeof($this->Rows[$i]); $j++){
				$tStr .= "<td class=\"" . $this->columnStyle[$j] . "\">" . $this->Rows[$i][$j] . "</td>";
			}
			$tStr .= "</tr>";
		}
		$tStr .= "</table>\n";
		return $tStr;
	}

	function Show(){
		echo $this->toString();
	}
}

/*
$grid = new Grid();
$grid->setHeader(array("111", "222"));
$grid->addRow(array("333", "4444"));
$grid->addRow(array("5555", "6666"));
$grid->Show();
*/
?>