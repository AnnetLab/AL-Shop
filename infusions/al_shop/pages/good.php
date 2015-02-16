<?php

$result = dbquery("SELECT g.*,c.*,m.* FROM ".DB_AL_SHOP_GOODS." g
                LEFT JOIN ".DB_AL_SHOP_CATS." c ON c.shp_cat_id=g.shp_good_cat
                LEFT JOIN ".DB_AL_SHOP_MANUFACTURES." m ON m.shp_manufacturer_id=g.shp_good_manufacturer
                WHERE shp_good_id='".$_GET['id']."' AND shp_good_published='1'");
if (dbrows($result)) {

    $currencies = array("RUB","BYR","UAH","USD","EUR");
    if (!isset($_GET['currency']) || !in_array($_GET['currency'],$currencies)) {
        $_GET['currency'] = $shop_settings['currency_default'];
    }
    $good = dbarray($result);
    set_title($good['shp_good_title']." | ".$good['shp_cat_title']." | ".$locale['shp229']." | ".$settings['sitename']);
    $views = dbquery("UPDATE ".DB_AL_SHOP_GOODS." SET shp_good_views=shp_good_views+1 WHERE shp_good_id='".$good['shp_good_id']."'");

    if ($good['shp_good_images'] != "") {
        $images_array = explode(".",$good['shp_good_images']);
        $images_result = dbquery("SELECT * FROM ".DB_AL_SHOP_IMAGES." WHERE shp_image_id IN (".implode(",",$images_array).")");
        $images_count = dbrows($images_result);
        $images = make_assoc($images_result);
    } else {
        $images_count = 0;
    }

    $good_params = array();
    $good_params_sel = array();
    $result_params = dbquery("SELECT gp.*, p.* FROM ".DB_AL_SHOP_GOODS_PARAMS." gp LEFT JOIN ".DB_AL_SHOP_GOOD_PARAMS." p ON p.shp_param_id=gp.shp_gp_param_id WHERE shp_gp_good_id='".$good['shp_good_id']."' AND shp_param_type='text'");
    if (dbrows($result_params)) {
        $good_params = make_assoc($result_params);
    }
    $result_params = dbquery("SELECT gp.*, p.* FROM ".DB_AL_SHOP_GOODS_PARAMS." gp LEFT JOIN ".DB_AL_SHOP_GOOD_PARAMS." p ON p.shp_param_id=gp.shp_gp_param_id WHERE shp_gp_good_id='".$good['shp_good_id']."' AND shp_param_type='select'");
    if (dbrows($result_params)) {
        while ($data = dbarray($result_params)) {
            if (!empty($data['shp_gp_param_value'])) {
                $values_result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAM_VALUES." WHERE shp_value_id IN (".implode(',',explode('.',$data['shp_gp_param_value'])).")");
                if (dbrows($values_result)) {
                    $values_arr = array();
                    while ($data2 = dbarray($values_result)) {
                        $values_arr[] = $data2['shp_value_data'];
                    }
                    $good_params_sel[] = array_merge($data,array('values'=>$values_arr));
                }
            }
        }
    }


    require_once AL_SHOP_TPL_DIR."good.php";

} else {
    redirect(FUSION_SELF);
}

?>