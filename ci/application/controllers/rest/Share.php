<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\Rest_Core;

defined('BASEPATH') OR exit('No direct script access allowed');

//To Solve File REST_Controller not found
require APPPATH . 'libraries/RestServer/REST_Controller.php';
require APPPATH . 'libraries/RestServer/Format.php';
require APPPATH . 'libraries/RestServer/Rest_Core.php'; // W_Controller 클래스에서 사용된 메소드이관

/**
 * 공유하기 관련 컨트롤러
 */
class Share extends REST_Controller
{

    public $core;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('rest');

        $this->core = new Rest_Core(); // Core Class (MyController 코어클래스와 같은역할)

        //model
        $this->load->model('product_model');
        $this->load->model('share_model');

    }

    /**
     * 찜하기 리스트
     */
    public function list_get()
    {

        if(member_login_status() == false){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang("site_error_default"), "", true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code

        }else{

            $query_data     = array();
            $share_prod_list = $this->share_model->get_share_list($query_data);

            foreach( $share_prod_list as $key => $row ) {
                $row['p_rep_image_array'] = json_decode($row['p_rep_image'], true);
                $row['p_display_info_array'] = json_decode($row['p_display_info'], true);
            }//end of foreach()


            if(empty($share_prod_list) == false){

                $this->set_response(

                    result_echo_rest_json(get_status_code("success"), "", true, "", "",
                        array(
                            "aShareList"  => $share_prod_list
                        )
                    ), REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;

            }else{

                $this->set_response(

                    result_echo_rest_json(get_status_code("success"), "", true, "", "",
                        array(
                            "aShareList"  => array()
                        )
                    ), REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;

            }

        }

    }
    /**
     * 공유하기 toggle
     */
    public function upsert_put()
    {

        if(member_login_status() == false){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang("site_error_default"), "", true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code

        }else{

            $req['p_num'] = $this->put('p_num');

            //상품 정보
            $product_row = $this->product_model->get_product_row(array('p_num' => $req['p_num']));

            if ( empty($product_row) ) {

                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), lang('site_error_empty_data'), true, "", "", ""
                    ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code

            }else{

                $query_data                 = array();
                $query_data['s_member_num'] = $_SESSION['session_m_num'];
                $query_data['s_product_num'] = $product_row['p_num'];

                // 찜하기 체크
                $share_row = $this->share_model->get_share_row($query_data['s_member_num'], $query_data['s_product_num']);
                $result = array('code' => get_status_code('success'));

                if(empty($share_row) == true) {
                    $result = $this->share_model->insert_share($query_data);
                }

                $query_data                 = array();
                $query_data['p_share_count'] = (int)($product_row['p_share_count']) + 1;

                $this->product_model->update_product($product_row['p_num'], $query_data);

                total_stat("product_share");

                $isShare = true;

                if ($result['code'] == get_status_code('success')) {

                    $this->set_response(
                        result_echo_rest_json($result['code'], "", true , '' , '' , array(
                                'isShare'        => $isShare
                            ,   'tot_share_cnt'  => $query_data['p_share_count']
                        )), REST_Controller::HTTP_OK
                    );

                } else {

                    $this->set_response(
                        result_echo_rest_json($result['code'], "이미 공유한 상품입니다.", true, ""), REST_Controller::HTTP_OK
                    );

                }

            }

        }

    }

}

