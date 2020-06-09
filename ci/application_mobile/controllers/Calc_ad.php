<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 */
class Calc_ad extends M_Controller {

    var $_adbrix_id = 'ordersuvin@gmail.com';
    var $_adbrix_pw = 'Fflw*84200';
    var $_adbrix_app_key= 'UDvd4XedQ0CrceXhCwoYbg';

    var $_kakao_id = 'U2FsdGVkX1+U38kZFj/oqS+EPAhbrDF2DxQhioWqY/8eyq8weMuj6NZXSEqLZRdH';
    var $_kakao_pw = 'U2FsdGVkX1+Vhj4u15TQqF5HFp9GIs+JAS0+DWyzmXc=';



    public function __construct() {
        parent::__construct();
        $this->load->library('Snoopy');

    }//end of __construct()


    public function kakao_req_send(){

        $uri = 'https://accounts.kakao.com/login/kakaoforbusiness/';
        $this->snoopy->fetch($uri);

        zsView($this->snoopy->results);

    }

    public function adbrix_req_send(){


        $uri = 'https://console.adbrix.io/login';
        $this->snoopy->fetch($uri);
        $this->snoopy->setcookies();

        /* ------------------------------------------------------------------------------------------------------------------- */

        $uri    = "https://api-console.adbrix.io/api/v1/User/Login";
        $params = "{'email' : '{$this->_adbrix_id}' , 'password' : '{$this->_adbrix_pw}'}";

        $this->snoopy->_adbrix      = true;
        $this->snoopy->_submit_type = 'application/json; charset=utf-8;';
        $this->snoopy->referer      = 'https://console.adbrix.io';

        $ret = $this->snoopy->submit($uri,$params);
        $this->snoopy->setcookies();

        $log_data1 = $this->snoopy->getResults();
        $log_data1 = json_decode($log_data1,true);

        $aInput = array(
                'user_token' => $log_data1['data']['user_token']
            ,   'account_id' => $log_data1['data']['accounts'][0]['account_id']
            ,   'app_key'    => $this->_adbrix_app_key
        );

        $uri = 'https://console.adbrix.io/api/Version';
        $this->snoopy->fetch($uri);
        $this->snoopy->setcookies();

        sleep(1);

        $uri    = "https://api-console.adbrix.io/api/v1/Account/GetAccounts";
        $params = "{'user_token':'{$aInput['user_token']}'}";
        $ret = $this->snoopy->submit($uri,$params);
        $this->snoopy->setcookies();

        zsView($params);
        zsView($ret);


        exit;

        /* ------------------------------------------------------------------------------------------------------------------- */

        if(empty($aInput['account_id']) == false && empty($aInput['user_token']) == false){

            sleep(1);

            unset($params);
            $params = "{\"account_id\":\"{$aInput['account_id']}\",\"user_token\":\"{$aInput['user_token']}\"}";
            $uri    = 'https://api-console.adbrix.io/api/v1/Account/AccountLogin';
            $ret    = $this->snoopy->submit($uri,$params);
            $this->snoopy->setcookies();

            $log_data2 = $this->snoopy->getResults();
            $log_data2 = json_decode($log_data2,true);

            $aInput['auth_token'] = $log_data2['data']['auth_token'];

            $uri = 'https://console.adbrix.io/api/Version';
            $this->snoopy->fetch($uri);

        }

        /* ------------------------------------------------------------------------------------------------------------------- */

        if(empty($aInput['auth_token']) == false){

            sleep(1);

            unset($params);
            $this->snoopy->_adbrix_auth = $aInput['auth_token'];
            $date_s = date("Y-m-d\TH:i:s\Z" , strtotime(date('YmdHis')." -1 month -9 hour"));
            $date_e = date("Y-m-d\TH:i:s\Z" , strtotime(date('YmdHis')." -9 hour"));

            $params = "{'appkeys': ['{$aInput['app_key']}', 'hdyD2mX0QUWdcH5Kuf3LWQ'] , 'this_month_start_date' , '{$date_e}' , 'last_month_start_date' : '{$date_s}' }";
            $uri    = 'https://api-console.adbrix.io/api/v1/Billing/GetBillingUsageByAppkeys';

            $ret = $this->snoopy->submit($uri,$params);
            $this->snoopy->setcookies();
            $resp_data = $this->snoopy->getResults();

            $uri = 'https://console.adbrix.io/api/Version';
            $this->snoopy->fetch($uri);
        }

        zsView('---aInput');
        zsView($aInput);

        zsView('---resp_data');
        zsView($resp_data);

        zsView('---ret');
        zsView($ret);



        exit;

        /* ------------------------------------------------------------------------------------------------------------------- */


        zsView($log_data1);
        zsView('*--------------------------------------------------------------------------------------------');
        zsView($log_data2);
        /*
Array
(
    [data] => Array
        (
            [auth_token] => NpXn13C%2fhu%2brQAWV%2fr3IIaCE44oAcOwWJM2KLN%2bEoAS3do3n0EgKI%2fwnsM5NJiYTevGIjCNTL84gGNAtnJW6NRRbYlvO2Wh%2bdBn%2bte86eMcRPiMQCkYhNP07NfLIjtfZt6ETVeFT20PHuyvmMPLgaqdBYGwyDgvzRT9z%2bME%2fdgGM1pKG5TBiBgE%2b%2bciZfJUeAPTIW3fScdYgAgWdAkn6oq3nV%2fJb1r8uXzCiZNAKs2qDCbo05qwIBvSXn71dKvc%2boLQfe9drA4tL4UE1%2fcd%2b9T%2f3TakZxeAfjFIAaTaN%2b0vIuf2cu7c0RicfLu09gXKR0jy0CR07TYoGeFpYmQ4Rzg%3d%3d
            [expires_at] => 1591339887
        )

    [result] => 1
    [message] => ok
    [version] => 1.12.3.21836
    [last_compile_time] => 2020-06-04T03:07:55+00:00
)
        */

        zsView('*--------------------------------------------------------------------------------------------');







        exit;


        zsView('-----------------------------------------------------------------------------------------------');

        unset($params);

        $uri = 'https://console.adbrix.io/api/Version';
        $ret = $this->snoopy->fetch($uri);
        $this->snoopy->setcookies();



        zsView($ret);exit;


//        $this->snoopy->referer = 'https://console.adbrix.io/8TdID8XlgEaZLi15gzVRbw/attributions/UDvd4XedQ0CrceXhCwoYbg/ad-tracking';

        $params = "{'appkey': 'UDvd4XedQ0CrceXhCwoYbg', 'ignore': false, 'first_index': 0, 'length': 2500}";
        $uri = 'https://api-console.adbrix.io/api/v1/AdCampaign/GetAdCampaigns';

        $this->snoopy->_adbrix_auth = '$user_token';

        $ret = $this->snoopy->submit($uri,$params);


        zsView($ret);

        exit;

//
    }


}//end of class Auth