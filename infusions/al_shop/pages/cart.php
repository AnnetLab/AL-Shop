<?php

if (iMEMBER) {
    $result = dbquery("SELECT b.*,g.*,c.* FROM ".DB_AL_SHOP_BASKET." b LEFT JOIN ".DB_AL_SHOP_GOODS." g ON g.shp_good_id=b.shp_basket_good LEFT JOIN ".DB_AL_SHOP_CATS." c ON c.shp_cat_id=g.shp_good_cat WHERE shp_basket_user='".$userdata['user_id']."' OR shp_basket_ip='".FUSION_IP."'");
} else {
    $result = dbquery("SELECT b.*,g.*,c.* FROM ".DB_AL_SHOP_BASKET." b LEFT JOIN ".DB_AL_SHOP_GOODS." g ON g.shp_good_id=b.shp_basket_good LEFT JOIN ".DB_AL_SHOP_CATS." c ON c.shp_cat_id=g.shp_good_cat WHERE shp_basket_ip='".FUSION_IP."'");
}


if (dbrows($result)) {

    set_title($locale['shp230']." | ".$settings['sitename']);
    $data = make_assoc($result);

    foreach ($data as $key=>$row) {
        if ($row['shp_basket_good_params'] != '') {
            $param_pairs = explode('||',$row['shp_basket_good_params']);
            foreach ($param_pairs as $param_pair) {
                list($param_id,$value_id) = explode('-',$param_pair);
                $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAMS." WHERE shp_param_id='".$param_id."'");
                $result2 = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAM_VALUES." WHERE shp_value_id='".$value_id."'");
                if (dbrows($result) && dbrows($result2)) {
                    $param_data = dbarray($result);
                    $value_data = dbarray($result2);
                    $data[$key]['params'][$param_data['shp_param_name']] = $value_data['shp_value_data'];
                }
            }
        }
    }

    require_once AL_SHOP_TPL_DIR."cart.php";

    echo "<script>
        $(document).ready(function(){
            $('.cart-select-all').click(function(){
                $('.cart-checkbox').each(function(){
                    $(this).attr('checked','checked');
                });
                return false;
            });
            $('.cart-unselect-all').click(function(){
                $('.cart-checkbox').each(function(){
                    $(this).removeAttr('checked');
                });
                return false;
            });
        });
    </script>";

} else {
    require_once AL_SHOP_TPL_DIR."cart_empty.php";
}





?>