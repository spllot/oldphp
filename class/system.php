<?php
class Module{
    var $status						= 0;
    var $buyer_msg					= 1;
    var $seller_msg					= 2;
    var $group						= 3;
    var $user						= 4;
    var $permission					= 5;
    var $ip							= 6;
    var $catalog					= 7;
    var $setting					= 8;
    var $page						= 9;
    var $marquee					= 10;
    var $ad							= 11;
	var $search						= 12;
	var $member						= 13;
	var $export						= 14;
	var $templates					= 15;
	var $sendmail					= 16;
	var $sendmsg					= 17;
	var $propose					= 18;
	var $code_view					= 19;
	var $ship_view					= 20;
    var $account_income				= 21;
    var $account_refund				= 22;
    var $account_earn				= 23;
    var $seller_export				= 24;
    var $buyer_export				= 25;
    var $seller_receipt				= 26;
	var $buyer_receipt				= 27;
	var $sponsor					= 28;
	var $bonus						= 29;
	var $donate						= 30;
	var $welcome					= 31;
	var $benefit					= 32;
	var $epaper						= 33;
	var $ad2						= 34;
	var $queue						= 35;
	var $blog						= 36;
	var $log_transaction			= 37;
	var $log_export					= 38;
	var $log_payment				= 39;
	var $log_export2				= 40;
	var $earn_sms					= 41;
	var $news						= 42;
	var $modules = array(
		0=> array("網站資訊狀態", "status.php"),
		1=> array("前台問題回覆", "contact.php"),
		2=> array("後台問題回覆", "seller_msg.php"),
		3=> array("管理群組維護", "group.php"),
		4=> array("管理人員維護", "user.php"), 
		5=> array("管理權限維護", "permission.php"), 
		6=> array("IP鎖定設定", "ip.php"),
		7=> array("分類維護", "catalog.php"), 
		8=> array("網站設定", "setting.php"),
		9=> array("文案與專案管理", "page.php"),
		10=> array("跑馬燈", "marquee.php"),
		11=> array("右側廣告管理", "ad.php"),
		12=> array("會員資訊內容", "search.php"),
		13=> array("分類搜尋", "member.php"),
		14=> array("資訊匯出管理", "export.php"),
		15=> array("短信範本匣編輯", "templates.php"),
		16=> array("站外資訊編輯器", "sendmail.php"),
		17=> array("站內資訊編輯器", "sendmsg.php"),
		18=> array("賣家提案審核", "propose.php"),
		19=> array("到店憑證消費檢視", "code_view.php"),
		20=> array("宅配商品消費檢視", "ship_view.php"),
		21=> array("金流請款處理", "account_income.php"),
		22=> array("金流退款處理", "account_refund.php"),
		23=> array("營業利潤處理", "account_earn.php"),
		24=> array("匯款&發票處理", "seller_export.php"),
		25=> array("優惠簡訊發票處理", "buyer_export.php"),
		26=> array("發票編號管理", "seller_receipt.php"),
		27=> array("買家發票管理", "buyer_receipt.php"),
		28=> array("傳銷點數管理", "sponsor.php"),
		29=> array("紅利管理", "bonus.php"),
		30=> array("愛心帳號維護", "donate.php"),
		31=> array("首頁訊息設定", "welcome.php"),
		32=> array("好康訊息發送", "benefit.php"),
		33=> array("電子報發送", "epaper.php"),
		34=> array("下方廣告管理", "ad2.php"),
		35=> array("郵件發送排程", "queue.php"),
		36=> array("徵求文章審核", "blog.php"),
		37=> array("儲值金查詢", "log_transaction.php"),
		38=> array("儲值金匯出處理", "log_export.php"),
		39=> array("儲值金購買處理", "log_payment.php"),
		40=> array("儲值金匯出紀錄", "log_export2.php"),
		41=> array("簡訊利潤管理", "earn_sms.php"),
		42=> array("最新消息管理", "news.php"),
	);

    var $apps = array(
        'misc'				=> array("綜合管理", array(0, 1, 2)),
        'system'			=> array("系統管理", array(3, 4, 5, 6, 7)),
        'web'				=> array("網站管理", array(8, 42, 9, 10, 11, 34, 31, 32, 33, 35)),
        'member'			=> array("會員資料", array(13, 12, 14)),
        'sms'				=> array("短信收發", array(15, 16, 17)),
        'product'			=> array("商品審核", array(30, 18, 19, 20, 36)),
        'income'		    => array("帳戶收支", array(39, 37, 21, 22, 23)),
        'outcome'			=> array("帳款匯出", array(38, 40, 24, 25)),
		'receipt'			=> array("發票管理", array(26, 27)),
		'bonus'				=> array("點數紅利", array(28, 29, 41))
	);

