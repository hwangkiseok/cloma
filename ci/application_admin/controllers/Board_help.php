<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 게시물 관련 컨트롤러
 */
class Board_help extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('board_help_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->board_help_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['div']             = trim($this->input->post_get('div', true));
        $req['cate']            = trim($this->input->post_get('cate', true));
        $req['top_yn']          = trim($this->input->post_get('top_yn', true));
        $req['usestate']        = trim($this->input->post_get('usestate', true));
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));

        if( empty($req['div']) ) {
            $req['div'] = 1;
        }
        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        return $req;
    }//end of _list_req()

    /**
     * 게시물 목록
     */
    public function board_help_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/board_help/board_help_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of board_help_list()

    /**
     * 게시물 목록 (Ajax)
     */
    public function board_help_list_ajax() {
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
        $list_count = $this->board_help_model->get_board_help_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/board_help/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $board_help_list = $this->board_help_model->get_board_help_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['bh_division'] = array("asc", "sorting");
        $sort_array['bh_category'] = array("asc", "sorting");
        $sort_array['bh_adminuser_num'] = array("asc", "sorting");
        $sort_array['bh_top_yn'] = array("asc", "sorting");
        $sort_array['bh_subject'] = array("asc", "sorting");
        $sort_array['bh_content'] = array("asc", "sorting");
        $sort_array['bh_regdatetime'] = array("asc", "sorting");
        $sort_array['bh_usestate'] = array("asc", "sorting");
        $sort_array['au_name'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/board_help/board_help_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "board_help_list"   => $board_help_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of board_help_list_ajax()

    /**
     * 게시물 추가 (팝업)
     */
    public function board_help_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/board_help/board_help_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url()
        ));
    }//end of board_help_insert_pop()

    /**
     * 게시물 추가 처리 (Ajax)
     */
    public function board_help_insert_proc() {
        ajax_request_check();

        $m_name = '관리자';

        $this->load->library('form_validation');

        $bh_category_set_rules = "in_list[".get_config_item_keys_string("faq_category")."]|" . $this->default_set_rules;
        if( $this->input->post('bh_division', true) == 2 ) {
            $bh_category_set_rules .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "bh_division" => array("field" => "bh_division", "label" => "게시판종류", "rules" => "required|in_list[".get_config_item_keys_string("board_help_division")."]|".$this->default_set_rules),
            "bh_category" => array("field" => "bh_category", "label" => "분류", "rules" => $bh_category_set_rules),
            "bh_top_yn" => array("field" => "bh_top_yn", "label" => "상위노출", "rules" => "in_list[".get_config_item_keys_string("board_help_top_yn")."]|".$this->default_set_rules),
            "bh_subject" => array("field" => "bh_subject", "label" => "제목", "rules" => "required|".$this->default_set_rules),
            "bh_content" => array("field" => "bh_content", "label" => "내용", "rules" => "required")
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $bh_division = $this->input->post('bh_division', true);
            $bh_category = $this->input->post('bh_category', true);
            $bh_top_yn = $this->input->post('bh_top_yn', true);
            $bh_subject = $this->input->post('bh_subject', true);
            $bh_content = $this->input->post('bh_content');

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['bh_division'] = $bh_division;
                $query_data['bh_category'] = $bh_category;
                $query_data['bh_top_yn'] = get_yn_value($bh_top_yn);
                $query_data['bh_subject'] = $bh_subject;
                $query_data['bh_content'] = $bh_content;
                $query_data['bh_last_writer'] = $m_name;

                if( $this->board_help_model->insert_board_help($query_data) ) {
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
    }//end of board_help_insert_proc()

    /**
     * 게시물 수정
     */
    public function board_help_update_pop() {
        //request
        $req = $this->_list_req();
        $req['bh_num'] = $this->input->post_get('bh_num', true);

        //row
        $board_help_row = $this->board_help_model->get_board_help_row($req['bh_num']);

        if( empty($board_help_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->view("/board_help/board_help_update_pop", array(
            'req'               => $req,
            'board_help_row'    => $board_help_row,
            'list_url'          => $this->_get_list_url()
        ));
    }//end of board_help_update_pop()

    /**
     * 게시물 수정 처리 (Ajax)
     */
    public function board_help_update_proc() {
        ajax_request_check();

        $m_name = '관리자';

        //request
        $req['bh_num'] = $this->input->post_get('bh_num', true);

        //row
        $board_help_row = $this->board_help_model->get_board_help_row($req['bh_num']);

        if( empty($board_help_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->library('form_validation');

        $bh_category_set_rules = "in_list[".get_config_item_keys_string("faq_category")."]|" . $this->default_set_rules;
        if( $this->input->post('bh_division', true) == 2 ) {
            $bh_category_set_rules .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "bh_num" => array("field" => "bh_num", "label" => "글번호", "rules" => "required|is_natural|".$this->default_set_rules),
            "bh_division" => array("field" => "bh_division", "label" => "게시판종류", "rules" => "required|in_list[".get_config_item_keys_string("board_help_division")."]|".$this->default_set_rules),
            "bh_category" => array("field" => "bh_category", "label" => "분류", "rules" => $bh_category_set_rules),
            "bh_top_yn" => array("field" => "bh_top_yn", "label" => "상위노출", "rules" => "in_list[".get_config_item_keys_string("board_help_top_yn")."]|".$this->default_set_rules),
            "bh_subject" => array("field" => "bh_subject", "label" => "제목", "rules" => "required|".$this->default_set_rules),
            "bh_content" => array("field" => "bh_content", "label" => "내용", "rules" => "required"),
            "bh_usestate" => array("field" => "bh_usestate", "label" => "노출여부", "rules" => "required|in_list[".get_config_item_keys_string("board_help_usestate")."]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $bh_num = $this->input->post('bh_num', true);
            $bh_division = $this->input->post('bh_division', true);
            $bh_category = $this->input->post('bh_category', true);
            $bh_top_yn = $this->input->post('bh_top_yn', true);
            $bh_subject = $this->input->post('bh_subject', true);
            $bh_content = $this->input->post('bh_content');
            $bh_usestate = $this->input->post('bh_usestate', true);

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['bh_division'] = $bh_division;
                $query_data['bh_category'] = $bh_category;
                $query_data['bh_top_yn'] = get_yn_value($bh_top_yn);
                $query_data['bh_subject'] = $bh_subject;
                $query_data['bh_content'] = $bh_content;
                $query_data['bh_usestate'] = $bh_usestate;
                $query_data['bh_last_writer'] = $m_name;

                if( $this->board_help_model->update_board_help($bh_num, $query_data) ) {
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
    }//end of board_help_update_proc()

    /**
     * 게시물 삭제 처리 (Ajax)
     */
    public function board_help_delete_proc() {
        ajax_request_check();

        //request
        $req['bh_num'] = $this->input->post_get('bh_num', true);

        //게시물 정보
        $board_help_row = $this->board_help_model->get_board_help_row($req['bh_num']);

        if( empty($board_help_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //삭제
        if( $this->board_help_model->delete_board_help($req['bh_num']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of board_help_delete_proc()

}//end of class board_help