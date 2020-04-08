<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 팝업 관련 컨트롤러
 */
class Popup extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('popup_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->popup_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['div']             = trim($this->input->post_get('div', true));
        $req['usestate']        = trim($this->input->post_get('usestate', true));
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
     * 팝업 목록
     */
    public function popup_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/popup/popup_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of popup_list()

    /**
     * 팝업 목록 (Ajax)
     */
    public function popup_list_ajax() {
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
        $list_count = $this->popup_model->get_popup_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/popup/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $popup_list = $this->popup_model->get_popup_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['pu_division'] = array("asc", "sorting");
        $sort_array['pu_subject'] = array("asc", "sorting");
        $sort_array['pu_target_url'] = array("asc", "sorting");
        $sort_array['pu_termlimit_yn'] = array("asc", "sorting");
        $sort_array['pu_regdatetime'] = array("asc", "sorting");
        $sort_array['pu_usestate'] = array("asc", "sorting");
        $sort_array['au_name'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/popup/popup_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "popup_list"       => $popup_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of popup_list_ajax()

    /**
     * 팝업 추가 (팝업)
     */
    public function popup_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/popup/popup_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url()
        ));
    }//end of popup_insert_pop()

    /**
     * 팝업 추가 처리 (Ajax)
     */
    public function popup_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //set rules
        $pu_termlimit_datetime1 = $this->default_set_rules;
        if( $this->input->post('pu_termlimit_yn', true) == 'Y' ) {
            $pu_termlimit_datetime1 .= "|required";
        }
        $pu_termlimit_datetime2 = $this->default_set_rules;
        if( $this->input->post('pu_termlimit_yn', true) == 'Y' ) {
            $pu_termlimit_datetime2 .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "pu_division" => array("field" => "pu_division", "label" => "팝업종류", "rules" => "required|in_list[".get_config_item_keys_string("popup_division")."]|".$this->default_set_rules),
            "pu_subject" => array("field" => "pu_subject", "label" => "팝업제목", "rules" => "required|".$this->default_set_rules),
            "pu_content" => array("field" => "pu_content", "label" => "팝업내용", "rules" => "required"),
            "pu_button_text_confirm" => array("field" => "pu_button_text_confirm", "label" => "버튼텍스트", "rules" => $this->default_set_rules),
            "pu_button_text_cancel" => array("field" => "pu_button_text_cancel", "label" => "버튼텍스트", "rules" => $this->default_set_rules),
            "pu_termlimit_yn" => array("field" => "pu_termlimit_yn", "label" => "노출기간사용여부", "rules" => "required|in_list[".get_config_item_keys_string("popup_termlimit_yn")."]|".$this->default_set_rules),
            "pu_termlimit_datetime1" => array("field" => "pu_termlimit_datetime1", "label" => "노출시작일", "rules" => $pu_termlimit_datetime1),
            "pu_termlimit_datetime2" => array("field" => "pu_termlimit_datetime2", "label" => "노출종료일", "rules" => $pu_termlimit_datetime2),
            "pu_target_url" => array("field" => "pu_target_url", "label" => "이동URL", "rules" => $this->default_set_rules),
            "pu_target_type" => array("field" => "pu_target_type", "label" => "이동URL", "rules" => "in_list[".get_config_item_keys_string("popup_target_type")."]".$this->default_set_rules),
            "pu_platform" => array("field" => "pu_platform", "label" => "플랫폼", "rules" => "in_list[".get_config_item_keys_string("popup_platform")."]".$this->default_set_rules),
            "pu_usestate" => array("field" => "pu_usestate", "label" => "노출여부", "rules" => "required|in_list[".get_config_item_keys_string("popup_usestate")."]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $pu_division = $this->input->post('pu_division', true);
            $pu_subject = $this->input->post('pu_subject', true);
            $pu_content = $this->input->post('pu_content');
            $pu_button_text_confirm = $this->input->post('pu_button_text_confirm', true);
            $pu_button_text_cancel = $this->input->post('pu_button_text_cancel', true);
            $pu_termlimit_yn = $this->input->post('pu_termlimit_yn', true);
            $pu_termlimit_datetime1 = $this->input->post('pu_termlimit_datetime1', true);
            $pu_termlimit_datetime2 = $this->input->post('pu_termlimit_datetime2', true);
            $pu_target_url = $this->input->post('pu_target_url', true);
            $pu_target_type = $this->input->post('pu_target_type', true);
            $pu_platform = $this->input->post('pu_platform', true);
            $pu_usestate = $this->input->post('pu_usestate', true);

            $pu_button_text_array = array();
            if( empty($pu_button_text_confirm) ) {
                $pu_button_text_confirm = "확인";
            }
            if( empty($pu_button_text_cancel) ) {
                $pu_button_text_cancel = "취소";
            }
            $pu_button_text_array['confirm'] = $pu_button_text_confirm;
            $pu_button_text_array['cancel'] = $pu_button_text_cancel;

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['pu_division'] = $pu_division;
                $query_data['pu_subject'] = $pu_subject;
                $query_data['pu_content'] = $pu_content;
                $query_data['pu_button_text'] = json_encode_no_slashes($pu_button_text_array);
                $query_data['pu_termlimit_yn'] = $pu_termlimit_yn;
                $query_data['pu_termlimit_datetime1'] = number_only($pu_termlimit_datetime1);
                $query_data['pu_termlimit_datetime2'] = number_only($pu_termlimit_datetime2);
                $query_data['pu_target_url'] = $pu_target_url;
                $query_data['pu_target_type'] = $pu_target_type;
                $query_data['pu_platform'] = $pu_platform;
                $query_data['pu_usestate'] = $pu_usestate;

                if( $this->popup_model->insert_popup($query_data) ) {
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
    }//end of popup_insert_proc()

    /**
     * 팝업 수정
     */
    public function popup_update_pop() {
        //request
        $req = $this->_list_req();
        $req['pu_num'] = $this->input->post_get('pu_num', true);

        //row
        $popup_row = $this->popup_model->get_popup_row($req['pu_num']);

        $button_text_array = json_decode($popup_row->pu_button_text, true);
        $popup_row->button_text_confirm = $button_text_array['confirm'];
        $popup_row->button_text_cancel = $button_text_array['cancel'];

        if( empty($popup_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->view("/popup/popup_update_pop", array(
            'req'               => $req,
            'popup_row'    => $popup_row,
            'list_url'          => $this->_get_list_url()
        ));
    }//end of popup_update_pop()

    /**
     * 팝업 수정 처리 (Ajax)
     */
    public function popup_update_proc() {
        ajax_request_check();

        //request
        $req['pu_num'] = $this->input->post_get('pu_num', true);

        //row
        $popup_row = $this->popup_model->get_popup_row($req['pu_num']);

        if( empty($popup_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->library('form_validation');

        //set rules
        $pu_termlimit_datetime1 = $this->default_set_rules;
        if( $this->input->post('pu_termlimit_yn', true) == 'Y' ) {
            $pu_termlimit_datetime1 .= "|required";
        }
        $pu_termlimit_datetime2 = $this->default_set_rules;
        if( $this->input->post('pu_termlimit_yn', true) == 'Y' ) {
            $pu_termlimit_datetime2 .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "pu_num" => array("field" => "pu_num", "label" => "팝업종류", "rules" => "required|is_natural|".$this->default_set_rules),
            "pu_division" => array("field" => "pu_division", "label" => "팝업종류", "rules" => "required|in_list[".get_config_item_keys_string("popup_division")."]|".$this->default_set_rules),
            "pu_subject" => array("field" => "pu_subject", "label" => "제목", "rules" => "required|".$this->default_set_rules),
            "pu_content" => array("field" => "pu_content", "label" => "팝업내용", "rules" => "required"),
            "pu_button_text_confirm" => array("field" => "pu_button_text_confirm", "label" => "버튼텍스트", "rules" => $this->default_set_rules),
            "pu_button_text_cancel" => array("field" => "pu_button_text_cancel", "label" => "버튼텍스트", "rules" => $this->default_set_rules),
            "pu_termlimit_yn" => array("field" => "pu_termlimit_yn", "label" => "공개기간설정", "rules" => "required|in_list[".get_config_item_keys_string("popup_termlimit_yn")."]|".$this->default_set_rules),
            "pu_termlimit_datetime1" => array("field" => "pu_termlimit_datetime1", "label" => "공개시작일", "rules" => $pu_termlimit_datetime1),
            "pu_termlimit_datetime2" => array("field" => "pu_termlimit_datetime2", "label" => "공개종료일", "rules" => $pu_termlimit_datetime2),
            "pu_target_url" => array("field" => "pu_target_url", "label" => "이동URL", "rules" => $this->default_set_rules),
            "pu_target_type" => array("field" => "pu_target_type", "label" => "이동URL", "rules" => "in_list[".get_config_item_keys_string("popup_target_type")."]".$this->default_set_rules),
            "pu_platform" => array("field" => "pu_platform", "label" => "플랫폼", "rules" => "in_list[".get_config_item_keys_string("popup_platform")."]".$this->default_set_rules),
            "pu_usestate" => array("field" => "pu_usestate", "label" => "활성여부", "rules" => "required|in_list[".get_config_item_keys_string("popup_usestate")."]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $pu_num = $this->input->post('pu_num', true);
            $pu_division = $this->input->post('pu_division', true);
            $pu_subject = $this->input->post('pu_subject', true);
            $pu_content = $this->input->post('pu_content');
            $pu_button_text_confirm = $this->input->post('pu_button_text_confirm', true);
            $pu_button_text_cancel = $this->input->post('pu_button_text_cancel', true);
            $pu_termlimit_yn = $this->input->post('pu_termlimit_yn', true);
            $pu_termlimit_datetime1 = $this->input->post('pu_termlimit_datetime1', true);
            $pu_termlimit_datetime2 = $this->input->post('pu_termlimit_datetime2', true);
            $pu_target_url = $this->input->post('pu_target_url', true);
            $pu_target_type = $this->input->post('pu_target_type', true);
            $pu_platform = $this->input->post('pu_platform', true);
            $pu_usestate = $this->input->post('pu_usestate', true);

            $pu_button_text_array = array();
            if( empty($pu_button_text_confirm) ) {
                $pu_button_text_confirm = "확인";
            }
            if( empty($pu_button_text_cancel) ) {
                $pu_button_text_cancel = "취소";
            }
            $pu_button_text_array['confirm'] = $pu_button_text_confirm;
            $pu_button_text_array['cancel'] = $pu_button_text_cancel;

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['pu_division'] = $pu_division;
                $query_data['pu_subject'] = $pu_subject;
                $query_data['pu_content'] = $pu_content;
                $query_data['pu_button_text'] = json_encode_no_slashes($pu_button_text_array);
                $query_data['pu_termlimit_yn'] = $pu_termlimit_yn;
                $query_data['pu_termlimit_datetime1'] = number_only($pu_termlimit_datetime1);
                $query_data['pu_termlimit_datetime2'] = number_only($pu_termlimit_datetime2);
                $query_data['pu_target_url'] = $pu_target_url;
                $query_data['pu_target_type'] = $pu_target_type;
                $query_data['pu_platform'] = $pu_platform;
                $query_data['pu_usestate'] = $pu_usestate;

                if( $this->popup_model->update_popup($pu_num, $query_data) ) {
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
    }//end of popup_update_proc()

    /**
     * 팝업 삭제 처리 (Ajax)
     */
    public function popup_delete_proc() {
        ajax_request_check();

        //request
        $req['pu_num'] = $this->input->post_get('pu_num', true);

        //팝업 정보
        $popup_row = $this->popup_model->get_popup_row($req['pu_num']);

        if( empty($popup_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //팝업 삭제
        if( $this->popup_model->delete_popup($req['pu_num']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of popup_delete_proc()

}//end of class Popup