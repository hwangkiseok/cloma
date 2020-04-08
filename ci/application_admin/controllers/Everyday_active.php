<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 매일응모 참여 관련 컨트롤러
 */
class Everyday_active extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('everyday_active_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->everyday_active_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['ym']              = trim($this->input->post_get('ym', true));
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['dateType']        = trim($this->input->post_get('dateType', true));
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));
        $req['usestate']        = trim($this->input->post_get('usestate', true));
        $req['displaystate']    = trim($this->input->post_get('displaystate', true));
        $req['win_yn']          = trim($this->input->post_get('win_yn', true));
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
     * 이벤트 참여 목록
     */
    public function everyday_active_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/everyday_active/everyday_active_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of everyday_active_list()

    /**
     * 이벤트 참여 목록 (Ajax)
     */
    public function everyday_active_list_ajax() {
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
        $list_count = $this->everyday_active_model->get_everyday_active_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/everyday_active/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $everyday_active_list = $this->everyday_active_model->get_everyday_active_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['p_name'] = array("asc", "sorting");
        $sort_array['m_loginid'] = array("asc", "sorting");
        $sort_array['ed_winner_count'] = array("asc", "sorting");
        $sort_array['ed_startdatetime'] = array("asc", "sorting");
        $sort_array['ed_enddatetime'] = array("asc", "sorting");
        $sort_array['eda_winner_yn'] = array("asc", "sorting");
        $sort_array['eda_regdatetime'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/everyday_active/everyday_active_list_ajax", array(
            'req'               => $req,
            'GV'                => $GV,
            'PGV'               => $PGV,
            'sort_array'        => $sort_array,
            'list_count'        => $list_count,
            'list_per_page'     => $req['list_per_page'],
            'page'              => $req['page'],
            'everyday_active_list' => $everyday_active_list,
            'pagination'        => $page_result['pagination']
        ));
    }//end of everyday_active_list_ajax()


    /**
     * 당첨자 배송정보
     */
    public function everyday_active_winner_detail_pop() {
        ajax_request_check();

        //request
        $req['eda_everyday_num'] = $this->input->post_get("ednum", true);
        $req['eda_member_num'] = $this->input->post_get("mnum", true);

        //model
        $this->load->model('everyday_winner_model');

        $query_data = array();
        $query_data['edw_everyday_num'] = $req['eda_everyday_num'];
        $query_data['edw_member_num'] = $req['eda_member_num'];
        $winner_row = $this->everyday_winner_model->get_everyday_winner_row($query_data);

        $this->load->view("/everyday_active/everyday_active_winner_detail_pop", array(
            "winner_row"   => $winner_row
        ));
    }//end of everyday_winner_detail()


    /**
     * 당첨자 배송정보 수정 (ajax)
     */
    public function everyday_active_winner_update_proc() {
        ajax_request_check();

        //request
        $req['edw_num'] = $this->input->post_get("edw_num", true);

        //model
        $this->load->model('everyday_winner_model');

        $query_data = array();
        $query_data['edw_num'] = $req['edw_num'];
        $winner_row = $this->everyday_winner_model->get_everyday_winner_row($query_data);

        if( empty($winner_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }


        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "edw_num" => array("field" => "edw_num", "label" => "이름", "rules" => "required|is_natural|".$this->default_set_rules),
            "edw_name" => array("field" => "edw_name", "label" => "이름", "rules" => "required|".$this->default_set_rules),
            "edw_contact" => array("field" => "edw_contact", "label" => "연락처", "rules" => "required|".$this->default_set_rules),
            "edw_zipcode" => array("field" => "edw_zipcode", "label" => "우편번호", "rules" => "required|".$this->default_set_rules),
            "edw_address1" => array("field" => "edw_address1", "label" => "주소1", "rules" => "required|".$this->default_set_rules),
            "edw_address2" => array("field" => "edw_address1", "label" => "주소2", "rules" => $this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $edw_num = $this->input->post('edw_num', true);
            $edw_name = $this->input->post('edw_name', true);
            $edw_contact = $this->input->post('edw_contact', true);
            $edw_zipcode = $this->input->post('edw_zipcode', true);
            $edw_address1 = $this->input->post('edw_address1', true);
            $edw_address2 = $this->input->post('edw_address2', true);

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['edw_name'] = $edw_name;
                $query_data['edw_contact'] = $edw_contact;
                $query_data['edw_zipcode'] = $edw_zipcode;
                $query_data['edw_address1'] = $edw_address1;
                $query_data['edw_address2'] = $edw_address2;

                if( $this->everyday_winner_model->update_everyday_winner($edw_num, $query_data) ) {
                    result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
                }
            }
        }//end of if(폼 검증 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of everyday_active_winner_update_proc()


    /**
     * 이벤트 참여 삭제 처리 (Ajax)
     */
    public function everyday_active_delete_proc() {
        ajax_request_check();

        //request
        $req['ea_num'] = $this->input->post_get('ea_num', true);

        //이벤트 참여 정보
        $everyday_active_row = $this->everyday_active_model->get_everyday_active_row($req['ea_num']);

        if( empty($everyday_active_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //이벤트 참여 삭제
        if( $this->everyday_active_model->delete_everyday_active($req['ea_num']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of everyday_active_delete_proc()

}//end of class Everyday_active