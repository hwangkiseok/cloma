<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 특가전
 */
class Special_offer extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('special_offer_model');
        $this->load->model('product_model');
    }//end of __construct()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['cate']            = trim($this->input->post_get('cate', true));
        $req['date_type']       = trim($this->input->post_get('date_type', true));
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));
        $req['md_div']          = trim($this->input->post_get('md_div', true));
        $req['term_yn']         = trim($this->input->post_get('term_yn', true));
        $req['display_state']   = $this->input->post_get('display_state', true);        //배열
        $req['sale_state']      = $this->input->post_get('sale_state', true);           //배열
        $req['sort_field']      = trim($this->input->post_get('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->post_get('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));
        $req['db']              = trim($this->input->post_get('db', true));             //DB
        $req['hash_chk']              = trim($this->input->post_get('hash_chk', true));             //DB

        $req['activate_flag']   = trim($this->input->post_get('activate_flag', true));

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
        $this->special_offer_lists();
    }//end of index()

    public function special_offer_lists() {

        $this->_header();

        //request
        $req = $this->_list_req();

        $this->load->view("/special_offer/special_offer_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();


    }//end of index()

    /**
     * 특가전 목록 (Ajax)
     */
    public function special_offer_list_ajax() {
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
        $list_count = $this->special_offer_model->get_special_offer_list($query_array, "", "", true)['cnt'];

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/special_offer/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $special_offer_list = $this->special_offer_model->get_special_offer_list($query_array, $page_result['start'], $page_result['limit']);

        //현재 노출되고 있는 특가전 수
        $special_offer_lists = $this->special_offer_model->get_activate_lists();
        $activate_num = count($special_offer_lists);

        //정렬
        $sort_array = array();
        $sort_array['sort_num'] = array("asc", "sorting");
        $sort_array['activate_flag'] = array("asc", "sorting");
        $sort_array['reg_date'] = array("asc", "sorting");
        $sort_array['mod_date'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/special_offer/special_offer_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "special_offer_list" => $special_offer_list,
            "pagination"        => $page_result['pagination'],
            'activate_num'     => number_format($activate_num)
        ));
    }//end of product_md_list_ajax()


    public function special_offer_detail() {

        $aInput = array(
            'seq'           => $this->input->get('seq')
        ,   'mode'          => $this->input->get('seq')?'update':'insert'
        ,   'action_path'   => $this->page_link->insert_proc
        );

        if($aInput['mode'] == 'update'){
            $special_offer_info = $this->special_offer_model->get_special_offer_info($aInput['seq']);
            $aInput['action_path'] = $this->page_link->update_proc;
        }

        $select_product_lists = $this->special_offer_model->get_product_lists();

        $i = 0;
        foreach ($select_product_lists as $v) {

            if($v['p_stock_state'] == 'N') continue;

            $aRes[$i]   = array(    'desc'              => $v['p_summary']
            ,   'label'             => $v['p_name']
            ,   'hash'              => $v['p_hash']
            ,   'value'             => $v['p_num']
            ,   'p_today_image'     => json_decode($v['p_rep_image'],true)
            ,   'p_review_count'    => $v['p_review_count']
            ,   'p_tot_order_count' => $v['p_tot_order_count']
            ,   'p_order_code'      => $v['p_order_code']
            ,   'p_review_count_str'    => number_format($v['p_review_count'])
            ,   'p_tot_order_count_str' => product_count($v['p_tot_order_count'])
            );
            $i++;

        }

        $aViewData = array(
            'aInput'                        => $aInput
        ,   'select_product_lists'          => $aRes
        ,   'special_offer_row'             => $special_offer_info['special_offer_row']
        ,   'special_offer_product_lists'   => $special_offer_info['special_offer_product_lists']
        );
        $this->_header();
        $this->load->view("/special_offer/special_offer_detail", $aViewData);
        $this->_footer();

    }//end of index()

    public function special_offer_sorting() {

        $special_offer_lists = $this->special_offer_model->get_activate_lists();

        $this->_header();
        $this->load->view("/special_offer/special_offer_sorting", array( 'special_offer_lists' => $special_offer_lists ));
        $this->_footer();

    }//end of index()


    public function get_product_row() {

        $aInput = array('p_num' => $this->input->post('p_num'));

        $product_row = $this->product_model->get_product_row($aInput['p_num']);
        $product_row['p_rep_image_array'] = json_decode($product_row['p_rep_image'], true);
        result_echo_json(get_status_code('success'), '', true, 'alert',array(''),$product_row);

    }

    public function special_offer_set_activate() {

        ajax_request_check(true);

        $aInput = array(
            'set_flag'  => $this->input->post('setFlag')
        ,   'seq'       => $this->input->post('seq')
        );

        $bRet = $this->special_offer_model->set_activate($aInput);

        if($bRet){
            result_echo_json(get_status_code('success'), '수정완료', true, 'alert');
        }else{
            result_echo_json(get_status_code('error'), '수정실패', true, 'alert');
        }

    }

    public function special_offer_delete(){

        ajax_request_check(true);

        $aInput = array( 'seq' => $this->input->post('seq') );

        $bRet = $this->special_offer_model->delete($aInput);

        if($bRet){
            result_echo_json(get_status_code('success'), '삭제완료', true, 'alert');
        }else{
            result_echo_json(get_status_code('error'), '삭제실패', true, 'alert');
        }

    }

    public function set_sorting(){

        ajax_request_check(true);

        $seq_arr = $this->input->post('seq');


        $this->db->trans_begin();

        $this->special_offer_model->set_sorting($seq_arr);

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            result_echo_json(get_status_code('error'), '변경실패', true, 'alert');
        } else {
            $this->db->trans_commit();
            result_echo_json(get_status_code('success'), '변경완료', true, 'alert');
        }

    }


    public function insert_proc(){

        ajax_request_check(true);

        $aInput = array(
            'activate_flag' => $this->input->post('activate_flag')
        ,   'view_type'     => $this->input->post('view_type')
        ,   'start_date'    => str_replace('-','',$this->input->post('start_date'))
        ,   'end_date'      => str_replace('-','',$this->input->post('end_date'))
        ,   'thema_name'    => $this->input->post('thema_name')
        ,   'use_class'     => $this->input->post('use_class')
        ,   'p_num_arr'     => $this->input->post('p_num')

        ,   'fold_flag'     => $this->input->post('fold_flag')
        ,   'folded'        => $this->input->post('folded')
        ,   'tag_arr'       => $this->input->post('tag_arr')
        ,   'head_title'    => $this->input->post('head_title')


        );

        {//유효성 검사

            if($aInput['activate_flag'] == '') $form_error_array['activate_flag'] = '활성여부를 선택해주세요';
            if($aInput['view_type'] == '') $form_error_array['view_type'] = '노출기간를 선택해주세요';
            if($aInput['view_type'] == 'A' && $aInput['start_date'] == '') $form_error_array['start_date'] = '노출기간(시작일)를 선택해주세요';
            if($aInput['view_type'] == 'A' && $aInput['end_date'] == '') $form_error_array['end_date'] = '노출기간(종료일)를 선택해주세요';
            if($aInput['thema_name'] == '') $form_error_array['thema_name'] = '테마명을 선택해주세요';
//            if($aInput['use_class'] == '') $form_error_array['use_class'] = '상품정렬를 선택해주세요';
            if(count($aInput['p_num_arr']) < 1) $form_error_array['p_num_arr'] = '등록할 상품을 선택해주세요';
//            if($aInput['fold_flag'] == 'Y' && $aInput['folded'] == '') $form_error_array['use_class'] = '상품 리스트 접기를 사용하는 경우 기본설정을 선택해주세요';
//            if(count($aInput['tag_arr']) < 1) $form_error_array['tag_arr'] = '등록할 태그를 입력해주세요';
            //if($aInput['head_title'] == '') $form_error_array['fold_flag'] = '헤드 타이틀을 입력해주세요';

        }

        //대표이미지 업로드 (썸네일 생성)
        $p_image_path_web = $this->config->item('special_offer_file_path_web') . "/" . date("Y") . "/" . date("md");
        $p_image_path = $this->config->item('special_offer_file_path') . "/" . date("Y") . "/" . date("md");
        create_directory($p_image_path);

        $config = array();
        $config['upload_path'] = $p_image_path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size']	= $this->config->item('upload_total_max_size');
        $config['encrypt_name'] = true;

        $this->load->library('upload', $config);

        if( $this->upload->do_upload('banner_img') ){
            $p_rep_image_data_array = $this->upload->data();
            $p_rep_image_data_array = create_thumb_image($p_rep_image_data_array, $p_image_path_web, $this->config->item('special_offer_banner_image_size'), true);
        } else {
            $form_error_array['banner_img'] = '배너이미지 업로드 에러 :: '.strip_tags($this->upload->display_errors());
        }//end of if()

//        if( $this->upload->do_upload('header_title_img') ){
//            $header_title_img_data_array = $this->upload->data();
//        } else {
//            $form_error_array['header_title_img'] = '배너이미지 업로드 에러 :: '.strip_tags($this->upload->display_errors());
//        }//end of if()

        if( empty($form_error_array) ) { //유효성 검사 ok

            $query_data = $aInput;
            $query_data['banner_img'] = $p_rep_image_data_array[1];
            //cdn purge
            cdn_purge($p_rep_image_data_array[1]);

            $this->db->trans_begin();

            $this->special_offer_model->insert_special_offer($query_data);

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                @unlink($p_rep_image_data_array['full_path']);
                result_echo_json(get_status_code('error'), lang('site_insert_fail'), true, 'alert');
            } else {
                $this->db->trans_commit();
                result_echo_json(get_status_code('success'), lang('site_insert_success'), true, 'alert');
            }

        }

        foreach ($form_error_array as $key => $item) {
            $form_error_array[$key] = strip_tags($item);
        }

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);

    }

    public function update_proc(){

        ajax_request_check(true);

        $aInput = array(
            'activate_flag' => $this->input->post('activate_flag')
        ,   'view_type'     => $this->input->post('view_type')
        ,   'start_date'    => str_replace('-','',$this->input->post('start_date'))
        ,   'end_date'      => str_replace('-','',$this->input->post('end_date'))
        ,   'thema_name'    => $this->input->post('thema_name')
        ,   'use_class'     => $this->input->post('use_class')
        ,   'p_num_arr'     => $this->input->post('p_num')
        ,   'seq'           => $this->input->post('seq')

        ,   'fold_flag'     => $this->input->post('fold_flag')
        ,   'folded'        => $this->input->post('folded')
        ,   'bg_color'      => $this->input->post('bg_color')

        ,   'tag_arr'       => $this->input->post('tag_arr')
        ,   'head_title'    => $this->input->post('head_title')


        );



        if(zsDebug()){
           //zsView($_REQUEST);exit;
        }

        {//유효성 검사

            if($aInput['activate_flag'] == '') $form_error_array['activate_flag'] = '활성여부를 선택해주세요';
            if($aInput['view_type'] == '') $form_error_array['view_type'] = '노출기간를 선택해주세요';
            if($aInput['view_type'] == 'A' && $aInput['start_date'] == '') $form_error_array['start_date'] = '노출기간(시작일)를 선택해주세요';
            if($aInput['view_type'] == 'A' && $aInput['end_date'] == '') $form_error_array['end_date'] = '노출기간(종료일)를 선택해주세요';
            if($aInput['thema_name'] == '') $form_error_array['thema_name'] = '테마명을 선택해주세요';
//            if($aInput['use_class'] == '') $form_error_array['use_class'] = '상품정렬를 선택해주세요';
            if(count($aInput['p_num_arr']) < 1) $form_error_array['p_num_arr'] = '등록할 상품을 선택해주세요';
//            if($aInput['fold_flag'] == '') $form_error_array['fold_flag'] = '상품 리스트 접기 여부를 선택해주세요';
//            if($aInput['fold_flag'] == 'Y' && $aInput['folded'] == '') $form_error_array['folded'] = '상품 리스트 접기를 사용하는 경우 기본설정을 선택해주세요';
//            if(count($aInput['tag_arr']) < 1) $form_error_array['tag_arr'] = '등록할 태그를 입력해주세요';
            //if($aInput['head_title'] == '') $form_error_array['fold_flag'] = '헤드 타이틀을 입력해주세요';

        }

        $is_file = false;
        $is_file2 = false;

        if($_FILES['banner_img']){

            //대표이미지 업로드 (썸네일 생성)
            $p_image_path_web = $this->config->item('special_offer_file_path_web') . "/" . date("Y") . "/" . date("md");
            $p_image_path = $this->config->item('special_offer_file_path') . "/" . date("Y") . "/" . date("md");
            create_directory($p_image_path);

            $config = array();
            $config['upload_path'] = $p_image_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size']	= $this->config->item('upload_total_max_size');
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);

            if( $this->upload->do_upload('banner_img') ){
                $is_file = true;
                $p_rep_image_data_array = $this->upload->data();
                $p_rep_image_data_array = create_thumb_image($p_rep_image_data_array, $p_image_path_web, $this->config->item('special_offer_banner_image_size'), true);

            } else {
                $form_error_array['banner_img'] = '배너이미지 업로드 에러 :: '.strip_tags($this->upload->display_errors());
            }//end of if()

        }

        //$header_title_img_data_array
        if( empty($form_error_array) ) { //유효성 검사 ok

            $query_data                     = $aInput;
            $query_data['banner_img']       = '';
            if( $is_file ) {
                $query_data['banner_img']       = $p_rep_image_data_array[1];
                //cdn purge
                cdn_purge($p_rep_image_data_array[1]);
            }


            $this->db->trans_begin();

            $this->special_offer_model->update_special_offer($query_data);

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                @unlink($p_rep_image_data_array['full_path']);
                result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
            } else {
                $this->db->trans_commit();
                result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
            }

        }

        foreach ($form_error_array as $key => $item) {
            $form_error_array[$key] = strip_tags($item);
        }

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);

    }




}//end of class Special_offer

