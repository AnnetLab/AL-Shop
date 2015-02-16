<?php
require_once "../../../../maincore.php";
require_once INFUSIONS."al_shop/infusion_db.php";

if (isset($_GET['InvId']) && isnum($_GET['InvId'])) {
    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_id='".$_GET['InvId']."'");
    if (dbrows($result)) {
        $data = dbarray($result);
        $crc = md5($data['shp_order_cost'].":".$data['shp_order_id'].":".$shop_settings['mrh_pass1']);
        if (strtoupper($crc) == strtoupper($_GET['SignatureValue']) && $_GET['OutSum'] == $data['shp_order_cost']) {
            redirect(BASEDIR."shop.php?action=order&id=".$data['shp_order_id']);
        } else {
            redirect(BASEDIR."shop.php");
        }

    }
}

?>