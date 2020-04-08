<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 금칙어 관련 컨트롤러
 */
class Banned_words extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('banned_words_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->banned_words_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['no_like']         = trim($this->input->post_get('no_like', true));        //일치검색(Y|'')
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
     * 금칙어 목록
     */
    public function banned_words_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/banned_words/banned_words_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of banned_words_list()

    /**
     * 금칙어 목록 (Ajax)
     */
    public function banned_words_list_ajax() {
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
        $list_count = $this->banned_words_model->get_banned_words_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/banned_words/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $banned_words_list = $this->banned_words_model->get_banned_words_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['bw_word'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/banned_words/banned_words_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "banned_words_list" => $banned_words_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of banned_words_list_ajax()

    /**
     * 금칙어 추가 (팝업)
     */
    public function banned_words_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/banned_words/banned_words_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url()
        ));
    }//end of banned_words_insert_pop()

    /**
     * 금칙어 추가 처리 (Ajax)
     */
    public function banned_words_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "bw_word" => array("field" => "bw_word", "label" => "금칙어", "rules" => "required|".$this->default_set_rules),
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $bw_word = trim($this->input->post('bw_word', true));

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['bw_word'] = $bw_word;

                $query_result = $this->banned_words_model->insert_banned_words($query_data);

                if( $query_result['code'] ==  get_status_code("success") ) {
                    result_echo_json(get_status_code('success'), lang('site_insert_success'), true, 'alert');
                }
                else {
                    $msg = lang('site_insert_fail');
                    if( !empty($query_result['message']) ) {
                        $msg = $query_result['message'];
                    }

                    result_echo_json(get_status_code('error'), $msg, true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of banned_words_insert_proc()

    /**
     * 금칙어 삭제 처리 (Ajax)
     */
    public function banned_words_delete_proc() {
        ajax_request_check();

        //request
        $req['bw_num'] = $this->input->post_get('bw_num', true);

        //금칙어 정보
        $banned_words_row = $this->banned_words_model->get_banned_words_row(array('bw_num' => $req['bw_num']));

        if( empty($banned_words_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //금칙어 삭제
        if( $this->banned_words_model->delete_banned_words($req['bw_num']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of banned_words_delete_proc()

}//end of class Banned_words