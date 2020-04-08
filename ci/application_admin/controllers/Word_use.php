<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 자주 사용하는 문구 관련 컨트롤러
 */
class Word_use extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('word_use_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->word_use_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['wd_use']          = trim($this->input->post_get('wd_use', true));
        $req['wd_view']          = trim($this->input->post_get('wd_view', true));
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
     * 자주 사용하는 문구 목록
     */
    public function word_use_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/word_use/word_use_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of word_use_list()

    /**
     * 자주 사용하는 문구 목록 (Ajax)
     */
    public function word_use_list_ajax() {
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
        $list_count = $this->word_use_model->get_word_use_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/word_use/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $word_use_list = $this->word_use_model->get_word_use_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['bw_word'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/word_use/word_use_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "word_use_list" => $word_use_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of word_use_list_ajax()

    /**
     * 자주 사용하는 문구 추가 (팝업)
     */
    public function word_use_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/word_use/word_use_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url()
        ));
    }//end of word_use_insert_pop()

    /**
     * 자주 사용하는 문구 추가 처리 (Ajax)
     */
    public function word_use_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "wd_subject" => array("field" => "wd_subject", "label" => "제목", "rules" => "required|".$this->default_set_rules),
            "wd_content" => array("field" => "wd_content", "label" => "내용", "rules" => "required|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $wd_subject = trim($this->input->post('wd_subject', true));
            $wd_content = trim($this->input->post('wd_content', true));
            $wd_use = trim($this->input->post('wd_use', true));
            $wd_view = trim($this->input->post('wd_view', true));



            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['wd_subject'] = $wd_subject;
                $query_data['wd_content'] = $wd_content;
                $query_data['wd_use'] = $wd_use;
                $query_data['wd_view'] = $wd_view;

                $query_result = $this->word_use_model->insert_word_use($query_data);

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
    }//end of word_use_insert_proc()



    public function word_use_update_pop() {
        //request
        $req = $this->_list_req();
        $req['wd_num'] = $this->input->post_get('wd_num', true);

        //row
        $word_use_row = $this->word_use_model->get_word_use_row(array('wd_num' => $req['wd_num']));

        if( empty($word_use_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->view("/word_use/word_use_insert_pop", array(
            'req'               => $req,
            'word_use_row'    => $word_use_row,
            'list_url'          => $this->_get_list_url()
        ));
    }//end of banner_update_pop()

    /**
     * 자주 사용하는 문구 수정 처리 (Ajax)
     */
    public function word_use_update_proc() {
        ajax_request_check();

        //request
        $req['wd_num'] = $this->input->post_get('wd_num', true);

        //row
        $word_use_row = $this->word_use_model->get_word_use_row(array('wd_num' => $req['wd_num']));

        if( empty($word_use_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->library('form_validation');



        //폼검증 룰 설정
        $set_rules_array = array(
            "wd_num" => array("field" => "wd_num", "label" => "번호", "rules" => "required|".$this->default_set_rules),
            "wd_use" => array("field" => "wd_use", "label" => "사용처", "rules" => "required|".$this->default_set_rules),
            "wd_subject" => array("field" => "wd_subject", "label" => "제목", "rules" => "required|".$this->default_set_rules),
            "wd_content" => array("field" => "wd_content", "label" => "내용", "rules" => "required|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $wd_num = $this->input->post('wd_num', true);
            $wd_use = $this->input->post('wd_use', true);
            $wd_view = $this->input->post('wd_view', true);
            $wd_subject = $this->input->post('wd_subject', true);
            $wd_content = $this->input->post('wd_content', true);



            if( empty($form_error_array) ) {
                $query_data = array();

                $query_data['wd_use'] = $wd_use;
                $query_data['wd_view'] = $wd_view;
                $query_data['wd_subject'] = $wd_subject;
                $query_data['wd_content'] = $wd_content;


                if( $this->word_use_model->update_word_use($wd_num, $query_data) ) {
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
    }//end of word_use_update_proc()


    /**
     * 자주 사용하는 문구 삭제 처리 (Ajax)
     */
    public function word_use_delete_proc() {
        ajax_request_check();

        //request
        $req['wd_num'] = $this->input->post_get('wd_num', true);

        //자주 사용하는 문구 정보
        $word_use_row = $this->word_use_model->get_word_use_row(array('wd_num' => $req['wd_num']));

        if( empty($word_use_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //자주 사용하는 문구 삭제
        if( $this->word_use_model->delete_word_use($req['wd_num']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of word_use_delete_proc()

}//end of class word_use