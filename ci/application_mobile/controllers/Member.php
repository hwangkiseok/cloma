<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @date 170905
 * @authour 황기석
 * @desc  로그인/로그아웃 관련 컨트롤러
 *      - 웹 : 카카오, 네이버, 페이스북
 *
 */

define("NAVER_CLIENT_KEY"       , 'mgCk4vgeHcdDAJd73kR9');
define('NAVER_SECRET_KEY'       , 'K96I8Xba0L');
define('NAVER_GET_USERINFO_URL' , 'https://openapi.naver.com/v1/nid/me');

define('FACEBOOK_CLIENT_KEY'    , '');
define('FACEBOOK_SECRET_KEY'    , '');

define('KAKAO_CLIENT_KEY'       , 'b26beb2de048fe8731afc9b260d71da2');
define('KAKAO_GET_USERINFO_URL' , 'https://kapi.kakao.com/v1/user/me');
define('KAKAO_GET_USERINFO_URL_V2' , 'https://kapi.kakao.com/v2/user/me');

define('GOOGLE_CLIENT_KEY'    , '617498554101-2dfhpj88abmkmgc56vn8c4pvks94s0d3.apps.googleusercontent.com');
define('GOOGLE_SECRET_KEY'    , 'yhwa5DDhzxRjjk6w6FQ1jtQO');

define('REJOIN_PROCESS_ERR'     , '회원 재가입 구분자가 누락.\n다시 시도해주세요.');
define('LOGIN_PROCESS_ERR'      , '로그인 시도중 문제가 발생하였습니다.\n다시 시도해주세요.');
define('PROFILE_PROCESS_ERR'    , '서버로부터 회원정보를 받지 못했습니다.\n다시 시도해주세요.');

class Member extends M_Controller {

    public $aInput;
    public $arrayParams;

    public function __construct() {
        parent::__construct();


        $this->load->helper('string');
        $alnum = random_string('alnum',32);

        //if($this->input->ip_address() != '121.131.27.155'){ show_404();exit; }

        /**
         * @date 170905
         * @authour 황기석
         * @desc 추가 예외처리
         *      1 앱인경우
         *      2 로그인되어 있는 경우
         *      3 회원가입시 이메일주소가 없는경우 (페이스북)
         */

        /* END */

        $this->load->model("member_model");
        $this->load->library('encryption');

        //로그인시 중복 호출방지를 위해 로그인 된 상태에서는 back url 또는 메인으로 이동
        $ignore_method = array('certify');

        if(member_login_status() && in_array($this->router->fetch_method(),$ignore_method) == false){
            if(empty($this->input->get('r_url')) == false){
                header('Location: ' . $this->input->get('r_url'));
            }else {
                header('Location: ' . $this->config->item('default_http'));
            }
        }

        //APP 접근일때
        if( is_app() ) {
            $join_path = '1';
        }
        //아닐때
        else {
            if ( $this->agent->is_mobile() ) {
                $join_path = '2';
            }
            else {
                $join_path = '3';
            }
        }
        $this->arrayParams = array();
        $this->aInput      = array(
            'NAVER_CLIENT_KEY'      => NAVER_CLIENT_KEY
        ,   'NAVER_SECRET_KEY'      => NAVER_SECRET_KEY
        ,   'NAVER_CALLBACK_URL'    => urlencode($this->config->item('default_http').'/member/naver_callback')
        ,   'NAVER_REQUEST_URL'     => "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=".NAVER_CLIENT_KEY."&redirect_uri=".urlencode($this->config->item('default_http').'/member/naver_callback?r_url='.urlencode($this->input->get('r_url')))."&svctype=0"

        ,   'FACEBOOK_CLIENT_KEY'   => FACEBOOK_CLIENT_KEY
        ,   'FACEBOOK_SECRET_KEY'   => FACEBOOK_SECRET_KEY
        ,   'FACEBOOK_CALLBACK_URL' => urlencode($this->config->item('default_http').'/member/facebook_callback?r_url='. urlencode($this->input->get('r_url')?$this->input->get('r_url'):'/'))
        ,   'FACEBOOK_REQUEST_URL'  => "https://www.facebook.com/v2.10/dialog/oauth?client_id=".FACEBOOK_CLIENT_KEY."&redirect_uri=".urlencode($this->config->item('default_http')."/member/facebook_callback?r_url=".urlencode($this->input->get('r_url')?$this->input->get('r_url'):'/'))."&auth_type=rerequest&scope=email"

        ,   'KAKAO_CLIENT_KEY'      => KAKAO_CLIENT_KEY
        ,   'KAKAO_CALLBACK_URL'    => urlencode($this->config->item('default_http').'/member/kakao_callback')
        ,   'KAKAO_REQUEST_URL'     => "https://kauth.kakao.com/oauth/authorize?client_id=".KAKAO_CLIENT_KEY."&redirect_uri=".urlencode($this->config->item('default_http').'/member/kakao_callback')."&response_type=code&state=".urlencode($this->input->get('r_url'))

        ,   'GOOGLE_CLIENT_KEY'   => GOOGLE_CLIENT_KEY
        ,   'GOOGLE_SECRET_KEY'   => GOOGLE_SECRET_KEY

        ,   'GOOGLE_CALLBACK_URL'    => urlencode($this->config->item('default_http').'/member/google_callback')
        ,   'GOOGLE_REQUEST_URL'     => "https://accounts.google.com/o/oauth2/v2/auth"
                ."?response_type=code"
                ."&client_id=".GOOGLE_CLIENT_KEY
                ."&scope=openid%20email%20profile"
                ."&redirect_uri=".urlencode($this->config->item('default_http').'/member/google_callback')
                ."&state=".urlencode($this->input->get('r_url'))

        ,   'join_path'             => $join_path
        ,   'rUrl'                  => $this->input->get('r_url')?$this->input->get('r_url'):$this->config->item('default_http')
        ,   'rUrl_kakao'            => $this->input->get('state')?$this->input->get('state'):''//kakao 에서 콜백 파라메터를 전달할수 없어 임시로 state로 처리
        );

        $this->arrayParams2 = array();

    }//end of __construct()


