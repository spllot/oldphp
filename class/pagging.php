<?php
class Pagging{
    var $total_page = 1;
    var $current_page = 1;

    function Pagging($new_total, $new_page_no){
        $this->setTotalPage($new_total);
        $this->setCurrPage($new_page_no);
    }//Pagging
    function setTotalPage($new_total){
        $this->total_page = $new_total;
    }//setTotalPage

    function setCurrPage($new_page_no){
        $this->current_page = $new_page_no;
    }//setCurrPage

    function toString(){
        $tStr = "<table width=\"100%\">\n<form name=\"pagging\">";
        $tStr .= "<tr>\n";
        $tStr .= "<td align=\"left\">\n";
        $tStr .= "<input class=\"command\" type=\"button\" value=\"上一頁\" onClick=\"prevPage();\"";
        if ($this->current_page == 1){$tStr .= " DISABLED";}
        $tStr .= ">\n";
        $tStr .= "</td>\n";
        $tStr .= "<td align=\"center\">\n";
        $tStr .= "<select name=\"pageno\" onChange=\"jumpPage();\">\n";
        for ($i=1; $i <= $this->total_page; $i++){
            $tStr .= "<option value=\"$i\"";
            if ($i == $this->current_page){
                $tStr .= " SELECTED";
            }//if
            $tStr .= ">第 $i 頁</option>\n";
        }//for                                                                                      \
        $tStr .= "</select>\n";
        $tStr .= "</td>\n";
        $tStr .= "<td align=\"right\">\n";
        $tStr .= "<input class=\"command\" type=\"button\" value=\"下一頁\" onClick=\"nextPage();\"";
        if ($this->current_page == $this->total_page){$tStr .= " DISABLED";}
        $tStr .= ">\n";
        $tStr .= "</td>\n";
        $tStr .= "</tr>\n";
        $tStr .= "</form></table>\n";
        return $tStr;
    }//toString

    function Show(){
        echo $this->toString();
    }//Show
}//Pagging
/*
$p = new Pagging(10, 4);
echo $p->toString();
*/
?>