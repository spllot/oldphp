<?php
class Tools{
	function isImage($xExt){
		$IMAGE_FILE = array("JPEG", "JPG", "PNG", "GIF");
		$xExt = strtoupper($xExt);
		return in_array($xExt, $IMAGE_FILE);
	}

	function unzip($file, $path) {
		$zip = zip_open($file);
		if ($zip) {
			while ($zip_entry = zip_read($zip)) {
			if (zip_entry_filesize($zip_entry) > 0) {
				$complete_path = $path . "/" .dirname(zip_entry_name($zip_entry));
				$complete_name = $path . "/" .zip_entry_name($zip_entry);
				 if(!file_exists($complete_path)) {
					$tmp = '';
					foreach(explode('/',$complete_path) AS $k) {
						$tmp .= $k.'/';
					if(!file_exists($tmp)) {
						mkdir($tmp, 0777);
					}
			 	}
		       }
			 if (zip_entry_open($zip, $zip_entry, "r")) {
				$fd = fopen($complete_name, 'w');
				fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
				fclose($fd);
				zip_entry_close($zip_entry);
			  }
		    }
		  }
			zip_close($zip);
		}
	} 

	function cutString($new_str, $new_len){
		$new_str = strip_tags($new_str);
		return mb_strimwidth($new_str, 0, $new_len, '...', 'UTF-8');
	}
	function getShareLink($page_url, $page_title){
		return <<<EOD
										<a href="javascript: void(window.open('http://www.facebook.com/share.php?u='.concat(encodeURIComponent(location.href)) ));"><img src="./images/icon_facebook.gif" border="0"><a>
										<a href="javascript: void(window.open('http://twitter.com/home/?status='.concat(encodeURIComponent(document.title)) .concat(' ') .concat(encodeURIComponent(location.href))));"><img src="./images/icon_twitter.gif" border="0"><a>
										<a href="javascript: void(window.open('http://www.plurk.com/?qualifier=shares&status=' .concat(encodeURIComponent(location.href)) .concat(' ') .concat('&#40;') .concat(encodeURIComponent(document.title)) .concat('&#41;')));"><img src="./images/icon_plurk.jpg" border="0"><a>
EOD;
	}
	function parsePath($new_path){
		$paths = explode(">", $new_path);
		return trim($paths[sizeof($paths) - 1]);

	}

	function Surround($new_string){
		$str = "";
		$str .= "<table cellpadding=\"0\" cellspacing=\"\" border=\"0\">";
		$str .= "<tr>";
		for($i = 0; $i<strlen($new_string); $i++){
			$str .= "<td style=\"font-size:10pt\">" . substr($new_string, $i, 1) . "</td>";
		}
		$str .= "</tr>";
		$str .= "</table>";
		return $str;
	}

	function formatBytes($bytes, $precision = 2) { 
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
	   
		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1); 
	   
		$bytes /= pow(1024, $pow); 
	   
