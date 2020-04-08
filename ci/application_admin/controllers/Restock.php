<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 특가전
 */
class Restock extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('product_model');
    }//end of __construct()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['date_type']       = trim($this->input->post_get('date_type', true));
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));
        $req['term_yn']         = trim($this->input->post_get('term_yn', true));
        $req['display_state']   = $this->input->post_get('display_state', true);        //배열
        $req['sale_state']      = $this->input->post_get('sale_state', true);           //배열
        $req['sort_field']      = trim($this->input->post_get('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->post_get('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));

        $req['display_state']   = $this->input->post_get('display_state', true);        //배열
        $req['sale_state']      = $this->input->post_get('sale_state', true);           //배열
        $req['hash_chk']        = trim($this->input->post_get('hash_chk', true));             //해시여부
        $req['second_prict_yn'] = trim($this->input->post_get('second_prict_yn', true)); //2차판매가 여부
        $req['restock_yn']      = trim($this->input->post_get('restock_yn', true)); //품절제외


        $req['isRestock']       = true;

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        return $req;
    }//end of _list_req()


    /**
     * index
     */
    public function index() {
        $this->lists();
    }//end of index()

    public function restock_lists(){

        $this->_header();

        //request
        $req = $this->_list_req();

        $this->load->view("/restock/restock_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();

    }

    /**
     * 특가전 목록 (Ajax)
     */
    public function restock_list_ajax() {
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
        $list_count = $this->product_model->get_product_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => "/special_offer/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $restock_list = $this->product_model->get_product_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['p_display_state'] = array("asc", "sorting");
        $sort_array['p_sale_state'] = array("asc", "sorting");
        $sort_array['p_stock_state'] = array("asc", "sorting");
        $sort_array['p_termlimit_yn'] = array("asc", "sorting");
        $sort_array['p_name'] = array("asc", "sorting");
        $sort_array['p_comment_count'] = array("asc", "sorting");
        $sort_array['p_review_count'] = array("asc", "sorting");
        $sort_array['p_restock_cnt'] = array("asc", "sorting");


        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/restock/restock_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count['cnt'],
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "restock_list"      => $restock_list,
            "pagination"        => $page_result['pagination'],
        ));
    }//end of product_md_list_ajax()

}//end of class Special_offer

