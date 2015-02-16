<?php
require_once "../../../maincore.php";
require_once INFUSIONS."al_shop/infusion_db.php";

if (isset($_POST['action']) && $_POST['action'] == 'delete_param') {
    $param_id = isset($_POST['param_id']) && isnum($_POST['param_id']) ? $_POST['param_id'] : 0;
    if ($param_id == 0 || !checkrights('SHP')) die('Invalid data');

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAM_VALUES." WHERE shp_value_id='".$param_id."'");
    if (dbrows($result)) {
        dbquery("DELETE FROM ".DB_AL_SHOP_GOOD_PARAM_VALUES." WHERE shp_value_id='".$param_id."'");
    }
    die();

}


?>