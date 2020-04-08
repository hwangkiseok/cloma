<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * APP 상태바 관련 컨트롤러
 */
class App_statusbar extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('app_statusbar_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->app_statusbar_list();
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
     * APP 상태바 목록
     */
    public function app_statusbar_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/app_statusbar/app_statusbar_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of app_statusbar_list()

    /**
     * APP 상태바 목록 (Ajax)
     */
    public function app_statusbar_list_ajax() {
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
        $list_count = $this->app_statusbar_model->get_app_statusbar_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/app_statusbar/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $app_statusbar_list = $this->app_statusbar_model->get_app_statusbar_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['asb_ostype'] = array("asc", "sorting");
        $sort_array['asb_regdatetime'] = array("asc", "sorting");
        $sort_array['asb_procdatetime'] = array("asc", "sorting");
        $sort_array['asb_state'] = array("asc", "sorting");
        $sort_array['asb_usestate'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/app_statusbar/app_statusbar_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "app_statusbar_list"     => $app_statusbar_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of app_statusbar_list_ajax()

    /**
     * APP 상태바 추가 (팝업)
     */
    public function app_statusbar_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/app_statusbar/app_statusbar_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url()
        ));
    }//end of app_statusbar_insert_pop()

    /**
     * APP 상태바 추가 처리 (Ajax)
     */
    public function app_statusbar_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "asb_color" => array("field" => "asb_color", "label" => "상태바색상", "rules" => "required|min_length[6]|max_length[7]" . $this->default_set_rules),
            "asb_usestate" => array("field" => "asb_usestate", "label" => "사용여부", "rules" => "required|in_list[".get_config_item_keys_string("app_statusbar_usestate")."]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $asb_color = $this->input->post('asb_color', true);
            $asb_usestate = $this->input->post('asb_usestate', true);

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['asb_color'] = "#" . strtoupper(str_replace("#", "", $asb_color));
                $query_data['asb_usestate'] = $asb_usestate;

                if( $this->app_statusbar_model->insert_app_statusbar($query_data) ) {
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
    }//end of app_statusbar_insert_proc()

    /**
     * APP 상태바 수정
     */
    public function app_statusbar_update_pop() {
        //request
        $req = $this->_list_req();
        $req['asb_num'] = $this->input->post_get('asb_num', true);

        //row
        $app_statusbar_row = $this->app_statusbar_model->get_app_statusbar_row($req['asb_num']);

        if( empty($app_statusbar_row) ) {
            alert(lang('site_error_empty_data'));
        }
        if( $app_statusbar_row->asb_state > 1 ) {
            alert("적용대기 항목만 수정 가능합니다.");
        }

        $this->load->view("/app_statusbar/app_statusbar_update_pop", array(
            'req'               => $req,
            'app_statusbar_row' => $app_statusbar_row,
            'list_url'          => $this->_get_list_url()
        ));
    }//end of app_statusbar_update_pop()

    /**
     * APP 상태바 수정 처리 (Ajax)
     */
    public function app_statusbar_update_proc() {
        ajax_request_check();

        //request
        $req['asb_num'] = $this->input->post_get('asb_num', true);

        //row
        $app_statusbar_row = $this->app_statusbar_model->get_app_statusbar_row($req['asb_num']);

        if( empty($app_statusbar_row) ) {
            alert(lang('site_error_empty_data'));
        }
        if( $app_statusbar_row->asb_state > 1 ) {
            alert("적용대기 항목만 수정 가능합니다.");
        }

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "asb_num" => array("field" => "asb_num", "label" => "번호", "rules" => "required|is_natural|".$this->default_set_rules),
            "asb_color" => array("field" => "asb_color", "label" => "상태바색상", "rules" => "required|min_length[6]|max_length[7]" . $this->default_set_rules),
            "asb_usestate" => array("field" => "asb_usestate", "label" => "사용여부", "rules" => "required|in_list[".get_config_item_keys_string("app_statusbar_usestate")."]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $asb_num = $this->input->post('asb_num', true);
            $asb_color = $this->input->post('asb_color', true);
            $asb_usestate = $this->input->post('asb_usestate', true);

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['asb_color'] = "#" . strtoupper(str_replace("#", "", $asb_color));
                $query_data['asb_usestate'] = $asb_usestate;

                if( $this->app_statusbar_model->update_app_statusbar($asb_num, $query_data) ) {
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
    }//end of app_statusbar_update_proc()

    /**
     * APP 상태바 삭제 처리 (Ajax)
     */
    public function app_statusbar_delete_proc() {
        ajax_request_check();

        //request
        $req['asb_num'] = $this->input->post_get('asb_num', true);

        //APP 상태바 정보
        $app_statusbar_row = $this->app_statusbar_model->get_app_statusbar_row($req['asb_num']);

        if( empty($app_statusbar_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }
        if( $app_statusbar_row->asb_state == 2 ) {
            alert("적용중인 항목은 삭제 불가!");
        }

        //APP 상태바 삭제
        if( $this->app_statusbar_model->delete_app_statusbar($req['asb_num']) ) {
            //이미지 삭제
            file_delete(1, str_replace($this->config->item("site_http"), "", $app_statusbar_row->asb_color), DOCROOT);

            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of app_statusbar_delete_proc()

    /**
     * 사용여부 변경
     */
    public function app_statusbar_usestate_toggle() {
        ajax_request_check();

        //request
        $req['asb_num'] = $this->input->post_get('asb_num', true);

        //APP 상태바 정보
        $push_row = $this->app_statusbar_model->get_app_statusbar_row($req['asb_num']);

        if( empty($push_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        if( $push_row->asb_usestate == "Y" ) {
            $asb_usestate = "N";
        }
        else {
            $asb_usestate = "Y";
        }

        $query_data = array();
        $query_data['asb_usestate'] = $asb_usestate;
        if( $this->app_statusbar_model->update_app_statusbar_info($push_row->asb_num, $query_data) ) {
            result_echo_json(get_status_code('success'), "", true);
        }
        else {
            result_echo_json(get_status_code('error'), "", true);
        }
    }//end of app_statusbar_usestate_toggle()

}//end of class App_statusbar