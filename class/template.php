<?php
class Template{
    var $content = "";
    var $jsFile = "";
    var $title = "";

    function setTitle($new_title){
        $this->title = $new_title;
    }//setTitle

    function addContent($new_content){
        $this->content .= $new_content . "\n";
    }//addContent

    function addContentFile($contentFileName){
        $this->content .= $this->readFromFile($contentFileName) . "\n";
    }//addContentFile

    function readFromFile($fileName){
        $file = "G\"$fileName\"!!";
        if (file_exists($fileName) > 0){
            $file = file_get_contents($fileName);
        }//if
        return $file;
    }//readFromFile

    function show(){
        echo $this->toString();
    }//show

    function toString(){
        echo "Empty Template!!";
    }

    function addJSFile($new_path){
        $this->jsFile .= "<script language=\"javascript\" src=\"" . $new_path . "\"></script>\n";
    }//addJSFile

    function addCSSFile($new_path){
        $this->jsFile .= "<LINK href=\"" . $new_path . "\" rel=\"stylesheet\" type=\"text/css\">\n";
    }//addCSSFile

}//template
?>