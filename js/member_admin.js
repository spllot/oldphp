function addNew(){
	if(mForm.id.value){
		mForm.action = "member_save.php";
		mForm.method = "post";
		mForm.submit();
	}
	else{
		alert("請輸入新增帳號");
	}
}
function Search(){
     mForm.action = "member.php";
     mForm.method = "get";
     mForm.submit();
}//Search


function Delete(){
    mForm.memberlist.value = getList();
    if (mForm.memberlist.value){
        if (confirm("確定要刪除所選項目?")){
            mForm.action = "member_delete.php";
            mForm.submit();
        }//if
    }//if
    else{
        alert("尚未選取!!");
    }//else
}//Delete

function Edit(xNo){
    if(xNo){
        mForm.mno.value = xNo;
        mForm.action = "member_property.php";
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
    mForm.action = "member.php";
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