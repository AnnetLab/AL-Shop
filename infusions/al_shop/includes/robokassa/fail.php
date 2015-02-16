<?php
require_once "../../../../maincore.php";
require_once INFUSIONS."al_shop/infusion_db.php";
require_once THEMES."templates/header.php";
if (file_exists(AL_SHOP_DIR."locale/".$settings['locale'].".php")) {
    include AL_SHOP_DIR."locale/".$settings['locale'].".php";
} else {
    include AL_SHOP_DIR."locale/Russian.php";
}

if (isset($_GET['InvId']) && isnum($_GET['InvId'])) {
    opentable($locale['shp166']);
        echo sprintf($locale['shp167'],$_GET['InvId']);
    closetable();
} else {
    redirect(BASEDIR."shop.php");
}

require_once THEMES."templates/footer.php";
?>