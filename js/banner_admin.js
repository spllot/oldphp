/*
JavaScript for CITE
Author:			BN.Cho
Date Created:	2006/6/26
Last Update:	2006/6/26 by BN
Contact:		programmer@ms95.url.com.tw
Copyright 2006 (c) programmer@ms95.url.com.tw All Rights Reserved.
*/

var SELECTED = null;
function mOvr(x){
	if (x != SELECTED){
		x.style.background = "#F7EFBD";
		x.style.border = "solid 1px #EFE342";
	}//if
}//mOver

function mClk(x, newUrl, newWin){
	if ((x) && (x != SELECTED)){
		if (SELECTED){
			SELECTED.style.background = "";
			SELECTED.style.border = "solid 1px #CCCC99";
			SELECTED.style.color = "gray";
		}//if
		x.style.background = "#EFE342";
		x.style.border = "solid 1px #AAAAAA";
		x.style.color = "white";
		SELECTED = x;
	}//if
    var targetWin = eval(newWin);
    if (targetWin){
	    targetWin.location.href = 'display.php?module=' + newUrl;
    }//if
}//mClk

function mOut(x){
	if (x != SELECTED){
		x.style.background = "";
		x.style.border = "solid 1px #CCCC99";
		x.style.color = "gray";
	}//if
}//mOut
		