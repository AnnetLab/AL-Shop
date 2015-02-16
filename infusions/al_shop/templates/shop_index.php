<?php
/**
 * $data - array of root categories
 */

if (!isset($_GET['action']) || $_GET['action'] != 'search') {
    require_once AL_SHOP_TPL_DIR."search_form.php";
}

opentable($locale['shp81']);

    echo "<table width='100%'>";
    $i = 0;
    echo "<tr>";
    foreach ($data as $d) {
        if ($i%$shop_settings['cats_in_line'] == 0 && $i!=0) echo "</tr><tr>";
        echo "<td class='tbl' width='".ceil(100/$shop_settings['cats_in_line'])."%' align='center'>";
        echo "<a href='".FUSION_SELF."?action=category&id=".$d['shp_cat_id']."'><img src='".AL_SHOP_DIR."asset/".($d['shp_cat_image'] != "" ? "cats/".$d['shp_cat_image'] : "no_image.gif")."' alt='".$d['shp_cat_title']."' width='".$shop_settings['cat_thumb_width']."' /><br />".$d['shp_cat_title']."</a>";
        echo "</td>";
        $i++;
    }
    echo "</tr>";
    echo "</table>";

closetable();
?>