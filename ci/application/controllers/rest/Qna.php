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
class Qna extends REST_Controller
{

    public $core;

    public function __construct()
    {
        parent::__construct();

        //model
        $this->load->model('product_model');
        $this->load->model('board_qna_model');
        $this->load->helper('rest');

        $this->core = new Rest_Core(); // Core Class (MyController 코어클래스와 같은역할)

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
            $query_data['where']['m_num'] = $_SESSION['session_m_num'];

            $qna_list = $this->board_qna_model->get_board_qna_list($query_data);


            foreach( $qna_list as $key => $row ) {
                $row['bq_file_arr'] = json_decode($row['bq_file'], true);
            }//end of foreach()


            if(empty($qna_list) == false){

                $this->set_response(

                    result_echo_rest_json(get_status_code("success"), "", true, "", "",
                        array(
                            "aQnaList"  => $qna_list
                        )
                    ), REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;

            }else{

                $this->set_response(

                    result_echo_rest_json(get_status_code("error"), lang('site_error_empty_data'), true, "", "", "" ), REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;

            }

        }

    }

    /**
     * 1:1문의 삭제
     */
    public function delete_delete()
    {

        //request
        $req['bq_num'] = $this->delete('bq_num', true);

        //문의 조회
        $qna_row = $this->board_qna_model->get_board_qna_row($req['bq_num'], $_SESSION['session_m_num']);

        if( empty($qna_row) ) {

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang('site_error_empty_data'), true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code

        }else{

            $member_row = $this->core->_get_member_info();

            //댓글 삭제
            if( $this->board_qna_model->delete_board_qna($req['bq_num']) ) {

                $this->set_response(
                    result_echo_rest_json(get_status_code("success"), lang("site_delete_success"), true, "", "", ""
                    ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code

            } else {
                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), lang("site_delete_fail"), true, "", "", ""
                    ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code

            }

        }

    }
    /**
     * 1:1문의 등록/수정
     */
    public function upsert_put()
    {

        if(member_login_status() == false){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang("site_error_default"), "", true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code

        }else{

            $bq_refund_info = implode(" / ", array_filter(array($this->put("refund_info_bank")
            , $this->put("refund_info_account")
            , $this->put("refund_info_owner"))));

            $req = array(
                    'bq_num'          => $this->put("bq_num")
                ,   'bq_content'      => $this->put("bq_content")
                ,   'bq_contact'      => $this->put("bq_contact")
                ,   'bq_product_num'  => $this->put("bq_product_num")
                ,   'bq_product_name' => $this->put("bq_product_name")
                ,   'bq_name'         => $this->put("bq_name")
                ,   'bq_category'     => $this->put("bq_category")
                ,   'board_qna_file'  => $this->put("board_qna_file")
                ,   'bq_refund_info'  => $bq_refund_info
                ,   'bq_member_num'   => $_SESSION['session_m_num']
            );

            $bUpdate = $empty_row = false;

            if(empty($req['bq_num']) == false){
                $qna_row = $this->board_qna_model->get_board_qna_row($req['bq_num'],$req['bq_member_num']);
                if(empty($qna_row) == true){
                    $empty_row = true;
                }
                $bUpdate = true;
            }

            if ( empty($empty_row) == false ) {

                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), lang('site_error_empty_data'), true, "", "", ""
                    ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code

            }else{
                //등록
                $query_data                     = array();
                $query_data['bq_member_num']    = $req['bq_member_num'];
                $query_data['bq_category']      = $req['bq_category'];
                $query_data['bq_product_name']  = $req['bq_product_name'];
                $query_data['bq_name']          = $req['bq_name'];
                $query_data['bq_contact']       = $req['bq_contact'];
                $query_data['bq_content']       = $req['bq_content'];
                $query_data['bq_refund_info']   = $req['bq_refund_info'];
                $query_data['bq_file']          = '';
                if( !empty($req['bq_product_num']) ) $query_data['bq_product_num'] = $req['bq_product_num'];
                //$query_data['bq_file'] = count($bg_img_arr) > 0 ? json_encode_no_slashes($bg_img_arr) : '';

                if($bUpdate == false){ //insert

                    if( $this->board_qna_model->insert_board_qna($query_data) ) { //성공

                        $this->set_response(
                            result_echo_rest_json(get_status_code("success"), lang('site_insert_success'), true, "", "", ""
                            ), REST_Controller::HTTP_OK
                        ); // NOT_FOUND (404) being the HTTP response code

                    }else{ //실패

                        $this->set_response(
                            result_echo_rest_json(get_status_code("error"), lang('site_insert_fail'), true, "", "", ""
                            ), REST_Controller::HTTP_OK
                        ); // NOT_FOUND (404) being the HTTP response code

                    }

                }else{ //update

                    if( $this->board_qna_model->update_board_qna($req['bq_num'],$query_data) ) { //성공

                        $this->set_response(
                            result_echo_rest_json(get_status_code("success"), lang('site_update_success'), true, "", "", ""
                            ), REST_Controller::HTTP_OK
                        ); // NOT_FOUND (404) being the HTTP response code

                    }else{//실패

                        $this->set_response(
                            result_echo_rest_json(get_status_code("error"), lang('site_update_fail'), true, "", "", ""
                            ), REST_Controller::HTTP_OK
                        ); // NOT_FOUND (404) being the HTTP response code

                    }

                }

            }

        }

    }

}
