<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 연관상품 관련 컨트롤러
 */
class Product_rel extends A_Controller {

    public function __construct() {

        parent::__construct();

        //model
        $this->load->model('product_rel_model');
    }//end of __construct()

    /**
     * DB 연결
     * @param string $db_group
     * @return mixed
     */
    private function _get_db($db_group="") {
        if( !empty($db_group) ) {
            if ( $this->load->database($db_group, true) ) {
                return $this->load->database($db_group, true);
            }
        }

        return $this->load->database("default", true);
    }//end of _get_db()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['page']            = trim($this->input->post_get('page', true));
        $req['prod_display_state']      = $this->input->post_get('prod_display_state', true);           //배열(상품노출여부)
        $req['prod_sale_state']         = $this->input->post_get('prod_sale_state', true);              //배열(상품판매상태)
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['rel_yn']          = $this->input->post_get('rel_yn', true);

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
        $this->product_rel_list();

    }//end of index()

    /**
     * 연관상품 목록
     */
    public function product_rel_list() {

        //echo get_short_url_yourls('naver.com');

        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/product_rel/product_rel_list", array(
                'req' => $req
        ));

        $this->_footer();
    }//end of product_rel_list()

    /**
     * 연관상품 목록 (Ajax)
     */
    public function product_rel_list_ajax() {
        ajax_request_check(true);

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
        $list_count = $this->product_rel_model->get_product_rel_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/product_rel/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //페이지번호 보정
        if( $req['page'] > $page_result['total_page'] ) {
            $req['page'] = $page_result['total_page'];
        }

        //목록
        $product_rel_list = $this->product_rel_model->get_product_rel_list($query_array, $page_result['start'], $page_result['limit']);

        foreach ($product_rel_list as $row) if($row->able_cnt > 0) $row->rel_list = $this->product_rel_model->get_product_rel_info($row->p_num,true);


        //정렬
        $sort_array = array();
        $sort_array['able_cnt'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";



        $this->load->view("/product_rel/product_rel_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "product_rel_list"  => $product_rel_list,
            "pagination"        => $page_result['pagination'],
            "sort_array"        => $sort_array
        ));
    }//end of product_rel_list_ajax()

    /**
     * 연관상품 수정
     */
    public function product_rel_update() {

        $this->load->model('product_model');

        $p_num          = $this->input->get('p_num');

        $oProductInfo   = $this->product_model->get_product_row($p_num);
        $rel_list       = $this->product_rel_model->get_product_rel_info($p_num);

        $this->load->model('product_model');

        $aInput = array(
                'sale_state'    => 'Y'
            ,   'restock_yn'    => 'Y'
            ,   'rel_product'   => 'Y'
        );

        $oProductList   = $this->product_model->get_product_list(array('where' => $aInput));

        $i = 0;
        foreach ($oProductList as $v) {

            if($p_num == $v->p_num) continue;
            if($v['p_stock_state'] == 'N') continue;

            $aRes[$i]   = array(    'desc'              => $v['p_summary']
                                ,   'label'             => $v['p_name']
                                ,   'hash'              => $v['p_hash']
                                ,   'value'             => $v['p_num']
                                ,   'p_today_image'     => json_decode($v['p_rep_image'],true)
                                ,   'p_review_count'    => $v['p_review_count']
                                ,   'p_tot_order_count' => $v['p_tot_order_count']
                                ,   'p_order_code'      => $v['p_order_code']
                                ,   'p_review_count_str'    => number_format($v['p_review_count'])
                                ,   'p_tot_order_count_str' => product_count($v['p_tot_order_count'])

                                ,   'p_sale_state'      => $v['p_sale_state']
                                ,   'p_display_state'   => $v['p_display_state']
                                ,   'p_stock_state'     => $v['p_stock_state']

            );
            $i++;

        }

        $this->_header();

        $this->load->view("/product_rel/product_rel_update", array(
                'oProductInfo'  => $oProductInfo
            ,   'rel_list'      => $rel_list
            ,   'aProductList'  => $aRes
            ,   'list_url'      => $this->_get_list_url()
        ));

        $this->_footer();

    }//end of product_rel_update()

    /**
     * 연관상품 수정 처리 (Ajax)
     *
     * @add 각 쇼핑앱에 데이터를 동기화하기때문에 field가 추가 및 upsert작업시
     *       - 각 쇼핑앱에 field 동기화
     *       - 또는 추가 필드 unset 처리
     */
    public function product_rel_update_proc() {

        ajax_request_check();

        $aInput = array(
            'p_num_arr'  => $this->input->post('p_num') //연관상품 배열[자식상품]
        ,   'p_pnum'     => $this->input->post('p_pnum') //상위상품[부모상품]
        );

        $ret = $this->product_rel_model->set_product_sort($aInput);

        echo json_encode_no_slashes($ret);

    }//end of product_rel_update_proc()

    public function product_rel_sort(){
        ajax_request_check();

        $aInput = array(
                'p_order_code_arr'  => $this->input->post('p_order_code')
            ,   'sort_type'         => $this->input->post('sort_type')
            ,   'sort'              => $this->input->post('sort')
        );

        $ret = $this->product_rel_model->get_product_sort($aInput);

        echo json_encode_no_slashes(array('success' => true , 'msg' => '' , 'data' => $ret));

    }

    public function product_rel_item_drop(){

        ajax_request_check();
        $aInput = array( 'seq' => $this->input->post('seq') );
        $ret = $this->product_rel_model->item_drop($aInput['seq']);

        if($ret == true){
            echo json_encode_no_slashes(array('success' => true , 'msg' => '' , 'data' => array()));
        }else{
            echo json_encode_no_slashes(array('success' => false , 'msg' => '변경 실패' , 'data' => array()));
        }

    }

    public function product_rel_detail() {

        $this->load->model('product_model');

        $p_num          = $this->input->get('p_num');

        $oProductInfo   = $this->product_model->get_product_row($p_num);
        $rel_list       = $this->product_rel_model->get_product_rel_info($p_num);

        $this->load->view("/product_rel/product_rel_detail", array(
                'rel_list'      => $rel_list
            ,   'oProductInfo'  => $oProductInfo
        ));

    }//end of product_rel_update_proc()


}//end of class Product_rel