<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * APP 버전 관련 컨트롤러
 */
class App_version extends A_Controller {

    var $app_download_url;       //기본 앱 설치 URL

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('app_version_model');

        $this->app_download_url['1'] = "";
        $this->app_download_url['2'] = "";
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->app_version_list();
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
     * APP 버전 목록
     */
    public function app_version_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/app_version/app_version_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of app_version_list()

    /**
     * APP 버전 목록 (Ajax)
     */
    public function app_version_list_ajax() {
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
        $list_count = $this->app_version_model->get_app_version_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/app_version/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $app_version_list = $this->app_version_model->get_app_version_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['av_version'] = array("asc", "sorting");
        $sort_array['av_version_code'] = array("asc", "sorting");
        $sort_array['av_offer_type'] = array("asc", "sorting");
        $sort_array['av_os_type'] = array("asc", "sorting");
        $sort_array['av_regdatetime'] = array("asc", "sorting");
        $sort_array['au_name'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/app_version/app_version_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "app_version_list"       => $app_version_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of app_version_list_ajax()

    /**
     * APP 버전 추가 (팝업)
     */
    public function app_version_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/app_version/app_version_insert_pop", array(
            'app_download_url'   => $this->app_download_url,
            'req'               => $req,
            'list_url'          => $this->_get_list_url()
        ));
    }//end of app_version_insert_pop()

    /**
     * APP 버전 추가 처리 (Ajax)
     */
    public function app_version_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "av_version" => array("field" => "av_version", "label" => "버전명", "rules" => "required|max_length[50]|".$this->default_set_rules),
            "av_version_code" => array("field" => "av_version_code", "label" => "버전코드", "rules" => "required|max_length[10]|".$this->default_set_rules),
            "av_offer_type" => array("field" => "av_offer_type", "label" => "제공방식", "rules" => "required|in_list[".get_config_item_keys_string("app_version_offer_type")."]|".$this->default_set_rules),
            "av_os_type" => array("field" => "av_os_type", "label" => "OS타입", "rules" => "required|in_list[".get_config_item_keys_string("app_version_os_type")."]|".$this->default_set_rules),
            "av_download_url" => array("field" => "av_download_url", "label" => "설치URL", "rules" => "required|".$this->default_set_rules),
            "av_content" => array("field" => "av_content", "label" => "작업내역", "rules" => $this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $av_version = $this->input->post('av_version', true);
            $av_version_code = $this->input->post('av_version_code', true);
            $av_offer_type = $this->input->post('av_offer_type', true);
            $av_os_type = $this->input->post('av_os_type', true);
            $av_download_url = $this->input->post('av_download_url', true);
            $av_content = $this->input->post('av_content', true);


            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['av_version'] = $av_version;
                $query_data['av_version_code'] = $av_version_code;
                $query_data['av_offer_type'] = $av_offer_type;
                $query_data['av_os_type'] = $av_os_type;
                $query_data['av_download_url'] = $av_download_url;
                $query_data['av_content'] = $av_content;

                if( $this->app_version_model->insert_app_version($query_data) ) {
                    result_echo_json(get_status_code('success'), lang('site_insert_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_insert_fail_db'), true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of app_version_insert_proc()

    /**
     * APP 버전 수정
     */
    public function app_version_update_pop() {
        //request
        $req = $this->_list_req();
        $req['av_num'] = $this->input->post_get('av_num', true);

        //row
        $app_version_row = $this->app_version_model->get_app_version_row($req['av_num']);

        if( empty($app_version_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->view("/app_version/app_version_update_pop", array(
            'app_download_url'   => $this->app_download_url,
            'req'               => $req,
            'app_version_row'   => $app_version_row,
            'list_url'          => $this->_get_list_url()
        ));
    }//end of app_version_update_pop()

    /**
     * APP 버전 수정 처리 (Ajax)
     */
    public function app_version_update_proc() {
        ajax_request_check();

        //request
        $req['av_num'] = $this->input->post_get('av_num', true);

        //row
        $app_version_row = $this->app_version_model->get_app_version_row($req['av_num']);

        if( empty($app_version_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "av_num" => array("field" => "av_num", "label" => "일련번호", "rules" => "required|is_natural|".$this->default_set_rules),
            "av_version" => array("field" => "av_version", "label" => "버전명", "rules" => "required|max_length[50]|".$this->default_set_rules),
            "av_version_code" => array("field" => "av_version_code", "label" => "버전코드", "rules" => "required|max_length[10]|".$this->default_set_rules),
            "av_offer_type" => array("field" => "av_offer_type", "label" => "제공방식", "rules" => "required|in_list[".get_config_item_keys_string("app_version_offer_type")."]|".$this->default_set_rules),
            "av_os_type" => array("field" => "av_os_type", "label" => "OS타입", "rules" => "required|in_list[".get_config_item_keys_string("app_version_os_type")."]|".$this->default_set_rules),
            "av_download_url" => array("field" => "av_download_url", "label" => "설치URL", "rules" => "required|".$this->default_set_rules),
            "av_content" => array("field" => "av_content", "label" => "작업내역", "rules" => $this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $av_num = $this->input->post('av_num', true);
            $av_version = $this->input->post('av_version', true);
            $av_version_code = $this->input->post('av_version_code', true);
            $av_offer_type = $this->input->post('av_offer_type', true);
            $av_os_type = $this->input->post('av_os_type', true);
            $av_download_url = $this->input->post('av_download_url', true);
            $av_content = $this->input->post('av_content', true);

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['av_version'] = $av_version;
                $query_data['av_version_code'] = $av_version_code;
                $query_data['av_offer_type'] = $av_offer_type;
                $query_data['av_os_type'] = $av_os_type;
                $query_data['av_download_url'] = $av_download_url;
                $query_data['av_content'] = $av_content;

                if( $this->app_version_model->update_app_version($av_num, $query_data) ) {
                    result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_update_fail_db'), true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of app_version_update_proc()

    /**
     * APP 버전 삭제 처리 (Ajax)
     */
    public function app_version_delete_proc() {
        ajax_request_check();

        //request
        $req['av_num'] = $this->input->post_get('av_num', true);

        //APP 버전 정보
        $app_version_row = $this->app_version_model->get_app_version_row($req['av_num']);

        if( empty($app_version_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //APP 버전 삭제
        if( $this->app_version_model->delete_app_version($req['av_num']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of app_version_delete_proc()

}//end of class app_version