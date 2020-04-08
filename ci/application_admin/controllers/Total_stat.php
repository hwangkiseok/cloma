<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 전체통계 관련 컨트롤러
 */
class Total_stat extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('total_stat_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->total_stat_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['year']            = trim($this->input->post_get('year', true));           //년도
        $req['month']           = trim($this->input->post_get('month', true));          //월
        $req['sort_yn']         = trim($this->input->post_get('sort_yn', true));        //정렬기능사용여부
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));

        if( empty($req['year']) ) {
            $req['year'] = date("Y", time());
        }
        if( empty($req['month']) ) {
            $req['month'] = date("m", time());
        }
        if( empty($req['sort_yn']) ) {
            $req['sort_yn'] = "Y";
        }
        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 50;
        }

        return $req;
    }//end of _list_req()

    /**
     * 목록
     */
    public function total_stat_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/total_stat/total_stat_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of total_stat_list()

    /**
     * 목록 (Ajax)
     */
    public function total_stat_list_ajax() {
        ajax_request_check(true);

        //request
        $req = $this->_list_req();

        //쿼리 배열
        $query_array =  array();
        $query_array['where'] = $req;
        if( !empty($req['sort_field']) && !empty($req['sort_type']) ) {
            $query_array['orderby'] = $req['sort_field'] . " " . $req['sort_type'];
        }

        //목록
        $stat_list = $this->total_stat_model->get_total_stat_list($query_array);

        $count_array = array();
        foreach( $stat_list as $key => $row ) {
            $row->week = get_config_item_text(date("N", strtotime($row->t_date)), "week_name");

            foreach( $row as $field => $value ) {
                if( $field == "t_num" || $field == "t_date" || $field == "week" ) {
                    continue;
                }

                $count_array[$field] += $value;
            }//end of foreach()
        }//end of foreach()

        //정렬
        $sort_array = array();
        $sort_array['t_date'] = array("asc", "sorting");
        $sort_array['t_view_app'] = array("asc", "sorting");
        $sort_array['t_view_web'] = array("asc", "sorting");
        $sort_array['t_uniq_view_app'] = array("asc", "sorting");
        $sort_array['t_uniq_view_web'] = array("asc", "sorting");
        $sort_array['t_join'] = array("asc", "sorting");
        $sort_array['t_join_sns'] = array("asc", "sorting");
        $sort_array['t_join_total'] = array("asc", "sorting");
        $sort_array['t_first_buy_match'] = array("asc", "sorting");
        $sort_array['t_join_del'] = array("asc", "sorting");
        $sort_array['t_join_1'] = array("asc", "sorting");
        $sort_array['t_join_tmp'] = array("asc", "sorting");
        $sort_array['t_product_view'] = array("asc", "sorting");
        $sort_array['t_product_view_app'] = array("asc", "sorting");
        $sort_array['t_product_view_web'] = array("asc", "sorting");
        $sort_array['t_product_click'] = array("asc", "sorting");
        $sort_array['t_product_click_app'] = array("asc", "sorting");
        $sort_array['t_product_click_web'] = array("asc", "sorting");
        $sort_array['t_order'] = array("asc", "sorting");
        $sort_array['t_order_app'] = array("asc", "sorting");
        $sort_array['t_order_web'] = array("asc", "sorting");
        $sort_array['t_product_wish'] = array("asc", "sorting");
        $sort_array['t_product_share'] = array("asc", "sorting");
        $sort_array['t_qna'] = array("asc", "sorting");
        $sort_array['t_attend'] = array("asc", "sorting");
        $sort_array['t_attend_accrue'] = array("asc", "sorting");
        $sort_array['t_attend_winner'] = array("asc", "sorting");
        $sort_array['t_everyday'] = array("asc", "sorting");
        $sort_array['t_review_cnt'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/total_stat/total_stat_list_ajax", array(
            "req"           => $req,
            "sort_array"    => $sort_array,
            "stat_list"     => $stat_list,
            "count_array"   => $count_array
        ));
    }//end of total_stat_list_ajax()

}//end of class Total_stat