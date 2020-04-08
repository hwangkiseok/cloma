<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\Rest_Core;

defined('BASEPATH') OR exit('No direct script access allowed');

//To Solve File REST_Controller not found
require APPPATH . 'libraries/RestServer/REST_Controller.php';
require APPPATH . 'libraries/RestServer/Format.php';
require APPPATH . 'libraries/RestServer/Rest_Core.php'; // W_Controller 클래스에서 사용된 메소드이관

/**
 * 상품 관련 컨트롤러
 */
class Offer extends REST_Controller
{

    public $core;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('rest');

        $this->core = new Rest_Core(); // Core Class (MyController 코어클래스와 같은역할)

    }

    /**
     * 대량등록구매 insert
     */
    public function insert_put()
    {

        if(member_login_status() == false){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang("site_error_default"), "", true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code

        }else{

            $req['m_num']       = $_SESSION['session_m_num'];
            $req['user_name']   = $this->put('user_name');
            $req['user_hp']     = $this->put('user_hp');
            $req['user_email']  = $this->put('user_email');
            $req['content']     = $this->put('content');
            $req['reg_date']    = current_datetime();


            if( empty($req['user_name']) == true
            ||  empty($req['user_hp']) == true
            ||  empty($req['user_email']) == true
            ){

                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), '필수 입력 정보 누락', "", true, "", "", ""
                    ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code

            }else{

                $this->load->model('offer_model');
                $bRet = $this->offer_model->publicInsert('offer_tb',$req);

                if ($bRet == true) {

                    $this->set_response(
                        result_echo_rest_json(get_status_code("success"), "", true , '' , '' , '')
                        , REST_Controller::HTTP_OK
                    );

                } else {

                    $this->set_response(
                        result_echo_rest_json(get_status_code("error"), "ERR DB", true, "","","")
                        , REST_Controller::HTTP_OK
                    );

                }

            }

        }

    }

}