    function nameOf($new_module){
        return $this->modules[$new_module][0];
    }//nameOf

    function getSize(){
        return sizeof($this->modules);
    }//getSize

    function toCheckList($new_permit){
        $tStr = "";
        $tStr .= "<table>\n";
        $module_list = "";
        foreach($this->apps as $app_id => $app_info){
            $tStr .= "<tr><td><input type=\"checkbox\" name=\"$app_id\" value=\"\" onClick=\"checkAll('$app_id');\"></td><th align=\"left\">$app_info[0]</th></tr>\n";
            $tStr .= "<tr><td></td><td><table>\n";
            foreach($app_info[1] as $module_id){
                $tStr .= "<tr><td>\n";
                $tStr .= "<input type=\"checkbox\" name=\"$app_id\" value=\"$module_id\"";
                if (substr($new_permit, $module_id, 1) == "1"){
                    $tStr .= " CHECKED";
                }//if
                else{
                    $tStr .= " TEST";
                }//else
                $tStr .= ">" . $this->modules[$module_id][0];
                $tStr .= "</td></tr>\n";
            }//foreach
            $module_list .= $app_id . ",";
            $tStr .= "</table></td></tr>\n";
        }//foreach
        $tStr .= "<input type=\"hidden\" name=\"modulelist\" value=\"$module_list\">\n";
        $tStr .= "</table>\n";
        return $tStr;
    }//toCheckList

    function toMenu(){
        $tStr = "";
        $tStr .= "<table>\n";
        $tStr .= "  <tr>\n";
        foreach($this->apps as $app_id => $app_name){
            $tStr .= "      <td class=\"toolbar\" onClick=\"mClk(this, '$app_id', 'parent.display');\" onMouseOver=\"mOvr(this);\" onMouseOut=\"mOut(this);\">$app_name[0]</td>\n";
        }//foreach
        $tStr .= "  </tr>\n";
        $tStr .= "</table>\n";
        return $tStr;
    }//toMenu

    function toSubMenu($new_menu, $new_permit, $new_user){
        $tStr = "";
        if ($this->apps[$new_menu]){
    		$tStr .= "<table class=\"menu\" cellpadding=\"2\" cellspacing=\"2\">\n";
            $i = 0;
            foreach($this->apps[$new_menu][1] as $app_url){
                if ($new_user == "programmer" || strpos($new_permit, $this->modules[$app_url][1]) > -1){
                    $i ++;
        		    $tStr .= "<tr>";
        			$tStr .= "<td class=\"item\" onClick=\"mClk(this,'" . $this->modules[$app_url][1] . "','parent.content');\" onMouseOver=\"mOvr(this);\" onMouseOut=\"mOut(this);\">" . $this->modules[$app_url][0] . "</td>";
                    $tStr .= "</tr>\n";
                }//if
            }//for
            if ($i == 0){
        	    $tStr .= "<tr>";
        		$tStr .= "<td class=\"noitem\">權限不足!!<br>無可用模組</td>";
                $tStr .= "</tr>\n";
            }//if
           	$tStr .= "</table>\n";
        }//if
        return $tStr;
    }//toSubMenu
}//Module

class Permission{
	function hasPermission($xID, $xPermit, $xUrl){
		$xPermit = "=" . $xPermit;
		if($xID == "programmer"){
			return true;
		}
		else{
			$xUrl = "," . $xUrl . ",";
			return strrpos($xPermit, $xUrl);
		}
	}
    function hasPermissionOf($new_instance, $new_permit){
        if (substr($new_permit, $new_instance, 1) == "1"){
            return true;
        }//if
        return false;
    }//hasPermissionOf

    function permitOf($new_size, $new_list){
        $new_list = "," . $new_list . ",";
        $permit = "";
        for ($i = 0; $i < $new_size; $i++){
            $permit .= (strpos($new_list, "," . $i . ",") > -1) ? "1" : "0";
        }//for
        return $permit;
    }//permitOf

    function getPermit($new_size, $new_seed){
        $permit = "";
        for ($i=0; $i < $new_size; $i++){
            $permit .= $new_seed;
        }//for
        return $permit;
    }//getPermit
}//Permission

class User{
    var $adminID = "programmer";
    var $adminName = "系統管理員";
    function isAdmin($new_userid){
        return (strtolower($new_userid) == $this->adminID) ? true : false;
    }//isAdmin
}//User
$_USER = new User();
$_MODULE = new Module();
?>