    public function index(){
        $this->member_login();
    }


    public function member_loc_login(){

        show_404();

        if(member_login_status()){ //앱인경우
            redirect('/');exit;
        }

        $options = array('title' => '옷쟁이들 로그인' , 'top_type' => 'back');
        $this->_header($options);
        $this->load->view("/member/loc_login");
        $this->_footer();

    }

    public function member_loc_join_form(){

        show_404();

        if(member_login_status()){ //앱인경우
            redirect('/');exit;
        }

        $sql = "SELECT * FROM board_help_tb WHERE bh_division = '3' ";
        $terms_of_use = $this->db->query($sql)->row_array();


        $sql = "SELECT * FROM board_help_tb WHERE bh_division = '4' ";
        $privacy = $this->db->query($sql)->row_array();

        $sql = "SELECT * FROM board_help_tb WHERE bh_division = '7' ";
        $event_use = $this->db->query($sql)->row_array();

        $options = array('title' => '옷쟁이들 회원가입' , 'top_type' => 'back');
        $this->_header($options);
        $this->load->view("/member/loc_join_form" , array(
                'terms_of_use'  => $terms_of_use
            ,   'privacy'       => $privacy
            ,   'event_use'     => $event_use
        ));
        $this->_footer();

    }





    public function member_login(){

        if(member_login_status()){ //앱인경우
            redirect('/');exit;
        }

        $aInput = $this->aInput;
        unset($aInput['NAVER_SECRET_KEY']);
        unset($aInput['FACEBOOK_SECRET_KEY']);

        $options = array('title' => '로그인' , 'top_type' => 'back');
        $this->_header($options);
        $this->load->view("/member/login", $aInput);
        $this->_footer();
    }

    public function certify(){

        member_login_check();

        $aInput = array( 'callback_url' => $this->input->get('c_url')?$this->input->get('c_url'):'/'
                    ,    'locType'      => $this->input->get('locType')?$this->input->get('locType'):''
        );

        $aMemberIfo = $this->_get_member_info_arr();

        if($aMemberIfo['m_auth_no']){ //이미 인증된 회원이 인증페이지 접근시 처리 프로세스
            redirect($aInput['callback_url']);
        }
        $options = array();
        if(get_app_version_code() >= "60"){
            $options = array("top_type" => "back", "title" => "회원인증");

        }
        
        $this->_header($options);
        $this->load->view("/member/certify",$aInput);
        $this->_footer();
    }


    public function member_google_callback(){

        $aInput     = $this->aInput;

        /*토큰 정보*/
        $code       = $this->input->get('code', true);
        $is_post    = true;
        $url        = "https://accounts.google.com/o/oauth2/token";
        $post_params     =   "code=".$code
                            ."&client_id=".GOOGLE_CLIENT_KEY
                            ."&client_secret=".GOOGLE_SECRET_KEY
                            ."&redirect_uri=".$aInput['GOOGLE_CALLBACK_URL']
                            ."&grant_type=authorization_code";

        $headers = array( 'Content-Type:application/x-www-form-urlencoded' );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        zsView(curl_error($ch));
        curl_close ($ch);

//        zsView($code);
//        zsView($_REQUEST);
//        zsView($status_code);
//        zsView($response);exit;

        if($status_code != 200) {
            alert(LOGIN_PROCESS_ERR.'[1]', '/');
            exit;
        }

        $data = json_decode($response, true);
        $_SESSION['google_access_token'] = $data['access_token'];

        /*
Array
(
[access_token] => ya29.Il-8BxMmkQRQvu4JcdQjxwO0rY4MNQ0XliDhjnwkn6XnxSZu7-Ql3b9LW8NjIXG6wtHnSaGs768M2zbdqaAyZbDDH1CM8xT9x2T5pn3dg3Ymqu0Zy2_ijP8i8t7DdOQgMw
[expires_in] => 3599
[scope] => https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile openid
[token_type] => Bearer
[id_token] => eyJhbGciOiJSUzI1NiIsImtpZCI6ImIyZWQwZGIxZjY2MWQ4OTg5OTY5YmFiNzhkMmZhZTc1NjRmZGMzYTkiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiI2MTc0OTg1NTQxMDEtMmRmaHBqODhhYm1rbWdjNTZ2bjhjNHB2a3M5NHMwZDMuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiI2MTc0OTg1NTQxMDEtMmRmaHBqODhhYm1rbWdjNTZ2bjhjNHB2a3M5NHMwZDMuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMDgwNjExNzIyNjA2ODI4NzA4MzgiLCJlbWFpbCI6InpldXM3MjExQGdtYWlsLmNvbSIsImVtYWlsX3ZlcmlmaWVkIjp0cnVlLCJhdF9oYXNoIjoiZHZHcDAyclZNTVJXZng4b2NtazJqZyIsIm5hbWUiOiLtmanquLDshJ0iLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDYuZ29vZ2xldXNlcmNvbnRlbnQuY29tLy1ZV3ZPMjVzenhOSS9BQUFBQUFBQUFBSS9BQUFBQUFBQUFBQS9BQ0hpM3JmTHBSWU8tNHFYRkViMWVnMXpqNHB2N3NxbXZRL3M5Ni1jL3Bob3RvLmpwZyIsImdpdmVuX25hbWUiOiLquLDshJ0iLCJmYW1pbHlfbmFtZSI6Iu2ZqSIsImxvY2FsZSI6ImtvIiwiaWF0IjoxNTgwNzIwNzYyLCJleHAiOjE1ODA3MjQzNjJ9.DmtfrEvtvoKvvSvnvXEUN41-VrFPt_pruNYynR1ZO7d2Lv0Ih4FR0IoB00THACqBwuTwGh6ZgnfF4to7egMPe2G37odlIZ-N4O5YpA6nQPifXTzKkewnRJvriAOkK_D7ts5lanB-AgY4I7XWGwFoItWqfO5-bS4G8gG1M3wIfinmio0XHWmyDEYvNiyPv3ZDdRREloQzMErm4L5vWXilbkuYePFBeTSU4vMnVx2y0K3HhSxrS9mQr-FKld--gugMDxfrezvKUTQ7EYfr0WrvvCUP_5FZlX_5Vx-ErVV7iLj60wClzTbOge-tCHosza3xAitI_0H8t6jDxT2hses13A
)
        */
        /*토큰 정보 END*/


        /*회원 정보*/
        unset($url);

        $url        = "https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token={$data['access_token']}";
        $headers = array( 'Content-Type:application/x-www-form-urlencoded; charset=UTF-8' );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        if($status_code != 200) {
            alert(PROFILE_PROCESS_ERR.'[2]','/');
            exit;
        }

        $tmpProfile = json_decode($response, true);
        /*
Array
{
"id": "",
"email": "",
"verified_email": true,
"name": "황기석",
"given_name": "기석",
"family_name": "황",
"picture": "https://lh6.googleusercontent.com/-YWvO25szxNI/AAAAAAAAAAI/AAAAAAAAAAA/ACHi3rfLpRYO-4qXFEb1eg1zj4pv7sqmvQ/photo.jpg",
"locale": "ko"
}
        */

        $profile = array(
            'id'            => $tmpProfile['id']
        ,   'nickname'      => $tmpProfile['name']
        ,   'email'         => $tmpProfile['email']
        ,   'profile_image' => $tmpProfile['picture']
        );

        /*회원 정보 END*/

        $initInput = array(
            'm_division'        => 2
        ,   'm_sns_site'        => 4
        ,   'm_loginid_prv_str' => 'go'
        ,   'access_token'      => $data['access_token']
        ,   'join_path'         => $aInput['join_path']
        ,   'code'              => $code
        ,   'state'             => $state?$state:''
        );

        $this->arrayParams = array_merge($initInput,$profile);

        $this->join_proc();

    }


