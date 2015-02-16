<?php
/**
 *  $man - manufacturer array
 */

echo "<div>";
    echo "<div style='float:left;'><br />".show_breadcrumbs("man",$_GET['id'])."</div>";
    if (!isset($_GET['action']) || $_GET['action'] != 'search') {
        require_once AL_SHOP_TPL_DIR."search_form.php";
    }
echo "</div>";

opentable($man['shp_manufacturer_title']);

    echo "<table width='100%'>";
    echo "<tr valign='top'>";
        echo "<td class='tbl' width='".($shop_settings['cat_thumb_width']+10)."'><img src='".AL_SHOP_DIR."asset/".($man['shp_manufacturer_image'] != '' ? "manufactures/".$man['shp_manufacturer_image'] : "no_image.gif")."' width='".$shop_settings['cat_thumb_width']."'/></td>";
        echo "<td class='tbl'>".$man['shp_manufacturer_title']."<br /><br />".$man['shp_manufacturer_desc']."</td>";
    echo "</tr>";
    echo "</table>";

closetable();

?>