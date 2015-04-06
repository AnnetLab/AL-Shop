<?php
require_once INFUSIONS."al_shop/includes/classes/PHPExcel/IOFactory.php";
require_once INCLUDES."photo_functions_include.php";

function shp_get_image($path) {

    global $shop_settings;

    if (is_numeric($path)) {

        $check_image = dbquery("SELECT * FROM ".DB_AL_SHOP_IMAGES." WHERE shp_image_id='".$path."'");
        if (dbrows($check_image)) {
            $image = dbarray($check_image);
            return $image['shp_image_id'];
        }

    } else {

        $ext = strtolower(strrchr($path,"."));
        if ($ext == ".gif") { $filetype = 1;
        } elseif ($ext == ".jpg") { $filetype = 2;
        } elseif ($ext == ".png") { $filetype = 3;
        } else { $filetype = false; }
        if ($filetype != false) {
            $path = AL_SHOP_DIR."asset/goods/";
            $i = 1;
            $img_name = md5(time());
            $img_name_full = filename_exists($path,$img_name.$ext);
            @file_put_contents($path.$img_name_full, @file_get_contents($path));
            if (file_exists($path.$img_name_full)) {
                $image_res = @getimagesize($path.$img_name_full);
                if ($image_res[0] > $shop_settings['thumb_width'] || $image_res[1] > $shop_settings['thumb_height']) {
                    $img_name_t = filename_exists($path, $img_name."_t".$ext);
                    createthumbnail($filetype, $path.$img_name_full, $path.$img_name_t, $shop_settings['thumb_width'], $shop_settings['thumb_height']);
                } else {
                    $img_name_t = $img_name_full;
                }
                dbquery("INSERT INTO ".DB_AL_SHOP_IMAGES." (shp_image_file,shp_image_thumb) VALUES ('".$img_name_full."','".$img_name_t."')");
                $image_id = mysql_insert_id();
                return $image_id;
            }
        }

        return 0;

    }

}

$result = dbquery("SELECT * FROM ".DB_AL_SHOP_CATS);
while ($data=dbarray($result)) {
    $cats_cache[$data['shp_cat_title']] = $data['shp_cat_id'];
}
$result = dbquery("SELECT * FROM ".DB_AL_SHOP_MANUFACTURES);
while ($data=dbarray($result)) {
    $mans_cache[$data['shp_manufacturer_title']] = $data['shp_manufacturer_id'];
}
$objPHPExcel = PHPExcel_IOFactory::load($temp_path.$filename);
foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
    $highestRow         = $worksheet->getHighestRow();
    for ($row = 2; $row <= $highestRow; ++ $row){

        $good_id = $worksheet->getCellByColumnAndRow(0, $row);
        if ($good_id) {

            $find_good = dbquery("SELECT * FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_id='".$good_id."'");

        }

        if (isset($cats_cache[iconv("UTF-8","Windows-1251",$worksheet->getCellByColumnAndRow(2, $row)->getValue())])) {
            $cat = $cats_cache[iconv("UTF-8","Windows-1251",$worksheet->getCellByColumnAndRow(2, $row)->getValue())];
        } else {
            $result = dbquery("INSERT INTO ".DB_AL_SHOP_CATS." (shp_cat_title,shp_cat_image,shp_cat_parent) VALUES ('".iconv("UTF-8","Windows-1251",$worksheet->getCellByColumnAndRow(2, $row)->getValue())."','','0')");
            $cat = mysql_insert_id();
            $cats_cache[iconv("UTF-8","Windows-1251",$worksheet->getCellByColumnAndRow(2, $row)->getValue())] = $cat;
        }
        if (isset($mans_cache[iconv("UTF-8","Windows-1251",$worksheet->getCellByColumnAndRow(4, $row)->getValue())])) {
            $man = $mans_cache[iconv("UTF-8","Windows-1251",$worksheet->getCellByColumnAndRow(4, $row)->getValue())];
        } else {
            $result = dbquery("INSERT INTO ".DB_AL_SHOP_MANUFACTURES." (shp_manufacturer_title,shp_manufacturer_image,shp_manufacturer_desc) VALUES ('".iconv("UTF-8","Windows-1251",$worksheet->getCellByColumnAndRow(4, $row)->getValue())."','','')");
            $man = mysql_insert_id();
            $mans_cache[iconv("UTF-8","Windows-1251",$worksheet->getCellByColumnAndRow(5, $row)->getValue())] = $man;
        }
        // cover
        $cover = '';
        if ($worksheet->getCellByColumnAndRow(5,$row)->getValue() != '') {

            $cover = shp_get_image($worksheet->getCellByColumnAndRow(5,$row)->getValue());

        }
        // other images
        $other_images = array();
        if ($worksheet->getCellByColumnAndRow(6,$row)->getValue() != '') {
            $images = explode('||',$worksheet->getCellByColumnAndRow(6,$row)->getValue());
            if ($images) {
                foreach ($images as $image) {
                    $received = shp_get_image($image);
                    if ($received) {
                        $other_images[] = $received;
                    }
                }
            }

        }

        if (dbrows($find_good)) {

            $update = dbquery("UPDATE ".DB_AL_SHOP_GOODS." SET shp_good_title='".iconv("UTF-8","Windows-1251",$worksheet->getCellByColumnAndRow(1, $row)->getValue())."',shp_good_desc='".iconv("UTF-8","Windows-1251",$worksheet->getCellByColumnAndRow(3, $row)->getValue())."',shp_good_manufacturer='".$man."',shp_good_images='".implode('.',$other_images)."', shp_good_cat='".$cat."', shp_good_cost='".str_replace(",",".",$worksheet->getCellByColumnAndRow(7, $row)->getValue())."',shp_good_currency='".$worksheet->getCellByColumnAndRow(8, $row)->getValue()."', shp_good_cover='".$cover."' WHERE shp_good_id='".$good_id."'");

        } else  {

            $insert = dbquery("INSERT INTO ".DB_AL_SHOP_GOODS." (shp_good_title,shp_good_desc,shp_good_manufacturer,shp_good_images,shp_good_cat,shp_good_cost,shp_good_currency,shp_good_available,shp_good_published,shp_good_views,shp_good_buys,shp_good_cover) VALUES ('".iconv("UTF-8","Windows-1251",$worksheet->getCellByColumnAndRow(1, $row)->getValue())."','".iconv("UTF-8","Windows-1251",$worksheet->getCellByColumnAndRow(3, $row)->getValue())."','".$man."','','".$cat."','".str_replace(",",".",$worksheet->getCellByColumnAndRow(7, $row)->getValue())."','".$worksheet->getCellByColumnAndRow(8, $row)->getValue()."','1','1','0','0','".$cover."')");

        }
    }
}

?>
