<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 특가전
 */
class Main_thema extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('main_thema_model');
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
        $this->main_thema_lists();
    }//end of index()

    public function main_thema_lists() {

        $this->_header();

        //request
        $req = $this->_list_req();

        $this->load->view("/main_thema/main_thema_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();


    }//end of index()

    /**
     * 특가전 목록 (Ajax)
     */
    public function main_thema_list_ajax() {
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
        $list_count = $this->main_thema_model->get_main_thema_list($query_array, "", "", true)['cnt'];

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/main_thema/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $main_thema_list = $this->main_thema_model->get_main_thema_list($query_array, $page_result['start'], $page_result['limit']);

        //현재 노출되고 있는 특가전 수
        $main_thema_lists = $this->main_thema_model->get_activate_lists();
        $activate_num = count($main_thema_lists);

        //정렬
        $sort_array = array();
        $sort_array['sort_num'] = array("asc", "sorting");
        $sort_array['activate_flag'] = array("asc", "sorting");
        $sort_array['reg_date'] = array("asc", "sorting");
        $sort_array['mod_date'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/main_thema/main_thema_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "main_thema_list" => $main_thema_list,
            "pagination"        => $page_result['pagination'],
            'activate_num'     => number_format($activate_num)
        ));
    }//end of product_md_list_ajax()


    public function main_thema_detail() {

        $aInput = array(
            'seq'           => $this->input->get('seq')
        ,   'mode'          => $this->input->get('seq')?'update':'insert'
        ,   'action_path'   => $this->page_link->insert_proc
        );

        if($aInput['mode'] == 'update'){
            $main_thema_info = $this->main_thema_model->get_main_thema_info($aInput['seq']);
            $aInput['action_path'] = $this->page_link->update_proc;
        }

        $select_product_lists = $this->main_thema_model->get_product_lists();

        $i = 0;
        foreach ($select_product_lists as $v) {

            if($v['p_stock_state'] == 'N') continue;

            $aRes[$i]   = array(    'desc'              => $v['p_summary']
            ,   'label'             => $v['p_name']
            ,   'hash'              => $v['p_hash']
            ,   'value'             => $v['p_num']

            ,   'p_display_state_str'   => get_config_item_text($v['p_display_state'], "product_display_state")
            ,   'p_sale_state_str'      => get_config_item_text($v['p_sale_state'], "product_sale_state")
            ,   'p_stock_state_str'     => get_config_item_text($v['p_stock_state'], "product_stock_state")

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
        ,   'main_thema_row'             => $main_thema_info['main_thema_row']
        ,   'main_thema_product_lists'   => $main_thema_info['main_thema_product_lists']
        );
        $this->_header();
        $this->load->view("/main_thema/main_thema_detail", $aViewData);
        $this->_footer();

    }//end of index()

    public function main_thema_sorting() {

        $main_thema_lists = $this->main_thema_model->get_activate_lists();

        $this->_header();
        $this->load->view("/main_thema/main_thema_sorting", array( 'main_thema_lists' => $main_thema_lists ));
        $this->_footer();

    }//end of index()


    public function get_product_row() {

        $aInput = array('p_num' => $this->input->post('p_num'));

        $product_row = $this->product_model->get_product_row($aInput['p_num']);
        $product_row['p_rep_image_array'] = json_decode($product_row['p_rep_image'], true);
        result_echo_json(get_status_code('success'), '', true, 'alert',array(''),$product_row);

    }

    public function main_thema_set_activate() {

        ajax_request_check(true);

        $aInput = array(
            'set_flag'  => $this->input->post('setFlag')
        ,   'seq'       => $this->input->post('seq')
        );

        $bRet = $this->main_thema_model->set_activate($aInput);

        if($bRet){
            result_echo_json(get_status_code('success'), '수정완료', true, 'alert');
        }else{
            result_echo_json(get_status_code('error'), '수정실패', true, 'alert');
        }

    }

    public function main_thema_delete(){

        ajax_request_check(true);

        $aInput = array( 'seq' => $this->input->post('seq') );

        $bRet = $this->main_thema_model->delete($aInput);

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

        $this->main_thema_model->set_sorting($seq_arr);

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
        ,   'display_type'  => $this->input->post('display_type')
        ,   'p_num_arr'     => $this->input->post('p_num')
        );

        {//유효성 검사

            if($aInput['activate_flag'] == '') $form_error_array['activate_flag'] = '활성여부를 선택해주세요';
            if($aInput['view_type'] == '') $form_error_array['view_type'] = '노출기간를 선택해주세요';
            if($aInput['view_type'] == 'A' && $aInput['start_date'] == '') $form_error_array['start_date'] = '노출기간(시작일)를 선택해주세요';
            if($aInput['view_type'] == 'A' && $aInput['end_date'] == '') $form_error_array['end_date'] = '노출기간(종료일)를 선택해주세요';
            if($aInput['thema_name'] == '') $form_error_array['thema_name'] = '테마명을 선택해주세요';
            if(count($aInput['p_num_arr']) < 1) $form_error_array['p_num_arr'] = '등록할 상품을 선택해주세요';

        }

        if( empty($form_error_array) ) { //유효성 검사 ok

            $query_data = $aInput;

            $this->db->trans_begin();

            $this->main_thema_model->insert_main_thema($query_data);

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
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
        ,   'display_type'  => $this->input->post('display_type')
        ,   'start_date'    => str_replace('-','',$this->input->post('start_date'))
        ,   'end_date'      => str_replace('-','',$this->input->post('end_date'))
        ,   'thema_name'    => $this->input->post('thema_name')
        ,   'p_num_arr'     => $this->input->post('p_num')
        ,   'seq'           => $this->input->post('seq')
        );

        {//유효성 검사

            if($aInput['activate_flag'] == '') $form_error_array['activate_flag'] = '활성여부를 선택해주세요';
            if($aInput['view_type'] == '') $form_error_array['view_type'] = '노출기간를 선택해주세요';
            if($aInput['view_type'] == 'A' && $aInput['start_date'] == '') $form_error_array['start_date'] = '노출기간(시작일)를 선택해주세요';
            if($aInput['view_type'] == 'A' && $aInput['end_date'] == '') $form_error_array['end_date'] = '노출기간(종료일)를 선택해주세요';
            if($aInput['thema_name'] == '') $form_error_array['thema_name'] = '테마명을 선택해주세요';
            if(count($aInput['p_num_arr']) < 1) $form_error_array['p_num_arr'] = '등록할 상품을 선택해주세요';

        }

        //$header_title_img_data_array
        if( empty($form_error_array) ) { //유효성 검사 ok

            $query_data                     = $aInput;
            $this->db->trans_begin();

            $this->main_thema_model->update_main_thema($query_data);

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
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

}//end of class main_thema

