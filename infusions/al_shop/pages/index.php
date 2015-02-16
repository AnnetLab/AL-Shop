<?php

set_title($locale['shp229']." | ".$settings['sitename']);


    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CATS." WHERE shp_cat_parent='0'");
    if (dbrows($result)) {
        $data = make_assoc($result);

        require_once AL_SHOP_TPL_DIR."shop_index.php";

    } else {
        opentable($locale['shp81']);
        $locale['shp82'];
        closetable();
    }






?>