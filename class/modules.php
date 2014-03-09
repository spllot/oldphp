<?php
$modules = array(
    'home'		=> array(
						"首頁", 
						array(
							array("關於我們", "about.php"),
							array("服務項目", "service.php"),
							array("成品展示", "product.php"),
							array("線上表單", "form.php"),
							array("聯絡我們", "contact.php")
							)
						),
   'system'		=> array(
						"系統管理", 
						array(
							array("類別維護", "admin/system_catalog.php"),
							array("部門維護", "admin/system_department.php"),
							array("群組維護", "admin/system_group.php"),
							array("員工維護", "admin/system_employee.php"),
							array("權限維護", "admin/system_permission.php")
							)
						),
	'supplier'		=> array(
						"廠商管理", 
						array(
							array("廠商類別", "admin/supplier_catalog.php"),
							array("新增廠商", "admin/supplier_property.php"),
							array("廠商維護", "admin/supplier.php"),
//							array("廠商查詢", "admin/supplier_query.php")
							)
						),
    'customer'		=> array(
						"客戶管理", 
						array(
							array("客戶類別", "admin/customers_catalog.php"),
							array("新增客戶", "admin/customers_property.php"),
							array("客戶維護", "admin/customers.php"),
//							array("客戶查詢", "admin/customers_query.php")
							)
						),
    'material'		=> array(
						"材料管理", 
						array(
							array("材料類別", "admin/material_catalog.php"),
							array("新增材料", "admin/material_property.php"),
							array("材料維護", "admin/material.php"),
//							array("材料查詢", "admin/material_query.php"),
							array("進貨作業", "admin/material_stock.php"),
							array("進貨查詢", "admin/material_stock_query.php")
							)
						),
    'order'			=> array(
						"訂單管理", 
						array(
							array("新增訂單", "admin/orders_property.php"),
							array("訂單維護", "admin/orders.php"),
//							array("訂單查詢", "admin/orders_query.php"),
							array("出貨作業", "admin/orders_stock.php"),
							array("出貨查詢", "admin/orders_stock_query.php")
							)
						),
    'accounting'	=> array(
						"帳務管理", 
						array(
							array("收款作業", "admin/accounting_collection.php"),
							array("付款作業", "admin/accounting_payment.php"),
							array("日報表", "admin/accounting_report_day.php"),
							array("月報表", "admin/accounting_report_month.php"),
							array("年度報表", "admin/accounting_report_year.php")
							)
						),
    'employee'	=> array(
						"員工功能", 
						array(
							array("變更密碼", "chpwd.php"),
							array("登出", "logout.php"),
							)
						)
);

?>