    public function kakaoSyncCall(){

        ajax_request_check();

        $aInput     = $this->aInput;

        $data = arraY(
                'token_type'    => $this->input->post('token_type')
            ,   'access_token'  => $this->input->post('access_token')
            ,   'return_url'    => $this->input->post('return_url')
        );

        if(empty($data['return_url']) == false) $this->aInput['rUrl'] = $data['return_url'];

        /*회원 정보*/
        $headers    = array();
        $headers[]  = "Authorization: {$data['token_type']} {$data['access_token']}";
        $headers[]  = "Content-type: application/x-www-form-urlencoded;charset=utf-8'";
        $is_post    = false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, KAKAO_GET_USERINFO_URL_V2);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        if($status_code != 200) {
            alert(PROFILE_PROCESS_ERR,'/');
            exit;
        }

        $tmpProfile = json_decode($response, true);

        $url = "https://kapi.kakao.com/v1/user/shipping_address";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        if($status_code != 200) {
            alert(PROFILE_PROCESS_ERR,'/');
            exit;
        }

        $delivery_addr = json_decode($response, true);

        if ($delivery_addr['shipping_addresses_needs_agreement'] == false) { // 배송지정보가 존재하면
            foreach ($delivery_addr['shipping_addresses'] as $key => $val) {

                if ($val['is_default'] == true) {

                    $phone_arr1 = '';
                    $phone_arr2 = '';
                    $receiver_phone_number1 = '';
                    $receiver_phone_number2 = '';

                    if (isset($val['receiver_phone_number1'])) {
                        $phone_arr1 = explode('-', $val['receiver_phone_number1']);
                        $receiver_phone_number1 = $phone_arr1[0] . $phone_arr1[1] . $phone_arr1[2];
                    }

                    if (isset($val['receiver_phone_number2'])) {
                        $phone_arr2 = explode('-', $val['receiver_phone_number2']);
                        $receiver_phone_number2 = $phone_arr2[0] . $phone_arr2[1] . $phone_arr2[2];
                    }

                    $tmpProfile['name'] = $val['name'];
                    $tmpProfile['addr_type'] = $val['type'];
                    $tmpProfile['addr_post'] = $val['zip_code'];
                    $tmpProfile['addr_post_new'] = $val['zone_number'];
                    $tmpProfile['addr1'] = $val['base_address'];
                    $tmpProfile['addr2'] = $val['detail_address'];
                    $tmpProfile['receiver_name'] = $val['receiver_name'];
                    $tmpProfile['receiver_phone_number1'] = $receiver_phone_number1;
                    $tmpProfile['receiver_phone_number2'] = $receiver_phone_number2;
                }

            }
        } else {

            $tmpProfile['name'] = '';
            $tmpProfile['addr_type'] = '';
            $tmpProfile['addr_post'] = '';
            $tmpProfile['addr_post_new'] = '';
            $tmpProfile['addr1'] = '';
            $tmpProfile['addr2'] = '';
            $tmpProfile['receiver_name'] = '';
            $tmpProfile['receiver_phone_number1'] = '';
            $tmpProfile['receiver_phone_number2'] = '';
        }

