<?php

opentable($locale['shp233']);

echo "<div style='width:100%;text-align:center;margin-top:20px;margin-bottom:30px;'>";
echo "<form action='".FUSION_SELF."' method='get'>";
echo "<input type='hidden' name='action' value='search' />";
echo "<input type='text' style='width:300px;' class='textbox' name='search_query' /><input type='submit' name='search' class='button' value='".$locale['shp225']."' />";
echo "</form>";
echo "</div>";

closetable();

?>