function checkModule(xNo){
	var m = "module_" + xNo + "_";
	for(var i=0; i<iForm.length; i++){
		if(iForm[i].name.indexOf(m) == 0){
			iForm[i].checked = eval("iForm.system_" + xNo).checked;
		}
	}
}

function Save(){
	iForm.itemlist.value = "";
	for(var i=0; i<iForm.length; i++){
		var m = "module_";
		if(iForm[i].name.indexOf(m) == 0 && iForm[i].checked){
			iForm.itemlist.value += iForm[i].value + ",";
		}
	}
	if(iForm.itemlist.value.length > 0){
		iForm.itemlist.value = iForm.itemlist.value.substring(0, iForm.itemlist.value.length - 1);
	}
	iForm.target = "iAction";
	iForm.action = "permission_save.php";
	iForm.submit();
}