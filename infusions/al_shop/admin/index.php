<?php
require_once "../../../maincore.php";
if (isset($_GET['page']) && $_GET['page'] == 'goods') {
    require_once THEMES."templates/admin_header_mce.php";
    echo "<script language='javascript' type='text/javascript'>advanced();</script>\n";
} else{
    require_once THEMES."templates/admin_header.php";
}
require_once INFUSIONS."al_shop/infusion_db.php";
if (file_exists(AL_SHOP_DIR."locale/".$settings['locale'].".php")) {
    include AL_SHOP_DIR."locale/".$settings['locale'].".php";
} else {
    include AL_SHOP_DIR."locale/Russian.php";
}

add_to_head("<link rel='stylesheet' href='".AL_SHOP_DIR."asset/shop-styles.css' />");

if (!checkAdminPageAccess("SHP")) redirect(START_PAGE);

opentable($locale['shp3']);
echo "<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=orders'>".$locale['shp8']."</a>&nbsp;";
echo "<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=categories'>".$locale['shp5']."</a>&nbsp;";
echo "<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=manufacturers'>".$locale['shp6']."</a>&nbsp;";
echo "<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=goods'>".$locale['shp7']."</a>&nbsp;";
echo "<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=statistics'>".$locale['shp4']."</a>&nbsp;";
echo "<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=deliveries'>".$locale['shp300']."</a>&nbsp;";
echo "<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=params'>".$locale['shp328']."</a>&nbsp;";
echo "<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=excel'>".$locale['shp301']."</a>&nbsp;";
echo "<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=settings'>".$locale['shp9']."</a>&nbsp;";
closetable();

if (!isset($_GET['page'])) {
    require_once AL_SHOP_DIR."admin/orders.php";
} else  {
    if (file_exists(AL_SHOP_DIR."admin/".$_GET['page'].".php")) {
        require_once AL_SHOP_DIR."admin/".$_GET['page'].".php";
    } else {
        redirect(FUSION_SELF.$aidlink."&page=orders");
    }
}

require_once THEMES."templates/footer.php";
?>