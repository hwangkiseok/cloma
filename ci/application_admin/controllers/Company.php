<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 외부광고 관련 컨트롤러
 */
class Company extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('company_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->company_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        return $req;
    }//end of _list_req()

    /**
     * 외부광고 목록
     */
    public function company_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/company/company_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of company_list()

    /**
     * 외부광고 목록 (Ajax)
     */
    public function company_list_ajax() {
        ajax_request_check(true);

        //request
        $req = $this->_list_req();

        $pgv_array = $req;
        unset($pgv_array['page']);

        $gv_array = $pgv_array;
        $gv_array['page'] = $req['page'];

        $PGV = http_build_query($pgv_array);
        $GV = http_build_query($gv_array);

        //쿼리 배열
        $query_array =  array();
        $query_array['where'] = $req;
        if( !empty($req['sort_field']) && !empty($req['sort_type']) ) {
            $query_array['orderby'] = $req['sort_field'] . " " . $req['sort_type'];
        }

        //전체갯수
        $list_count = $this->company_model->get_company_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/company/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $company_list = $this->company_model->get_company_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['co_name'] = array("asc", "sorting");
        $sort_array['co_loginid'] = array("asc", "sorting");
        $sort_array['co_passwd'] = array("asc", "sorting");
        $sort_array['co_url'] = array("asc", "sorting");
        $sort_array['co_regdatetime'] = array("asc", "sorting");
        $sort_array['au_name'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/company/company_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "company_list"       => $company_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of company_list_ajax()

    /**
     * 외부광고 추가 (팝업)
     */
    public function company_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/company/company_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url()
        ));
    }//end of company_insert_pop()

    /**
     * 외부광고 추가 처리 (Ajax)
     */
    public function company_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "co_name" => array("field" => "co_name", "label" => "제휴사명", "rules" => "required|max_length[50]|".$this->default_set_rules),
            "co_loginid" => array("field" => "co_loginid", "label" => "제휴사아이디", "rules" => "required|max_length[50]|".$this->default_set_rules),
            "co_passwd" => array("field" => "co_passwd", "label" => "제휴사비밀번호", "rules" => "max_length[50]|".$this->default_set_rules),
            "co_url" => array("field" => "co_url", "label" => "제휴사URL", "rules" => "max_length[200]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $co_name = $this->input->post('co_name', true);
            $co_loginid = $this->input->post('co_loginid', true);
            $co_passwd = $this->input->post('co_passwd', true);
            $co_url = str_replace("http://", "", $this->input->post('co_url', true));
            if( !empty($co_url) ) {
                $co_url = "http://" . $co_url;
            }

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['co_name'] = $co_name;
                $query_data['co_loginid'] = $co_loginid;
                $query_data['co_passwd'] = $co_passwd;
                $query_data['co_url'] = $co_url;

                if( $this->company_model->insert_company($query_data) ) {
                    result_echo_json(get_status_code('success'), lang('site_insert_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_insert_fail'), true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of company_insert_proc()

    /**
     * 외부광고 수정
     */
    public function company_update_pop() {
        //request
        $req = $this->_list_req();
        $req['co_num'] = $this->input->post_get('co_num', true);

        //row
        $company_row = $this->company_model->get_company_row($req['co_num']);

        if( empty($company_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->view("/company/company_update_pop", array(
            'req'           => $req,
            'company_row'   => $company_row,
            'list_url'      => $this->_get_list_url()
        ));
    }//end of company_update_pop()

    /**
     * 외부광고 수정 처리 (Ajax)
     */
    public function company_update_proc() {
        ajax_request_check();

        //request
        $req['co_num'] = $this->input->post_get('co_num', true);

        //row
        $company_row = $this->company_model->get_company_row($req['co_num']);

        if( empty($company_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "co_num" => array("field" => "co_num", "label" => "번호", "rules" => "required|is_natural|".$this->default_set_rules),
            "co_name" => array("field" => "co_name", "label" => "제휴사명", "rules" => "required|max_length[50]|".$this->default_set_rules),
            "co_loginid" => array("field" => "co_loginid", "label" => "제휴사아이디", "rules" => "required|max_length[50]|".$this->default_set_rules),
            "co_passwd" => array("field" => "co_passwd", "label" => "제휴사비밀번호", "rules" => "max_length[50]|".$this->default_set_rules),
            "co_url" => array("field" => "co_url", "label" => "제휴사URL", "rules" => "max_length[200]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $co_num = $this->input->post('co_num', true);
            $co_name = $this->input->post('co_name', true);
            $co_loginid = $this->input->post('co_loginid', true);
            $co_passwd = $this->input->post('co_passwd', true);
            $co_url = str_replace("http://", "", $this->input->post('co_url', true));
            if( !empty($co_url) ) {
                $co_url = "http://" . $co_url;
            }

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['co_name'] = $co_name;
                $query_data['co_loginid'] = $co_loginid;
                $query_data['co_passwd'] = $co_passwd;
                $query_data['co_url'] = $co_url;

                if( $this->company_model->update_company($co_num, $query_data) ) {
                    result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of company_update_proc()

    /**
     * 외부광고 삭제 처리 (Ajax)
     */
    public function company_delete_proc() {
        ajax_request_check();

        //request
        $req['co_num'] = $this->input->post_get('co_num', true);

        //외부광고 정보
        $company_row = $this->company_model->get_company_row($req['co_num']);

        if( empty($company_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //외부광고 삭제
        if( $this->company_model->delete_company($req['co_num']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of company_delete_proc()

}//end of class Company