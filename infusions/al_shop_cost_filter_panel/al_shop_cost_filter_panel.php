<?php
defined("IN_FUSION") or die("DENIED");
require_once INFUSIONS."al_shop/infusion_db.php";
if (file_exists(AL_SHOP_DIR."locale/".$settings['locale'].".php")) {
    include AL_SHOP_DIR."locale/".$settings['locale'].".php";
} else {
    include AL_SHOP_DIR."locale/Russian.php";
}
require_once INFUSIONS."al_shop/includes/functions.php";

if (strpos($_SERVER['SCRIPT_NAME'],'/shop.php') !== FALSE && isset($_GET['action']) && ($_GET['action'] == 'category' || $_GET['action'] == 'manufacturer')) {

    $action = FUSION_SELF.'?action='.$_GET['action'].(isset($_GET['id']) && isnum($_GET['id']) ? '&id='.$_GET['id'] : '').(isset($_GET['currency']) && !empty($_GET['currency']) ? '&currency='.$_GET['currency'] : '').(isset($_GET['filter']) && !empty($_GET['filter']) ? '&filter='.$_GET['filter'] : '');

    opentable($locale['shp343']);
        echo "<form action='".FUSION_SELF."' method='get'>";
            echo "<input type='hidden' name='action' value='".$_GET['action']."' />";
            echo "<input type='hidden' name='id' value='".$_GET['id']."' />";
            if (isset($_GET['currency']) && !empty($_GET['currency'])) {
                echo "<input type='hidden' name='currency' value='".$_GET['currency']."' />";
            }
            if (isset($_GET['filter']) && !empty($_GET['filter'])) {
                echo "<input type='hidden' name='filter' value='".$_GET['filter']."' />";
            }
        echo "<div style='text-align:center'>";
            echo $locale['shp344']." <input type='text' class='textbox' value='".(isset($_GET['cost_min']) && isnum($_GET['cost_min']) ? $_GET['cost_min'] : '0')."' name='cost_min' style='width:40px;' /> ".$locale['shp345']." <input type='text' class='textbox' value='".(isset($_GET['cost_max']) && isnum($_GET['cost_max']) ? $_GET['cost_max'] : '9999')."' name='cost_max' style='width:40px;' /><br />";
            echo "<input type='submit' class='button' name='' value='".$locale['shp346']."' />";
        echo "</div>";
        echo "</form>";
    closetable();

}


?>