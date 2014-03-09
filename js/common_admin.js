function BrowseServer( startupPath, functionData ){
	var finder = new CKFinder();
	finder.basePath = '../';
	finder.startupPath = startupPath;
	finder.selectActionFunction = SetFileField;
	finder.selectActionData = functionData;
	finder.selectThumbnailActionFunction = ShowThumbnails;
	finder.popup();
}

function SetFileField( fileUrl, data){
	document.getElementById( data["selectActionData"] ).value = fileUrl;
}

function selectFile(xField){
	var new_file = window.showModalDialog("filemanager.php", '', "dialogHeight:600px;dialogWidth:800px;help:no;scroll:no;status:no;dialogHide:yes;unadorned:yes");
	if(new_file){
		xField.value = new_file;
	}

}

function ShowThumbnails( fileUrl, data ){
	var sFileName = this.getSelectedFile().name;
	document.getElementById( 'thumbnails' ).innerHTML +=
			'<div class="thumb">' +
				'<img src="' + fileUrl + '" />' +
				'<div class="caption">' +
					'<a href="' + data["fileUrl"] + '" target="_blank">' + sFileName + '</a> (' + data["fileSize"] + 'KB)' +
				'</div>' +
			'</div>';

	document.getElementById( 'preview' ).style.display = "";
	return false;
}

function uploadImage(returnField, returnImage){
	var newImage = window.showModalDialog("upload_image.php",'',"dialogHeight:480px;dialogWidth:640px;help:no;scroll:no;status:no;dialogHide:yes;unadorned:yes");
	if (newImage){
		returnField.value = newImage;
		document[returnImage].src = newImage;
	}
}

function jumpPage(){
    iForm.pageno.value = pForm.pageno.value;
	Post(iForm);
}//jumpPage

function nextPage(){
    if (pForm.pageno.selectedIndex < pForm.pageno.options.length - 1){
        pForm.pageno.selectedIndex ++;
        jumpPage();
    }//if
}//nextPage

function prevPage(){
    if (pForm.pageno.selectedIndex > 0){
        pForm.pageno.selectedIndex--;
        jumpPage();
    }//if
}//prevPage

function checkAll(xField){
    if ((xField) && (xField.length)){
        for (var i=1; i<xField.length; i++){
            xField[i].checked = xField[0].checked;
        }//for
    }//if
}//checkAll

function setAscending(xURL, xScending){
	window.location.href = "set_ascending.php?usefor=" + xURL + "&content=" + xScending;
}
function setAscending2(xURL, xScending){
	window.location.href = "set_ascending_page.php?pageno=" + iForm.pageno.value + "&parent=" + iForm.parent.value + "&usefor=" + xURL + "&content=" + xScending;
}
function setAscending3(xURL, xUse, xScending){
	if(mForm.parent){
		window.location.href = "set_ascending_product2.php?parent=" + mForm.parent.value + "&pageno=" + mForm.pageno.value + "&catalog=" + mForm.catalog.value + "&mno=" + mForm.mno.value + "&url=" + xURL + "&usefor=" + xUse + "&content=" + xScending;
	}
	else{
		window.location.href = "set_ascending_product2.php?pageno=" + mForm.pageno.value + "&catalog=" + mForm.catalog.value + "&mno=" + mForm.mno.value + "&url=" + xURL + "&usefor=" + xUse + "&content=" + xScending;
	}
}
function Tabbing(){
	if(event.keyCode==13){event.keyCode=9;}
}



function getList(xField){
    var tStr = "";
    for (var i=1; i<xField.length; i++){
        if (xField[i].checked){
            tStr += xField[i].value + ",";
        }//if
    }//for
	if (tStr.length > 0){
		tStr = tStr.substring(0, tStr.length - 1);
	}//if
    return tStr;
}//getList

function formatCurrency(num) {
	num = num.toString().replace(/\$|\,/g,'');
	if(isNaN(num))
		num = "0";
	num = Math.floor(num*100+0.50000000001);
	num = Math.floor(num/100).toString();
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0,num.length-(4*i+3))+','+ num.substring(num.length-(4*i+3));
	return num;
}