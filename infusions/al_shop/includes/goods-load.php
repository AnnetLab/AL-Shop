<?php
require_once "../../../maincore.php";
require_once INFUSIONS."al_shop/infusion_db.php";


if (isset($_GET['cat_id'])) {
    $result = dbquery("SELECT shp_good_id,shp_good_title,shp_good_published FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_cat='".$_GET['cat_id']."'");
    if (dbrows($result)) {
        $i = 0;
        $goods_published = false; $goods_unpublished = false;
        while ($data = dbarray($result)) {
            if ($data['shp_good_published'] == 1) {
                $goods_published[] = array('good_id'=>$data['shp_good_id'],'good_title'=>iconv("Windows-1251","UTF-8",$data['shp_good_title']));
            } else {
                $goods_unpublished[] = array('good_id'=>$data['shp_good_id'],'good_title'=>iconv("Windows-1251","UTF-8",$data['shp_good_title']));
            }
            $i++;
        }
        $response = array('total'=>$i,'goods_published'=>$goods_published,'goods_unpublished'=>$goods_unpublished);
    } else {
        $response = array('total'=>0);
    }
    print(json_encode($response));
}
?>