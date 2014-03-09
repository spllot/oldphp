function County(new_name, new_areas){
	this.name = new_name;
	this.areas = new_areas;
}

function Area(new_zipcode, new_name){
	this.zipcode = new_zipcode;
	this.name = new_name;
}

var Counties = new Array(
	new County("基隆市", new Array(
							new Area('200', '仁愛區'),
							new Area('201', '信義區'),
							new Area('202', '中正區'),
							new Area('203', '中山區'),
							new Area('204', '安樂區'),
							new Area('205', '暖暖區'),
							new Area('206', '七堵區')
							)
				), 
	new County("台北市", new Array(
							new Area('100', '中正區'), 
							new Area('103', '大同區'), 
							new Area('104', '中山區'), 
							new Area('105', '松山區'), 
							new Area('106', '大安區'), 
							new Area('108', '萬華區'), 
							new Area('110', '信義區'), 
							new Area('111', '士林區'), 
							new Area('112', '北投區'), 
							new Area('114', '內湖區'), 
							new Area('115', '南港區'), 
							new Area('116', '文山區')
							)
				), 
	new County("新北市", new Array(
 							new Area('207', '萬里區'),
 							new Area('208', '金山區'),
 							new Area('220', '板橋區'),
 							new Area('221', '汐止區'),
 							new Area('222', '深坑區'),
 							new Area('223', '石碇區'),
 							new Area('224', '瑞芳區'),
 							new Area('226', '平溪區'),
 							new Area('227', '雙溪區'),
 							new Area('228', '貢寮區'),
 							new Area('231', '新店區'),
 							new Area('232', '坪林區'),
 							new Area('233', '烏來區'),
 							new Area('234', '永和區'),
 							new Area('235', '中和區'),
 							new Area('236', '土城區'),
 							new Area('237', '三峽區'),
 							new Area('238', '樹林區'),
 							new Area('239', '鶯歌區'),
 							new Area('241', '三重區'),
 							new Area('242', '新莊區'),
 							new Area('243', '泰山區'),
 							new Area('244', '林口區'),
 							new Area('247', '蘆洲區'),
							new Area('248', '五股區'),
							new Area('249', '八里區'), 
 							new Area('251', '淡水區'), 
 							new Area('252', '三芝區'), 
							new Area('253', '石門區')
							)
				),  
	new County("桃園縣", new Array(
							new Area('320', '中壢市'),
							new Area('324', '平鎮市'),
							new Area('325', '龍潭鄉'),
							new Area('326', '楊梅鎮'),
							new Area('327', '新屋鄉'),
							new Area('328', '觀音鄉'),
							new Area('330', '桃園市'),
							new Area('333', '龜山鄉'),
							new Area('334', '八德市'),
							new Area('335', '大溪鎮'),
							new Area('336', '復興鄉'),
							new Area('337', '大園鄉'),
							new Area('338', '蘆竹鄉')
							)
				), 
	new County("新竹市", new Array(
							new Area('300', '------')
							)
				),
	new County("新竹縣", new Array( 
							new Area('302', '竹北市'), 
							new Area('303', '湖口鄉'), 
							new Area('304', '新豐鄉'), 
							new Area('305', '新埔鎮'), 
							new Area('306', '關西鎮'), 
							new Area('307', '芎林鄉'), 
							new Area('308', '寶山鄉'), 
							new Area('310', '竹東鎮'), 
							new Area('311', '五峰鄉'), 
							new Area('312', '橫山鄉'), 
							new Area('313', '尖石鄉'), 
							new Area('314', '北埔鄉'), 
							new Area('315', '峨嵋鄉')
							)
				), 
	new County("苗栗縣", new Array(
							new Area('350', '竹南鎮'),
							new Area('351', '頭份鎮'),
							new Area('352', '三灣鄉'),
							new Area('353', '南庄鄉'),
							new Area('354', '獅潭鄉'),
							new Area('356', '後龍鎮'),
							new Area('357', '通霄鎮'),
							new Area('358', '苑裡鎮'),
							new Area('360', '苗栗市'),
							new Area('361', '造橋鄉'),
							new Area('362', '頭屋鄉'),
							new Area('363', '公館鄉'),
							new Area('364', '大湖鄉'),
							new Area('365', '泰安鄉'),
							new Area('366', '銅鑼鄉'),
							new Area('367', '三義鄉'),
							new Area('368', '西湖鄉'),
							new Area('369', '卓蘭鎮')
							)
				), 
	new County("台中市", new Array(
							new Area('400', '中區'),
							new Area('401', '東區'),
							new Area('402', '南區'),
							new Area('403', '西區'),
							new Area('404', '北區'),
							new Area('406', '北屯區'),
							new Area('407', '西屯區'),
							new Area('408', '南屯區'),
							new Area('411', '太平區'),
							new Area('412', '大里區'),
							new Area('413', '霧峰區'),
							new Area('414', '烏日區'),
							new Area('420', '豐原區'),
							new Area('421', '后里區'),
							new Area('422', '石岡區'),
							new Area('423', '東勢區'),
							new Area('424', '和平區'),
							new Area('426', '新社區'),
							new Area('427', '潭子區'),
							new Area('428', '大雅區'),
							new Area('429', '神岡區'),
							new Area('432', '大肚區'),
							new Area('433', '沙鹿區'),
							new Area('434', '龍井區'),
							new Area('435', '梧棲區'),
							new Area('436', '清水區'),
							new Area('437', '大甲區'),
							new Area('438', '外埔區'),
							new Area('439', '大安區')
							)
				), 
	new County("彰化縣",new Array(
							new Area('500', '彰化市'),
							new Area('502', '芬園鄉'),
							new Area('503', '花壇鄉'),
							new Area('504', '秀水鄉'),
							new Area('505', '鹿港鎮'),
							new Area('506', '福興鄉'),
							new Area('507', '線西鄉'),
							new Area('508', '和美鎮'),
							new Area('509', '伸港鄉'),
							new Area('510', '員林鎮'),
							new Area('511', '社頭鄉'),
							new Area('512', '永靖鄉'),
							new Area('513', '埔心鄉'),
							new Area('514', '溪湖鎮'),
							new Area('515', '大村鄉'),
							new Area('516', '埔鹽鄉'),
							new Area('520', '田中鎮'),
							new Area('521', '北斗鎮'),
							new Area('522', '田尾鄉'),
							new Area('523', '埤頭鄉'),
							new Area('524', '溪州鄉'),
							new Area('525', '竹塘鄉'),
							new Area('526', '二林鎮'),
							new Area('527', '大城鄉'),
							new Area('528', '芳苑鄉'),
							new Area('530', '二水鄉')
							)
				), 
	new County("南投縣", new Array(
							new Area('540', '南投市'),
							new Area('541', '中寮鄉'),
							new Area('542', '草屯鎮'),
							new Area('544', '國姓鄉'),
							new Area('545', '埔里鎮'),
							new Area('546', '仁愛鄉'),
							new Area('551', '名間鄉'),
							new Area('552', '集集鎮'),
							new Area('553', '水里鄉'),
							new Area('555', '魚池鄉'),
							new Area('556', '信義鄉'),
							new Area('557', '竹山鎮'),
							new Area('558', '鹿谷鄉')
							)
				), 
	new County("雲林縣", new Array(
							new Area('630', '斗南鎮'),
							new Area('631', '大埤鄉'),
							new Area('632', '虎尾鎮'),
							new Area('633', '土庫鎮'),
							new Area('634', '褒忠鄉'),
							new Area('635', '東勢鄉'),
							new Area('636', '台西鄉'),
							new Area('637', '崙背鄉'),
							new Area('638', '麥寮鄉'),
							new Area('640', '斗六市'),
							new Area('643', '林內鄉'),
							new Area('646', '古坑鄉'),
							new Area('647', '莿桐鄉'),
							new Area('648', '西螺鎮'),
							new Area('649', '二崙鄉'),
							new Area('651', '北港鎮'),
							new Area('652', '水林鄉'),
							new Area('653', '口湖鄉'),
							new Area('654', '四湖鄉'),
							new Area('655', '元長鄉')
							)
				), 
	new County("嘉義市", new Array(
							new Area('600', '------')
							)
				),
	new County("嘉義縣", new Array(
							new Area('602', '番路鄉'),
							new Area('603', '梅山鄉'),
							new Area('604', '竹崎鄉'),
							new Area('605', '阿里山'),
							new Area('606', '中埔鄉'),
							new Area('607', '大埔鄉'),
							new Area('608', '水上鄉'),
							new Area('611', '鹿草鄉'),
							new Area('612', '太保市'),
							new Area('613', '朴子市'),
							new Area('614', '東石鄉'),
							new Area('615', '六腳鄉'),
							new Area('616', '新港鄉'),
							new Area('621', '民雄鄉'),
							new Area('622', '大林鎮'),
							new Area('623', '溪口鄉'),
							new Area('624', '義竹鄉'),
							new Area('625', '布袋鎮')
							)
				), 
	new County("台南市", new Array(
							new Area('700', '中西區 '),
							new Area('701', '東區'),
							new Area('702', '南區'),
							new Area('704', '北區'),
							new Area('708', '安平區'),
							new Area('709', '安南區'),
							new Area('710', '永康區'),
							new Area('711', '歸仁區'),
							new Area('712', '新化區'),
							new Area('713', '左鎮區'),
							new Area('714', '玉井區'),
							new Area('715', '楠西區'),
							new Area('716', '南化區'),
							new Area('717', '仁德區'),
							new Area('718', '關廟區'),
							new Area('719', '龍崎區'),
							new Area('720', '官田區'),
							new Area('721', '麻豆區'),
							new Area('722', '佳里區'),
							new Area('723', '西港區'),
							new Area('724', '七股區'),
							new Area('725', '將軍區'),
							new Area('726', '學甲區'),
							new Area('727', '北門區'),
							new Area('730', '新營區'),
							new Area('731', '後壁區'),
							new Area('732', '白河區'),
							new Area('733', '東山區'),
							new Area('734', '六甲區'),
							new Area('735', '下營區'),
							new Area('736', '柳營區'),
							new Area('737', '鹽水區'),
							new Area('741', '善化區'),
							new Area('742', '大內區'),
							new Area('743', '山上區'),
							new Area('744', '新市區'),
							new Area('745', '安定區')
							)
				), 
	new County("高雄市", new Array(
							new Area('800', '新興區'),
							new Area('801', '前金區'),
							new Area('802', '苓雅區'),
							new Area('803', '鹽埕區'),
							new Area('804', '鼓山區'),
							new Area('805', '旗津區'),
							new Area('806', '前鎮區'),
							new Area('807', '三民區'),
							new Area('811', '楠梓區'),
							new Area('812', '小港區'),
							new Area('813', '左營區'),
							new Area('814', '仁武區'),
							new Area('815', '大社區'),
							new Area('820', '岡山區'),
							new Area('821', '路竹區'),
							new Area('822', '阿蓮區'),
							new Area('823', '田寮區'),
							new Area('824', '燕巢區'),
							new Area('825', '橋頭區'),
							new Area('826', '梓官區'),
							new Area('827', '彌陀區'),
							new Area('828', '永安區'),
							new Area('829', '湖內區'),
							new Area('830', '鳳山區'),
							new Area('831', '大寮區'),
							new Area('832', '林園區'),
							new Area('833', '鳥松區'),
							new Area('840', '大樹區'),
							new Area('842', '旗山區'),
							new Area('843', '美濃區'),
							new Area('844', '六龜區'),
							new Area('845', '內門區'),
							new Area('846', '杉林區'),
							new Area('847', '甲仙區'),
							new Area('848', '桃源區'),
							new Area('849', '三民區'),
							new Area('851', '茂林區'),
							new Area('852', '茄萣區')
							)
				), 
	new County("屏東縣", new Array(
							new Area('900', '屏東市'),
							new Area('901', '三地門'),
							new Area('902', '霧台鄉'),
							new Area('903', '瑪家鄉'),
							new Area('904', '九如鄉'),
							new Area('905', '里港鄉'),
							new Area('906', '高樹鄉'),
							new Area('907', '盬埔鄉'),
							new Area('908', '長治鄉'),
							new Area('909', '麟洛鄉'),
							new Area('911', '竹田鄉'),
							new Area('912', '內埔鄉'),
							new Area('913', '萬丹鄉'),
							new Area('920', '潮州鎮'),
							new Area('921', '泰武鄉'),
							new Area('922', '來義鄉'),  
							new Area('923', '萬巒鄉'),  
							new Area('924', '崁頂鄉'),  
							new Area('925', '新埤鄉'),    
							new Area('926', '南州鄉'),   
							new Area('927', '林邊鄉'),   
							new Area('928', '東港鎮'),   
							new Area('929', '琉球鄉'),   
							new Area('931', '佳冬鄉'),   
							new Area('932', '新園鄉'),   
							new Area('940', '枋寮鄉'),   
							new Area('941', '枋山鄉'),   
							new Area('942', '春日鄉'),   
							new Area('943', '獅子鄉'),   
							new Area('944', '車城鄉'),   
							new Area('945', '牡丹鄉'),   
							new Area('946', '恆春鎮'),   
							new Area('947', '滿州鄉')
							)
				),   
	new County("台東縣", new Array(
							new Area('950', '台東市'),  
							new Area('951', '綠島鄉'),  
							new Area('952', '蘭嶼鄉'),  
							new Area('953', '延平鄉'),  
							new Area('954', '卑南鄉'),  
							new Area('955', '鹿野鄉'),  
							new Area('956', '關山鎮'),  
							new Area('957', '海端鄉'),  
							new Area('958', '池上鄉'),  
							new Area('959', '東河鄉'),  
							new Area('961', '成功鎮'),  
							new Area('962', '長濱鄉'),  
							new Area('963', '太麻里'), 
							new Area('964', '金峰鄉'),  
							new Area('965', '大武鄉'),  
							new Area('966', '達仁鄉')
							)
				),   
	new County("花蓮縣", new Array(
							new Area('970', '花蓮市'),  
							new Area('971', '新城鄉'),  
							new Area('972', '秀林鄉'),  
							new Area('973', '吉安鄉'),  
							new Area('974', '壽豐鄉'),  
							new Area('975', '鳳林鎮'),  
							new Area('976', '光復鄉'),  
							new Area('977', '豐濱鄉'),  
							new Area('978', '瑞穗鄉'),  
							new Area('979', '萬榮鄉'),  
							new Area('981', '玉里鎮'),  
							new Area('982', '卓溪鄉'),  
							new Area('983', '富里鄉')
							)
				),   
	new County("宜蘭縣", new Array( 
							new Area('260', '宜蘭市'), 
							new Area('261', '頭城鎮'), 
							new Area('262', '礁溪鄉'), 
							new Area('263', '壯圍鄉'), 
							new Area('264', '員山鄉'), 
							new Area('265', '羅東鎮'), 
							new Area('266', '三星鄉'), 
							new Area('267', '大同鄉'), 
							new Area('268', '五結鄉'), 
							new Area('269', '冬山鄉'), 
							new Area('270', '蘇澳鎮'), 
							new Area('272', '南澳鄉')
							)
				),  
	new County("澎湖縣", new Array(
							new Area('880', '馬公市'),
							new Area('881', '西嶼鄉'),
							new Area('882', '望安鄉'),
							new Area('883', '七美鄉'),
							new Area('884', '白沙鄉'),
							new Area('885', '湖西鄉')
							)
				), 
	new County("金門縣", new Array(
							new Area('890', '金沙鎮'),  
							new Area('891', '金湖鎮'),  
							new Area('892', '金寧鄉'),  
							new Area('893', '金城鎮'),  
							new Area('894', '烈嶼鄉'),  
							new Area('896', '烏坵鄉')
							)
				),   
	new County("連江縣", new Array(
							new Area('209', '南竿鄉'),  
							new Area('210', '北竿鄉'),  
							new Area('211', '莒光鄉'),  
							new Area('212', '東引鄉')
							)
				)/*,   

	new County("南海諸島", new Array(
							new Area('817', '東沙'),  
							new Area('819', '南沙')
							)
				), 
	new County("釣魚台", new Array(
							new Area('290', '------')
							)
				)
*/
);

