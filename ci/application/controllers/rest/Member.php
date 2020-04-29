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
    * 삼품상세 정보
    * 앱실행 후 1회 실행
    */
    public function info_get()
    {

        $this->load->model('member_model');

        if(empty($_SESSION) == false){

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

                $query_data = array();
                if(empty($this->get('app_version')) == false ) $query_data['m_app_version']             = $this->get('app_version');
                if(empty($this->get('app_version_code')) == false ) $query_data['m_app_version_code']   = $this->get('app_version_code');
                if(empty($this->get('device_model')) == false ) $query_data['m_device_model']           = $this->get('device_model');
                if(empty($this->get('os_version')) == false ) $query_data['m_os_version']               = $this->get('os_version');
                $query_data['m_login_ip']       = $this->input->ip_address();
                $query_data['m_logindatetime']  = current_datetime();

                $this->member_model->publicUpdate('member_tb' , $query_data , array('m_num' , $aInput['m_num']));

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

}