        $phone_arr = '';
        $phone_number = '';
        $phone_number_country = '';
        if ($tmpProfile['kakao_account']['has_phone_number'] == true) {
            $phone_arr = explode(' ', $tmpProfile['kakao_account']['phone_number']);
            $phone_number_country = $phone_arr[0];

            if ($phone_arr[0] == '+82') {
                $phone_number = '0' . $phone_arr[1];

                $phone_num_arr = explode('-', $phone_number);

                $phone_number = $phone_num_arr[0] . $phone_num_arr[1] . $phone_num_arr[2];
            } else {
                $phone_number = $phone_arr[1];
            }
        }

        $profile = array(
            'id'                => $tmpProfile['id']
        ,   'nickname'      => $tmpProfile['properties']['nickname']
        ,   'email'         => $tmpProfile['kakao_account']['has_email'] ? $tmpProfile['kakao_account']['email'] : ''
        ,   'profile_image' => $tmpProfile['properties']['profile_image']
        );

        // 확장 데이터
        $profile_ext = array(
            'sns_id'                => $tmpProfile['id']
        ,   'sns_site'              => '1'
        ,   'nickname'              => $tmpProfile['properties']['nickname']
        ,   'profile_image'         => $tmpProfile['properties']['profile_image']
        ,   'profile_image_thumb'   => $tmpProfile['properties']['thumbnail_image']
        ,   'email_needs_yn'        => $tmpProfile['kakao_account']['has_email'] == true ? 'N' : 'Y'
        ,   'email'                 => $tmpProfile['kakao_account']['has_email'] == true ? $tmpProfile['kakao_account']['email'] : ''
        ,   'email_valid_yn'        => $tmpProfile['kakao_account']['is_email_valid'] == true ? 'Y' : 'N'
        ,   'gender_needs_yn'       => $tmpProfile['kakao_account']['has_gender'] == true ? 'N' : 'Y'
        ,   'gender'                => $tmpProfile['kakao_account']['has_gender'] == true ? ($tmpProfile['kakao_account']['gender'] == 'male' ? 'M' : 'F') : ''
        ,   'birthyear_needs_yn'    => $tmpProfile['kakao_account']['has_birthyear'] == true ? 'N' : 'Y'
        ,   'birthyear'             => $tmpProfile['kakao_account']['has_birthyear'] == true ? $tmpProfile['kakao_account']['birthyear'] : ''
        ,   'birthday_needs_yn'     => $tmpProfile['kakao_account']['has_birthday'] == true ? 'N' : 'Y'
        ,   'birthday'              => $tmpProfile['kakao_account']['has_birthday'] == true ? $tmpProfile['kakao_account']['birthday'] : ''
        ,   'age_range_needs_yn'    => $tmpProfile['kakao_account']['has_age_range'] == true ? 'N' : 'Y'
        ,   'age_range'             => $tmpProfile['kakao_account']['age_range'] == true ?  $tmpProfile['kakao_account']['age_range'] : ''
        ,   'phone_number_country'  => $phone_number_country
        ,   'phone_number'          => $phone_number
        ,   'name'                  => $tmpProfile['name']
        ,   'addr_type'             => $tmpProfile['addr_type']
        ,   'addr_post'             => $tmpProfile['addr_post']
        ,   'addr_post_new'         => $tmpProfile['addr_post_new']
        ,   'addr1'                 => $tmpProfile['addr1']
        ,   'addr2'                 => $tmpProfile['addr2']
        ,   'receiver_name'         => $tmpProfile['receiver_name']
        ,   'receiver_phone_number1'=> $tmpProfile['receiver_phone_number1']
        ,   'receiver_phone_number2'=> $tmpProfile['receiver_phone_number2']
        ,   'login_path'            => 'web'
        );

        $this->arrayParams2 = $profile_ext;

        /*회원 정보 END*/

        $initInput = array(
            'm_division'        => 2
        ,   'm_sns_site'        => 1
        ,   'm_loginid_prv_str' => 'ka'
        ,   'access_token'      => $data['access_token']
        ,   'join_path'         => $aInput['join_path']
        ,   'code'              => ''
        ,   'state'             => ''
        );
        $this->arrayParams = array_merge($initInput, $profile);

