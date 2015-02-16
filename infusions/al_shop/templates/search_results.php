<?php
/**
 * $search_query - search query
 * $error - search errors (1 - too short query, 2 - no results)
 * $total - total results if has results
 * $data - results if have
 */

opentable($locale['shp233']);

    echo "<div style='width:100%;text-align:center;margin-top:20px;margin-bottom:30px;'>";
        echo "<form action='".FUSION_SELF."' method='get'>";
        echo "<input type='hidden' name='action' value='search' />";
        echo "<input type='text' style='width:300px;' class='textbox' name='search_query' value='".$search_query."' /><input type='submit' name='search' class='button' value='".$locale['shp225']."' />";
        echo "</form>";
    echo "</div>";

closetable();

opentable($locale['shp274']);

    if ($error == 1) {
        echo $locale['shp275'];
    } else if ($error == 2) {
        echo $locale['shp276'];
    } else {

        echo $locale['shp277']." - ".$total."<br /><br />";
        echo "<table width='100%'>";
        foreach ($data as $row) {
            echo "<tr>";
                $img = get_good_cover($row['shp_good_cover'],'thumb');

                echo "<td clas='tbl' width='".($shop_settings['thumb_width']+10)."'><a href='".FUSION_SELF."?action=good&id=".$row['shp_good_id']."'><img src='".$img."' alt='".$row['shp_good_title']."' width='".$shop_settings['thumb_width']."' /></a></td>";
                echo "<td class='tbl'><a href='".FUSION_SELF."?action=good&id=".$row['shp_good_id']."'>".$row['shp_good_title']."</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        if ($total > 10) {
            echo makepagenav($_GET['rowstart'], 10, $total, 3, FUSION_SELF."action=search&search_query=".$_GET['search_query']."&search=".$_GET['search']."&");
        }

    }

closetable();

?>