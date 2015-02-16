<?php
require_once AL_SHOP_DIR."includes/functions.php";
add_to_title(": ".$locale['shp7']);


if (isset($_GET['status']) && !isset($message)) {
    if ($_GET['status'] == "success") {
        $message = $locale['shp52'];
    } elseif ($_GET['status'] == "su") {
        $message = $locale['shp53'];
    } elseif ($_GET['status'] == "del") {
        $message = $locale['shp54'];
    }
    if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}

add_to_head("<script>
    var al_shop_dir = '".AL_SHOP_DIR."';
    var fusion_self_aid = '".FUSION_SELF.$aidlink."';
</script>");
add_to_head("<script>
    var editTEXT = '".$locale['shp16']."';
    var deleteTEXT = '".$locale['shp25']."';
</script>");
add_to_head("<link rel='stylesheet' type='text/css' href='".AL_SHOP_DIR."asset/css/tree.css' media='screen' />");
add_to_head("<script src='".AL_SHOP_DIR."asset/js/tree.js'></script>");
add_to_head("<script src='".AL_SHOP_DIR."includes/plupload/js/plupload.full.js'></script>");
echo "<script src='".AL_SHOP_DIR."asset/js/images-upload.js'></script>";


$result = dbquery("SELECT * FROM ".DB_AL_SHOP_CATS);
if (dbrows($result)) {
    while ($data = dbarray($result)) {
        $cats[$data['shp_cat_id']] = $data;
    }
} else {
    $cats = null;
}


opentable($locale['shp30']);
    if (!empty($cats)) {
        $tree_array = build_cats_tree_array($cats);
        echo "<table width='100%'><tr valign='top'>";
            echo "<td width='33%'><div id='catTree'>";
            echo build_cats_tree_list($tree_array);
            echo "</div></td>";
            echo "<td width='33%' style='padding: 10px;'>".$locale['shp55'];
                echo "<div id='catResult-published'></div>";
            echo "</td><td width='33%' style='padding: 10px;'>".$locale['shp56'];
                echo "<div id='catResult-unpublished'></div>";
            echo "</td>";
        echo "</tr></table>";
    } else {
        echo $locale['shp32'];
    }
closetable();


$result = dbquery("SELECT * FROM ".DB_AL_SHOP_CATS);
if (dbrows($result)) {
    while ($data = dbarray($result)) {
        $cats[$data['shp_cat_id']] = $data;
    }
} else {
    $cats = null;
}

$error = array();
if (isset($_POST['save'])) {

    //var_dump($_POST); die;

    //print_r($_POST);
    $title = trim(stripinput($_POST['title']));
    $cat_id = $_POST['cat_id'];
    $man_id = isset($_POST['man_id']) && isnum($_POST['man_id']) ? $_POST['man_id'] : 0;
    $currency = $_POST['currency'];
    $cost = floatval($_POST['cost']);
    $desc = addslash($_POST['desc']);
    $available = $_POST['available'];
    $published = $_POST['published'];

    if (isset($_POST['cover']) && isnum($_POST['cover'])) {
        $cover = $_POST['cover'];
    } else {
        if (!empty($_POST['images-uploaded'])) {
            $cover = $_POST['images-uploaded'][0];
        } else {
            $cover = 0;
        }
    }
    foreach ($_POST['images-uploaded'] as $key=>$value) {
        if ($value == $cover) {
            unset($_POST['images-uploaded'][$key]);
        }
    }

    $images_str = implode(".",$_POST['images-uploaded']);

    if ($title != "") {

        if (isset($_POST['good_id']) && isnum($_POST['good_id'])) {

            $update = dbquery("UPDATE ".DB_AL_SHOP_GOODS." SET shp_good_title='".$title."',shp_good_desc='".$desc."',shp_good_manufacturer='".$man_id."',shp_good_images='".$images_str."',shp_good_cat='".$cat_id."',shp_good_cost='".$cost."',shp_good_currency='".$currency."',shp_good_available='".$available."',shp_good_published='".$published."',shp_good_cover='".$cover."' WHERE shp_good_id='".$_POST['good_id']."'");
            make_search_index($_POST['good_id']);

            $del = dbquery("DELETE FROM ".DB_AL_SHOP_GOODS_PARAMS." WHERE shp_gp_good_id='".$_POST['good_id']."'");

            if (isset($_POST['param_values']) && !empty($_POST['param_values'])) {

                foreach ($_POST['param_values'] as $param_id=>$param_data) {

                    if (isset($param_data['enabled']) && $param_data['enabled'] == 'yes') {
                        if ($param_data['type'] == 'text') {
                            $val = trim(stripinput($param_data['value']));
                            if (!empty($val)) {
                                dbquery("INSERT INTO ".DB_AL_SHOP_GOODS_PARAMS." (shp_gp_good_id,shp_gp_param_id,shp_gp_param_value) VALUES ('".$_POST['good_id']."','".$param_id."','".$val."')");
                            }
                        } else {
                            if (isset($param_data['options']) && !empty($param_data['options'])) {
                                $value_arr = array();
                                foreach ($param_data['options'] as $value_id=>$row) {
                                    if ($row == 'yes') {
                                        $value_arr[] = $value_id;
                                    }
                                }
                                dbquery("INSERT INTO ".DB_AL_SHOP_GOODS_PARAMS." (shp_gp_good_id,shp_gp_param_id,shp_gp_param_value) VALUES ('".$_POST['good_id']."','".$param_id."','".(implode('.',$value_arr))."')");
                            }
                        }
                    }

                }

            }

            redirect(FUSION_SELF.$aidlink."&page=goods&status=su");

        } else {

            $insert = dbquery("INSERT INTO ".DB_AL_SHOP_GOODS." (shp_good_title,shp_good_desc,shp_good_manufacturer,shp_good_images,shp_good_cat,shp_good_cost,shp_good_currency,shp_good_available,shp_good_published,shp_good_views,shp_good_buys,shp_good_cover) VALUES ('".$title."','".$desc."','".$man_id."','".$images_str."','".$cat_id."','".$cost."','".$currency."','".$available."','".$published."','0','0','".$cover."')");
            $new_good_id = mysql_insert_id();
            make_search_index($new_good_id);

            if (isset($_POST['param_values']) && !empty($_POST['param_values'])) {

                foreach ($_POST['param_values'] as $param_id=>$param_data) {

                    if (isset($param_data['enabled']) && $param_data['enabled'] == 'yes') {
                        if ($param_data['type'] == 'text') {
                            $val = trim(stripinput($param_data['value']));
                            if (!empty($val)) {
                                dbquery("INSERT INTO ".DB_AL_SHOP_GOODS_PARAMS." (shp_gp_good_id,shp_gp_param_id,shp_gp_param_value) VALUES ('".$new_good_id."','".$param_id."','".$val."')");
                            }
                        } else {
                            if (isset($param_data['options']) && !empty($param_data['options'])) {
                                $value_arr = array();
                                foreach ($param_data['options'] as $value_id=>$row) {
                                    if ($row == 'yes') {
                                        $value_arr[] = $value_id;
                                    }
                                }
                                dbquery("INSERT INTO ".DB_AL_SHOP_GOODS_PARAMS." (shp_gp_good_id,shp_gp_param_id,shp_gp_param_value) VALUES ('".$new_good_id."','".$param_id."','".(implode('.',$value_arr))."')");
                            }
                        }
                    }

                }

            }

            redirect(FUSION_SELF.$aidlink."&page=goods&status=success");

        }

    } else {
        $error[] = $locale['shp51'];
    }


} else if (isset($_GET['delete']) && isnum($_GET['delete'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_id='".$_GET['delete']."'");
    if (dbrows($result)) {
        $data = dbarray($result);
        $images_array = explode(".",$data['shp_good_images']);
        if (!empty($images_array)) {
            foreach ($images_array as $img) {
                $img_result = dbquery("SELECT * FROM ".DB_AL_SHOP_IMAGES." WHERE shp_image_id='".$img."'");
                if (dbrows($img_result)) {
                    $img_data = dbarray($img_result);
                    if (file_exists(AL_SHOP_DIR."asset/goods/".$img_data['shp_image_file'])) {
                        unlink(AL_SHOP_DIR."asset/goods/".$img_data['shp_image_file']);
                    }
                    if (file_exists(AL_SHOP_DIR."asset/goods/".$img_data['shp_image_thumb'])) {
                        unlink(AL_SHOP_DIR."asset/goods/".$img_data['shp_image_thumb']);
                    }
                    $del = dbquery("DELETE FROM ".DB_AL_SHOP_IMAGES." WHERE shp_image_id='".$img_data['shp_image_id']."'");
                }
            }
        }
        $del = dbquery("DELETE FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_id='".$_GET['delete']."'");
        $del = dbquery("DELETE FROM ".DB_AL_SHOP_SEARCH." WHERE shp_search_good='".$_GET['delete']."'");
        $del = dbquery("DELETE FROM ".DB_AL_SHOP_GOODS_PARAMS." WHERE shp_gp_good_id='".$_GET['delete']."'");
        redirect(FUSION_SELF.$aidlink."&page=goods&status=del");
    }
    redirect(FUSION_SELF.$aidlink."&page=goods");

} else if (isset($_GET['edit']) && isnum($_GET['edit'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_id='".$_GET['edit']."'");
    if (dbrows($result)) {

        $is_edit = true;
        $data = dbarray($result);
        $good_id = $data['shp_good_id'];
        $title = $data['shp_good_title'];
        $desc = $data['shp_good_desc'];
        $cat_id = $data['shp_good_cat'];
        $man_id = $data['shp_good_manufacturer'];
        $currency = $data['shp_good_currency'];
        $cost = $data['shp_good_cost'];
        $available = $data['shp_good_available'];
        $published = $data['shp_good_published'];
        $images_str = $data['shp_good_cover'].".".$data['shp_good_images'];
        $cover = $data['shp_good_cover'];
        $result = dbquery("SELECT gp.*, p.* FROM ".DB_AL_SHOP_GOODS_PARAMS." gp LEFT JOIN ".DB_AL_SHOP_GOOD_PARAMS." p ON p.shp_param_id=gp.shp_gp_param_id WHERE shp_gp_good_id='".$good_id."'");
        $param_values = array();
        if (dbrows($result)) {
            while ($data = dbarray($result)) {
                if ($data['shp_param_type'] == 'text') {
                    $param_values[$data['shp_param_id']] = array('value'=>$data['shp_gp_param_value']);
                } else {
                    $param_values[$data['shp_param_id']] = array('value'=>explode('.',$data['shp_gp_param_value']));
                }
            }
        }

    } else {
        redirect(FUSION_SELF.$aidlink."&page=goods");
    }

} else {
    $title = "";
    $cat_id = 0;
    $man_id = 0;
    $available = 1;
    $published = 1;
    $desc = "";
    $cost = 0;
    $currency = "USD";
    $is_edit = false;
    $images_str = "";
    $cover = 0;
    $param_values = array();
}

opentable($locale['shp34']);
if ($cats != null) {
    echo "<form action='".FUSION_SELF.$aidlink."&page=goods' method='post' name='inputform'>";
    echo "<table width='100%'>";
    if (!empty($error)) {
        echo "<tr>";
            echo "<td class='tbl'></td><td class='tbl'>";
                foreach ($error as $e) {
                    echo $e."<br />";
                }
            echo "</td>";
        echo "</tr>";
    }
    echo "<tr>";
        echo "<td width='250' class='tbl'>".$locale['shp35']."</td>";
        echo "<td class='tbl'><input type='text' name='title' value='".$title."' class='textbox' style='width:250px;' /></td>";
    echo "</tr><tr>";
        echo "<td class='tbl'>".$locale['shp36']."</td>";
        echo "<td class='tbl'><select name='cat_id' class='textbox'>".build_cats_tree_select(build_cats_tree_array($cats),0,$cat_id)."</select></td>";
    echo "</tr><tr>";
        echo "<td class='tbl'>".$locale['shp37']."</td>";
        echo "<td class='tbl'>";
            $result = dbquery("SELECT * FROM ".DB_AL_SHOP_MANUFACTURES);
            if (dbrows($result)) {
                echo "<select name='man_id' class='textbox'>";
                echo "<option value='0'".($man_id == 0 ? " selected='selected'" : "").">".$locale['shp50']."</option>";
                    while ($data=dbarray($result)) {
                        echo "<option value='".$data['shp_manufacturer_id']."'".($data['shp_manufacturer_id'] == $man_id ? " selected='selected'" : "").">".$data['shp_manufacturer_title']."</option>";
                    }
                echo "</select>";
            } else {
                echo $locale['shp28'];
            }
        echo "</td>";
    echo "</tr><tr valign='top'>";
        echo "<td class='tbl'>".$locale['shp38']."</td>";
        echo "<td class='tbl'>";
        echo "<div id='files-uploaded'>";
            if ($images_str != "") {
                $images_array = explode(".",$images_str);
                foreach ($images_array as $img) {
                    $img_result = dbquery("SELECT * FROM ".DB_AL_SHOP_IMAGES." WHERE shp_image_id='".$img."'");
                    if (dbrows($img_result)) {
                        $img_data = dbarray($img_result);
                        echo "<div class='uimage uimage-".$img_data['shp_image_id']."'><input type='radio' name='cover' value='".$img_data['shp_image_id']."'".($img_data['shp_image_id'] == $cover ? " checked='checked'" : "")." /><img src='".AL_SHOP_DIR."asset/goods/".$img_data['shp_image_thumb']."' height='50' /><a href='#del' onclick='javascript: delete_image(".$img_data['shp_image_id'].");'>".$locale['shp17']."</a><input type='hidden' name='images-uploaded[]' value='".$img_data['shp_image_id']."' /></div>";
                    }
                }
            }
        echo "</div>";
        echo "<div id='files-container'><div id='filelist'></div><br /><a class='shop-button' id='pickfiles' href='#hatethisstupidnullanchorinfusion'>".$locale['shp47']."</a></div>";
        echo "</td>";
    echo "</tr><tr valign='top'>";
        echo "<td class='tbl'>".$locale['shp39']."</td>";
        echo "<td class='tbl'>";
        $currencies = array("RUB","BYR","UAH","USD","EUR");
        echo "<select name='currency'>";
        foreach ($currencies as $c) {
            if ($shop_settings[$c.'_enabled'] == 1) {
                echo "<option value='".$c."'".($currency == $c ? " selected='selected'" : "").">".$c."</option>";
            }
        }    

    echo "&nbsp;<input type='text' name='cost' class='textbox' value='".$cost."' style='width:60px;' /></td>";
    echo "</tr><tr valign='top'>";
        echo "<td class='tbl'>".$locale['shp40']."</td>";
        echo "<td class='tbl'><textarea class='textbox' name='desc' style='width:400px;' rows='7'>".$desc."</textarea><br />";
    echo "</td>";
    echo "</tr><tr>";
        echo "<td class='tbl'>".$locale['shp41']."</td>";
        echo "<td class='tbl'><select name='available' class='textbox'><option value='1'".($available == 1 ? " selected='selected'" : "").">".$locale['shp43']."</option><option value='0'".($available == 0 ? " selected='selected'" : "").">".$locale['shp44']."</option></select></td>";
    echo "</tr><tr>";
        echo "<td class='tbl'>".$locale['shp42']."</td>";
        echo "<td class='tbl'><select name='published' class='textbox'><option value='1'".($published == 1 ? " selected='selected'" : "").">".$locale['shp45']."</option><option value='0'".($published == 0 ? " selected='selected'" : "").">".$locale['shp46']."</option></select></td>";
    echo "</tr><tr>";

    echo "</tr>";
    //var_dump($param_values);

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAMS);
    if (dbrows($result)) {
        while ($data = dbarray($result)) {
            echo "<tr valign='top'>";
                echo "<td class='tbl'><input type='checkbox' name='param_values[".$data['shp_param_id']."][enabled]' value='yes'".(isset($param_values[$data['shp_param_id']]) ? " checked='checked'" : "")." /> ".$data['shp_param_name']."<input type='hidden' name='param_values[".$data['shp_param_id']."][type]' value='".$data['shp_param_type']."' /></td>";
                echo "<td class='tbl'>";
                    switch ($data['shp_param_type']) {
                        case 'text':
                            echo "<input type='text' class='textbox' name='param_values[".$data['shp_param_id']."][value]' value='".(isset($param_values[$data['shp_param_id']]) && isset($param_values[$data['shp_param_id']]['value']) && !empty($param_values[$data['shp_param_id']]['value']) ? $param_values[$data['shp_param_id']]['value'] : '')."' />";
                        break;
                        case 'select':
                            $result2 = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAM_VALUES." WHERE shp_value_param_id='".$data['shp_param_id']."'");
                            if (dbrows($result2)) {
                                while ($data2 = dbarray($result2)) {
                                    echo "<input type='checkbox' name='param_values[".$data['shp_param_id']."][options][".$data2['shp_value_id']."]' value='yes'".(isset($param_values[$data['shp_param_id']]) && isset($param_values[$data['shp_param_id']]['value']) && in_array($data2['shp_value_id'],$param_values[$data['shp_param_id']]['value']) ? " checked='checked'" : "")." /> ".$data2['shp_value_data']."<br />";
                                }
                            }
                        break;
                    }
                echo "</td>";
            echo "</tr>";
        }
    }
    echo "<tr>";
        echo "<td class='tbl' colspan='2'><input type='submit' class='button' name='save' value='".$locale['shp18']."' />&nbsp;";
        if ($is_edit) {
            echo "<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=goods'>".$locale['shp19']."</a>";
            echo "<input type='hidden' name='good_id' value='".$good_id."' />";
        }
        echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</form>";
} else {
    echo $locale['shp32'];
}
closetable();



?>