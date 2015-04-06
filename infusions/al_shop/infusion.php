<?php defined("IN_FUSION") or die("DEnied");
require_once INFUSIONS."al_shop/infusion_db.php";
if (file_exists(AL_SHOP_DIR."locale/".$settings['locale'].".php")) {
    include AL_SHOP_DIR."locale/".$settings['locale'].".php";
} else {
    include AL_SHOP_DIR."locale/Russian.php";
}

$inf_title = $locale['shp1'];
$inf_description = $locale['shp2'];
$inf_version = "1.3";
$inf_developer = "Rush @ AnnetLab.ru";
$inf_email = "roman.kunashko@annetlab.ru";
$inf_weburl = "http://annetlab.ru";

$inf_folder = "al_shop";

$inf_newtable[1] = DB_AL_SHOP_CATS." (
shp_cat_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_cat_parent INT(11) NOT NULL DEFAULT '0',
shp_cat_title VARCHAR(250) NOT NULL DEFAULT '',
shp_cat_image VARCHAR(250) NOT NULL DEFAULT '',
PRIMARY KEY (shp_cat_id)
) ENGINE=MYISAM;";

$inf_newtable[2] = DB_AL_SHOP_GOODS." (
shp_good_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_good_title VARCHAR(250) NOT NULL DEFAULT '',
shp_good_desc TEXT NOT NULL,
shp_good_manufacturer INT(11) NOT NULL DEFAULT '0',
shp_good_images VARCHAR(250) NOT NULL DEFAULT '',
shp_good_cat INT(11) NOT NULL DEFAULT '0',
shp_good_cost FLOAT(15) NOT NULL DEFAULT '0',
shp_good_currency VARCHAR(3) NOT NULL DEFAULT 'USD',
shp_good_available INT(1) NOT NULL DEFAULT '0',
shp_good_published INT(1) NOT NULL DEFAULT '0',
shp_good_views INT(11) NOT NULL DEFAULT '0',
shp_good_buys INT(11) NOT NULL DEFAULT '0',
shp_good_cover INT(11) NOT NULL DEFAULT '0',
PRIMARY KEY (shp_good_id)
) ENGINE=MYISAM;";

$inf_newtable[3] = DB_AL_SHOP_ORDERS." (
shp_order_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_order_cost VARCHAR(15) NOT NULL DEFAULT '',
shp_order_str TEXT NOT NULL,
shp_order_type INT(1) NOT NULL DEFAULT '0',
shp_order_fio VARCHAR(250) NOT NULL DEFAULT '',
shp_order_address VARCHAR(250) NOT NULL DEFAULT '',
shp_order_email VARCHAR(250) NOT NULL DEFAULT '',
shp_order_phone VARCHAR(250) NOT NULL DEFAULT '',
shp_order_company VARCHAR(250) NOT NULL DEFAULT '',
shp_order_inn VARCHAR(250) NOT NULL DEFAULT '',
shp_order_kpp VARCHAR(250) NOT NULL DEFAULT '',
shp_order_note TEXT NOT NULL,
shp_order_payed INT(1) NOT NULL DEFAULT '0',
shp_order_payment_type INT(1) NOT NULL DEFAULT '0',
shp_order_datestamp INT(11) NOT NULL DEFAULT '0',
shp_order_user INT(11) NOT NULL DEFAULT '0',
shp_order_ip VARCHAR(16) NOT NULL DEFAULT '0.0.0.0',
shp_order_finished INT(1) NOT NULL DEFAULT '0',
shp_order_delivery INT(1) NOT NULL DEFAULT '0',
shp_order_admin_note TEXT NOT NULL,
PRIMARY KEY (shp_order_id)
) ENGINE=MYISAM;";

$inf_newtable[4] = DB_AL_SHOP_BASKET." (
shp_basket_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_basket_good INT(11) NOT NULL DEFAULT '0',
shp_basket_amount INT(11) NOT NULL DEFAULT '0',
shp_basket_good_params VARCHAR(250) NOT NULL DEFAULT '',
shp_basket_date INT(11) NOT NULL DEFAULT '0',
shp_basket_user INT(11) NOT NULL DEFAULT '0',
shp_basket_ip VARCHAR(16) NOT NULL DEFAULT '0.0.0.0',
PRIMARY KEY (shp_basket_id)
) ENGINE=MYISAM;";

