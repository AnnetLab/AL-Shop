<?php
/**
 *  $good - good array
 *  $images_count - num of good images
 *  $images - array of good images if $images_count > 0
 *  $good_params - text attributes
 *  $good_params_sel - select attributes
 */

echo "<div>";
    echo "<div style='float:left;'><br />".show_breadcrumbs("good",$_GET['id'])."</div>";
    if (!isset($_GET['action']) || $_GET['action'] != 'search') {
        require_once AL_SHOP_TPL_DIR."search_form.php";
    }
echo "</div>";

add_to_head("<script type='text/javascript' src='".AL_SHOP_DIR."includes/fancybox/lib/jquery.mousewheel-3.0.6.pack.js'></script>");
add_to_head("<link rel='stylesheet' href='".AL_SHOP_DIR."includes/fancybox/source/jquery.fancybox.css?v=2.1.3' type='text/css' media='screen' />");
add_to_head("<script type='text/javascript' src='".AL_SHOP_DIR."includes/fancybox/source/jquery.fancybox.pack.js?v=2.1.3'></script>");
    echo "<script>
        $(document).ready(function(){
            $('.goods-fancybox').fancybox();
        });
    </script>";

opentable($good['shp_good_title']." - ".$locale['shp94']);

    echo "<table width='100%'>";
    echo "<tr valign='top'>";
    echo "<td width='300' class='tbl'>";

    $cover = get_good_cover($good['shp_good_cover'],'file');
    echo "<a class='goods-fancybox' rel='good-images' href='".$cover."'><img src='".$cover."' style='max-width: 300px;' /></a><br />";

    if ($images_count > 0) {
        for ($i=0;$i<=$images_count-1;$i++) {
            echo "<a class='goods-fancybox' rel='good-images' href='".AL_SHOP_DIR."asset/goods/".$images[$i]['shp_image_file']."'><img src='".AL_SHOP_DIR."asset/goods/".$images[$i]['shp_image_thumb']."' width='50' style='max-height:50px;margin-right:10px; margin-top:10px;' /></a>";
            if ($i%4==0 && $i != 0) echo "<br />";
        }
    }

    echo "</td>";
    echo "<td class='tbl'><strong>".$good['shp_good_title']."</strong><br /><br />";
    echo $locale['shp36']." <a href='".FUSION_SELF."?action=category&id=".$good['shp_good_cat']."'>".$good['shp_cat_title']."</a><br />";
    if ($good['shp_good_manufacturer'] != 0) {
        echo $locale['shp37'].": <a href='".FUSION_SELF."?action=manufacturer&id=".$good['shp_manufacturer_id']."'>".$good['shp_manufacturer_title']."</a><br />";
    }
    echo "<br />";

    if (count($good_params_sel)>0) {
        foreach ($good_params_sel as $good_param_sel) {
            echo "<strong>".$good_param_sel['shp_param_name'].":</strong> ".implode(', ',$good_param_sel['values']);
            echo "<br />";
        }
        echo "<br />";
    }

    if ($good['shp_good_available'] == 1) {
        echo "<font style='color:green;'>".$locale['shp43']."</font><br />";
        echo $locale['shp95']." ".show_cost($good['shp_good_cost'],$good['shp_good_currency'],$_GET['currency'])."<br />";
        echo $locale['shp97']." <select name='change_currency'><option value='".$shop_settings['currency_default']."'".($_GET['currency'] == $shop_settings['currency_default'] ? " selected='selected'" : "").">".$shop_settings['currency_default']."</option>";
        foreach ($currencies as $c) {
            if ($shop_settings['currency_default'] != $c && $shop_settings[$c.'_enabled'] == 1) {
                echo "<option value='".$c."'".($_GET['currency'] == $c ? " selected='selected'" : "").">".$c."</option>";
            }
        }
        echo "</select>";
        echo "<br />";
        echo "<a href='#adc' class='add_to_cart shop-button' data-good-id='".$good['shp_good_id']."'>".$locale['shp249']."</a><br />";
        echo "<script>
                        $(document).ready(function(){
                            $('.add_to_cart').click(function(){
                                var good_id = $(this).attr('data-good-id');
                                $.fancybox({
                                    type: 'ajax',
                                    href: '".AL_SHOP_DIR."includes/cart.php?action=add&id='+good_id
                                });
                            });
                            $('select[name=change_currency]').change(function(){
                                var new_cur = $(this).val();
                                var redir = '".FUSION_SELF."?action=good&id=".$_GET['id']."&currency='+new_cur;
                                window.location.href = redir;
                            });
                        });
                    </script>";
    } else {
        echo "<font style='color:red;'>".$locale['shp44']."</font><br />";
    }
    echo "<br />";
    echo "<script type='text/javascript' src='http://yandex.st/share/share.js' charset='windows-1251'></script>";
    echo "<div class='yashare-auto-init' data-yashareL10n='en' data-yashareType='none' data-yashareQuickServices='yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,moikrug,gplus'></div>";
    echo "</td>";
    echo "</tr></table>";

    if (!empty($good['shp_good_desc'])) {
        echo "<strong>".$locale['shp96']."</strong><br />".$good['shp_good_desc'];
    }

    if (count($good_params)>0) {
        echo "<table width='100%'>";
            echo "<tr>";
                echo "<td class='tbl2' colspan='2'><strong>".$locale['shp342']."</strong></td>";
            echo "</tr>";
            $i=1;
            foreach ($good_params as $good_param) {
                echo "<tr>";
                    echo "<td class='".($i%2==0 ? "tbl2" : "tbl1")."' width='200'><strong>".$good_param['shp_param_name']."</strong></td>";
                    echo "<td class='".($i%2==0 ? "tbl2" : "tbl1")."'>".$good_param['shp_gp_param_value']."</td>";
                echo "</tr>";
                $i++;
            }
        echo "</table>";
    }



closetable();

?>