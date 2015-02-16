<?php
/**
 *  $goods - goods to checkout
 */
            // jquery validation and its rules
            add_to_head("<script type='text/javascript' src='".AL_SHOP_DIR."includes/validate/jquery.validate.js'></script>");
            add_to_head("<script>

                $(document).ready(function(){

                    var validator = $('#orderForm').validate();

                    $('input[name=order_choose]').change(function(){

                        if ($('input[name=order_choose]:checked').val() == 'fizlico') {

                                $('input[name=urlico_fio]').rules('remove');
                                $('input[name=urlico_address]').rules('remove');
                                $('input[name=urlico_email]').rules('remove');
                                $('input[name=urlico_phone]').rules('remove');
                                $('input[name=urlico_company]').rules('remove');
                                $('input[name=urlico_inn]').rules('remove');
                                $('input[name=urlico_kpp]').rules('remove');

                            $('input[name=fizlico_fio]').rules('add',{
                                required: true,
                                minlength: 10,
                                messages: {
                                    required: '".$locale['shp251']."',
                                    minlength: '".$locale['shp258']."'
                                }
                            });
                            $('input[name=fizlico_address]').rules('add',{
                                required: true,
                                minlength: 15,
                                messages: {
                                    required: '".$locale['shp252']."',
                                    minlength: '".$locale['shp259']."'
                                }
                            });
                            $('input[name=fizlico_email]').rules('add',{
                                required: true,
                                email: true,
                                messages: {
                                    required: '".$locale['shp253']."',
                                    email: '".$locale['shp260']."'
                                }
                            });
                            $('input[name=fizlico_phone]').rules('add',{
                                required: true,
                                number: true,
                                //minlength: 7,
                                messages: {
                                    required: '".$locale['shp254']."',
                                    number: '".$locale['shp261']."',
                                    //minlength: '".$locale['shp262']."'
                                }
                            });


                        } else if ($('input[name=order_choose]:checked').val() == 'urlico') {

                            $('input[name=fizlico_fio]').rules('remove');
                            $('input[name=fizlico_address]').rules('remove');
                            $('input[name=fizlico_email]').rules('remove');
                            $('input[name=fizlico_phone]').rules('remove');

                            $('input[name=urlico_fio]').rules('add',{
                                required: true,
                                minlength: 10,
                                messages: {
                                    required: '".$locale['shp251']."',
                                    minlength: '".$locale['shp258']."'
                                }
                            });
                            $('input[name=urlico_address]').rules('add',{
                                required: true,
                                minlength: 15,
                                messages: {
                                    required: '".$locale['shp252']."',
                                    minlength: '".$locale['shp259']."'
                                }
                            });
                            $('input[name=urlico_email]').rules('add',{
                                required: true,
                                email: true,
                                messages: {
                                    required: '".$locale['shp253']."',
                                    email: '".$locale['shp260']."'
                                }
                            });
                            $('input[name=urlico_phone]').rules('add',{
                                required: true,
                                digits: true,
                                minlength: 7,
                                messages: {
                                    required: '".$locale['shp254']."',
                                    digits: '".$locale['shp261']."',
                                    minlength: '".$locale['shp262']."'
                                }
                            });
                            $('input[name=urlico_company]').rules('add',{
                                required: true,
                                minlength: 5,
                                messages: {
                                    required: '".$locale['shp255']."',
                                    minlength: '".$locale['shp263']."'
                                }
                            });
                            $('input[name=urlico_inn]').rules('add',{
                                required: true,
                                digits: true,
                                messages: {
                                    required: '".$locale['shp256']."',
                                    digits: '".$locale['shp261']."'
                                }
                            });
                            $('input[name=urlico_kpp]').rules('add',{
                                required: true,
                                digits: true,
                                messages: {
                                    required: '".$locale['shp257']."',
                                    digits: '".$locale['shp261']."'
                                }
                            });


                        }

                    })


                });

            </script>");
            echo "<style>
                label.error {
                    color: red;
                    margin-left: 10px;
                }
            </style>";

            echo "<form action='".FUSION_SELF."?action=checkout' method='post' id='orderForm'>";

            opentable($locale['shp108']);

                echo "<table width='100%'>";
                echo "<tr>";
                echo "<td class='tbl2'><strong>".$locale['shp102']."</strong></td>";
                echo "<td class='tbl2' width='180'></td>";
                echo "<td class='tbl2' width='150'><strong>".$locale['shp105']."</strong></td>";
                echo "</tr>";
                foreach ($goods as $good) {
                    echo "<tr>";
                    echo "<td class='tbl'><a href='".FUSION_SELF."?action=good&id=".$good['shp_good_id']."'>".$good['shp_good_title']."</a> ";
                    if (isset($good['params']) && !empty($good['params'])) {
                        echo "<i class='small'>(";
                        $i = 1;
                        foreach ($good['params'] as $param=>$value) {
                            echo $param.": ".$value.($i < count($good['params']) ? ", " : "");
                            $i++;
                        }
                        echo ")</i>";
                    }
                    echo "</td>";
                    echo "<td class='tbl' align='right'>".$good['amount']." x ".show_cost($good['shp_good_cost'],$good['shp_good_currency'],$shop_settings['currency_default'])."</td>";
                    echo "<td class='tbl' align='right'>".show_cost($good['amount']*$good['shp_good_cost'],$good['shp_good_currency'],$shop_settings['currency_default'])."</td>";
                    echo "</tr>";
                }
                echo "<tr><td class='tbl'><input type='hidden' name='basket_ids' value='".$basket_ids."' /><input type='hidden' name='orders_str' value='".$orders_str."' /><input type='hidden' name='total_cost' value='".$total_cost."' /></td><td class='tbl' align='right'><strong>".$locale['shp109']."</strong></td><td class='tbl' align='right'><strong>".show_cost($total_cost,$shop_settings['currency_default'],$shop_settings['currency_default'])."</strong></td></tr>";
                echo "<tr><td class='tbl' colspan='3'>".$locale['shp264']."<select name='delivery' class='textbox'>";
                    echo "<option value='0'>".$locale['shp308']."</option>";
                    foreach ($deliveries as $delivery) {
                        echo "<option value='".$delivery['shp_delivery_id']."'>".$delivery['shp_delivery_title']."</option>";
                    }
                echo "</select><br /><label for='radio_fizlico'><input id='radio_fizlico' type='radio' name='order_choose' value='fizlico'".($client_data && $client_data['type'] == 1 ? " checked='checked'" : "")." /> ".$locale['shp112']."</label> <label for='radio_urlico'><input id='radio_urlico' type='radio' name='order_choose' value='urlico'".($client_data && $client_data['type'] == 2 ? " checked='checked'" : "")." /> ".$locale['shp113']."</label></td></tr>";
                echo "</table>";
            closetable();

            // order forms. u can stylize it, but do not change it names/ids
            echo "<div id='order_fizlico'>";
            opentable($locale['shp110']);
            echo "<table width='100%'>";
            echo "<tr>";
                echo "<td class='tbl' width='200'>".$locale['shp114']."</td>";
                echo "<td class='tbl'><input type='text' class='textbox' style='width:250px;' name='fizlico_fio' value='".($client_data && $client_data['type'] == 1 && $client_data['fio'] ? $client_data['fio'] : "")."' /></td>";
            echo "</tr><tr>";
                echo "<td class='tbl'>".$locale['shp115']."</td>";
                echo "<td class='tbl'><input type='text' class='textbox' style='width:250px;' name='fizlico_address' value='".($client_data && $client_data['type'] == 1 && $client_data['address'] ? $client_data['address'] : "")."' /></td>";
            echo "</tr><tr>";
                echo "<td class='tbl'>".$locale['shp116']."</td>";
                echo "<td class='tbl'><input type='text' class='textbox' style='width:250px;' name='fizlico_email' value='".($client_data && $client_data['type'] == 1 && $client_data['email'] ? $client_data['email'] : "")."' /></td>";
            echo "</tr><tr>";
                echo "<td class='tbl'>".$locale['shp117']."</td>";
                echo "<td class='tbl'><input type='text' class='textbox' style='width:250px;' name='fizlico_phone' value='".($client_data && $client_data['type'] == 1 && $client_data['phone'] ? $client_data['phone'] : "")."' /></td>";
            echo "</tr><tr>";
                echo "<td class='tbl' valign='top'>".$locale['shp311']."</td>";
                echo "<td class='tbl'><textarea name='fizlico_note' class='textbox' cols='40' rows='4'></textarea></td>";
            echo "</tr><tr>";
                echo "<td class='tbl'></td>";
                echo "<td class='tbl'><input type='submit' class='button' name='order' value='".$locale['shp118']."' /> <a class='shop-button' href='".FUSION_SELF."?action=cart'>".$locale['shp19']."</a></td>";
            echo "</tr>";
            echo "</table>";
            closetable();
            echo "</div>";

            echo "<div id='order_urlico'>";
            opentable($locale['shp111']);
            echo "<table width='100%'>";
            echo "<tr>";
                echo "<td class='tbl' width='200'>".$locale['shp114']."</td>";
                echo "<td class='tbl'><input type='text' class='textbox' style='width:250px;' name='urlico_fio' value='".($client_data && $client_data['type'] == 2 && $client_data['fio'] ? $client_data['fio'] : "")."' /></td>";
            echo "</tr><tr>";
                echo "<td class='tbl'>".$locale['shp115']."</td>";
                echo "<td class='tbl'><input type='text' class='textbox' style='width:250px;' name='urlico_address' value='".($client_data && $client_data['type'] == 2 && $client_data['address'] ? $client_data['address'] : "")."' /></td>";
            echo "</tr><tr>";
                echo "<td class='tbl'>".$locale['shp116']."</td>";
                echo "<td class='tbl'><input type='text' class='textbox' style='width:250px;' name='urlico_email' value='".($client_data && $client_data['type'] == 2 && $client_data['email'] ? $client_data['email'] : "")."' /></td>";
            echo "</tr><tr>";
                echo "<td class='tbl'>".$locale['shp117']."</td>";
                echo "<td class='tbl'><input type='text' class='textbox' style='width:250px;' name='urlico_phone' value='".($client_data && $client_data['type'] == 2 && $client_data['phone'] ? $client_data['phone'] : "")."' /></td>";
            echo "</tr><tr>";
                echo "<td class='tbl'>".$locale['shp119']."</td>";
                echo "<td class='tbl'><input type='text' class='textbox' style='width:250px;' name='urlico_company' value='".($client_data && $client_data['type'] == 2 && $client_data['company'] ? $client_data['company'] : "")."' /></td>";
            echo "</tr><tr>";
                echo "<td class='tbl'>".$locale['shp120']."</td>";
                echo "<td class='tbl'><input type='text' class='textbox' style='width:250px;' name='urlico_inn' value='".($client_data && $client_data['type'] == 2 && $client_data['inn'] ? $client_data['inn'] : "")."' /></td>";
            echo "</tr><tr>";
                echo "<td class='tbl'>".$locale['shp121']."</td>";
                echo "<td class='tbl'><input type='text' class='textbox' style='width:250px;' name='urlico_kpp' value='".($client_data && $client_data['type'] == 2 && $client_data['kpp'] ? $client_data['kpp'] : "")."' /></td>";
            echo "</tr><tr>";
                echo "<td class='tbl' valign='top'>".$locale['shp311']."</td>";
                echo "<td class='tbl'><textarea name='urlico_note' class='textbox' cols='40' rows='4'></textarea></td>";
            echo "</tr><tr>";
                echo "<td class='tbl'></td>";
                echo "<td class='tbl'><input type='submit' class='button' name='order' value='".$locale['shp118']."' /> <a class='shop-button' href='".FUSION_SELF."?action=cart'>".$locale['shp19']."</a></td>";
            echo "</tr>";
            echo "</table>";
            closetable();
            echo "</div>";

            echo "</form>";


            echo "<script>
                $(document).ready(function(){
                    $('#order_fizlico').hide();
                    $('#order_urlico').hide();
                    $('input[name=order_choose]').live('change',function(){
                        if ($(this).val() == 'fizlico' && $(this).attr('checked') == 'checked') {
                            $('#order_fizlico').show();
                            $('#order_urlico').hide();
                        } else if ($(this).val() == 'urlico' && $(this).attr('checked') == 'checked') {
                            $('#order_fizlico').hide();
                            $('#order_urlico').show();
                        }
                    }).change();
                });
            </script>";

?>