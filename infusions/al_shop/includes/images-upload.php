<?php
require_once "../../../maincore.php";
require_once INCLUDES."infusions_include.php";
require_once INFUSIONS."al_shop/infusion_db.php";

if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {

    $upload_dir = AL_SHOP_DIR."asset/goods/";


    $uploaded = upload_image(
        "file", "", $upload_dir, $shop_settings['photo_max_width'], $shop_settings['photo_max_height'],
        $shop_settings['max_photo_size'], false, true, false,
        0, $upload_dir, "_t1", $shop_settings['thumb_width'], $shop_settings['thumb_height']);

    if ($uploaded['error'] == 0) {

        $insert = dbquery("INSERT INTO ".DB_AL_SHOP_IMAGES." (shp_image_file,shp_image_thumb) VALUES ('".$uploaded['image_name']."','".$uploaded['thumb1_name']."')");
        $id = mysql_insert_id();
        //die(json_encode(array('jsonrpc'=>'2.0','image_id'=>$id)));
        //die(json_encode(array('jsonrpc'=>'2.0','id'=>'id','image_id'=>$id,'image_thumb'=>$uploaded['thumb1_name'])));
        die('{"jsonrpc" : "2.0", "thumb" : "'.$uploaded['thumb1_name'].'", "id" : "'.$id.'"}');

    } else {
        die('{"jsonrpc" : "2.0", "result" : "'.$uploaded['error'].'"}');
    }

}

if (isset($_POST['action']) && $_POST['action'] == "delete_image" && isset($_POST['image_id']) && isnum($_POST['image_id'])) {

    $img_result = dbquery("SELECT * FROM ".DB_AL_SHOP_IMAGES." WHERE shp_image_id='".$_POST['image_id']."'");
    if (dbrows($img_result)) {
        $img_data = dbarray($img_result);
        if (file_exists(AL_SHOP_DIR."asset/goods/".$img_data['shp_image_file'])) {
            unlink(AL_SHOP_DIR."asset/goods/".$img_data['shp_image_file']);
        }
        if (file_exists(AL_SHOP_DIR."asset/goods/".$img_data['shp_image_thumb'])) {
            unlink(AL_SHOP_DIR."asset/goods/".$img_data['shp_image_thumb']);
        }
        $del = dbquery("DELETE FROM ".DB_AL_SHOP_IMAGES." WHERE shp_image_id='".$_POST['image_id']."'");
    }
    die();

}

?>