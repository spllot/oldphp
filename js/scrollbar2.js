var __scrollBar2Control2  = null;
var __divinnerHTML2 = null;
var __firstTime2 = true;
function innerBarProp2(barID, width, height, interval, direction)
{
　this.barID = barID;
　this.width = width;
　this.height = height;
　this.interval = interval;
　this.direction = direction;
　this.stopScroll = false;
　this.maxValue = 0;
　this.preValue = 0;
}
function scrollBar2()
{
　this.barsArray = new Array();
　__scrollBar2Control2 = this;
}

scrollBar2.prototype.addBar = function(barID, width, height, interval, direction){
　var paraCount = arguments.length;
　if ( paraCount < 1 )
　{
　　alert("parameters count incorect!");
　　return;
　}

　if ( typeof( width ) == "undefined" )
　{
　　var width = 100;
　}

　if ( typeof( height ) == "undefined" )
　{
　　var height = 100;
　}

　if ( typeof( interval ) == "undefined" )
　{
　　var interval = 1000;
　}
　
　if ( typeof( direction ) == "undefined" )
　{
　　var direction = "up";
　}

　var barProp = new innerBarProp2(barID, width, height, interval, direction);
　var objBar = document.getElementById(barID);
　if(__divinnerHTML2!=null)
　　objBar.innerHTML=__divinnerHTML2;
　else
　　__divinnerHTML2=objBar.innerHTML;
　var barCount = this.barsArray.length;
　this.barsArray[barCount] = barProp;
}
scrollBar2.prototype.clear = function(){
　for(i=0;i<this.barsArray.length;i++)
　　this.barsArray.pop();
}
scrollBar2.prototype.createScrollBars = function(){
　var barCount = this.barsArray.length;
　if ( barCount == 0 )
　{
　　return;
　}

　for ( var i=0; i<barCount; i++ )
　{
　　var objBarID = this.barsArray[i].barID;
　　if ( typeof( objBarID ) == "string" )
　　{
　　　var objBar = document.getElementById( objBarID ); 
　　　if (objBar == null)
　　　{
　　　　if ( document.readyState == "complete" || document.readyState == "loaded" )
　　　　{
　　　　　alert("ScrollBar[" + objBarID + "]: not exist!");
　　　　　return;
　　　　}
　　　　else
　　　　{
　　　　　window.setTimeout("__scrollBar2Control2.createScrollBars()",50);
　　　　　return;
　　　　}
　　　}
　　　this.barsArray[i].barID = objBar;
　　}
　}

　for ( var i=0; i<barCount; i++ )
　{
　　this.innerInitBar(i);
　}
}
scrollBar2.prototype.innerInitBar = function (index)
{ 
　var barID = this.barsArray[index].barID;
　var width = this.barsArray[index].width;
　var height = this.barsArray[index].height;
　var interval = this.barsArray[index].interval;
　var direction = this.barsArray[index].direction;
　var maxValue = 0;

　with(barID)
　{
　　style.width = width;
　　style.height = height;
　　noWrap=true;
　　switch( direction )
　　{
　　　case "up":
　　　　maxValue = Math.max(scrollHeight, height);
　　　　style.overflowX = "visible";
　　　　style.overflowY = "hidden";
　　　　var barHtml = innerHTML;
　　　　var newHtml = "<table border='0' cellspacing='0' cellpadding='0'>\n";
　　　　newHtml += " <tr height='20'>\n";
　　　　newHtml += " <td> \n";
　　　　newHtml += " </td>\n";
　　　　newHtml += " </tr>\n";
　　　　newHtml += " <tr>\n";
　　　　newHtml += " <td height='" + maxValue + "' valign='top'>\n";
　　　　newHtml += barHtml + "\n";
　　　　newHtml += " </td>\n";
　　　　newHtml += " </tr>\n";
　　　　newHtml += " <tr>\n";
　　　　newHtml += " <td height='" + maxValue + "' valign='top'>\n";
　　　　newHtml += barHtml + "\n";
　　　　newHtml += " </td>\n";
　　　　newHtml += " </tr>\n";
　　　　newHtml += " <tr>\n";
　　　　newHtml += " <td height='" + maxValue + "' valign='top'>\n";
　　　　newHtml += barHtml + "\n";
　　　　newHtml += " </td>\n";
　　　　newHtml += " </tr>\n";
　　　　newHtml += "</table>\n";
　　　　innerHTML = newHtml;
　　　　break;
　　　case "down":
　　　　maxValue = Math.max(scrollHeight, height);
　　　　style.overflowX = "visible";
　　　　style.overflowY = "hidden";
　　　　var barHtml = innerHTML;
　　　　var newHtml = "<table border='0' cellspacing='0' cellpadding='0'>\n";
　　　　newHtml += " <tr>\n";
　　　　newHtml += " <td height='" + maxValue + "' valign='top'>\n";
　　　　newHtml += barHtml + "\n";
　　　　newHtml += " </td>\n";
　　　　newHtml += " </tr>\n";
　　　　newHtml += " <tr>\n";
　　　　newHtml += " <td height='" + maxValue + "' valign='top'>\n";
　　　　newHtml += barHtml + "\n";
　　　　newHtml += " </td>\n";
　　　　newHtml += " </tr>\n";
　　　　newHtml += "</table>\n";
　　　　innerHTML = newHtml;
　　　　scrollTop = maxValue;
　　　　break;
　　　case "left":
　　　　maxValue = Math.max(scrollWidth, width);
　　　　style.overflowX = "hidden";
　　　　style.overflowY = "visible";
　　　　var barHtml = barID.innerHTML;
　　　　var newHtml = "<table border='0' cellspacing='0' cellpadding='0' width='" + (maxValue * 2 ) + "'>\n";
　　　　newHtml += " <tr>\n";
　　　　newHtml += " <td width='" + maxValue + "' valign='top'>\n";
　　　　newHtml += barHtml + "\n";
　　　　newHtml += " </td>\n";
　　　　newHtml += " <td width='" + maxValue + "' valign='top'>\n";
　　　　newHtml += barHtml + "\n";
　　　　newHtml += " </td>\n";
　　　　newHtml += " </tr>\n";
　　　　newHtml += "</table>\n";
　　　　innerHTML = newHtml;
　　　　break;
　　　case "right":
　　　　maxValue = Math.max(scrollWidth, width);
　　　　style.overflowX = "hidden";
　　　　style.overflowY = "visible";
　　　　var barHtml = innerHTML;
　　　　var newHtml = "<table border='0' cellspacing='0' cellpadding='0' width='" + (maxValue * 2 ) + "'>\n";
　　　　newHtml += " <tr>\n";
　　　　newHtml += " <td width='" + maxValue + "' valign='top'>\n";
　　　　newHtml += barHtml + "\n";
　　　　newHtml += " </td>\n";
　　　　newHtml += " <td width='" + maxValue + "' valign='top'>\n";
　　　　newHtml += barHtml + "\n";
　　　　newHtml += " </td>\n";
　　　　newHtml += " </tr>\n";
　　　　newHtml += "</table>\n";
　　　　innerHTML = newHtml;
　　　　scrollLeft = maxValue;
　　　　break;
　　　default:
　　　　alert("ScrollBar[" + id + "]: direction is incorect!");
　　　　return;
　　}

　　onmouseover = new Function("__scrollBar2Control2.mouseEvt(" + index + ",true);");
　　onmouseout = new Function("__scrollBar2Control2.mouseEvt(" + index + ",false);");
　　if(__firstTime2)
　　{
　　　__firstTime2=false;
　　　window.setInterval("__scrollBar2Control2.scroll(" + index + ");",interval);
　　}
　　this.barsArray[index].maxValue = maxValue;
　}
}
scrollBar2.prototype.mouseEvt = function(index, stop){
　this.barsArray[index].stopScroll = stop;
}
scrollBar2.prototype.scroll = function(index){
　var barID = this.barsArray[index].barID;
　var width = this.barsArray[index].width;
　var height = this.barsArray[index].height;
　var interval = this.barsArray[index].interval;
　var direction = this.barsArray[index].direction;
　var stopScroll = this.barsArray[index].stopScroll;
　var preValue = this.barsArray[index].preValue;
　var maxValue = this.barsArray[index].maxValue;

　if ( stopScroll == true ) return;

　switch(direction)
　{
　　case "up":
　　　preValue++;
　　　if ( preValue >= maxValue )
　　　{
　　　　preValue = 0;
　　　}
　　　barID.scrollTop = preValue;
　　　break;
　　case "down":
　　　preValue--;
　　　if ( preValue <= 0 )
　　　{
　　　　preValue = maxValue;
　　　}
　　　barID.scrollTop = preValue;
　　　break;
　　case "left":
　　　preValue++;
　　　if ( preValue >= maxValue )
　　　{
　　　　preValue = 0;
　　　}
　　　barID.scrollLeft = preValue;
　　　break;
　　case "right":
　　　preValue--;
　　　if ( preValue <=0 )
　　　{
　　　　preValue = maxValue;
　　　}
　　　barID.scrollLeft = preValue;
　　　break;
　}
　this.barsArray[index].preValue = preValue;
}
