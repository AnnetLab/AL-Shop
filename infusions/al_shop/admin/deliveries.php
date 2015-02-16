<?php
require_once AL_SHOP_DIR."includes/functions.php";
require_once INCLUDES."infusions_include.php";
add_to_title(": ".$locale['shp300']);

if (isset($_GET['status']) && !isset($message)) {
    if ($_GET['status'] == "success") {
        $message = $locale['shp302'];
    } elseif ($_GET['status'] == "su") {
        $message = $locale['shp303'];
    } elseif ($_GET['status'] == "del") {
        $message = $locale['shp304'];
    }
    if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
}

$result = dbquery("SELECT * FROM ".DB_AL_SHOP_DELIVERIES);
if (dbrows($result)) {
    while ($data = dbarray($result)) {
        $deliveries[$data['shp_delivery_id']] = $data;
    }
} else {
    $deliveries = null;
}

$is_edit = false; $error = array();
if (isset($_POST['save'])) {
    $title = trim(stripinput($_POST['title']));
    $cost = isset($_POST['cost']) && floatval($_POST['cost']) ? floatval($_POST['cost']) : 0;
    $currency = $_POST['currency'];
    if ($title == "") {
        $error[] = $locale['shp20'];
    } else if (!is_numeric($cost)) {
        $error[] = $locale['shp305'];
    }
    if (empty($error)) {
        if (isset($_POST['delivery_id'])) {
            $upd = dbquery("UPDATE ".DB_AL_SHOP_DELIVERIES." SET shp_delivery_title='".$title."',shp_delivery_cost='".$cost."',shp_delivery_currency='".$currency."' WHERE shp_delivery_id='".$_POST['delivery_id']."'");

            redirect(FUSION_SELF.$aidlink."&page=deliveries&status=su");
        } else {
            $ins = dbquery("INSERT INTO ".DB_AL_SHOP_DELIVERIES." (shp_delivery_title,shp_delivery_cost,shp_delivery_currency) VALUES ('".$title."','".$cost."','".$currency."')");
            redirect(FUSION_SELF.$aidlink."&page=deliveries&status=success");
        }
    } else {
        if (isset($_POST['delivery_id'])) {
            $delivery_id = $_POST['delivery_id'];
            $is_edit = true;
        }
    }
} else if (isset($_POST['delete'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_DELIVERIES." WHERE shp_delivery_id='".$_POST['delivery_id']."'");
    if (dbrows($result)) {
        $del = dbquery("DELETE FROM ".DB_AL_SHOP_DELIVERIES." WHERE shp_delivery_id='".$_POST['delivery_id']."'");
    }
    redirect(FUSION_SELF.$aidlink."&page=deliveries&status=del");

} else if (isset($_POST['edit'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_DELIVERIES." WHERE shp_delivery_id='".$_POST['delivery_id']."'");
    if (dbrows($result)) {
        $data = dbarray($result);
        $title = $data['shp_delivery_title'];
        $cost = $data['shp_delivery_cost'];
        $currency = $data['shp_delivery_currency'];
        $delivery_id = $data['shp_delivery_id'];
        $is_edit = true;
    } else {
        redirect(FUSION_SELF.$aidlink."&page=delivery");
    }

} else {
    $title = "";
    $cost = "";
    $is_edit = false;
    $currency = "USD";
    $delivery_id = "";
}

opentable($locale['shp300']);
$result = dbquery("SELECT * FROM ".DB_AL_SHOP_DELIVERIES);
echo "<div style='text-align:center;width:100%;'>";
if (dbrows($result)) {
    echo "<form action='".FUSION_SELF.$aidlink."&page=deliveries' method='post'>";
    echo "<select name='delivery_id' class='textbox'>";
    while($data=dbarray($result)) {
        echo "<option value='".$data['shp_delivery_id']."'>".$data['shp_delivery_title']."</option>";
    }
    echo "</select>";
    echo "&nbsp<input type='submit' name='edit' class='button' value='".$locale['shp16']."' />";
    echo "&nbsp<input type='submit' name='delete' class='button' value='".$locale['shp17']."' />";
    echo "</form>";
} else {
    echo $locale['shp307'];
}
echo "</div>";
closetable();


opentable($locale['shp306']);
echo "<form action='".FUSION_SELF.$aidlink."&page=deliveries' method='post'>";
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
echo "</tr><tr>";
echo "<td colspan='2' class='tbl'><input type='submit' class='button' name='save' value='".$locale['shp18']."' />";
if ($is_edit) {
    echo "<input type='hidden' name='delivery_id' value='".$delivery_id."' />";
    echo "&nbsp;<a class='shop-button' href='".FUSION_SELF.$aidlink."&page=deliveries'>".$locale['shp19']."</a>";
}
echo "</td>";
echo "</tr>";
echo "</table>";
echo "</form>";
closetable();

?>