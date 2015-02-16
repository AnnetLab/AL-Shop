<?php

$shop_settings['guest_checkout'] = 1;

if (isset($_POST['delete'])) {
    if (!empty($_POST['goods'])) {
        foreach ($_POST['goods'] as $basket_id) {
            $result = dbquery("SELECT * FROM ".DB_AL_SHOP_BASKET." WHERE shp_basket_id='".$basket_id."'");
            if (dbrows($result)) {
                $data = dbarray($result);
                if ((iMEMBER && $data['shp_basket_user'] == $userdata['user_id']) || (FUSION_IP == $data['shp_basket_ip'])) {
                    $del = dbquery("DELETE FROM ".DB_AL_SHOP_BASKET." WHERE shp_basket_id='".$data['shp_basket_id']."'");
                }
            }
        }
        redirect(FUSION_SELF."?action=cart");
    } else {
        redirect(FUSION_SELF."?action=cart");
    }
}

if (isset($_POST['order'])) {

    $basket_ids = explode("|",$_POST['basket_ids']);
    $total_cost = $_POST['total_cost'];
    $orders_str = $_POST['orders_str'];
    if ($_POST['order_choose'] == "fizlico") {
        $type = 1;
        $fio = trim(stripinput($_POST['fizlico_fio']));
        $address = trim(stripinput($_POST['fizlico_address']));
        $email = trim(stripinput($_POST['fizlico_email']));
        $phone = trim(stripinput($_POST['fizlico_phone']));
        $company = "";
        $inn = "";
        $kpp = "";
        $note = trim(stripinput($_POST['fizlico_note']));
        if (iMEMBER) {
            $client_data = json_encode(array('type'=>$type,'fio'=>$fio,'address'=>$address,'email'=>$email,'phone'=>$phone));
        }
    } else {
        $type = 2;
        $fio = trim(stripinput($_POST['urlico_fio']));
        $address = trim(stripinput($_POST['urlico_address']));
        $email = trim(stripinput($_POST['urlico_email']));
        $phone = trim(stripinput($_POST['urlico_phone']));
        $company = trim(stripinput($_POST['urlico_company']));
        $inn = trim(stripinput($_POST['urlico_inn']));
        $kpp = trim(stripinput($_POST['urlico_kpp']));
        $note = trim(stripinput($_POST['urlico_note']));
        if (iMEMBER) {
            $client_data = json_encode(array('type'=>$type,'fio'=>$fio,'address'=>$address,'email'=>$email,'phone'=>$phone,'company'=>$company,'inn'=>$inn,'kpp'=>$kpp));
        }
    }
    if (iMEMBER) {
        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CLIENTS_DATA." WHERE shp_client_user='".$userdata['user_id']."'");
        if (dbrows($result)) {
            $result = dbquery("UPDATE ".DB_AL_SHOP_CLIENTS_DATA." SET shp_client_data='".$client_data."' WHERE shp_client_user='".$userdata['user_id']."'");
        } else {
            $result = dbquery("INSERT INTO ".DB_AL_SHOP_CLIENTS_DATA." (shp_client_user,shp_client_data) VALUES ('".$userdata['user_id']."','".$client_data."')");
        }
    }

    $delivery = isset($_POST['delivery']) && isnum($_POST['delivery']) ? $_POST['delivery'] : 0;
    $user_id = iMEMBER ? $userdata['user_id'] : 0;
    $insert = dbquery("INSERT INTO ".DB_AL_SHOP_ORDERS." (shp_order_cost,shp_order_str,shp_order_type,shp_order_fio,shp_order_address,shp_order_email,shp_order_phone,shp_order_company,shp_order_inn,shp_order_kpp,shp_order_payed,shp_order_payment_type,shp_order_datestamp,shp_order_user,shp_order_ip,shp_order_finished,shp_order_delivery,shp_order_note) VALUES ('".$total_cost."','".$orders_str."','".$type."','".$fio."','".$address."','".$email."','".$phone."','".$company."','".$inn."','".$kpp."','0','0','".time()."','".$user_id."','".FUSION_IP."','4','".$delivery."','".$note."')");
    if ($insert) {
        $order_id = mysql_insert_id();
        foreach ($basket_ids as $basket_id) {
            $del = dbquery("DELETE FROM ".DB_AL_SHOP_BASKET." WHERE shp_basket_id='".$basket_id."'");
        }
        require_once INCLUDES."sendmail_include.php";
        $subject = $settings['sitename'];
        $message_owner = sprintf($locale['shp215'],$order_id);
        $message_buyer = sprintf($locale['shp216'],show_cost($total_cost,$shop_settings['currency_default'],$shop_settings['currency_default']),$order_id,$order_id);
        sendemail($fio, $email, $settings['siteusername'], $settings['siteemail'], $subject, $message_buyer);
        sendemail($settings['siteusername'], $settings['siteemail'], $settings['siteusername'], $settings['siteemail'], $subject, $message_owner);


        redirect(FUSION_SELF."?action=order&id=".$order_id);
    }
    redirect(FUSION_SELF."?action=cart");

}


