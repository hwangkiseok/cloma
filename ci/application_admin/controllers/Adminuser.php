<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 관리자계정 관련 컨트롤러
 */
class Adminuser extends A_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model("adminuser_model");
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->adminuser_list();
    }//end of index()

    /**
     * 관리자계정 목록
     */
    public function adminuser_list() {
        //전체 권한이 없으면 자기 계정 수정으로 이동
        if( !is_adminuser_high_auth() ) {
            redirect($this->page_link->update);
        }

        //request
        $req['kfd']             = trim($this->input->post_get("kfd", TRUE));
        $req['kwd']             = trim($this->input->post_get("kwd", TRUE));
        $req['page']            = trim($this->input->post_get("page", TRUE));
        //$req['list_per_page']   = trim($this->input->post_get("list_per_page", TRUE));

        //페이지당 출력 갯수
        $list_per_page = 20;

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }

        $pgv_array = $req;
        unset($pgv_array['page']);

        $gv_array = $pgv_array;
        $gv_array['page'] = $req['page'];

        $PGV = http_build_query($pgv_array);
        $GV = http_build_query($gv_array);

        //쿼리 배열
        $query_array =  array();
        $query_array['where'] = $req;

        //검색갯수
        $list_count = $this->adminuser_model->get_adminuser_list($query_array, "", "", TRUE);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => $this->page_link->list . "/?" . $PGV,
            "per_page"      => $list_per_page,
            "page"          => $req['page']
        ));

        //목록
        $adminuser_list = $this->adminuser_model->get_adminuser_list($query_array, $page_result['start'], $page_result['limit']);

        //전체갯수
        $total_count = $this->db->count_all("adminuser_tb");

        $this->_header();

        $this->load->view("/adminuser/adminuser_list", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "total_count"       => $total_count,
            "list_count"        => $list_count,
            "list_per_page"     => $list_per_page,
            "page"              => $req['page'],
            "adminuser_list"    => $adminuser_list,
            "pagination"        => $page_result['pagination']
        ));

        $this->_footer();
    }//end of adminuser_list()

    /**
     * 관리자계정 추가
     */
    public function adminuser_insert() {
        $this->_header();

        $this->load->view("/adminuser/adminuser_insert", array(
            "list_url"  => $this->_get_list_url()
        ));

        $this->_footer();
    }//end of adminuser_insert()

    /**
     * 상품 추가 처리 (Ajax)
     */
    public function adminuser_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');


        //폼검증 룰 설정
        $set_rules_array = array(
            "au_level"              => array("field" => "au_level", "label" => "레벨", "rules" => "trim|required|in_list[".get_config_item_keys_string("adminuser_level")."]|xss_clean|prep_for_form|strip_tags"),
            "au_loginid"            => array("field" => "au_loginid", "label" => "아이디", "rules" => "trim|required|alpha_dash|min_length[4]|max_length[20]|is_unique[adminuser_tb.au_loginid]|xss_clean|prep_for_form|strip_tags"),
            "au_password"           => array("field" => "au_password", "label" => "비밀번호", "rules" => "trim|required|min_length[4]|max_length[20]|xss_clean|prep_for_form|strip_tags"),
            "au_password_confirm"   => array("field" => "au_password_confirm", "label" => "비밀번호 확인", "rules" => "trim|required|matches[au_password]|xss_clean|prep_for_form|strip_tags"),
            "au_name"               => array("field" => "au_name", "label" => "이름", "rules" => "trim|required|xss_clean|prep_for_form|strip_tags"),
            "au_email"              => array("field" => "au_email", "label" => "이메일", "rules" => "trim|valid_email|xss_clean|prep_for_form|strip_tags"),
            "au_mobile"             => array("field" => "au_mobile", "label" => "휴대폰", "rules" => "trim|valid_mobile|xss_clean|prep_for_form|strip_tags"),
            "au_usestate"           => array("field" => "au_usestate", "label" => "사용여부", "rules" => "trim|required|xss_clean|prep_for_form|strip_tags")
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $au_level       = $this->input->post('au_level', true);
            $au_loginid     = $this->input->post('au_loginid', true);
            $au_password    = $this->input->post('au_password', true);
            $au_name        = $this->input->post('au_name', true);
            $au_email       = $this->input->post('au_email', true);
            $au_mobile      = $this->input->post('au_mobile', true);
            $au_usestate    = $this->input->post('au_usestate', true);

            if( empty($form_error_array) ) {
                //등록
                $query_data = array();
                $query_data['au_level'] = $au_level;
                $query_data['au_loginid'] = $au_loginid;
                $query_data['au_password'] = $au_password;
                $query_data['au_name'] = $au_name;
                $query_data['au_email'] = $au_email;
                $query_data['au_mobile'] = $au_mobile;
                $query_data['au_usestate'] = $au_usestate;
                if( $this->adminuser_model->insert_adminuser($query_data) ) {
                    result_echo_json(get_status_code('success'), "등록 완료", true, "alert");
                } else {
                    result_echo_json(get_status_code('error'), "등록 실패!!", true, "alert");
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        foreach( array_keys($set_rules_array) as $item ) {
            if( form_error($item) ) {
                if( preg_match("/(\[|\])/", $item) ) {
                    $key_array = explode("[", $item);
                    $key = $key_array[0];
                }
                else {
                    $key = $item;
                }
                $form_error_array[$key] = strip_tags(form_error($item));
            }
        }//end of foreach()

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of adminuser_insert_proc()

    /**
     * 상품 수정
     * : 전체관리자가 아닐때는 자기 정보만 수정 가능
     */
    public function adminuser_update() {

        if( is_adminuser_high_auth() && $this->input->get_post('au_num', true) ) {
            $req['au_num'] = $this->input->get_post('au_num', true);
        }
        else {
            $req['au_num'] = $_SESSION['session_au_num'];
        }

        //관리자 계정 정보
        $adminuser_row = $this->adminuser_model->get_adminuser_row($req['au_num']);

        if( empty($adminuser_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->_header();

        $this->load->view('/adminuser/adminuser_update', array(
            'list_url'      => $this->_get_list_url(),
            'adminuser_row' => $adminuser_row
        ));

        $this->_footer();
    }//end of adminuser_update()

    /**
     * 관리자계정 수정 처리 (Ajax)
     */
    public function adminuser_update_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        $au_num_set_rules = "trim|xss_clean|is_natural|prep_for_form|strip_tags";
        $au_level_set_rules = "trim|in_list[".get_config_item_keys_string("adminuser_level")."]|xss_clean|prep_for_form|strip_tags";
        $au_usestate_set_rules = "trim|xss_clean|prep_for_form|strip_tags";
        $au_password_confirm_set_rules = "trim|matches[au_password]|xss_clean|prep_for_form|strip_tags";

        //전체관리자일때
        if( is_adminuser_high_auth() ) {
            $au_num_set_rules .= "|required";
            $au_level_set_rules .= "|required";
            $au_usestate_set_rules .= "|required";
        }
        //비밀번호가 입력되었을때 비밀번호 확인 필수 체크
        if( $this->input->post('au_password', TRUE) ) {
            $au_password_confirm_set_rules .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "au_num"                => array("field" => "au_num", "label" => "번호", "rules" => $au_num_set_rules),
            "au_level"              => array("field" => "au_level", "label" => "레벨", "rules" => $au_level_set_rules),
            "au_password"           => array("field" => "au_password", "label" => "비밀번호", "rules" => "trim|min_length[4]|max_length[20]|xss_clean|prep_for_form|strip_tags"),
            "au_password_confirm"   => array("field" => "au_password_confirm", "label" => "비밀번호 확인", "rules" =>$au_password_confirm_set_rules),
            "au_name"               => array("field" => "au_name", "label" => "이름", "rules" => "trim|required|xss_clean|prep_for_form|strip_tags"),
            "au_email"              => array("field" => "au_email", "label" => "이메일", "rules" => "trim|valid_email|xss_clean|prep_for_form|strip_tags"),
            "au_mobile"             => array("field" => "au_mobile", "label" => "휴대폰", "rules" => "trim|valid_mobile|xss_clean|prep_for_form|strip_tags"),
            "au_usestate"           => array("field" => "au_usestate", "label" => "사용여부", "rules" => $au_usestate_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            if( is_adminuser_high_auth() ) {
                $au_num = $this->input->post('au_num', true);
            }
            else {
                $au_num = $_SESSION['session_au_num'];
            }
            $au_level       = $this->input->post('au_level', true);
            $au_password    = $this->input->post('au_password', true);
            $au_name        = $this->input->post('au_name', true);
            $au_email       = $this->input->post('au_email', true);
            $au_mobile      = $this->input->post('au_mobile', true);
            $au_usestate    = $this->input->post('au_usestate', true);

            if( empty($form_error_array) ) {
                //수정
                $query_data = array();
                if( is_adminuser_high_auth() ) {
                    $query_data['au_level'] = $au_level;
                    $query_data['au_usestate'] = $au_usestate;
                }
                if( !empty($au_password) ) {
                    $query_data['au_password'] = $au_password;
                }
                $query_data['au_name'] = $au_name;
                $query_data['au_email'] = $au_email;
                $query_data['au_mobile'] = $au_mobile;
                if( $this->adminuser_model->update_adminuser($au_num, $query_data) ) {
                    result_echo_json(get_status_code('success'), "수정 완료", true, "alert");
                }
                else {
                    result_echo_json(get_status_code('error'), "수정 실패!!", true, "alert");
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        foreach( array_keys($set_rules_array) as $item ) {
            if( form_error($item) ) {
                if( preg_match("/(\[|\])/", $item) ) {
                    $key_array = explode("[", $item);
                    $key = $key_array[0];
                }
                else {
                    $key = $item;
                }
                $form_error_array[$key] = strip_tags(form_error($item));
            }
        }//end of foreach()

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of adminuser_update_proc()

    /**
     * 관리자계정 삭제 처리
     */
    public function adminuser_delete_proc() {
        if( !is_adminuser_high_auth() ) {
            alert(lang('site_unauthorized'));
        }

        $au_num = $this->input->get_post('au_num', true);

        //관리자 계정 정보
        $adminuser_row = $this->adminuser_model->get_adminuser_row($au_num);

        if( empty($adminuser_row) ) {
            alert(lang('site_error_empty_data'));
        }

        //삭제
        if ( $this->adminuser_model->delete_adminuser($au_num) ) {
            alert(lang('site_delete_success'), $this->_get_list_url());
            }
        else {
            alert(lang('site_delete_fail'));
        }
    }//end of adminuser_delete_proc()

}//end of class Adminuser