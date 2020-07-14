<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * APP 팝업 관리 컨트롤러
 */
class App_popup extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('app_popup_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->app_popup_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['date_type']       = trim($this->input->post_get('date_type', true));
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));

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
     * APP 팝업 목록
     */
    public function app_popup_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/app_popup/app_popup_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of app_popup_list()

    /**
     * APP 팝업 목록 (Ajax)
     */
    public function app_popup_list_ajax() {
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
        $list_count = $this->app_popup_model->get_app_popup_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/app_popup/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $app_popup_list = $this->app_popup_model->get_app_popup_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['apo_os_type'] = array("asc", "sorting");
        $sort_array['apo_pop_type'] = array("asc", "sorting");
        $sort_array['apo_subject'] = array("asc", "sorting");
        $sort_array['apo_url'] = array("asc", "sorting");
        $sort_array['apo_termlimit_yn'] = array("asc", "sorting");
        $sort_array['apo_termlimit_datetime1'] = array("asc", "sorting");
        $sort_array['apo_termlimit_datetime2'] = array("asc", "sorting");
        $sort_array['apo_regdatetime'] = array("asc", "sorting");
        //$sort_array['apo_click_count'] = array("asc", "sorting");
        //$sort_array['apo_close_count'] = array("asc", "sorting");
        $sort_array['apo_fail_cnt'] = array("asc", "sorting");
        $sort_array['apo_display_yn'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $view_file = "/app_popup/app_popup_list_ajax";

        $this->load->view($view_file, array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "app_popup_list"    => $app_popup_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of app_popup_list_ajax()

    /**
     * APP 팝업 추가 (팝업)
     */
    public function app_popup_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->model('special_offer_model');
        $aSpecialOfferLists = $this->special_offer_model->get_special_offer_list();

        $this->load->view("/app_popup/app_popup_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url(),
            'aSpecialOfferLists'    => $aSpecialOfferLists
        ));
    }//end of app_popup_insert_pop()

    /**
     * APP 팝업 추가 처리 (Ajax)
     */
    public function app_popup_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        $set_rules_apo_p_num = $this->default_set_rules;
        $set_rules_apo_special_offer_seq = $this->default_set_rules;
        $set_rules_apo_noti_content = $this->default_set_rules;
        $set_rules_apo_noti_subject = $this->default_set_rules;

        //상품상세로 이동 일때 => 상품번호 필수
        if( $this->input->post("apo_content_type", true) == "1" ) {
            $set_rules_apo_p_num .= "|required";
        }
        //기획전인경우 => 기획전 seq 필수
        else if( $this->input->post("apo_content_type", true) == "2" ) {
            $set_rules_apo_special_offer_seq .= "|required";
        }
        //공지사항인경우 => 공지내용 필수
        else if( $this->input->post("apo_content_type", true) == "3" ) {
            $set_rules_apo_noti_content .= "|required";
//            $set_rules_apo_noti_subject .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "apo_os_type" => array("field" => "apo_os_type", "label" => "OS타입", "rules" => "required|in_list[".get_config_item_keys_string("app_popup_os_type")."]|".$this->default_set_rules),
            "apo_subject" => array("field" => "apo_subject", "label" => "팝업제목", "rules" => "required|max_length[100]|trim|xss_clean|prep_for_form"),
            "apo_size_type" => array("field" => "apo_size_type", "label" => "팝업사이즈", "rules" => "required|in_list[".get_config_item_keys_string("app_popup_size_type")."]|".$this->default_set_rules),
            "apo_content_type" => array("field" => "apo_content_type", "label" => "콘텐츠타입", "rules" => "required|in_list[".get_config_item_keys_string("app_popup_content_type")."]|".$this->default_set_rules),
            "apo_btn_type" => array("field" => "apo_btn_type", "label" => "버튼타입", "rules" => "required|in_list[".get_config_item_keys_string("app_popup_btn_type")."]|".$this->default_set_rules),
            "apo_view_target" => array("field" => "apo_view_target", "label" => "노출대상", "rules" => "required|in_list[".get_config_item_keys_string("app_popup_view_target")."]|".$this->default_set_rules),
            "apo_product_num" => array("field" => "apo_product_num", "label" => "상품번호", "rules" => $set_rules_apo_p_num),
            "apo_special_offer_seq" => array("field" => "apo_special_offer_seq", "label" => "기획전seq", "rules" => $set_rules_apo_special_offer_seq),
            "apo_image" => array("field" => "apo_image", "label" => "팝업이미지", "rules" => $this->default_set_rules),
            "apo_termlimit_yn" => array("field" => "apo_termlimit_yn", "label" => "노출기간사용여부", "rules" => $this->default_set_rules),
            "apo_startdate" => array("field" => "apo_startdate", "label" => "시작일", "rules" => $this->default_set_rules),
            "apo_enddate" => array("field" => "apo_enddate", "label" => "종료일", "rules" => $this->default_set_rules),

            "apo_noti_subject" => array("field" => "apo_noti_subject", "label" => "공지제목", "rules" => $set_rules_apo_noti_subject),
            "apo_noti_content" => array("field" => "apo_noti_content", "label" => "공지내용", "rules" => $set_rules_apo_noti_content),

            "apo_display_yn" => array("field" => "apo_display_yn", "label" => "팝업노출여부", "rules" => "required|in_list[".get_config_item_keys_string("app_popup_display_yn")."]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        if(empty($_FILES['apo_image']) == true && $this->input->post('apo_content_type', true) != 3) $form_error_array['apo_image'] = '팝업에 노출할 이미지를 등록해주세요';

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $apo_position = $this->input->post('apo_position', true);
            $apo_os_type = $this->input->post('apo_os_type', true);
            $apo_content_type = $this->input->post('apo_content_type', true);
            $apo_btn_type = $this->input->post('apo_btn_type', true);
            $apo_expire_day = $this->input->post('apo_expire_day', true);
            $apo_subject = $this->input->post('apo_subject', true);

            $apo_noti_subject = $this->input->post('apo_noti_subject', true);
            $apo_noti_content = $this->input->post('apo_noti_content', true);

            $apo_size_type = $this->input->post('apo_size_type', true);
            $apo_p_num = $this->input->post('apo_product_num', true);
            $apo_url = $this->input->post('apo_url', true);
            $apo_termlimit_yn = $this->input->post('apo_termlimit_yn', true);
            $apo_startdate = $this->input->post('apo_startdate', true);
            $apo_enddate = $this->input->post('apo_enddate', true);
            if( $apo_termlimit_yn == "N" ) {
                $apo_startdate = "";
                $apo_enddate = "";
            }
            $apo_display_yn = $this->input->post('apo_display_yn', true);
            $apo_view_page_arr = $this->input->post('apo_view_page', true);
            $apo_view_target = $this->input->post('apo_view_target', true);
            $apo_special_offer_seq = $this->input->post('apo_special_offer_seq', true);

            if( empty($apo_view_page_arr) == true ) {
                $form_error_array['apo_view_page'] = '노출페이지를 설정해주세요';
            }
            else {
                $apo_view_page = implode(',', $apo_view_page_arr);
            }

            $apo_image = "";

            //이미지 업로드
            if( isset($_FILES['apo_image']['name']) && !empty($_FILES['apo_image']['name']) ) {
                $apo_image_path_web = $this->config->item('app_popup_image_path_web') . "/" . date("Y") . "/" . date("md");
                $apo_image_path = $this->config->item('app_popup_image_path') . "/" . date("Y") . "/" . date("md");
                create_directory($apo_image_path);

                $config = array();
                $config['upload_path'] = $apo_image_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '5000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('apo_image') ) {
                    $apo_image_data_array = $this->upload->data();
                    $apo_image = $apo_image_path_web . "/" . $apo_image_data_array['file_name'];

                    //cdn purge
                    cdn_purge($apo_image_path_web . "/" . $apo_image_data_array['file_name']);
                }
                else {
                    $form_error_array['apo_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['apo_position'] = $apo_position;
                $query_data['apo_os_type'] = $apo_os_type;
                $query_data['apo_size_type'] = $apo_size_type;
                $query_data['apo_content_type'] = $apo_content_type;
                $query_data['apo_btn_type'] = $apo_btn_type;
                $query_data['apo_expire_day'] = $apo_expire_day;
                $query_data['apo_subject'] = $apo_subject;
                $query_data['apo_image'] = $apo_image;
                $query_data['apo_p_num'] = $apo_content_type != 1 ? 0 : $apo_p_num;
                $query_data['apo_special_offer_seq'] = $apo_content_type != 2 ? 0 : $apo_special_offer_seq;
                $query_data['apo_termlimit_yn'] = $apo_termlimit_yn;
                $query_data['apo_termlimit_datetime1'] = $apo_startdate;
                $query_data['apo_termlimit_datetime2'] = $apo_enddate;
                $query_data['apo_display_yn'] = $apo_display_yn;

                $query_data['apo_noti_subject'] = $apo_noti_subject;
                $query_data['apo_noti_content'] = $apo_noti_content;

                $query_data['apo_view_page'] = $apo_view_page;
                $query_data['apo_view_target'] = $apo_view_target;
                if( $this->app_popup_model->insert_app_popup($query_data) ) {
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
    }//end of app_popup_insert_proc()

    /**
     * APP 팝업 수정
     */
    public function app_popup_update_pop() {

        $this->load->model('product_model');
        //request
        $req = $this->_list_req();
        $req['apo_num'] = $this->input->post_get('apo_num', true);

        //row
        $app_popup_row = $this->app_popup_model->get_app_popup_row($req['apo_num']);

        $product_row = $this->product_model->get_product_row($app_popup_row->apo_p_num);

        if( empty($app_popup_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->model('special_offer_model');
        $aSpecialOfferLists = $this->special_offer_model->get_special_offer_list();

        $special_offer_info = $this->special_offer_model->get_special_offer_info($app_popup_row->apo_special_offer_seq);
        $special_offer_row = $special_offer_info['special_offer_row'];

        $this->load->view("/app_popup/app_popup_update_pop", array(
            'req'               => $req,
            'app_popup_row'     => $app_popup_row,
            'list_url'          => $this->_get_list_url(),
            'product_name'      => $product_row['p_name'],
            'thema_name'        => $special_offer_row['thema_name'],
            'aSpecialOfferLists'    => $aSpecialOfferLists
        ));
    }//end of app_popup_update_pop()

    
    /**
     * APP 팝업 수정 처리 (Ajax)
     */
    public function app_popup_update_proc() {
        ajax_request_check();

        //request
        $req['apo_num'] = $this->input->post_get('apo_num', true);

        //row
        $row = $this->app_popup_model->get_app_popup_row($req['apo_num']);

        if( empty($row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->library('form_validation');

        $set_rules_apo_p_num = $this->default_set_rules;
        $set_rules_apo_special_offer_seq = $this->default_set_rules;
        $set_rules_apo_noti_subject = $this->default_set_rules;
        $set_rules_apo_noti_content = 'trim|xss_clean|prep_for_form';//$this->default_set_rules;
        //상품상세로 이동 일때 => 상품번호 필수
        if( $this->input->post("apo_content_type", true) == "1" ) {
            $set_rules_apo_p_num .= "|required";
        }
        //기획전인경우 => 기획전 seq 필수
        else if( $this->input->post("apo_content_type", true) == "2" ) {
            $set_rules_apo_special_offer_seq .= "|required";
        }
        //공지사항인경우 => 공지내용 필수
        else if( $this->input->post("apo_content_type", true) == "3" ) {
            $set_rules_apo_noti_content .= "|required";
//            $set_rules_apo_noti_subject .= "|required";
        }
        //폼검증 룰 설정
        $set_rules_array = array(
            "apo_os_type" => array("field" => "apo_os_type", "label" => "OS타입", "rules" => "required|in_list[".get_config_item_keys_string("app_popup_os_type")."]|".$this->default_set_rules),
            "apo_subject" => array("field" => "apo_subject", "label" => "팝업제목", "rules" => "required|max_length[100]|trim|xss_clean|prep_for_form"),
            "apo_size_type" => array("field" => "apo_size_type", "label" => "팝업사이즈", "rules" => "required|in_list[".get_config_item_keys_string("app_popup_size_type")."]|".$this->default_set_rules),
            "apo_content_type" => array("field" => "apo_content_type", "label" => "콘텐츠타입", "rules" => "required|in_list[".get_config_item_keys_string("app_popup_content_type")."]|".$this->default_set_rules),
            "apo_btn_type" => array("field" => "apo_btn_type", "label" => "버튼타입", "rules" => "required|in_list[".get_config_item_keys_string("app_popup_btn_type")."]|".$this->default_set_rules),
            "apo_view_target" => array("field" => "apo_view_target", "label" => "노출대상", "rules" => "required|in_list[".get_config_item_keys_string("app_popup_view_target")."]|".$this->default_set_rules),
            "apo_product_num" => array("field" => "apo_product_num", "label" => "상품번호", "rules" => $set_rules_apo_p_num),
            "apo_special_offer_seq" => array("field" => "apo_special_offer_seq", "label" => "기획전seq", "rules" => $set_rules_apo_special_offer_seq),
            "apo_image" => array("field" => "apo_image", "label" => "팝업이미지", "rules" => $this->default_set_rules),
            "apo_termlimit_yn" => array("field" => "apo_termlimit_yn", "label" => "노출기간사용여부", "rules" => $this->default_set_rules),
            "apo_startdate" => array("field" => "apo_startdate", "label" => "시작일", "rules" => $this->default_set_rules),
            "apo_enddate" => array("field" => "apo_enddate", "label" => "종료일", "rules" => $this->default_set_rules),
            "apo_display_yn" => array("field" => "apo_display_yn", "label" => "팝업노출여부", "rules" => "required|in_list[".get_config_item_keys_string("app_popup_display_yn")."]|".$this->default_set_rules),

            "apo_noti_subject" => array("field" => "apo_noti_subject", "label" => "공지제목", "rules" => $set_rules_apo_noti_subject),
            "apo_noti_content" => array("field" => "apo_noti_content", "label" => "공지내용", "rules" => $set_rules_apo_noti_content)

        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        if(empty($row->apo_image) == true && empty($_FILES['apo_image']) == true && $this->input->post('apo_content_type', true) != 3) $form_error_array['apo_image'] = '팝업에 노출할 이미지를 등록해주세요';
        //if(empty($row->apo_image) == true && empty($_FILES['apo_image']) == true) $form_error_array['apo_image'] = '팝업에 노출할 이미지를 등록해주세요';

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $apo_num = $this->input->post('apo_num', true);
            $apo_position = $this->input->post('apo_position', true);
            $apo_os_type = $this->input->post('apo_os_type', true);
            $apo_content_type = $this->input->post('apo_content_type', true);
            $apo_btn_type = $this->input->post('apo_btn_type', true);
            $apo_expire_day = $this->input->post('apo_expire_day', true);
            $apo_subject = $this->input->post('apo_subject', true);
            $apo_size_type = $this->input->post('apo_size_type', true);
            $apo_p_num = $this->input->post('apo_product_num', true);
            $apo_termlimit_yn = $this->input->post('apo_termlimit_yn', true);
            $apo_startdate = $this->input->post('apo_startdate', true);
            $apo_enddate = $this->input->post('apo_enddate', true);
            if( $apo_termlimit_yn == "N" ) {
                $apo_startdate = "";
                $apo_enddate = "";
            }
            $apo_display_yn = $this->input->post('apo_display_yn', true);
            $apo_view_page_arr = $this->input->post('apo_view_page', true);
            $apo_view_target = $this->input->post('apo_view_target', true);
            $apo_special_offer_seq = $this->input->post('apo_special_offer_seq', true);

            $apo_noti_subject = $this->input->post('apo_noti_subject', true);
            $apo_noti_content = $this->input->post('apo_noti_content', true);

            if( empty($apo_view_page_arr) == true ) {
                $form_error_array['apo_view_page'] = '노출페이지를 설정해주세요';
            }
            else {
                $apo_view_page = implode(',',$apo_view_page_arr);
            }

            $apo_image = "";

            if( isset($_FILES['apo_image']['name']) && !empty($_FILES['apo_image']['name']) ) {
                $apo_image_path_web = $this->config->item('app_popup_image_path_web') . "/" . date("Y") . "/" . date("md");
                $apo_image_path = $this->config->item('app_popup_image_path') . "/" . date("Y") . "/" . date("md");
                create_directory($apo_image_path);

                $config = array();
                $config['upload_path'] = $apo_image_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '5000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('apo_image') ) {
                    $apo_image_data_array = $this->upload->data();
                    $apo_image = $apo_image_path_web . "/" . $apo_image_data_array['file_name'];

                }
                else {
                    $form_error_array['apo_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }


            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['apo_position'] = $apo_position;
                $query_data['apo_os_type'] = $apo_os_type;
                $query_data['apo_size_type'] = $apo_size_type;
                $query_data['apo_content_type'] = $apo_content_type;
                $query_data['apo_btn_type'] = $apo_btn_type;
                $query_data['apo_expire_day'] = $apo_expire_day;
                $query_data['apo_subject'] = $apo_subject;

                if ( !empty($apo_image) ) {
                    $query_data['apo_image'] = $apo_image;
                }

                $query_data['apo_p_num'] = $apo_content_type != 1 ? 0 : $apo_p_num;
                $query_data['apo_special_offer_seq'] = $apo_content_type != 2 ? 0 : $apo_special_offer_seq;


                $query_data['apo_termlimit_yn'] = $apo_termlimit_yn;
                $query_data['apo_termlimit_datetime1'] = $apo_startdate;
                $query_data['apo_termlimit_datetime2'] = $apo_enddate;
                $query_data['apo_display_yn'] = $apo_display_yn;

                $query_data['apo_view_page'] = $apo_view_page;
                $query_data['apo_view_target'] = $apo_view_target;

                $query_data['apo_noti_subject'] = $apo_noti_subject;
                $query_data['apo_noti_content'] = $apo_noti_content;


                if( $this->app_popup_model->update_app_popup($apo_num, $query_data) ) {
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
    }//end of app_popup_update_proc()

    
    /**
     * APP 팝업 삭제 처리 (Ajax)
     */
    public function app_popup_delete_proc() {
        ajax_request_check();

        //request
        $req['apo_num'] = $this->input->post_get('apo_num', true);

        //row
        $row = $this->app_popup_model->get_app_popup_row($req['apo_num']);

        if( empty($row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //삭제
        if( $this->app_popup_model->delete_app_popup($req['apo_num']) ) {
            //이미지 삭제
            file_delete(1, str_replace(array($this->config->item("site_http"), $this->config->item("site_img_http")), "", $row->apo_image), DOCROOT);

            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of app_popup_delete_proc()

    /**
     * 노출상태 변경
     */
    public function app_popup_display_toggle() {
        ajax_request_check();

        //request
        $req['apo_num'] = $this->input->post_get('apo_num', true);

        //APP 푸시 정보
        $popup_row = $this->app_popup_model->get_app_popup_row($req['apo_num']);

        if( empty($popup_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        if( $popup_row->apo_display_yn == "Y" ) {
            $apo_display_yn = "N";
        }
        else {
            $apo_display_yn = "Y";
        }

        $query_data = array();
        $query_data['apo_display_yn'] = $apo_display_yn;

        if( $this->app_popup_model->update_app_popup_nonchk($popup_row->apo_num, $query_data) ) {
            result_echo_json(get_status_code('success'), "", true);
        }
        else {
            result_echo_json(get_status_code('error'), "", true);
        }
    }//end of app_push_display_toggle()
}//end of class App_popup