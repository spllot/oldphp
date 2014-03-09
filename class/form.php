<?php
class Form{
    var $tmp_script = "";

    function setValue($new_field, $new_value){
        $this->tmp_script .= "setValue($new_field, \"" . str_replace("\r\n", "\\n", $new_value) ."\");\n";
    }//SetValue

    function setRadio($new_field, $new_index){
        $this->tmp_script .= "setRadio($new_field, $new_index);\n";
    }//setRadio

    function setRadioValue($new_field, $new_value){
        $this->tmp_script .= "setRadioValue($new_field, \"$new_value\");\n";
    }//setRadioValue

    function setCheckBox($new_field, $new_list){
        $this->tmp_script .= "setValue($new_field, \"$new_list\");\n";
    }//setCheckBox

    function setCheckBoxValue($new_field, $new_listvalue){
        $this->tmp_script .= "setCheckBoxValue($new_field, \"$new_listvalue\");\n";
    }//setCheckBoxValue

    function addComboItem($new_field, $new_value, $new_text){
        $this->tmp_script .= "addComboItem($new_field, \"$new_value\", \"$new_text\");\n";
    }//addCombo

    function setDisabled($new_field){
        $this->tmp_script .= "setDisabled($new_field);\n";
    }//setDisabled

    function toString(){
        $tStr = "";
        $tStr .= "<script language=\"javascript\">\n";
        $tStr .= $this->tmp_script;
        $tStr .= "</script>\n";
        return $tStr;
    }//toString

    function genEditor($new_field){
        $this->tmp_script .= "editor_generate('$new_field');\n";
    }//getEditor
    function genEditor2($new_field){
        $this->tmp_script .= "editor_generate2('$new_field');\n";
    }//getEditor

    function Show(){
        echo $this->toString();
    }//Show

}//JavaScript
/*

*/
?>