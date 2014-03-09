function facebook_show_display(){
    
	var BSHTML = [];
    
	BSHTML[BSHTML.length] = '<span style="display:inline; margin:0; padding:0; background:none; float:none; border:0;"><img src="./images/icops_1.gif" width="53" height="24" alt="分享至"  style="padding:0; margin:0;border:0" />';
	/*Facebook分享*/
	
    BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '<a href="javascript: void(window.open(\'http://www.facebook.com/sharer.php?u=\'.concat(encodeURIComponent(pro_url)) .concat(\'&amp;t=\') .concat(encodeURIComponent(pro_name)),\'sharer\',\'toolbar=0,status=0,width=626,height=436\'));"><img src="http://www1.ichannels.com.tw/images/icops_2.gif" alt="Facebook" width="21" height="24" border="0"  style="padding:0; margin:0; border:0"/></a>';
	BSHTML[BSHTML.length] = '';
	/*Twitter分享*/
	BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '<a href="javascript: void(window.open(\'http://www.plurk.com/?qualifier=shares&amp;status=\' .concat(encodeURIComponent(pro_url)) .concat(\' \') .concat(\'&#40;\') .concat(encodeURIComponent(pro_name)) .concat(\'&#41;\')));"><img src="http://www1.ichannels.com.tw/images/icops_3.gif" alt="Plurk" width="21" height="24" border="0"  style="padding:0; margin:0; border:0"/></a>';
	BSHTML[BSHTML.length] = '';
	/*Plur分享*/
	BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '<a href="javascript: void(window.open(\'http://twitter.com/home/?status=\'.concat(encodeURIComponent(pro_name)) .concat(\' \') .concat(encodeURIComponent(pro_url))));"><img src="http://www1.ichannels.com.tw/images/icops_4.gif" alt="Twitter" width="31" height="24" border="0" style="padding:0; margin:0;border:0" /></a>';
	BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '</span>';
	
	document.write(BSHTML.join(''));
} 

function facebook_show_display1(){
    
	var BSHTML = [];
    
	BSHTML[BSHTML.length] = '<span style="display:inline; margin:0; padding:0; background:none; float:none; border:0;"><img src="./images/icops_1.gif" width="53" height="24" alt="分享至"  style="padding:0; margin:0;border:0" />';
	/*Facebook分享*/
	
    BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '<a href="javascript: void(window.open(\'http://www.facebook.com/sharer.php?u=\'.concat(encodeURIComponent(pro_url)) .concat(\'&amp;t=\') .concat(encodeURIComponent(pro_name)),\'sharer\',\'toolbar=0,status=0,width=626,height=436\'));"><img src="http://www1.ichannels.com.tw/images/icops_2.gif" alt="Facebook" width="21" height="24" border="0"  style="padding:0; margin:0; border:0"/></a>';
	BSHTML[BSHTML.length] = '';
	/*Twitter分享*/
	BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '<a href="javascript: void(window.open(\'http://www.plurk.com/?qualifier=shares&amp;status=\' .concat(encodeURIComponent(pro_url)) .concat(\' \') .concat(\'&#40;\') .concat(encodeURIComponent(pro_name)) .concat(\'&#41;\')));"><img src="http://www1.ichannels.com.tw/images/icops_3.gif" alt="Plurk" width="21" height="24" border="0"  style="padding:0; margin:0; border:0"/></a>';
	BSHTML[BSHTML.length] = '';
	/*Plur分享*/
	BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '<a href="javascript: void(window.open(\'http://twitter.com/home/?status=\'.concat(encodeURIComponent(pro_name)) .concat(\' \') .concat(encodeURIComponent(pro_url))));"><img src="./images/icops_4-1.gif" alt="Twitter" width="20" height="24" border="0" style="padding:0; margin:0;border:0" /></a>';
	BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '</span>';
	/*Plur分享*/
	BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '<a href="javascript: void(0)"><img src="./images/icops_6.gif" alt="Twitter" width="31" height="24" border="0" style="padding:0; margin:0;border:0" /></a>';
	BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '</span>';
	
	document.write(BSHTML.join(''));
} 

function facebook_show_image(){
    
	var BSHTML = [];
    
	BSHTML[BSHTML.length] = '<span style="display:inline; margin:0; padding:0; background:none; float:none; border:0;"><img src="./images/icops_1.gif" width="53" height="24" alt="分享至"  style="padding:0; margin:0;border:0" />';
	/*Facebook分享*/
	
    BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '<img src="http://www1.ichannels.com.tw/images/icops_2.gif" alt="Facebook" width="21" height="24" border="0"  style="padding:0; margin:0; border:0"/>';
	BSHTML[BSHTML.length] = '';
	/*Twitter分享*/
	BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '<img src="http://www1.ichannels.com.tw/images/icops_3.gif" alt="Plurk" width="21" height="24" border="0"  style="padding:0; margin:0; border:0"/>';
	BSHTML[BSHTML.length] = '';
	/*Plur分享*/
	BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '<img src="http://www1.ichannels.com.tw/images/icops_4.gif" alt="Twitter" width="31" height="24" border="0" style="padding:0; margin:0;border:0" />';
	BSHTML[BSHTML.length] = '';
	BSHTML[BSHTML.length] = '</span>';
	
	document.write(BSHTML.join(''));
} 