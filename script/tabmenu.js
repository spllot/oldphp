
function selectTab1(){
	document.getElementById('contentsA').style.display = "block";
	document.getElementById('contentsB').style.display = "none";
	document.getElementById('contentsC').style.display = "none";
	document.getElementById('jsCall_ID1').className = "tabOn";
	document.getElementById('jsCall_ID2').className = "tabOff";
	document.getElementById('jsCall_ID3').className = "tabOff";
}

function selectTab2(){
	document.getElementById('contentsA').style.display = "none";
	document.getElementById('contentsB').style.display = "block";
	document.getElementById('contentsC').style.display = "none";
	document.getElementById('jsCall_ID1').className = "tabOff";
	document.getElementById('jsCall_ID2').className = "tabOn";
	document.getElementById('jsCall_ID3').className = "tabOff";
}

function selectTab3(){
	document.getElementById('contentsA').style.display = "none";
	document.getElementById('contentsB').style.display = "none";
	document.getElementById('contentsC').style.display = "block";
	document.getElementById('jsCall_ID1').className = "tabOff";
	document.getElementById('jsCall_ID2').className = "tabOff";
	document.getElementById('jsCall_ID3').className = "tabOn";
}