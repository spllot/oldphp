/*
JavaScript for CITE
Author:			BN.Cho
Date Created:	2006/6/26
Last Update:	2006/6/26 by BN
Contact:		programmer@ms95.url.com.tw
Copyright 2006 (c) programmer@ms95.url.com.tw All Rights Reserved.
*/
var code = "propose";
function Search(){
     mForm.action = "propose.php";
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

function Refuse(){
    mForm.memberlist.value = getList();
    if (mForm.memberlist.value){
        if (confirm("確定要退回所選項目?")){
            mForm.action = code + "_refuse.php";
            mForm.submit();
        }//if
    }//if
    else{
        alert("尚未選取!!");
    }//else
}//Delete
function Delete2(){
    mForm.memberlist.value = getList();
    if (mForm.memberlist.value){
        if (confirm("確定要下架所選項目?")){
            mForm.action = code + "_delete.php";
            mForm.submit();
        }//if
    }//if
    else{
        alert("尚未選取!!");
    }//else
}

function Approve(){
    mForm.memberlist.value = getList();
    if (mForm.memberlist.value){
        if (confirm("確定要通過所選項目?")){
            mForm.action = code + "_approve.php";
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
    mForm.pageno.value = pagging.pageno.value
    mForm.action = code + ".php";
    mForm.submit();
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