function setAddress(xField, xZip, xAddress){
	var prefix = "";
	if (xZip){
		xZip = xZip.substring(0, 3);
		for(var i=0; i<Counties.length; i++){
			for(var j=0; j<Counties[i].areas.length; j++){
				if(Counties[i].areas[j].zipcode == xZip){
					prefix = Counties[i].name + Counties[i].areas[j].name;
					break;
				}
			}
		}
	}
	xField.value = xZip + prefix + xAddress;
}

function setArea(county_field, area_field, new_code){
	if (new_code){
		new_code = new_code.substring(0, 3);
		for(var i=0; i<Counties.length; i++){
			for(var j=0; j<Counties[i].areas.length; j++){
				if(Counties[i].areas[j].zipcode == new_code){
					county_field.options.selectedIndex = i;
					this.chgCounty(county_field, area_field);
					area_field.options.selectedIndex = j;
					break;
				}
			}
		}
	}
}

function getArea(new_code){
	if (new_code){
		new_code = new_code.substring(0, 3);
		for(var i=0; i<Counties.length; i++){
			for(var j=0; j<Counties[i].areas.length; j++){
				if(Counties[i].areas[j].zipcode == new_code){
					return Counties[i].name + Counties[i].areas[j].name;
				}
			}
		}
	}
	return "";
}

function chgCounty(county_field, area_field){
	area_field.options.length = 0;
	for(var i=0; i<Counties[county_field.options.selectedIndex].areas.length; i++){
		area_field.options.length ++;
		area_field.options[i].text = Counties[county_field.options.selectedIndex].areas[i].name;
		area_field.options[i].value = Counties[county_field.options.selectedIndex].areas[i].zipcode;
	}
}

function chgArea(area_field, zip_field){
	zip_field.value = area_field.value;
}

function genCounty(county_field){
	county_field.options.length = 0;
	for (var i=0; i<Counties.length; i++){
		county_field.options.length ++;
		county_field.options[i].value = i;
		county_field.options[i].text = Counties[i].name;
	}
}
