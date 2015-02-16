<?php
require_once "../../../maincore.php";
require_once INFUSIONS."al_shop/infusion_db.php";
if (file_exists(AL_SHOP_DIR."locale/".$settings['locale'].".php")) {
    include AL_SHOP_DIR."locale/".$settings['locale'].".php";
} else {
    include AL_SHOP_DIR."locale/Russian.php";
}
require_once AL_SHOP_DIR."includes/functions.php";

if (isset($_POST['action']) && $_POST['action'] == "add" && isset($_POST['id']) && isnum($_POST['id'])) {

    $check = dbquery("SELECT * FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_id='".$_POST['id']."'");
    if (dbrows($check)) {
        $amount = isset($_POST['amount']) && isnum($_POST['amount']) ? intval($_POST['amount']) : 0;
        $good = intval($_POST['id']);
        $params = isset($_POST['params_str']) && !empty($_POST['params_str']) ? stripinput($_POST['params_str']) : '';
        if (iMEMBER) {
            $result = dbquery("SELECT * FROM ".DB_AL_SHOP_BASKET." WHERE shp_basket_good='".$good."' AND (shp_basket_user='".$userdata['user_id']."' OR shp_basket_ip='".FUSION_IP."')");
            if (dbrows($result)) {
                $data = dbarray($result);
                $update = dbquery("UPDATE ".DB_AL_SHOP_BASKET." SET shp_basket_amount=shp_basket_amount+".$amount.", shp_basket_date='".time()."', shp_basket_good_params='".$params."' WHERE shp_basket_id='".$data['shp_basket_id']."'");
            } else {
                $add = dbquery("INSERT INTO ".DB_AL_SHOP_BASKET." (shp_basket_good,shp_basket_amount,shp_basket_date,shp_basket_user,shp_basket_ip,shp_basket_good_params) VALUES ('".$good."','".$amount."','".time()."','".$userdata['user_id']."','".FUSION_IP."','".$params."')");
            }
        } else {
            $result = dbquery("SELECT * FROM ".DB_AL_SHOP_BASKET." WHERE shp_basket_good='".$good."' AND shp_basket_ip='".FUSION_IP."'");
            if (dbrows($result)) {
                $data = dbarray($result);
                $update = dbquery("UPDATE ".DB_AL_SHOP_BASKET." SET shp_basket_amount=shp_basket_amount+".$amount.", shp_basket_date='".time()."', shp_basket_good_params='".$params."' WHERE shp_basket_id='".$data['shp_basket_id']."'");
            } else {
                $add = dbquery("INSERT INTO ".DB_AL_SHOP_BASKET." (shp_basket_good,shp_basket_amount,shp_basket_date,shp_basket_ip,shp_basket_user,shp_basket_good_params) VALUES ('".$good."','".$amount."','".time()."','".FUSION_IP."','0','".$params."')");
            }
        }
        print json_encode(array("result"=>"ok"));
    } else {
        print json_encode(array("result"=>"fail"));
    }

}

if (isset($_GET['action']) && $_GET['action'] == "add" && isset($_GET['id']) && isnum($_GET['id'])) {

    header('Content-Type: text/html; charset=UTF-8');
    //header('Content-Type: text/html; charset=utf-8', true);
    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_id='".$_GET['id']."'");
    if (dbrows($result)) {

        $data = dbarray($result);

        if ($data['shp_good_cover'] != 0) {
            $img_result = dbquery("SELECT * FROM ".DB_AL_SHOP_IMAGES." WHERE shp_image_id='".$data['shp_good_cover']."'");
            if (dbrows($result)) {
                $img = dbarray($img_result);
                $img = $settings['siteurl']."infusions/al_shop/asset/goods/".$img['shp_image_thumb'];
            } else {
                $img = $settings['siteurl']."infusions/al_shop/asset/no_image.gif";
            }
        } else {
            $img = $settings['siteurl']."infusions/al_shop/asset/no_image.gif";
        }

        $good_params_sel = array();
        $result_params = dbquery("SELECT gp.*, p.* FROM ".DB_AL_SHOP_GOODS_PARAMS." gp LEFT JOIN ".DB_AL_SHOP_GOOD_PARAMS." p ON p.shp_param_id=gp.shp_gp_param_id WHERE shp_gp_good_id='".$_GET['id']."' AND shp_param_type='select'");
        if (dbrows($result_params)) {
            while ($data2 = dbarray($result_params)) {
                if (!empty($data2['shp_gp_param_value'])) {
                    $values_result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOOD_PARAM_VALUES." WHERE shp_value_id IN (".implode(',',explode('.',$data2['shp_gp_param_value'])).")");
                    if (dbrows($values_result)) {
                        $good_params_sel[] = array_merge($data2,array('values'=>make_assoc($values_result)));
                    }
                }
            }
        }

        echo "<html>";
        echo "<header><title>Cart</title><meta http-equiv='content-type' content='text/html; charset=utf-8'></header><body>";
        echo "<div style='min-width:300px;text-align:center;' id='add_div'>";
            echo "<img src='".$img."' width='".$shop_settings['thumb_width']."' /><br />";
            echo iconv("windows-1251","utf-8",$data['shp_good_title'])."<br /><br />";
            if (count($good_params_sel)>0) {
                foreach ($good_params_sel as $good_param_sel) {
                    echo iconv("windows-1251","utf-8",$good_param_sel['shp_param_name']).": ";
                    echo "<select class='params' data-param-id='".$good_param_sel['shp_param_id']."'>";
                        foreach ($good_param_sel['values'] as $val) {
                            echo "<option value='".$val['shp_value_id']."'>".iconv("windows-1251","utf-8",$val['shp_value_data'])."</option>";
                        }
                    echo "</select>";
                    echo "<br />";
                }
                echo "<br />";
            }
            echo iconv("windows-1251","utf-8",$locale['shp90'])."<input type='text' class='textbox' name='amount' style='width:30px;' value='1' /> <input type='submit' class='button' name='add' value='".iconv("windows-1251","utf-8",$locale['shp91'])."' />";

        echo "</div>";
        echo "</body></html>";

        echo "<script>
            $(document).ready(function(){

                var panel_exists = false;
                if ($('#shp_cart_panel').length) {
                    panel_exists = true;
                }
                $('input[name=add]').click(function(){
                    var amount = $('input[name=amount]').val();
                    var id = '".$data['shp_good_id']."';
                    var params_str = '';
                    $('.params').each(function(key,el){
                        if (params_str == '') {
                            params_str = $(this).attr('data-param-id')+'-'+$(this).val();
                        } else {
                            params_str = params_str+'||'+$(this).attr('data-param-id')+'-'+$(this).val();
                        }
                    });
                    $.ajax({
                        url: 'infusions/al_shop/includes/cart.php',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            action: 'add',
                            id: id,
                            amount: amount,
                            params_str: params_str
                        }, success: function(data) {
                            if (data.result == 'ok') {
                                $('#add_div').empty().append('".iconv("windows-1251","utf-8",$locale['shp92'])."');
                            } else {
                                $('#add_div').empty().append('".iconv("windows-1251","utf-8",$locale['shp93'])."');
                            }
                            setTimeout(function() {
                                parent.$.fancybox.close();
                            },1500);
                            if (panel_exists) {
                                $.ajax({
                                    url: 'infusions/al_shop/includes/cart.php',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        action: 'get_cart'
                                    }, success: function(data) {
                                        //console.log(data);
                                        if (data.result == 'success') {
                                            $('#shp_cart_panel').empty().append(data.html);
                                        }
                                    }
                                });
                            }
                        }
                    });
                })
            });
        </script>";

    } else {
        echo "Invalid id";
    }

}

