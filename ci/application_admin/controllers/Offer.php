<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 댓글 관련 컨트롤러
 */
class Offer extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('offer_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {

        $this->offer_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
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
     * 댓글 목록
     */
    public function offer_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/offer/offer_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();

    }//end of offer_list()

    /**
     * 댓글 목록 데이터 (ajax)
     */
    public function offer_list_ajax() {
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
        $list_count = $this->offer_model->get_offer_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => "/offer/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
            //"sort"          => "reverse"
        ));

        $offer_list = $this->offer_model->get_offer_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
//        $sort_array = array();
//        $sort_array['cmt_table'] = array("asc", "sorting");
//
//        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
//        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/offer/offer_list_ajax", array(
            "req"           => $req,
            "GV"            => $GV,
            "PGV"           => $PGV,
//            "sort_array"    => $sort_array,
            "list_count"    => $list_count,
            "list_per_page" => $req['list_per_page'],
            "page"          => $req['page'],
            "offer_list"  => $offer_list,
            "pagination"    => $page_result['pagination']
        ));
    }//end offer_list_ajax;

    /**
     * 참고(노출)여부 변경 토글 추가 dhkim 20190411
     */
    public function offer_proc_flag() {
        ajax_request_check();

        //request
        $req['seq'] = $this->input->post_get('seq', true);
        $req['proc_flag'] = $this->input->post_get('proc_flag', true);

        //글정보
        $offer_row = $this->offer_model->get_offer_row($req['seq']);

        if( empty($offer_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        // 참고처리
        $query_data['proc_flag'] = $req['proc_flag'];
        if( $req['proc_flag'] == "Y" ) {
            $query_data['proc_date'] = current_datetime();
        }
        else {
            $query_data['proc_date'] = "";
        }

        if( $this->offer_model->update_offer($req['seq'], $query_data) ) {
            result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
        }

    }//end of offer_proc_flag()

    /**
     * 댓글 삭제 처리 (Ajax)
     */
    public function offer_delete_proc() {
        ajax_request_check();

        //request
        $req['seq'] = trim($this->input->post_get('seq', true));

        //댓글 정보
        $offer_row = $this->offer_model->get_offer_row($req['seq']);

        if( empty($offer_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //댓글 삭제
        if( $this->offer_model->delete_offer($req['seq']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of offer_delete_proc()

}//end of class offer