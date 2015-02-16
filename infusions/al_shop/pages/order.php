<?php

if (isset($_POST['save_nal']) && isset($_POST['order_id']) && isnum($_POST['order_id'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_id='".$_POST['order_id']."'");
    if (dbrows($result)) {
        $update = dbquery("UPDATE ".DB_AL_SHOP_ORDERS." SET shp_order_payed='1', shp_order_payment_type='4' WHERE shp_order_id='".$_POST['order_id']."'");
    }
    redirect(FUSION_SELF."?action=order&id=".$_POST['order_id']);

} else if (isset($_GET['delete']) && isnum($_GET['delete'])) {
    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_id='".$_GET['delete']."'");
    if (dbrows($result)) {
        $data = dbarray($result);
        if ($data['shp_order_finished'] == 6 || (iMEMBER && $data['shp_order_user'] != $userdata['user_id']) || (!iMEMBER && $data['shp_order_ip'] != FUSION_IP)) redirect(FUSION_SELF."?action=cart");
        $del = dbquery("DELETE FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_id='".$data['shp_order_id']."'");
        redirect(FUSION_SELF."?action=cart");
    }
} else if (isset($_GET['id']) && isnum($_GET['id'])) {
    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_id='".$_GET['id']."'");

    if (dbrows($result)) {

        $data = dbarray($result);
        set_title($locale['shp232']." | ".$settings['sitename']);
        if ($data['shp_order_finished'] == 6 || (iMEMBER && $data['shp_order_user'] != $userdata['user_id']) || (!iMEMBER && $data['shp_order_ip'] != FUSION_IP)) redirect(FUSION_SELF."?action=cart");
        opentable($locale['shp123']);
            echo "<table width='100%'>";
                echo "<tr>";
                    echo "<td class='tbl' width='200'>".$locale['shp124']."</td>";
                    echo "<td class='tbl'>#".$data['shp_order_id']." ".($data['shp_order_payed'] == 0 ? "<a class='shop-button' href='".FUSION_SELF."?action=order&delete=".$data['shp_order_id']."' onclick='return confirm(\"".$locale['shp137']."\");'>".$locale['shp136']."</a>" : "")."</td>";
                echo "</tr><tr>";
                    echo "<td class='tbl'>".$locale['shp125']."</td>";
                    echo "<td class='tbl'>".($data['shp_order_payed'] == 1 ? $locale['shp126'] : $locale['shp127'])."</td>";
                echo "</tr>";
                echo "</tr><tr>";
                    echo "<td class='tbl'>".$locale['shp125-2']."</td>";
                    echo "<td class='tbl'>".$locale['shp_status_'.$data['shp_order_finished']]."</td>";
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
                if ($data['shp_order_payed'] == 0) {
                    echo "<tr>";
                        echo "<td class='tbl'>".$locale['shp128']."</td>";
                        echo "<td class='tbl'>".show_cost($data['shp_order_cost'],$shop_settings['currency_default'],$shop_settings['currency_default'])."</td>";
                    echo "</tr><tr valign='top'>";
                        echo "<td class='tbl'>".$locale['shp129']."</td>";
                        echo "<td class='tbl'><select name='payment_choose' class='textbox'>
                            <option value='0'>".$locale['shp130']."</option>";
                            if ($shop_settings['beznal_enabled'] == 1) {
                                echo "<option value='1'>".$locale['shp131']."</option>";
                            }
                            if ($shop_settings['robokassa_enabled'] == 1) {
                                echo "<option value='2'>".$locale['shp132']."</option>";
                            }
                            if ($shop_settings['paypal_enabled'] == 1) {
                                echo "<option value='3'>".$locale['shp286']."</option>";
                            }
                            if ($shop_settings['nal_enabled'] == 1) {
                                echo "<option value='4'>".$locale['shp298']."</option>";
                            }
                        echo "</select>
                        <div id='beznal'><br /><a class='shop-button' href='".BASEDIR."print_check.php?id=".$data['shp_order_id']."' target='_blank'>".$locale['shp135']."</a></div>
                        <div id='robokassa'>";



                            $crc = md5($shop_settings['mrh_login'].":".convert_cost($data['shp_order_cost'],$shop_settings['currency_default'],"RUR").":".$data['shp_order_id'].":".$shop_settings['mrh_pass1']);

                            //echo "<br /><script language='JavaScript' src='https://merchant.roboxchange.com/Handler/MrchSumPreview.ashx?MrchLogin=".$shop_settings['mrh_login']."&OutSum=".$data['shp_order_cost']."&InvId=".$data['shp_order_id']."&IncCurrLabel=&Desc=&SignatureValue=".$crc."&Culture=ru&Encoding=windows-1251'></script>";
                            //echo "<br /><script language='JavaScript' src='http://test.robokassa.com/Handler/Index.aspx?MrchLogin=".$shop_settings['mrh_login']."&OutSum=".$data['shp_order_cost']."&InvId=".$data['shp_order_id']."&IncCurrLabel=&Desc=&SignatureValue=".$crc."&Culture=ru&Encoding=windows-1251'></script>";
                            echo "<br /><a class='shop-button' href='https://merchant.roboxchange.com/Index.aspx?MrchLogin=".$shop_settings['mrh_login']."&OutSum=".convert_cost($data['shp_order_cost'],$shop_settings['currency_default'],"RUR")."&InvId=".$data['shp_order_id']."&IncCurrLabel=WMRM&Desc=&SignatureValue=".$crc."&Culture=ru&Encoding=".$locale['charset']."'>".$locale['shp285']."</a>";


                    echo "</div>
                        <div id='paypal'><br />
                            <form action='https://www.sandbox.paypal.com/cgi-bin/webscr' method='post'>
                                <input name='cmd' type='hidden' value='_xclick' />
                                <input name='business' type='hidden' value='".$shop_settings['paypal_email']."' /
                                <input name='item_name' type='hidden' value='OrderPaying' />
                                <input name='item_number' type='hidden' value='".$data['shp_order_id']."' />
                                <input name='amount' type='hidden' value='".convert_cost($data['shp_order_cost'],$shop_settings['currency_default'],"USD")."' />
                                <input name='no_shipping' type='hidden' value='1' />
                                <input name='rm' type='hidden' value='2' />
                                <input name='return' type='hidden' value='".$settings['siteurl']."infusions/al_shop/includes/paypal/success.php?order=".$data['shp_order_id']."' />
                                <input name='cancel_return' type='hidden' value='".$settings['siteurl']."infusions/al_shop/includes/paypal/fail.php?order=".$data['shp_order_id']."' />
                                <input name='currency_code' type='hidden' value='USD' />
                                <input name='notify_url' type='hidden' value='".$settings['siteurl']."infusions/al_shop/includes/paypal/listener.php' />
                                <input type='submit' value='".$locale['shp285']."' class='button' />
                            </form>
                        </div>
                        <div id='nal'>
                            <br /><form action='' method='post'><input type='hidden' name='order_id' value='".$data['shp_order_id']."' /><input type='submit' class='button' name='save_nal' value='".$locale['shp299']."' /></form>
                        </div>
                        <script>
                            $(document).ready(function(){
                                $('#beznal').hide();
                                $('#robokassa').hide();
                                $('#paypal').hide();
                                $('#nal').hide();
                                $('select[name=payment_choose]').change(function(){
                                    if ($(this).val() == 0) {
                                        $('#beznal').hide();
                                        $('#robokassa').hide();
                                        $('#paypal').hide();
                                        $('#nal').hide();
                                    } else if ($(this).val() == 1) {
                                        $('#beznal').show();
                                        $('#robokassa').hide();
                                        $('#paypal').hide();
                                        $('#nal').hide();
                                    } else if ($(this).val() == 2) {
                                        $('#beznal').hide();
                                        $('#robokassa').show();
                                        $('#paypal').hide();
                                        $('#nal').hide();
                                    } else if ($(this).val() == 3) {
                                        $('#beznal').hide();
                                        $('#robokassa').hide();
                                        $('#paypal').show();
                                        $('#nal').hide();
                                    } else if ($(this).val() == 4) {
                                        $('#beznal').hide();
                                        $('#robokassa').hide();
                                        $('#paypal').hide();
                                        $('#nal').show();
                                    }
                                });
                            });
                        </script>
                        </td>";
                    echo "</tr>";
                }
            echo "</table>";
        closetable();

    } else {
        redirect(FUSION_SELF."?action=cart");
    }
} else {
    redirect(FUSION_SELF."?action=cart");
}

?>