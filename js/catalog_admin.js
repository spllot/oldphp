/*
JavaScript for CITE
Author:			BN.Cho
Date Created:	2006/6/26
Last Update:	2006/6/26 by BN
Contact:		programmer@ms95.url.com.tw
Copyright 2006 (c) programmer@ms95.url.com.tw All Rights Reserved.
*/
var code = "catalog";
function Search(){
     iForm.action = "catalog.php";
     iForm.method = "get";
     iForm.submit();
}//Search

function Delete(){
    iForm.memberlist.value = getList();
    if (iForm.memberlist.value){
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
        iForm.mno.value = xNo;
        iForm.action = code + "_property.php";
        iForm.submit();
    }//if
}//Edit

function getList(){
    var tStr = "";
    for (var i=1; i<iForm.memberno.length; i++){
        if (iForm.memberno[i].checked){
            tStr += iForm.memberno[i].value + ",";
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
	iForm.action = "catalog_sort.php";
	iForm.itemno.value = xNo;
	iForm.sort.value = xDiff;
	iForm.submit();
}
function Resort(){
    var tStr = "";
    for (var i=1; i<iForm.memberno.length; i++){
        tStr += iForm.memberno[i].value + ",";
    }//for
	if (tStr.length > 0){
		tStr = tStr.substring(0, tStr.length - 1);
	}//if
	iForm.memberlist.value = tStr;
	iForm.action = code + "_resort.php";
	iForm.submit();
}