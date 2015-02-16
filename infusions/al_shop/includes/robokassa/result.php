<?php
require_once "../../../../maincore.php";
require_once INFUSIONS."al_shop/infusion_db.php";
require_once AL_SHOP_DIR."includes/functions.php";

if (isset($_GET['InvId']) && isnum($_GET['InvId'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_id='".$_GET['InvId']."'");
    if (dbrows($result)) {
        $data = dbarray($result);
        $crc = md5($data['shp_order_cost'].":".$data['shp_order_id'].":".$shop_settings['mrh_pass2']);
        if ($_GET['OutSum'] == $data['shp_order_cost'] && strtoupper($crc) == strtoupper($_GET['SignatureValue'])) {
            $update = dbquery("UPDATE ".DB_AL_SHOP_ORDERS." SET shp_order_payed='1', shp_order_payment_type='2' WHERE shp_order_id='".$data['shp_order_id']."'");

            require_once INCLUDES."sendmail_include.php";
            $subject = $settings['sitename'];
            $message_owner = sprintf($locale['shp217'],$_GET['InvId']);
            $message_buyer = sprintf($locale['shp218'],show_cost($data['shp_order_cost'],$shop_settings['currency_default'],$shop_settings['currency_default']),$order_id,$order_id);
            sendemail($data['shp_order_fio'], $data['shp_order_email'], $settings['siteusername'], $settings['siteemail'], $subject, $message_buyer);
            sendemail($settings['siteusername'], $settings['siteemail'], $settings['siteusername'], $settings['siteemail'], $subject, $message_owner);

            echo "OK".$data['shp_order_id'];
        }

    }

}

?>