if (isset($_POST['action']) && $_POST['action'] == 'get_cart') {

    $res = 'fail';
    if (iMEMBER) {
        $result = dbquery("SELECT b.*,g.* FROM ".DB_AL_SHOP_BASKET." b LEFT JOIN ".DB_AL_SHOP_GOODS." g ON g.shp_good_id=b.shp_basket_good WHERE shp_basket_user='".$userdata['user_id']."' OR shp_basket_ip='".FUSION_IP."'");
    } else {
        $result = dbquery("SELECT b.*,g.* FROM ".DB_AL_SHOP_BASKET." b LEFT JOIN ".DB_AL_SHOP_GOODS." g ON g.shp_good_id=b.shp_basket_good WHERE shp_basket_ip='".FUSION_IP."'");
    }
    if (dbrows($result)) {
        $res = 'success';
        $html = "";
        $amount = 0;
        $sum = 0;
        while ($data=dbarray($result)) {
            //$html .= $data['shp_basket_amount']."x <a href='".BASEDIR."shop.php?action=good&id=".$data['shp_basket_good']."'>".$data['shp_good_title']."</a> - ".show_cost($data['shp_good_cost']*$data['shp_good_amount'],$data['shp_good_currency'],$shop_settings['currency_default'])."<br />";
            //$html .= $data['shp_basket_amount']."x <a href='".$settings['siteurl']."shop.php?action=good&id=".$data['shp_basket_good']."'>".iconv("windows-1251","utf-8",$data['shp_good_title'])."</a> - ".iconv("windows-1251","utf-8",show_cost($data['shp_good_cost']*$data['shp_basket_amount'],$data['shp_good_currency'],$shop_settings['currency_default']))."<br />";
            //$html[] = $data;
            $amount += $data['shp_basket_amount'];
            $sum += convert_cost($data['shp_good_cost']*$data['shp_basket_amount'],$data['shp_good_currency'],$shop_settings['currency_default']);
        }
        $html .= iconv("windows-1251","utf-8",sprintf($locale['shp327'],$amount,$sum,$locale['shp_'.$shop_settings['currency_default'].'3']));
        $html .= "<br /><a href='".$settings['siteurl']."shop.php?action=cart'>".iconv("windows-1251","utf-8",$locale['shp100'])."</a>";
    } else {
        $res = 'success';
        $html = iconv("windows-1251","utf-8",$locale['shp99']);
    }
    if (iMEMBER) {
        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_finished<>'6' AND shp_order_user='".$userdata['user_id']."' AND shp_order_ip='".FUSION_IP."'");
    } else {
        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_finished<>'6' AND shp_order_ip='".FUSION_IP."'");
    }
    if (dbrows($result)) {
        $html .= '<br/><br />'.iconv("windows-1251","utf-8",$locale['shp133']).'<br />';
        while ($data=dbarray($result)) {
            $html .= "<a href='".$settings['siteurl']."shop.php?action=order&id=".$data['shp_order_id']."'>".iconv("windows-1251","utf-8",$locale['shp134']).$data['shp_order_id']."</a><br />";
        }
    }
    print(json_encode(array("result"=>$res,"html"=>$html)));

}


?>