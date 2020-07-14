<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 공통관리 관련 컨트롤러
 */
class Common extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('common_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->common_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['code']            = trim($this->input->post_get('code', true));
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
     * 공통관리 목록
     */
    public function common_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/common/common_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of common_list()

    /**
     * 공통관리 목록 (Ajax)
     */
    public function common_list_ajax() {
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
        $list_count = $this->common_model->get_common_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/common/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $common_list = $this->common_model->get_common_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['cm_code'] = array("asc", "sorting");
        $sort_array['cm_content'] = array("asc", "sorting");
        $sort_array['cm_adminuser_num'] = array("asc", "sorting");
        $sort_array['cm_datetime'] = array("asc", "sorting");
        $sort_array['cm_usestate'] = array("asc", "sorting");
        $sort_array['au_name'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/common/common_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "common_list"       => $common_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of common_list_ajax()

    /**
     * 공통관리 추가 (팝업)
     */
    public function common_insert_pop() {
        //request
        $req = $this->_list_req();

        $this->load->view("/common/common_insert_pop", array(
            'req'       => $req,
            'list_url'  => $this->_get_list_url()
        ));
    }//end of common_insert_pop()

    /**
     * 공통관리 추가 처리 (Ajax)
     */
    public function common_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "cm_code" => array("field" => "cm_code", "label" => "코드", "rules" => "required|in_list[".get_config_item_keys_string("common_code")."]|".$this->default_set_rules),
            "cm_content" => array("field" => "cm_content", "label" => "내용", "rules" => "required"),
            "cm_usestate" => array("field" => "cm_usestate", "label" => "활성여부", "rules" => "required|in_list[".get_config_item_keys_string("common_usestate")."]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $cm_code = $this->input->post('cm_code', true);
            $cm_content = $this->input->post('cm_content');
            $cm_usestate = $this->input->post('cm_usestate', true);

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['cm_code'] = $cm_code;
                $query_data['cm_content'] = $cm_content;
                $query_data['cm_usestate'] = get_yn_value($cm_usestate);

                if( $this->common_model->insert_common($query_data) ) {
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
    }//end of common_insert_proc()

    /**
     * 공통관리 수정
     */
    public function common_update_pop() {
        //request
        $req = $this->_list_req();
        $req['cm_num'] = $this->input->post_get('cm_num', true);

        //row
        $common_row = $this->common_model->get_common_row($req['cm_num']);

        if( empty($common_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->view("/common/common_update_pop", array(
            'req'               => $req,
            'common_row'    => $common_row,
            'list_url'          => $this->_get_list_url()
        ));
    }//end of common_update_pop()

    /**
     * 공통관리 수정 처리 (Ajax)
     */
    public function common_update_proc() {
        ajax_request_check();

        //request
        $req['cm_num'] = $this->input->post_get('cm_num', true);

        //row
        $common_row = $this->common_model->get_common_row($req['cm_num']);

        if( empty($common_row) ) {
            alert(lang('site_error_empty_data'));
        }

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "cm_num" => array("field" => "cm_num", "label" => "번호", "rules" => "required|is_natural|".$this->default_set_rules),
            "cm_code" => array("field" => "cm_code", "label" => "코드", "rules" => "required|in_list[".get_config_item_keys_string("common_code")."]|".$this->default_set_rules),
            "cm_content" => array("field" => "cm_content", "label" => "내용", "rules" => "required"),
            "cm_usestate" => array("field" => "cm_usestate", "label" => "활성여부", "rules" => "required|in_list[".get_config_item_keys_string("common_usestate")."]|".$this->default_set_rules)
        );
        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $cm_num = $this->input->post('cm_num', true);
            $cm_code = $this->input->post('cm_code', true);
            $cm_content = $this->input->post('cm_content');
            $cm_usestate = $this->input->post('cm_usestate', true);

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['cm_code'] = $cm_code;
                $query_data['cm_content'] = $cm_content;
                $query_data['cm_usestate'] = get_yn_value($cm_usestate);

                if( $this->common_model->update_common($cm_num, $query_data) ) {
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
    }//end of common_update_proc()

    /**
     * 공통관리 삭제 처리 (Ajax)
     */
    public function common_delete_proc() {
        ajax_request_check();

        //request
        $req['cm_num'] = $this->input->post_get('cm_num', true);

        //row
        $common_row = $this->common_model->get_common_row($req['cm_num']);

        if( empty($common_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //삭제
        if( $this->common_model->delete_common($req['cm_num']) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of common_delete_proc()


    public function common_file_download() {

        $aInput = array(
            'file_path' => urldecode(base64_decode($this->input->get('file_path')))
        ,   'file_name' => urldecode(base64_decode($this->input->get('file_name')))
        );

        if(empty($aInput['file_path']) == true){
            //alert('첨부된 파일이 없습니다.');
            echo '<script>parent.alert("첨부된 파일이 없습니다.");</script>';
            exit;
        }


        $this->load->helper('download');
        force_download(".{$aInput['file_path']}", NULL ,$aInput['file_name']);
    }

    /**
     * 푸시발송 팝업
     */
    public function common_send_push_pop() {

        ajax_request_check();

        $aInput = array( 'm_num' => $this->input->get('m_num', true) );

        $this->load->view("/common/send_push_pop", array(
            'aInput' => $aInput
        ));
    }//end of common_send_push_pop()

    /**
     * 푸시발송 proc
     */
    public function common_send_push_proc() {

        ajax_request_check();

        $aInput             = array(
            'm_num'         => $this->input->post('m_num', true)
        ,   'push_title'    => $this->input->post('push_title', true)
        ,   'push_content'  => $this->input->post('push_content', true)
        );

        $push_data          = array();
        $push_data['title'] = $aInput['push_title'];
        $push_data['body']  = $aInput['push_content'];
        $push_data['page']  = "none";

        $resp = send_app_push_log($aInput['m_num'], $push_data);

        if( $resp['success'] == true ) echo json_encode_no_slashes(array('success' => true , 'msg' => '푸시발송완료'));
        else echo json_encode_no_slashes(array('success' => true , 'msg' => '푸시발송실패'));

    }//end of common_send_push_proc()


    /**
     * 재고체크
     */
    public function common_chk_stock() {

        $sql = "SELECT pt.p_num,pt.p_name,spt.option_info
                FROM product_tb pt
                INNER JOIN snsform_product_tb spt ON spt.item_no = pt.p_order_code 
                WHERE pt.p_display_state = 'Y' 
                AND pt.p_sale_state = 'Y' 
                AND pt.p_stock_state = 'Y';  
        ";
        $aProductList = $this->db->query($sql)->result_array();


        $alert = array();
        foreach ($aProductList as $r) {
            $option_info = json_decode($r['option_info'],true);



            foreach ($option_info as $rr) {
                $msg = '';
                if($rr['option_count'] <= 10){
                    $msg .= "{$r['p_name']}";
                    $msg .= " | {$rr['option_depth1']}";
                    $msg .= $rr['option_depth2'] ? " | {$rr['option_depth2']}" : '';
                    $msg .= $rr['option_depth3'] ? " | {$rr['option_depth3']}" : '';
                    $msg .= " | 재고 : {$rr['option_count']}";

                    $alert[] = $msg;

                }
            }

        }


        zsView($alert);




    }

}//end of class Common