<?php
require_once 'template.php';
class Shop extends Template{
    var $title = "home";
    var $subtitle = "home";
    var $content = "";
	var $theme = "";
	function setTheme($new_theme){
		$this->theme = $new_theme;
	}//setTheme
    function setContent($new_content){
        $this->content = $new_content;
    }//setContent

    function setSubject($new_title){
        $this->title = $new_title;
    }
    function setSubtitle($new_subtitle){
        $this->subtitle = $new_subtitle;
    }

    function toString(){
        return <<<EOD
		<table cellpadding="0" cellspacing="0" border="0" style="width:100%;height:100%;background-color:#FFFFFF">
			<tr>
				<td style="height:277px; text-align:left; vertical-align:bottom; background-image:url('./images/$this->theme');">
					<table cellspacing="5">
						<tr style="height:50px">
							<td style="color:white; font-weight:bold;font-size:45px; vertical-align:top">$this->title</td>
							<td style="color:white; font-weight:bold;font-size:20px; vertical-align:bottom">$this->subtitle</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="vertical-align:top; text-align:center">
					<table style="width:95%;height:220px">
						<tr>
							<td style="vertical-align:top">$this->content</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td>&nbsp;</td>
							<td style="text-align:right; width:57px; height:57px"><img src="./images/corner.jpg"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
EOD;
    }//toString()
}//Shop

?>