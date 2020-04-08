<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * APP 스플래시 관련 컨트롤러
 */
class App_splash extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('app_splash_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->app_splash_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
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
     * APP 스플래시 목록
     */
    public function app_splash_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/app_splash/app_splash_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of app_splash_list()

    /**
     * APP 스플래시 목록 (Ajax)
     */
    public function app_splash_list_ajax() {
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
        $list_count = $this->app_splash_model->get_app_splash_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/app_splash/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $app_splash_list = $this->app_splash_model->get_app_splash_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['aps_ostype'] = array("asc", "sorting");
        $sort_array['aps_regdatetime'] = array("asc", "sorting");
        $sort_array['aps_termlimit1'] = array("asc", "sorting");
        $sort_array['aps_termlimit2'] = array("asc", "sorting");
        $sort_array['aps_bg_color'] = array("asc", "sorting");
        $sort_array['aps_state'] = array("asc", "sorting");
        $sort_array['aps_usestate'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/app_splash/app_splash_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "app_splash_list"     => $app_splash_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of app_splash_list_ajax()

    /**
     * APP 스플래시 추가 (팝업)
     */
    public function app_splash_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/app_splash/app_splash_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url()
        ));
    }//end of app_splash_insert_pop()

    /**
     * APP 스플래시 추가 처리 (Ajax)
     */
    public function app_splash_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        $aps_image_set_rules = $this->default_set_rules;
        if( !isset($_FILES['aps_image']['name']) || empty($_FILES['aps_image']['name']) ) {
            $aps_image_set_rules .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
                "aps_image" => array("field" => "aps_image", "label" => "스플래시이미지", "rules" => $aps_image_set_rules)
            ,   "aps_usestate" => array("field" => "aps_usestate", "label" => "사용여부", "rules" => "required|in_list[".get_config_item_keys_string("app_splash_usestate")."]|".$this->default_set_rules)
            ,   "aps_termlimit1" => array("field" => "aps_termlimit1", "label" => "시작일", "rules" => "required|".$this->default_set_rules)
            ,   "aps_termlimit2" => array("field" => "aps_termlimit2", "label" => "종료일", "rules" => "required|".$this->default_set_rules)
            ,   "aps_bg_color" => array("field" => "aps_bg_color", "label" => "배경색", "rules" => "required|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $aps_image      = "";
            $aps_usestate   = $this->input->post('aps_usestate', true);
            $aps_termlimit1 = number_only($this->input->post('aps_termlimit1', true));
            $aps_termlimit2 = number_only($this->input->post('aps_termlimit2', true));
            $aps_bg_color   = $this->input->post('aps_bg_color', true);


            //이미지 업로드
            if( isset($_FILES['aps_image']['name']) && !empty($_FILES['aps_image']['name']) ) {
                $aps_image_path_web = $this->config->item('app_splash_image_path_web') . "/" . date("Y") . "/" . date("md");
                $aps_image_path = $this->config->item('app_splash_image_path') . "/" . date("Y") . "/" . date("md");
                create_directory($aps_image_path);

                $config = array();
                $config['upload_path'] = $aps_image_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '20000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('aps_image') ) {

                    $aps_image_data_array = $this->upload->data();
                    $aps_image_data_array = create_thumb_image($aps_image_data_array, $aps_image_path_web, $this->config->item('splash_image_size'), true);
                    $aps_image = $aps_image_data_array[1];

                    //cdn purge
                    cdn_purge($aps_image_path_web . "/" . $aps_image_data_array[1]);
                }
                else {
                    $form_error_array['aps_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['aps_image'] = $aps_image;
                $query_data['aps_usestate'] = $aps_usestate;
                $query_data['aps_termlimit1'] = $aps_termlimit1;
                $query_data['aps_termlimit2'] = $aps_termlimit2;
                $query_data['aps_bg_color'] = $aps_bg_color;

                if( $this->app_splash_model->insert_app_splash($query_data) ) {
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
    }//end of app_splash_insert_proc()

    /**
     * APP 스플래시 수정
     */
    public function app_splash_update_pop() {
        //request
        $req = $this->_list_req();
        $req['aps_num'] = $this->input->post_get('aps_num', true);

        //row
        $app_splash_row = $this->app_splash_model->get_app_splash_row($req['aps_num']);

        if( empty($app_splash_row) ) {
            alert(lang('site_error_empty_data'));
        }
        if( $app_splash_row->aps_state > 1 ) {
            alert("적용대기 항목만 수정 가능합니다.");
        }

        $this->load->view("/app_splash/app_splash_update_pop", array(
            'req'               => $req,
            'app_splash_row'   => $app_splash_row,
            'list_url'          => $this->_get_list_url()
        ));
    }//end of app_splash_update_pop()

    /**
     * APP 스플래시 수정 처리 (Ajax)
     */
    public function app_splash_update_proc() {
        ajax_request_check();

        //request
        $req['aps_num'] = $this->input->post_get('aps_num', true);

        //row
        $app_splash_row = $this->app_splash_model->get_app_splash_row($req['aps_num']);

        if( empty($app_splash_row) ) {
            alert(lang('site_error_empty_data'));
        }
        if( $app_splash_row->aps_state > 1 ) {
            alert("적용대기 항목만 수정 가능합니다.");
        }

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
                "aps_num" => array("field" => "aps_num", "label" => "번호", "rules" => "required|is_natural|".$this->default_set_rules)
            ,   "aps_image" => array("field" => "aps_image", "label" => "스플래시이미지", "rules" => $this->default_set_rules)
            ,   "aps_usestate" => array("field" => "aps_usestate", "label" => "사용여부", "rules" => "required|in_list[".get_config_item_keys_string("app_splash_usestate")."]|".$this->default_set_rules)
            ,   "aps_termlimit1" => array("field" => "aps_termlimit1", "label" => "시작일", "rules" => "required|".$this->default_set_rules)
            ,   "aps_termlimit2" => array("field" => "aps_termlimit2", "label" => "종료일", "rules" => "required|".$this->default_set_rules)
            ,   "aps_bg_color" => array("field" => "aps_bg_color", "label" => "배경색", "rules" => "required|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $aps_num = $this->input->post('aps_num', true);
            $aps_usestate = $this->input->post('aps_usestate', true);
            $aps_termlimit1 = number_only($this->input->post('aps_termlimit1', true));
            $aps_termlimit2 = number_only($this->input->post('aps_termlimit2', true));
            $aps_bg_color   = $this->input->post('aps_bg_color', true);
            $aps_image = "";

            //이미지 업로드
            if( isset($_FILES['aps_image']['name']) && !empty($_FILES['aps_image']['name']) ) {
                $aps_image_path_web = $this->config->item('app_splash_image_path_web') . "/" . date("Y") . "/" . date("md");
                $aps_image_path = $this->config->item('app_splash_image_path') . "/" . date("Y") . "/" . date("md");
                create_directory($aps_image_path);

                $config = array();
                $config['upload_path'] = $aps_image_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '20000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('aps_image') ) {

                    $aps_image_data_array = $this->upload->data();
                    $aps_image_data_array = create_thumb_image($aps_image_data_array, $aps_image_path_web, $this->config->item('splash_image_size'), true);
                    $aps_image = $aps_image_data_array[1];

                    //기존 이미지 삭제
                    file_delete(1, str_replace(array($this->config->item("site_http"), $this->config->item("site_img_http")), "", $app_splash_row->aps_image), DOCROOT);
                }
                else {
                    $form_error_array['aps_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }

            if( empty($form_error_array) ) {
                $query_data = array();
                if ( !empty($aps_image) ) {
                    $query_data['aps_image'] = $aps_image;
                }
                $query_data['aps_usestate'] = $aps_usestate;
                $query_data['aps_termlimit1'] = $aps_termlimit1;
                $query_data['aps_termlimit2'] = $aps_termlimit2;
                $query_data['aps_bg_color'] = $aps_bg_color;

                if( $this->app_splash_model->update_app_splash($aps_num, $query_data) ) {
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
    }//end of app_splash_update_proc()

    /**
     * APP 스플래시 삭제 처리 (Ajax)
     */
    public function app_splash_delete_proc() {
        ajax_request_check();

        //request
        $req['aps_num'] = $this->input->post_get('aps_num', true);

        //APP 스플래시 정보
        $app_splash_row = $this->app_splash_model->get_app_splash_row($req['aps_num']);

        if( empty($app_splash_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }
        if( $app_splash_row->aps_state == 2 ) {
            alert("적용중인 항목은 삭제 불가!");
        }

        //APP 스플래시 삭제
        if( $this->app_splash_model->delete_app_splash($req['aps_num']) ) {
            //이미지 삭제
            file_delete(1, str_replace(array($this->config->item("site_http"), $this->config->item("site_img_http")), "", $app_splash_row->aps_image), DOCROOT);

            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of app_splash_delete_proc()

    /**
     * 사용여부 변경
     */
    public function app_splash_usestate_toggle() {
        ajax_request_check();

        //request
        $req['aps_num'] = $this->input->post_get('aps_num', true);

        //APP 스플래시 정보
        $push_row = $this->app_splash_model->get_app_splash_row($req['aps_num']);

        if( empty($push_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        if( $push_row->aps_usestate == "Y" ) {
            $aps_usestate = "N";
        }
        else {
            $aps_usestate = "Y";
        }

        $query_data = array();
        $query_data['aps_usestate'] = $aps_usestate;
        if( $this->app_splash_model->update_app_splash_info($push_row->aps_num, $query_data) ) {
            result_echo_json(get_status_code('success'), "", true);
        }
        else {
            result_echo_json(get_status_code('error'), "", true);
        }
    }//end of app_splash_usestate_toggle()

}//end of class App_splash