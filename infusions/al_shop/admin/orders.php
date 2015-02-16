<?php
require_once AL_SHOP_DIR."includes/functions.php";
add_to_title(": ".$locale['shp8']);

if (isset($_POST['change_order']) && isset($_POST['order_id']) && isnum($_POST['order_id'])) {

    if (in_array($_POST['do'],array(1,2,3,4,5))) {
        $update = dbquery("UPDATE ".DB_AL_SHOP_ORDERS." SET shp_order_finished='".$_POST['do']."' WHERE shp_order_id='".$_POST['order_id']."'");
    } else {
        switch ($_POST['do']) {
            case "finish":
                $update = dbquery("UPDATE ".DB_AL_SHOP_ORDERS." SET shp_order_finished='6' WHERE shp_order_id='".$_POST['order_id']."'");
            break;
            case "payed_1":
                $update = dbquery("UPDATE ".DB_AL_SHOP_ORDERS." SET shp_order_payed='1', shp_order_payment_type='1' WHERE shp_order_id='".$_POST['order_id']."'");
            break;
            case "payed_2":
                $update = dbquery("UPDATE ".DB_AL_SHOP_ORDERS." SET shp_order_payed='1', shp_order_payment_type='2' WHERE shp_order_id='".$_POST['order_id']."'");
            break;
            case "payed_3":
                $update = dbquery("UPDATE ".DB_AL_SHOP_ORDERS." SET shp_order_payed='1', shp_order_payment_type='3' WHERE shp_order_id='".$_POST['order_id']."'");
                break;
            case "payed_4":
                $update = dbquery("UPDATE ".DB_AL_SHOP_ORDERS." SET shp_order_payed='1', shp_order_payment_type='4' WHERE shp_order_id='".$_POST['order_id']."'");
                break;
            case "unpayed":
                $update = dbquery("UPDATE ".DB_AL_SHOP_ORDERS." SET shp_order_payed='0', shp_order_payment_type='0' WHERE shp_order_id='".$_POST['order_id']."'");
            break;
        }
    }
    redirect(FUSION_SELF.$aidlink."&page=orders&id=".$_POST['order_id']);

}

if (isset($_POST['change_note'])) {
    $result = dbquery("UPDATE ".DB_AL_SHOP_ORDERS." SET shp_order_admin_note='".trim(stripinput($_POST['admin_note']))."' WHERE shp_order_id='".$_POST['order_id']."'");
    redirect(FUSION_SELF.$aidlink."&page=orders&id=".$_POST['order_id']);
}

