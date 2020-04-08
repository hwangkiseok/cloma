<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 대량구매 문의
 */
class Offer extends M_Controller
{

    public function __construct()
    {
        parent::__construct();

    }//end of __construct()

    public function index()
    {


        $this->load->view('/offer/index', array( ) );


    }//end of index()


    public function offer_insert_proc(){

        ajax_request_check();

        $this->load->library('form_validation');

        $set_rules_array = array(
                "user_name"     => array("field" => "user_name", "label" => "이름", "rules" => "required|".$this->default_set_rules)
            ,   "user_hp"       => array("field" => "user_hp", "label" => "연락처", "rules" => "required|numeric|".$this->default_set_rules)
            ,   "user_email"    => array("field" => "user_email", "label" => "이메일", "rules" => "required|".$this->default_set_rules)
            ,   "content"       => array("field" => "content", "label" => "내용", "rules" => $this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        //폼 검증 성공시
        if( $this->form_validation->run() === TRUE ) {

            $this->load->model('common_model');

            $aInput = array(
                    'user_name'     => $this->input->post('user_name')
                ,   'user_hp'       => $this->input->post('user_hp')
                ,   'user_email'    => $this->input->post('user_email')
                ,   'content'       => $this->input->post('content')
                ,   'reg_date'      => current_datetime()
                ,   'm_num'         => $_SESSION['session_m_num']?$_SESSION['session_m_num']:0
            );

            if( $this->common_model->publicInsert('offer_tb' , $aInput) == true ) {
                echo json_encode_no_slashes(array('msg' => '문의가 등록되었습니다.' , 'success' => true , 'data' => array() ));
                exit;
            }else{
                echo json_encode_no_slashes(array('msg' => '문의 실패[DB]' , 'success' => false , 'data' => array() ));
                exit;
            }

        }

        echo json_encode_no_slashes(array('msg' => '필수 입력값을 정확하게 입력해주세요 !' , 'success' => false , 'data' => array() ));

    }

}//end of class Offer