        $this->join_proc();

    }

    ///kakao/oauth
    public function member_kakao_callback(){

        $aInput     = $this->aInput;

        /*토큰 정보*/
        $code       = $this->input->get('code', true);
        $state      = '';
        $is_post    = false;
        $url        = "https://kauth.kakao.com/oauth/token?grant_type=authorization_code&client_id={$aInput['KAKAO_CLIENT_KEY']}&redirect_uri={$aInput['KAKAO_CALLBACK_URL']}&code={$code}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        zsView(curl_error($ch));

        curl_close ($ch);



//        zsView($code);
//        zsView($_REQUEST);
//        zsView($status_code);
//        zsView($response);exit;


        if($status_code != 200) {
            alert(LOGIN_PROCESS_ERR, '/');
            exit;
        }

        $data = json_decode($response, true);

        $_SESSION['kakao_access_token'] = $data['access_token'];

        /*
         Array (
            [access_token] => xhd6CG85NeE0u7dvTPHEtHF-ZwvJMCp4Ex_l5Ao8BVUAAAFeVQ3U9Q
            [token_type] => bearer
            [refresh_token] => gn0_svxvp8a5I8659awKmz00-GhoHgCXFoXyMQo8BVUAAAFeVQ3U8A
            [expires_in] => 21599
            [scope] => account_email profile story_publish story_read
         )
        */
        /*토큰 정보 END*/

        /*회원 정보*/
        $headers[0]   = "Authorization: {$data['token_type']} {$data['access_token']}";
        $headers[]    = "Content-type: application/x-www-form-urlencoded;charset=utf-8'";
        $is_post      = false;

        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, KAKAO_GET_USERINFO_URL);
        curl_setopt($ch, CURLOPT_URL, KAKAO_GET_USERINFO_URL_V2); // V2로 변경
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        if($status_code != 200) {
            alert(PROFILE_PROCESS_ERR,'/');
            exit;
        }

        $tmpProfile = json_decode($response, true);
        /*
         Array
            (
                [kaccount_email] => zeus721@naver.com
                [kaccount_email_verified] => 1
                [id] => 497393650
                [properties] => Array
                    (
                        [profile_image] =>
                        [nickname] => 황기석
                        [thumbnail_image] =>
                    )

            )
        */


        $profile = array(
            'id'                => $tmpProfile['id']
        ,   'nickname'      => $tmpProfile['properties']['nickname']
        ,   'email'         => $tmpProfile['kakao_account']['has_email'] ? $tmpProfile['kakao_account']['email'] : ''
        ,   'profile_image' => $tmpProfile['properties']['profile_image']
        );




        // 카카오싱크 api변경으로 인한 확장 데이터 dhkim
        if(1){

            // 배송지 정보 api
            $url = "https://kapi.kakao.com/v1/user/shipping_address";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, $is_post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);



            if ($status_code != 200) {
                alert(PROFILE_PROCESS_ERR, '/');
                exit;
            }

            $delivery_addr = json_decode($response, true);

            if ($delivery_addr['shipping_addresses_needs_agreement'] == false) { // 배송지정보가 존재하면
                foreach ($delivery_addr['shipping_addresses'] as $key => $val) {

                    if ($val['is_default'] == true) {

                        $phone_arr1 = '';
                        $phone_arr2 = '';
                        $receiver_phone_number1 = '';
                        $receiver_phone_number2 = '';

                        if (isset($val['receiver_phone_number1'])) {
                            $phone_arr1 = explode('-', $val['receiver_phone_number1']);
                            $receiver_phone_number1 = $phone_arr1[0] . $phone_arr1[1] . $phone_arr1[2];
                        }

                        if (isset($val['receiver_phone_number2'])) {
                            $phone_arr2 = explode('-', $val['receiver_phone_number2']);
                            $receiver_phone_number2 = $phone_arr2[0] . $phone_arr2[1] . $phone_arr2[2];
                        }

                        $tmpProfile['name'] = $val['name'];
                        $tmpProfile['addr_type'] = $val['type'];
                        $tmpProfile['addr_post'] = $val['zip_code'];
                        $tmpProfile['addr_post_new'] = $val['zone_number'];
                        $tmpProfile['addr1'] = $val['base_address'];
                        $tmpProfile['addr2'] = $val['detail_address'];
                        $tmpProfile['receiver_name'] = $val['receiver_name'];
                        $tmpProfile['receiver_phone_number1'] = $receiver_phone_number1;
                        $tmpProfile['receiver_phone_number2'] = $receiver_phone_number2;
                    }

                }
            } else {

                $tmpProfile['name'] = '';
                $tmpProfile['addr_type'] = '';
                $tmpProfile['addr_post'] = '';
                $tmpProfile['addr_post_new'] = '';
                $tmpProfile['addr1'] = '';
                $tmpProfile['addr2'] = '';
                $tmpProfile['receiver_name'] = '';
                $tmpProfile['receiver_phone_number1'] = '';
                $tmpProfile['receiver_phone_number2'] = '';
            }

            $phone_arr = '';
            $phone_number = '';
            $phone_number_country = '';
            if ($tmpProfile['kakao_account']['has_phone_number'] == true) {
                $phone_arr = explode(' ', $tmpProfile['kakao_account']['phone_number']);
                $phone_number_country = $phone_arr[0];

                if ($phone_arr[0] == '+82') {
                    $phone_number = '0' . $phone_arr[1];

                    $phone_num_arr = explode('-', $phone_number);

                    $phone_number = $phone_num_arr[0] . $phone_num_arr[1] . $phone_num_arr[2];
                } else {
                    $phone_number = $phone_arr[1];
                }
            }

            // 확장 데이터
            $profile_ext = array(
                'sns_id' => $tmpProfile['id']
                , 'sns_site' => '1'
                , 'nickname' => $tmpProfile['properties']['nickname']
                , 'profile_image' => $tmpProfile['properties']['profile_image']
                , 'profile_image_thumb' => $tmpProfile['properties']['[thumbnail_image']
                , 'email_needs_yn' => $tmpProfile['kakao_account']['has_email'] == true ? 'N' : 'Y'
                , 'email' => $tmpProfile['kakao_account']['has_email'] == true ? $tmpProfile['kakao_account']['email'] : ''
                , 'email_valid_yn' => $tmpProfile['kakao_account']['is_email_valid'] == true ? 'Y' : 'N'
                , 'gender_needs_yn' => $tmpProfile['kakao_account']['has_gender'] == true ? 'N' : 'Y'
                , 'gender' => $tmpProfile['kakao_account']['has_gender'] == true ? ($tmpProfile['kakao_account']['gender'] == 'male' ? 'M' : 'F') : ''
                , 'birthyear_needs_yn' => $tmpProfile['kakao_account']['has_birthyear'] == true ? 'N' : 'Y'
                , 'birthyear' => $tmpProfile['kakao_account']['has_birthyear'] == true ? $tmpProfile['kakao_account']['birthyear'] : ''
                , 'birthday_needs_yn' => $tmpProfile['kakao_account']['has_birthday'] == true ? 'N' : 'Y'
                , 'birthday' => $tmpProfile['kakao_account']['has_birthday'] == true ? $tmpProfile['kakao_account']['birthday'] : ''
                , 'age_range_needs_yn' => $tmpProfile['kakao_account']['has_age_range'] == true ? 'N' : 'Y'
                , 'age_range' => $tmpProfile['kakao_account']['age_range'] == true ? $tmpProfile['kakao_account']['age_range'] : ''
                , 'phone_number_country' => $phone_number_country
                , 'phone_number' => $phone_number
                , 'name' => $tmpProfile['name']
                , 'addr_type' => $tmpProfile['addr_type']
                , 'addr_post' => $tmpProfile['addr_post']
                , 'addr_post_new' => $tmpProfile['addr_post_new']
                , 'addr1' => $tmpProfile['addr1']
                , 'addr2' => $tmpProfile['addr2']
                , 'receiver_name' => $tmpProfile['receiver_name']
                , 'receiver_phone_number1' => $tmpProfile['receiver_phone_number1']
                , 'receiver_phone_number2' => $tmpProfile['receiver_phone_number2']
                , 'login_path'            => 'web'
            );

            $this->arrayParams2 = $profile_ext;
        }


        /*회원 정보 END*/

        $initInput = array(
            'm_division'        => 2
        ,   'm_sns_site'        => 1
        ,   'm_loginid_prv_str' => 'ka'
        ,   'access_token'      => $data['access_token']
        ,   'join_path'         => $aInput['join_path']
        ,   'code'              => $code
        ,   'state'             => $state?$state:''
        );

        $this->arrayParams = array_merge($initInput,$profile);

        $this->join_proc();

    }

    public function member_facebook_callback(){

        $aInput     = $this->aInput;
        $code       = $this->input->get('code', true);
        $state      = '';
        $aInput     = array_merge($aInput,array('code' => $code)) ;

        /*토큰 정보*/
        $url        = "https://graph.facebook.com/v2.10/oauth/access_token?client_id={$aInput['FACEBOOK_CLIENT_KEY']}&redirect_uri={$aInput['FACEBOOK_CALLBACK_URL']}&client_secret={$aInput['FACEBOOK_SECRET_KEY']}&code={$aInput['code']}";
        $is_post    = false;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        /*
        if(zsDebug()){
            //<a href="#none" class="sns_log03" onclick="go_link('https://www.facebook.com/v2.10/dialog/oauth?client_id=1734182536622004&amp;
            //redirect_uri=
            //http%3A%2F%2Fm.mysdis.co.kr%2Fmember%2Ffacebook_callback%3Fr_url%3D&amp;auth_type=rerequest&amp;scope=email');">페이스북으로 시작하기</a>
            //http%3A%2F%2Fm.mysdis.co.kr%2Fmember%2Ffacebook_callback%3Fr_url%3D
            //http%3A%2F%2Fm.mysdis.co.kr%2Fmember%2Ffacebook_callback%3Fr_url%3D
            //http%3A%2F%2Fm.mysdis.co.kr%2Fmember%2Ffacebook_callback%3Fr_url%3D

            zsView(urldecode('http%3A%2F%2Fm.mysdis.co.kr%2Fmember%2Ffacebook_callback%3Fr_url%3D'));
            //zsView($aInput['rUrl']);
            zsView($response,true);
        }
        */
        if($status_code != 200) {
            alert(LOGIN_PROCESS_ERR, '/');
            exit;
        }

        //log_message('zs','facebook regist callback resp :: '.$response);

        $data = json_decode($response, true);
        /*
         $data Array
            (
                [access_token] => EAAYpOtYSL7QBAM8nE35b2fnZBpjHlvf2SRPfhYZCBUymcrA8WBg1613JFpFc5B9UKbZCouOAM1ZBwueQvnFSCIOePloZA7MmZBQ5RE7ZBpIZCpdhHry17mu9qzbh8ioZAx8WndJKZAtJbimyvmynA4i79uMdZCk84olHCjXtmIJS6Hk4AZDZD
                [token_type] => bearer
                [expires_in] => 5182871
            )
        */
        /*토큰 정보*/

        /*회원 정보*/
        $url =  "https://graph.facebook.com/v2.10/me?access_token={$data['access_token']}&fields=id,name,email,picture";//&debug=all

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $tmpProfile = json_decode($response, true);
        /*
         $data Array
            (
                [id] => 10209917097865736
                [name] => Silver D Kim
                [email] => isinken@naver.com
                [picture] => Array
                    (
                        [data] => Array
                            (
                                [is_silhouette] =>
                                [url] => https://scontent.xx.fbcdn.net/v/t1.0-1/c210.90.540.540/s50x50/19598985_10209603574427846_4082328045461280856_n.jpg?oh=1b8dfa07115dee49cff69841c8d7f8da&oe=5A57A2B6
                            )

                    )
        )*/

        if($status_code != 200) {
            alert(PROFILE_PROCESS_ERR,'/');
            exit;
        }

        $profile = array(   'id'            => $tmpProfile['id']
        ,   'nickname'      => $tmpProfile['name']
        ,   'email'         => $tmpProfile['email']
        ,   'profile_image' => $tmpProfile['picture']['data']['url']
        );
        /*회원 정보 END*/

        // 확장 데이터
        $profile_ext = array(
            'sns_id' => $tmpProfile['id']
            , 'sns_site' => '3'
            , 'nickname' => $tmpProfile['name']
            , 'profile_image' => $tmpProfile['picture']['data']['url']
            , 'profile_image_thumb' => ''
            , 'email_needs_yn' => 'Y'
            , 'email' => $tmpProfile['email']
            , 'email_valid_yn' => 'Y'
            , 'gender_needs_yn' => 'Y'
            , 'gender' => ''
            , 'birthyear_needs_yn' => 'Y'
            , 'birthyear' => ''
            , 'birthday_needs_yn' => 'Y'
            , 'birthday' => ''
            , 'age_range_needs_yn' => 'Y'
            , 'age_range' => ''
            , 'phone_number_country' => ''
            , 'phone_number' => ''
            , 'name' => ''
            , 'addr_type' => ''
            , 'addr_post' => ''
            , 'addr_post_new' => ''
            , 'addr1' => ''
            , 'addr2' => ''
            , 'receiver_name' => ''
            , 'receiver_phone_number1' => ''
            , 'receiver_phone_number2' => ''
            , 'login_path'            => 'web'
        );

        $this->arrayParams2 = $profile_ext;

        $initInput = array(
            'm_division'        => 2
        ,   'm_sns_site'        => 3
        ,   'm_loginid_prv_str' => 'fb'
        ,   'access_token'      => $data['access_token']
        ,   'join_path'         => $aInput['join_path']
        ,   'code'              => $code
        ,   'state'             => $state?$state:''
        );

        $this->arrayParams = array_merge($initInput,$profile);

        $this->join_proc();

    }

    public function member_naver_callback(){

        $aInput = $this->aInput;
        /*토큰 정보*/
        $client_id      = $aInput['NAVER_CLIENT_KEY'];
        $client_secret  = $aInput['NAVER_SECRET_KEY'];
        $code           = $_GET["code"];
        $state          = $_GET["state"];
        $redirectURI    = urlencode($aInput['NAVER_CALLBACK_URL']);
        $url            = "https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id=".$client_id."&client_secret=".$client_secret."&redirect_uri=".$redirectURI."&code=".$code."&state=".$state;
        $is_post        = false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        if($status_code != 200) {
            alert(LOGIN_PROCESS_ERR, '/');
            exit;
        }

        $data = json_decode($response, true);

        $_SESSION['naver_access_token'] = $data['access_token'];

        /*
         $data Array
            (
                [access_token] => AAAAN4NCb6SYhrcv1JzOFd8Zx/Q7AVxn9ZuATDXvg4lYBxHtAwqty3RoO2oa2ZOenx9eHbdFxCJ6zhnRoMA/M1zI4+4=
                [refresh_token] => isG50WxEQMb0Ugq2awgX0zXMeVt7AY2ipdVm2r7CBgwYxTBOXefPJnkxIii7iplOjoQCIjmFj9jVh8Dl0QWGhWE8MHJ8g0kQFOo6ipOipuRDnii5UMie
                [token_type] => bearer
                [expires_in] => 3600
            )
        */
        /*토큰 정보 END*/


        /*회원 정보*/
        $headers    = array('Authorization: '.$data['token_type'].' '.$data['access_token']);
        $is_post    = false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, NAVER_GET_USERINFO_URL);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        if($status_code != 200) {
            alert(PROFILE_PROCESS_ERR,'/');
            exit;
        }

        $profile = json_decode($response, true);

        $initInput = array(
            'm_division'        => 2
        ,   'm_sns_site'        => 2
        ,   'm_loginid_prv_str' => 'nv'
        ,   'access_token'      => $data['access_token']
        ,   'join_path'         => $aInput['join_path']
        ,   'code'              => $code
        ,   'state'             => $state?$state:''
        );

        $this->arrayParams = array_merge($initInput,$profile['response']);
        /*회원 정보 END*/

        // 확장 데이터
        $profile_ext = array(
            'sns_id' => $profile['response']['id']
            , 'sns_site' => '2'
            , 'nickname' => $profile['response']['nickname']
            , 'profile_image' => $profile['response']['profile_image']
            , 'profile_image_thumb' => ''
            , 'email_needs_yn' => !empty($profile['response']['email']) ? 'N' : 'Y'
            , 'email' => $profile['response']['email']
            , 'email_valid_yn' => 'Y'
            , 'gender_needs_yn' => 'Y'
            , 'gender' => !empty($profile['response']['gender']) ? ($profile['response']['gender'] != 'U' ? $profile['response']['gender'] : '') : ''
            , 'birthyear_needs_yn' => 'Y'
            , 'birthyear' => ''
            , 'birthday_needs_yn' => 'Y'
            , 'birthday' => ''
            , 'age_range_needs_yn' => !empty($profile['response']['birthday']) ? 'N' : 'Y'
            , 'age_range' => !empty($profile['response']['birthday']) ? $profile['response']['age'] : ''
            , 'phone_number_country' => ''
            , 'phone_number' => ''
            , 'name' => !empty($profile['response']['name']) ? $profile['response']['name'] : ''
            , 'addr_type' => ''
            , 'addr_post' => ''
            , 'addr_post_new' => ''
            , 'addr1' => ''
            , 'addr2' => ''
            , 'receiver_name' => ''
            , 'receiver_phone_number1' => ''
            , 'receiver_phone_number2' => ''
            , 'login_path'            => 'web'
        );

        $this->arrayParams2 = $profile_ext;

        /*
         $profile Array
            (
                [resultcode] => 00
                [message] => success
                [response] => Array
                    (
                        [nickname] => 페이
                        [enc_id] => 9716e8227faabdb75fc7f3b411836e4ec7010fc0cd2c1be1de8dd5bfed6e417a
                        [profile_image] => https://ssl.pstatic.net/static/pwe/address/img_profile.png
                        [age] => 30-39
                        [gender] => M
                        [id] => 7792141
                        [name] => 황기석
                        [email] => zeus721@naver.com
                        [birthday] => 10-01
                    )

            )
         */


        $this->join_proc();

    }

    private function join_proc(){

        $aInput      = $this->aInput;
        $arrayParams = $this->arrayParams;

        //회원정보 확인
        $member_row = $this->member_model->get_member_row(array('m_sns_site' => $arrayParams['m_sns_site'], 'm_sns_id' => $arrayParams['id']));


        if( empty($member_row) ) { //없으면 => 가입

            $m_loginid = $arrayParams['nickname']?$arrayParams['nickname']:$arrayParams['m_loginid_prv_str'].$arrayParams['id'];

            /**
             * @date 180423
             * @modify 황기석
             * @desc 특수문자 변경 정규식변경
             */
            $tmp_nickname = preg_replace("/\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2}/", "", $m_loginid);
            $tmp_nickname = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $tmp_nickname);

            /**
             * @date 180214
             * @modify 황기석
             * @desc 닉네임이 없는 사람이 발생
             *       닉네임이 있지만 위 정규식에 의해 삭제가 되는 경우에는 빈값이 들어갈수 있음
             *       아래 최종 닉네임에 빈값인 경우 prv_str.id 로 처리
             */

            if($tmp_nickname == '' || $tmp_nickname == null ) $tmp_nickname = $arrayParams['m_loginid_prv_str'].$arrayParams['id']; // $tmp_nickname = random_string();

            $query_data = array(
                'm_key'                 => $_SESSION['my_session_id']
            ,   'm_division'            => $arrayParams['m_division']
            ,   'm_sns_site'            => $arrayParams['m_sns_site']
            ,   'm_sns_id'              => $arrayParams['id']
            ,   'm_nickname'            => str_replace(array("\"", "'"), array("˝", "´"), $tmp_nickname)
            ,   'm_sns_nickname'        => str_replace(array("\"", "'"), array("˝", "´"), $tmp_nickname)
            ,   'm_sns_profile_img'     => $arrayParams['profile_image']?$arrayParams['profile_image']:''
            ,   'm_email'               => $arrayParams['email']?$arrayParams['email']:'' //체크요소
            ,   'm_sns_token'           => $arrayParams['access_token']
            ,   'm_join_ip'             => $this->input->ip_address()
            ,   'm_join_path'           => $arrayParams['join_path']
            ,   'm_login_ip'            => $this->input->ip_address()
            ,   'm_state'               => '4'
            );

            $insert_result = $this->member_model->insert_member($query_data);
            //$insert_result = $this->member_model->insert_member2($query_data, $arrayParams2); //카카오 sync version

            if ( $insert_result['code'] == get_status_code('success') ) {
                $member_row = $insert_result['data'];

                $_SESSION['session_sns_site'] = $arrayParams['m_sns_site'];
                $_SESSION['session_sns_userid'] = $arrayParams['id'];

            }
            else {

                $insert_result['message'] = nl2br($insert_result['message']);
                $insert_result['message'] = str_replace('<br>','\n',$insert_result['message']);
                alert($insert_result['message'],'/');

            }

            if($aInput['rUrl_kakao'] != '(null)' && $aInput['rUrl_kakao'] != ''){
                $_SESSION['session_return_url'] = $aInput['rUrl_kakao'];
            }else if($aInput['rUrl']){
                $_SESSION['session_return_url'] = $aInput['rUrl'];
            }else{
                $_SESSION['session_return_url'] = "/";
            }


            redirect('/Auth/join_web');


        } else { //있으면 => 로그인

            if( $member_row['m_state'] == '2' ) {
                alert("정책위반으로 서비스 이용이 정지되었으며,\n제재기간 동안에는 서비스를 이용하실 수 없습니다.", "/");
            }else if( $member_row['m_state'] == '3' || $member_row['m_state'] == '4' ) {

                $_SESSION['session_sns_site'] = $arrayParams['m_sns_site'];
                $_SESSION['session_sns_userid'] = $arrayParams['id'];

                if($aInput['rUrl_kakao'] != '(null)' && $aInput['rUrl_kakao'] != ''){
                    $_SESSION['session_return_url'] = $aInput['rUrl_kakao'];
                }else if($aInput['rUrl']){
                    $_SESSION['session_return_url'] = $aInput['rUrl'];
                }else{
                    $_SESSION['session_return_url'] = "/";
                }

                redirect('/Auth/join_web/');

            }

            $query_data = array();
            if( $member_row['m_sns_profile_img'] != $arrayParams['profile_image'] ) {
                $query_data['m_sns_profile_img'] = $arrayParams['profile_image'];
                $query_data['m_sns_profile_img_thumb'] = "";
            }
            $query_data['m_login_ip'] = $this->input->ip_address();
            $query_data['m_logindatetime'] = current_datetime();
            if($arrayParams['access_token']){ $query_data['m_sns_token'] = $arrayParams['access_token']; }

            if( $this->member_model->update_member($member_row['m_num'], $query_data ) ) {
                set_login_session($member_row);
                //로그인 유지 쿠키
                $auto_login_enc =  $this->encryption->encrypt(time() . "|".$member_row['m_sns_site']."|" . $member_row['m_sns_id']);
                set_cookie('cookie_sal', $auto_login_enc, get_strtotime_diff("+1 years"));
            }

        }//end of if()


        if($this->input->is_ajax_request() == true){

            $go_url = '';
            if($aInput['rUrl_kakao'] != '(null)' && $aInput['rUrl_kakao'] != '') $go_url = $aInput['rUrl_kakao'];
            else if($aInput['rUrl']) $go_url = $aInput['rUrl'];

            result_echo_json(get_status_code('success'), "", true, "", "", "", $go_url);

        }else{

            if($aInput['rUrl_kakao'] != '(null)' && $aInput['rUrl_kakao'] != ''){
                redirect($aInput['rUrl_kakao']);
            }else if($aInput['rUrl']){
                redirect($aInput['rUrl']);
            }else{
                redirect('/');
            }
            //alert('로그인 되었습니다.', '/');
        }

    }

}//end of class Member
