<?php
class auth{
    function newpasswd(){
        srand((double)microtime()*1000000);
        $L_len = rand(6, 9);
        $passwd = "";
        for ($i = 0; $i < $L_len; $i++)
            $passwd .= chr(rand(40, 90));
        return $passwd;
    }//newpasswd

    function enpasswd($new_passwd){
        $key = (date("U") % 94);
        $len = strlen($new_passwd);
        for ($i = 0; $i < $len; $i++)
            $new_passwd[$i] = chr(((ord($new_passwd[$i]) - 33 + $key) % 94) + 33);
        $new_passwd = addslashes($new_passwd . chr($key + 33));
        return $new_passwd;
    }//enpasswd

    function depasswd($org_passwd){
        $key = ord(substr($org_passwd, -1)) - 33;
        $passwd = substr($org_passwd, 0, -1);
        $len = strlen($passwd);
        for($i = 0; $i < $len; $i++){
            $temp = ord($passwd[$i]) - 33;
            if ($temp < $key)
                $temp += 94;
            $passwd[$i] = chr($temp - $key + 33);
        }//for
        return $passwd;
    }//depasswd
}//auth
/*
$a = new auth();
$newpasswd = $a->enpasswd("admin");

echo ($newpasswd)."<br>";
echo $a-> depasswd("00000z")."<br>";
echo $a-> enpasswd("55555")."<br>";
*/
?>