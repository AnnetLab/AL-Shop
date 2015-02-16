<?php
require_once AL_SHOP_DIR."includes/functions.php";
add_to_title(": ".$locale['shp328']);

$errors = array();

if (isset($_GET['status']) && !isset($message)) {
    if ($_GET['status'] == "success") {
        $message = $locale['shp339'];
    } elseif ($_GET['status'] == "su") {
        $message = $locale['shp340'];
    } elseif ($_GET['status'] == "del") {
        $message = $locale['shp341'];
    }
    if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}

if (isset($_POST['save'])) {

    $param_name = trim(stripinput($_POST['param_name']));
    $param_type = trim(stripinput($_POST['param_type']));

    if (empty($param_name)) $errors[] = $locale['shp338'];

    //var_dump($_POST); die;

    if (empty($errors)) {
        if (isset($_POST['param_id']) && isnum($_POST['param_id'])) {

            $result = dbquery("UPDATE ".DB_AL_SHOP_GOOD_PARAMS." SET shp_param_name='".$param_name."',shp_param_type='".$param_type."' WHERE shp_param_id='".$_POST['param_id']."'");
            if ($param_type == 'select') {
                if (isset($_POST['param_values']) && !empty($_POST['param_values'])) {
                    foreach ($_POST['param_values'] as $row) {
                        $value_value = trim(stripinput($row));
                        if (!empty($value_value)) {
                            $result = dbquery("INSERT INTO ".DB_AL_SHOP_GOOD_PARAM_VALUES." (shp_value_param_id,shp_value_data) VALUES ('".$_POST['param_id']."','".$value_value."')");
                        }
                    }
                }
                if (isset($_POST['param_ex_values']) && !empty($_POST['param_ex_values'])) {
                    foreach ($_POST['param_ex_values'] as $key=>$row) {
                        $value_value = trim(stripinput($row));
                        if (!empty($value_value)) {
                            $result = dbquery("UPDATE ".DB_AL_SHOP_GOOD_PARAM_VALUES." SET shp_value_data='".$value_value."' WHERE shp_value_id='".$key."'");
                        } else {
                            $result = dbquery("DELETE FROM ".DB_AL_SHOP_GOOD_PARAM_VALUES." WHERE shp_value_id='".$key."'");
                        }
                    }
                }
            } else {
                // delete values
                $result = dbquery("DELETE * FROM ".DB_AL_SHOP_GOOD_PARAM_VALUES." WHERE shp_value_param_id='".$_POST['param_id']."'");
            }
            redirect(FUSION_SELF.$aidlink."&page=params&status=su");

        } else {
            $result = dbquery("INSERT INTO ".DB_AL_SHOP_GOOD_PARAMS." (shp_param_name,shp_param_type) VALUES ('".$param_name."','".$param_type."')");
            $new_param_id = mysql_insert_id();
            if ($param_type == 'select') {
                if (isset($_POST['param_values']) && !empty($_POST['param_values'])) {
                    foreach ($_POST['param_values'] as $row) {
                        $value_value = trim(stripinput($row));
                        if (!empty($value_value)) {
                            $result = dbquery("INSERT INTO ".DB_AL_SHOP_GOOD_PARAM_VALUES." (shp_value_param_id,shp_value_data) VALUES ('".$new_param_id."','".$value_value."')");
                        }
                    }
                }
            }
            redirect(FUSION_SELF.$aidlink."&page=params&status=success");
        }
    }

} else if (isset($_POST['edit'])) {

    if (isset($_POST['param_id']) && isnum($_POST['param_id'])) {
        $param_id = $_POST['param_id'];
        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAMS." WHERE shp_param_id='".$_POST['param_id']."'");
        if (dbrows($result)) {
            $data = dbarray($result);
            $param_name = $data['shp_param_name'];
            $param_type = $data['shp_param_type'];
            $is_edit = true;
            if ($param_type == 'select') {
                $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAM_VALUES." WHERE shp_value_param_id='".$data['shp_param_id']."'");
                if (dbrows($result)) {
                    $param_values = array();
                    while ($data2 = dbarray($result)) {
                        $param_values[] = array('id'=>$data2['shp_value_id'],'value'=>$data2['shp_value_data']);
                    }
                }
            }
        } else {
            redirect(FUSION_SELF.$aidlink."&page=params");
        }
    } else {
        redirect(FUSION_SELF.$aidlink."&page=params");
    }

} else if (isset($_POST['delete'])) {

    if (isset($_POST['param_id']) && isnum($_POST['param_id'])) {
        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAMS." WHERE shp_param_id='".$_POST['param_id']."'");
        if (dbrows($result)) {
            $result = dbquery("DELETE FROM ".DB_AL_SHOP_GOOD_PARAMS." WHERE shp_param_id='".$_POST['param_id']."'");
            $result = dbquery("DELETE FROM ".DB_AL_SHOP_GOOD_PARAMS." WHERE shp_value_param_id='".$_POST['param_id']."'");
        }
        redirect(FUSION_SELF.$aidlink."&page=params&status=del");
    }
    redirect(FUSION_SELF.$aidlink."&page=params");

} else {
    $param_name = '';
    $param_type = 'text';
    $param_values = array();
    $is_edit = false;
}






