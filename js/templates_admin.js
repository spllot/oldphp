/*
JavaScript for CITE
Author:			BN.Cho
Date Created:	2006/6/26
Last Update:	2006/6/26 by BN
Contact:		programmer@ms95.url.com.tw
Copyright 2006 (c) programmer@ms95.url.com.tw All Rights Reserved.
*/
var code = "templates";
function Search(){
     mForm.action = "templates.php";
     mForm.method = "get";
     mForm.submit();
}//Search

function Delete(){
    mForm.memberlist.value = getList();
    if (mForm.memberlist.value){
        if (confirm("確定要刪除所選項目?")){
            mForm.action = code + "_delete.php";
            mForm.submit();
        }//if
    }//if
    else{
        alert("尚未選取!!");
    }//else
}//Delete

function New(){
    mForm.action = code + "_property.php";
    mForm.submit();
}//New

function Edit(xNo){
    if(xNo){
        mForm.mno.value = xNo;
        mForm.action = code + "_property.php";
        mForm.submit();
    }//if
}//Edit

function getList(){
    var tStr = "";
    for (var i=1; i<mForm.memberno.length; i++){
        if (mForm.memberno[i].checked){
            tStr += mForm.memberno[i].value + ",";
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

function setPermission(xNo, xPermit){
    var Permit = window.showModalDialog(code + "_permission.php?permit=" + xPermit, "", "status:no");
    if (Permit != "-1"){
        mForm.mno.value = xNo;
        mForm.memberlist.value = Permit;
        mForm.action = code + "_permission_save.php";
        mForm.submit();
    }//if
}//setPermission

function gSort(xNo, xDiff){
	mForm.action = "group_sort.php";
	mForm.itemno.value = xNo;
	mForm.sort.value = xDiff;
	mForm.submit();
}

function Resort(){
    var tStr = "";
    for (var i=1; i<mForm.memberno.length; i++){
        tStr += mForm.memberno[i].value + ",";
    }//for
	if (tStr.length > 0){
		tStr = tStr.substring(0, tStr.length - 1);
	}//if
	mForm.memberlist.value = tStr;
	mForm.action = code + "_resort.php";
	mForm.submit();
}