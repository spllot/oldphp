<?php
class Page{
    var $cssFile = "";
    var $jsFile = "";
    var $Title = "未命名";
    var $menu = "";
    var $content = "";

    function addCSSFile($new_path){
        $this->cssFile .= "<LINK href=\"" . $new_path . "\" rel=\"stylesheet\" type=\"text/css\">\n";
    }//addCSSFile

    function addJSFile($new_path){
        $this->jsFile .= "<script language=\"javascript\" src=\"" . $new_path . "\"></script>\n";
    }//addJSFile

    function setTitle($new_title){
        $this->Title = $new_title;
    }//setTitle

    function addMenu($menu){
        $this->menu .= $menu;
    }//addMenu

    function addMenuFile($menuFileName){
        $this->menu .= $this->readFromFile($menuFileName)."\n";
    }//addMenuFile

    function addContent($content){
        $this->content .= $content."\n";
    }//addContent

    function addContentFile($contentFileName){
        $this->content .= $this->readFromFile($contentFileName)."\n";
    }//addContentFile

    function readFromFile($fileName){
        $file = "找不到檔案：\"$fileName\"!!";
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
    }//toString
}//template
?>