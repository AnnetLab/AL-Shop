<?php
require_once "../../../../maincore.php";
require_once AL_SHOP_DIR."infusion_db.php";


$result = dbquery("SELECT * FROM ".DB_AL_SHOP_ORDERS." WHERE shp_order_id='".$_POST['item_number']."'");
if (dbrows($result)) {

    $data = dbarray($result);


    $logStr = "";
    $logFd = fopen("ipn.log", "a");


    fwrite($logFd, "****************************************************************************************************\n");

    if(array_key_exists("txn_id", $_POST)) {
        $logStr = "Received IPN,  TX ID : ".htmlspecialchars($_POST["txn_id"]);
        fwrite($logFd, strftime("%d %b %Y %H:%M:%S ")."[IPNListner.php] $logStr\n");
    } else {
        $logStr = "IPN Listner recieved an HTTP request with out a Transaction ID.";
        fwrite($logFd, strftime("%d %b %Y %H:%M:%S ")."[IPNListner.php] $logStr\n");
        fclose($logFd);
        exit;
    }

    $tmpAr = array_merge($_POST, array("cmd" => "_notify-validate"));
    $postFieldsAr = array();
    foreach ($tmpAr as $name => $value) {
        $postFieldsAr[] = "$name=$value";
    }
    $logStr = "Sending IPN values:\n".implode("\n", $postFieldsAr);
    fwrite($logFd, strftime("%d %b %Y %H:%M:%S ")."[IPNListner.php] $logStr\n");

    $ppResponseAr = PPHttpPost("https://www.paypal.com/cgi-bin/webscr", implode("&", $postFieldsAr), false);
    if(!$ppResponseAr["status"]) {
        fwrite($logFd, "--------------------\n");
        $logStr = "IPN Listner recieved an Error:\n";
        if(0 !== $ppResponseAr["error_no"]) {
            $logStr .= "Error ".$ppResponseAr["error_no"].": ";
        }
        $logStr .= $ppResponseAr["error_msg"];
        fwrite($logFd, strftime("%d %b %Y %H:%M:%S ")."[IPNListner.php] $logStr\n");
        fclose($logFd);
        exit;
    }

    fwrite($logFd, "--------------------\n");
    $logStr = "IPN Post Response:\n".$ppResponseAr["httpResponse"];
    fwrite($logFd, strftime("%d %b %Y %H:%M:%S ")."[IPNListner.php] $logStr\n");


    if (strpos($ppResponseAr["httpResponse"], "VERIFIED")!==false) {

        if ($_POST["payment_status"]!="Completed") {
            if ($_POST["payment_status"]=="Pending" ) {
                fwrite($logFd,"ERROR - payment status is not Completed - $_POST[payment_status] | $_POST[pending_reason]\r\n");
                fclose($logFd);
                // а тут отмечаем заказ как оплаченный, но требующий подтверждение оплаты со стороны плательщика
                // такое бывает редко, но все же бывает и лучше подстраховаться.
                $update = dbquery("UPDATE ".DB_AL_SHOP_ORDERS." SET shp_order_payed='2', shp_order_payment_type='3' WHERE shp_order_id='".$data['shp_order_id']."'");
                require_once INCLUDES."sendmail_include.php";
                $subject = $settings['sitename'];
                $message_owner = sprintf($locale['shp217a'],$data['shp_order_id']);
                $message_buyer = sprintf($locale['shp218'],show_cost($data['shp_order_cost'],$shop_settings['currency_default'],$shop_settings['currency_default']),$order_id,$order_id);
                sendemail($data['shp_order_fio'], $data['shp_order_email'], $settings['siteusername'], $settings['siteemail'], $subject, $message_buyer);
                sendemail($settings['siteusername'], $settings['siteemail'], $settings['siteusername'], $settings['siteemail'], $subject, $message_owner);
                return;

            }

            fwrite($logFd,"ERROR - payment status is not Completed - $_POST[payment_status] | $_POST[pending_reason]\r\n");
            fclose($logFd);
            return;
            //update order status

        }

        // тут отмечаем заказ оплаченным.
        // деньги уже на счету продавца
        $update = dbquery("UPDATE ".DB_AL_SHOP_ORDERS." SET shp_order_payed='1', shp_order_payment_type='3' WHERE shp_order_id='".$data['shp_order_id']."'");

        require_once INCLUDES."sendmail_include.php";
        $subject = $settings['sitename'];
        $message_owner = sprintf($locale['shp217'],$_GET['InvId']);
        $message_buyer = sprintf($locale['shp218'],show_cost($data['shp_order_cost'],$shop_settings['currency_default'],$shop_settings['currency_default']),$order_id,$order_id);
        sendemail($data['shp_order_fio'], $data['shp_order_email'], $settings['siteusername'], $settings['siteemail'], $subject, $message_buyer);
        sendemail($settings['siteusername'], $settings['siteemail'], $settings['siteusername'], $settings['siteemail'], $subject, $message_owner);


    }

} else {
    $log = fopen("ipn.log", "a");
    fwrite($log, "ipn is calling $_POST[item_number]\r\n");
}

function PPHttpPost($url_, $postFields_, $parsed_) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url_);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch,CURLOPT_POSTFIELDS,$postFields_);

    $httpResponse = curl_exec($ch);

    if (curl_errno($ch) == 60) {

        curl_setopt($ch, CURLOPT_CAINFO,
            dirname(__FILE__) . '/cacert.pem');
        $httpResponse = curl_exec($ch);
    }

    if(!$httpResponse) {
        return array("status" => false, "error_msg" => curl_error($ch), "error_no" => curl_errno($ch));
    }

    if(!$parsed_) {
        return array("status" => true, "httpResponse" => $httpResponse);
    }

    $httpResponseAr = explode("\n", $httpResponse);

    $httpParsedResponseAr = array();
    foreach ($httpResponseAr as $i => $value) {
        $tmpAr = explode("=", $value);
        if(sizeof($tmpAr) > 1) {
            $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
        }
    }

    if(0 == sizeof($httpParsedResponseAr)) {
        $error = "Invalid HTTP Response for POST request($postFields_) to $url_.";
        return array("status" => false, "error_msg" => $error, "error_no" => 0);
    }
    return array("status" => true, "httpParsedResponseAr" => $httpParsedResponseAr);

}


?>