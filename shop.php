<?php
require_once "maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."al_shop/infusion_db.php";

if (file_exists(AL_SHOP_DIR."locale/".$settings['locale'].".php")) {
    include AL_SHOP_DIR."locale/".$settings['locale'].".php";
} else {
    include AL_SHOP_DIR."locale/Russian.php";
}
require_once AL_SHOP_DIR."includes/functions.php";
add_to_head("<link rel='stylesheet' href='".AL_SHOP_DIR."asset/shop-styles.css' />");



if (isset($_GET['action'])) {

    if (in_array($_GET['action'],array("category","cart","good","checkout","order","manufacturer","search")) && file_exists(AL_SHOP_DIR."pages/".$_GET['action'].".php")) {
        if (in_array($_GET['action'],array("category","good","manufacturer")) && (!isset($_GET['id']) || !isnum($_GET['id']))) {
            redirect(FUSION_SELF);
        }
        if ($_GET['action'] == "order" && ((!isset($_GET['id']) || !isnum($_GET['id'])) && ((!isset($_GET['delete']) || !isnum($_GET['delete']))))) {
            redirect(FUSION_SELF);
        }

        require_once AL_SHOP_DIR."pages/".$_GET['action'].".php";
    } else {
        redirect(FUSION_SELF);
    }

} else {
    //require_once AL_SHOP_TPL_DIR."search_form.php";
    require_once AL_SHOP_DIR."pages/index.php";
}

require_once THEMES."templates/footer.php";
?>