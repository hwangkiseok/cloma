<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 배너 관련 컨트롤러
 */
class Banner extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('banner_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->banner_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['div']             = trim($this->input->post_get('div', true));
        $req['usestate']        = trim($this->input->post_get('usestate', true));
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
     * 배너 목록
     */
    public function banner_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/banner/banner_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of banner_list()

    /**
     * 배너 목록 (Ajax)
     */
    public function banner_list_ajax() {
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
        $list_count = $this->banner_model->get_banner_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/banner/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $banner_list = $this->banner_model->get_banner_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['bn_division'] = array("asc", "sorting");
        $sort_array['bn_adminuser_num'] = array("asc", "sorting");
        $sort_array['bn_subject'] = array("asc", "sorting");
        $sort_array['bn_target_url'] = array("asc", "sorting");
        $sort_array['bn_order'] = array("asc", "sorting");
        $sort_array['bn_regdatetime'] = array("asc", "sorting");
        $sort_array['bn_usestate'] = array("asc", "sorting");
        $sort_array['au_name'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/banner/banner_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "banner_list"       => $banner_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of banner_list_ajax()

    /**
     * 배너 추가 (팝업)
     */
    public function banner_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/banner/banner_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url()
        ));
    }//end of banner_insert_pop()

    /**
     * 배너 추가 처리 (Ajax)
     */
    public function banner_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //set rules
        $bn_image_set_rules = $this->default_set_rules;
        if( !isset($_FILES['bn_image']['name']) || empty($_FILES['bn_image']['name']) ) {
            $bn_image_set_rules .= "|required";
        }
        $bn_termlimit_datetime1 = $this->default_set_rules;
        if( $this->input->post('bn_termlimit_yn', true) == 'Y' ) {
            $bn_termlimit_datetime1 .= "|required";
        }
        $bn_termlimit_datetime2 = $this->default_set_rules;
        if( $this->input->post('bn_termlimit_yn', true) == 'Y' ) {
            $bn_termlimit_datetime2 .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "bn_division" => array("field" => "bn_division", "label" => "배너종류", "rules" => "required|in_list[".get_config_item_keys_string("banner_division")."]|".$this->default_set_rules),
            "bn_subject" => array("field" => "bn_subject", "label" => "배너제목", "rules" => "required|".$this->default_set_rules),
            "bn_image" => array("field" => "bn_image", "label" => "배너이미지", "rules" => $bn_image_set_rules),
            "bn_termlimit_yn" => array("field" => "bn_termlimit_yn", "label" => "노출기간사용여부", "rules" => "required|in_list[".get_config_item_keys_string("banner_termlimit_yn")."]|".$this->default_set_rules),
            "bn_termlimit_datetime1" => array("field" => "bn_termlimit_datetime1", "label" => "노출시작일", "rules" => $bn_termlimit_datetime1),
            "bn_termlimit_datetime2" => array("field" => "bn_termlimit_datetime2", "label" => "노출종료일", "rules" => $bn_termlimit_datetime2),
            "bn_target_url" => array("field" => "bn_target_url", "label" => "타겟URL", "rules" => $this->default_set_rules),
            "bn_usestate" => array("field" => "bn_usestate", "label" => "노출여부", "rules" => "required|in_list[".get_config_item_keys_string("banner_usestate")."]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $bn_division = $this->input->post('bn_division', true);
            $bn_subject = $this->input->post('bn_subject', true);
            $bn_termlimit_yn = $this->input->post('bn_termlimit_yn', true);
            $bn_termlimit_datetime1 = $this->input->post('bn_termlimit_datetime1', true);
            $bn_termlimit_datetime2 = $this->input->post('bn_termlimit_datetime2', true);
            $bn_target_url = $this->input->post('bn_target_url', true);
            if( !empty($bn_target_url) ) {
                $bn_target_url = "http://" . str_replace("http://", "", $bn_target_url);
            }
            $bn_usestate = $this->input->post('bn_usestate', true);
            $bn_image = "";


            //배너이미지 업로드 (썸네일 생성)
            $bn_image_path_web = $this->config->item('banner_image_path_web') . "/" . date("Y") . "/" . date("md");
            $bn_image_path = $this->config->item('banner_image_path') . "/" . date("Y") . "/" . date("md");
            create_directory($bn_image_path);

            $config = array();
            $config['upload_path'] = $bn_image_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size']	= '5000';
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if( $this->upload->do_upload('bn_image') ){
                $bn_image_data_array = $this->upload->data();
                //$bn_image_array = create_thumb_image($bn_image_data_array, $bn_image_path_web, $this->config->item('banner_image_size'));
                //
                //if( empty($bn_image_array) || $bn_image_array === false ) {
                //    $form_error_array['bn_image'] = "배너이미지 썸네일 생성을 실패했습니다.";
                //}
                //else {
                //    $bn_image_array[0] = $bn_image_path_web . "/" . $bn_image_data_array['file_name'];
                //    $bn_image = json_encode_no_slashes($bn_image_array);
                //}

                //썸네일 생성하지 않음
                $bn_image_array = array();
                $bn_image_array[1] = $bn_image_path_web . "/" . $bn_image_data_array['file_name'];
                $bn_image_array[0] = $bn_image_path_web . "/" . $bn_image_data_array['file_name'];
                $bn_image = json_encode_no_slashes($bn_image_array);

                //cdn purge
                cdn_purge($bn_image_array[0]);
            }
            else {
                $form_error_array['bn_image'] = strip_tags($this->upload->display_errors());
            }//end of if()

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['bn_division'] = $bn_division;
                $query_data['bn_subject'] = $bn_subject;
                $query_data['bn_image'] = $bn_image;
                $query_data['bn_termlimit_yn'] = $bn_termlimit_yn;
                $query_data['bn_termlimit_datetime1'] = number_only($bn_termlimit_datetime1);
                $query_data['bn_termlimit_datetime2'] = number_only($bn_termlimit_datetime2);
                $query_data['bn_target_url'] = $bn_target_url;
                $query_data['bn_usestate'] = $bn_usestate;

                if( $this->banner_model->insert_banner($query_data) ) {
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
    }//end of banner_insert_proc()

    /**
     * 배너 수정
     */
    public function banner_update_pop() {
        //request
        $req = $this->_list_req();
        $req['bn_num'] = $this->input->post_get('bn_num', true);

        //row
        $banner_row = $this->banner_model->get_banner_row($req['bn_num']);

        if( empty($banner_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->view("/banner/banner_update_pop", array(
            'req'               => $req,
            'banner_row'    => $banner_row,
            'list_url'          => $this->_get_list_url()
        ));
    }//end of banner_update_pop()

    /**
     * 배너 수정 처리 (Ajax)
     */
    public function banner_update_proc() {
        ajax_request_check();

        //request
        $req['bn_num'] = $this->input->post_get('bn_num', true);

        //row
        $banner_row = $this->banner_model->get_banner_row($req['bn_num']);

        if( empty($banner_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->library('form_validation');

        //set rules
        $bn_image_set_rules = $this->default_set_rules;
        if( empty($banner_row->bn_image) && (!isset($_FILES['bn_image']['name']) || empty($_FILES['bn_image']['name'])) ) {
            $bn_image_set_rules .= "|required";
        }
        $bn_termlimit_datetime1 = $this->default_set_rules;
        if( $this->input->post('bn_termlimit_yn', true) == 'Y' ) {
            $bn_termlimit_datetime1 .= "|required";
        }
        $bn_termlimit_datetime2 = $this->default_set_rules;
        if( $this->input->post('bn_termlimit_yn', true) == 'Y' ) {
            $bn_termlimit_datetime2 .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "bn_num" => array("field" => "bn_num", "label" => "배너종류", "rules" => "required|is_natural|".$this->default_set_rules),
            "bn_division" => array("field" => "bn_division", "label" => "배너종류", "rules" => "required|in_list[".get_config_item_keys_string("banner_division")."]|".$this->default_set_rules),
            "bn_subject" => array("field" => "bn_subject", "label" => "제목", "rules" => "required|".$this->default_set_rules),
            "bn_image" => array("field" => "bn_image", "label" => "이미지", "rules" => $bn_image_set_rules),
            "bn_termlimit_yn" => array("field" => "bn_termlimit_yn", "label" => "공개기간설정", "rules" => "required|in_list[".get_config_item_keys_string("banner_termlimit_yn")."]|".$this->default_set_rules),
            "bn_termlimit_datetime1" => array("field" => "bn_termlimit_datetime1", "label" => "공개시작일", "rules" => $bn_termlimit_datetime1),
            "bn_termlimit_datetime2" => array("field" => "bn_termlimit_datetime2", "label" => "공개종료일", "rules" => $bn_termlimit_datetime2),
            "bn_target_url" => array("field" => "bn_target_url", "label" => "이동URL", "rules" => $this->default_set_rules),
            "bn_usestate" => array("field" => "bn_usestate", "label" => "활성여부", "rules" => "required|in_list[".get_config_item_keys_string("banner_usestate")."]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $bn_num = $this->input->post('bn_num', true);
            $bn_division = $this->input->post('bn_division', true);
            $bn_subject = $this->input->post('bn_subject', true);
            $bn_termlimit_yn = $this->input->post('bn_termlimit_yn', true);
            $bn_termlimit_datetime1 = $this->input->post('bn_termlimit_datetime1', true);
            $bn_termlimit_datetime2 = $this->input->post('bn_termlimit_datetime2', true);
            $bn_target_url = $this->input->post('bn_target_url', true);
            if( !empty($bn_target_url) ) {
                $bn_target_url = "http://" . str_replace("http://", "", $bn_target_url);
            }
            $bn_usestate = $this->input->post('bn_usestate', true);
            $bn_image = "";


            if( isset($_FILES['bn_image']['name']) && !empty($_FILES['bn_image']['name']) ) {
                //배너이미지 업로드 (썸네일 생성)
                $bn_image_path_web = $this->config->item('banner_image_path_web') . "/" . date("Y") . "/" . date("md");
                $bn_image_path = $this->config->item('banner_image_path') . "/" . date("Y") . "/" . date("md");
                create_directory($bn_image_path);

                $config = array();
                $config['upload_path'] = $bn_image_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = '5000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('bn_image') ) {
                    $bn_image_data_array = $this->upload->data();
                    //$bn_image_array = create_thumb_image($bn_image_data_array, $bn_image_path_web, $this->config->item('banner_image_size'));
                    //
                    //if ( empty($bn_image_array) || $bn_image_array === false ) {
                    //    $form_error_array['bn_image'] = "배너이미지 썸네일 생성을 실패했습니다.";
                    //}
                    //else {
                    //    $bn_image_array[0] = $bn_image_path_web . "/" . $bn_image_data_array['file_name'];
                    //    $bn_image = json_encode_no_slashes($bn_image_array);
                    //
                    //    //기존 파일 삭제
                    //    file_delete(3, $banner_row->bn_image, DOCROOT);
                    //}

                    //썸네일 생성하지 않음.
                    $bn_image_array = array();
                    $bn_image_array[1] = $bn_image_path_web . "/" . $bn_image_data_array['file_name'];
                    $bn_image_array[0] = $bn_image_path_web . "/" . $bn_image_data_array['file_name'];
                    $bn_image = json_encode_no_slashes($bn_image_array);

                    //기존 파일 삭제
                    file_delete(3, $banner_row->bn_image, DOCROOT);

                    //cdn purge
                    cdn_purge($bn_image_array[0]);
                }
                else {
                    $form_error_array['bn_image'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['bn_division'] = $bn_division;
                $query_data['bn_subject'] = $bn_subject;
                if( !empty($bn_image) ) {
                    $query_data['bn_image'] = $bn_image;

                }
                $query_data['bn_termlimit_yn'] = $bn_termlimit_yn;
                $query_data['bn_termlimit_datetime1'] = number_only($bn_termlimit_datetime1);
                $query_data['bn_termlimit_datetime2'] = number_only($bn_termlimit_datetime2);
                $query_data['bn_target_url'] = $bn_target_url;
                $query_data['bn_usestate'] = $bn_usestate;

                if( $this->banner_model->update_banner($bn_num, $query_data) ) {
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
    }//end of banner_update_proc()

    /**
     * 배너 삭제 처리 (Ajax)
     */
    public function banner_delete_proc() {
        ajax_request_check();

        //request
        $req['bn_num'] = $this->input->post_get('bn_num', true);

        //배너 정보
        $banner_row = $this->banner_model->get_banner_row($req['bn_num']);

        if( empty($banner_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //배너 삭제
        if( $this->banner_model->delete_banner($req['bn_num']) ) {
            //파일 삭제
            file_delete(3, $banner_row->bn_image, DOCROOT);

            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of banner_delete_proc()

    /**
     * 배너 순서 수정 (Ajax)
     */
    public function banner_order_proc() {
        ajax_request_check();

        //var_dump($_POST);

        $data = $this->input->post('data', true);       //배열 ([div:pdt_num] => order 형식)

        if( empty($data) ) {
            result_echo_json(get_status_code('error'), lang('site_no_data'), true, 'alert');
        }

        foreach( $data as $key => $value ) {
            $bn_num = $key;
            $bn_order = $value;

            $this->banner_model->order_update_banner($bn_num, $bn_order);
        }//end of foreach()

        result_echo_json(get_status_code('success'), '', true);
    }//end of banner_order_proc()

}//end of class Banner