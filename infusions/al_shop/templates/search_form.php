<?php
add_to_head("<link rel='stylesheet' href='".AL_SHOP_DIR."asset/search.css' />");
echo "<div class='shop-search-field'>";
    echo "<form action='".FUSION_SELF."' method='get'>";
        echo "<input type='hidden' name='action' value='search' />";
        echo "<input type='text' class='textbox' name='search_query' /><input type='submit' name='search' class='button' value='".$locale['shp225']."' />";
    echo "</form>";
    echo "<div style='clear:both;'></div>";
echo "</div>";


?>