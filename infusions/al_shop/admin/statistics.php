<?php
require_once AL_SHOP_DIR."includes/functions.php";
add_to_title(": ".$locale['shp4']);

$cats = dbcount("(shp_cat_id)",DB_AL_SHOP_CATS);
$mans = dbcount("(shp_manufacturer_id)",DB_AL_SHOP_MANUFACTURES);
$goods = dbcount("(shp_good_id)",DB_AL_SHOP_GOODS);
$orders = dbcount("(shp_order_id)",DB_AL_SHOP_ORDERS);
$orders_sum = dbresult(dbquery("SELECT SUM(shp_order_cost) AS cost FROM ".DB_AL_SHOP_ORDERS), 0);
$orders_finished = dbcount("(shp_order_id)",DB_AL_SHOP_ORDERS,"shp_order_finished='6'");
$orders_sum_finished = dbresult(dbquery("SELECT SUM(shp_order_cost) AS cost FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_finished='6'"), 0);

opentable($locale['shp4']);
echo "<table width='100%'>";
    echo "<tr>";
        echo "<td class='tbl' width='250'>".$locale['shp237']."</td>";
        echo "<td class='tbl'>".$cats."</td>";
    echo "</tr>";
    echo "<tr>";
        echo "<td class='tbl' width='250'>".$locale['shp238']."</td>";
        echo "<td class='tbl'>".$mans."</td>";
    echo "</tr>";
    echo "<tr>";
        echo "<td class='tbl' width='250'>".$locale['shp239']."</td>";
        echo "<td class='tbl'>".$goods."</td>";
    echo "</tr>";
    echo "<tr>";
        echo "<td class='tbl' width='250'>".$locale['shp240']."</td>";
        echo "<td class='tbl'>".$orders." ".$locale['shp242']." ".show_cost($orders_sum,$shop_settings['currency_default'],$shop_settings['currency_default'])."</td>";
    echo "</tr>";
    echo "<tr>";
        echo "<td class='tbl' width='250'>".$locale['shp241']."</td>";
        echo "<td class='tbl'>".$orders_finished." ".$locale['shp242']." ".show_cost($orders_sum_finished,$shop_settings['currency_default'],$shop_settings['currency_default'])."</td>";
    echo "</tr>";
echo "</table>";
closetable();

?>