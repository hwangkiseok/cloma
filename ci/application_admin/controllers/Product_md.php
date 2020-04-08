<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 상품 MD 관련 컨트롤러
 */
class Product_md extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('product_md_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->product_md_list();
    }//end of index()

    /**
     * 목록 request 배열
     * @return array
     */
    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['cate']            = trim($this->input->post_get('cate', true));
        $req['md_div']          = trim($this->input->post_get('md_div', true));
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));
        $req['md_div']          = trim($this->input->post_get('md_div', true));
        $req['display_state']   = $this->input->post_get('display_state', true);        //배열
        $req['sale_state']      = $this->input->post_get('sale_state', true);           //배열
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
     * 상품 MD 목록
     */
    public function product_md_list() {
        //request
        $req = $this->_list_req();

        //카테고리별 상품갯수
        $md_count_array = array();
        $md_count_array['total'] = $this->product_md_model->get_product_md_list("", "", "", true);
        foreach( $this->config->item('product_md_division') as $key => $item ) {
            $md_count_array[$key] = $this->product_md_model->get_product_md_list(array("where" => array("md_div" => $key)), "", "", true);
        }

        //var_dump($md_count_array);

        $this->_header();

        $this->load->view("/product_md/product_md_list", array(
            'req'               => $req,
            'md_count_array'    => $md_count_array,
            'list_per_page'     => $this->list_per_page
        ));

        $this->_footer();
    }//end of product_list()

    /**
     * 상품 MD 목록 (Ajax)
     */
    public function product_md_list_ajax() {
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
        $list_count = $this->product_md_model->get_product_md_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => "/product_md/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $md_list = $this->product_md_model->get_product_md_list($query_array, $page_result['start'], $page_result['limit']);

        //카테고리별 상품갯수
        $md_count_array = array();
        $md_count_array['total'] = $this->product_md_model->get_product_md_list("", "", "", true);
        foreach( $this->config->item('product_md_division') as $key => $item ) {
            $md_count_array[$key] = $this->product_md_model->get_product_md_list(array("where" => array("md_div" => $key)), "", "", true);
        }

        //정렬
        $sort_array = array();
        $sort_array['p_category'] = array("asc", "sorting");
        $sort_array['p_sale_state'] = array("asc", "sorting");
        $sort_array['p_display_state'] = array("asc", "sorting");
        $sort_array['p_termlimit_yn'] = array("asc", "sorting");
        $sort_array['p_name'] = array("asc", "sorting");
        $sort_array['p_supply_price'] = array("asc", "sorting");
        $sort_array['p_original_price'] = array("asc", "sorting");
        $sort_array['p_sale_price'] = array("asc", "sorting");
        $sort_array['p_discount_rate'] = array("asc", "sorting");
        $sort_array['p_wish_count'] = array("asc", "sorting");
        $sort_array['p_share_count'] = array("asc", "sorting");
        $sort_array['p_view_count'] = array("asc", "sorting");
        $sort_array['p_click_count'] = array("asc", "sorting");
        $sort_array['pmd_division'] = array("asc", "sorting");
        $sort_array['pmd_product_num'] = array("asc", "sorting");
        $sort_array['pmd_order'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/product_md/product_md_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "md_count_array"    => $md_count_array,
            "list_count"        => $list_count['cnt'],
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "md_list"           => $md_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of product_md_list_ajax()

    /**
     * 상품 MD 등록 팝업
     */
    public function product_md_insert_pop() {
        $this->load->view("/product_md/product_md_insert_pop", array(
        ));
    }//end of product_md_insert_pop()

    /**
     * 상품 MD 등록 처리 (Ajax)
     */
    public function product_md_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "pmd_division" => array("field" => "pmd_division", "label" => "카테고리", "rules" => "required|in_list[".get_config_item_keys_string("product_md_division")."]|".$this->default_set_rules),
            "pmd_product_num" => array("field" => "pmd_product_num", "label" => "상품", "rules" => "required|".$this->default_set_rules),
            //"pmd_order" => array("field" => "pmd_order", "label" => "기간한정 시작날짜", "rules" => "in_natural|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $pmd_division = $this->input->post('pmd_division', true);
            $pmd_product_num = $this->input->post('pmd_product_num', true);     //":"로 구분
            //$pmd_order = $this->input->post('pmd_order', true);

            if( empty($form_error_array) ) {
                $pmd_product_num_array = explode(":", $pmd_product_num);

                foreach($pmd_product_num_array as $p_num) {
                    //등록
                    $query_data = array();
                    $query_data['pmd_division'] = $pmd_division;
                    $query_data['pmd_product_num'] = $p_num;
                    //$query_data['pmd_order'] = $pmd_order;
                    $this->product_md_model->insert_product_md($query_data);
                }

                ////등록
                //$query_data = array();
                //$query_data['pmd_division'] = $pmd_division;
                //$query_data['pmd_product_num'] = $pmd_product_num;
                ////$query_data['pmd_order'] = $pmd_order;

                //if( $this->product_md_model->insert_product_md($query_data) ) {
                //    result_echo_json(get_status_code('success'), lang('site_insert_success'), true, 'alert');
                //}
                //else {
                //    result_echo_json(get_status_code('error'), lang('site_insert_fail'), true, 'alert');
                //}

                result_echo_json(get_status_code('success'), lang('site_insert_success'), true, 'alert');
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of product_md_insert_proc()

    /**
     * 상품 MD 삭제 (Ajax)
     */
    public function product_md_delete_proc() {
        ajax_request_check();

        //request
        $pmd_division = $this->input->post_get('md_div', true);
        $pmd_product_num = $this->input->post_get('p_num', true);

        //삭제
        if( $this->product_md_model->delete_product_md($pmd_division, $pmd_product_num) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true);
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of product_md_delete_proc()

    /**
     * 상품 MD 순서 수정 (Ajax)
     */
    public function product_md_order_proc() {
        ajax_request_check();

        //var_dump($_POST);

        $data = $this->input->post('data', true);       //배열 ([div:pdt_num] => order 형식)

        if( empty($data) ) {
            result_echo_json(get_status_code('error'), lang('site_no_data'), true, 'alert');
        }

        foreach( $data as $key => $value ) {
            $key_arr = explode(":", $key);
            $pmd_division = $key_arr[0];
            $pmd_product_num = $key_arr[1];
            $pmd_order = $value;

            //var_dump($pmd_division, $pmd_product_num, $pmd_order);

            $this->product_md_model->order_update_product_md($pmd_division, $pmd_product_num, $pmd_order);
        }//end of foreach()

        result_echo_json(get_status_code('success'), '', true);
    }//end of product_md_order_proc()

}//end of class Product_md