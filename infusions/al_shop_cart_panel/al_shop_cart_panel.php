<?php
defined("IN_FUSION") or die("DENIED");
require_once INFUSIONS."al_shop/infusion_db.php";
if (file_exists(AL_SHOP_DIR."locale/".$settings['locale'].".php")) {
    include AL_SHOP_DIR."locale/".$settings['locale'].".php";
} else {
    include AL_SHOP_DIR."locale/Russian.php";
}

openside($locale['shp98']);
    echo "<div id='shp_cart_panel'></div>";
closeside();

echo "<script>
    $(document).ready(function(){
        $.ajax({
            url: '".AL_SHOP_DIR."includes/cart.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_cart'
            }, success: function(data) {
                if (data.result == 'success') {
                    $('#shp_cart_panel').empty().append(data.html);
                }
            }
        });
    });
</script>";


?>