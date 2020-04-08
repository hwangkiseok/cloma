<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 로그인/로그아웃 관련 컨트롤러
 */
/**
 * @date 170804
 * @writer 황기석
 * @desc 자동로그인되게 최소한의 인증을 하는 리퍼러 주소 상수
 */
CONST REFERER_DOMAIN = '09sns.co.kr/totalAdmin';

class Auth extends A_Controller {

    public function __construct() {
        parent::__construct();
    }//end of __construct()

    public function index() {
       $this->login();
    }//end of index()

    /**
     * 로그인 페이지
     */
    public function login() {

        if( adminuser_login_status() ) {
            redirect("/");
            exit;
        }

        $this->_header(TRUE);

        $this->load->view('auth/login', array());

        $this->_footer(TRUE);
    }//end of login()

    /**
     * 로그인 처리 (Ajax)
     */
    public function login_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "user_id"   => array("field" => "user_id", "label" => "아이디", "rules" => "trim|required|max_length[30]|xss_clean|prep_for_form|strip_tags"),
            "user_pw"   => array("field" => "user_pw", "label" => "비밀번호", "rules" => "trim|required|max_length[30]|xss_clean|prep_for_form|strip_tags"),
        );

        $this->form_validation->set_rules($set_rules_array);

        //$form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === TRUE ) {
            $id = $this->input->post("user_id", TRUE);
            $pw = $this->input->post("user_pw", TRUE);

            //model
            //$this->load->model('auth_model');
            $this->load->model('adminuser_model');
            $adminuser_row = $this->adminuser_model->get_adminuser_login($id, $pw);

            /* 그룹웨어 계정 연동 조회 */


            if ( empty($adminuser_row) ) {
                result_echo_json(get_status_code('fail'), "계정정보가 없습니다.3", TRUE, "alert");
            }
            if( $adminuser_row->au_usestate == 'N' ) {
                result_echo_json(get_status_code('fail'), "사용중지된 계정입니다.", TRUE, "alert");
            }

            //세션 생성
            //$this->session->set_userdata('session_au_num', $adminuser_row->au_num);
            //$this->session->set_userdata('session_au_level', $adminuser_row->au_level);
            //$this->session->set_userdata('session_au_name', $adminuser_row->au_name);
            //$this->session->set_userdata('session_au_loginid', $adminuser_row->au_loginid);
            $_SESSION['session_au_num'] = $adminuser_row->au_num;
            $_SESSION['session_au_level'] = $adminuser_row->au_level;
            $_SESSION['session_au_name'] = $adminuser_row->au_name;
            $_SESSION['session_au_loginid'] = $adminuser_row->au_loginid;
            //session_write_close();

            //로그인일시 업데이트
            $this->adminuser_model->update_adminuser($adminuser_row->au_num, array("au_logindatetime"=>current_datetime()));

            result_echo_json(get_status_code('success'), "", TRUE);
        }

        result_echo_json(get_status_code('fail'), "", TRUE);
    }//end of login_proc()

    /**
     * 로그아웃 처리
     */
    public function logout() {
        //$this->session->sess_destroy();
        session_destroy();

        redirect("/auth/login");
    }//end of logout()

}//end of class Auth