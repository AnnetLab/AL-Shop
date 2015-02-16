<?php

$result = dbquery("SELECT * FROM ".DB_AL_SHOP_MANUFACTURES." WHERE shp_manufacturer_id='".$_GET['id']."'");
if (dbrows($result)) {


    if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }
    $man = dbarray($result);
    set_title($man['shp_manufacturer_title']." | ".$locale['shp229']." | ".$settings['sitename']);

    require_once AL_SHOP_TPL_DIR."manufacturer_section.php";



    $count = dbcount("(shp_good_id)",DB_AL_SHOP_GOODS,"shp_good_manufacturer='".$man['shp_manufacturer_id']."' AND shp_good_published='1'");

    if ($count>0) {

        if (isset($_GET['filter'])) {
            switch ($_GET['filter']) {
                case "az-desc":
                    $filter = " ORDER BY shp_good_title DESC";
                    break;
                case "az-asc":
                    $filter = " ORDER BY shp_good_title ASC";
                    break;
                case "cost-desc":
                    $filter = " ORDER BY shp_good_cost DESC";
                    break;
                case "cost-asc":
                    $filter = " ORDER BY shp_good_cost ASC";
                    break;
                case "pop-desc":
                    $filter = " ORDER BY shp_good_views DESC";
                    break;
                default:

                    break;
            }
        } else {
            $filter = "";
        }
        if (isset($_GET['cost_min']) && isnum($_GET['cost_min']) && isset($_GET['cost_max']) && isnum($_GET['cost_max']) && $_GET['cost_max'] > $_GET['cost_min']) {
            $cost_filter = " AND shp_good_cost>='".$_GET['cost_min']."' AND shp_good_cost<='".$_GET['cost_max']."'";
        } else {
            $cost_filter = "";
        }

        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_manufacturer='".$man['shp_manufacturer_id']."' AND shp_good_published='1'".$cost_filter.$filter." LIMIT ".$_GET['rowstart'].",".$shop_settings['goods_per_page']);
        //$result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_manufacturer='".$man['shp_manufacturer_id']."' AND shp_good_published='1' LIMIT ".$_GET['rowstart'].",".$shop_settings['goods_per_page']);
        $currencies = array("RUB","BYR","UAH","USD","EUR");
        if (!isset($_GET['currency']) || !in_array($_GET['currency'],$currencies)) {
            $_GET['currency'] = $shop_settings['currency_default'];
        }
        $data = make_assoc($result);

        require_once AL_SHOP_TPL_DIR."goods_list.php";

    }

    echo "<script>
        $(document).ready(function(){
            $('select[name=change_currency]').change(function(){
                var new_cur = $(this).val();
                var redir = '".FUSION_SELF."?action=manufacturer&id=".$_GET['id']."&currency='+new_cur+'".(isset($_GET['rowstart']) && $_GET['rowstart'] != 0 ? "&rowstart=".$_GET['rowstart'] : "").(isset($_GET['filter']) && in_array($_GET['filter'],array("az-desc","az-asc","cost-desc","cost-asc","pop-desc")) ? "&filter=".$_GET['filter'] : "").(isset($_GET['cost_min']) && isnum($_GET['cost_min']) ? "&cost_min=".$_GET['cost_min'] : "").(isset($_GET['cost_max']) && isnum($_GET['cost_max']) ? "&cost_max=".$_GET['cost_max'] : "")."';
                window.location.href = redir;
            });
            $('select[name=change_filter]').change(function(){
                var new_filter = $(this).val();
                var redir = '".FUSION_SELF."?action=manufacturer&id=".$_GET['id']."&filter='+new_filter+'".(isset($_GET['currency']) && in_array($_GET['currency'],array("RUB","BYR","EUR","USD","UAH")) ? "&currency=".$_GET['currency'] : "").(isset($_GET['rowstart']) && $_GET['rowstart'] != 0 ? "&rowstart=".$_GET['rowstart'] : "").(isset($_GET['cost_min']) && isnum($_GET['cost_min']) ? "&cost_min=".$_GET['cost_min'] : "").(isset($_GET['cost_max']) && isnum($_GET['cost_max']) ? "&cost_max=".$_GET['cost_max'] : "")."';
                window.location.href = redir;
            });
            $('.add_to_cart').click(function(){
                var good_id = $(this).attr('data-good-id');
                $.fancybox({
                    type: 'ajax',
                    href: '".AL_SHOP_DIR."includes/cart.php?action=add&id='+good_id
                });
            });
        });
    </script>";




} else {
    redirect(FUSION_SELF);
}



?>