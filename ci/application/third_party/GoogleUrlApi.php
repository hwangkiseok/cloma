<?php
/**
 * 구글 단축 URL
 */
class GoogleUrlApi{

    function GoogleUrlApi($fKey) {
        $this->fApiUrl = "https://www.googleapis.com/urlshortener/v1/url?key=" . $fKey;
    }

    function shorten($fUrl) {
        $rResponse = $this->send($fUrl);
        return count($rResponse)!=0 ? $rResponse : false;
    }

    function expand($fUrl, $fOption = null) {
        // $fOption Values : ANALYTICS_CLICKS ANALYTICS_TOP_STRINGS FULL
        $rResponse = $this->send($fUrl, false, $fOption);
        return count($rResponse)!=0 ? $rResponse : false;
    }

    function send($fUrl, $fShorten = true, $fOption = null) {
        $fOption = $fOption ? "&projection=$fOption": null;

        $ch = curl_init();
        if($fShorten==true) {
            curl_setopt ($ch, CURLOPT_URL,$this->fApiUrl);
            curl_setopt ($ch, CURLOPT_POST,1);
            curl_setopt ($ch, CURLOPT_POSTFIELDS,json_encode(array("longUrl"=>$fUrl)));
        }
        else {
            curl_setopt ($ch, CURLOPT_URL,$this->fApiUrl.'&shortUrl='.$fUrl.$fOption);
        }
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt ($ch, CURLOPT_SSLVERSION,3);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $rReturn = curl_exec($ch);
        curl_close($ch);
        return json_decode($rReturn,true);
    }

}//end of class GoogleUrlApi
