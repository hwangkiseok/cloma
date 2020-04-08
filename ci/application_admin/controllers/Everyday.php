<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 매일응모 관련 컨트롤러
 */
class Everyday extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('everyday_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->everyday_list();
    }//end of index()

    /**
     * 목록 request 배열
     * @return array
     */
    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['usestate']        = trim($this->input->post_get('usestate', true));
        $req['displaystate']    = trim($this->input->post_get('displaystate', true));
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
     * 매일응모 목록
     */
    public function everyday_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/everyday/everyday_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of everyday_list()

    /**
     * 매일응모 목록 (Ajax)
     */
    public function everyday_list_ajax() {
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
        $list_count = $this->everyday_model->get_everyday_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/everyday/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //페이지번호 보정
        if( $req['page'] > $page_result['total_page'] ) {
            $req['page'] = $page_result['total_page'];
        }

        //목록
        $everyday_list = $this->everyday_model->get_everyday_list($query_array, $page_result['start'], $page_result['limit']);

        //var_dump($everyday_list);

        //정렬
        $sort_array = array();
        $sort_array['ed_product_num'] = array("asc", "sorting");
        $sort_array['p_name'] = array("asc", "sorting");
        $sort_array['ed_winner_count'] = array("asc", "sorting");
        $sort_array['au_name'] = array("asc", "sorting");
        $sort_array['ed_usestate'] = array("asc", "sorting");
        $sort_array['ed_displaystate'] = array("asc", "sorting");
        $sort_array['ed_enddatetime'] = array("asc", "sorting");
        $sort_array['ed_regdatetime'] = array("asc", "sorting");
        $sort_array['p_termlimit_datetime2'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/everyday/everyday_list_ajax", array(
            "req"           => $req,
            "GV"            => $GV,
            "PGV"           => $PGV,
            "sort_array"    => $sort_array,
            "list_count"    => $list_count,
            "list_per_page" => $req['list_per_page'],
            "page"          => $req['page'],
            "everyday_list" => $everyday_list,
            "pagination"    => $page_result['pagination']
        ));
    }//end of everyday_list_ajax()

    /**
     * 매일응모 등록 팝업
     */
    public function everyday_insert_pop() {
        $this->load->view("/everyday/everyday_insert_pop", array(
        ));
    }//end of everyday_insert_pop()

    /**
     * 매일응모 등록 처리 (Ajax)
     */
    public function everyday_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "ed_usestate" => array("field" => "ed_usestate", "label" => "진행상태", "rules" => "required|in_list[" . get_config_item_keys_string("everyday_usestate"). "]|" . $this->default_set_rules),
            "ed_displaystate" => array("field" => "ed_displaystate", "label" => "노출여부", "rules" => "required|in_list[" . get_config_item_keys_string("everyday_displaystate"). "]|" . $this->default_set_rules),
            "ed_product_num" => array("field" => "ed_product_num", "label" => "상품", "rules" => "required|is_natural|" . $this->default_set_rules),
            "ed_winner_count" => array("field" => "ed_winner_count", "label" => "당첨인원", "rules" => "required|is_natural|" . $this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $ed_usestate = $this->input->post('ed_usestate', true);
            $ed_displaystate = $this->input->post('ed_displaystate', true);
            $ed_product_num = $this->input->post('ed_product_num', true);
            $ed_winner_count = $this->input->post('ed_winner_count', true);

            if( empty($form_error_array) ) {
                //등록
                $query_data = array();
                $query_data['ed_usestate'] = get_yn_value($ed_usestate);
                $query_data['ed_displaystate'] = get_yn_value($ed_displaystate);
                $query_data['ed_product_num'] = number_only($ed_product_num);
                $query_data['ed_winner_count'] = number_only($ed_winner_count);
                $query_data['ed_enddatetime'] = date("YmdHi59", strtotime("+" . $this->config->item('everyday_winner_day') . " days", time())); //초단위로 59초로

                //insert
                $result = $this->everyday_model->insert_everyday($query_data);

                if( $result['code'] == get_status_code('success') ) {
                    result_echo_json(get_status_code('success'), lang('site_insert_success'), true, 'alert');
                }
                else {
                    if( !empty($result['message']) ) {
                        result_echo_json(get_status_code('error'), $result['message'], true, 'alert');
                    }
                    else {
                        result_echo_json(get_status_code('error'), lang('site_insert_fail'), true, 'alert');
                    }
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of everyday_insert_proc()


    /**
     * 매일응모 수정 팝업
     */
    public function everyday_update_pop() {
        //request
        $req['ed_num'] = $this->input->post_get('ed_num', true);

        $everyday_row = $this->everyday_model->get_everyday_row(array("ed_num" => $req['ed_num']));

        if( empty($everyday_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, "alert");
        }

        $this->load->view("/everyday/everyday_update_pop", array(
            "everyday_row"  => $everyday_row
        ));
    }//end of everyday_insert_pop()

    /**
     * 매일응모 수정 처리 (Ajax)
     */
    public function everyday_update_proc() {
        ajax_request_check();

        //request
        $req['ed_num'] = $this->input->post_get('ed_num', true);

        $everyday_row = $this->everyday_model->get_everyday_row(array("ed_num" => $req['ed_num']));

        if( empty($everyday_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, "alert");
        }

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "ed_num" => array("field" => "ed_num", "label" => "번호", "rules" => "required|is_natural|" . $this->default_set_rules),
            "ed_usestate" => array("field" => "ed_usestate", "label" => "진행상태", "rules" => "required|in_list[" . get_config_item_keys_string("everyday_usestate"). "]|" . $this->default_set_rules),
            "ed_displaystate" => array("field" => "ed_displaystate", "label" => "노출여부", "rules" => "required|in_list[" . get_config_item_keys_string("everyday_displaystate"). "]|" . $this->default_set_rules),
            //"ed_product_num" => array("field" => "ed_product_num", "label" => "상품", "rules" => "required|is_natural|" . $this->default_set_rules),
            "ed_winner_count" => array("field" => "ed_winner_count", "label" => "당첨인원", "rules" => "required|is_natural|" . $this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $ed_num = $this->input->post('ed_num', true);
            $ed_usestate = $this->input->post('ed_usestate', true);
            $ed_displaystate = $this->input->post('ed_displaystate', true);
            //$ed_product_num = $this->input->post('ed_product_num', true);
            $ed_winner_count = $this->input->post('ed_winner_count', true);

            if( empty($form_error_array) ) {
                //수정
                $query_data = array();
                $query_data['ed_usestate'] = get_yn_value($ed_usestate);
                $query_data['ed_displaystate'] = get_yn_value($ed_displaystate);
                //$query_data['ed_product_num'] = $ed_product_num;
                $query_data['ed_winner_count'] = number_only($ed_winner_count);

                //update
                $result = $this->everyday_model->update_everyday($ed_num, $query_data);

                if( $result['code'] == get_status_code('success') ) {
                    result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
                }
                else {
                    if( !empty($result['message']) ) {
                        result_echo_json(get_status_code('error'), $result['message'], true, 'alert');
                    }
                    else {
                        result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
                    }
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of everyday_update_proc()

    /**
     * 매일응모 수정 토글
     */
    function everyday_update_toggle() {
        ajax_request_check();

        //request
        $req['ed_num'] = trim($this->input->post_get('ed_num', true));
        $req['fd'] = trim($this->input->post_get('fd', true));          //ed_usestate, ed_displaystate


        //수정 가능 필드
        $allow_fds = array('ed_usestate', 'ed_displaystate');

        if( !in_array($req['fd'], $allow_fds) ) {
            result_echo_json(get_status_code('error'), "", true, 'alert');
        }

        $query_data = array();
        $query_data['ed_num'] = $req['ed_num'];
        $everyday_row = $this->everyday_model->get_everyday_row($query_data);

        if( empty($everyday_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        $query_data = array();
        if( $everyday_row->{$req['fd']} == "Y" ) {
            $query_data[$req['fd']] = "N";
        }
        else {
            $query_data[$req['fd']] = "Y";
        }

        if( $this->everyday_model->update_everyday($everyday_row->ed_num, $query_data, false) ) {
            result_echo_json(get_status_code('success'), '', true);
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_error_db'), true, 'alert');
        }
    }//end of everyday_update_toggle()

    /**
     * 매일응모 삭제 (Ajax)
     */
    public function everyday_delete_proc() {
        ajax_request_check();

        //request
        $req['ed_num'] = $this->input->post_get('ed_num', true);

        //삭제
        if( $this->everyday_model->delete_everyday($req['ed_num']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true);
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of everyday_delete_proc()

}//end of class Everyday