<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 상품 관련 컨트롤러
 */
class Product extends A_Controller {

    public function __construct() {

        parent::__construct();

        //model
        $this->load->model('product_model');
        $this->load->model('product_option_model');
        $this->load->model('product_md_model');

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

        $req['p_outside_display_able'] = $this->input->post_get('p_outside_display_able', true);               //배송조건(배열)



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
        $this->product_list();
    }//end of index()

    /**
     * 상품 목록
     */
    public function product_list() {
        //request
        $req = $this->_list_req();


        //상태별 상품갯수
        $product_count_array = array();
        $product_count_array['total'] = $this->product_model->get_product_list("", "", "", true);
        $product_count_array['sale_state']['Y'] = $this->product_model->get_product_list(array('where'=>array('sale_state'=>array('Y'))), "", "", true);
        $product_count_array['sale_state']['N'] = $this->product_model->get_product_list(array('where'=>array('sale_state'=>array('N'))), "", "", true);
        $product_count_array['display_state']['Y'] = $this->product_model->get_product_list(array('where'=>array('display_state'=>array('Y'))), "", "", true);
        $product_count_array['display_state']['N'] = $this->product_model->get_product_list(array('where'=>array('display_state'=>array('N'))), "", "", true);

        $this->_header();

        $this->load->view("/product/product_list", array(
            'req'                   => $req,
            'product_count_array'   => $product_count_array,
            'list_per_page'         => $this->list_per_page
        ));

        $this->_footer();
    }//end of product_list()

