<?php
class Password{
    var $salt = "abchefghjkmnpqrstuvwxyz0123456789";
    function getNew($new_length) {
      srand((double)microtime()*1000000);
      	$i = 0;
      	while ($i < $new_length) {
            $num = rand() % 33;
        	$tmp = substr($this->salt, $num, 1);
        	$pass = $pass . $tmp;
        	$i++;
      	}//while
      	return $pass;
    }//Random

    function Validate($new_pass){


    }//Check
}//Password

 $p = new Password();
 $newpass = $p->getNew(6);
 echo $newpass."<br>";
 echo md5($newpass);  /*
*/
 ?>