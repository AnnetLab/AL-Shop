<?php
/**
 *  $count - total goods in category
 *  $data - goods
 */

opentable($locale['shp84']);

    // currency change
    echo "<div style='float:right;'>";
    echo $locale['shp313']."<select name='change_filter'>";
        echo "<option value='az-asc'".(isset($_GET['filter']) && $_GET['filter'] == "az-asc" ? " selected='selected'" : "").">".$locale['shp314']."</option>";
        echo "<option value='az-desc'".(isset($_GET['filter']) && $_GET['filter'] == "az-desc" ? " selected='selected'" : "").">".$locale['shp315']."</option>";
        echo "<option value='cost-desc'".(isset($_GET['filter']) && $_GET['filter'] == "cost-desc" ? " selected='selected'" : "").">".$locale['shp316']."</option>";
        echo "<option value='cost-asc'".(isset($_GET['filter']) && $_GET['filter'] == "cost-asc" ? " selected='selected'" : "").">".$locale['shp317']."</option>";
        echo "<option value='pop-desc'".(isset($_GET['filter']) && $_GET['filter'] == "pop-desc" ? " selected='selected'" : "").">".$locale['shp318']."</option>";
    echo "</select> ";
    echo $locale['shp88']."<select name='change_currency'><option value='".$shop_settings['currency_default']."'".($_GET['currency'] == $shop_settings['currency_default'] ? " selected='selected'" : "").">".$shop_settings['currency_default']."</option>";
    foreach ($currencies as $c) {
        if ($shop_settings['currency_default'] != $c && $shop_settings[$c.'_enabled'] == 1) {
            echo "<option value='".$c."'".($_GET['currency'] == $c ? " selected='selected'" : "").">".$c."</option>";
        }
    }
    echo "</select></div>";

    // fancybox
    add_to_head("<script type='text/javascript' src='".AL_SHOP_DIR."includes/fancybox/lib/jquery.mousewheel-3.0.6.pack.js'></script>");
    add_to_head("<link rel='stylesheet' href='".AL_SHOP_DIR."includes/fancybox/source/jquery.fancybox.css?v=2.1.3' type='text/css' media='screen' />");
    add_to_head("<script type='text/javascript' src='".AL_SHOP_DIR."includes/fancybox/source/jquery.fancybox.pack.js?v=2.1.3'></script>");

    // render goods
    echo "<table width='100%'>";
    $i = 0;
    echo "<tr>";
    foreach ($data as $d) {
        if ($i%$shop_settings['goods_in_line'] == 0 && $i!=0) echo "</tr><tr>";
        echo "<td class='tbl' width='".round(100/$shop_settings['goods_in_line'])."%'>";
        $img = get_good_cover($d['shp_good_cover'],'thumb');

        echo "<a href='".FUSION_SELF."?action=good&id=".$d['shp_good_id']."' style='float:left;margin-right:10px;'><img src='".$img."' alt='".$d['shp_good_title']."' width='".$shop_settings['thumb_width']."' /></a>";
        echo "<div style='margin-left:".($shop_settings['thumb_width']+10)."px;'>";
        echo "<a href='".FUSION_SELF."?action=good&id=".$d['shp_good_id']."'>".$d['shp_good_title']."</a><br /><br />";
        if ($d['shp_good_available'] == 1) {
            echo $locale['shp87']."<br />";
            echo show_cost($d['shp_good_cost'],$d['shp_good_currency'],$_GET['currency'])."<br /><br />";
            echo "<a href='#adc' class='add_to_cart shop-button' data-good-id='".$d['shp_good_id']."'>".$locale['shp249']."</a>";
        } else {
            echo $locale['shp86'];
        }
        echo "</div>";
        echo "</td>";
        $i++;
    }
    if ($i%$shop_settings['goods_in_line'] != 0) {
        do {
            echo "<td width='".round(100/$shop_settings['goods_in_line'])."%'>&nbsp;</td>";
            $i++;
        } while ($i%$shop_settings['goods_in_line'] != 0);
    }
    echo "</tr>";
    echo "</table>";

closetable();

// pagination
if ($count > $shop_settings['goods_per_page']) {
    echo makepagenav($_GET['rowstart'], $shop_settings['goods_per_page'], $count, 3, FUSION_SELF."?action=category&id=".$cat['shp_cat_id']."&currency=".$_GET['currency']."&");
}

?>