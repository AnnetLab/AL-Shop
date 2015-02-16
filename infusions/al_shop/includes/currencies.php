<?php

function update_currency() {
    global $shop_settings;
    $curs = array("RUB","BYR","UAH","USD","EUR");
    $url = $shop_settings['url_'.$shop_settings['currency_current_updater']];
    //$geted = file_get_contents($url.date("d")."/".date("m")."/".date("Y"));
    $geted = file_get_contents($url);
    $xml = simplexml_load_string($geted);
    $data = json_decode(json_encode($xml), TRUE);
    $result = dbquery("TRUNCATE TABLE ".DB_AL_SHOP_CURRENCY);

    if ($shop_settings['currency_current_updater'] == "RUB") {
        foreach ($data['Valute'] as $item) {
            if (in_array($item['CharCode'],$curs)) {
                $result = dbquery("INSERT INTO ".DB_AL_SHOP_CURRENCY." (shp_currency_current,shp_currency_code,shp_currency_nominal,shp_currency_rate) VALUES ('".$shop_settings['currency_current_updater']."','".$item['CharCode']."','".$item['Nominal']."','".$item['Value']."')");
            }
        }

    } else if ($shop_settings['currency_current_updater'] == "BYR") {
        foreach ($data['Currency'] as $item) {
            if (in_array($item['CharCode'],$curs)) {
                $result = dbquery("INSERT INTO ".DB_AL_SHOP_CURRENCY." (shp_currency_current,shp_currency_code,shp_currency_nominal,shp_currency_rate) VALUES ('".$shop_settings['currency_current_updater']."','".$item['CharCode']."','".$item['Scale']."','".$item['Rate']."')");
            }
        }
    } else if ($shop_settings['currency_current_updater'] == "UAH") {
        foreach ($data['item'] as $item) {
            if (in_array($item['char3'],$curs)) {
                $result = dbquery("INSERT INTO ".DB_AL_SHOP_CURRENCY." (shp_currency_current,shp_currency_code,shp_currency_nominal,shp_currency_rate) VALUES ('".$shop_settings['currency_current_updater']."','".$item['char3']."','".$item['size']."','".$item['rate']."')");
            }
        }
    }
    $upd = dbquery("UPDATE ".DB_AL_SHOP_SETTINGS." SET currency_lastupdate='".time()."'");

}
//update_currency();
?>