		return round($bytes, $precision) . ' ' . $units[$pow]; 
	} 


	function lastIndexOf($string,$item){
		$index=strpos(strrev($string),strrev($item));
		if ($index){
			$index=strlen($string)-strlen($item)-$index;
			return $index;
		}
		else{
			return -1;
		}
	}
	function getRemoteIP(){
		return ((getenv(HTTP_X_FORWARDED_FOR)) ?  getenv(HTTP_X_FORWARDED_FOR) :  getenv(REMOTE_ADDR));
	}

	function getExts ($filename){ 
		$filename = strtolower($filename) ; 
		$exts = explode("[/\\.]", $filename) ; 
		$n = count($exts)-1; 
		$exts = $exts[$n]; 
		return $exts; 
	} 

    function parseInt($new_int){
        return Tools::parseInt2($new_int, 0);
    }//parseInt

    function parseInt2($new_int, $new_value){
        settype($new_int, "int");
        if ($new_int > 0){
            return $new_int;
        }//if
        else{
            settype($new_value, "int");
            return $new_value;
        }//else
    }//parseInt2

    function parseString($new_string){
        return Tools::parseString2($new_string, "");
    }//parseString

    function parseString2($new_string, $new_value){
        settype($new_string, "string");
        settype($new_value, "string");
        if (strlen($new_string) > 0){
            return $new_string;
        }//if
        else{
            return $new_value;
        }//else
    }//parseString


	function checkCellPhone($new_phone){
        return preg_match("/09[0-9]{8}$/", $new_phone);
	}//checkCellPhone

    function checkEMail($new_email){
        return preg_match("/^[^\s()<>@,;:\"\/\[\]?=]+@\w[\w-]*(\.\w[\w-]*)*\.[a-z]{2,}$/i", $new_email);
	}//checkEMail

    function checkAccount($new_account){
        return preg_match("/^[a-zA-Z][a-zA-Z0-9]{2,11}$/", $new_account);
    }//checkAccount

    function checkPassword($new_password){
        return preg_match("/[a-zA-Z0-9]{6,12}$/", $new_password);
    }//checkPassword

    function newPassword($new_length){
        $pattern = "abchefghjkmnpqrstuvwxyz0123456789";
        srand((double)microtime()*1000000);
      	$i = 0;
      	while ($i < $new_length) {
            $num = rand() % 33;
        	$tmp = substr($pattern, $num, 1);
        	$pass = $pass . $tmp;
        	$i++;
      	}//while
      	return $pass;
    }//newPassword

    function newCode($new_length){
        $pattern = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        srand((double)microtime()*1000000);
      	$i = 0;
      	while ($i < $new_length) {
            $num = rand() % 26;
        	$tmp = substr($pattern, $num, 1);
        	$pass = $pass . $tmp;
        	$i++;
      	}//while
      	return $pass;
    }//newCode

    function readFromFile($fileName){
        $file = "G\"$fileName\"!!";
        if (file_exists($fileName) > 0){
            $file = file_get_contents($fileName);
        }//if
        return $file;
    }//readFromFile

    function getHeader($newCode){
	   $cur_year  = date('Y') - 1911;
	   $cur_month = date('m');
	   $cur_day  = date('d');
       return $newCode . $cur_year . $cur_month . $cur_day;

    }
	function getNextNo($code, $currNo){
	   if ($currNo){
			$currNo = substr($currNo, 8);
			$nextNo = substr("0000" . ($currNo + 1), -4);
	   }
	   else{
			$nextNo = "0001";
	   }
	   return Tools::getHeader($code) . $nextNo;
	}//


	function getImageType($new_subname){
		switch (strtoupper($new_subname)) {
		  case "JPEG":
			return "JPEG";
		  case "JPG":
			return "JPEG";
		  case "JPE":
			return "JPEG";
		  case "GIF":
			return "GIF";
		  case "PNG":
			return "PNG";
		}
	}

	function loadImage($imgname, $imgtype){
		if(file_exists($imgname)){
			if ( $imgtype == "JPEG" )
			  $im = @ImageCreateFromJPEG($imgname);
			elseif ( $imgtype == "PNG" )
			  $im = @ImageCreateFromPNG($imgname);
			else	
			  $im = @ImageCreateFromGIF($imgname);
		}
	   if (!$im) {
		   $im  = ImageCreateTrueColor(150, 30);
		   $bgc = ImageColorAllocate($im, 255, 255, 255);
		   $tc  = ImageColorAllocate($im, 0, 0, 0);
		   ImageFilledRectangle($im, 0, 0, 150, 30, $bgc);
		   ImageString($im, 1, 5, 5, "Error loading $imgname", $tc);
	   }
	   return $im;
	}
	/*傳回 0 表示這個身分證字號是正確的*/
	function check_roc_id($roc_id){
		if(strlen($roc_id) != 10){
			return -1;
		}
		$id_head = array(
			'A' => 10, 'B' => 11, 'C' => 12, 'D' => 13, 'E' => 14, 
			'F' => 15, 'G' => 16, 'H' => 17, 'J' => 18, 'K' => 19, 
			'L' => 20, 'M' => 21, 'N' => 22, 'P' => 23, 'Q' => 24, 
			'R' => 25, 'S' => 26, 'T' => 27, 'U' => 28, 'V' => 29, 
			'W' => 30, 'X' => 31, 'Y' => 32, 'Z' => 33, 'I' => 34, 
			'O' => 35); 

		$n0 = $id_head[strtoupper(substr($roc_id, 0, 1))]; 
		$n[] = '0'; 
		$n[] = substr($n0, 0, 1); 
		$n[] = substr($n0, 1, 1); 

		for ($lop1=1; $lop1<strlen($roc_id); $lop1++) { 
			$n[] = substr($roc_id, $lop1, 1); 
		}

		return (
			($n[1] + ($n[2]*9) + ($n[3]* 8)+ ($n[4]*7) + ($n[5]*6) + 
			($n[6]*5) + ($n[7]*4) + ($n[8]*3) + ($n[9]*2) + $n[10] + 
			$n[11]) % 10); 
	}

	function checkNick($id){   
		$head = array('A'=>1,'I'=>39,'O'=>48,'B'=>10,'C'=>19,'D'=>28,   
					  'E'=>37,'F'=>46,'G'=>55,'H'=>64,'J'=>73,'K'=>82,   
					  'L'=>2,'M'=>11,'N'=>20,'P'=>29,'Q'=>38,'R'=>47,   
					  'S'=>56,'T'=>65,'U'=>74,'V'=>83,'W'=>21,'X'=>3,   
					  'Y'=>12,'Z'=>30);   
		$multiply = array(8,7,6,5,4,3,2,1);   
		if (preg_match("/^[a-zA-Z][1-2][0-9]+$/",$id) && strlen($id) == 10){   
			$len = strlen($id);   
			for($i=0; $i<$len; $i++){   
				$stringArray[$i] = substr($id,$i,1);   
			}   
			$total = $headPoint[array_shift($stringArray)];   
			$point = array_pop($stringArray);   
			$len = count($stringArray);
			for($j=0; $j<$len; $j++){   
				$total += $stringArray[$j]*$multiply[$j];   
			}   
			if (($total%10 == 0 )?0:10-$total%10 != $point) {   
				return false;   
			}
			else {   
				return true;   
			}   
		} 
		else {   
		   return false;   
		}   
	} 
}//Tools


/*
echo Tools::newPassword(6);
if (Tools::checkAccount("a9233212311111111111")) {
    echo "AAA";
}//if
echo Tools::checkPassword("01a9dfsdfa");
echo Tools::checkPassword("a#9dfsdfa");

echo crypt("TEST");
echo Tools::parseInt2("AAAA", "512");
$t = new tools();
$t->jsAlert('AAAA');
echo addcslashes("'dd'dd'", "\'");
*/
?>