if (isset($_POST['checkout'])) {

    if (!empty($_POST['goods'])) {
        if ($shop_settings['guest_checkout'] == 0 && !iMEMBER) redirect(BASEDIR."login.php?redirect=".$settings['siteurl']."?shop.php?action=cart");

        set_title($locale['shp231']." | ".$settings['sitename']);
        $orders_str = ""; $total_cost = 0; $basket_ids = "";
        foreach ($_POST['goods'] as $basket_id) {
            if (iMEMBER) {
                $result = dbquery("SELECT b.*,g.* FROM ".DB_AL_SHOP_BASKET." b LEFT JOIN ".DB_AL_SHOP_GOODS." g ON g.shp_good_id=b.shp_basket_good WHERE shp_basket_id='".$basket_id."' AND (shp_basket_user='".$userdata['user_id']."' OR shp_basket_ip='".FUSION_IP."')");
            } else {
                $result = dbquery("SELECT b.*,g.* FROM ".DB_AL_SHOP_BASKET." b LEFT JOIN ".DB_AL_SHOP_GOODS." g ON g.shp_good_id=b.shp_basket_good WHERE shp_basket_id='".$basket_id."' AND shp_basket_ip='".FUSION_IP."'");
            }
            if (dbrows($result) && isset($_POST['amount'][$basket_id]) && isnum($_POST['amount'][$basket_id]) && $_POST['amount'][$basket_id] > 0) {
                $data = dbarray($result);
                $update = dbquery("UPDATE ".DB_AL_SHOP_GOODS." SET shp_good_buys=shp_good_buys+".$_POST['amount'][$basket_id]." WHERE shp_good_id='".$data['shp_good_id']."'");
                $basket_ids = $basket_ids != "" ? $basket_ids."|".$basket_id : $basket_id;
                // params to array_merge
                $params_str = '';
                if ($data['shp_basket_good_params'] != '') {
                    $param_pairs = explode('||',$data['shp_basket_good_params']);
                    foreach ($param_pairs as $param_pair) {
                        list($param_id,$value_id) = explode('-',$param_pair);
                        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAMS." WHERE shp_param_id='".$param_id."'");
                        $result2 = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAM_VALUES." WHERE shp_value_id='".$value_id."'");
                        if (dbrows($result) && dbrows($result2)) {
                            $param_data = dbarray($result);
                            $value_data = dbarray($result2);
                            $data['params'][$param_data['shp_param_name']] = $value_data['shp_value_data'];
                            $params_str = !empty($params_str) ? $params_str.".".$param_id."_".$value_id : $param_id."_".$value_id;
                        }
                    }
                }
                $goods[] = array_merge($data,array('amount'=>$_POST['amount'][$basket_id]));
                $orders_str = $orders_str != "" ? $orders_str."||" : $orders_str;
                // params to orders_str
                //$orders_str .= $data['shp_good_id']."_".$_POST['amount'][$basket_id];
                $orders_str .= !empty($params_str) ? $data['shp_good_id']."_".$_POST['amount'][$basket_id]."|".$params_str : $data['shp_good_id']."_".$_POST['amount'][$basket_id];
                $total_cost = $total_cost + convert_cost($data['shp_good_cost']*$_POST['amount'][$basket_id],$data['shp_good_currency'],$shop_settings['currency_default']);
                $deliveries = make_assoc(dbquery("SELECT * FROM ".DB_AL_SHOP_DELIVERIES));
                $client_data = get_client_data();
            }

        }

        if ($orders_str != "") {

            require_once AL_SHOP_TPL_DIR."checkout.php";

        } else {
            redirect(FUSION_SELF."?action=cart");
        }

    } else {
        redirect(FUSION_SELF."?action=cart");
    }

}

?>