$inf_newtable[5] = DB_AL_SHOP_MANUFACTURES." (
shp_manufacturer_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_manufacturer_title VARCHAR(250) NOT NULL DEFAULT '',
shp_manufacturer_image VARCHAR(250) NOT NULL DEFAULT '',
shp_manufacturer_desc TEXT NOT NULL,
PRIMARY KEY (shp_manufacturer_id)
) ENGINE=MYISAM;";

$inf_newtable[6] = DB_AL_SHOP_SETTINGS." (
photo_max_width int(5) NOT NULL,
photo_max_height int(5) NOT NULL,
cat_thumb_width int(5) NOT NULL,
cat_thumb_height int(5) NOT NULL,
max_photo_size int(11) NOT NULL,
thumb_width int(11) NOT NULL,
thumb_height int(11) NOT NULL,
url_RUB varchar(250) NOT NULL,
url_BYR varchar(250) NOT NULL,
url_UAH varchar(250) NOT NULL,
currency_current_updater varchar(3) NOT NULL,
RUB_enabled int(1) NOT NULL,
BYR_enabled int(1) NOT NULL,
UAH_enabled int(1) NOT NULL,
EUR_enabled int(1) NOT NULL,
USD_enabled int(1) NOT NULL,
currency_default varchar(3) NOT NULL,
currency_lastupdate int(11) NOT NULL,
currency_manual int(1) NOT NULL,
mrh_login VARCHAR(100) NOT NULL DEFAULT '',
mrh_pass1 VARCHAR(100) NOT NULL DEFAULT '',
mrh_pass2 VARCHAR(100) NOT NULL DEFAULT '',
firm_nachalnik VARCHAR(250) NOT NULL DEFAULT '',
firm_buhgalter VARCHAR(250) NOT NULL DEFAULT '',
firm_name VARCHAR(250) NOT NULL DEFAULT '',
firm_address VARCHAR(250) NOT NULL DEFAULT '',
firm_inn VARCHAR(250) NOT NULL DEFAULT '',
firm_kpp VARCHAR(250) NOT NULL DEFAULT '',
firm_bank VARCHAR(250) NOT NULL DEFAULT '',
firm_schet VARCHAR(250) NOT NULL DEFAULT '',
firm_schet_banka VARCHAR(250) NOT NULL DEFAULT '',
firm_bik VARCHAR(250) NOT NULL DEFAULT '',
firm_nds_enabled INT(1) NOT NULL DEFAULT '0',
check_expired INT(2) NOT NULL DEFAULT '15',
cats_in_line INT(2) NOT NULL DEFAULT '5',
goods_in_line INT(2) NOT NULL DEFAULT '5',
goods_per_page INT(2) NOT NULL DEFAULT '30',
robokassa_enabled INT(1) NOT NULL DEFAULT '1',
beznal_enabled INT(1) NOT NULL DEFAULT '1',
paypal_enabled INT(1) NOT NULL DEFAULT '1',
nal_enabled INT(1) NOT NULL DEFAULT '1',
paypal_email VARCHAR(250) NOT NULL DEFAULT ''
) ENGINE=MYISAM;";

$inf_newtable[7] = DB_AL_SHOP_IMAGES." (
shp_image_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_image_file VARCHAR(250) NOT NULL DEFAULT '',
shp_image_thumb VARCHAR(250) NOT NULL DEFAULT '',
PRIMARY KEY (shp_image_id)
) ENGINE=MYISAM;";

$inf_newtable[8] = DB_AL_SHOP_CURRENCY." (
shp_currency_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_currency_current VARCHAR(3) NOT NULL DEFAULT '',
shp_currency_code VARCHAR(3) NOT NULL DEFAULT '',
shp_currency_nominal INT(10) NOT NULL DEFAULT '0',
shp_currency_rate FLOAT(15) NOT NULL DEFAULT '0',
PRIMARY KEY (shp_currency_id)
) ENGINE=MYISAM;";

$inf_newtable[9] = DB_AL_SHOP_SEARCH." (
shp_search_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_search_key TEXT NOT NULL,
shp_search_good INT(10) NOT NULL DEFAULT '0',
PRIMARY KEY (shp_search_id)
) ENGINE=MYISAM;";

