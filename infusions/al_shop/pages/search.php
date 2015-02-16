<?php

set_title($locale['shp233']." | ".$settings['sitename']);

if (isset($_GET['search']) && isset($_GET['search_query'])) {

    $error = 0;
    $search_query = trim(stripinput(urldecode($_GET['search_query'])));
    if (strlen($search_query) < 3) {
        $error = 1;
    } else {
        if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) $_GET['rowstart'] = 0;
        $result = dbquery("SELECT s.shp_search_good, g.* FROM ".DB_AL_SHOP_SEARCH." s LEFT JOIN ".DB_AL_SHOP_GOODS." g ON g.shp_good_id=s.shp_search_good WHERE shp_search_key LIKE '%".$search_query."%' LIMIT ".$_GET['rowstart'].",10");
        if (dbrows($result)) {
            $data = make_assoc($result);
            $total = dbcount("(shp_search_id)",DB_AL_SHOP_SEARCH,"shp_search_key LIKE '%".$search_query."%'");
        } else {
            $error = 2;
        }
    }
    require_once AL_SHOP_TPL_DIR."search_results.php";


} else {

    require_once AL_SHOP_TPL_DIR."search_page.php";

}






/*$result = dbquery("SELECT shp_search_good FROM ".DB_AL_SHOP_SEARCH." WHERE shp_search_key LIKE '%".$_POST['search_query']."%'");
if (dbrows($result)) {
    while ($data=dbarray($result)) {
        print_r($data);
    }
} else {
    echo 'no';
}*/


?>