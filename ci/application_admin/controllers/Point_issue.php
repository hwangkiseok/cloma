<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 쿠폰 관련 컨트롤러
 */
class point_issue extends A_Controller {

    public $issue_type = 'point';

    public function __construct() {
        parent::__construct();

        $this->load->model('coupon_model');
        $this->load->model('point_model');

    }

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));
        $req['issue_type']      = $this->issue_type;

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        return $req;
    }//end of _list_req()

    public function index() {

        //request
        $req = $this->_list_req();

        $condition = $this->coupon_model->getConditionAlimtalkProc();

        $this->_header();

        $this->load->view("/point_issue/point_issue_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page,
            'condition'     => $condition
        ));

        $this->_footer();

    }

    public function point_issue_upsert_pop() {

        $aInput = array(
            'mode'  => $this->input->get('m')
        ,   'seq'   => $this->input->get('seq')
        );

        $aSelectedCouponLists   = array();
        $aTempArr               = array();
        $inid                   = $this->config->item('order_cpid');
        $param                  = array( 'inid' => $inid );

        $aCouponLists = $this->point_model->getPointMasterList($param);

        if($aInput['mode'] == 'update'){

            $aCouponInfoLists = $this->coupon_model->get_coupon_info_row($aInput);

            foreach ($aCouponLists as $row) { $row = (array)$row;
                $aTempArr[$row['pt_uid']] = $row;
            }

            $coupon_seq_str = $aCouponInfoLists['coupon_seq_str'];
            $coupon_seq_arr = explode(',',$coupon_seq_str);

            foreach ($coupon_seq_arr as $v) {
                $aSelectedCouponLists[] = $aTempArr[$v];
            }

        }

        $this->load->view("/point_issue/point_issue_upsert_pop",array(
            'aInput'                => $aInput
        ,   'aCouponLists'          => $aCouponLists
        ,   'aSelectedCouponLists'  => $aSelectedCouponLists
        ,   'aCouponInfoLists'      => $aCouponInfoLists
        ));

    }

    public function point_issue_set_activate(){

        ajax_request_check();

        $aInput = array(
            'seq'       => $this->input->post('seq')
        ,   'flag'      => $this->input->post('flag')
        ,   'set_flag'  => $this->input->post('flag') =='Y'?'N':'Y'

        );

        if($aInput['seq'] == ''){
            result_echo_json(get_status_code('error'), '쿠폰 SEQ 값 누락', true, 'alert');
        }

        if($aInput['flag'] == ''){
            result_echo_json(get_status_code('error'), '쿠폰 flag 값 누락', true, 'alert');
        }

        $bRet = $this->coupon_model->set_coupon_activate($aInput);

        if($bRet){
            result_echo_json(get_status_code('success'),'',true);
        }

        result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');

    }

    public function point_issue_delete_proc(){

        ajax_request_check();

        $aInput = array( 'seq' => $this->input->post('seq') );

        if($aInput['seq'] == ''){
            result_echo_json(get_status_code('error'), '쿠폰 SEQ 값 누락', true, 'alert');
        }

        $bRet = $this->coupon_model->delete_coupon($aInput);
        if($bRet){
            result_echo_json(get_status_code('success'),'',true);
        }
        result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');

    }

    public function alimtalk_send_proc_v2(){

        ajax_request_check();

        $aInput = array(
            'seq'       => $this->input->post('seq')
        ,   'code'      => $this->input->post('code')
        ,   'send_type' => $this->input->post('send_type')
        ,   'tpl_code'  => 'point_noti'
        ,   'contents'  => '쇼핑앱::적립금소멸알림톡발송'
        ,   'mode'      => 'setBlockAlimtalk'
        ,   'inid'      => $this->config->item('order_cpid')

        );

        if($aInput['seq'] == ''){
            result_echo_json(get_status_code('error'), '쿠폰 SEQ 값 누락', true, 'alert');
        }

        //--------------------------------------------------------------------------------
        // 알림톡 발송전 선택된 쿠폰이 발급이 가능한 쿠폰인지 확인하는 유효성검사 처리
        //--------------------------------------------------------------------------------


        $aCouponInfoRow = $this->coupon_model->get_coupon_info_row($aInput);
        $aTempArr       = array();
        $isGo           = true;
        $cpn_uid_arr    = explode(',',$aCouponInfoRow['coupon_seq_str']);

        $inid           = $this->config->item('order_cpid');
        $param          = array( 'inid' => $inid );
        $aCouponLists   = $this->point_model->getPointMasterList($param);

        foreach ($aCouponLists as $row) { $row = (array)$row;
            $aTempArr[$row['pt_uid']] = $row;
        }

        foreach ($cpn_uid_arr as $v) {
            if(empty($aTempArr[$v])) $isGo = false;
        }

        if($isGo == false || empty($aCouponLists) == true){
            result_echo_json(get_status_code('error'), '셋팅된 적립금 중 발급되지 않는 적립금이 있습니다.\\n 확인 후 다시시도해주세요', true, 'alert');
        }

        //--------------------------------------------------------------------------------
        // 알림톡 발송전 선택된 쿠폰이 발급이 가능한 쿠폰인지 확인하는 유효성검사 처리 END
        //--------------------------------------------------------------------------------

        //알림톡 발송 update
        $bRet = $this->coupon_model->send_alimtalk_log($aInput,'I');

        if(!$bRet){
            result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
        }

        //push_server_09 upsert
        $aResult = $this->coupon_model->setBlockAlimtalk($aInput);

        if($aResult['success'] == true){
            result_echo_json(get_status_code('success'),'',true);
        }

        $this->coupon_model->send_alimtalk_log($aInput,'W');
        result_echo_json(get_status_code('error'), $aResult['msg'], true, 'alert');

    }


    public function point_issue_update_proc(){

        ajax_request_check();

        $this->load->library('form_validation');

        $set_rules_array = array(
            "date1"         => array("field" => "start_date", "label" => "발급기간 시작일", "rules" => "required|".$this->default_set_rules)
        ,   "date2"         => array("field" => "end_date", "label" => "발급기간 종료일", "rules" => "required|".$this->default_set_rules)
        ,   "coupon_info"   => array("field" => "coupon_info[]", "label" => "쿠폰정보", "rules" => "required|".$this->default_set_rules)
        );

        $aInput = array(
            'mode'          => trim($this->input->post('mode'))
        ,   'seq'           => trim($this->input->post('seq'))
        ,   'start_date'    => trim($this->input->post('start_date'))
        ,   'end_date'      => trim($this->input->post('end_date'))
        ,   'issue_type'    => $this->issue_type
        ,   'coupon_info'   => $this->input->post('coupon_info') //array ==> [쿠폰코드]::[쿠폰seq]::[쿠폰명]::[type]
        );

        if( $aInput['mode'] == 'update' ){
            $set_rules_array['seq'] = array("field" => "seq", "label" => "쿠폰SEQ", "rules" => "required|".$this->default_set_rules);
        }

        $isGo = true;
        $err_arr = array();
        foreach ($aInput['coupon_info'] as $data) {
            /* *
             * $data_arr[0] => 쿠폰코드
             * $data_arr[1] => 쿠폰seq
             * $data_arr[2] => 쿠폰명
             * $data_arr[3] => 발급type
             * */
            $data_arr = explode('::',$data);

            if( empty($data_arr[0]) == true ) {
                $isGo       = false;
                $err_arr[]  = $data_arr[2];
            }

        }

        if($isGo == false){
            $err_msg = "";
            foreach ($err_arr as $k => $v) {
                if($k > 0) $err_msg .= "\n";
                $err_msg .= "{$v}의 코드값이 없습니다. 포인트설정을 확인해주세요.";
            }
            result_echo_json(get_status_code('error'), $err_msg, true, 'alert');

        }

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {

            if($aInput['mode'] == 'insert'){
                $bRet = $this->coupon_model->coupon_insert($aInput);
                $msgSuccess = lang('site_insert_success');
                $msgFail    = lang('site_insert_fail');
            }else{
                $bRet = $this->coupon_model->coupon_update($aInput);
                $msgSuccess = lang('site_update_success');
                $msgFail    = lang('site_update_fail');
            }

            if($bRet){
                result_echo_json(get_status_code('success'), $msgSuccess, true, 'alert');
            }else{
                result_echo_json(get_status_code('error'), $msgFail, true, 'alert');
            }

        }

        $form_error_array_tmp = $this->form_validation->error_array();

        foreach ($form_error_array_tmp as $k => $r) {
            if( preg_match("/(\[|\])/", $k) ) {
                $key_array = explode("[", $k);
                $key = $key_array[0];
            }else{
                $key = $k;
            }
            $form_error_array[$key] = $r;
        }

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);
        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);

    }


    /**
     */
    public function get_point_issue_lists_ajax() {
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
        $list_count = $this->coupon_model->get_coupon_lists($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/coupon/get_coupon_lists_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $coupon_info_list = $this->coupon_model->get_coupon_lists($query_array, $page_result['start'], $page_result['limit']);

        //정렬
        $sort_array = array();
        $sort_array['sort_num'] = array("asc", "sorting");
        $sort_array['activate_flag'] = array("asc", "sorting");
        $sort_array['reg_date'] = array("asc", "sorting");
        $sort_array['mod_date'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/point_issue/point_issue_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "coupon_info_list" => $coupon_info_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of product_md_list_ajax()

}//end of class Coupon