opentable($locale['shp328']);
    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAMS);
    if (dbrows($result)) {
        echo "<form action='".FUSION_SELF.$aidlink."&page=params' method='post'>";
        echo "<div style='width:100%;text-align:center;'>";
            echo "<select name='param_id'>";
            while ($data = dbarray($result)) {
                $type = $data['shp_param_type'] == 'text' ? '[T]' : '[S]';
                echo "<option value='".$data['shp_param_id']."'>".$type." ".$data['shp_param_name']."</option>";
            }
            echo "</select>";
        echo "&nbsp;<input type='submit' name='edit' value='".$locale['shp16']."' class='button' />";
        echo "&nbsp;<input type='submit' name='delete' value='".$locale['shp17']."' class='button' />";
        echo "</div>";
        echo "</form>";
    } else {
        echo $locale['shp330'];
    }
closetable();


opentable($locale['shp331']);

    echo "<form action='".FUSION_SELF.$aidlink."&page=params' method='post'>";
    echo "<table width='100%'>";
        echo "<tr>";
            echo "<td class='tbl' width='150'>".$locale['shp332']."</td>";
            echo "<td class='tbl'><input type='text' name='param_name' class='textbox' value='".$param_name."' style='width:250px;' /></td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td class='tbl' width='150'>".$locale['shp333']."</td>";
            echo "<td class='tbl'>";
                echo "<select name='param_type' id='change_param'>";
                    echo "<option value='text'".($param_type == 'text' ? " selected='selected'" : "").">".$locale['shp334']."</option>";
                    echo "<option value='select'".($param_type == 'select' ? " selected='selected'" : "").">".$locale['shp335']."</option>";
                echo "</select>";
            echo "</td>";
        echo "</tr>";
        echo "<tr valign='top' id='param_options'".($param_type == 'text' ? " style='display:none;'" : "").">";
            echo "<td class='tbl' width='150'>".$locale['shp336']."</td>";
            echo "<td class='tbl'>";
                if (!empty($param_values)) {
                    foreach ($param_values as $param_value) {
                        echo "<div><input type='text' class='textbox' name='param_ex_values[".$param_value['id']."]' value='".$param_value['value']."' /> <a href='#' class='del-ex-param' data-param-id='".$param_value['id']."'></a></div>";
                    }
                }

                echo "<div id='prepender'></div>";
                echo "<a href='#' id='add_value'>".$locale['shp337']."</a>";
            echo "</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td class='tbl'></td>";
            echo "<td class='tbl'><input type='submit' class='button' name='save' value='".$locale['shp18']."' />";
                if ($is_edit) {
                    echo "<input type='hidden' name='param_id' value='".$param_id."' />";
                }
            echo "</td>";
        echo "</tr>";
    echo "</table>";
    echo "</form>";

closetable();

echo "<script>
    var baseurl = '".AL_SHOP_DIR."';
</script>";
add_to_head("<script src='".AL_SHOP_DIR."asset/js/params.js'></script>");

?>