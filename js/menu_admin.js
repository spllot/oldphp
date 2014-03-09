/*
JavaScript for CITE
Author:			BN.Cho
Date Created:	2006/6/26
Last Update:	2006/6/26 by BN
Contact:		programmer@ms95.url.com.tw
Copyright 2006 (c) programmer@ms95.url.com.tw All Rights Reserved.
*/
var SELECTED = null;
var MENU = new Array();
function Menu(mCaption, mURL){
    this.Caption = mCaption;
    this.URL = mURL;
}//Menu

function setMenu(){
//   addMenu("member", "等級維護", "content.php");
    addMenu("system", "使用者管理", "user.php");
    addMenu("system", "權限管理", "permission.php");
    addMenu("member", "會員維護", "member.php");
    addMenu("epaper", "電子報", "epaper.php");
    addMenu("epaper", "訂閱名單", "subscribe.php");
    addMenu("product", "分類維護", "product_cat.php");
    addMenu("product", "商品維護", "product.php");
    addMenu("product", "評價維護", "product_review.php");
    addMenu("order", "新進訂單", "order.php?status=0");
    addMenu("order", "等待付款", "order.php?status=1");
    addMenu("order", "等待出貨", "order.php?status=2");
    addMenu("order", "退貨處理", "order.php?status=3");
//    addMenu("order", "訂單查詢", "order_search.php");
    addMenu("news", "公告維護", "news.php");
    addMenu("forum", "討論維護", "forum.php");
    addMenu("forum", "回覆管理", "reply.php");
    addMenu("service", "新進信件", "content.php");
    addMenu("service", "查詢", "content.php");
    addMenu("service", "線上發信", "service_send.php");
    addMenu("statistics", "訂單統計", "content.php");
    addMenu("statistics", "銷售統計", "content.php");
}//setMenu

function addMenu(xArea, xCaption, xURL){
    if (!MENU[xArea]){
        MENU[xArea] = new Array();
    }//if
    MENU[xArea][MENU[xArea].length] = new Menu(xCaption, xURL);
}//addMenu

function showMenu(xArea){
    if (MENU[xArea]){
		document.write("<table class=\"menu\" cellpadding=\"2\" cellspacing=\"2\">\n");
        for (var i=0; i<MENU[xArea].length; i++){
		    document.write("<tr>");
			document.write("<td class=\"item\" onClick=\"mClk(this,'" + MENU[xArea][i].URL + "','parent.content');\" onMouseOver=\"mOvr(this);\" onMouseOut=\"mOut(this);\">" + MENU[xArea][i].Caption + "</td>");
            document.write("</tr>\n")
        }//for
       	document.write("</table>\n");
    }//if
}//showMenu

function mOvr(x){
	if (x != SELECTED){
		x.style.background = "#CCFFFF";
		x.style.border = "solid 1px #3366CC";
	}//if
}//mOver

function mClk(x, newUrl, newWin){
	if ((x) && (x != SELECTED)){
		if (SELECTED){
			SELECTED.style.background = "";
			SELECTED.style.border = "solid 1px #33CCCC";
			SELECTED.style.color = "gray";
		}//if
		x.style.background = "#0066FF";
		x.style.border = "solid 1px #33CCCC";
		x.style.color = "white";
		SELECTED = x;
	}//if
	eval(newWin).location.href = newUrl;
}//mClk

function mOut(x){
	if (x != SELECTED){
		x.style.background = "";
		x.style.border = "solid 1px #33CCCC";
		x.style.color = "gray";
	}//if
}//mOut