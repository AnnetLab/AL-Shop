<?php defined("IN_FUSION") or die("Denied");

if (!defined("DB_AL_SHOP_CATS")) {
    define("DB_AL_SHOP_CATS",DB_PREFIX."al_shop_cats");
}
if (!defined("DB_AL_SHOP_GOODS")) {
    define("DB_AL_SHOP_GOODS",DB_PREFIX."al_shop_goods");
}
if (!defined("DB_AL_SHOP_ORDERS")) {
    define("DB_AL_SHOP_ORDERS",DB_PREFIX."al_shop_orders");
}
if (!defined("DB_AL_SHOP_BASKET")) {
    define("DB_AL_SHOP_BASKET",DB_PREFIX."al_shop_basket");
}
if (!defined("DB_AL_SHOP_MANUFACTURES")) {
    define("DB_AL_SHOP_MANUFACTURES",DB_PREFIX."al_shop_manufactures");
}
if (!defined("DB_AL_SHOP_SETTINGS")) {
    define("DB_AL_SHOP_SETTINGS",DB_PREFIX."al_shop_settings");
}
if (!defined("DB_AL_SHOP_IMAGES")) {
    define("DB_AL_SHOP_IMAGES",DB_PREFIX."al_shop_images");
}
if (!defined("DB_AL_SHOP_CURRENCY")) {
    define("DB_AL_SHOP_CURRENCY",DB_PREFIX."al_shop_currency");
}
if (!defined("DB_AL_SHOP_SEARCH")) {
    define("DB_AL_SHOP_SEARCH", DB_PREFIX."al_shop_search");
}
if (!defined("DB_AL_SHOP_DELIVERIES")) {
    define("DB_AL_SHOP_DELIVERIES", DB_PREFIX."al_shop_deliveries");
}
if (!defined("DB_AL_SHOP_CLIENTS_DATA")) {
    define("DB_AL_SHOP_CLIENTS_DATA", DB_PREFIX."al_shop_clients_data");
}
if (!defined("DB_AL_SHOP_GOOD_PARAMS")) {
    define("DB_AL_SHOP_GOOD_PARAMS", DB_PREFIX."al_shop_good_params");
}
if (!defined("DB_AL_SHOP_GOOD_PARAM_VALUES")) {
    define("DB_AL_SHOP_GOOD_PARAM_VALUES", DB_PREFIX."al_shop_good_param_values");
}
if (!defined("DB_AL_SHOP_GOODS_PARAMS")) {
    define("DB_AL_SHOP_GOODS_PARAMS", DB_PREFIX."al_shop_goods_params");
}

if (!defined("AL_SHOP_DIR")) {
    define("AL_SHOP_DIR",INFUSIONS."al_shop/");
}
if (!defined("AL_SHOP_TPL_DIR")) {
    define("AL_SHOP_TPL_DIR",AL_SHOP_DIR."templates/");
}
if (!isset($shop_settings)) {
    $result = dbquery("SELECT * FROM ".DB_INFUSIONS." WHERE inf_folder='al_shop'");
    if (dbrows($result)) {
        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_SETTINGS);
        if ($result && dbrows($result)) {
            $shop_settings = dbarray($result);
        }
    }
}




?>