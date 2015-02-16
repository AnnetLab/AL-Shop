<?php
require_once "maincore.php";
require_once INFUSIONS."al_shop/infusion_db.php";
if (file_exists(AL_SHOP_DIR."locale/".$settings['locale'].".php")) {
    include AL_SHOP_DIR."locale/".$settings['locale'].".php";
} else {
    include AL_SHOP_DIR."locale/Russian.php";
}
require_once AL_SHOP_DIR."includes/functions.php";

if (isset($_GET['id']) && isnum($_GET['id'])) {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_id='".$_GET['id']."'");
    if (dbrows($result)) {
        $order = dbarray($result);
        if ($data['shp_order_finished'] == 0) {

            echo "<!doctype HTML>";
            echo "<html>";
            echo "<head><title>".$settings['sitename']."</title>";
            echo "<meta http-equiv='Content-Type' content='text/html; charset=windows-1251' />";
            echo "<link rel='stylesheet' href='".AL_SHOP_DIR."asset/check.css' type='text/css' media='screen' />";
            echo "<link rel='stylesheet' href='".AL_SHOP_DIR."asset/check.css' type='text/css' media='print' />";
            echo "</head><body>";
            echo "<table class='out-border'>
                <tr>
                    <td width='15%'>".$locale['shp145']."</td><td>".$shop_settings['firm_name']."</td>
                </tr>
                <tr>
                    <td width='15%'>".$locale['shp146']."</td><td>".$shop_settings['firm_address']."</td>
                </tr>
            </table>
            <br /><br />
            <table class='border' border='1' cellpadding='0' cellspacing='0'>
                <tr>
                    <td width='25%'>".$locale['shp147']." ".$shop_settings['firm_inn']."</td>
                    <td width='25%'>".$locale['shp148']." ".$shop_settings['firm_kpp']."</td>
                    <td width='10%' rowspan='2' valign='bottom' align='center'>".$locale['shp151']."</td>
                    <td rowspan='2' valign='bottom'>".$shop_settings['firm_schet']."</td>
                </tr>
                <tr>
                    <td colspan='2'>".$locale['shp149']."<br /><br />".$shop_settings['firm_name']."</td>
                </tr>
                <tr>
                    <td rowspan='2' colspan='2'>".$locale['shp150']."<br /><br />".$shop_settings['firm_bank']."</td>
                    <td align='center'>".$locale['shp152']."</td>
                    <td rowspan='2'>".$shop_settings['firm_bik']."<br /><br />".$shop_settings['firm_schet_banka']."</td>
                </tr>
                <tr>
                    <td align='center'>".$locale['shp151']."</td>
                </tr>
            </table><br />
            <table width='100%' border='0' class='padding-table'>
                <tr><td colspan='2' align='center'><h2>".$locale['shp153'].$order['shp_order_id']." ".$locale['shp154']." &laquo;".date("j",$order['shp_order_datestamp'])."&raquo; ".$locale['shp_month_'.date("n",$order['shp_order_datestamp'])]." ".date("Y",$order['shp_order_datestamp'])."</h2></td></tr>
                <tr>
                    <td width='20%'>".$locale['shp155']."</td>
                    <td>";
                    if ($order['shp_order_type'] == 1) { //fizlico
                        echo $order['shp_order_fio'].", ".$order['shp_order_address'];
                    } else { // urlico
                        echo $order['shp_order_company'].", ".$locale['shp147']." ".$order['shp_order_inn']."/".$order['shp_order_kpp'].", ".$order['shp_order_address'];
                    }
                    echo "</td>
                </tr>
            </table><br />
            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                <tr>
                    <td class='border-td border-top2 border-left2' width='1%'>&#8470;</td>
                    <td class='border-td border-top2'>".$locale['shp156']."</td>
                    <td class='border-td border-top2'>".$locale['shp157']."</td>
                    <td class='border-td border-top2'>".$locale['shp158']."</td>
                    <td class='border-td border-top2'>".$locale['shp159']."</td>
                    <td class='border-td border-top2 border-right2'>".$locale['shp160']."</td>
                </tr>";
                $orders_arr = explode("|",$order['shp_order_str']);
                $i=1; $total_amount = 0;
                foreach ($orders_arr as $order_str) {
                    list($good_id,$amount) = explode("_",$order_str);
                    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_id='".$good_id."'");
                    if (dbrows($result)) {
                        $good = dbarray($result);
                        echo "<tr>";
                            echo "<td class='border-td border-left2'>".$i."</td>";
                            echo "<td class='border-td'>".$good['shp_good_title']."</td>";
                            echo "<td class='border-td'>".$locale['shp161']."</td>";
                            echo "<td class='border-td'>".$amount."</td>";
                            echo "<td class='border-td' align='right'>".convert_cost($good['shp_good_cost'],$good['shp_good_currency'],$shop_settings['currency_default'])."</td>";
                            echo "<td class='border-td border-right2' align='right'>".convert_cost($amount*$good['shp_good_cost'],$good['shp_good_currency'],$shop_settings['currency_default'])."</td>";
                        echo "</tr>";
                        $i++; $total_amount = $amount+$total_amount;
                    }
                }

            echo "
            <tr>
                <td colspan='5' class='border-top padding' align='right'>".$locale['shp162']."</td>
                <td class='border-td border-left2 border-right2' align='right'>".$order['shp_order_cost']."</td>
            </tr>";
            if ($shop_settings['firm_nds_enabled'] == 1) {
                echo "<tr>
                    <td colspan='5' class='padding' align='right'>".$locale['shp163']."</td>
                    <td class='border-td border-left2 border-right2' align='right'>".($order['shp_order_cost']*0.2)."</td>
                </tr>";
            }
            echo "<tr>
                <td colspan='5' class='padding' align='right'>".$locale['shp164']."</td>
                <td class='border-td border-left2 border-right2 border-bottom2' align='right'>".$order['shp_order_cost']."</td>
            </tr>
            </table><br />
            ".sprintf($locale['shp165'],$total_amount,$order['shp_order_cost'])."<br />".num2str($order['shp_order_cost'],$shop_settings['currency_default'])."<br />
            <span style='margin-left:80px;' class='small'>(".$locale['shp139'].")</span><br /><br />
            ".sprintf($locale['shp140'],$shop_settings['check_expired'])."<br /><br />
            <table width='100%' class='padding-table'>
                <tr>
                    <td width='40%'>".$locale['shp143']."</td>
                    <td width='20%'></td>
                    <td>".$shop_settings['firm_nachalnik']."</td>
                </tr>
                <tr>
                    <td></td>
                    <td align='center'><span class='small'>(".$locale['shp141'].")</span></td>
                    <td><span class='small'>(".$locale['shp142'].")</span></td>
                </tr>
                <tr>
                    <td>".$locale['shp144']."</td>
                    <td></td>
                    <td>".$shop_settings['firm_buhgalter']."</td>
                </tr>
                <tr>
                    <td></td>
                    <td align='center'><span class='small'>(".$locale['shp141'].")</span></td>
                    <td><span class='small'>(".$locale['shp142'].")</span></td>
                </tr>
            </table>
            ";

            echo "</body></html>";

        } else {
            redirect(BASEDIR."shop.php");
        }

    } else {
        redirect(BASEDIR."shop.php");
    }

}





?>