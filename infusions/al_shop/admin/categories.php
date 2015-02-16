<?php
require_once AL_SHOP_DIR."includes/functions.php";
require_once INCLUDES."infusions_include.php";
add_to_title(": ".$locale['shp5']);

if (isset($_GET['status']) && !isset($message)) {
    if ($_GET['status'] == "success") {
        $message = $locale['shp243'];
    } elseif ($_GET['status'] == "su") {
        $message = $locale['shp244'];
    } elseif ($_GET['status'] == "del") {
        $message = $locale['shp245'];
    }
    if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}

$result = dbquery("SELECT * FROM ".DB_AL_SHOP_CATS);
if (dbrows($result)) {
    while ($data = dbarray($result)) {
        $cats[$data['shp_cat_id']] = $data;
    }
} else {
    $cats = null;
}

$is_edit = false; $error = array();
if (isset($_POST['save'])) {

    $title = trim(stripinput($_POST['title']));
    $parent_cat = $_POST['parent_cat'];
    if ($title != "") {
        if ($_FILES['image']['name'] != "") {
            $image_uploaded = upload_image("image", "", AL_SHOP_DIR."asset/cats/", $shop_settings['photo_max_width'], $shop_settings['photo_max_height'],
                $shop_settings['max_photo_size'], true, true, false,
                0, AL_SHOP_DIR."asset/cats/", "_t1", $shop_settings['cat_thumb_width'], $shop_settings['cat_thumb_height']);
            if ($image_uploaded['error'] == 0) {
                $image = $image_uploaded['thumb1_name'];
            } else {
                $error[] = $locale['shp2'.$image_uploaded['error']];
            }
        } else {
            $image = "";
        }
    } else {
        $error[] = $locale['shp20'];
    }


    if (empty($error)) {
        if (isset($_POST['cat_id'])) {
            if (isset($_POST['delete_image']) || $image != "") {
                $del = dbarray(dbquery("SELECT shp_cat_image FROM ".DB_AL_SHOP_CATS." WHERE shp_cat_id='".$_POST['cat_id']."'"));
                if ($del['shp_cat_image'] != "" && file_exists(AL_SHOP_DIR."asset/cats/".$del['shp_cat_image'])) {
                    unlink(AL_SHOP_DIR."asset/cats/".$del['shp_cat_image']);
                }

            }
            $upd = dbquery("UPDATE ".DB_AL_SHOP_CATS." SET shp_cat_title='".$title."',shp_cat_parent='".$parent_cat."',shp_cat_image='".$image."' WHERE shp_cat_id='".$_POST['cat_id']."'");


            redirect(FUSION_SELF.$aidlink."&page=categories&status=su");
        } else {
            $ins = dbquery("INSERT INTO ".DB_AL_SHOP_CATS." (shp_cat_title,shp_cat_parent,shp_cat_image) VALUES ('".$title."','".$parent_cat."','".$image."')");


            redirect(FUSION_SELF.$aidlink."&page=categories&status=su");
        }
    } else {
        if (isset($_POST['cat_id'])) {
            $cat_id = $_POST['cat_id'];
            $is_edit = true;
        }
    }

} else if (isset($_POST['delete'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CATS." WHERE shp_cat_id='".$_POST['cat_id']."'");
    if (dbrows($result)) {
        $result = dbquery("SELECT shp_good_id FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_cat='".$_POST['cat_id']."'");
        if (dbrows($result)) {
            while ($data = dbarray($result)) {
                $del = dbquery("DELETE FROM ".DB_AL_SHOP_SEARCH." WHERE shp_search_good='".$data['shp_good_id']."'");
            }
        }
        $del = dbquery("DELETE FROM ".DB_AL_SHOP_GOODS."' WHERE shp_good_cat='".$_POST['cat_id']."'");
        $del = dbquery("DELETE FROM ".DB_AL_SHOP_CATS." WHERE shp_cat_id='".$_POST['cat_id']."'");
    }
    redirect(FUSION_SELF.$aidlink."&page=categories&status=del");

} else if (isset($_POST['edit'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CATS." WHERE shp_cat_id='".$_POST['cat_id']."'");
    if (dbrows($result)) {
        $data = dbarray($result);
        $title = $data['shp_cat_title'];
        $parent_cat = $data['shp_cat_parent'];
        $image = $data['shp_cat_image'];
        $cat_id = $data['shp_cat_id'];
        $is_edit = true;

    } else {
        redirect(FUSION_SELF.$aidlink."&page=categories");
    }

} else {
    $title = '';
    $parent_cat = 0;
    $image = '';
    $cat_id = '';
}


opentable($locale['shp10']);

    echo "<div style='width:100%;text-align:center;'>";
    if ($cats !== null) {
        echo "<form action='".FUSION_SELF.$aidlink."&page=categories' method='post'>";
        echo "<select name='cat_id' class='textbox'>".build_cats_tree_select(build_cats_tree_array($cats),0,$parent_cat)."</select>";
        echo "&nbsp;<input type='submit' name='edit' value='".$locale['shp16']."' class='button' />";
        echo "&nbsp;<input type='submit' name='delete' value='".$locale['shp17']."' class='button' />";
        echo "</form>";
    } else {
        echo $locale['shp32'];
    }
    echo "</div>";
closetable();

opentable($locale['shp11']);
echo "<form action='".FUSION_SELF.$aidlink."&page=categories' method='post' enctype='multipart/form-data'>";
echo "<table width='100%'>";
if (isset($error) && !empty($error)) {
    echo "<tr>";
        foreach ($error as $e) {
            echo "<td class='tbl'></td><td class='tbl'>".$e."</td>";
        }
    echo "</tr>";
}
echo "<tr>";
    echo "<td class='tbl' width='250'>".$locale['shp12']."</td>";
    echo "<td class='tbl'><input type='text' class='textbox' style='width:250px;' name='title' value='".$title."' /></td>";
echo "</tr><tr>";
    echo "<td class='tbl'>".$locale['shp13']."</td>";
    echo "<td class='tbl'><select name='parent_cat' class='textbox'><option value='0'>".$locale['shp15']."</option>".build_cats_tree_select(build_cats_tree_array($cats),0,$parent_cat)."</select></td>";
echo "</tr><tr valign='top'>";
    echo "<td class='tbl'>".$locale['shp14']."</td>";
    echo "<td class='tbl'><input type='file' name='image' class='textbox'/><br />";
        if (!empty($image)) {
            echo "<input type='checkbox' name='delete_image' class='textbox' value='yes' /> ".$locale['shp25'];
            echo "<br /><img src='".AL_SHOP_DIR."asset/cats/".$image."' />";
        }
    echo "</td>";
echo "</tr><tr>";

    echo "<td colspan='2' class='tbl'><input type='submit' class='button' name='save' value='".$locale['shp18']."' />";
    if ($is_edit) {
        echo "<input type='hidden' name='cat_id' value='".$cat_id."' />";
        echo "&nbsp;<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=categories'>".$locale['shp19']."</a>";
    }
    echo "</td>";
echo "</tr>";
echo "</table>";
echo "</form>";
closetable();
?>