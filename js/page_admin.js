
var code = "page";

function Delete(){
    iForm.itemlist.value = getList();
    if (iForm.itemlist.value){
        if (confirm("確定要刪除所選項目?")){
            iForm.action = code + "_delete.php";
            iForm.submit();
        }//if
    }//if
    else{
        alert("尚未選取!!");
    }//else
}//Delete

function New(){
    iForm.action = code + "_property.php";
    iForm.submit();
}//New

function Edit(xNo){
    if(xNo){
        iForm.itemno.value = xNo;
        iForm.action = code + "_property.php";
        iForm.submit();
    }//if
}//Edit

function getList(){
    var tStr = "";
    for (var i=1; i<iForm.no.length; i++){
        if (iForm.no[i].checked){
            tStr += iForm.no[i].value + ",";
        }//if
    }//for
	if (tStr.length > 0){
		tStr = tStr.substring(0, tStr.length - 1);
	}//if
    return tStr;
}//getList

function jumpPage(){
    iForm.pageno.value = pagging.pageno.value
    iForm.action = code + ".php";
    iForm.submit();
}//jumpPage

function nextPage(){
    if (pagging.pageno.selectedIndex < pagging.pageno.options.length - 1){
        pagging.pageno.selectedIndex ++;
        jumpPage();
    }//if
}//nextPage

function prevPage(){
    if (pagging.pageno.selectedIndex > 0){
        pagging.pageno.selectedIndex--;
        jumpPage();
    }//if
}//prevPage

function gSort(xNo, xDiff){
	iForm.action = "page_sort.php";
	iForm.itemno.value = xNo;
	iForm.sort.value = xDiff;
	iForm.submit();
}

function setParent(xNo){
	iForm.parent.value = xNo;
	iForm.action = code + ".php";
	iForm.submit();
}
function Resort(){
    var tStr = "";
    for (var i=1; i<iForm.no.length; i++){
        tStr += iForm.no[i].value + ",";
    }//for
	if (tStr.length > 0){
		tStr = tStr.substring(0, tStr.length - 1);
	}//if
	iForm.itemlist.value = tStr;
	iForm.action = code + "_resort.php";
	iForm.submit();
}