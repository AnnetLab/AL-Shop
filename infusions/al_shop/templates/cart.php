<?php
/**
 *  $data = ordered goods
 */

$total_cost = 0;
//render cart
opentable($locale['shp98']);
    echo "<form method='post' action='".FUSION_SELF."?action=checkout'>";
    echo "<table width='100%'>";
    echo "<tr>";
    echo "<td class='tbl2' width='1%'></td>";
    echo "<td class='tbl2'><strong>".$locale['shp102']."</strong></td>";
    echo "<td class='tbl2'><strong>".$locale['shp103']."</strong></td>";
    echo "<td class='tbl2' width='70'><strong>".$locale['shp104']."</strong></td>";
    echo "<td class='tbl2' width='220'><strong>".$locale['shp105']."</strong></td>";
    echo "</tr>";
    foreach ($data as $d) {
        $total_cost += $d['shp_basket_amount']*convert_cost($d['shp_good_cost'],$d['shp_good_currency'],$shop_settings['currency_default']);
        echo "<tr>";
        echo "<td class='tbl'><input type='checkbox' class='textbox cart-checkbox' name='goods[]' checked='checked' value='".$d['shp_basket_id']."' /></td>";
        echo "<td class='tbl'><a href='".FUSION_SELF."?action=good&id=".$d['shp_good_id']."'>".$d['shp_good_title']."</a> ";
        if (isset($d['params']) && !empty($d['params'])) {
            echo "<i class='small'>(";
            $i = 1;
            foreach ($d['params'] as $param=>$value) {
                echo $param.": ".$value.($i < count($d['params']) ? ", " : "");
                $i++;
            }
            echo ")</i>";
        }
        echo "</td>";
        echo "<td class='tbl'><a href='".FUSION_SELF."?action=category&id=".$d['shp_good_cat']."'>".$d['shp_cat_title']."</a></td>";
        echo "<td class='tbl'><input type='text' class='textbox' style='width:60px;text-align:right;' name='amount[".$d['shp_basket_id']."]' value='".$d['shp_basket_amount']."' /></td>";
        echo "<td class='tbl' align='right'>".$d['shp_basket_amount']."x".show_cost($d['shp_good_cost'],$d['shp_good_currency'],$shop_settings['currency_default'])." = ".show_cost($d['shp_good_cost']*$d['shp_basket_amount'],$d['shp_good_currency'],$shop_settings['currency_default'])."</td>";
        echo "</tr>";
    }
    echo "<tr>";
        echo "<td colspan='5' class='tbl' align='right'><strong>".$locale['shp310'].show_cost($total_cost,$shop_settings['currency_default'],$shop_settings['currency_default'])."</strong></td>";
    echo "</tr>";
    echo "<tr>";
        echo "<td class='tbl' colspan='2'><a href='#s' class='shop-button cart-select-all'>".$locale['shp214']."</a> <a href='#u' class='shop-button cart-unselect-all'>".$locale['shp219']."</a></td>";
        echo "<td class='tbl' colspan='3' align='right'><input type='submit' class='button' name='checkout' value='".$locale['shp106']."' /> <input type='submit' class='button' name='delete' value='".$locale['shp107']."' onclick='return confirm(\"".$locale['shp213']."\");' /></td>";
    echo "</tr>";
    echo "</table>";
    echo "</form>";

closetable();

?>