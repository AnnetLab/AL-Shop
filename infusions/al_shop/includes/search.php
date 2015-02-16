<?php
add_to_head("<link rel='stylesheet' href='".AL_SHOP_DIR."asset/search.css' />");
echo "<div class='shop-search-field'>";
    echo "<form action='".FUSION_SELF."?action=search' method='post'>";
    echo "<input type='text' class='textbox' name='search_query' /><input type='submit' name='search' class='button' value='".$locale['shp225']."' />";
    echo "</form>";
echo "</div>";
echo "<div style='clear:both;'></div>";

?>