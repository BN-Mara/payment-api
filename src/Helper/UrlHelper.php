<?php

use App\Entity\Payment;

class UrlHelper{
    public static function paymentUrl(Payment $payment): string{
        
        $postData = ["PayType"=>"MaxiCash","Amount"=>$payment->getAmount(),"Currency"=>$payment->getCurrency(),
        "Telephone"=>$payment->getPhone(),"Email"=>$payment->getEmail(),"MerchantID"=>"2bd7fd5caedc48dd8c5bcabee629812b","MerchantPassword"=>"55a6046137584680abddafe262985ff2",
        "Language"=>"fr","Reference"=>$payment->getReference(),"Accepturl"=>"https://maajaburafiki.com/web/success",
        "Cancelurl"=>"https://maajaburafiki.com/web/failed","Declineurl"=>"https://maajaburafiki.com/web/failed",
        "NotifyURL"=>"https://maajaburafiki.com/web/failed"];
        
        $jsonData = json_encode($postData);
        $maxiUrl = 'https://api.maxicashapp.com/payentry?data='.$jsonData;
        return $maxiUrl;
       
    }
}