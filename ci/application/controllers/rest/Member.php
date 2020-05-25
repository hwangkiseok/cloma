<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\Rest_Core;

defined('BASEPATH') OR exit('No direct script access allowed');

//To Solve File REST_Controller not found
require APPPATH . 'libraries/RestServer/REST_Controller.php';
require APPPATH . 'libraries/RestServer/Format.php';
require APPPATH . 'libraries/RestServer/Rest_Core.php'; // W_Controller 클래스에서 사용된 메소드이관

/**
 * 회원 관련 컨트롤러
 */
class Member extends REST_Controller
{

    public $core;

    public function __construct()
    {
        parent::__construct();

        //model
        $this->load->helper('rest');
        $this->core = new Rest_Core(); // Core Class (MyController 코어클래스와 같은역할)

    }

    /**
     * @date 200508
     * @modify 황기석
     * @desc 회원가입후 대기->정상 처리
     */
    public function accept_proc_get() {

        $m_num = $_SESSION['session_m_num'];
        $m_key = $_SESSION['session_m_key'];

        //SNS userid 세션이 없을때
        if( empty($m_num) || empty($m_key) ) {
            $this->set_response(
                result_echo_rest_json(get_status_code("error"), '필수입력정보 누락[m_num|m_key]', true, "", "", "" )
                , REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code;
        }else{

            $where_data = array();
            $where_data['m_num'] = $m_num;
            $where_data['m_key'] = $m_key;
            $member_row = $this->member_model->get_member_row($where_data);

            if( empty($member_row) ) {

                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), '회원정보 없음[member_row]', true, "", "", "" )
                    , REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;

            }else{

                /* m_state 변경 회원대기(4) -> 정상(1) */
                $bRet = $this->member_model->set_user_activate($member_row['m_num']);

                if($bRet == true){

                    if( self::_login_proc(array(),$member_row) == true ) {

                        $this->set_response(
                            result_echo_rest_json(get_status_code("success"), "" , true, "", "", "" )
                            , REST_Controller::HTTP_OK
                        ); // OK (200) being the HTTP response code;

                    }
                    else {

                        $this->set_response(
                            result_echo_rest_json(get_status_code("error"), "로그인실패[DB]" , true, "", "", "" )
                            , REST_Controller::HTTP_OK
                        ); // OK (200) being the HTTP response code;

                    }

                }else {

                    $this->set_response(
                        result_echo_rest_json(get_status_code("error"), "승인실패[DB]" , true, "", "", "" )
                        , REST_Controller::HTTP_OK
                    ); // OK (200) being the HTTP response code;

                }

            }

        }

    }

    /**
     * @date 200508
     * @modify 황기석
     * @desc 회원가입/로그인 start
     */
    public function login_post(){

        //request
        $req = array(
             'id'			    => $this->post('id')                  //필수
            ,'nickname'		    => $this->post('nickname')            //필수
            ,'email'			=> $this->post('email')               //필수 x
            ,'profile_image'	=> $this->post('profile_image')       //필수 x
            ,'access_token'		=> $this->post('sns_site') == "4" ? "" : $this->get('access_token')        //필수 x
            ,'fcm_id'		    => $this->post('fcm_id')              //필수
            ,'device_info'		=> $this->post('device_info')         //필수
            ,'os_version'		=> $this->post('os_version')          //필수
            ,'app_version'		=> $this->post('app_version')         //필수
            ,'app_version_code'	=> $this->post('app_version_code')    //필수
            ,'sns_site'		    => $this->post('sns_site')            //필수
            ,'adid'             => $this->post('adid')                //필수 x
            ,'reg_id'           => $this->post('fcm_id')              //필수 x
            ,'age_range'        => $this->post('age_range')           //필수 x
            ,'birthyear'        => $this->post('birthyear')           //필수 x
            ,'birthday'         => $this->post('birthday')            //필수 x
            ,'gender'           => $this->post('gender')              //필수 x
            ,'phone_number'     => $this->post('phone_number')        //필수 x
            ,'address'          => $this->post('address')             //필수 x
            ,'ci'               => $this->post('ci')                  //필수 x

        );

        if(empty($req['id']) == true){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), 'SNS_ID 누락', true, "", "", "" )
                , REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code;

        }else{

            foreach ($req as $k => $v) $req[$k] = AES_Decode($v);

            if($req['device_info'] == "iPhone") $req['join_path'] = "4";
            else $req['join_path'] = "1";

            $query_data = array();
            $query_data['m_sns_site']   = $req['sns_site'];
            $query_data['m_sns_id']     = $req['id'];

            $member_row = $this->member_model->get_member_row($query_data);

            $_proc_code = '000';

            if( empty($member_row) == false ) {

                $ret = self::_login_proc($req, $member_row);

                if( $ret == false ) $_proc_code = '401';
                else $member_row['auth_type'] = 'login';

            } else {

                $member_row = self::_insert_member($req);

                if( empty($member_row) == true )  $_proc_code = '402';
                else $member_row['auth_type'] = 'join';

            }

            if($_proc_code == '000'){

                $this->set_response(
                    result_echo_rest_json(get_status_code("success"), '', true, "", "", $member_row )
                    , REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;

            }else{

                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), '로그인 실패['.$_proc_code.']', true, "", "", "" )
                    , REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;

            }

        }

    }

    /**
     * @date 200508
     * @modify 황기석
     * @desc 로그인처리
     * @return boolean
     */
    private function _login_proc($req, $member_row){

        //회원 로그인 세션 생성
        set_login_session($member_row);

        $query_data = array();
        $query_data['m_login_ip']       = $this->input->ip_address();
        $query_data['m_logindatetime']  = current_datetime();

        if(empty($req['app_version']) == false) $query_data['m_app_version'] = $req['app_version'];
        if(empty($req['app_version_code']) == false) $query_data['m_app_version_code'] = $req['app_version_code'];
        if(empty($req['reg_id']) == false) $query_data['m_regid'] = $req['reg_id'];
        if(empty($req['adid']) == false) $query_data['m_adid'] = $req['adid'];

        $ret = $this->member_model->publicUpdate('member_tb' , $query_data , array('m_num',$member_row['m_num']));

        return $ret;

    }

    /**
     * @date 200508
     * @modify 황기석
     * @desc 회원가입처리
     * @return (array) member_info
     */
    private function _insert_member($req){

        $tmp_nickname = $req['nickname'];

        { //특수문자 제거
            $tmp_nickname = preg_replace("/\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2}/", "", $tmp_nickname);
            $tmp_nickname = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $tmp_nickname);
            $tmp_nickname = str_replace(array("\"", "'"), array("˝", "´"), $tmp_nickname);
        }

        if( empty($tmp_nickname) ) $tmp_nickname = $req['id'];

        //회원 등록
        $query_data = array();
        $query_data['m_key']                = $_SESSION['my_session_id'];
        $query_data['m_division']           = "2";
        $query_data['m_nickname']           = $tmp_nickname;
        $query_data['m_sns_site']           = $req['sns_site'];
        $query_data['m_sns_id']             = $req['id'];
        $query_data['m_sns_token']          = $req['access_token'];
        $query_data['m_sns_profile_img']    = $req['profile_image'];
        $query_data['m_sns_nickname']       = $tmp_nickname;
        $query_data['m_email']              = $req['email'];
        $query_data['m_adid']               = $req['adid']?$req['adid']:"";
        $query_data['m_join_ip']            = $this->input->ip_address();
        $query_data['m_join_path']          = $req['join_path'];
        $query_data['m_state']              = 4; //대기
        $query_data['m_login_ip']           = $this->input->ip_address();
        $query_data['m_app_version']        = $req['app_version'];
        $query_data['m_app_version_code']   = $req['app_version_code']?$req['app_version_code']:"0";
        $query_data['m_device_model']       = $req['device_info'];
        $query_data['m_os_version']         = $req['os_version'];
        $query_data['m_push_yn']            = 'Y';//$req['push'];

        if( $req['sns_site'] == '1' ){ // "카카오"인 경우 추가정보 가공

            //연락처
            $phone_arr = explode(' ', $req['phone_number']);
            $phone_number_country = $phone_arr[0];
            if ($phone_arr[0] == '+82') {
                $phone_number = '0' . $phone_arr[1];
                $phone_num_arr = explode('-', $phone_number);
                $phone_number = $phone_num_arr[0] . $phone_num_arr[1] . $phone_num_arr[2];
            } else {
                $phone_number = $phone_arr[1];
            }

            //성별
            $gender = '';
            if($req['gender'] == 'male') $gender = 'M';
            else if($req['gender'] == 'female') $gender = 'F';

            $query_data['m_age_range'] = empty($req['age_range']) == false ? substr($req['age_range'],0,1).'0' : '';
            $query_data['m_gender']    = $gender;
            $query_data['m_authno']    = $phone_number;

            if(empty($req['address']) == false){

                $delivery_addr_1 = json_decode($req['address'],true);

                foreach ($delivery_addr_1 as $key => $val) {

                    if ($val['isDefault'] == true) {

                        $phone_arr1 = '';
                        $phone_arr2 = '';
                        $receiver_phone_number1 = '';
                        $receiver_phone_number2 = '';

                        if (isset($val['receiverPhoneNumber1'])) {
                            $phone_arr1 = explode('-', $val['receiverPhoneNumber1']);
                            $receiver_phone_number1 = $phone_arr1[0] . $phone_arr1[1] . $phone_arr1[2];
                        }

                        if (isset($val['receiverPhoneNumber2'])) {
                            $phone_arr2 = explode('-', $val['receiverPhoneNumber2']);
                            $receiver_phone_number2 = $phone_arr2[0] . $phone_arr2[1] . $phone_arr2[2];
                        }

                        $tmpProfile['name'] = $val['name'];
                        $tmpProfile['addr_type'] = $val['type'];
                        $tmpProfile['addr_post'] = $val['zipCode'];
                        $tmpProfile['addr_post_new'] = $val['zoneNumber'];
                        $tmpProfile['addr1'] = $val['baseAddress'];
                        $tmpProfile['addr2'] = $val['detailAddress'];
                        $tmpProfile['receiver_name'] = $val['receiverName'];
                        $tmpProfile['receiver_phone_number1'] = $receiver_phone_number1;
                        $tmpProfile['receiver_phone_number2'] = $receiver_phone_number2;
                    }

                }

            }

            // 확장 데이터
            $profile_ext = array(
                'sns_id'                => $req['id']
            ,   'sns_site'              => $req['sns_site']
            ,   'nickname'              => str_replace(array("\"", "'"), array("˝", "´"), $tmp_nickname)
            ,   'profile_image'         => $req['profile_image']
            ,   'profile_image_thumb'   => $req['profile_image']
            ,   'email'                 => $req['email']
            ,   'gender'                => $req['gender']
            ,   'birthyear'             => $req['birthyear']
            ,   'birthday'              => $req['birthday']
            ,   'age_range'             => $req['age_range']
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
            ,   'login_path'            => 'app'
            );

        }

        if( !empty($req['reg_id']) )  $query_data['m_regid'] = $req['reg_id'];

        log_message('M', '- APP Member Insert Params --------------------------------------------------------------------------------------');
        foreach ($query_data as $k => $v) {
            log_message('M',$k . ' ==> ' . $v);
        }

        //회원가입
        $ret            = $this->member_model->insert_member($query_data,$profile_ext);
        $member_row     = $ret['data'];

        ///fcmid 처리
        if( !empty($req['reg_id']) ) {

            $this->load->model('app_device_model');

            if( $ret['code'] == get_status_code('success') ) {

                $device_row = $this->app_device_model->get_app_device_row(array('dv_regid' => $req['reg_id']));
                //있으면 수정
                if (!empty($device_row)) {
                    $query_data = array();
                    $query_data['dv_member_num']    = $member_row['m_num'];
                    $query_data['dv_push_yn']       = $member_row['m_push_yn'];
                    $query_data['dv_app_version']   = $req['app_version'];
                    $query_data['dv_regid']         = $req['reg_id'];
                    $query_data['dv_app_version_code'] = $req['app_version_code'];

                    $this->app_device_model->update_app_device($device_row['dv_num'], $query_data);
                } //없으면 등록
                else {
                    $query_data = array();
                    $query_data['dv_regid']         = $req['reg_id'];
                    $query_data['dv_member_num']    = $member_row['m_num'];
                    $query_data['dv_push_yn']       = $member_row['m_push_yn'];
                    $query_data['dv_deviceinfo']    = $req['device_info'] . "|" . $req['os_version'];
                    $query_data['dv_useragent']     = $this->agent->agent_string()?$this->agent->agent_string():"";
                    $query_data['dv_app_version']   = $req['app_version'];
                    $query_data['dv_app_version_code'] = $req['app_version_code'];

                    $this->app_device_model->insert_app_device($query_data);
                }

            }

        }//end of if()

        //회원 로그인 세션 생성
        set_login_session($member_row);

        return $member_row;

    }

    /**
    * 삼품상세 정보
    * 앱실행 후 1회 실행
    */
    public function info_get()
    {
        $this->load->model('member_model');

        if(member_login_status() == true){

            $aInput = array(
                    'm_num' => $_SESSION['session_m_num']
                ,   'm_key' => $_SESSION['session_m_key']
                ,   'm_state'   => 1
            );

            $aMemberInfo = $this->member_model->get_member_row($aInput);

            if(empty($aMemberInfo) == true){
                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), lang('site_error_empty_data'), true, "", "", "" )
                    , REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;
            }else{
                $this->set_response(
                    result_echo_rest_json(get_status_code("success"), "", true, "", "",
                        array(
                                "aMemberInfo"  => $aMemberInfo
                        )
                    ), REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;
            }

        }else{

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang('site_error_empty_data'), true, "", "", "" )
                , REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code;

        }

    }

    /**
     * @date 200520
     * @modify 황기석
     * @desc 각종 회원정보 저장
     */
    public function save_info_put(){

        if(member_login_status() == true){

            $query_data = array();
            if(empty($this->put('app_version')) == false ) $query_data['m_app_version']             = $this->put('app_version');
            if(empty($this->put('app_version_code')) == false ) $query_data['m_app_version_code']   = $this->put('app_version_code');
            if(empty($this->put('device_model')) == false ) $query_data['m_device_model']           = $this->put('device_model');
            if(empty($this->put('os_version')) == false ) $query_data['m_os_version']               = $this->put('os_version');
            if(empty($this->put('adid')) == false ) $query_data['m_adid']                           = $this->put('adid');
            if(empty($this->put('fcm_id')) == false ) $query_data['m_regid']                        = $this->put('fcm_id');

            $query_data['m_login_ip']       = $this->input->ip_address();
            $query_data['m_logindatetime']  = current_datetime();

            $this->member_model->publicUpdate('member_tb' , $query_data , array('m_num' , $_SESSION['session_m_num']));

        }

    }

}

