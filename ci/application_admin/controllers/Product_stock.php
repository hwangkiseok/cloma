<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 상품재고관리
 */
class Product_stock extends A_Controller {

    public $LIMIT_CNT = 10;

    public function __construct() {

        parent::__construct();

        //model
        $this->load->model('product_stock_model');



    }//end of __construct()

    private function _list_req() {
        $req = array();
        $req['kfd']                 = trim($this->input->post_get('kfd', true));
        $req['kwd']                 = trim($this->input->post_get('kwd', true));
        $req['cate']                = trim($this->input->post_get('cate', true));
        $req['date_type']           = trim($this->input->post_get('date_type', true));
        $req['date1']               = trim($this->input->post_get('date1', true));
        $req['date2']               = trim($this->input->post_get('date2', true));
        $req['md_div']              = trim($this->input->post_get('md_div', true));
        $req['term_yn']             = trim($this->input->post_get('term_yn', true));
        $req['display_state']       = $this->input->post_get('display_state', true);            //배열
        $req['sale_state']          = $this->input->post_get('sale_state', true);               //배열
        $req['sort_field']          = trim($this->input->post_get('sort_field', true));         //정렬필드
        $req['sort_type']           = trim($this->input->post_get('sort_type', true));          //정렬구분(asc, desc)
        $req['page']                = trim($this->input->post_get('page', true));
        $req['list_per_page']       = trim($this->input->post_get('list_per_page', true));
        $req['db']                  = trim($this->input->post_get('db', true));                 //DB
        $req['main_banner_view']    = trim($this->input->post_get('main_banner_view', true));   //메인노출
        $req['hash_chk']            = trim($this->input->post_get('hash_chk', true));           //DB
        $req['second_prict_yn']     = trim($this->input->post_get('second_prict_yn', true));    //2차판매가 여부
        $req['restock_yn']          = trim($this->input->post_get('restock_yn', true));         //품절제외
        $req['price_second']        = $this->input->post_get('price_second', true);             //2차판매가여부(배열)
        $req['price_third']         = $this->input->post_get('price_third', true);              //3차판매가여부(배열)
        $req['p_dlv_type']          = $this->input->post_get('p_dlv_type', true);               //배송조건(배열)

        $req['proc_yn']             = $this->input->post_get('srh_proc_yn', true);  //처리상태
        $req['issue_yn']            = $this->input->post_get('srh_issue_yn', true);  //재고부족옵션

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
        $this->product_stock_list();
    }//end of index()

    /**
     * 상품 목록
     */
    public function product_stock_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/product_stock/product_list", array(
            'req'                   => $req,
            'list_per_page'         => $this->list_per_page
        ));

        $this->_footer();
    }//end of product_list()

    /**
     * 상품 목록 (Ajax)
     */
    public function product_stock_list_ajax() {
        ajax_request_check(true);

        //request
        $req = $this->_list_req();
        //print_r($req);

        //var_dump($req);

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
        $list_count = $this->product_stock_model->get_product_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => "/product/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //페이지번호 보정
        if( $req['page'] > $page_result['total_page'] ) {
            $req['page'] = $page_result['total_page'];
        }


        //목록
        $product_list = $this->product_stock_model->get_product_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['p_category'] = array("asc", "sorting");
        $sort_array['p_sale_state'] = array("asc", "sorting");
        $sort_array['p_display_state'] = array("asc", "sorting");
        $sort_array['p_stock_state'] = array("asc", "sorting");
        $sort_array['p_regdatetime'] = array("asc", "sorting");
        $sort_array['p_termlimit_yn'] = array("asc", "sorting");
        $sort_array['p_name'] = array("asc", "sorting");
        $sort_array['p_supply_price'] = array("asc", "sorting");
        $sort_array['p_original_price'] = array("asc", "sorting");
        $sort_array['p_sale_price'] = array("asc", "sorting");
        $sort_array['p_discount_rate'] = array("asc", "sorting");
        $sort_array['p_wish_count'] = array("asc", "sorting");
        $sort_array['p_share_count'] = array("asc", "sorting");
        $sort_array['p_view_count'] = array("asc", "sorting");
        $sort_array['p_view_today_count'] = array("asc", "sorting");
        $sort_array['p_click_count'] = array("asc", "sorting");
        $sort_array['p_click_today_count'] = array("asc", "sorting");
        $sort_array['p_order_count'] = array("asc", "sorting");
        $sort_array['p_order'] = array("asc", "sorting");

        $sort_array['p_comment_count'] = array("asc", "sorting");
        $sort_array['p_review_count'] = array("asc", "sorting");
        $sort_array['p_restock_cnt'] = array("asc", "sorting");


        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/product_stock/product_list_ajax", array(
            "req"                   => $req,
            "GV"                    => $GV,
            "PGV"                   => $PGV,
            "sort_array"            => $sort_array,
            "list_count"            => $list_count,
            "list_per_page"         => $req['list_per_page'],
            "page"                  => $req['page'],
            "product_list"          => $product_list,
            "pagination"            => $page_result['pagination']
        ));
    }//end of product_list_ajax()

    public function set_flag(){

        $p_num = $this->input->post('p_num');

        $aInput = array(
            'proc_yn'   => $this->input->post('proc_yn')
        ,   'mod_id'    => $_SESSION['session_au_num']
        ,   'proc_date' => date('YmdHis')
        ,   'mod_date'  => date('YmdHis')
        );

        $ret = $this->product_stock_model->publicUpdate('stock_chk_tb' , $aInput , array('p_num',$p_num));

        if($ret == true){
            echo json_encode_no_slashes(array('success' => true , 'msg' => '' , 'data' => array() ));
        }else{
            echo json_encode_no_slashes(array('success' => false , 'msg' => '실패!' , 'data' => array() ));
        }

    }

    public function option_pop(){

        $aInput = array(
            'p_order_code' => $this->input->get('p_order_code')
        );

        $sql = "SELECT * FROM snsform_product_tb WHERE item_no = '{$aInput['p_order_code']}'; ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();


        $option_arr = array();
        $option_info = json_decode($aResult['option_info'],true);
        foreach ($option_info as $r) {
            if( $r['option_count'] <= $this->LIMIT_CNT ) $option_arr[] = $r;
        }

        $this->load->view("/product_stock/option_pop", array(
                'aProductInfo'  => $aResult
            ,   'option_arr'    => $option_arr
        ));

    }


    public function chk_stock(){

        $sql = "SELECT B.p_name 
                FROM stock_chk_tb A
                INNER JOIN product_tb B ON A.p_num = B.p_num 
                WHERE issue_yn = 'Y' 
                AND del_yn = 'N'
                AND proc_yn <> 'I';
        ";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        $issue_name_arr = array();
        foreach ($aResult as $r) {
            $issue_name_arr[] = $r['p_name'];
        }

        echo json_encode_no_slashes(array('success' => true , 'msg' => '' , 'data' => array('cnt'=> count($aResult) , 'name' => implode('<br>',$issue_name_arr))));

    }

}//end of class Product