if (isset($_GET['id']) && isnum($_GET['id'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_id='".$_GET['id']."'");
    if (dbrows($result)) {
        $data = dbarray($result);

        opentable($locale['shp182']);
            echo "<table width='100%'>";
                echo "<tr>";
                    echo "<td class='tbl' width='200'>".$locale['shp124']."</td>";
                    echo "<td class='tbl'>#".$data['shp_order_id']."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td class='tbl' width='200'>".$locale['shp125']."</td>";
                    echo "<td class='tbl'>".show_payed_status($data['shp_order_payed'])." ".show_payment_type($data['shp_order_payment_type'])."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td class='tbl' width='200'>".$locale['shp125-2']."</td>";
                    echo "<td class='tbl'>".$locale['shp_status_'.$data['shp_order_finished']]."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td class='tbl' width='200'>".$locale['shp171']."</td>";
                    echo "<td class='tbl'>".show_cost($data['shp_order_cost'],$shop_settings['currency_default'],$shop_settings['currency_default'])."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td class='tbl' width='200'>".$locale['shp174']."</td>";
                    echo "<td class='tbl'>".showdate("forumdate",$data['shp_order_datestamp'])."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td class='tbl' width='200'>".$locale['shp264']."</td>";
                    echo "<td class='tbl'>".show_delivery($data['shp_order_delivery'])."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td class='tbl' width='200'>".$locale['shp185']."</td>";
                    echo "<td class='tbl'>";
                        $orders_arr = explode("||",$data['shp_order_str']);
                        foreach ($orders_arr as $order_str) {
                            list ($good_data,$params_data) = explode('|',$order_str);
                            list($good_id,$amount) = explode("_",$good_data);
                            $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_id='".$good_id."'");
                            if (dbrows($result)) {
                                $good = dbarray($result);
                                echo $amount." x <a href='".BASEDIR."shop.php?action=good&id=".$good['shp_good_id']."'>".$good['shp_good_title']."</a> ";
                            }
                            if (!empty($params_data)) {
                                foreach (explode('.',$params_data) as $param_data) {
                                    list($param_id,$value_id) = explode('_',$param_data);

                                    $result11 = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAMS." WHERE shp_param_id='".$param_id."'");
                                    $result22 = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAM_VALUES." WHERE shp_value_id='".$value_id."'");
                                    if (dbrows($result11) && dbrows($result22)) {
                                        $param_data = dbarray($result11);
                                        $value_data = dbarray($result22);
                                        $params[$param_data['shp_param_name']] = $value_data['shp_value_data'];
                                    }
                                }
                                echo "<i class='small'>(";
                                $i = 1;
                                foreach ($params as $param=>$value) {
                                    echo $param.": ".$value.($i < count($params) ? ", " : "");
                                    $i++;
                                }
                                echo ")</i>";
                            }
                            echo "<br />";
                        }
                    echo "</td>";
                echo "</tr>";
            echo "</table>";
        closetable();

        if ($data['shp_order_finished'] != 6) {
            opentable($locale['shp186']);
                echo "<div style='width:100%;text-align:center;'><form method='post'>";
                    echo $locale['shp309'];
                    echo "<select name='do' class='textbox'>";
                    if ($data['shp_order_finished'] != 6) {
                        echo "<option value='finish'>".$locale['shp187']."</option>";
                    }
                    if ($data['shp_order_payed'] == 0) {
                        echo "<option value='payed_2'>".$locale['shp188']."</option>";
                        echo "<option value='payed_1'>".$locale['shp189']."</option>";
                        echo "<option value='payed_3'>".$locale['shp293']."</option>";
                        echo "<option value='payed_4'>".$locale['shp294']."</option>";
                    } else {
                        echo "<option value='unpayed'>".$locale['shp190']."</option>";
                    }
                    echo "<option value='1'>".$locale['shp220']."</option>";
                    echo "<option value='2'>".$locale['shp221']."</option>";
                    echo "<option value='3'>".$locale['shp222']."</option>";
                    echo "<option value='4'>".$locale['shp223']."</option>";
                    echo "<option value='5'>".$locale['shp224']."</option>";
                echo "</select> <input type='submit' class='button' name='change_order' value='".$locale['shp191']."' /><input type='hidden' name='order_id' value='".$data['shp_order_id']."' /></form></div>";
            closetable();
        }

        opentable($locale['shp312']);
            echo "<div style='width:100%;text-align:center;'><form method='post'>";
            echo "<textarea name='admin_note' class='textbox' cols='90' rows='5'>".$data['shp_order_admin_note']."</textarea>";
            echo "<br /><input type='submit' class='button' name='change_note' value='".$locale['shp18']."' /><input type='hidden' name='order_id' value='".$data['shp_order_id']."' /></form></div>";
        closetable();

        opentable($locale['shp184']);
        echo "<table width='100%'>";
            echo "<tr>";
                echo "<td class='tbl' width='200'></td>";
                echo "<td class='tbl'>".($data['shp_order_type'] == 1 ? $locale['shp112'] : $locale['shp113'])."</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td class='tbl' width='200'>".$locale['shp114']."</td>";
                echo "<td class='tbl'>".$data['shp_order_fio']."</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td class='tbl' width='200'>".$locale['shp115']."</td>";
                echo "<td class='tbl'>".$data['shp_order_address']."</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td class='tbl' width='200'>".$locale['shp116']."</td>";
                echo "<td class='tbl'>".$data['shp_order_email']."</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td class='tbl' width='200'>".$locale['shp117']."</td>";
                echo "<td class='tbl'>".$data['shp_order_phone']."</td>";
            echo "</tr>";
            if ($data['shp_order_type'] == 2) {
                echo "<tr>";
                    echo "<td class='tbl' width='200'>".$locale['shp119']."</td>";
                    echo "<td class='tbl'>".$data['shp_order_company']."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td class='tbl' width='200'>".$locale['shp120']."</td>";
                    echo "<td class='tbl'>".$data['shp_order_inn']."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td class='tbl' width='200'>".$locale['shp121']."</td>";
                    echo "<td class='tbl'>".$data['shp_order_kpp']."</td>";
                echo "</tr>";
            }
            echo "<tr>";
                echo "<td class='tbl' width='200'>".$locale['shp311']."</td>";
                echo "<td class='tbl'>".$data['shp_order_note']."</td>";
            echo "</tr>";
        echo "</table>";
        closetable();

    } else {
        redirect(FUSION_SELF.$aidlink."&page=orders");
    }

} else if (isset($_GET['delete']) && isnum($_GET['delete'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_id='".$_GET['delete']."'");
    if (dbrows($result)) {
        $del = dbquery("DELETE FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_id='".$_GET['delete']."'");
    }
    redirect(FUSION_SELF.$aidlink."&page=orders");

} else if (isset($_GET['finished'])) {


    opentable($locale['shp180']);

    if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) $_GET['rowstart'] = 0;

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_finished='6' ORDER BY shp_order_id DESC LIMIT ".$_GET['rowstart'].",30");
    if (dbrows($result)) {

        $count = dbcount("(shp_order_id)",DB_AL_SHOP_ORDERS,"shp_order_finished='6'");

        echo "<table width='100%'>";
        echo "<tr>";
            echo "<td class='tbl2' width='1%'><strong>#</strong></td>";
            echo "<td class='tbl2'><strong>".$locale['shp175']."</strong></td>";
            echo "<td class='tbl2'><strong>".$locale['shp170']."</strong></td>";
            echo "<td class='tbl2'><strong>".$locale['shp174']."</strong></td>";
            echo "<td class='tbl2'><strong>".$locale['shp171']."</strong></td>";
            echo "<td class='tbl2'><strong>".$locale['shp172']."</strong></td>";
        echo "</tr>";
        while ($data=dbarray($result)) {
            echo "<tr>";
                echo "<td class='tbl'>".$data['shp_order_id']."</td>";
                echo "<td class='tbl'>".($data['shp_order_type'] == 1 ? $data['shp_order_fio'].$locale['shp176'] : $data['shp_order_company'].$locale['shp177'])."</td>";
                echo "<td class='tbl'>".show_payed_status($data['shp_order_payed'])."</td>";
                echo "<td class='tbl'>".showdate("forumdate",$data['shp_order_datestamp'])."</td>";
                echo "<td class='tbl'>".show_cost($data['shp_order_cost'],$shop_settings['currency_default'],$shop_settings['currency_default'])."</td>";
                echo "<td class='tbl'><a href='".FUSION_SELF.$aidlink."&page=orders&id=".$data['shp_order_id']."'>".$locale['shp178']."</a> <a href='".FUSION_SELF.$aidlink."&page=orders&delete=".$data['shp_order_id']."' onclick='return confirm(\"".$locale['shp137']."\");'>".$locale['shp179']."</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        if ($count > 30) {
            echo makepagenav($_GET['rowstart'], 30, $count, 3, FUSION_SELF.$aidlink."&page=orders&finished&");
        }

    } else {
        echo $locale['shp169'];
    }
    echo "<br /><br /><a class='shop-button' href='".FUSION_SELF.$aidlink."&page=orders'>".$locale['shp181']."</a>";
    closetable();

} else {


    opentable($locale['shp168']);

        if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) $_GET['rowstart'] = 0;

        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_finished<>'6' ORDER BY shp_order_id DESC LIMIT ".$_GET['rowstart'].",30");
        if (dbrows($result)) {

            $count = dbcount("(shp_order_id)",DB_AL_SHOP_ORDERS,"shp_order_finished<>'6'");

            echo "<table width='100%'>";
                echo "<tr>";
                    echo "<td class='tbl2' width='1%'><strong>#</strong></td>";
                    echo "<td class='tbl2'><strong>".$locale['shp175']."</strong></td>";
                    echo "<td class='tbl2'><strong>".$locale['shp170']."</strong></td>";
                    echo "<td class='tbl2'><strong>".$locale['shp170-2']."</strong></td>";
                    echo "<td class='tbl2'><strong>".$locale['shp174']."</strong></td>";
                    echo "<td class='tbl2'><strong>".$locale['shp171']."</strong></td>";
                    echo "<td class='tbl2'><strong>".$locale['shp172']."</strong></td>";
                echo "</tr>";
                while ($data=dbarray($result)) {
                    echo "<tr>";
                        echo "<td class='tbl'>".$data['shp_order_id']."</td>";
                        echo "<td class='tbl'>".($data['shp_order_type'] == 1 ? $data['shp_order_fio'].$locale['shp176'] : $data['shp_order_company'].$locale['shp177'])."</td>";
                        echo "<td class='tbl'>".show_payed_status($data['shp_order_payed'])."</td>";
                        echo "<td class='tbl'>".$locale['shp_status_'.$data['shp_order_finished']]."</td>";
                        echo "<td class='tbl'>".showdate("forumdate",$data['shp_order_datestamp'])."</td>";
                        echo "<td class='tbl'>".show_cost($data['shp_order_cost'],$shop_settings['currency_default'],$shop_settings['currency_default'])."</td>";
                        echo "<td class='tbl'><a href='".FUSION_SELF.$aidlink."&page=orders&id=".$data['shp_order_id']."'>".$locale['shp178']."</a> <a href='".FUSION_SELF.$aidlink."&page=orders&delete=".$data['shp_order_id']."' onclick='return confirm(\"".$locale['shp137']."\");'>".$locale['shp179']."</a></td>";
                    echo "</tr>";
                }
            echo "</table>";
            if ($count > 30) {
                echo makepagenav($_GET['rowstart'], 30, $count, 3, FUSION_SELF.$aidlink."&page=orders&");
            }

        } else {
            echo $locale['shp169'];
        }
    echo "<br /><br /><a class='shop-button' href='".FUSION_SELF.$aidlink."&page=orders&finished'>".$locale['shp180']."</a><br />";
    closetable();
}

?>