$inf_newtable[10] = DB_AL_SHOP_DELIVERIES." (
shp_delivery_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_delivery_title VARCHAR(250) NOT NULL DEFAULT '',
shp_delivery_cost FLOAT(15) NOT NULL DEFAULT '0',
shp_delivery_currency VARCHAR(3) NOT NULL DEFAULT 'USD',
PRIMARY KEY (shp_delivery_id)
) ENGINE=MYISAM;";

$inf_newtable[11] = DB_AL_SHOP_CLIENTS_DATA." (
shp_client_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_client_user INT(11) NOT NULL DEFAULT '0',
shp_client_data TEXT NOT NULL,
PRIMARY KEY (shp_client_id)
) ENGINE=MYISAM;";

$inf_newtable[12] = DB_AL_SHOP_GOOD_PARAMS." (
shp_param_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_param_name VARCHAR(250) NOT NULL DEFAULT '',
shp_param_type ENUM('text','select') NOT NULL DEFAULT 'text',
PRIMARY KEY (shp_param_id)
) ENGINE=MYISAM;";

$inf_newtable[13] = DB_AL_SHOP_GOOD_PARAM_VALUES." (
shp_value_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_value_param_id INT(11) NOT NULL DEFAULT '0',
shp_value_data VARCHAR(250) NOT NULL DEFAULT '',
PRIMARY KEY (shp_value_id)
) ENGINE=MYISAM;";

$inf_newtable[14] = DB_AL_SHOP_GOODS_PARAMS." (
shp_gp_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
shp_gp_good_id INT(11) NOT NULL DEFAULT '0',
shp_gp_param_id INT(11) NOT NULL DEFAULT '0',
shp_gp_param_value TEXT NOT NULL,
PRIMARY KEY (shp_gp_id)
) ENGINE=MYISAM;";

$inf_insertdbrow[1] = DB_AL_SHOP_SETTINGS." (photo_max_width,photo_max_height,cat_thumb_width,cat_thumb_height,max_photo_size,thumb_width,thumb_height,url_RUB,url_BYR,url_UAH,currency_current_updater,RUB_enabled,BYR_enabled,UAH_enabled,EUR_enabled,USD_enabled,currency_default,currency_lastupdate,currency_manual,mrh_login,mrh_pass1,mrh_pass2,firm_nachalnik, firm_buhgalter, firm_name, firm_address, firm_inn, firm_kpp, firm_bank, firm_schet, firm_schet_banka, firm_bik, firm_nds_enabled ,check_expired,cats_in_line,goods_in_line,goods_per_page,beznal_enabled,robokassa_enabled,paypal_enabled,nal_enabled,paypal_email) VALUES ('1800','1600','150','150','2000000','100','100','http://www.cbr.ru/scripts/XML_daily.asp','http://nbrb.by/Services/XmlExRates.aspx','http://bank-ua.com/export/currrate.xml','RUB','1','1','1','1','1','RUB','0','0','','','','','','','','','','','','','','0','15','5','5','30','1','1','1','1','')";

$inf_droptable[1] = DB_AL_SHOP_CATS;
$inf_droptable[2] = DB_AL_SHOP_GOODS;
$inf_droptable[3] = DB_AL_SHOP_ORDERS;
$inf_droptable[4] = DB_AL_SHOP_BASKET;
$inf_droptable[5] = DB_AL_SHOP_MANUFACTURES;
$inf_droptable[6] = DB_AL_SHOP_SETTINGS;
$inf_droptable[7] = DB_AL_SHOP_IMAGES;
$inf_droptable[8] = DB_AL_SHOP_CURRENCY;
$inf_droptable[9] = DB_AL_SHOP_SEARCH;
$inf_droptable[10] = DB_AL_SHOP_DELIVERIES;
$inf_droptable[11] = DB_AL_SHOP_CLIENTS_DATA;
$inf_droptable[12] = DB_AL_SHOP_GOOD_PARAMS;
$inf_droptable[13] = DB_AL_SHOP_GOOD_PARAM_VALUES;
$inf_droptable[14] = DB_AL_SHOP_GOODS_PARAMS;

$inf_adminpanel[1] = array(
    "title" => $locale['shp1'],
    "image" => "shop.png",
    "panel" => "admin/index.php",
    "rights" => "SHP"
);

$inf_sitelink[1] = array(
    "title" => $locale['shp1'],
    "url" => "../../shop.php",
    "visibility" => "0"
);

?>
