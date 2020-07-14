<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * APP 푸시 관련 컨트롤러
 */
class App_push extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('app_push_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->app_push_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['rctly']           = trim($this->input->post_get('rctly', true));

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
     * APP 푸시 목록
     */
    public function app_push_list() {
        //request
        $req = $this->_list_req();

        //관리회원 목록 추출
        $this->load->model("member_model");
        $adm_mem_list = $this->member_model->get_admin_member_list("m_num, m_sns_site, m_nickname, m_email, m_device_model, m_regid");

        $this->_header();

        $this->load->view("/app_push/app_push_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page,
            'adm_mem_list'  => $adm_mem_list
        ));

        $this->_footer();
    }//end of app_push_list()

    /**
     * APP 푸시 목록 (Ajax)
     */
    public function app_push_list_ajax() {
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
        $list_count = $this->app_push_model->get_app_push_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/app_push/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $app_push_list = $this->app_push_model->get_app_push_list($query_array, $page_result['start'], $page_result['limit']);

        if( $query_array['where']['rctly'] == 'Y') {
            $tot_reserve_push_cnt = $this->app_push_model->get_tot_reserve_push_cnt();
        }

        //정렬
        $sort_array = array();
        $sort_array['ap_os_type'] = array("asc", "sorting");
        $sort_array['ap_subject'] = array("asc", "sorting");
        $sort_array['ap_message'] = array("asc", "sorting");
        $sort_array['ap_summary'] = array("asc", "sorting");
        $sort_array['ap_noti_type'] = array("asc", "sorting");
        $sort_array['ap_badge'] = array("asc", "sorting");
        $sort_array['ap_reserve_datetime'] = array("asc", "sorting");
        $sort_array['ap_target_url'] = array("asc", "sorting");
        $sort_array['ap_regdatetime'] = array("asc", "sorting");
        $sort_array['ap_proc_datetime'] = array("asc", "sorting");
        $sort_array['ap_state'] = array("asc", "sorting");
        $sort_array['ap_success_cnt'] = array("asc", "sorting");
        $sort_array['ap_fail_cnt'] = array("asc", "sorting");
        $sort_array['au_name'] = array("asc", "sorting");
        $sort_array['ap_display_state'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/app_push/app_push_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "app_push_list"     => $app_push_list,
            "pagination"        => $page_result['pagination'] ,
            "tot_reserve_push_cnt" => $tot_reserve_push_cnt
        ));
    }//end of app_push_list_ajax()

    /**
     * APP 푸시 통계 목록
     */
    public function app_push_stat() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/app_push/app_push_stat", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of app_push_stat()

    /**
     * APP 푸시 통계 목록 (Ajax)
     */
    public function app_push_stat_ajax() {
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
        $list_count = $this->app_push_model->get_app_push_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/app_push/stat_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $app_push_list = $this->app_push_model->get_app_push_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['ap_os_type'] = array("asc", "sorting");
        $sort_array['ap_subject'] = array("asc", "sorting");
        $sort_array['ap_message'] = array("asc", "sorting");
        $sort_array['ap_summary'] = array("asc", "sorting");
        $sort_array['ap_noti_type'] = array("asc", "sorting");
        $sort_array['ap_badge'] = array("asc", "sorting");
        $sort_array['ap_reserve_datetime'] = array("asc", "sorting");
        $sort_array['ap_target_url'] = array("asc", "sorting");
        $sort_array['ap_regdatetime'] = array("asc", "sorting");
        $sort_array['ap_proc_datetime'] = array("asc", "sorting");
        $sort_array['ap_state'] = array("asc", "sorting");
        $sort_array['ap_success_cnt'] = array("asc", "sorting");
        $sort_array['ap_fail_cnt'] = array("asc", "sorting");
        $sort_array['ap_receive_cnt'] = array("asc", "sorting");
        $sort_array['au_name'] = array("asc", "sorting");
        $sort_array['ap_display_state'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/app_push/app_push_stat_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "app_push_list"     => $app_push_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of app_push_stat_ajax()


    /**
     * APP 푸시 추가 (팝업)
     */
    public function app_push_insert_pop() {
        //request
        $req = $this->_list_req();
        $this->load->view("/app_push/app_push_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url()
        ));
    }//end of app_push_insert_pop()

    /**
     * APP 푸시 추가 처리 (Ajax)
     */
    public function app_push_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(

            //-- 190409 황기석 적립금관련 field 추가
            "ap_push_type" => array("field" => "ap_push_type", "label" => "푸시타입", "rules" => "required|in_list[".get_config_item_keys_string("app_push_type")."]|".$this->default_set_rules),
            "point_select" => array("field" => "point_select", "label" => "적립금", "rules" => $this->default_set_rules),
            //-- 190409 황기석 적립금관련 field 추가

            "ap_os_type" => array("field" => "ap_os_type", "label" => "OS타입", "rules" => "required|in_list[".get_config_item_keys_string("app_push_os_type")."]|".$this->default_set_rules),
            "ap_subject" => array("field" => "ap_subject", "label" => "푸시제목", "rules" => "required|max_length[100]|trim|xss_clean|prep_for_form"),
            "ap_message" => array("field" => "ap_message", "label" => "푸시내용", "rules" => "required|".$this->default_set_rules),
            "ap_summary" => array("field" => "ap_summary", "label" => "푸시요약내용", "rules" => $this->default_set_rules),

            "background_color" => array("field" => "background_color", "label" => "배경색상", "rules" => "exact_length[6]|" . $this->default_set_rules),
            "title_color" => array("field" => "title_color", "label" => "제목색상", "rules" => "exact_length[6]|" . $this->default_set_rules),
            "message_color" => array("field" => "message_color", "label" => "메시지색상", "rules" => "exact_length[6]|" . $this->default_set_rules),

            "ap_icon" => array("field" => "ap_icon", "label" => "아이콘이미지", "rules" => $this->default_set_rules),
            "ap_image" => array("field" => "ap_image", "label" => "푸시이미지", "rules" => $this->default_set_rules),
            //"ap_noti_type" => array("field" => "ap_noti_type", "label" => "알림타입", "rules" => "required|in_list[".get_config_item_keys_string("app_push_noti_type")."]|".$this->default_set_rules),
            "ap_badge" => array("field" => "ap_badge", "label" => "뱃지", "rules" => "required|in_list[".get_config_item_keys_string("app_push_badge")."]|".$this->default_set_rules),
            "ap_reserve_datetime" => array("field" => "ap_reserve_datetime", "label" => "예약일시", "rules" => $this->default_set_rules),
            "ap_target_url" => array("field" => "ap_target_url", "label" => "이동URL", "rules" => $this->default_set_rules),
            "ap_display_state" => array("field" => "ap_display_state", "label" => "노출여부", "rules" => "required|in_list[".get_config_item_keys_string("app_push_display_state")."]|".$this->default_set_rules),

            "ap_new_push" => array("field" => "ap_new_push", "label" => "푸시페이지 사용여부", "rules" => "required|in_list[Y,N]|".$this->default_set_rules),
            "ap_list_btn_msg" => array("field" => "ap_list_btn_msg", "label" => "푸시페이지 버튼 메시지", "rules" => $this->default_set_rules),
            "ap_list_comment" => array("field" => "ap_list_comment", "label" => "푸시페이지 코멘트", "rules" => $this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        /**
         * @date 190410
         * @modify 황기석
         * @desc 상품번호 추출 및 valid chk
         */
        $p_num = "0";
        if(empty($this->input->post('ap_target_url')) == false){
            $chkUrl = $this->input->post('ap_target_url');

            if( preg_match("/\/bit.ly\//i", $chkUrl) == true) { //비틀리쇼트너인 경우
                $resp = get_bitly_shorturl_info($chkUrl);
                $url = str_replace('http://m.cloma.co.kr','',$resp['long_url']);
            }else{ // 원문 URL 인경우
                $url = $chkUrl;
            }

            preg_match_all('/product\/detail\/(.*)\/\?ref_site\=app_push/',$url, $o);

            $p_num = $o[1][0];
            if(empty($p_num) == true) $p_num = 0;
        }

        if($this->input->post('ap_push_type') == 'product' && empty($p_num) == true){ //상품푸시인경우 url에서 상품번호 추출
            //상품번호 추출 validation
            result_echo_json(get_status_code('error'), '상품번호가 없습니다.', true, 'alert');
            exit;
        }
        /*------- 190410 End */


        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {

            //-- 190409 황기석 적립금관련 field 추가
            $ap_push_type = $this->input->post('ap_push_type', true);
            $point_select = $this->input->post('point_select', true);
            //-- 190409 황기석 적립금관련 field 추가

            $ap_os_type = $this->input->post('ap_os_type', true);
            $ap_subject = rawurlencode($this->input->post('ap_subject', true));
            $ap_message = rawurlencode($this->input->post('ap_message', true));
            $ap_summary = $this->input->post('ap_summary', true);

            $ap_new_push = $this->input->post('ap_new_push', true);
            $ap_list_comment = $this->input->post('ap_list_comment', true);
            $ap_list_btn_msg = $this->input->post('ap_list_btn_msg', true);

            $ap_style = "";
            $ap_style_array = array();
            $background_color = $this->input->post('background_color', true);
            $title_color = $this->input->post('title_color', true);
            $message_color = $this->input->post('message_color', true);
            if( !empty($background_color) ) {
                $ap_style_array['background_color'] = "#" . strtoupper(str_replace("#", "", $background_color));
            }
            if( !empty($title_color) ) {
                $ap_style_array['title_color'] = "#" . strtoupper(str_replace("#", "", $title_color));
            }
            if( !empty($message_color) ) {
                $ap_style_array['message_color'] = "#" . strtoupper(str_replace("#", "", $message_color));
            }
            if( !empty($ap_style_array) ) {
                $ap_style = json_encode_no_slashes($ap_style_array);
            }

            $ap_noti_type = $this->input->post('ap_noti_type', true);
            $ap_badge = $this->input->post('ap_badge', true);
            $ap_reserve_datetime = $this->input->post('ap_reserve_datetime', true);
            $ap_display_state = $this->input->post('ap_display_state', true);
            $ap_target_url = $this->input->post('ap_target_url', true);
            $ap_icon = "";
            $ap_image = "";

            //아이콘 이미지 업로드
            if( isset($_FILES['ap_icon']['name']) && !empty($_FILES['ap_icon']['name']) ) {
                $ap_image_path_web = $this->config->item('app_push_image_path_web') . "/" . date("Y") . "/" . date("md");
                $ap_image_path = $this->config->item('app_push_image_path') . "/" . date("Y") . "/" . date("md");
                create_directory($ap_image_path);

                $config = array();
                $config['upload_path'] = $ap_image_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '5000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('ap_icon') ) {
                    $ap_icon_data_array = $this->upload->data();
                    $ap_icon = $ap_image_path_web . "/" . $ap_icon_data_array['file_name'];
                }
                else {
                    $form_error_array['ap_icon'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }

            //이미지 업로드
            if( isset($_FILES['ap_image']['name']) && !empty($_FILES['ap_image']['name']) ) {
                $ap_image_path_web = $this->config->item('app_push_image_path_web') . "/" . date("Y") . "/" . date("md");
                $ap_image_path = $this->config->item('app_push_image_path') . "/" . date("Y") . "/" . date("md");
                create_directory($ap_image_path);

                $config = array();
                $config['upload_path'] = $ap_image_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '5000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('ap_image') ) {
                    $ap_image_data_array = $this->upload->data();
                    $ap_image = $ap_image_path_web . "/" . $ap_image_data_array['file_name'];
                }
                else {
                    $form_error_array['ap_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['ap_os_type'] = $ap_os_type;
                $query_data['ap_subject'] = $ap_subject;
                $query_data['ap_message'] = $ap_message;
                $query_data['ap_summary'] = $ap_summary;
                $query_data['ap_style'] = $ap_style;
                $query_data['ap_icon'] = $ap_icon;
                $query_data['ap_image'] = $ap_image;
                $query_data['ap_noti_type'] = $ap_noti_type;
                $query_data['ap_badge'] = $ap_badge;
                $query_data['ap_reserve_datetime'] = number_only($ap_reserve_datetime);
                $query_data['ap_target_url'] = $ap_target_url;
                $query_data['ap_display_state'] = $ap_display_state;
                $query_data['ap_pnum'] = $p_num;

                $query_data['ap_new_push'] = $ap_new_push;
                $query_data['ap_list_comment'] = $ap_list_comment;
                $query_data['ap_list_btn_msg'] = $ap_list_btn_msg;

                //-- 190409 황기석 적립금관련 field 추가
                $query_data['ap_push_type']     = $ap_push_type;
                if($query_data['ap_push_type'] == 'point'){

                    $this->load->helper('string');
                    $query_data['ap_point_authkey'] = random_string('sha1');
                    $query_data['ap_ptuid']         = $point_select;
                };
                //-- 190409 황기석 적립금관련 field 추가

                if( $this->app_push_model->insert_app_push($query_data) ) {
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
    }//end of app_push_insert_proc()

    /**
     * APP 푸시 수정
     */
    public function app_push_update_pop() {
        //request
        $req = $this->_list_req();
        $req['ap_num'] = $this->input->post_get('ap_num', true);

        //row
        $app_push_row = $this->app_push_model->get_app_push_row($req['ap_num']);

        if( empty($app_push_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $background_color = "";
        $title_color = "";
        $message_color = "";
        if( !empty($app_push_row['ap_style']) ) {
            $ap_style_array = json_decode($app_push_row['ap_style'], true);
            if( isset($ap_style_array['background_color']) && !empty($ap_style_array['background_color']) ) {
                $background_color = str_replace("#", "", $ap_style_array['background_color']);
            }
            if( isset($ap_style_array['title_color']) && !empty($ap_style_array['title_color']) ) {
                $title_color = str_replace("#", "", $ap_style_array['title_color']);
            }
            if( isset($ap_style_array['message_color']) && !empty($ap_style_array['message_color']) ) {
                $message_color = str_replace("#", "", $ap_style_array['message_color']);
            }
        }

        $this->load->view("/app_push/app_push_update_pop", array(
            'req'               => $req,
            'app_push_row'      => $app_push_row,
            'background_color'  => $background_color,
            'title_color'       => $title_color,
            'message_color'     => $message_color,
            'list_url'          => $this->_get_list_url()
        ));
    }//end of app_push_update_pop()

    /**
     * APP 푸시 수정 처리 (Ajax)
     */
    public function app_push_update_proc() {


        ajax_request_check();


        //request
        $req['ap_num'] = $this->input->post_get('ap_num', true);

        //row
        $app_push_row = $this->app_push_model->get_app_push_row($req['ap_num']);

        if( empty($app_push_row) ) {
            alert(lang('site_error_empty_data'));
        }


        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "ap_num" => array("field" => "ap_num", "label" => "번호", "rules" => "required|is_natural|".$this->default_set_rules),

            //-- 190409 황기석 적립금관련 field 추가
            "ap_push_type" => array("field" => "ap_push_type", "label" => "푸시타입", "rules" => "required|in_list[".get_config_item_keys_string("app_push_type")."]|".$this->default_set_rules),
            "point_select" => array("field" => "point_select", "label" => "적립금", "rules" => $this->default_set_rules),
            //-- 190409 황기석 적립금관련 field 추가

            "ap_os_type" => array("field" => "ap_os_type", "label" => "OS타입", "rules" => "required|in_list[".get_config_item_keys_string("app_push_os_type")."]|".$this->default_set_rules),
            "ap_subject" => array("field" => "ap_subject", "label" => "푸시제목", "rules" => "required|max_length[100]|trim|xss_clean|prep_for_form"),
            "ap_message" => array("field" => "ap_message", "label" => "푸시내용", "rules" => "required|".$this->default_set_rules),
            "ap_summary" => array("field" => "ap_summary", "label" => "푸시요약내용", "rules" => $this->default_set_rules),

            "background_color" => array("field" => "background_color", "label" => "배경색상", "rules" => "exact_length[6]|" . $this->default_set_rules),
            "title_color" => array("field" => "title_color", "label" => "제목색상", "rules" => "exact_length[6]|" . $this->default_set_rules),
            "message_color" => array("field" => "message_color", "label" => "메시지색상", "rules" => "exact_length[6]|" . $this->default_set_rules),

            "ap_icon" => array("field" => "ap_icon", "label" => "아이콘이미지", "rules" => $this->default_set_rules),
            "ap_image" => array("field" => "ap_image", "label" => "푸시이미지", "rules" => $this->default_set_rules),
            //"ap_noti_type" => array("field" => "ap_noti_type", "label" => "알림타입", "rules" => "required|in_list[".get_config_item_keys_string("app_push_noti_type")."]|".$this->default_set_rules),
            "ap_badge" => array("field" => "ap_badge", "label" => "뱃지", "rules" => "required|in_list[".get_config_item_keys_string("app_push_badge")."]|".$this->default_set_rules),
            "ap_reserve_datetime" => array("field" => "ap_reserve_datetime", "label" => "예약일시", "rules" => $this->default_set_rules),
            "ap_target_url" => array("field" => "ap_target_url", "label" => "이동URL", "rules" => $this->default_set_rules),
            //"ap_state" => array("field" => "ap_state", "label" => "발송상태", "rules" => "required|in_list[".get_config_item_keys_string("app_push_state")."]|" . $this->default_set_rules),
            "ap_display_state" => array("field" => "ap_display_state", "label" => "노출여부", "rules" => "required|in_list[".get_config_item_keys_string("app_push_display_state")."]|".$this->default_set_rules),

            "ap_new_push" => array("field" => "ap_new_push", "label" => "푸시페이지 사용여부", "rules" => "required|in_list[Y,N]|".$this->default_set_rules),
            "ap_list_btn_msg" => array("field" => "ap_list_btn_msg", "label" => "푸시페이지 버튼 메시지", "rules" => $this->default_set_rules),
            "ap_stock_flag" => array("field" => "ap_stock_flag", "label" => "재고여부", "rules" => "required|in_list[".get_config_item_keys_string("app_stock_flag")."]|".$this->default_set_rules),
            "ap_list_comment" => array("field" => "ap_list_comment", "label" => "푸시페이지 코멘트", "rules" => $this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        /**
         * @date 190410
         * @modify 황기석
         * @desc 상품번호 추출 및 valid chk
         */

        $p_num = "0";
        if(empty($this->input->post('ap_target_url')) == false){
            $chkUrl = $this->input->post('ap_target_url');

            if( preg_match("/\/bit.ly\//i", $chkUrl) == true) { //비틀리쇼트너인 경우
                $resp = get_bitly_shorturl_info($chkUrl);
                $url = str_replace('http://m.cloma.co.kr','',$resp['long_url']);
            }else{ // 원문 URL 인경우
                $url = $chkUrl;
            }

            preg_match_all('/product\/detail\/(.*)\/\?ref_site\=app_push/',$url, $o);

            $p_num = $o[1][0];
            if(empty($p_num) == true) $p_num = 0;
        }

        if($this->input->post('ap_push_type') == 'product' && empty($p_num) == true){ //상품푸시인경우 url에서 상품번호 추출
            //상품번호 추출 validation
            result_echo_json(get_status_code('error'), '상품번호가 없습니다.', true, 'alert');
            exit;
        }
        /*------- 190410 End */

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {

            $ap_num = $this->input->post('ap_num', true);

            //-- 190409 황기석 적립금관련 field 추가
            $ap_push_type = $this->input->post('ap_push_type', true);
            $point_select = $this->input->post('point_select', true);
            //-- 190409 황기석 적립금관련 field 추가

            $ap_os_type = $this->input->post('ap_os_type', true);
            $ap_subject = rawurlencode($this->input->post('ap_subject', true));
            $ap_message = rawurlencode($this->input->post('ap_message', true));
            $ap_summary = $this->input->post('ap_summary', true);
            $ap_stock_flag = $this->input->post('ap_stock_flag', true);



            $ap_new_push = $this->input->post('ap_new_push', true);
            $ap_list_comment = $this->input->post('ap_list_comment', true);
            $ap_list_btn_msg = $this->input->post('ap_list_btn_msg', true);


            $ap_style = "";
            $ap_style_array = array('background_color' => '', 'title_color' => '', 'message_color' => '');
            $background_color = $this->input->post('background_color', true);
            $title_color = $this->input->post('title_color', true);
            $message_color = $this->input->post('message_color', true);
            if( !empty($background_color) ) {
                $ap_style_array['background_color'] = "#" . strtoupper(str_replace("#", "", $background_color));
            }
            else {
                unset($ap_style_array['background_color']);
            }
            if( !empty($title_color) ) {
                $ap_style_array['title_color'] = "#" . strtoupper(str_replace("#", "", $title_color));
            }
            else {
                unset($ap_style_array['title_color']);
            }
            if( !empty($message_color) ) {
                $ap_style_array['message_color'] = "#" . strtoupper(str_replace("#", "", $message_color));
            }
            else {
                unset($ap_style_array['message_color']);
            }

            if( !empty($ap_style_array) ) {
                $ap_style = json_encode_no_slashes($ap_style_array);
            }

            $ap_noti_type = $this->input->post('ap_noti_type', true);
            $ap_badge = $this->input->post('ap_badge', true);
            $ap_reserve_datetime = $this->input->post('ap_reserve_datetime', true);
            $ap_display_state = $this->input->post('ap_display_state', true);
            $ap_target_url = $this->input->post('ap_target_url', true);
            //$ap_state = $this->input->post('ap_state', true);
            $ap_icon = "";
            $ap_image = "";

            //아이콘 이미지 업로드
            if( isset($_FILES['ap_icon']['name']) && !empty($_FILES['ap_icon']['name']) ) {
                $ap_image_path_web = $this->config->item('app_push_image_path_web') . "/" . date("Y") . "/" . date("md");
                $ap_image_path = $this->config->item('app_push_image_path') . "/" . date("Y") . "/" . date("md");
                create_directory($ap_image_path);

                $config = array();
                $config['upload_path'] = $ap_image_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '5000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('ap_icon') ) {
                    $ap_icon_data_array = $this->upload->data();
                    $ap_icon = $ap_image_path_web . "/" . $ap_icon_data_array['file_name'];
                    //기존 이미지 삭제
                    file_delete(1, str_replace(array($this->config->item("site_http"), $this->config->item("site_img_http")), "", $app_push_row['ap_icon']), DOCROOT);
                }
                else {
                    $form_error_array['ap_icon'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }

            //이미지 업로드
            if( isset($_FILES['ap_image']['name']) && !empty($_FILES['ap_image']['name']) ) {
                $ap_image_path_web = $this->config->item('app_push_image_path_web') . "/" . date("Y") . "/" . date("md");
                $ap_image_path = $this->config->item('app_push_image_path') . "/" . date("Y") . "/" . date("md");
                create_directory($ap_image_path);

                $config = array();
                $config['upload_path'] = $ap_image_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '5000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('ap_image') ) {
                    $ap_image_data_array = $this->upload->data();
                    $ap_image = $ap_image_path_web . "/" . $ap_image_data_array['file_name'];

                    //기존 이미지 삭제
                    file_delete(1, str_replace(array($this->config->item("site_http"), $this->config->item("site_img_http")), "", $app_push_row['ap_image']), DOCROOT);
                }
                else {
                    $form_error_array['ap_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }



            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['ap_os_type'] = $ap_os_type;
                $query_data['ap_subject'] = $ap_subject;
                $query_data['ap_message'] = $ap_message;
                $query_data['ap_summary'] = $ap_summary;
                $query_data['ap_style'] = $ap_style;
                if ( !empty($ap_icon) ) {
                    $query_data['ap_icon'] = $ap_icon;
                }
                if ( !empty($ap_image) ) {
                    $query_data['ap_image'] = $ap_image;
                }
                $query_data['ap_noti_type'] = $ap_noti_type;
                $query_data['ap_badge'] = $ap_badge;
                $query_data['ap_reserve_datetime'] = number_only($ap_reserve_datetime);
                $query_data['ap_target_url'] = $ap_target_url;
                //$query_data['ap_state'] = $ap_state;
                $query_data['ap_display_state'] = $ap_display_state;
                $query_data['ap_pnum'] = $p_num;
                $query_data['ap_new_push'] = $ap_new_push;
                $query_data['ap_list_comment'] = $ap_list_comment;
                $query_data['ap_list_btn_msg'] = $ap_list_btn_msg;

                //-- 190409 황기석 적립금관련 field 추가
                $query_data['ap_push_type']     = $ap_push_type;

                $query_data['ap_stock_flag']    = $ap_stock_flag;
                $query_data['ap_point_authkey'] = $app_push_row['ap_point_authkey'];

                if($query_data['ap_push_type'] == 'point' && empty($app_push_row['ap_point_authkey'])){
                    $this->load->helper('string');
                    $query_data['ap_point_authkey'] = random_string('sha1');
                    $query_data['ap_ptuid']         = $point_select;
                }else if($query_data['ap_push_type'] != 'point'){
                    $query_data['ap_point_authkey'] = '';
                    $query_data['ap_ptuid']         = 0;
                }


                //-- 190409 황기석 적립금관련 field 추가

                if( $this->app_push_model->update_app_push($ap_num, $query_data) ) {
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
    }//end of app_push_update_proc()

    /**
     * APP 푸시 삭제 처리 (Ajax)
     */
    public function app_push_delete_proc() {
        ajax_request_check();

        //request
        $req['ap_num'] = $this->input->post_get('ap_num', true);

        //APP 푸시 정보
        $app_push_row = $this->app_push_model->get_app_push_row($req['ap_num']);

        if( empty($app_push_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //발송중, 발송완료 상태는 삭제 불가
        if( $app_push_row['ap_state'] != '1' ) {
            result_echo_json(get_status_code('error'), '발송중, 발송완료 상태인 푸시는 삭제가 불가능합니다.', true, 'alert');
        }

        //APP 푸시 삭제
        if( $this->app_push_model->delete_app_push($req['ap_num']) ) {
            //이미지 삭제
            file_delete(1, str_replace(array($this->config->item("site_http"), $this->config->item("site_img_http")), "", $app_push_row['ap_icon']), DOCROOT);
            file_delete(1, str_replace(array($this->config->item("site_http"), $this->config->item("site_img_http")), "", $app_push_row['ap_image']), DOCROOT);

            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of app_push_delete_proc()

    /**
     * 이미지 삭제
     */
    public function app_push_image_delete_proc() {
        ajax_request_check();

        //request
        $req['ap_num'] = $this->input->post_get('ap_num', true);
        $req['fd'] = $this->input->post_get('fd', true);            //icon|image

        if( empty($req['fd']) ) {
            $req['fd'] = 'image';
        }

        $allow_fd = array("image", "icon");

        if( !in_array($req['fd'], $allow_fd) ) {
            result_echo_json(get_status_code('error'), lang('site_error_default'), true, 'alert');
        }

        //APP 푸시 정보
        $app_push_row = $this->app_push_model->get_app_push_row($req['ap_num']);

        if( empty($app_push_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        $field = 'ap_' . $req['fd'];

        if( $this->app_push_model->image_delete_app_push($app_push_row['ap_num'], $field) ) {
            //이미지 삭제
            file_delete(1, str_replace(array($this->config->item("site_http"), $this->config->item("site_img_http")), "", $app_push_row[$field]), DOCROOT);

            result_echo_json(get_status_code('success'), "", true);
        }
        else {
            result_echo_json(get_status_code('error'), "", true);
        }
    }//end of app_push_image_delete_proc()

    /**
     * 노출상태 변경
     */
    public function app_push_display_toggle() {
        ajax_request_check();

        //request
        $req['ap_num'] = $this->input->post_get('ap_num', true);

        //APP 푸시 정보
        $push_row = $this->app_push_model->get_app_push_row($req['ap_num']);

        if( empty($push_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        if( $push_row->ap_display_state == "Y" ) {
            $ap_display_state = "N";
        }
        else {
            $ap_display_state = "Y";
        }

        $query_data = array();
        $query_data['ap_display_state'] = $ap_display_state;
        if( $this->app_push_model->update_app_push_info($push_row->ap_num, $query_data) ) {
            result_echo_json(get_status_code('success'), "", true);
        }
        else {
            result_echo_json(get_status_code('error'), "", true);
        }
    }//end of app_push_display_toggle()

    public function getProductViewDetail(){

        ajax_request_check();

        //request
        $aInput = array('ap_num' => $this->input->post('ap_num', true) );


        $sql = "
            SELECT 
                A.apv_cnt
              , B.p_name
            FROM app_push_product_view_tb A
            INNER JOIN product_tb B on A.p_num = B.p_num
            WHERE ap_num = '{$aInput['ap_num']}'
            ORDER BY apv_cnt DESC, B.p_name ASC     
            ; 
        ";

        //zsView($sql);

        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        result_echo_json(get_status_code('success'), '', true, '','',$aResult);

    }

    /**
     * 푸시 테스트 발송 (ajax)
     */
    public function app_push_test_send() {
        ajax_request_check();

        //request
        $req['ap_num'] = $this->input->post('ap_num', true);
        $req['adm_num'] = $this->input->post('adm_num');        //배열

        //푸시 정보
        $push_row = $this->app_push_model->get_app_push_row($req['ap_num']);

        //회원 regid
        $sql = "
            select
                m_regid, m_device_model
            from member_tb
            where
                m_num in ('" . implode("','", $req['adm_num']) . "')
        ";
        $regid_list = $this->db->query($sql)->result_array();

        if( empty($regid_list) ) {
            echo page_request_return(get_status_code("error"), "FCM ID 없음!!", true);
        }

        /* 푸시 렌딩페이지 변경 End*/

        $return_data = array();

        foreach($regid_list as $k => $r) {
            $fields                         = array();
            $fields['title']        = rawurldecode($push_row['ap_subject']);     //제목
            $fields['body']         = rawurldecode($push_row['ap_message']);    //내용
            $fields['seq']          = $push_row['ap_num'];      //내용
            $fields['badge']        = 'Y';                      //뱃지올리기여부(Y/N)
            $fields['seq']          = $push_row['ap_pnum'];     //상품번호
            $fields['app_push_id']  = $push_row['ap_num'];      //ap_num

            if($push_row['ap_new_push'] == 'Y') $fields['page']     = 'push';
            else $fields['page']     = 'product';

            //Android
            $result = send_app_push($r['m_regid'], $fields);
            $return_data[] = $result;
        }

        $return_data['url']=$ap_target_url;

        echo json_encode_no_slashes($return_data);
    }//end app_push_test_send;

}//end of class App_push