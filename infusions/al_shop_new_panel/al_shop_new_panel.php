<?php
defined("IN_FUSION") or die("Denied");
require_once INFUSIONS."al_shop/infusion_db.php";
if (file_exists(AL_SHOP_DIR."locale/".$settings['locale'].".php")) {
    include AL_SHOP_DIR."locale/".$settings['locale'].".php";
} else {
    include AL_SHOP_DIR."locale/Russian.php";
}
require_once AL_SHOP_DIR."includes/functions.php";
add_to_head("<link rel='stylesheet' href='".AL_SHOP_DIR."asset/shop-styles.css' />");
add_to_head("<script type='text/javascript' src='".AL_SHOP_DIR."includes/fancybox/lib/jquery.mousewheel-3.0.6.pack.js'></script>");
add_to_head("<link rel='stylesheet' href='".AL_SHOP_DIR."includes/fancybox/source/jquery.fancybox.css?v=2.1.3' type='text/css' media='screen' />");
add_to_head("<script type='text/javascript' src='".AL_SHOP_DIR."includes/fancybox/source/jquery.fancybox.pack.js?v=2.1.3'></script>");

opentable($locale['shp_n_1']);

$result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOODS." ORDER BY shp_good_id DESC LIMIT 0,3");
if (dbrows($result)) {
    echo "<table width='100%'>";
    echo "<tr>";
    while ($data = dbarray($result)) {
        echo "<td width='33%' align='center'>";

        $img = get_good_cover($data['shp_good_cover'],'thumb');
        echo "<a href='".BASEDIR."shop.php?action=good&id=".$data['shp_good_id']."'><img src='".$img."' width='".$shop_settings['thumb_width']."' /><br />".$data['shp_good_title']."</a><br /><strong>".show_cost($data['shp_good_cost'],$data['shp_good_currency'],$shop_settings['currency_default'])."</strong><br />";
        echo "<br /><a href='#adc' class='add_to_cart shop-button' data-good-id='".$data['shp_good_id']."'>".$locale['shp249']."</a><br />";
        echo "<script>
                    $(document).ready(function(){
                        $('.add_to_cart').click(function(){
                            var good_id = $(this).attr('data-good-id');
                            $.fancybox({
                                type: 'ajax',
                                href: '".AL_SHOP_DIR."includes/cart.php?action=add&id='+good_id
                            });
                        });
                    });
                </script>";

        echo "</td>";
    }
    echo "</tr>";
    echo "</table>";
} else {
    echo $locale['shp_n_2'];
}

closetable()

?>