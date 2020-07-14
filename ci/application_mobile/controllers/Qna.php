<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 1:1문의
 */
class Qna extends M_Controller
{

    var $back_url = "/";

    public function __construct()
    {
        parent::__construct();

        member_login_check();

        //model
        $this->load->model('board_qna_model');

    }//end of __construct()

    private function _list_req() {
        $req = array();
        $req['cate']            = trim($this->input->post_get('cate', true));
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['sort_field']      = trim($this->input->post_get('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->post_get('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 10;
        }

        return $req;
    }//end of _list_req()

    public function index()
    {

        //request
        $req = $this->_list_req();

        $query_data =  array();
        $query_data['where'] = $req;
        $query_data['where']['m_num'] = $_SESSION['session_m_num'];
        $query_data['where']['usestate'] = 'Y';

        //전체갯수
        $list_count = $this->board_qna_model->get_board_qna_list($query_data, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => $this->page_link->list_ajax,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        $qna_list = $this->board_qna_model->get_board_qna_list($query_data, $page_result['start'], $page_result['limit']);

        $options = array('title' => '1:1문의' , 'top_type' => 'back');
        $this->_header($options);
        if(1){
            $viewfile= '/qna/index_v2';
        }else{
            $viewfile= '/qna/index';
        }
        $this->load->view($viewfile, array(
            'req'           => $req,
            'list_count'    => $list_count,
            'total_page'    => $page_result['total_page'],
            'qna_list'      => $qna_list
        ));

        $this->_footer();

    }//end of index()


    public function qna_insert_pop(){

        if(1){
            $this->load->view('/qna/pop_v2', array( ));
        }else{
            $this->load->view('/qna/pop', array( ));
        }

    }

    public function qna_list_ajax(){

        ajax_request_check();

        $req = $this->_list_req();

        $query_data =  array();
        $query_data['where'] = $req;
        $query_data['where']['m_num'] = $_SESSION['session_m_num'];
        $query_data['where']['usestate'] = 'Y';

        //전체갯수
        $list_count = $this->board_qna_model->get_board_qna_list($query_data, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => $this->page_link->list_ajax,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        $qna_list = $this->board_qna_model->get_board_qna_list($query_data, $page_result['start'], $page_result['limit']);

        $view_file = '/qna/ajax_list';

        if(1){
            $view_file = '/qna/ajax_list_v2';
        }

        $this->load->view($view_file, array(
            'req'           => $req,
            'list_count'    => $list_count,
            'total_page'    => $page_result['total_page'],
            'qna_list'      => $qna_list
        ));

    }

    public function delete_proc(){
        ajax_request_check();

        $aInput = array('bq_num' => $this->input->post('seq') );

        $state_arr = array('bq_display_state_1' => 'N');

        if(is_array($aInput['bq_num']) == true){
            $ret = true;
            foreach ($aInput['bq_num'] as $v) {
                if( $this->board_qna_model->update_board_qna($v , $state_arr) == false ) $ret = false;
            }
        }else{
            $ret = $this->board_qna_model->update_board_qna($aInput['bq_num'] , $state_arr);
        }

        echo  json_encode_no_slashes(array('success' => $ret , 'msg' => '' , 'data' => array() ));




    }

    public function qna_insert_proc(){
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "bq_category" => array("field" => "bq_category", "label" => "문의유형", "rules" => "required|in_list[" . get_config_item_keys_string('board_qna_category') ."]|" . $this->default_set_rules),
            "bq_product_num" => array("field" => "bq_product_num", "label" => "상품번호", "rules" => "is_natural|" . $this->default_set_rules),
            "bq_product_name" => array("field" => "bq_product_name", "label" => "상품명", "rules" => $this->default_set_rules),
//            "bq_name" => array("field" => "bq_name", "label" => "이름", "rules" => "required|" . $this->default_set_rules),
//            "bq_contact" => array("field" => "bq_contact", "label" => "연락처", "rules" => "required|" . $this->default_set_rules),
            "bq_content" => array("field" => "bq_content", "label" => "내용", "rules" => "required|" . $this->default_set_rules),
            "bq_refund_info_bank" => array("field" => "bq_refund_info_bank", "label" => "환불계좌 은행명", "rules" => $this->default_set_rules),
            "bq_refund_info_account" => array("field" => "bq_refund_info_account", "label" => "환불계좌 계좌번호", "rules" => $this->default_set_rules),
            "bq_refund_info_owner" => array("field" => "bq_refund_info_owner", "label" => "환불계좌 예금주", "rules" => $this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {

            $aMemberInfo = $this->_get_member_info();

            $bq_category            = $this->input->post('bq_category', true);
            $bq_product_num         = $this->input->post('bq_product_num', true);
            $bq_product_name        = $this->input->post('bq_product_name', true);
            $bq_name                = $this->input->post('bq_name', true)?$this->input->post('bq_name', true) : $aMemberInfo['m_nickname'] ;
            $bq_contact             = $this->input->post('bq_contact', true)?$this->input->post('bq_contact', true) : $aMemberInfo['m_authno'] ;
            $bq_content             = $this->input->post('bq_content', true);
            $bq_refund_info_bank    = $this->input->post('bq_refund_info_bank', true);
            $bq_refund_info_account = $this->input->post('bq_refund_info_account', true);
            $bq_refund_info_owner   = $this->input->post('bq_refund_info_owner', true);
            $bq_refund_info = implode(" / ", array_filter(array($bq_refund_info_bank, $bq_refund_info_account, $bq_refund_info_owner)));
            $bq_file = "";

            $query_data = array();
            $query_data['bq_member_num'] = $_SESSION['session_m_num'];
            $query_data['bq_category'] = $bq_category;

            /**
             * @date 200708
             * @modify 황기석
             * @desc 특수문자(이모지) 제거
             */
            $bq_content = preg_replace("/\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2}/", "", $bq_content);
            $bq_content = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $bq_content);
            $query_data['bq_content'] = $bq_content;

            if( $this->board_qna_model->get_overlapl_board_qna($query_data) === true ) {
                result_echo_json(get_status_code('success'), "", true, "");
            }

            if( empty($form_error_array) ) {

                //첨부파일 업로드
                $bq_file_path = $this->config->item('qna_img_path') . "/" . date("Y") . "/" . date("md");
                $bq_file_path_web = $this->config->item('qna_img_path_web') . "/" . date("Y") . "/" . date("md");

                create_directory($bq_file_path);

                if(count($_FILES['qna_img']['name']) > 0){

                    //이미지 경로arr
                    $bg_img_arr = array();

                    foreach ($_FILES['qna_img'] as $key => $row) {
                        if($key < 3){
                            foreach ($row as $kkey => $val) {
                                $_FILES['board_qna_file'.$kkey][$key] = $val;
                            }
                        }
                    }

                    unset($_FILES['qna_img']);

                    foreach ($_FILES as $key => $FILE) {

                        $config = array();
                        $config['upload_path'] = $bq_file_path;
                        $config['allowed_types'] = 'gif|jpg|jpeg|png';
                        $config['max_size'] = '10000';
                        $config['encrypt_name'] = true;

                        $this->load->library('upload');
                        $this->upload->initialize($config);

                        if ( $this->upload->do_upload($key) ) {

                            //업로드 이미지정보
                            $upload_data_array = $this->upload->data();
                            //이미지 org정보
                            $bg_img_arr[] = $bq_file_path_web.'/'.$upload_data_array['file_name'];
                            @chmod($upload_data_array['full_path'], 0775);

                        }else{

                            $form_error_array['bq_file'] = strip_tags($this->upload->display_errors());

                        } //upload end

                    } // foreach $_FILES End

                } // IF $_FILES count End

                //등록
                $query_data = array();
                $query_data['bq_member_num'] = $_SESSION['session_m_num'];
                $query_data['bq_category'] = $bq_category;
                if( !empty($bq_product_num) ) {
                    $query_data['bq_product_num'] = $bq_product_num;
                }
                $query_data['bq_product_name'] = $bq_product_name;
                $query_data['bq_name'] = $bq_name;
                $query_data['bq_contact'] = $bq_contact;
                $query_data['bq_content'] = $bq_content;
                $query_data['bq_refund_info'] = $bq_refund_info;
                $query_data['bq_file'] = count($bg_img_arr) > 0 ? json_encode_no_slashes($bg_img_arr) : '';

                if( $this->board_qna_model->insert_board_qna($query_data) ) {
                    total_stat("qna");

                    $msg = "접수되었습니다.";
                    if( !empty($bq_product_num) ) {
                        $msg .= "\n1:1문의에서 확인하세요.";
                    }

                    result_echo_json(get_status_code('success'), $msg, true, "alert");
                }
                else {
                    result_echo_json(get_status_code('error'), "문의 등록에 실패했습니다.", true, "alert");
                }
            }//end of if()
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }

}//end of class Qna