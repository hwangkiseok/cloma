<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 카카오광고 상품 관련 컨트롤러
 */
class Kakao_product extends A_Controller {

    var $campaign = "kakao_push";

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('kakao_product_model');
    }//end of __construct()

    private function _list_req() {
        $req = array();
        $req['kfd']                     = trim($this->input->post_get('kfd', true));
        $req['kwd']                     = trim($this->input->post_get('kwd', true));
        $req['prod_type']               = $this->input->post_get('prod_type', true);                    //상품구분(배열)
        $req['display_state']           = $this->input->post_get('display_state', true);                //노출여부(배열)
        $req['prod_display_state']      = $this->input->post_get('prod_display_state', true);           //배열(상품노출여부)
        $req['prod_sale_state']         = $this->input->post_get('prod_sale_state', true);              //배열(상품판매상태)
        $req['prod_hash_chk']           = trim($this->input->post_get('prod_hash_chk', true));          //해시
        $req['prod_second_price_yn']    = trim($this->input->post_get('prod_second_price_yn', true));   //2차판매가 여부
        $req['prod_restock_yn']         = trim($this->input->post_get('prod_restock_yn', true));        //품절제외
        $req['sort_field']              = trim($this->input->post_get('sort_field', true));             //정렬필드
        $req['sort_type']               = trim($this->input->post_get('sort_type', true));              //정렬구분(asc, desc)
        $req['page']                    = trim($this->input->post_get('page', true));
        $req['list_per_page']           = trim($this->input->post_get('list_per_page', true));

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
        $this->kakao_product_list();
    }//end of index()


    /**
     * 카카오광고 상품 목록
     */
    public function kakao_product_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/kakao_product/kakao_product_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of kakao_product_list()

    /**
     * 카카오광고 상품 목록 (Ajax)
     */
    public function kakao_product_list_ajax() {
        ajax_request_check(true);

        //request
        $req = $this->_list_req();
        //print_r($req);

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
        $list_count = $this->kakao_product_model->get_kakao_product_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/kakao_product/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //페이지번호 보정
        if( $req['page'] > $page_result['total_page'] ) {
            $req['page'] = $page_result['total_page'];
        }

        //목록
        $kakao_product_list = $this->kakao_product_model->get_kakao_product_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['p_name'] = array("asc", "sorting");
        $sort_array['kp_prod_type'] = array("asc", "sorting");
        $sort_array['kp_title'] = array("asc", "sorting");
        $sort_array['kp_content'] = array("asc", "sorting");
        $sort_array['kp_button_name'] = array("asc", "sorting");
        $sort_array['kp_url'] = array("asc", "sorting");
        $sort_array['kp_click_count'] = array("asc", "sorting");
        $sort_array['kp_display_state'] = array("asc", "sorting");
        $sort_array['kp_regdatetime'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/kakao_product/kakao_product_list_ajax", array(
            "req"                   => $req,
            "GV"                    => $GV,
            "PGV"                   => $PGV,
            "sort_array"            => $sort_array,
            "list_count"            => $list_count,
            "list_per_page"         => $req['list_per_page'],
            "page"                  => $req['page'],
            "kakao_product_list"    => $kakao_product_list,
            "pagination"            => $page_result['pagination']
        ));
    }//end of kakao_product_list_ajax()

    /**
     * 상품 검색 (Ajax)
     */
    public function kakao_product_search_ajax() {
        ajax_request_check(true);

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
        if( !empty($req['sort_field']) && !empty($req['sort_type']) ) {
            $query_array['orderby'] = $req['sort_field'] . " " . $req['sort_type'];
        }

        //전체갯수
        $list_count = $this->kakao_product_model->get_kakao_product_list($query_array, "", "", true);

        $list_start = "";
        $list_limit = "";
        if( $dataType == 'html' ) {
            //페이징
            $page_result = $this->_paging(array(
                "total_rows"    => $list_count,
                "base_url"      => "/kakao_product/search_ajax/?" . $PGV,
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
            $kakao_product_list = $this->kakao_product_model->get_kakao_product_list($query_array, "", "");
        }
        else {
            $kakao_product_list = $this->kakao_product_model->get_kakao_product_list($query_array, $list_start, $list_limit);
        }

        foreach( $kakao_product_list as $key => $row ) {
            $row->p_rep_image_array = json_decode($row->p_rep_image, true);
            $row->p_display_state_text = $this->config->item($row->p_display_state, 'kakao_product_display_state');
            $row->p_sale_state_text = $this->config->item($row->p_sale_state, 'kakao_product_sale_state');
        }

        //json 출력
        if( $dataType == 'json' ) {
            result_echo_json(get_status_code('success'), "", true, "", "", $kakao_product_list);
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

            $this->load->view("/kakao_product/kakao_product_search_ajax", array(
                "req"                   => $req,
                "GV"                    => $GV,
                "PGV"                   => $PGV,
                "sort_array"            => $sort_array,
                "list_count"            => $list_count,
                "list_per_page"         => $req['list_per_page'],
                "page"                  => $req['page'],
                "kakao_product_list"          => $kakao_product_list,
                "pagination"            => ( isset($page_result['pagination']) ) ? $page_result['pagination'] : ""
            ));
        }
    }//end of kakao_product_list_ajax()

    /**
     * 카카오광고 상품 추가
     */
    public function kakao_product_insert_pop() {
        $this->load->view("/kakao_product/kakao_product_insert_pop", array(
            "list_url"  => $this->_get_list_url()
        ));
    }//end of kakao_product_insert()

    /**
     * 카카오광고 상품 추가 처리 (Ajax)
     */
    public function kakao_product_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "kp_prod_type" => array("field" => "kp_prod_type", "label" => "상품구분", "rules" => "required|in_list[".get_config_item_keys_string("kakao_product_prod_type")."]"),
            "kp_product_num" => array("field" => "kp_product_num", "label" => "상품선택", "rules" => "required|".$this->default_set_rules),
            "kp_seq" => array("field" => "kp_seq", "label" => "출력순서", "rules" => $this->default_set_rules),
            "kp_title" => array("field" => "kp_title", "label" => "제목", "rules" => $this->default_set_rules),
            "kp_content" => array("field" => "kp_content", "label" => "내용", "rules" => $this->default_set_rules),
            "kp_button_name" => array("field" => "kp_button_name", "label" => "버튼명", "rules" => $this->default_set_rules),
            "kp_display_state" => array("field" => "kp_display_state", "label" => "노출여부", "rules" => "required|in_list[".get_config_item_keys_string("kakao_product_display_state")."]|".$this->default_set_rules),
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $kp_prod_type = $this->input->post('kp_prod_type', true);
            $kp_product_num = $this->input->post('kp_product_num', true);
            $kp_seq = $this->input->post('kp_seq', true);
            $kp_title = $this->input->post('kp_title', true);
            $kp_content = $this->input->post('kp_content', true);
            $kp_button_name = $this->input->post('kp_button_name', true);
            $kp_display_state = $this->input->post('kp_display_state', true);

            if( empty($form_error_array) ) {
                //등록
                $query_data = array();
                $query_data['kp_prod_type'] = $kp_prod_type;
                $query_data['kp_product_num'] = number_only($kp_product_num);
                $query_data['kp_seq'] = $kp_seq;
                $query_data['kp_title'] = $kp_title;
                $query_data['kp_content'] = $kp_content;
                $query_data['kp_button_name'] = $kp_button_name;
                $query_data['kp_display_state'] = get_yn_value($kp_display_state);

                $insert_result = $this->kakao_product_model->insert_kakao_product($query_data);
                if( $insert_result['code'] == get_status_code('success') ) {
                    $kp_num = $insert_result['id'];

                    ////APP(웹) 단축URL
                    //$utm_param = "&utm_source=" . $this->campaign . "&utm_campaign=" . $this->campaign;
                    //$link =  urlencode($this->config->item("site_http") . "/kakao_product/" . $kp_num . "/?ref_site=" . $this->campaign);
                    //$ios_add_web = "&ibi=".$this->config->item("app_id");
                    //$kp_url = $this->config->item("dynamic_link_http") . "/?link=" . urlencode($link) . "&apn=" . $this->config->item("app_id") . "&afl=" . urlencode($link) . $utm_param . $ios_add_web;
                    //
                    ////단축 URL
                    //$query_data = array();
                    //$query_data['kp_url'] = get_short_url_bitly($kp_url);
                    //$this->kakao_product_model->update_kakao_product($kp_num, $query_data);

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
    }//end of kakao_product_insert_proc()

    /**
     * 카카오광고 상품 수정 (팝업)
     */
    public function kakao_product_update_pop() {
        //request
        $req['kp_num'] = $this->input->post_get('kp_num', true);

        $kakao_product_row = $this->kakao_product_model->get_kakao_product_row($req['kp_num']);
        if( empty($kakao_product_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->view("/kakao_product/kakao_product_update_pop", array(
            "kakao_product_row"  => $kakao_product_row
        ));
    }//end of kakao_product_update()

    /**
     * 상품 수정 처리 (Ajax)
     */
    public function kakao_product_update_proc() {
        ajax_request_check();

        //request
        $req['kp_num'] = $this->input->post("kp_num", true);
        if( empty($req['kp_num']) ) {
            page_request_return(get_status_code("error"), lang('site_error_empty_id'), true);
        }

        //row
        $kakao_product_row = $this->kakao_product_model->get_kakao_product_row($req['kp_num']);
        if( empty($kakao_product_row) ) {
            page_request_return(get_status_code("error"), lang('site_error_empty_data'), true);
        }

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "kp_prod_type" => array("field" => "kp_prod_type", "label" => "상품구분", "rules" => "required|in_list[".get_config_item_keys_string("kakao_product_prod_type")."]"),
            "kp_product_num" => array("field" => "kp_product_num", "label" => "상품선택", "rules" => "required|".$this->default_set_rules),
            "kp_seq" => array("field" => "kp_seq", "label" => "출력순서", "rules" => $this->default_set_rules),
            "kp_title" => array("field" => "kp_title", "label" => "제목", "rules" => $this->default_set_rules),
            "kp_content" => array("field" => "kp_content", "label" => "내용", "rules" => $this->default_set_rules),
            "kp_button_name" => array("field" => "kp_button_name", "label" => "버튼명", "rules" => $this->default_set_rules),
            "kp_display_state" => array("field" => "kp_display_state", "label" => "노출여부", "rules" => "required|in_list[".get_config_item_keys_string("kakao_product_display_state")."]|".$this->default_set_rules),
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $kp_prod_type = $this->input->post('kp_prod_type', true);
            $kp_product_num = $this->input->post('kp_product_num', true);
            $kp_seq= $this->input->post('kp_seq', true);
            $kp_title = $this->input->post('kp_title', true);
            $kp_content = $this->input->post('kp_content', true);
            $kp_button_name = $this->input->post('kp_button_name', true);
            $kp_display_state = $this->input->post('kp_display_state', true);

            if( empty($form_error_array) ) {
                //수정
                $query_data = array();
                $query_data['kp_prod_type'] = $kp_prod_type;
                $query_data['kp_product_num'] = number_only($kp_product_num);
                $query_data['kp_seq'] = $kp_seq;
                $query_data['kp_title'] = $kp_title;
                $query_data['kp_content'] = $kp_content;
                $query_data['kp_button_name'] = $kp_button_name;
                $query_data['kp_display_state'] = get_yn_value($kp_display_state);

                if( $this->kakao_product_model->update_kakao_product($req['kp_num'], $query_data) ) {
                    result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
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
    }//end of kakao_product_update_proc()

    /**
     * 카카오광고 상품 수정 토글
     */
    function kakao_product_update_toggle() {
        ajax_request_check();

        //request
        $req['kp_num'] = trim($this->input->post_get('kp_num', true));
        $req['fd'] = trim($this->input->post_get('fd', true));          //kp_display_state


        //수정 가능 필드
        $allow_fds = array('kp_display_state');

        if( !in_array($req['fd'], $allow_fds) ) {
            result_echo_json(get_status_code('error'), "", true, 'alert');
        }

        $kakao_product_row = $this->kakao_product_model->get_kakao_product_row($req['kp_num']);

        if( empty($kakao_product_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        $query_data = array();
        if( $kakao_product_row->{$req['fd']} == "Y" ) {
            $query_data[$req['fd']] = "N";
        }
        else {
            $query_data[$req['fd']] = "Y";
        }

        if( $this->kakao_product_model->update_kakao_product($kakao_product_row->kp_num, $query_data) ) {
            result_echo_json(get_status_code('success'), '', true);
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_error_unknown'), true);
        }
    }//end of kakao_product_update_toggle()

    /**
     * 카카오광고 상품 삭제 처리 (Ajax)
     */
    public function kakao_product_delete_proc() {
        ajax_request_check();

        //request
        $req['kp_num'] = $this->input->post_get('kp_num', true);

        //상품 정보
        $kakao_product_row = $this->kakao_product_model->get_kakao_product_row($req['kp_num']);

        if( empty($kakao_product_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //상품 삭제
        if( $this->kakao_product_model->delete_kakao_product($req['kp_num']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of kakao_product_delete_proc()

    /**
     * 카카오광고 상품 링크 단축 URL 생성
     */
    public function kakao_product_shorten_url() {
        ajax_request_check();

        //reqeust
        $req['kp_num'] = $this->input->post("kp_num", true);    //일련번호

        $link = $this->config->item("site_http") . "/kakao_product/list/" . $req['kp_num'] . "/?ref_site=" . $this->campaign . "&newapp=1";
        $utm_param = "&utm_source=" . $this->campaign . "&utm_campaign=" . $this->campaign;
        $ios_add_web = "&ibi=".$this->config->item("app_id");

        //APP(웹) 다이나믹링크
        $long_url = $this->config->item("dynamic_link_http") . "/?link=" . urlencode($link) . "&afl=" . urlencode($link) . "&apn=" . $this->config->item("app_id") . $utm_param . $ios_add_web;

        $url = get_short_url($long_url);

        if( empty($url) ) {
            page_request_return(get_status_code("error"), lang("site_error_unknown"), true, "alert");
        }

        $data = array();
        $data['url'] = $url;

        page_request_return(get_status_code("success"), "", true, "", "", $data);
    }//end of kakao_product_shorten_url()

    /**
     * 브랜드상품 출력순서 수정 (Ajax)
     */
    public function kakao_product_seq_update_proc() {
        ajax_request_check();

        $kp_seq = $this->input->post('kp_seq', true);       //배열 ([kp_num] => seq 형식)

        if( empty($kp_seq) ) {
            result_echo_json(get_status_code('error'), lang('site_no_data'), true, 'alert');
        }

        foreach( $kp_seq as $k => $v ) {
            $this->kakao_product_model->update_kakao_product($k, array('kp_seq' => $v));
        }//end of foreach()

        result_echo_json(get_status_code("success"), lang("site_update_success"), true, "alert");
    }//end of kakao_product_seq_update_proc()

}//end of class Kakao_product