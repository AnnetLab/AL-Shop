<?php
defined("IN_FUSION") or die("Denied");
require_once INFUSIONS."al_shop/infusion_db.php";
if (file_exists(AL_SHOP_DIR."locale/".$settings['locale'].".php")) {
    include AL_SHOP_DIR."locale/".$settings['locale'].".php";
} else {
    include AL_SHOP_DIR."locale/Russian.php";
}
require_once AL_SHOP_DIR."includes/functions.php";



opentable($locale['shp_m_1']);

$result = dbquery("SELECT * FROM ".DB_AL_SHOP_MANUFACTURES);
if (dbrows($result)) {
    while ($data=dbarray($result)) {
        echo "<a href='".BASEDIR."shop.php?action=manufacturer&id=".$data['shp_manufacturer_id']."'>".$data['shp_manufacturer_title']."</a><br />";
    }
} else {
    echo $locale['shp_m_2'];
}

closetable()

?>