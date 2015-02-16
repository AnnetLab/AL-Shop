<?php
require_once INCLUDES."infusions_include.php";
add_to_title(": ".$locale['shp6']);

if (isset($_GET['status']) && !isset($message)) {
    if ($_GET['status'] == "success") {
        $message = $locale['shp246'];
    } elseif ($_GET['status'] == "su") {
        $message = $locale['shp247'];
    } elseif ($_GET['status'] == "del") {
        $message = $locale['shp248'];
    }
    if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}


$is_edit = false; $error = array();
if (isset($_POST['save'])) {
    $title = trim(stripinput($_POST['title']));
    $desc = trim(stripinput(strip_tags($_POST['desc'])));
    if ($title != "") {
        if ($_FILES['image']['name'] != "") {
            $image_uploaded = upload_image("image", "", AL_SHOP_DIR."asset/manufactures/", $shop_settings['photo_max_width'], $shop_settings['photo_max_height'],
                $shop_settings['max_photo_size'], true, true, false,
                0, AL_SHOP_DIR."asset/manufactures/", "_t1", $shop_settings['cat_thumb_width'], $shop_settings['cat_thumb_height']);
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
        if (isset($_POST['man_id'])) {
            if (isset($_POST['delete_image']) || $image != "") {
                $del = dbarray(dbquery("SELECT shp_manufacturer_image FROM ".DB_AL_SHOP_MANUFACTURES." WHERE shp_manufacturer_id='".$_POST['man_id']."'"));
                if ($del['shp_manufacturer_image'] != "" && file_exists(AL_SHOP_DIR."asset/manufacturers/".$del['shp_manufacturer_image'])) {
                    unlink(AL_SHOP_DIR."asset/manufacturers/".$del['shp_manufacturer_image']);
                }

            }
            $upd = dbquery("UPDATE ".DB_AL_SHOP_MANUFACTURES." SET shp_manufacturer_title='".$title."',shp_manufacturer_desc='".$desc."',shp_manufacturer_image='".$image."' WHERE shp_manufacturer_id='".$_POST['man_id']."'");

            redirect(FUSION_SELF.$aidlink."&page=manufacturers&status=su");
        } else {
            $ins = dbquery("INSERT INTO ".DB_AL_SHOP_MANUFACTURES." (shp_manufacturer_title,shp_manufacturer_desc,shp_manufacturer_image) VALUES ('".$title."','".$desc."','".$image."')");
            redirect(FUSION_SELF.$aidlink."&page=manufacturers&status=success");
        }
    } else {
        if (isset($_POST['man_id'])) {
            $man_id = $_POST['man_id'];
            $is_edit = true;
        }
    }
} else if (isset($_POST['delete'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_MANUFACTURES." WHERE shp_manufacturer_id='".$_POST['man_id']."'");
    if (dbrows($result)) {
        $del = dbquery("DELETE FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_manufacturer='".$_POST['man_id']."'");
        $del = dbquery("DELETE FROM ".DB_AL_SHOP_MANUFACTURES." WHERE shp_manufacturer_id='".$_POST['man_id']."'");
    }
    redirect(FUSION_SELF.$aidlink."&page=manufacturers&status=del");

} else if (isset($_POST['edit'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_MANUFACTURES." WHERE shp_manufacturer_id='".$_POST['man_id']."'");
    if (dbrows($result)) {
        $data = dbarray($result);
        $title = $data['shp_manufacturer_title'];
        $desc = $data['shp_manufacturer_desc'];
        $image = $data['shp_manufacturer_image'];
        $man_id = $data['shp_manufacturer_id'];
        $is_edit = true;
    } else {
        redirect(FUSION_SELF.$aidlink."&page=manufacturers");
    }

} else {
    $title = "";
    $image = "";
    $is_edit = false;
    $desc = "";
    $man_id = "";
}

opentable($locale['shp26']);
    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_MANUFACTURES);
    echo "<div style='text-align:center;width:100%;'>";
    if (dbrows($result)) {
        echo "<form action='".FUSION_SELF.$aidlink."&page=manufacturers' method='post'>";
            echo "<select name='man_id' class='textbox'>";
            while($data=dbarray($result)) {
                echo "<option value='".$data['shp_manufacturer_id']."'>".$data['shp_manufacturer_title']."</option>";
            }
            echo "</select>";
            echo "&nbsp<input type='submit' name='edit' class='button' value='".$locale['shp16']."' />";
            echo "&nbsp<input type='submit' name='delete' class='button' value='".$locale['shp17']."' />";
        echo "</form>";
    } else {
        echo $locale['shp28'];
    }
    echo "</div>";
closetable();


opentable($locale['shp27']);
echo "<form action='".FUSION_SELF.$aidlink."&page=manufacturers' method='post' enctype='multipart/form-data'>";
echo "<table width='100%' valign='top'>";
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
echo "</tr><tr valign='top'>";
echo "<td class='tbl'>".$locale['shp14']."</td>";
echo "<td class='tbl'><input type='file' name='image' class='textbox'/><br />";
if (!empty($image)) {
    echo "<input type='checkbox' name='delete_image' class='textbox' value='yes' /> ".$locale['shp25'];
    echo "<br /><img src='".AL_SHOP_DIR."asset/manufactures/".$image."' />";
}
echo "</td>";
echo "</tr><tr valign='top'>";
echo "<td class='tbl'>".$locale['shp29']."</td>";
echo "<td class='tbl'><textarea class='textbox' rows='5' style='width:250px;' name='desc'>".$desc."</textarea></td>";
echo "</tr><tr>";
echo "<td colspan='2' class='tbl'><input type='submit' class='button' name='save' value='".$locale['shp18']."' />";
if ($is_edit) {
    echo "<input type='hidden' name='man_id' value='".$man_id."' />";
    echo "&nbsp;<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=categories'>".$locale['shp19']."</a>";
}
echo "</td>";
echo "</tr>";
echo "</table>";
echo "</form>";
closetable();

?>