    /**
     * 상품 목록 (Ajax)
     */
    public function product_list_ajax() {
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
        $list_count = $this->product_model->get_product_list($query_array, "", "", true);

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
        $product_list = $this->product_model->get_product_list($query_array, $page_result['start'], $page_result['limit']);

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

        $this->load->view("/product/product_list_ajax", array(
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

    /**
     * 상품 검색 (Ajax)
     */
    public function product_search_ajax() {
        ajax_request_check(true);

        //var_dump($_SERVER['HTTP_ACCEPT']);

        if( strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false ) {
            $dataType = 'json';
        }
        else {
            $dataType = 'html';
        }

        //request
        $req = $this->_list_req();
        $req['noPage'] = $this->input->post_get('noPage', true);
        //$req['dataType'] = $this->input->post_get('dataType', true);        // 출력형태(html=기본, json)
        //$req['pagination'] = $this->input->post_get('pagination', true);    // 페이징출력여부(Y=기본, N=출력안함)

        //if( empty($req['dataType']) ) {
        //    $req['dataType'] = 'html';
        //}
        //if( empty($req['pagination']) ) {
        //    $req['pagination'] = 'Y';
        //}

        $pgv_array = $req;
        unset($pgv_array['page']);

        $gv_array = $pgv_array;
        $gv_array['page'] = $req['page'];

        $PGV = http_build_query($pgv_array);
        $GV = http_build_query($gv_array);

        //쿼리 배열
        $query_array =  array();
        $query_array['where'] = $req;
        $query_array['where']['display_state'] = 'Y';
        if( !empty($req['sort_field']) && !empty($req['sort_type']) ) {
            $query_array['orderby'] = $req['sort_field'] . " " . $req['sort_type'];
        }

        //전체갯수
        $list_count = $this->product_model->get_product_list($query_array, "", "", true);

        $list_start = "";
        $list_limit = "";
        if( $dataType == 'html' ) {
            //페이징
            $page_result = $this->_paging(array(
                "total_rows"    => $list_count['cnt'],
                "base_url"      => "/product/search_ajax/?" . $PGV,
                "per_page"      => $req['list_per_page'],
                "page"          => $req['page'],
                "ajax"          => true
            ));

            $list_start = $page_result['start'];
            $list_limit = $page_result['limit'];

            //페이지번호 보정
            if( $req['page'] > $page_result['total_page'] ) {
                $req['page'] = $page_result['total_page'];
            }
        }

        //목록
        if( $req['noPage'] == 'Y' ) {
            $product_list = $this->product_model->get_product_list($query_array, "", "");
        }
        else {
            $product_list = $this->product_model->get_product_list($query_array, $list_start, $list_limit);
        }

        foreach( $product_list as $key => $row ) {
            $product_list[$key]['p_rep_image_array'] = json_decode($row['p_rep_image'], true);
            $product_list[$key]['p_display_state_text'] = $this->config->item($row['p_display_state'], 'product_display_state');
            $product_list[$key]['p_sale_state_text'] = $this->config->item($row['p_sale_state'], 'product_sale_state');
            $product_list[$key]['p_stock_state_text'] = $this->config->item($row['p_stock_state'], 'product_stock_state');
        }

        //json 출력
        if( $dataType == 'json' ) {
            result_echo_json(get_status_code('success'), "", true, "", "", $product_list);
        }
        //html 출력
        else {
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

            $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
            $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

            $this->load->view("/product/product_search_ajax", array(
                "req"                   => $req,
                "GV"                    => $GV,
                "PGV"                   => $PGV,
                "sort_array"            => $sort_array,
                "list_count"            => $list_count['cnt'],
                "list_per_page"         => $req['list_per_page'],
                "page"                  => $req['page'],
                "product_list"          => $product_list,
                "pagination"            => ( isset($page_result['pagination']) ) ? $page_result['pagination'] : ""
            ));
        }
    }//end of product_list_ajax()

    /**
     * 상품 추가
     */
    public function product_insert() {
        $this->_header();

        if($this->input->get('dev') == 'Y'){
            $viewFile = "/product/product_insert_zs";
        }else{
            $viewFile = "/product/product_insert";
        }


        $this->load->model('category_md_model');
        $aCategoryTmpLists = $this->category_md_model->get_category_md_list();
        $aCategoryLists = array();

        foreach ($aCategoryTmpLists as $r) {
            $aCategoryLists[$r->cmd_name] = $r->cmd_name;
        }

        $this->load->view($viewFile, array(
                "list_url"  => $this->_get_list_url()
            ,   "aCategoryLists" => $aCategoryLists
            ,   'opt_token' => create_session_id()
        ));

        $this->_footer();
    }//end of product_insert()

    /**
     * 상품 추가 처리 (Ajax)
     * @add 각 쇼핑앱에 데이터를 동기화하기때문에 field가 추가 및 upsert작업시
     *      - 각 쇼핑앱에 field 동기화
     *      - 또는 추가 필드 unset 처리
     */
    public function product_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //set rules
        $p_termlimit_datetime1_set_rules = $this->default_set_rules;
        $p_termlimit_datetime1_hour_set_rules = $this->default_set_rules;
        $p_termlimit_datetime2_set_rules = $this->default_set_rules;
        $p_termlimit_datetime2_hour_set_rules = $this->default_set_rules;
        if( $this->input->post('p_termlimit_yn', true) == 'Y' ) {
            $p_termlimit_datetime1_set_rules .= "|required";
            $p_termlimit_datetime1_hour_set_rules .= "|required";
            $p_termlimit_datetime2_set_rules .= "|required";
            $p_termlimit_datetime2_hour_set_rules .= "|required";
        }
        $p_wish_raise_count_set_rules = $this->default_set_rules . "|is_natural";
        if( $this->input->post('p_wish_raise_yn', true) == 'Y' ) {
            $p_wish_raise_count_set_rules .= "|required";
        }
        $p_share_raise_count_set_rules = $this->default_set_rules . "|is_natural";
        if( $this->input->post('p_share_raise_yn', true) == 'Y' ) {
            $p_share_raise_count_set_rules .= "|required";
        }
        //$p_deliveryprice_set_rules = $this->default_set_rules;
        //if( $this->input->post('p_deliveryprice_type', true) == '1' ) {
        //    $p_deliveryprice_set_rules .= "|required";
        //}
        $p_rep_image_set_rules = $this->default_set_rules;
        if( !isset($_FILES['p_rep_image']['name']) || empty($_FILES['p_rep_image']['name']) ) {
            $p_rep_image_set_rules .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "p_cate1" => array("field" => "p_cate1", "label" => "카테고리", "rules" => "required|".$this->default_set_rules),
            "p_termlimit_yn" => array("field" => "p_termlimit_yn", "label" => "기간한정사용여부", "rules" => "required|in_list[".get_config_item_keys_string("product_termlimit_yn")."]|".$this->default_set_rules),
            "p_termlimit_datetime1" => array("field" => "p_termlimit_datetime1", "label" => "기간한정 시작날짜", "rules" => $p_termlimit_datetime1_set_rules),
            "p_termlimit_datetime1_hour" => array("field" => "p_termlimit_datetime1_hour", "label" => "기간한정 시작시각", "rules" => $p_termlimit_datetime1_hour_set_rules),
            "p_termlimit_datetime2" => array("field" => "p_termlimit_datetime2", "label" => "기간한정 종료날짜", "rules" => $p_termlimit_datetime2_set_rules),
            "p_termlimit_datetime2_hour" => array("field" => "p_termlimit_datetime2_hour", "label" => "기간한정 종료시각", "rules" => $p_termlimit_datetime2_hour_set_rules),
            "p_display_state" => array("field" => "p_display_state", "label" => "진열상태", "rules" => "required|in_list[".get_config_item_keys_string("product_display_state")."]|".$this->default_set_rules),
            "p_sale_state" => array("field" => "p_sale_state", "label" => "판매상태", "rules" => "required|in_list[".get_config_item_keys_string("product_sale_state")."]|".$this->default_set_rules),
            "p_stock_state" => array("field" => "p_stock_state", "label" => "재고상태", "rules" => "required|in_list[".get_config_item_keys_string("product_stock_state")."]|".$this->default_set_rules),
            "p_display_info" => array("field" => "p_display_info[]", "label" => "아이콘 1차", "rules" => $this->default_set_rules),
            "p_display_info_4" => array("field" => "p_display_info_4[]", "label" => "아이콘 2차(추가)", "rules" => $this->default_set_rules),
            "pmd_division" => array("field" => "pmd_division[]", "label" => "추가노출", "rules" => $this->default_set_rules),
            "p_wish_count" => array("field" => "p_wish_count", "label" => "관심수", "rules" => "is_natural|".$this->default_set_rules),
            "p_wish_raise_yn" => array("field" => "p_wish_raise_yn", "label" => "관심증가수 사용여부", "rules" => "in_list[".get_config_item_keys_string("product_wish_raise_yn")."]|".$this->default_set_rules),
            "p_wish_raise_count" => array("field" => "p_wish_raise_count", "label" => "관심증가수", "rules" => $p_wish_raise_count_set_rules),
            "p_share_count" => array("field" => "p_share_count", "label" => "공유수", "rules" => "is_natural|".$this->default_set_rules),
            "p_share_raise_yn" => array("field" => "p_share_raise_yn", "label" => "공유증가수 사용여부", "rules" => "trim|xss_clean|in_list[".get_config_item_keys_string("product_share_raise_yn")."]|prep_for_form|strip_tags"),
            "p_share_raise_count" => array("field" => "p_share_raise_count", "label" => "공유증가수", "rules" => $p_share_raise_count_set_rules),
            "p_name" => array("field" => "p_name", "label" => "상품명", "rules" => "required|".$this->default_set_rules),
            "p_hash" => array("field" => "p_hash", "label" => "해시태그", "rules" => "".$this->default_set_rules),
            "p_summary" => array("field" => "p_summary", "label" => "간략설명", "rules" => $this->default_set_rules),
            "p_detail" => array("field" => "p_detail", "label" => "상세설명", "rules" => "trim"),
            //"p_order_link_protocol" => array("field" => "p_order_link_protocol", "label" => "주문링크 프로토콜", "rules" => "required|in_list[http://,https://]|".$this->default_set_rules),
            //"p_order_link" => array("field" => "p_order_link", "label" => "주문링크", "rules" => "required|".$this->default_set_rules),
            "p_order_code" => array("field" => "p_order_code", "label" => "공구폼상품코드", "rules" => "required|".$this->default_set_rules),
            "p_supply_price" => array("field" => "p_supply_price", "label" => "공급가격(원가)", "rules" => "required|".$this->default_set_rules),
            "p_discount_rate" => array("field" => "p_discount_rate", "label" => "할인율", "rules" => $this->default_set_rules),
            "p_original_price" => array("field" => "p_original_price", "label" => "기존가격", "rules" => $this->default_set_rules),
            "p_margin_price" => array("field" => "p_margin_price", "label" => "판매마진", "rules" => $this->default_set_rules),
            "p_sale_price" => array("field" => "p_sale_price", "label" => "판매가격", "rules" => "required|".$this->default_set_rules),
            "p_margin_rate" => array("field" => "p_margin_rate", "label" => "마진율", "rules" => $this->default_set_rules),
            "p_taxation" => array("field" => "p_taxation", "label" => "과세여부", "rules" => "required|in_list[".get_config_item_keys_string("product_taxation")."]|".$this->default_set_rules),
            "p_origin" => array("field" => "p_origin", "label" => "원산지", "rules" => $this->default_set_rules),
            "p_manufacturer" => array("field" => "p_manufacturer", "label" => "제조사", "rules" => $this->default_set_rules),
            "p_supplier" => array("field" => "p_supplier", "label" => "공급사", "rules" => $this->default_set_rules),
            "p_deliveryprice_type" => array("field" => "p_deliveryprice_type", "label" => "배송비설정", "rules" => "required|in_list[".get_config_item_keys_string("product_deliveryprice_type")."]|".$this->default_set_rules),
            "p_deliveryprice" => array("field" => "p_deliveryprice", "label" => "배송비", "rules" => "is_natural|".$this->default_set_rules),
            "p_rep_image" => array("field" => "p_rep_image", "label" => "대표이미지", "rules" => $p_rep_image_set_rules),
            "p_today_image" => array("field" => "p_today_image", "label" => "오늘추천이미지", "rules" => $this->default_set_rules),
            "p_banner_image" => array("field" => "p_banner_image", "label" => "배너이미지", "rules" => $this->default_set_rules),
            "p_detail_image" => array("field" => "p_detail_image[]", "label" => "상세이미지", "rules" => $this->default_set_rules),
            "detail_add_weight" => array("field" => "detail_add_weight", "label" => "용량/중량", "rules" => $this->default_set_rules),
            "detail_add_delivery_info" => array("field" => "detail_add_delivery_info", "label" => "배송정보", "rules" => $this->default_set_rules),
            "detail_add_deliveryprice_text" => array("field" => "detail_add_deliveryprice_text", "label" => "배송비설명", "rules" => $this->default_set_rules),


            "p_option_use" => array("field" => "p_option_use", "label" => "옵션 여부", "rules" => $this->default_set_rules),
            "p_option_type" => array("field" => "p_option_type", "label" => "옵션 타입", "rules" => $this->default_set_rules),
            "p_option_depth" => array("field" => "p_option_depth", "label" => "옵션 차수", "rules" => $this->default_set_rules),
            "opt_token" => array("field" => "opt_token", "label" => "옵션 토큰", "rules" => $this->default_set_rules),

            // 190412 황기석 새 필드 추가
//            "p_outside_display_able" => array("field" => "p_outside_display_able", "label" => "노출여부", "rules" => "required|in_list[".get_config_item_keys_string("product_info_tab_view")."]|".$this->default_set_rules),
            // 190703 황기석 새 필드 추가
//            "p_info_tab_view" => array("field" => "p_info_tab_view", "label" => "상품정보제공공시 노출여부", "rules" => "required|in_list[".get_config_item_keys_string("product_info_tab_view")."]|".$this->default_set_rules),

        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $p_cate1 = $this->input->post('p_cate1', true);
            $p_termlimit_yn = $this->input->post('p_termlimit_yn', true);
            $p_termlimit_datetime1 = number_only($this->input->post('p_termlimit_datetime1', true));
            $p_termlimit_datetime1_hour = number_only($this->input->post('p_termlimit_datetime1_hour', true));
            $p_termlimit_datetime1_min = number_only($this->input->post('p_termlimit_datetime1_min', true));
            if( !empty($p_termlimit_datetime1) ) {
                $p_termlimit_datetime1 = $p_termlimit_datetime1.$p_termlimit_datetime1_hour.$p_termlimit_datetime1_min."00";
            }
            $p_termlimit_datetime2 = number_only($this->input->post('p_termlimit_datetime2', true));
            $p_termlimit_datetime2_hour = number_only($this->input->post('p_termlimit_datetime2_hour', true));
            $p_termlimit_datetime2_min = number_only($this->input->post('p_termlimit_datetime2_min', true));
            if( !empty($p_termlimit_datetime2) ) {
                $p_termlimit_datetime2 = $p_termlimit_datetime2.$p_termlimit_datetime2_hour.$p_termlimit_datetime2_min."59";
            }
            $p_display_state = $this->input->post('p_display_state', true);
            $p_sale_state = $this->input->post('p_sale_state', true);
            $p_stock_state = $this->input->post('p_stock_state', true);
            $p_display_info = $this->input->post('p_display_info', true);
            $p_display_info_4 = $this->input->post('p_display_info_4', true);   //배열
            if( !empty($p_display_info) || !empty($p_display_info_4) ) {
                $p_display_info_array = array();

                if( is_array($p_display_info) ) {
                    foreach( $p_display_info as $item ) {
                        $p_display_info_array[$item] = 'Y';
                    }
                }
                else {
                    $p_display_info_array[$p_display_info] = 'Y';
                }

                if( is_array($p_display_info_4) ) {
                    foreach( $p_display_info_4 as $item ) {
                        $p_display_info_array[$item] = 'Y';
                    }
                }
                else {
                    $p_display_info_array[$p_display_info_4] = 'Y';
                }

                $p_display_info = json_encode_no_slashes($p_display_info_array);
            }
            else {
                $p_display_info = "";
            }
            $pmd_division = $this->input->post('pmd_division', true);               //배열
            $p_wish_count = $this->input->post('p_wish_count', true);
            $p_wish_raise_yn = $this->input->post('p_wish_raise_yn', true);
            $p_wish_raise_count = $this->input->post('p_wish_raise_count', true);
            $p_share_count = $this->input->post('p_share_count', true);
            $p_share_raise_yn = $this->input->post('p_share_raise_yn', true);
            $p_share_raise_count = $this->input->post('p_share_raise_count', true);
            $p_hash = $this->input->post('p_hash', true);
            $p_name = $this->input->post('p_name', true);
            $p_summary = $this->input->post('p_summary', true);
            $p_detail = $this->input->post('p_detail');     //HTML 태그 사용
            //$p_order_link_protocol = $this->input->post('p_order_link_protocol', true);
            //$p_order_link = $p_order_link_protocol . str_replace(array("http://", "https://"), array("", ""), $this->input->post('p_order_link', true));
            $p_order_code = $this->input->post('p_order_code', true);
            $p_supply_price = number_only($this->input->post('p_supply_price', true));
            $p_discount_rate = $this->input->post('p_discount_rate', true);
            $p_original_price = number_only($this->input->post('p_original_price', true));
            $p_margin_price = $this->input->post('p_margin_price', true);
            $p_sale_price = number_only($this->input->post('p_sale_price', true));
            $p_margin_rate = $this->input->post('p_margin_rate', true);
            $p_taxation = $this->input->post('p_taxation', true);
            $p_origin = $this->input->post('p_origin', true);
            $p_manufacturer = $this->input->post('p_manufacturer', true);
            $p_supplier = $this->input->post('p_supplier', true);
            $p_deliveryprice_type = $this->input->post('p_deliveryprice_type', true);
            $p_deliveryprice = $this->input->post('p_deliveryprice', true);
            $p_top_desc = $this->input->post('p_top_desc', true);

            $detail_add_weight = $this->input->post('detail_add_weight', true);
            $detail_add_delivery_info = $this->input->post('detail_add_delivery_info', true);
            $detail_add_deliveryprice_text = $this->input->post('detail_add_deliveryprice_text', true);

            //190412 황기석 새필드 추가
            $p_outside_display_able = $this->input->post('p_outside_display_able', true);
            //190703 황기석 새필드 추가
//            $p_info_tab_view = $this->input->post('p_info_tab_view', true);


            $p_option_use = $this->input->post('p_option_use', true);
            $p_option_type = $this->input->post('p_option_type', true);
            $p_option_depth = $this->input->post('p_option_depth', true);
            $opt_token = $this->input->post('opt_token', true);


            $p_detail_add = "";
            $p_detail_add_array = array();
            foreach ($this->config->item('product_detail_add') as $key => $item) {
                $p_detail_add_array[$key] .= ${"detail_add_" . $key};
            }
            if( !empty($p_detail_add_array) ) {
                $p_detail_add = json_encode_no_slashes($p_detail_add_array);
            }

            //할인율, 마진, 마진율 계산
            $p_discount_rate = "0.00";
            $p_margin_price = "0";
            $p_margin_rate = "0.00";
            if( $p_original_price && $p_sale_price ) {
                $p_discount_rate = number_format((($p_original_price - $p_sale_price) / $p_original_price) * 100, 2);
            }
            if( $p_supply_price && $p_sale_price ) {
                $p_margin_price = $p_sale_price - $p_supply_price;
            }
            if( $p_supply_price && $p_sale_price ) {
                $p_margin_rate = number_format((($p_sale_price - $p_supply_price) / $p_sale_price ) * 100, 2);
            }

            //if( $p_deliveryprice_type == '2' ) {
            //    $p_deliveryprice = 0;
            //}
            //else {
            //    $p_deliveryprice = $this->input->post('p_deliveryprice', true);
            //}
            $p_rep_image = $this->input->post('p_rep_image', true);
            $p_today_image = "";
            $p_banner_image = "";
            $p_detail_image = "";
            $p_rep_image_add = "";

            //대표이미지 업로드 (썸네일 생성)
            $p_image_path_web = $this->config->item('product_image_path_web') . "/" . substr($p_order_code, 0, 2) . "/" . $p_order_code;
            $p_image_path = DOCROOT . $p_image_path_web;
            create_directory($p_image_path);

            $config = array();
            $config['upload_path'] = $p_image_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size']	= $this->config->item('upload_total_max_size');
            $config['file_ext_tolower'] = true;                 //확장자 소문자
            $config['overwrite'] = true;                        //덮어쓰기
            $config['file_name'] = $p_order_code . "_rep";      //파일명
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if( $this->upload->do_upload('p_rep_image') ){
                $p_rep_image_data_array = $this->upload->data();
                @chmod($p_rep_image_data_array['full_path'], 0664);

                //움직이는 gif일때 썸네일 생성하지 않음.(181019/김홍주)
                if( is_ani_pic($p_rep_image_data_array['full_path']) ) {
                    $file_name = $p_image_path_web . "/" . $p_rep_image_data_array['raw_name'] . $p_rep_image_data_array['file_ext'];
                    $p_rep_image_array = array('1' => $file_name, '0' => $file_name);
                }
                else {
                    $p_rep_image_array = create_thumb_image($p_rep_image_data_array, $p_image_path_web, $this->config->item('product_rep_image_size'), true);
                }

                if( empty($p_rep_image_array) || $p_rep_image_array === false ) {
                    $form_error_array['p_rep_image'] = "대표이미지 썸네일 생성을 실패했습니다.";
                }
                else {
                    //$p_rep_image_array[0] = $p_image_path_web . "/" . $p_rep_image_data_array['file_name'];   //원본유지일때
                    $p_rep_image_array[0] = $p_rep_image_array[1];  //원본삭제일때
                    $p_rep_image = json_encode_no_slashes($p_rep_image_array);
                }
            }
            else {
                $form_error_array['p_rep_image'] = strip_tags($this->upload->display_errors());
            }//end of if()





            //대표이미지 추가 업로드 (썸네일 생성)
            if( isset($_FILES['p_rep_image_add']['name']) && !empty($_FILES['p_rep_image_add']['name']) ) {
                $p_rep_image_add_data_array = array();
                $p_rep_image_add_array = array();

                foreach( $_FILES['p_rep_image_add']['name'] as $key => $name ) {

                    //var_dump($_FILES['p_detail_image']['name'][$key]);

                    //기본 파일 배열 설정 (CI에서 사용)
                    $_FILES['userfile']['name'] = $_FILES['p_rep_image_add']['name'][$key];
                    $_FILES['userfile']['type'] = $_FILES['p_rep_image_add']['type'][$key];
                    $_FILES['userfile']['tmp_name'] = $_FILES['p_rep_image_add']['tmp_name'][$key];
                    $_FILES['userfile']['error'] = $_FILES['p_rep_image_add']['error'][$key];
                    $_FILES['userfile']['size'] = $_FILES['p_rep_image_add']['size'][$key];

                    $config['file_name'] = $p_order_code . "_detail_" . $key;      //파일명

                    $this->upload->initialize($config);

                    if( $this->upload->do_upload() ){
                        //var_dump("ok =====> ", $key);

                        $p_rep_image_add_data_array[$key] = $this->upload->data();

                        //움직이는 gif일때 썸네일 생성하지 않음.(181019/김홍주)
                        if( is_ani_pic($p_rep_image_add_data_array['full_path']) ) {
                            $file_name = $p_image_path_web . "/" . $p_rep_image_add_data_array['raw_name'] . $p_rep_image_add_data_array['file_ext'];
                            $p_rep_image_add_array[$key] = array('1' => $file_name, '0' => $file_name);
                        }
                        else {
                            $p_rep_image_add_array[$key] = create_thumb_image($p_rep_image_add_data_array[$key], $p_image_path_web, $this->config->item('product_detail_image_size'), true);
                        }

                        //$p_rep_image_add_array[$key] = create_thumb_image($p_rep_image_add_data_array[$key], $p_image_path_web, $this->config->item('product_detail_image_size'), true);
                        @chmod($p_rep_image_add_data_array[$key]['full_path'], 0664);

                        //var_dump($p_detail_image_data_array[$key]);
                        //var_dump($p_detail_image_array[$key]);

                        if( empty($p_rep_image_add_array[$key]) || $p_rep_image_add_array[$key] === false ) {
                            $form_error_array['p_rep_image_add'] = "[".$key."] 추가 대표이미지 썸네일 생성을 실패했습니다.";
                        }
                        //업로드 성공시
                        else {
                            //$p_detail_image_array[$key][0] = $p_image_path_web . "/" . $p_detail_image_data_array[$key]['file_name']; //원본유지일때
                            $p_rep_image_add_array[$key][0] = $p_rep_image_add_array[$key][1];    //원본삭제일때
                            //기존 파일 삭제 (파일명이 고정이기 때문에 기존 파일 삭제하면 안 됨)
                            //file_delete(2, $product_detail_image_array[$key], DOCROOT);

                        }
                    }
                    else {
                        $form_error_array['p_rep_image_add'] = "[".$key."] ".strip_tags($this->upload->display_errors());
                    }
                }//end of foreach()

                $p_rep_image_add = json_encode_no_slashes($p_rep_image_add_array);
            }//end of if()

            //상세이미지 사용을 위해 init
            unset($_FILES['userfile']);

            //오늘추천이미지 업로드
            if( isset($_FILES['p_today_image']['name']) && !empty($_FILES['p_today_image']['name']) ) {
                $config['file_name'] = $p_order_code . "_today";      //파일명
                $this->upload->initialize($config);

                if( $this->upload->do_upload('p_today_image') ){
                    $upload_data_array = $this->upload->data();
                    $p_today_image = $p_image_path_web . "/" . $upload_data_array['file_name'];

                }
                else {
                    $form_error_array['p_today_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if()

            //배너이미지 업로드
            if( isset($_FILES['p_banner_image']['name']) && !empty($_FILES['p_banner_image']['name']) ) {
                $config['file_name'] = $p_order_code . "_banner";      //파일명
                $this->upload->initialize($config);

                if( $this->upload->do_upload('p_banner_image') ){
                    $upload_data_array = $this->upload->data();
                    $upload_data_array = create_thumb_image($upload_data_array, $p_image_path_web, $this->config->item('banner_image_size'), true);

                    $p_banner_image = $p_image_path_web . "/" . $upload_data_array['file_name'];

                }
                else {
                    $form_error_array['p_banner_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if()

            //상세이미지 업로드 (썸네일 생성)
            if( isset($_FILES['p_detail_image']['name']) && !empty($_FILES['p_detail_image']['name']) ) {
                $p_detail_image_data_array = array();
                $p_detail_image_array = array();

                foreach( $_FILES['p_detail_image']['name'] as $key => $name ) {
                    $no = $key + 1;

                    //기본 파일 배열 설정 (CI에서 사용)
                    $_FILES['userfile']['name'] = $_FILES['p_detail_image']['name'][$key];
                    $_FILES['userfile']['type'] = $_FILES['p_detail_image']['type'][$key];
                    $_FILES['userfile']['tmp_name'] = $_FILES['p_detail_image']['tmp_name'][$key];
                    $_FILES['userfile']['error'] = $_FILES['p_detail_image']['error'][$key];
                    $_FILES['userfile']['size'] = $_FILES['p_detail_image']['size'][$key];

                    $config['file_name'] = $p_order_code . "_detail_" . $key;      //파일명

                    $this->upload->initialize($config);

                    if( $this->upload->do_upload() ){
                        $p_detail_image_data_array[$key] = $this->upload->data();
                        $p_detail_image_array[$key] = create_thumb_image($p_detail_image_data_array[$key], $p_image_path_web, $this->config->item('product_detail_image_size'), true);

                        if( empty($p_detail_image_array[$key]) || $p_detail_image_array[$key] === false ) {
                            $form_error_array['p_detail_image'] = "[".$no."] 상세이미지 썸네일 생성을 실패했습니다.";
                        }
                        else {
                            //$p_detail_image_array[$key][0] = $p_image_path_web . "/" . $p_detail_image_data_array[$key]['file_name']; //원본유지일때
                            $p_detail_image_array[$key][0] = $p_detail_image_array[$key][1];    //원본삭제일때
                            //$p_detail_image = json_encode_no_slashes($p_detail_image_array);

                        }
                    }
                    else {
                        $form_error_array['p_detail_image'] = "[".$no."] ".strip_tags($this->upload->display_errors());
                    }
                }//end of foreach()

                $p_detail_image = json_encode_no_slashes($p_detail_image_array);
            }//end of if()

            if( empty($form_error_array) ) {
                //카테고리정보 가져오기
                $p_cate1 = $p_cate1 ? $p_cate1 : '';
                $p_cate2 = $p_cate2 ? $p_cate2 : '';
                $p_cate3 = $p_cate3 ? $p_cate3 : '';

                /*3차판매가*/
                $p_price_third_yn = (isset($cate_info['p_price_third_yn']) && !empty($cate_info['p_price_third_yn'])) ? $cate_info['p_price_third_yn'] : "N";
                $p_price_third = (isset($cate_info['p_price_third']) && !empty($cate_info['p_price_third'])) ? $cate_info['p_price_third'] : "0";

                /*2차판매가*/
                $p_price_second_yn = (isset($cate_info['p_price_second_yn']) && !empty($cate_info['p_price_second_yn'])) ? $cate_info['p_price_second_yn'] : "N";
                $p_price_second = (isset($cate_info['p_price_second']) && !empty($cate_info['p_price_second'])) ? $cate_info['p_price_second'] : "0";


                //등록
                $query_data = array();

                $query_data['p_price_third_yn'] = $p_price_third_yn;
                $query_data['p_price_third'] = $p_price_third;

                $query_data['p_price_second_yn'] = $p_price_second_yn;
                $query_data['p_price_second'] = $p_price_second;

//                $query_data['p_category'] = $p_category;
                $query_data['p_cate1'] = $p_cate1;
                $query_data['p_cate2'] = $p_cate2;
                $query_data['p_cate3'] = $p_cate3;
                $query_data['p_hash'] = str_replace(array("\'", "\""), array("´", "˝"), $p_hash);
                $query_data['p_name'] = str_replace(array("\'", "\""), array("´", "˝"), $p_name);
                $query_data['p_summary'] = $p_summary;
                $query_data['p_detail'] = $p_detail;
                $query_data['p_detail_add'] = $p_detail_add;
                $query_data['p_rep_image'] = $p_rep_image;
                $query_data['p_today_image'] = $p_today_image;
                $query_data['p_banner_image'] = $p_banner_image;
                $query_data['p_detail_image'] = $p_detail_image;
                //$query_data['p_order_link'] = $p_order_link;
                $query_data['p_order_code'] = $p_order_code;
                $query_data['p_supply_price'] = number_only($p_supply_price, true);
                $query_data['p_original_price'] = number_only($p_original_price, true);
                $query_data['p_sale_price'] = number_only($p_sale_price, true);
                $query_data['p_margin_price'] = number_only($p_margin_price, true);
                $query_data['p_discount_rate'] = $p_discount_rate;
                $query_data['p_margin_rate'] = $p_margin_rate;
                $query_data['p_taxation'] = $p_taxation;
                $query_data['p_origin'] = $p_origin;
                $query_data['p_manufacturer'] = $p_manufacturer;
                $query_data['p_supplier'] = $p_supplier;
                $query_data['p_deliveryprice_type'] = $p_deliveryprice_type;
                $query_data['p_deliveryprice'] = number_only($p_deliveryprice, true);
                $query_data['p_wish_count'] = number_only($p_wish_count, true);
                $query_data['p_wish_raise_yn'] = get_yn_value($p_wish_raise_yn);
                $query_data['p_wish_raise_count'] = number_only($p_wish_raise_count, true);
                $query_data['p_share_count'] = number_only($p_share_count, true);
                $query_data['p_share_raise_yn'] = get_yn_value($p_share_raise_yn);
                $query_data['p_share_raise_count'] = number_only($p_share_raise_count, true);
                $query_data['p_termlimit_yn'] = get_yn_value($p_termlimit_yn);
                $query_data['p_termlimit_datetime1'] = $p_termlimit_datetime1;
                $query_data['p_termlimit_datetime2'] = $p_termlimit_datetime2;
                $query_data['p_display_info'] = $p_display_info;
                $query_data['p_display_state'] = get_yn_value($p_display_state);
                $query_data['p_sale_state'] = get_yn_value($p_sale_state);
                $query_data['p_stock_state'] = get_yn_value($p_stock_state);

                $query_data['p_outside_display_able'] = $p_outside_display_able;
//                $query_data['p_info_tab_view'] = get_yn_value($p_info_tab_view);

                if(empty($p_option_use) == false ) $query_data['p_option_use'] = $p_option_use;
                if(empty($p_option_type) == false ) $query_data['p_option_type'] = $p_option_type;
                if(empty($p_option_depth) == false ) $query_data['p_option_depth'] = $p_option_depth;


                if($p_rep_image_add != ''){
                    $query_data['p_rep_image_add'] = $p_rep_image_add;
                }

                /*상단 고정이미지*/
                $query_data['p_top_desc'] = number_only($p_top_desc,true);
                /*상단 고정이미지*/

                $insert_result = $this->product_model->insert_product($query_data);

                if( $insert_result['code'] == get_status_code('success') ) {

                    /**
                     *@TODO 옵션 매핑관련 테스트 진행예정
                     **/
                    if($p_option_use == 'Y'){

                        $aOptionList = $this->product_option_model->get_option_list(array('option_token' => $opt_token));
                        if(count($aOptionList) > 0){
                            $this->product_option_model->set_mapping_option(array('p_num' => $insert_result['id'] ,'option_token' => $opt_token , 'option_type' => $p_option_type));
                        }

                    }

                    result_echo_json(get_status_code('success'), lang('site_insert_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_insert_fail'), true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        foreach( array_keys($set_rules_array) as $item ) {
            if( form_error($item) ) {
                if( preg_match("/(\[|\])/", $item) ) {
                    $key_array = explode("[", $item);
                    $key = $key_array[0];
                }
                else {
                    $key = $item;
                }
                $form_error_array[$key] = strip_tags(form_error($item));
            }
        }//end of foreach()

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of product_insert_proc()

    /**
     * 상품 상세
     */
    public function product_detail() {
        //request
        $req['p_num'] = $this->input->post_get('p_num', true);
        $req['pop'] = $this->input->post_get('pop', true);


        //row
        $product_row = $this->product_model->get_product_row($req['p_num']);

        if( empty($product_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $product_row['p_display_info_array'] = json_decode($product_row['p_display_info'],true);
        $product_row['p_detail_add_array'] = json_decode($product_row['p_detail_add'], true);

        //var_dump($product_row->p_display_info_array->{"today"});
        //$product_row->p_display_info_array = json_decode($product_row->p_display_info, true);

        $product_detail_image_json_array = json_decode($product_row['p_detail_image'], true);
        $product_detail_image_array = ( !empty($product_detail_image_json_array) ) ? $product_detail_image_json_array : array();
        //var_dump($product_detail_image_array);

        //MD 검색
        $query_data = array();
        $query_data['where']['pnum'] = $req['p_num'];
        $product_md_list = $this->product_md_model->get_product_md_list($query_data);

        $product_md_division_array = array();
        foreach( $product_md_list as $key => $row ) {
            $product_md_division_array[$row->pmd_division] = 'Y';
        }

        if( !empty($req['pop']) ) {
            $this->_header(true);
        }
        else {
            $this->_header();
        }

        $this->load->view("/product/product_detail", array(
            "req"           => $req,
            "product_row"  => $product_row,
            "product_detail_image_array"  => $product_detail_image_array,
            //"product_md_list"  => $product_md_list,
            "product_md_division_array"  => $product_md_division_array,
            "list_url"      => $this->_get_list_url()
        ));

        if( !empty($req['pop']) ) {
            $this->_footer(true);
        }
        else {
            $this->_footer();
        }
    }//end of product_update()

    /**
     * 상품 수정
     */
    public function product_update() {
        //request
        $req['p_num'] = $this->input->post_get('p_num', true);



        //row
        $product_row = $this->product_model->get_product_row($req['p_num']);

        if( empty($product_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $product_row['p_display_info_array'] = json_decode($product_row['p_display_info'], true);
        $product_row['p_detail_add_array'] = json_decode($product_row['p_detail_add'], true);

        $product_detail_image_json_array = json_decode($product_row['p_detail_image'], true);
        $product_detail_image_array = ( !empty($product_detail_image_json_array) ) ? $product_detail_image_json_array : array();
        //var_dump($product_detail_image_array);

        //MD 검색
        $query_data = array();
        $query_data['where']['pnum'] = $req['p_num'];
        $product_md_list = $this->product_md_model->get_product_md_list($query_data);

        $product_md_division_array = array();
        foreach( $product_md_list as $key => $row ) {
            $product_md_division_array[$row['pmd_division']] = 'Y';
        }

        //var_dump($product_md_list);
        //var_dump($product_md_division_array);

        $this->load->model('category_md_model');
        $aCategoryTmpLists = $this->category_md_model->get_category_md_list();
        $aCategoryLists = array();

        foreach ($aCategoryTmpLists as $r) {
            $aCategoryLists[$r->cmd_name] = $r->cmd_name;
        }

        $this->_header();

        $file = '/product/product_update';
        if($this->input->get('dev') == 'Y'){
            $file = '/product/product_update_zs';
        }

        $this->load->view($file, array(
            "product_row"  => $product_row,
            "product_detail_image_array"  => $product_detail_image_array,
            //"product_md_list"  => $product_md_list,
            "product_md_division_array"  => $product_md_division_array,
            "list_url"      => $this->_get_list_url(),
            'aCategoryLists'    => $aCategoryLists,
            'opt_token' => create_session_id()

        ));

        $this->_footer();

    }//end of product_update()

    /**
     * 상품 수정 처리 (Ajax)
     *
     * @add 각 쇼핑앱에 데이터를 동기화하기때문에 field가 추가 및 upsert작업시
     *       - 각 쇼핑앱에 field 동기화
     *       - 또는 추가 필드 unset 처리
     */
    public function product_update_proc() {

        ajax_request_check();

        //reqeust
        $req['p_num'] = $this->input->post_get('p_num', true);

        //상품 정보
        $product_row = $this->product_model->get_product_row($req['p_num']);

        if( empty($product_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        $product_detail_image_array = json_decode($product_row['p_detail_image'], true);
        if( empty($product_detail_image_array) ) {
            $product_detail_image_array = array();
        }


        $product_rep_image_add_array = json_decode($product_row['p_rep_image_add'], true);
        if( empty($product_rep_image_add_array) ) {
            $product_rep_image_add_array = array();
        }

        $this->load->library('form_validation');

        //set rules
        $p_termlimit_datetime1_set_rules = $this->default_set_rules;
        $p_termlimit_datetime1_hour_set_rules = $this->default_set_rules;
        $p_termlimit_datetime2_set_rules = $this->default_set_rules;
        $p_termlimit_datetime2_hour_set_rules = $this->default_set_rules;
        if( $this->input->post('p_termlimit_yn', true) == 'Y' ) {
            $p_termlimit_datetime1_set_rules .= "|required";
            $p_termlimit_datetime1_hour_set_rules .= "|required";
            $p_termlimit_datetime2_set_rules .= "|required";
            $p_termlimit_datetime2_hour_set_rules .= "|required";
        }
        $p_wish_raise_count_set_rules = $this->default_set_rules . "|is_natural";
        if( $this->input->post('p_wish_raise_yn', true) == 'Y' ) {
            $p_wish_raise_count_set_rules .= "|required";
        }
        $p_share_raise_count_set_rules = $this->default_set_rules . "|is_natural";
        if( $this->input->post('p_share_raise_yn', true) == 'Y' ) {
            $p_share_raise_count_set_rules .= "|required";
        }

        $p_rep_image_set_rules = $this->default_set_rules;
        if( empty($product_row['p_rep_image']) && (!isset($_FILES['p_rep_image']['name']) || empty($_FILES['p_rep_image']['name'])) ) {
            $p_rep_image_set_rules .= "|required";
        }

        $p_today_image_set_rules = $this->default_set_rules;
        if( empty($product_row['p_today_image']) && (!isset($_FILES['p_today_image']['name']) || empty($_FILES['p_today_image']['name'])) ) {
            $p_today_image_set_rules .= "|required";
        }


        //폼검증 룰 설정
        $set_rules_array = array(
            "p_num" => array("field" => "p_num", "label" => "번호", "rules" => "required|is_natural|".$this->default_set_rules),
            //"p_category" => array("field" => "p_category", "label" => "카테고리", "rules" => "required|in_list[".get_config_item_keys_string("product_category")."]|".$this->default_set_rules),
            "p_termlimit_yn" => array("field" => "p_termlimit_yn", "label" => "기간한정사용여부", "rules" => "required|in_list[".get_config_item_keys_string("product_termlimit_yn")."]|".$this->default_set_rules),
            "p_termlimit_datetime1" => array("field" => "p_termlimit_datetime1", "label" => "기간한정 시작날짜", "rules" => $p_termlimit_datetime1_set_rules),
            "p_termlimit_datetime1_hour" => array("field" => "p_termlimit_datetime1_hour", "label" => "기간한정 시작시각", "rules" => $p_termlimit_datetime1_hour_set_rules),
            "p_termlimit_datetime2" => array("field" => "p_termlimit_datetime2", "label" => "기간한정 종료날짜", "rules" => $p_termlimit_datetime2_set_rules),
            "p_termlimit_datetime2_hour" => array("field" => "p_termlimit_datetime2_hour", "label" => "기간한정 종료시각", "rules" => $p_termlimit_datetime2_hour_set_rules),
            "p_display_state" => array("field" => "p_display_state", "label" => "진열상태", "rules" => "required|in_list[".get_config_item_keys_string("product_display_state")."]|".$this->default_set_rules),
            "p_sale_state" => array("field" => "p_sale_state", "label" => "판매상태", "rules" => "required|in_list[".get_config_item_keys_string("product_sale_state")."]|".$this->default_set_rules),
            "p_stock_state" => array("field" => "p_stock_state", "label" => "재고상태", "rules" => "required|in_list[".get_config_item_keys_string("product_stock_state")."]|".$this->default_set_rules),
            "p_display_info" => array("field" => "p_display_info[]", "label" => "아이콘 1차", "rules" => $this->default_set_rules),
            "p_display_info_4" => array("field" => "p_display_info_4[]", "label" => "아이콘 2차(추가)", "rules" => $this->default_set_rules),
            "pmd_division" => array("field" => "pmd_division[]", "label" => "추가노출", "rules" => $this->default_set_rules),
            "p_wish_count" => array("field" => "p_wish_count", "label" => "관심수", "rules" => "is_natural|".$this->default_set_rules),
            "p_wish_raise_yn" => array("field" => "p_wish_raise_yn", "label" => "관심증가수 사용여부", "rules" => "in_list[".get_config_item_keys_string("product_wish_raise_yn")."]|".$this->default_set_rules),
            "p_wish_raise_count" => array("field" => "p_wish_raise_count", "label" => "관심증가수", "rules" => $p_wish_raise_count_set_rules),
            "p_share_count" => array("field" => "p_share_count", "label" => "공유수", "rules" => "is_natural|".$this->default_set_rules),
            "p_share_raise_yn" => array("field" => "p_share_raise_yn", "label" => "공유증가수 사용여부", "rules" => "trim|xss_clean|in_list[".get_config_item_keys_string("product_share_raise_yn")."]|prep_for_form|strip_tags"),
            "p_share_raise_count" => array("field" => "p_share_raise_count", "label" => "공유증가수", "rules" => $p_share_raise_count_set_rules),
            "p_hash" => array("field" => "p_hash", "label" => "해시태그", "rules" => $this->default_set_rules),
            "p_name" => array("field" => "p_name", "label" => "상품명", "rules" => "required|".$this->default_set_rules),
            "p_summary" => array("field" => "p_summary", "label" => "간략설명", "rules" => $this->default_set_rules),
            "p_detail" => array("field" => "p_detail", "label" => "상세설명", "rules" => "trim"),
            //"p_order_link_protocol" => array("field" => "p_order_link_protocol", "label" => "주문링크 프로토콜", "rules" => "required|in_list[http://,https://]|".$this->default_set_rules),
            //"p_order_link" => array("field" => "p_order_link", "label" => "주문링크", "rules" => "required|".$this->default_set_rules),
            "p_order_code" => array("field" => "p_order_code", "label" => "공구폼상품코드", "rules" => "required|".$this->default_set_rules),
            "p_supply_price" => array("field" => "p_supply_price", "label" => "공급가격(원가)", "rules" => "required|".$this->default_set_rules),
            "p_discount_rate" => array("field" => "p_discount_rate", "label" => "할인율", "rules" => $this->default_set_rules),
            "p_original_price" => array("field" => "p_original_price", "label" => "기존가격", "rules" => $this->default_set_rules),
            "p_margin_price" => array("field" => "p_margin_price", "label" => "판매마진", "rules" => $this->default_set_rules),
            "p_sale_price" => array("field" => "p_sale_price", "label" => "판매가격", "rules" => "required|".$this->default_set_rules),
            "p_margin_rate" => array("field" => "p_margin_rate", "label" => "마진율", "rules" => $this->default_set_rules),
            "p_taxation" => array("field" => "p_taxation", "label" => "과세여부", "rules" => "required|in_list[".get_config_item_keys_string("product_taxation")."]|".$this->default_set_rules),
            "p_origin" => array("field" => "p_origin", "label" => "원산지", "rules" => $this->default_set_rules),
            "p_manufacturer" => array("field" => "p_manufacturer", "label" => "제조사", "rules" => $this->default_set_rules),
            "p_supplier" => array("field" => "p_supplier", "label" => "공급사", "rules" => $this->default_set_rules),
            "p_deliveryprice_type" => array("field" => "p_deliveryprice_type", "label" => "배송비설정", "rules" => "required|in_list[".get_config_item_keys_string("product_deliveryprice_type")."]|".$this->default_set_rules),
            "p_deliveryprice" => array("field" => "p_deliveryprice", "label" => "배송비", "rules" => "is_natural|".$this->default_set_rules),
            "p_rep_image" => array("field" => "p_rep_image", "label" => "대표이미지", "rules" => $p_rep_image_set_rules),
            "p_today_image" => array("field" => "p_today_image", "label" => "오늘추천이미지", "rules" => $p_today_image_set_rules),
            "p_banner_image" => array("field" => "p_banner_image", "label" => "배너이미지", "rules" => $this->default_set_rules),
            "p_detail_image" => array("field" => "p_detail_image[]", "label" => "상세이미지", "rules" => $this->default_set_rules),
            "detail_add_weight" => array("field" => "detail_add_weight", "label" => "용량/중량", "rules" => $this->default_set_rules),
            "detail_add_delivery_info" => array("field" => "detail_add_delivery_info", "label" => "배송정보", "rules" => $this->default_set_rules),
            "detail_add_deliveryprice_text" => array("field" => "detail_add_deliveryprice_text", "label" => "배송비설명", "rules" => $this->default_set_rules),
            "p_option_buy_cnt_view" => array("field" => "p_option_buy_cnt_view", "label" => "옵션구매카운트", "rules" => "in_list[".get_config_item_keys_string("product_option_buy_cnt_view")."]|".$this->default_set_rules),

            // 190412 황기석 새 필드 추가
            //"p_outside_display_able" => array("field" => "p_outside_display_able", "label" => "노출여부", "rules" => "required|in_list[".get_config_item_keys_string("product_outside_display_able")."]|".$this->default_set_rules),
            // 190703 황기석 새 필드 추가
//            "p_info_tab_view" => array("field" => "p_info_tab_view", "label" => "상품정보제공공시 노출여부", "rules" => "required|in_list[".get_config_item_keys_string("product_info_tab_view")."]|".$this->default_set_rules),

            "p_option_use" => array("field" => "p_option_use", "label" => "옵션여부", "rules" => $this->default_set_rules),
            "p_option_type" => array("field" => "p_option_type", "label" => "옵션타입", "rules" => $this->default_set_rules),
            "p_option_depth" => array("field" => "p_option_depth", "label" => "옵션차수", "rules" => $this->default_set_rules)

        );


        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $p_num = $this->input->post('p_num', true);

            $p_cate1_tmp = $this->input->post('p_cate1', true);

            if(is_array($p_cate1_tmp) == true) $p_cate1 = implode(',',$p_cate1_tmp);
            else $p_cate1 = $p_cate1_tmp;

            $p_termlimit_yn = $this->input->post('p_termlimit_yn', true);
            $p_termlimit_datetime1 = number_only($this->input->post('p_termlimit_datetime1', true));
            $p_termlimit_datetime1_hour = number_only($this->input->post('p_termlimit_datetime1_hour', true));
            $p_termlimit_datetime1_min = number_only($this->input->post('p_termlimit_datetime1_min', true));
            if( !empty($p_termlimit_datetime1) ) {
                $p_termlimit_datetime1 = $p_termlimit_datetime1.$p_termlimit_datetime1_hour.$p_termlimit_datetime1_min."00";
            }
            $p_termlimit_datetime2 = number_only($this->input->post('p_termlimit_datetime2', true));
            $p_termlimit_datetime2_hour = number_only($this->input->post('p_termlimit_datetime2_hour', true));
            $p_termlimit_datetime2_min = number_only($this->input->post('p_termlimit_datetime2_min', true));
            if( !empty($p_termlimit_datetime2) ) {
                $p_termlimit_datetime2 = $p_termlimit_datetime2.$p_termlimit_datetime2_hour.$p_termlimit_datetime2_min."59";
            }
            $p_display_state = $this->input->post('p_display_state', true);
            $p_sale_state = $this->input->post('p_sale_state', true);
            $p_stock_state = $this->input->post('p_stock_state', true);
            $p_display_info = $this->input->post('p_display_info', true);
            $p_display_info_4 = $this->input->post('p_display_info_4', true);   //배열

            if( !empty($p_display_info) || !empty($p_display_info_4) ) {
                $p_display_info_array = array();

                if( is_array($p_display_info) ) {
                    foreach( $p_display_info as $item ) {
                        $p_display_info_array[$item] = 'Y';
                    }
                }
                else {
                    $p_display_info_array[$p_display_info] = 'Y';
                }

                if( is_array($p_display_info_4) ) {
                    foreach( $p_display_info_4 as $item ) {
                        $p_display_info_array[$item] = 'Y';
                    }
                }
                else {
                    $p_display_info_array[$p_display_info_4] = 'Y';
                }

                $p_display_info = json_encode_no_slashes($p_display_info_array);
            }
            else {
                $p_display_info = "";
            }
            $pmd_division = $this->input->post('pmd_division', true);               //배열
            $p_wish_count = $this->input->post('p_wish_count', true);
            $p_wish_raise_yn = $this->input->post('p_wish_raise_yn', true);
            $p_wish_raise_count = $this->input->post('p_wish_raise_count', true);
            $p_share_count = $this->input->post('p_share_count', true);
            $p_share_raise_yn = $this->input->post('p_share_raise_yn', true);
            $p_share_raise_count = $this->input->post('p_share_raise_count', true);
            $p_hash = $this->input->post('p_hash', true);
            $p_name = $this->input->post('p_name', true);
            $p_summary = $this->input->post('p_summary', true);
            $p_detail = $this->input->post('p_detail');     //HTML 태그 사용
            //$p_order_link_protocol = $this->input->post('p_order_link_protocol', true);
            //$p_order_link = $p_order_link_protocol . str_replace(array("http://", "https://"), array("", ""), $this->input->post('p_order_link', true));
            $p_order_code = $this->input->post('p_order_code', true);
            $p_supply_price = number_only(delComma($this->input->post('p_supply_price', true)));
            $p_discount_rate = $this->input->post('p_discount_rate', true);
            $p_original_price = number_only(delComma($this->input->post('p_original_price', true)));
            $p_margin_price = $this->input->post('p_margin_price', true);
            $p_sale_price = number_only(delComma($this->input->post('p_sale_price', true)));
            $p_margin_rate = $this->input->post('p_margin_rate', true);
            $p_taxation = $this->input->post('p_taxation', true);
            $p_origin = $this->input->post('p_origin', true);
            $p_manufacturer = $this->input->post('p_manufacturer', true);
            $p_supplier = $this->input->post('p_supplier', true);
            $p_deliveryprice_type = $this->input->post('p_deliveryprice_type', true);
            $p_deliveryprice = $this->input->post('p_deliveryprice', true);
            //if( $p_deliveryprice_type == '2' ) {
            //    $p_deliveryprice = 0;
            //}
            //else {
            //    $p_deliveryprice = $this->input->post('p_deliveryprice', true);
            //}
            $p_name_b   = $this->input->post('p_name_b', true);
            $p_detail_b = $this->input->post('p_detail_b', true);



            $p_top_desc = $this->input->post('p_top_desc', true);
            $p_top_desc = $this->input->post('p_top_desc', true);

            $detail_add_weight = $this->input->post('detail_add_weight', true);
            $detail_add_delivery_info = $this->input->post('detail_add_delivery_info', true);
            $detail_add_deliveryprice_text = $this->input->post('detail_add_deliveryprice_text', true);

            //190412 황기석 새필드 추가
            $p_outside_display_able = $this->input->post('p_outside_display_able', true);
            //190703 황기석 새필드 추가
//            $p_info_tab_view = $this->input->post('p_info_tab_view', true);

            $p_detail_add = "";
            $p_detail_add_array = array();
            foreach ($this->config->item('product_detail_add') as $key => $item) {
                $p_detail_add_array[$key] = ${"detail_add_" . $key};
            }
            if( !empty($p_detail_add_array) ) {
                $p_detail_add = json_encode_no_slashes($p_detail_add_array);
            }

            $p_option_buy_cnt_view = $this->input->post('p_option_buy_cnt_view', true);

            $p_option_use = $this->input->post('p_option_use', true);
            $p_option_type = $this->input->post('p_option_type', true);
            $p_option_depth = $this->input->post('p_option_depth', true);


            //할인율, 마진, 마진율 계산
            $p_discount_rate = "0.00";
            $p_margin_price = "0";
            $p_margin_rate = "0.00";
            if( $p_original_price && $p_sale_price ) {
                $p_discount_rate = number_format((($p_original_price - $p_sale_price) / $p_original_price) * 100, 2);
            }
            if( $p_supply_price && $p_sale_price ) {
                $p_margin_price = $p_sale_price - $p_supply_price;
            }
            if( $p_supply_price && $p_sale_price ) {
                $p_margin_rate = number_format((($p_sale_price - $p_supply_price) / $p_sale_price ) * 100, 2);
            }

            $p_today_image = "";
            $p_banner_image = "";
            $p_detail_image = "";
            $p_rep_image_add = "";

            $p_image_path_web = $this->config->item('product_image_path_web') . "/" . substr($p_order_code, 0, 2) . "/" . $p_order_code;
            $p_image_path = DOCROOT . $p_image_path_web;
            create_directory($p_image_path);

            $config = array();
            $config['upload_path'] = $p_image_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size']	= $this->config->item('upload_total_max_size');
            $config['file_ext_tolower'] = true;                 //확장자 소문자
            $config['overwrite'] = true;                        //덮어쓰기
            $config['file_name'] = $p_order_code . "_rep";      //파일명
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            //대표이미지 수정 업로드 (썸네일 생성)
            if( isset($_FILES['p_rep_image']['name']) && !empty($_FILES['p_rep_image']['name']) ) {
                if ( $this->upload->do_upload('p_rep_image') ) {
                    $p_rep_image_data_array = $this->upload->data();
                    @chmod($p_rep_image_data_array['full_path'], 0664);

                    //움직이는 gif일때 썸네일 생성하지 않음.(181019/김홍주)
                    if( is_ani_pic($p_rep_image_data_array['full_path']) ) {
                        $file_name = $p_image_path_web . "/" . $p_rep_image_data_array['raw_name'] . $p_rep_image_data_array['file_ext'];
                        $p_rep_image_array = array('1' => $file_name, '0' => $file_name);
                    }
                    else {
                        $p_rep_image_array = create_thumb_image($p_rep_image_data_array, $p_image_path_web, $this->config->item('product_rep_image_size'), true);
                    }

                    if ( empty($p_rep_image_array) || $p_rep_image_array === false ) {
                        $form_error_array['p_rep_image'] = "대표이미지 썸네일 생성을 실패했습니다.";
                    }
                    else {
                        //$p_rep_image_array[0] = $p_image_path_web . "/" . $p_rep_image_data_array['file_name'];   //원본유지일때
                        $p_rep_image_array[0] = $p_rep_image_array[1]; //원본삭제일때
                        $p_rep_image = json_encode_no_slashes($p_rep_image_array);
                    }
                }
                else {
                    $form_error_array['p_rep_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if( p_rep_image )

            $rep_image_add_count = count($product_rep_image_add_array);
            //대표이미지 추가 수정 업로드 (썸네일 생성)
            if( isset($_FILES['p_rep_image_add']['name']) && !empty($_FILES['p_rep_image_add']['name']) ) {
                $p_rep_image_add_data_array = array();
                $p_rep_image_add_array = array();

                foreach( $_FILES['p_rep_image_add']['name'] as $key => $name ) {
                    $no = $key + 1;

                    //var_dump($_FILES['p_detail_image']['name'][$key]);

                    //기본 파일 배열 설정 (CI에서 사용)
                    $_FILES['userfile']['name'] = $_FILES['p_rep_image_add']['name'][$key];
                    $_FILES['userfile']['type'] = $_FILES['p_rep_image_add']['type'][$key];
                    $_FILES['userfile']['tmp_name'] = $_FILES['p_rep_image_add']['tmp_name'][$key];
                    $_FILES['userfile']['error'] = $_FILES['p_rep_image_add']['error'][$key];
                    $_FILES['userfile']['size'] = $_FILES['p_rep_image_add']['size'][$key];

                    $rep_image_add_count = $rep_image_add_count + 1;
                    $config['file_name'] = $p_order_code . "_detail_add_" . $rep_image_add_count;      //파일명

                    $this->upload->initialize($config);

                    if( $this->upload->do_upload() ){
                        //var_dump("ok =====> ", $key);

                        $p_rep_image_add_data_array[$key] = $this->upload->data();


                        //움직이는 gif일때 썸네일 생성하지 않음.(181019/김홍주)
                        if( is_ani_pic($p_rep_image_add_data_array['full_path']) ) {
                            $file_name = $p_image_path_web . "/" . $p_rep_image_add_data_array['raw_name'] . $p_rep_image_add_data_array['file_ext'];
                            $p_rep_image_add_array[$key] = array('1' => $file_name, '0' => $file_name);
                        }
                        else {
                            $p_rep_image_add_array[$key] = create_thumb_image($p_rep_image_add_data_array[$key], $p_image_path_web, $this->config->item('product_detail_image_size'), true);
                        }


                        //$p_rep_image_add_array[$key] = create_thumb_image($p_rep_image_add_data_array[$key], $p_image_path_web, $this->config->item('product_detail_image_size'), true);
                        @chmod($p_rep_image_add_data_array[$key]['full_path'], 0664);

                        //var_dump($p_detail_image_data_array[$key]);
                        //var_dump($p_detail_image_array[$key]);

                        if( empty($p_rep_image_add_array[$key]) || $p_rep_image_add_array[$key] === false ) {
                            $form_error_array['p_rep_image_add'] = "[".$no."] 추가 대표이미지 썸네일 생성을 실패했습니다.";
                        }
                        //업로드 성공시
                        else {
                            //$p_detail_image_array[$key][0] = $p_image_path_web . "/" . $p_detail_image_data_array[$key]['file_name']; //원본유지일때
                            $p_rep_image_add_array[$key][0] = $p_rep_image_add_array[$key][1];    //원본삭제일때
                            //기존 파일 삭제 (파일명이 고정이기 때문에 기존 파일 삭제하면 안 됨)
                            //file_delete(2, $product_detail_image_array[$key], DOCROOT);
                            array_push($product_rep_image_add_array, $p_rep_image_add_array[$key]);

                        }
                    }
                    else {
                        $form_error_array['p_rep_image_add'] = "[".$no."] ".strip_tags($this->upload->display_errors());
                    }
                }//end of foreach()

                $p_rep_image_add = json_encode_no_slashes($product_rep_image_add_array);
            }//end of if()

            //상세이미지 사용을 위해 init
            unset($_FILES['userfile']);



            //오늘추천이미지 업로드
            if( isset($_FILES['p_today_image']['name']) && !empty($_FILES['p_today_image']['name']) ) {
                $config['file_name'] = $p_order_code . "_today";      //파일명
                $this->upload->initialize($config);

                if( $this->upload->do_upload('p_today_image') ){
                    $upload_data_array = $this->upload->data();
                    $p_today_image = $p_image_path_web . "/" . $upload_data_array['file_name'];
                    @chmod($upload_data_array['full_path'], 0664);

                    //기존 파일 삭제 (파일명이 고정이기 때문에 기존 파일 삭제하면 안 됨)
                    //file_delete(1, $product_row->p_today_image, DOCROOT);

                }
                else {
                    $form_error_array['p_today_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if( p_today_image )

            //배너이미지 업로드
            if( isset($_FILES['p_banner_image']['name']) && !empty($_FILES['p_banner_image']['name']) ) {
                $config['file_name'] = $p_order_code . "_banner";      //파일명
                $this->upload->initialize($config);

                if( $this->upload->do_upload('p_banner_image') ){
                    $upload_data_array = $this->upload->data();
                    $p_banner_image = $p_image_path_web . "/" . $upload_data_array['file_name'];

                    //기존 파일 삭제 (파일명이 고정이기 때문에 기존 파일 삭제하면 안 됨)
                    //file_delete(1, $product_row->p_banner_image, DOCROOT);

                }
                else {
                    $form_error_array['p_banner_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }//end of if( p_banner_image )


            $detail_img_count = count($product_detail_image_array);

            //상세이미지 수정 업로드 (썸네일 생성)
            if( isset($_FILES['p_detail_image']['name']) && !empty($_FILES['p_detail_image']['name']) ) {
                $p_detail_image_data_array = array();
                $p_detail_image_array = array();

                foreach( $_FILES['p_detail_image']['name'] as $key => $name ) {
                    $no = $key + 1;

                    //var_dump($_FILES['p_detail_image']['name'][$key]);

                    //기본 파일 배열 설정 (CI에서 사용)
                    $_FILES['userfile']['name'] = $_FILES['p_detail_image']['name'][$key];
                    $_FILES['userfile']['type'] = $_FILES['p_detail_image']['type'][$key];
                    $_FILES['userfile']['tmp_name'] = $_FILES['p_detail_image']['tmp_name'][$key];
                    $_FILES['userfile']['error'] = $_FILES['p_detail_image']['error'][$key];
                    $_FILES['userfile']['size'] = $_FILES['p_detail_image']['size'][$key];

                    $detail_img_count = $detail_img_count + 1;
                    $config['file_name'] = $p_order_code . "_detail_" . $detail_img_count;      //파일명

                    $this->upload->initialize($config);

                    if( $this->upload->do_upload() ){

                        $p_detail_image_data_array[$key] = $this->upload->data();
                        $p_detail_image_array[$key] = create_thumb_image($p_detail_image_data_array[$key], $p_image_path_web, $this->config->item('product_detail_image_size'), true);
                        @chmod($p_detail_image_data_array[$key]['full_path'], 0664);

                        if( empty($p_detail_image_array[$key]) || $p_detail_image_array[$key] === false ) {
                            $form_error_array['p_detail_image'] = "[".$no."] 상세이미지 썸네일 생성을 실패했습니다.";
                        }
                        //업로드 성공시
                        else {
                            $p_detail_image_array[$key][0] = $p_detail_image_array[$key][1];    //원본삭제일때
                            array_push($product_detail_image_array, $p_detail_image_array[$key]);
                        }
                    }
                    else {
                        $form_error_array['p_detail_image'] = "[".$no."] ".strip_tags($this->upload->display_errors());
                    }
                }//end of foreach()

                $p_detail_image = json_encode_no_slashes($product_detail_image_array);
            }//end of if()

            $link = urlencode($this->config->item("site_http") . "/product/detail/" . $product_row['p_num']);
            $ios_add = "&isi=".$this->config->item("ios_link_key")."&ibi=".$this->config->item("app_id");
            $ios_add_web = "&ibi=".$this->config->item("app_id");

            $p_short_url = "";
            if( empty($product_row['p_short_url']) ) {
                $p_short_url = get_short_url($this->config->item("site_http") . "/product/detail/" . $product_row['p_num']);
            }
            $p_app_link_url = "";
            if( empty($product_row['p_app_link_url']) ) {
                $p_app_link_url = get_short_url($this->config->item('dynamic_link_http') . "/?link=" . $link . "&apn=" . $this->config->item("app_id") . "&amv=" . $this->config->item("check_app_versioncode_link").$ios_add);
            }
            $p_app_link_url_2 = "";
            if( empty($product_row['p_app_link_url_2']) ) {
                $p_app_link_url_2 = get_short_url($this->config->item('dynamic_link_http') . "/?link=" . $link . "&apn=" . $this->config->item("app_id") . "&amv=" . $this->config->item("check_app_versioncode_link") . "&afl=" . $link.$ios_add_web);
            }

            if( empty($form_error_array) ) {
                //상품카테고리가 설정되지 않았으면
                $p_cate1 = $p_cate1 ? $p_cate1 : '';
                $p_cate2 = $p_cate2 ? $p_cate2 : '';
                $p_cate3 = $p_cate3 ? $p_cate3 : '';

                /*3차판매가*/
                $p_price_third_yn = (isset($cate_info['p_price_third_yn']) && !empty($cate_info['p_price_third_yn'])) ? $cate_info['p_price_third_yn'] : "N";
                $p_price_third = (isset($cate_info['p_price_third']) && !empty($cate_info['p_price_third'])) ? $cate_info['p_price_third'] : "0";

                /*2차판매가*/
                $p_price_second_yn = (isset($cate_info['p_price_second_yn']) && !empty($cate_info['p_price_second_yn'])) ? $cate_info['p_price_second_yn'] : "N";
                $p_price_second = (isset($cate_info['p_price_second']) && !empty($cate_info['p_price_second'])) ? $cate_info['p_price_second'] : "0";

                //수정
                $query_data = array();

                $query_data['p_price_third_yn'] = $p_price_third_yn;
                $query_data['p_price_third'] = $p_price_third;
                $query_data['p_price_second_yn'] = $p_price_second_yn;
                $query_data['p_price_second'] = $p_price_second;

//                $query_data['p_category'] = $p_category;

                //if( empty($product_row->p_cate1) ) {
                    $query_data['p_cate1'] = $p_cate1;
                    $query_data['p_cate2'] = $p_cate2;
                    $query_data['p_cate3'] = $p_cate3;
                //}
                $query_data['p_hash'] = str_replace(array("\'", "\""), array("´", "˝"), $p_hash);
                $query_data['p_name'] = str_replace(array("\'", "\""), array("´", "˝"), $p_name);
                $query_data['p_summary'] = $p_summary;
                $query_data['p_detail'] = $p_detail;
                $query_data['p_detail_add'] = $p_detail_add;
                if( !empty($p_rep_image) ) {
                    $query_data['p_rep_image'] = $p_rep_image;
                }
                if( !empty($p_today_image) ) {
                    $query_data['p_today_image'] = $p_today_image;
                }
                if( !empty($p_banner_image) ) {
                    $query_data['p_banner_image'] = $p_banner_image;
                }
                if( !empty($p_detail_image) ) {
                    $query_data['p_detail_image'] = $p_detail_image;
                }

                /**
                 * @date 181115
                 * @modify 황기석
                 * @desc 상품대표이미지 slide를 위한 추가 대표이미지 필드
                 */
                if( !empty($p_rep_image_add) ) {
                    $query_data['p_rep_image_add'] = $p_rep_image_add;
                }

                //$query_data['p_order_link'] = $p_order_link;
                $query_data['p_order_code'] = $p_order_code;
                if( !empty($p_short_url) ) {
                    $query_data['p_short_url'] = $p_short_url;
                }
                if( !empty($p_app_link_url) ) {
                    $query_data['p_app_link_url'] = $p_app_link_url;
                }
                if( !empty($p_app_link_url_2) ) {
                    $query_data['p_app_link_url_2'] = $p_app_link_url_2;
                }
                $query_data['p_supply_price'] = number_only($p_supply_price, true);
                $query_data['p_original_price'] = number_only($p_original_price, true);
                $query_data['p_sale_price'] = number_only($p_sale_price, true);
                $query_data['p_margin_price'] = number_only($p_margin_price, true);
                $query_data['p_discount_rate'] = $p_discount_rate;
                $query_data['p_margin_rate'] = $p_margin_rate;
                $query_data['p_taxation'] = $p_taxation;
                $query_data['p_origin'] = $p_origin;
                $query_data['p_manufacturer'] = $p_manufacturer;
                $query_data['p_supplier'] = $p_supplier;
                $query_data['p_deliveryprice_type'] = $p_deliveryprice_type;
                $query_data['p_deliveryprice'] = number_only($p_deliveryprice, true);
                $query_data['p_wish_count'] = number_only($p_wish_count, true);
                $query_data['p_wish_raise_yn'] = get_yn_value($p_wish_raise_yn);
                $query_data['p_wish_raise_count'] = number_only($p_wish_raise_count, true);
                $query_data['p_share_count'] = number_only($p_share_count, true);
                $query_data['p_share_raise_yn'] = get_yn_value($p_share_raise_yn);
                $query_data['p_share_raise_count'] = number_only($p_share_raise_count, true);
                $query_data['p_termlimit_yn'] = get_yn_value($p_termlimit_yn);
                $query_data['p_termlimit_datetime1'] = $p_termlimit_datetime1;
                $query_data['p_termlimit_datetime2'] = $p_termlimit_datetime2;
                $query_data['p_display_info'] = $p_display_info;
                $query_data['p_display_state'] = get_yn_value($p_display_state);
                $query_data['p_sale_state'] = get_yn_value($p_sale_state);
                $query_data['p_stock_state'] = get_yn_value($p_stock_state);
                $query_data['p_option_buy_cnt_view'] = get_yn_value($p_option_buy_cnt_view);
                $query_data['p_outside_display_able'] = $p_outside_display_able;

                $query_data['p_mod_id'] = $_SESSION['session_au_num'];



                if(empty($p_option_use) == false ) $query_data['p_option_use'] = $p_option_use;
                if(empty($p_option_type) == false ) $query_data['p_option_type'] = $p_option_type;
                if(empty($p_option_depth) == false ) $query_data['p_option_depth'] = $p_option_depth;

                if( $this->product_model->update_product($p_num, $query_data) ) {

                    //상품 MD 초기화
                    $this->product_md_model->delete_product_md_in_product($p_num);

                    if( !empty($pmd_division) ) {
                        foreach($pmd_division as $item) {
                            $query_data = array();
                            $query_data['pmd_division'] = $item;
                            $query_data['pmd_product_num'] = $p_num;
                            $this->product_md_model->insert_product_md($query_data);
                        }//end of foreach()
                    }

                    //진열안함 -> 진열함으로 변경시 예전댓글복구함. (2018-04-25)
                    if( $product_row['p_display_state'] == 'N' && get_yn_value($p_display_state) == "Y" ) {
                        $this->product_model->product_old_comment_restore($product_row['p_num']);
                    }

                    result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_update_fail').'//', true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        foreach( array_keys($set_rules_array) as $item ) {
            if( form_error($item) ) {
                if( preg_match("/(\[|\])/", $item) ) {
                    $key_array = explode("[", $item);
                    $key = $key_array[0];
                }
                else {
                    $key = $item;
                }
                $form_error_array[$key] = strip_tags(form_error($item));
            }
        }//end of foreach()

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of product_update_proc()

    /**
     * 상품 수정 토글
     */
    function product_update_toggle() {
        ajax_request_check();

        //request
        $req['p_num'] = trim($this->input->post_get('p_num', true));
        $req['fd'] = trim($this->input->post_get('fd', true));          //p_display_state, p_sale_state


        //수정 가능 필드
        $allow_fds = array('p_display_state', 'p_sale_state', 'p_stock_state');

        if( !in_array($req['fd'], $allow_fds) ) {
            result_echo_json(get_status_code('error'), "", true, 'alert');
        }

        $product_row = $this->product_model->get_product_row($req['p_num']);

        if( empty($product_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        $query_data = array();
        if( $product_row[$req['fd']] == "Y" ) {
            $query_data[$req['fd']] = "N";
        }
        else {
            $query_data[$req['fd']] = "Y";
        }

        if( $this->product_model->update_product($product_row['p_num'], $query_data) ) {
            result_echo_json(get_status_code('success'), '', true);
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_error_unknown'), true);
        }
    }//end of product_update_toggle()

    /**
     * 상품 예전 댓글 갯수 추출
     */
    public function product_old_comment_count() {
        ajax_request_check();

        //request
        $req['p_num'] = trim($this->input->post_get('p_num', true));

        if( empty($req['p_num']) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_id'), true, 'alert');
        }

        //조회
        $product_row = $this->product_model->get_product_row($req['p_num']);

        if( empty($product_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //예전댓글갯수
        $old_comment_count = $this->product_model->get_product_old_comment_list($product_row['p_num'], true);
        if( empty($old_comment_count) ) {
            result_echo_json(get_status_code('error'), lang('site_no_data'), true);
        }

        $data = array();
        $data['old_comment_count'] = $old_comment_count;
        result_echo_json(get_status_code('success'), '', true, 'alert', '', $data);
    }//end of product_old_comment_count()

    /**
     * 상품 예전 댓글 복구
     */
    public function product_comment_restore() {
        ajax_request_check();

        //request
        $req['p_num'] = trim($this->input->post_get('p_num', true));

        if( empty($req['p_num']) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_id'), true, 'alert');
        }

        //조회
        $product_row = $this->product_model->get_product_row($req['p_num']);

        if( empty($product_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        ////예전댓글갯수
        //$old_commnet_count = $this->product_model->get_product_old_comment_list($product_row->p_num, true);
        //
        //if( empty($old_commnet_count) ) {
        //    result_echo_json(get_status_code('error'), lang('site_no_data'), true);
        //}

        //댓글 복구
        if( $this->product_model->product_old_comment_restore($product_row['p_num']) ) {
            $old_commnet_count = $this->product_model->get_product_old_comment_list($product_row['p_num'], true);

            $data = array();
            $data['old_commnet_count'] = $old_commnet_count;
            result_echo_json(get_status_code('success'), '복구 완료', true, 'alert', '', $data);
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_error_unknown'), true, 'alert');
        }
    }//end of product_comment_restore()

    /**
     * 상품 삭제 처리 (Ajax)
     */
    public function product_delete_proc() {
        ajax_request_check();

        //request
        $req['p_num'] = $this->input->post_get('p_num', true);

        //상품 정보
        $product_row = $this->product_model->get_product_row($req['p_num']);

        if( empty($product_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //상품 삭제
        if( $this->product_model->delete_product($req['p_num']) ) {

            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of product_delete_proc()

    /**
     * 상품 상세 이미지 부분 삭제
     */
    public function product_image_delete_proc() {
        ajax_request_check();

        $this->load->library('form_validation');



        //폼검증 룰 설정
        $set_rules_array = array(
            "p_num" => array("field" => "p_num", "label" => "상품번호", "rules" => "required|is_natural|".$this->default_set_rules),
            "img_div" => array("field" => "img_div", "label" => "이미지구분", "rules" => "in_list[1,2,3,4,5]|".$this->default_set_rules),
            "img_no" => array("field" => "img_no", "label" => "이미지번호", "rules" => "is_natural|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $p_num = $this->input->post_get('p_num', true);
            $img_div = $this->input->post_get('img_div', true); //이미지구분(1=오늘추천이미지, 2=상세이미지)
            $img_no = $this->input->post_get('img_no', true);   //상세이미지번호(0~9)
            $type = $this->input->post_get('type', true)?$this->input->post_get('type', true):'A';   //B || A


            //이미지구분 기본값 : 상세이미지
            if( empty($img_div) ) {
                $img_div = 2;
            }

            //상품 정보
            $product_row = $this->product_model->get_product_row($p_num);

            if( empty($product_row) ) {
                result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
            }

            //오늘추천이미지 삭제
            if( $img_div == 1 ) {
                file_delete(1, $product_row['p_today_image'], DOCROOT);

                $query_data = array();
                $query_data['p_today_image'] = "";
                $this->product_model->update_product($p_num, $query_data);
            }
            //상세이미지 삭제
            else if( $img_div == 2 ) {
                if( $img_no != "" ) {

                    if($type == 'B'){
                        $detail_image_array = json_decode($product_row['p_detail_image_b'], true);
                    }else{
                        $detail_image_array = json_decode($product_row['p_detail_image'], true);
                    }

                    file_delete(2, $detail_image_array[$img_no], DOCROOT);

                    unset($detail_image_array[$img_no]);
                    array_filter($detail_image_array);

                    $new_detail_image_array = array();
                    foreach ($detail_image_array as $key => $item) {
                        if( !empty($item) ) {
                            $new_detail_image_array[] = $item;
                        }
                    }
                    $p_detail_image = json_encode_no_slashes($new_detail_image_array);

                    $query_data = array();
                    if($type == 'B'){
                        $query_data['p_detail_image_b'] = $p_detail_image;
                    }else{
                        $query_data['p_detail_image'] = $p_detail_image;
                    }

                    $this->product_model->update_product($p_num, $query_data);
                }
            }
            //상세이미지 전체 삭제
            else if( $img_div == 3 ) {

                if($type == 'B'){
                    $img_array = json_decode($product_row['p_detail_image_b'], true);   //2차원 배열
                }else{
                    $img_array = json_decode($product_row['p_detail_image'], true);   //2차원 배열
                }


                if( !empty($img_array) ) {
                    foreach ($img_array as $key => $item) {
                        file_delete(2, $item, DOCROOT);
                    }//end of foreach()

                    $query_data = array();
                    if($type == 'B'){
                        $query_data['p_detail_image_b'] = "";
                    }else{
                        $query_data['p_detail_image'] = "";
                    }

                    $this->product_model->update_product($p_num, $query_data);
                }
            }
            //배너이미지 삭제
            else if( $img_div == 4 ) {
                file_delete(1, $product_row['p_banner_image'], DOCROOT);

                $query_data = array();
                $query_data['p_banner_image'] = "";
                $this->product_model->update_product($p_num, $query_data);
            }
            //추기대표이미지 삭제
            else if( $img_div == 5 ) {
                if( $img_no != "" ) {

                    $p_rep_image_add_array = json_decode($product_row['p_rep_image_add'], true);

                    file_delete(2, $p_rep_image_add_array[$img_no], DOCROOT);

                    unset($p_rep_image_add_array[$img_no]);
                    array_filter($p_rep_image_add_array);

                    $new_p_rep_image_add_array = array();
                    foreach ($p_rep_image_add_array as $key => $item) {
                        if( !empty($item) ) {
                            $new_p_rep_image_add_array[] = $item;
                        }
                    }
                    $p_rep_image_add = json_encode_no_slashes($new_p_rep_image_add_array);

                    $query_data = array();
                    $query_data['p_rep_image_add'] = $p_rep_image_add;

                    $this->product_model->update_product($p_num, $query_data);
                }
            }

            result_echo_json(get_status_code('success'), "", true);
        }
        else {
            result_echo_json(get_status_code('error'), strip_tags(validation_errors()), true, 'alert');
        }

        result_echo_json(get_status_code('error'), "", true);
    }//end of product_image_delete_proc()

    /**
     * 상품 노출순서 수정 (Ajax)
     */
    public function product_order_proc() {
        ajax_request_check();

        $data = $this->input->post('data', true);       //배열 ([p_num] => order 형식)

        if( empty($data) ) {
            result_echo_json(get_status_code('error'), lang('site_no_data'), true, 'alert');
        }

        foreach( $data as $key => $value ) {
            $p_num = $key;
            $p_order = $value;

            $this->product_model->order_update_product($p_num, $p_order);
        }//end of foreach()

        result_echo_json(get_status_code('success'), '', true);
    }//end of product_md_order_proc()

    /**
     * 앱 URL 생성 (ajax)
     */
    public function product_app_url() {
        ajax_request_check();

        //request
        $req['p_num'] = trim($this->input->post_get('p_num', true));

        if( empty($req['p_num']) ) {
            page_request_return(get_status_code('error'), lang('site_error_empty_id'), true);
        }

        $product_row = $this->product_model->get_product_row($req['p_num']);

        if( empty($product_row) ) {
            page_request_return(get_status_code('error'), lang('site_error_empty_data'), true);
        }

        $org_url = $this->config->item('site_http') . "/app/?url=" . urlencode($this->config->item('site_http') . "/product/detail/?p_num=" . $req['p_num']);
        //echo $org_url;
        //exit;

        $url = get_short_url($org_url);
        echo $url;
        exit;
    }//end of product_app_url()

    /**
     * 상세이미지 순서 수정
     */
    function product_detail_img_order_update() {
        //request
        $req['p_num'] = trim($this->input->post_get('p_num', true));
        $req['data'] = trim($this->input->post_get('data', true));      //1|2|3|4 형식
        $req['type'] = $this->input->post_get('type', true)?$this->input->post_get('type', true):'A';      //1|2|3|4 형식

        if( empty($req['p_num']) || empty($req['data']) ) {
            page_request_return(get_status_code('error'), lang('site_error_empty_id'), true, "alert");
        }

        //상품 정보
        $product_row = $this->product_model->get_product_row($req['p_num']);
        if( empty($product_row) ) {
            page_request_return(get_status_code('error'), lang('site_error_empty_data'), true, "alert");
        }

        if($req['type'] == 'B') {
            $detail_image_array = json_decode($product_row['p_detail_image_b'], true);
        }else{
            $detail_image_array = json_decode($product_row['p_detail_image'], true);
        }


        $new_detail_image_array = array();
        $data_array = explode("|", $req['data']);

        foreach ($data_array as $key => $item) {
            $new_detail_image_array[$key] = $detail_image_array[$item];
        }//end of foreach()

        $p_detail_image = json_encode_no_slashes($new_detail_image_array);

        //수정
        $query_data = array();
        if($req['type'] == 'B'){
            $query_data['p_detail_image_b'] = $p_detail_image;
        }else{
            $query_data['p_detail_image'] = $p_detail_image;
        }

        $this->product_model->update_product($req['p_num'], $query_data);

        page_request_return(get_status_code('success'), "", true);
    }//end of product_detail_img_order_update()


    /**
     * 상세이미지 순서 수정
     */
    function rep_image_add_order_update() {
        //request
        $req['p_num'] = trim($this->input->post_get('p_num', true));
        $req['data'] = trim($this->input->post_get('data', true));      //1|2|3|4 형식
        $req['type'] = $this->input->post_get('type', true)?$this->input->post_get('type', true):'A';      //1|2|3|4 형식

        if( empty($req['p_num']) || empty($req['data']) ) {
            page_request_return(get_status_code('error'), lang('site_error_empty_id'), true, "alert");
        }

        //상품 정보
        $product_row = $this->product_model->get_product_row($req['p_num']);
        if( empty($product_row) ) {
            page_request_return(get_status_code('error'), lang('site_error_empty_data'), true, "alert");
        }

        if($req['type'] == 'B') {
            $p_rep_image_add_arr = json_decode($product_row['p_rep_image_add'], true);
        }else{
            $p_rep_image_add_arr = json_decode($product_row['p_rep_image_add'], true);
        }


        $new_rep_image_add_arr = array();
        $data_array = explode("|", $req['data']);

        foreach ($data_array as $key => $item) {
            $new_rep_image_add_arr[$key] = $p_rep_image_add_arr[$item];
        }//end of foreach()

        $p_rep_image_add = json_encode_no_slashes($new_rep_image_add_arr);

        //수정
        $query_data = array();
        if($req['type'] == 'B'){
            $query_data['p_rep_image_add'] = $p_rep_image_add;
        }else{
            $query_data['p_rep_image_add'] = $p_rep_image_add;
        }

        $this->product_model->update_product($req['p_num'], $query_data);

//        $product_row = $this->product_model->get_product_row($req['p_num']);
//
//        if($req['type'] != 'B'){ // A값인경우
//
//            $aReqInput = array(
//                'p_detail_image'    => $product_row->p_detail_image
//            ,   'p_order_code'      => $product_row->p_order_code
//            ,   'p_rep_image'       => $product_row->p_rep_image
//            ,   'p_today_image'     => $product_row->p_today_image
//            ,   'p_banner_image'    => $product_row->p_banner_image
//            ,   'mode'              => 'setSyncProductDetail_img'
//            );
//
//            $url = $this->config->item("order_site_http") . "/api/zsApi.php";
//            $param = http_build_query($aReqInput);
//            http_post_request($url, $param);
//
//        }

        page_request_return(get_status_code('success'), "", true);
    }//end of product_detail_img_order_update()

    /*
     * 상품 메인 롤링배너 오픈여부
     * */
    public function setMainBannerFlag()
    {
        ajax_request_check();

        $aInput = array(
                'p_num'     => $this->input->post('p_num')
            ,   'flag'      => $this->input->post('flag') //현재 플래그
            ,   'setFlag'   => $this->input->post('flag')=='Y'?'N':'Y' //변경 플래그
        );

        if($aInput['setFlag'] == 'Y'){

            $sql = "SELECT COUNT(*) AS cnt FROM product_tb WHERE p_main_banner_view = 'Y' ; ";
            $oResult = $this->db->query($sql);
            $aResult = $oResult->row_array();

            if($aResult['cnt'] > 1){
                page_request_return(get_status_code('error'), "메인롤링배너에 노출되는 상품은 최대 2개를 입니다 .", true);
                exit;
            }

        }

        $query_data = array();
        $query_data['p_main_banner_view'] = $aInput['setFlag'];
        $this->product_model->update_product($aInput['p_num'], $query_data);

        page_request_return(get_status_code('success'), "변경완료", true);
        exit;
    }
    /**
     * 상품 단축 URL 생성
     */
    public function product_shorten_url() {
        ajax_request_check();

        //reqeust
        $req['p_num'] = $this->input->post("p_num", true);    //상품번호
        $req['site'] = $this->input->post("site", true);    //사이트
        $req['type'] = $this->input->post("type", true);    //타입(1=웹, 2=앱(웹), 3=앱(마켓))

        $long_url = "";
        $link = $this->config->item("site_http") . "/product/detail/" . $req['p_num'];
        //$long_url .= "ref_site=" . $req['ssu_site'] . "&ref_kwd=" . $req['ssu_keyword'];
        if( !empty($req['site']) ) {
            $utm_param = "&utm_source=" . $req['site'] . "&utm_campaign=" . $req['site'];
            //$long_url = $g_shop_dynamic_link_domain[$req['ssu_inid']] . "/?link=" . urlencode($long_url) . "&apn=&afl=" . urlencode($long_url) . $utm_param;
            $link .= "/?ref_site=" . $req['site'];
        }

        $ios_add = "&isi=".$this->config->item("ios_link_key")."&ibi=".$this->config->item("app_id");
        $ios_add_web = "&ibi=".$this->config->item("app_id");

        //일반
        if( $req['type'] == "1" ) {
            $long_url .= $link;
        }
        //앱(웹)
        else if( $req['type'] == "2" ) {
            $long_url .= $this->config->item("dynamic_link_http") . "/?link=" . urlencode($link) . "&apn=" . $this->config->item("app_id") . "&afl=" . urlencode($link) . $utm_param.$ios_add_web;
        }
        //앱(마켓)
        else if( $req['type'] == "3" ) {
            $long_url .= $this->config->item("dynamic_link_http") . "/?link=" . urlencode($link) . "&apn=" . $this->config->item("app_id") . "&afl=" . $utm_param.$ios_add;
        }

        $url = get_short_url($long_url);
        if( empty($url) ) {
            page_request_return(get_status_code("error"), lang("site_error_unknown"), true, "alert");
        }

        $data = array();
        $data['url'] = $url;

        page_request_return(get_status_code("success"), "", true, "", "", $data);
    }//end of product_shorten_url()




    public function product_restock_push_mass() {

        ajax_request_check();

        $aInput = array( 'p_num_str' => $this->input->post('p_num_str') );

        if($aInput['p_num_str'] == ''){
            $rResult = array('success' => false , 'msg' => '필수입력사항누락[\'p_num\']' );
            echo json_encode_no_slashes($rResult);
            exit;
        }

        $p_num_arr = explode(',',$aInput['p_num_str']);

        foreach ($p_num_arr as $val) {

            $aInput['p_num'] = $val;

            /*----------------------------------------------------
             * 상품 valid chk
             *----------------------------------------------------*/

            $product_row = $this->product_model->get_product_row($aInput['p_num']);

            if( empty($product_row) ) {
                $rResult = array('success' => true , 'msg' => lang('site_error_empty_data')."[상품번호:{$aInput['p_num']}]" );
                echo json_encode_no_slashes($rResult);
                exit;
            }

            if(     $product_row['p_display_state']   == 'N' //진열상태
                ||  $product_row['p_sale_state']      == 'N' //판매상태
                ||  $product_row['p_stock_state']     == 'N' //재고상태
            ){
                $rResult = array('success' => false , 'msg' => '푸시발송은 구매가능한 상품만 가능합니다.[쇼핑앱]'."[상품번호:{$aInput['p_num']}]" );
                echo json_encode_no_slashes($rResult);
                exit;
            }

            /*----------------------------------------------------
             * 상품 valid chk END
             *----------------------------------------------------*/

            $aLists = $this->product_model->get_restock_push_list($aInput['p_num']);

            if(count($aLists) < 1){
                $rResult = array('success' => false , 'msg' => '발송대상자가 없습니다.'."[상품번호:{$aInput['p_num']}]" );
                echo json_encode_no_slashes($rResult);
                exit;
            }

            $this->db->trans_begin();

            //상품 초기화
            $this->product_model->init_restock_push_data($aInput['p_num']);

            if ($this->db->trans_status() === FALSE){

                $this->db->trans_rollback();
                $rResult = array('success' => false , 'msg' => '푸시발송실패[DB]' );

            } else {

                $this->db->trans_commit();
                $rResult = array('success' => true , 'msg' => '푸시발송완료' );

                /*----------------------------------------------------
                 * 푸시발송
                 *----------------------------------------------------*/

                $aPushData = $aPushData_i = array();

                $push_data = array();
                $push_data['title'] = "재입고알림 안내";
                $push_data['msg'] = "[".$product_row->p_name."] 구매가능";
                $push_data['tarUrl'] = $this->config->item('site_http') . "/product/detail/{$aInput['p_num']}/?ref_site=push_restock";

                log_message('ZS','푸시 일괄발송 :: 대상자 - '.count($aLists).':: 데이터 - '.json_encode_no_slashes($push_data));

                foreach ($aLists as $row) {
                    if($row['m_device_model'] == 'iPad' || $row['m_device_model'] == 'iPhone'){ //ios
                        $aPushData_i[] = $row['m_regid'];
                    }else{ //그외
                        $aPushData[] = $row['m_regid'];
                    }
                }

                send_app_push_1($aPushData_i, $push_data);
                send_app_push($aPushData, $push_data);

                /*----------------------------------------------------
                 * 푸시발송 END
                 *----------------------------------------------------*/

            }

        }
        echo json_encode_no_slashes($rResult);



    }
    public function product_restock_push() {

        ajax_request_check();

        $aInput = array( 'p_num' => $this->input->post('p_num') );

        if($aInput['p_num'] == ''){
            $rResult = array('success' => false , 'msg' => '필수입력사항누락[\'p_num\']' );
            echo json_encode_no_slashes($rResult);
            exit;
        }

        /*----------------------------------------------------
         * 상품 valid chk
         *----------------------------------------------------*/

        $product_row = $this->product_model->get_product_row($aInput['p_num']);

        if( empty($product_row) ) {
            $rResult = array('success' => true , 'msg' => lang('site_error_empty_data') );
            echo json_encode_no_slashes($rResult);
            exit;
        }

        if(     $product_row['p_display_state']   == 'N' //진열상태
            ||  $product_row['p_sale_state']      == 'N' //판매상태
            ||  $product_row['p_stock_state']     == 'N' //재고상태
        ){
            $rResult = array('success' => false , 'msg' => '푸시발송은 구매가능한 상품만 가능합니다.[쇼핑앱]' );
            echo json_encode_no_slashes($rResult);
            exit;
        }

        /*----------------------------------------------------
         * 상품 valid chk END
         *----------------------------------------------------*/

        $aLists = $this->product_model->get_restock_push_list($aInput['p_num']);

        if(count($aLists) < 1){
            $rResult = array('success' => false , 'msg' => '발송대상자가 없습니다.' );
            echo json_encode_no_slashes($rResult);
            exit;
        }

        $this->db->trans_begin();

        //상품 초기화
        $this->product_model->init_restock_push_data($aInput['p_num']);

        if ($this->db->trans_status() === FALSE){

            $this->db->trans_rollback();
            $rResult = array('success' => false , 'msg' => '푸시발송실패[DB]' );

        } else {

            $this->db->trans_commit();
            $rResult = array('success' => true , 'msg' => '푸시발송완료' );

            /*----------------------------------------------------
             * 푸시발송
             *----------------------------------------------------*/

            $aPushData = $aPushData_i = array();


            $push_data = array();
            $push_data['title'] = "재입고알림 안내";
            $push_data['msg'] = "[".$product_row->p_name."] 구매가능";
            $push_data['tarUrl'] = $this->config->item('site_http') . "/product/detail/{$aInput['p_num']}/?ref_site=push_restock";

            foreach ($aLists as $row) {
                if($row['m_device_model'] == 'iPad' || $row['m_device_model'] == 'iPhone'){ //ios
                    $aPushData_i[] = $row['m_regid'];
                }else{ //그외
                    $aPushData[] = $row['m_regid'];
                }
            }

            send_app_push_1($aPushData_i, $push_data);
            send_app_push($aPushData, $push_data);

            /*----------------------------------------------------
             * 푸시발송 END
             *----------------------------------------------------*/

        }

        echo json_encode_no_slashes($rResult);

    }

    public function product_set_auto_comment(){
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "auto_cmt_cont" => array("field" => "auto_cmt_cont", "label" => "댓글내용", "rules" => "required|".$this->default_set_rules),
            "reg_name" => array("field" => "reg_name", "label" => "등록자명", "rules" => "required|".$this->default_set_rules),
            "reg_min" => array("field" => "reg_min", "label" => "자동등록 시간(분)", "rules" => "numeric|".$this->default_set_rules),
            "p_num" => array("field" => "p_num", "label" => "상품번호", "rules" => "required|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {

            $aInput = array(
                    'p_num'         => $this->input->post('p_num')
                ,   'reg_min'       => $this->input->post('reg_min')
                ,   'auto_cmt_cont' => $this->input->post('auto_cmt_cont')
                ,   'reg_name'      => $this->input->post('reg_name')
                ,   'reg_date'      => current_datetime()
            );

            if($aInput['reg_min'] > 0 && $aInput['reg_min'] < 10){

                $rResult = array('success' => false , 'msg' => '댓글등록시간을 지정하시려면 최소 10분 이상으로 등록해주세요!' );

            }else{

                $aInput['reg_min'] = $aInput['reg_min'] < 1 ? rand(10,60) : $aInput['reg_min'] ;
                $this->product_model->publicInsert('auto_cmt_tb',$aInput);
                $rResult = array('success' => true , 'msg' => '' );

            }

            echo json_encode_no_slashes($rResult);
            exit;

        }

        $rResult = array('success' => false , 'msg' => '새로고침 후 다시시도해주세요!' );

        echo json_encode_no_slashes($rResult);
        exit;

    }

    public function product_get_auto_comment(){
        ajax_request_check();

        $p_num = $this->input->post('p_num');

        if(empty($p_num) == true){
            $rResult = array('success' => false , 'msg' => '필수입력정보 누락(p_num)' );
            echo json_encode_no_slashes($rResult);
            exit;
        }

        $sql = "SELECT * FROM auto_cmt_tb WHERE p_num = '{$p_num}' ORDER BY proc_flag ASC ;  ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        foreach ($aResult as $k => $r) {
            $aResult[$k]['proc_date_str'] = view_date_format($r['proc_date'] , 2);
        }

        $rResult = array('success' => true , 'msg' => '' , 'data' => $aResult );
        echo json_encode_no_slashes($rResult);
        exit;

    }

    public function product_del_auto_comment(){
        ajax_request_check();

        $seq = $this->input->post('seq');

        if(empty($seq) == true){
            $rResult = array('success' => false , 'msg' => '필수입력정보 누락(seq)' );
            echo json_encode_no_slashes($rResult);
            exit;
        }

        $sql = "SELECT * FROM auto_cmt_tb WHERE seq = '{$seq}'; ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        if(empty($aResult) == true){
            $rResult = array('success' => false , 'msg' => '삭제할 데이터가 없습니다.' );
        }else{

            $sql = "DELETE FROM auto_cmt_tb WHERE seq = '{$seq}'; ";
            $bResult = $this->db->query($sql);

            $rResult = array('success' => false , 'msg' => '삭제실패[DB]' );
            if($bResult == true){
                $rResult = array('success' => true , 'msg' => '' );
            }

        }

        echo json_encode_no_slashes($rResult);
        exit;

    }


    /**
     * 이미지 복사
     * @param $remote
     * @param $local
     * @return bool
     */
    private function _copy_image($remote, $local) {
        if( empty($remote) || empty($local) ) {
            return false;
        }

        $file_data = get_url_content($remote);
        file_write($local, $file_data);
    }//end of _copy_image()

    /**
     * 새로운 파일명 추출
     * @param $path
     * @param $file
     * @return string
     */
    private  function _get_new_file_name($path, $file) {
        $file_ext = pathinfo($file, PATHINFO_EXTENSION);
        return $path . "/"  . create_session_id() . "." . $file_ext;
    }//end of _get_file_name()



    /**
     * @modify 황기석
     * @desc 재고옵션상품 제거
     */
    public function product_stock_del(){

        ajax_request_check();

        $aInput = array( 'p_num' => $this->input->post('p_num') );

        $au_num = $_SESSION['session_au_num'];

        if(is_array($aInput['p_num']) == true){
            $p_num_str = join(',',$aInput['p_num']);
            $sql = "UPDATE stock_chk_tb 
                    SET del_yn = 'Y'
                    ,   del_id = '{$au_num}'
                    ,   del_date = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                    WHERE p_num in ({$p_num_str}) ;
            ";
            $this->db->query($sql);
        }else{
            $sql = "UPDATE stock_chk_tb 
                    SET del_yn = 'Y'
                    ,   del_id = '{$au_num}'
                    ,   del_date = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                    WHERE p_num in ({$aInput['p_num']}) ;
            ";
            $this->db->query($sql);
        }

        echo json_encode_no_slashes(array('success' => true , 'msg' => '처리완료'));

    }
    /**
     * @modify 황기석
     * @desc 재고옵션상품 추가
     */
    public function product_stock_chk(){

        ajax_request_check();

        $aInput = array( 'p_num' => $this->input->post('p_num') );

        $au_num = $_SESSION['session_au_num'];

        if(is_array($aInput['p_num']) == true){

            foreach ($aInput['p_num'] as $v) {

                $data = explode('|',$v);

                $sql = "SELECT * FROM stock_chk_tb WHERE p_num = '{$data[0]}'; ";
                $cnt = $this->db->query($sql)->num_rows();

                if($cnt < 1){
                    $sql = "INSERT INTO stock_chk_tb
                            SET     
                                p_num        = '{$data[0]}'
                            ,   p_order_code = '{$data[1]}'
                            ,   reg_id       = '{$au_num}'
                            ,   mod_id       = '{$au_num}'
                            ,   reg_date     = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                            ,   mod_date     = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                    ";
                    $this->db->query($sql);
                }else{

                    $sql = "UPDATE stock_chk_tb
                            SET mod_date    = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                            ,   mod_id      = '{$au_num}'
                            ,   del_yn      = 'N'
                            ,   del_id      = NULL
                            ,   del_date    = NULL
                            ,   issue_yn    = 'N'
                            ,   proc_yn     = 'Y'
                            WHERE p_num = '{$data[0]}' ; 
                    ";
                    $this->db->query($sql);

                }

            };

        }else{

            $data = explode('|',$aInput['p_num']);

            $sql = "SELECT * FROM stock_chk_tb WHERE p_num = '{$data[0]}'; ";
            $cnt = $this->db->query($sql)->num_rows();

            if($cnt < 1){
                $sql = "INSERT INTO stock_chk_tb
                        SET     
                            p_num        = '{$data[0]}'
                        ,   p_order_code = '{$data[1]}'
                        ,   reg_id       = '{$au_num}'
                        ,   mod_id       = '{$au_num}'
                        ,   reg_date     = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                        ,   mod_date     = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                ";
                $this->db->query($sql);
            }else{

                $sql = "UPDATE stock_chk_tb
                        SET mod_date = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                        ,   mod_id = '{$au_num}'
                        ,   del_yn = 'N'
                        ,   del_id = NULL
                        ,   del_date = NULL
                        WHERE p_num = '{$data[0]}' ; 
                ";
                $this->db->query($sql);

            }

        }

        echo json_encode_no_slashes(array('success' => true , 'msg' => '처리완료'));

    }

    /**
     * @date 200528
     * @author 황기석
     * @TODO 신규 상품등록시 토큰을 통한 옵션연결작업
     *
     * @desc 옵션관련 method
     */

    /**
     * @modify 황기석
     * @desc 옵션팝업
     */
    public function product_option_pop(){

        $aInput = array(
            'type'  => $this->input->get('view_type')
        ,   'depth' => $this->input->get('depth')
        ,   'p_num' => $this->input->get('p_num')
        ,   'option_token' => $this->input->get('opt_token')
        );

        $this->load->model('product_model');

        $this->load->view('/product/product_option_pop', array(
            'aInput'        => $aInput
        ));

    }

    /**
     * @modify 황기석
     * @desc 값에 따라 옵션페이지 분기 및 데이터 리스트
     */
    public function product_option(){

        $headers = apache_request_headers();

        foreach ($headers as $header => $value) {
            if($header == 'Accept'){
                if( preg_match('/application\/json/',$value) == true) $isDatatype = 'json';
                else $isDatatype = 'html';
            }
        }

        $aInput = array(
            'type'  => $isDatatype=='html'?$this->input->post('type'):'basic' //데이터타입이 json인 경우 무조건 옵션유무체크를 위한 값(basic)으로 변경
        ,   'depth' => $this->input->post('depth')
        ,   'p_num' => $this->input->post('p_num')
        ,   'option_token' => $this->input->post('option_token')
        );

        $aProductInfo       = $this->product_model->get_product_row($aInput['p_num']);
        $aProductOptionList = $this->product_option_model->get_option_list(array('p_num' => $aInput['p_num'] , 'option_token' => $aInput['option_token'] , 'type' => $aInput['type'] ));

        if($isDatatype == 'json'){

            echo json_encode_no_slashes(array('success' => true , 'msg' => '' , 'data' => $aProductOptionList));

        }else{

            $this->load->model('product_model');

            if($aInput['type'] == 'basic') $view_file = '/product/product_option'.$aInput['depth'];
            else $view_file = '/product/product_option'.$aInput['type'];

            $this->load->view($view_file, array(
                'aInput'                => $aInput
            ,   'aProductInfo'          => $aProductInfo
            ,   'aProductOptionList'    => $aProductOptionList
            ));

        }

    }

    /**
     * @modify 황기석
     * @desc 옵션삭제
     */
    public function delete_option(){

        ajax_request_check();

        $aInput = array(
                'option_id'         => $this->input->post('option_id')
            ,   'option_group_id'   => $this->input->post('option_group_id')
        );

        $aProductOptionInfo = $this->product_option_model->get_option_row(array('option_id' => $aInput['option_id']));
        $aProductOptionGroupInfo = $this->product_option_model->get_option_group_row(array('option_group_id' => $aInput['option_group_id']));

        if(empty($aProductOptionInfo) == true && empty($aProductOptionGroupInfo) == true){
            echo json_encode_no_slashes(array('success' => false, 'msg' => '옵션정보가 없습니다.'));
            exit;
        }

        if( empty($aInput['option_id']) == false ) $bRet = $this->product_option_model->del_option($aInput, 'single');
        if( empty($aInput['option_group_id']) == false ) $bRet = $this->product_option_model->del_option($aInput, 'group');

        if($bRet == false){
            echo json_encode_no_slashes(array('success' => false, 'msg' => '옵션삭제 실패[DB]'));
        }else{
            if($aProductOptionInfo['option_use_img'] == 'Y') file_delete(1,$aProductOptionInfo['option_img'],DOCROOT);
            echo json_encode_no_slashes(array('success' => true, 'msg' => ''));
        }

    }

    /**
     * @modify 황기석
     * @desc 1+1 / 1+1+1 그룹옵션 관련 등록/수정
     */
    public function upsert_option_group(){

        ajax_request_check();

        $tmp_data = array(
            'p_num'                 => $this->input->post('p_num')
        ,   'depth'                 => $this->input->post('depth')
        ,   'type'                  => $this->input->post('type')
        ,   'option_token'          => $this->input->post('option_token')
        ,   'option_group_id'       => $this->input->post('option_group_id')
        ,   'act_type'              => $this->input->post('act_type')
        ,   'option_group1'         => $this->input->post('option_group1')
        ,   'option_group2'         => $this->input->post('option_group2')
        ,   'option_group3'         => $this->input->post('option_group3')
        ,   'option_sale_price'     => $this->input->post('option_sale_price')
        ,   'option_org_price'      => $this->input->post('option_org_price')
        ,   'option_supply_price'   => $this->input->post('option_supply_price')
        ,   'option_group_stock1'   => $this->input->post('option_group_stock1')
        ,   'option_group_stock2'   => $this->input->post('option_group_stock2')
        ,   'option_group_stock3'   => $this->input->post('option_group_stock3')

        );

        $query_data = array();
        $i = 0;
        foreach ($tmp_data['option_group_id'] as $k => $v) {

            //각차수에 맞는 옵션명이 비어있는 경우 pass
//            if($tmp_data['depth'] == 1 && empty($tmp_data['option_group1'][$k]) == true) continue;
//            else if($tmp_data['depth'] == 2 && ( empty($tmp_data['option_group1'][$k]) == true || empty($tmp_data['option_group2'][$k]) == true )) continue;
//            else if($tmp_data['depth'] == 3 && ( empty($tmp_data['option_group1'][$k]) == true || empty($tmp_data['option_group2'][$k]) == true || empty($tmp_data['option_group3'][$k]) == true )) continue;

            if(     empty($tmp_data['option_group1'][$k]) == true
                &&  empty($tmp_data['option_group2'][$k]) == true
                &&  empty($tmp_data['option_group3'][$k]) == true
            ) continue;

            $query_data[$i]['option_group_id']      = $v;
            $query_data[$i]['p_num']                = $tmp_data['p_num']?$tmp_data['p_num']:0;
            $query_data[$i]['option_depth']         = $tmp_data['depth'];
            $query_data[$i]['option_type']          = $tmp_data['type'];
            $query_data[$i]['option_token']         = $tmp_data['option_token'];
            $query_data[$i]['act_type']             = $tmp_data['act_type'][$k];

            $query_data[$i]['option_group1']        = $tmp_data['option_group1'][$k];
            $query_data[$i]['option_group2']        = $tmp_data['option_group2'][$k];
            $query_data[$i]['option_group3']        = $tmp_data['option_group3'][$k];
            $query_data[$i]['option_sale_price']    = $tmp_data['option_sale_price'][$k]?$tmp_data['option_sale_price'][$k]:0;
            $query_data[$i]['option_org_price']     = $tmp_data['option_org_price'][$k]?$tmp_data['option_org_price'][$k]:0;
            $query_data[$i]['option_supply_price']  = $tmp_data['option_supply_price'][$k]?$tmp_data['option_supply_price'][$k]:0;
            $query_data[$i]['option_group_stock1']  = $tmp_data['option_group_stock1'][$k]?$tmp_data['option_group_stock1'][$k]:0;
            $query_data[$i]['option_group_stock2']  = $tmp_data['option_group_stock2'][$k]?$tmp_data['option_group_stock2'][$k]:0;
            $query_data[$i]['option_group_stock3']  = $tmp_data['option_group_stock3'][$k]?$tmp_data['option_group_stock3'][$k]:0;

            $i++;

        }

        $this->product_option_model->upsert_option_group($query_data);
        $aProductOptionGroupList = $this->product_option_model->get_option_group_list(array('p_num' => $tmp_data['p_num'],'option_token' => $tmp_data['option_token']));

        $query_data = array();
        $i          = 0;
        if( $aProductOptionGroupList[0]['option_depth'] == 2 ){

            foreach ($aProductOptionGroupList as $k => $r) {

                foreach ($aProductOptionGroupList as $kk => $rr) {

                    if(empty($r['option_group1']) == true) continue;

                    $aInput             = array('option_group_id1' => $r['option_group_id'], 'option_group_id2' => $rr['option_group_id']);
                    $aProductOptionInfo = $this->product_option_model->get_option_row($aInput);

                    if(empty($aProductOptionInfo) == false){
                        $query_data[$i]['option_id']            = $aProductOptionInfo['option_id'];
                        $query_data[$i]['act_type']             = 'update';
                    }else{
                        $query_data[$i]['option_id']            = '';
                        $query_data[$i]['act_type']             = 'insert';
                    }

                    $query_data[$i]['p_num']                = $r['p_num'];
                    $query_data[$i]['option_depth']         = $r['option_depth'];
                    $query_data[$i]['option_type']          = $r['option_type'];
                    $query_data[$i]['option_token']         = $r['option_token'];
                    $query_data[$i]['option_sort']          = (int)$i+1;
                    $query_data[$i]['option_1']             = $r['option_group1'];
                    $query_data[$i]['option_2']             = $rr['option_group2'];
                    $query_data[$i]['option_sale_price']    = $rr['option_sale_price']?$rr['option_sale_price']:0;
                    $query_data[$i]['option_org_price']     = $rr['option_org_price']?$rr['option_org_price']:0;
                    $query_data[$i]['option_supply_price']  = $rr['option_supply_price']?$rr['option_supply_price']:0;
                    $query_data[$i]['option_stock']         = '0';
                    $query_data[$i]['option_add']           = 'N';
                    $query_data[$i]['option_use_img']       = 'N';

                    $query_data[$i]['option_group_id1']     = $r['option_group_id'];
                    $query_data[$i]['option_group_id2']     = $rr['option_group_id'];

                    $i++;

                }

            }

        } else if( $aProductOptionGroupList[0]['option_depth'] == 3 ){

            foreach ($aProductOptionGroupList as $k => $r) {

                foreach ($aProductOptionGroupList as $kk => $rr) {

                    foreach ($aProductOptionGroupList as $kkk => $rrr) {

                        if(empty($r['option_group1']) == true || empty($r['option_group2']) == true) continue;

                        $aInput             = array('option_group_id1' => $r['option_group_id'], 'option_group_id2' => $rr['option_group_id'], 'option_group_id3' => $rrr['option_group_id']);
                        $aProductOptionInfo = $this->product_option_model->get_option_row($aInput);

                        if(empty($aProductOptionInfo) == false){
                            $query_data[$i]['option_id']            = $aProductOptionInfo['option_id'];
                            $query_data[$i]['act_type']             = 'update';
                        }else{
                            $query_data[$i]['option_id']            = '';
                            $query_data[$i]['act_type']             = 'insert';
                        }

                        $query_data[$i]['p_num']                = $r['p_num'];
                        $query_data[$i]['option_depth']         = $r['option_depth'];
                        $query_data[$i]['option_type']          = $r['option_type'];
                        $query_data[$i]['option_token']         = $r['option_token'];
                        $query_data[$i]['option_sort']          = (int)$i+1;
                        $query_data[$i]['option_1']             = $r['option_group1'];
                        $query_data[$i]['option_2']             = $rr['option_group2'];
                        $query_data[$i]['option_2']             = $rrr['option_group3'];
                        $query_data[$i]['option_sale_price']    = $rrr['option_sale_price']?$rrr['option_sale_price']:0;
                        $query_data[$i]['option_org_price']     = $rrr['option_org_price']?$rrr['option_org_price']:0;
                        $query_data[$i]['option_supply_price']  = $rrr['option_supply_price']?$rrr['option_supply_price']:0;
                        $query_data[$i]['option_stock']         = '0';
                        $query_data[$i]['option_add']           = 'N';
                        $query_data[$i]['option_use_img']       = 'N';

                        $query_data[$i]['option_group_id1']     = $r['option_group_id'];
                        $query_data[$i]['option_group_id2']     = $rr['option_group_id'];
                        $query_data[$i]['option_group_id3']     = $rrr['option_group_id'];

                        $i++;

                    }

                }

            }

        } else { //error
            echo json_encode_no_slashes(array('success' => false , 'msg' => 'error'));
            exit;
        }

        $this->product_option_model->upsert_option($query_data);

        echo json_encode_no_slashes(array('success' => true , 'msg' => '처리완료'));
        exit;

    }

    /**
     * @modify 황기석
     * @desc 단일옵션 관련 등록/수정
     */
    public function upsert_option(){

        ajax_request_check();

        $tmp_data = array(
                'p_num'                 => $this->input->post('p_num')
            ,   'depth'                 => $this->input->post('depth')
            ,   'type'                  => $this->input->post('type')
            ,   'option_token'          => $this->input->post('option_token')
            ,   'option_id'             => $this->input->post('option_id')
            ,   'act_type'              => $this->input->post('act_type')
            ,   'option_sort'           => $this->input->post('option_sort')
            ,   'option_1'              => $this->input->post('option_1')
            ,   'option_2'              => $this->input->post('option_2')
            ,   'option_3'              => $this->input->post('option_3')
            ,   'option_sale_price'     => $this->input->post('option_sale_price')
            ,   'option_org_price'      => $this->input->post('option_org_price')
            ,   'option_supply_price'   => $this->input->post('option_supply_price')
            ,   'option_stock'          => $this->input->post('option_stock')
            ,   'option_add'            => $this->input->post('option_add')
        );

        $query_data = array();
        $i = 0;
        foreach ($tmp_data['option_id'] as $k => $v) {

            //각차수에 맞는 옵션명이 비어있는 경우 pass
            if($tmp_data['depth'] == 1 && empty($tmp_data['option_1'][$k]) == true) continue;
            else if($tmp_data['depth'] == 2 && ( empty($tmp_data['option_1'][$k]) == true || empty($tmp_data['option_2'][$k]) == true )) continue;
            else if($tmp_data['depth'] == 3 && ( empty($tmp_data['option_1'][$k]) == true || empty($tmp_data['option_2'][$k]) == true || empty($tmp_data['option_3'][$k]) == true )) continue;

            $query_data[$i]['option_id']            = $v;
            $query_data[$i]['p_num']                = $tmp_data['p_num']?$tmp_data['p_num']:0;
            $query_data[$i]['option_depth']         = $tmp_data['depth'];
            $query_data[$i]['option_type']          = $tmp_data['type'];
            $query_data[$i]['option_token']         = $tmp_data['option_token'];

            $query_data[$i]['act_type']             = $tmp_data['act_type'][$k];
            $query_data[$i]['option_sort']          = $tmp_data['option_sort'][$k];
            $query_data[$i]['option_1']             = $tmp_data['option_1'][$k];
            $query_data[$i]['option_2']             = $tmp_data['option_2'][$k];
            $query_data[$i]['option_3']             = $tmp_data['option_3'][$k];
            $query_data[$i]['option_sale_price']    = $tmp_data['option_sale_price'][$k]?$tmp_data['option_sale_price'][$k]:0;
            $query_data[$i]['option_org_price']     = $tmp_data['option_org_price'][$k]?$tmp_data['option_org_price'][$k]:0;
            $query_data[$i]['option_supply_price']  = $tmp_data['option_supply_price'][$k]?$tmp_data['option_supply_price'][$k]:0;
            $query_data[$i]['option_stock']         = $tmp_data['option_stock'][$k]?$tmp_data['option_stock'][$k]:0;
            $query_data[$i]['option_add']           = $tmp_data['option_add'][$k];

            if(empty($v) == false){
                $aProductOptionRow = $this->product_option_model->get_option_row($v);
                $query_data[$i]['option_img']       = $aProductOptionRow['option_img'];
                $query_data[$i]['option_use_img']   = $aProductOptionRow['option_use_img'];
            }else{
                $query_data[$i]['option_use_img']       = 'N';
            }

            $i++;

        }

        if( isset($_FILES['option_img']['name']) && !empty($_FILES['option_img']['name']) ) {

            //업로드
            $p_image_path_web = $this->config->item('option_image_path_web') . "/" . date("Y") . "/" . date("md").'/';
            $p_image_path = DOCROOT.$p_image_path_web;

            create_directory($p_image_path);

            $config = array();
            $config['upload_path']      = $p_image_path;
            $config['allowed_types']    = 'gif|jpg|jpeg|png';
            $config['max_size']	        = $this->config->item('upload_total_max_size');
            $config['file_ext_tolower'] = true; //확장자 소문자
            $config['overwrite']        = true; //덮어쓰기
            $config['encrypt_name']     = true;

            $this->load->library('upload' , $config);

            foreach ($_FILES['option_img']['name'] as $key => $name) {

                $_FILES['userfile']['name']     = $_FILES['option_img']['name'][$key];
                $_FILES['userfile']['type']     = $_FILES['option_img']['type'][$key];
                $_FILES['userfile']['tmp_name'] = $_FILES['option_img']['tmp_name'][$key];
                $_FILES['userfile']['error']    = $_FILES['option_img']['error'][$key];
                $_FILES['userfile']['size']     = $_FILES['option_img']['size'][$key];

                if(empty($query_data[$key]) == false){

                    if( $this->upload->do_upload() ){
                        $img_data = $this->upload->data();
                        $query_data[$key]['option_img'] = $p_image_path_web.$img_data['file_name'];
                        $query_data[$key]['option_use_img'] = 'Y';
                    }

                }
            }
        }


        $this->product_option_model->upsert_option($query_data);

        echo json_encode_no_slashes(array('success' => true , 'msg' => '처리완료'));
        exit;

    }
    /* END OF OPTION METHOD */

}//end of class Product