<script language="javascript">
var nodeAry = new Array();
var Parent = new Array();
var Root = "";

function Node(No, Value, Caption, parentNo){
	this.No = No;
	this.Value = Value;
	this.Caption = Caption;
	this.parentNo = parentNo;
	this.Path = setPath(No,parentNo);
	this.pathAry = this.Path.explode("-");
	this.Order = 0;
}//Node

function addNode(No, Value, Caption, parentNo){
	nodeAry[No] = new Node(No, Value, Caption, parentNo);
	if(!Parent[parentNo])
		Parent[parentNo] = new Array();
	Parent[parentNo][Parent[parentNo].length] = No;
	nodeAry[No].Order = Parent[parentNo].length;
}//addNode

function setRoot(No,Value, Caption){
	Root = No;
	nodeAry[No] = new Node(No, Value, Caption, No);
}//setRoot

function setPath(No,parentNo){
	if (No == parentNo)
		return '';
	else if (nodeAry[parentNo]){
		if (nodeAry[parentNo].Path.length == 0)
			return parentNo;
		else
			return nodeAry[parentNo].Path + "-" + parentNo;
	}//else
	return Root;
}//setPath

function getPath(No){
	if (nodeAry[No]){
		return nodeAry[No].Path;
	}//if
	return Root;
}//getPath

function getParentNo(No){
	if (nodeAry[No]){
		return nodeAry[No].parentNo;
	}//if
}//getParent

function getCaption(x){
	var tStr = "";
	for (var i=0 ;i<x.pathAry.length ;i++ ){
		if (i == (x.pathAry.length - 1)){
			if (x.Order == Parent[x.parentNo].length){tStr += "└";}//if
			else{tStr += "├";}//else
		}//if
		else {
			if (Parent[x.pathAry[i]].length > nodeAry[x.pathAry[i + 1]].Order){tStr += "│";}//if
			else{tStr += "　";}//else
		}//else
	}//for
	tStr += "&nbsp;" + x.Caption ;
	return tStr;
}//getCaption

function nodeList(parentNo,catSelected){
	var tStr = "";
	var myNode = null;
	if (Parent[parentNo]){
		for (var i=0;i<Parent[parentNo].length ;i++ ){
			myNode = nodeAry[Parent[parentNo][i]];
			tStr += "<option value=\"" + myNode.Value + "\"";
			if (myNode.No == catSelected)
				tStr += " SELECTED";
			tStr += ">" + getCaption(myNode) + "</option>\n";
			tStr += nodeList(myNode.No,catSelected);
		}//for
	}//if
	myNode = null;
	return tStr;
}//nodeList

function getChildList(parentNo,x){
	x.options.length = 0;
	if (Parent[parentNo]){
		for (var i=0;i<Parent[parentNo].length ;i++ ){
			x.options.length = i + 1;
			x.options[i].text = nodeAry[Parent[parentNo][i]].Caption;
			x.options[i].value = nodeAry[Parent[parentNo][i]].No;
		}//for
	}//if
}//getChildList
</script>
