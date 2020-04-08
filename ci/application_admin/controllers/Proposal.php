<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 배너 관련 컨트롤러
 */
class Proposal extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('proposal_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->proposal_list();
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
    public function proposal_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/proposal/proposal_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of banner_list()

    /**
     * 배너 목록 (Ajax)
     */
    public function proposal_list_ajax() {
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
        $list_count = $this->proposal_model->get_proposal_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => "/proposal/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $proposal_list = $this->proposal_model->get_proposal_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['reg_date'] = array("asc", "reg_date");
        $sort_array['pf_name'] = array("asc", "pf_name");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/proposal/proposal_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "proposal_list"       => $proposal_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of banner_list_ajax()

    /**
     * 배너 추가 (팝업)
     */
    public function proposal_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/proposal/proposal_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url()
        ));
    }//end of banner_insert_pop()

    /**
     * 배너 추가 처리 (Ajax)
     */
    public function proposal_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //set rules
        $pf_file_set_rules = $this->default_set_rules;
        if( empty($proposal_row['pf_file_url']) && (!isset($_FILES['pf_file']['name']) || empty($_FILES['pf_file']['name'])) ) {
            $pf_file_set_rules .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "pf_name" => array("field" => "pf_name", "label" => "제안서 상품명", "rules" => "required|".$this->default_set_rules),
            "pf_file" => array("field" => "pf_file", "label" => "이미지", "rules" => $pf_file_set_rules),
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $pf_num = $this->input->post('pf_num', true);
            $pf_name = $this->input->post('pf_name', true);

            $pf_file = "";

            if( isset($_FILES['pf_file']['name']) && !empty($_FILES['pf_file']['name']) ) {
                //배너이미지 업로드 (썸네일 생성)
                $pf_image_path_web = $this->config->item('proposal_image_path_web') . "/" . date("Y") . "/" . date("md");
                $pf_image_path = $this->config->item('proposal_image_path') . "/" . date("Y") . "/" . date("md");
                create_directory($pf_image_path);

                $config = array();
                $config['upload_path'] = $pf_image_path;
                $config['allowed_types'] = 'xls|xlsx|zip';
                $config['max_size'] = '25000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('pf_file') ) {
                    $pf_file_data_array = $this->upload->data();

                    //썸네일 생성하지 않음.
                    $pf_file = /*$this->config->item('default_http').*/$pf_image_path_web . "/" . $pf_file_data_array['file_name'];

                }
                else {
                    $form_error_array['pf_file_url'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['pf_name'] = $pf_name;
                if( !empty($pf_file) ) {
                    $query_data['pf_file_url'] = $pf_file;
                }

                if( $this->proposal_model->insert_proposal($query_data) ) {
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
    public function proposal_update_pop() {
        //request
        $req = $this->_list_req();
        $req['pf_num'] = $this->input->post_get('pf_num', true);

        //row
        $proposal_row = $this->proposal_model->get_proposal_row($req['pf_num']);

        if( empty($proposal_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->view("/proposal/proposal_update_pop", array(
            'req'               => $req,
            'proposal_row'    => $proposal_row,
            'list_url'          => $this->_get_list_url()
        ));
    }//end of banner_update_pop()

    /**
     * 배너 수정 처리 (Ajax)
     */
    public function proposal_update_proc() {
        ajax_request_check();

        //request
        $req['pf_num'] = $this->input->post_get('pf_num', true);

        //row
        $proposal_row = $this->proposal_model->get_proposal_row($req['pf_num']);

        if( empty($proposal_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->library('form_validation');

        //set rules
        $pf_file_set_rules = $this->default_set_rules;
        if( empty($proposal_row['pf_file_url']) && (!isset($_FILES['pf_file']['name']) || empty($_FILES['pf_file']['name'])) ) {
            $pf_file_set_rules .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "pf_num" => array("field" => "pf_num", "label" => "seq", "rules" => "required|is_natural|".$this->default_set_rules),
            "pf_name" => array("field" => "pf_name", "label" => "제안서 상품명", "rules" => "required|".$this->default_set_rules),
            "pf_file" => array("field" => "pf_file", "label" => "이미지", "rules" => $pf_file_set_rules),
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $pf_num = $this->input->post('pf_num', true);
            $pf_name = $this->input->post('pf_name', true);

            $bn_image = "";

            if( isset($_FILES['pf_file']['name']) && !empty($_FILES['pf_file']['name']) ) {
                //배너이미지 업로드 (썸네일 생성)
                $pf_image_path_web = $this->config->item('proposal_image_path_web') . "/" . date("Y") . "/" . date("md");
                $pf_image_path = $this->config->item('proposal_image_path') . "/" . date("Y") . "/" . date("md");
                create_directory($pf_image_path);

                $config = array();
                $config['upload_path'] = $pf_image_path;
                $config['allowed_types'] = 'xls|xlsx|zip';
                $config['max_size'] = '25000';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ( $this->upload->do_upload('pf_file') ) {
                    $pf_file_data_array = $this->upload->data();

                    //썸네일 생성하지 않음.
                    $pf_file = /*$this->config->item('default_http').*/$pf_image_path_web . "/" . $pf_file_data_array['file_name'];

                    //기존 파일 삭제
                    file_delete(3, $proposal_row['pf_file_url'], DOCROOT);

                    //cdn purge
                    cdn_purge($pf_file);
                }
                else {
                    $form_error_array['pf_file_url'] = strip_tags($this->upload->display_errors());
                }//end of if()
            }

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['pf_name'] = $pf_name;
                if( !empty($pf_file) ) {
                    $query_data['pf_file_url'] = $pf_file;
                }

                if( $this->proposal_model->update_proposal($pf_num, $query_data) ) {
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
    public function proposal_delete_proc() {
        ajax_request_check();

        //request
        $req['pf_num'] = $this->input->post_get('pf_num', true);

        //배너 정보
        $proposal_row = $this->proposal_model->get_proposal_row($req['pf_num']);

        if( empty($proposal_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //배너 삭제
        if( $this->proposal_model->delete_proposal($req['pf_num']) ) {
            //파일 삭제
            file_delete(1, $proposal_row['pf_file_url'], DOCROOT);
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of banner_delete_proc()

}//end of class Banner