<?php

echo "<div>";
    echo "<div style='float:left;'><br />".show_breadcrumbs("cat",$_GET['id'])."</div>";
    if (!isset($_GET['action']) || $_GET['action'] != 'search') {
        require_once AL_SHOP_TPL_DIR."search_form.php";
    }
echo "</div>";

opentable($locale['shp229']);
    echo "<div style='width:100%;text-align:center;'>
        ".$locale['shp250']."
    </div>";
closetable();
?>