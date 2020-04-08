<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 쿠폰 관련 컨트롤러
 */
class Coupon extends A_Controller {

    public $issue_type = 'coupon';

    public function __construct() {
        parent::__construct();

        $this->load->model('coupon_model');

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

        $this->load->view("/coupon/coupon_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page,
            'condition'     => $condition
        ));

        $this->_footer();

    }

    public function coupon_upsert_pop() {

        $aInput = array(
            'mode'  => $this->input->get('m')
        ,   'seq'   => $this->input->get('seq')
        );
        $aSelectedCouponLists = array();
        $aTempArr = array();

        $inid = $this->config->item('order_cpid');
        /**
         * @date 180308
         * @modify 황기석
         * @desc 관리자/사용자쿠폰 모두
         */
        $jCouponLists = get_coupon_list($inid/*, "issue_type=2"*/);
        $aCouponLists = json_decode($jCouponLists,true);

        if($aInput['mode'] == 'update'){

            $aCouponInfoLists = $this->coupon_model->get_coupon_info_row($aInput);

            foreach ($aCouponLists['data'] as $row) {
                $aTempArr[$row['cpn_uid']] = $row;
            }

            $coupon_seq_str = $aCouponInfoLists['coupon_seq_str'];
            $coupon_seq_arr = explode(',',$coupon_seq_str);

            foreach ($coupon_seq_arr as $v) {
                $aSelectedCouponLists[] = $aTempArr[$v];
            }

        }

        $this->load->view("/coupon/coupon_upsert_pop",array(
            'aInput'        => $aInput
        ,   'aCouponLists'  => $aCouponLists['data']
        ,   'aSelectedCouponLists'  => $aSelectedCouponLists
        ,   'aCouponInfoLists'      => $aCouponInfoLists
        ));

    }

    public function coupon_set_activate(){

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

    public function coupon_delete_proc(){

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
        ,   'tpl_code'  => 'coupon_noti'
        ,   'contents'  => '쇼핑앱::쿠폰소멸알림톡발송'
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
        $jCouponLists   = get_coupon_list($inid, "issue_type=2");
        $aCouponLists   = json_decode($jCouponLists,true);

        foreach ($aCouponLists['data'] as $row) {
            $aTempArr[$row['cpn_uid']] = $row;
        }

        foreach ($cpn_uid_arr as $v) {
            if(empty($aTempArr[$v])) $isGo = false;
        }

        if($isGo == false){
            result_echo_json(get_status_code('error'), '셋팅된 쿠폰 중 발급되지 않는 쿠폰이 있습니다.\\n 확인 후 다시시도해주세요', true, 'alert');
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

    public function alimtalk_send_proc(){
        ajax_request_check();

        $aInput = array(
            'seq'       => $this->input->post('seq')
        ,   'code'      => $this->input->post('code')
        ,   'send_type' => $this->input->post('send_type')
        ,   'tpl_code'  => 'coupon_noti'
        ,   'contents'  => '쇼핑앱::쿠폰소멸알림톡발송'
        ,   'mode'      => 'setBlockAlimtalk'
        );

        //--------------------------------------------------------------------------------
        // 알림톡 발송전 선택된 쿠폰이 발급이 가능한 쿠폰인지 확인하는 유효성검사 처리
        //--------------------------------------------------------------------------------

        $aCouponInfoRow = $this->coupon_model->get_coupon_info_row($aInput);
        $aTempArr       = array();
        $isGo           = true;
        $cpn_uid_arr    = explode(',',$aCouponInfoRow['coupon_seq_str']);

        $inid           = $this->config->item('order_cpid');
        $jCouponLists   = get_coupon_list($inid, "issue_type=2");
        $aCouponLists   = json_decode($jCouponLists,true);

        foreach ($aCouponLists['data'] as $row) {
            $aTempArr[$row['cpn_uid']] = $row;
        }

        foreach ($cpn_uid_arr as $v) {
            if(empty($aTempArr[$v])) $isGo = false;
        }

        if($isGo == false){
            result_echo_json(get_status_code('error'), '셋팅된 쿠폰 중 발급되지 않는 쿠폰이 있습니다.\\n 확인 후 다시시도해주세요', true, 'alert');
        }

        //--------------------------------------------------------------------------------
        // 알림톡 발송전 선택된 쿠폰이 발급이 가능한 쿠폰인지 확인하는 유효성검사 처리 END
        //--------------------------------------------------------------------------------

        if($aInput['seq'] == ''){
            result_echo_json(get_status_code('error'), '쿠폰 SEQ 값 누락', true, 'alert');
        }
        //알림톡 발송 update
        $bRet = $this->coupon_model->send_alimtalk_log($aInput,'I');

        if(!$bRet){
            result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
        }

        $url        = $this->config->item("order_site_http") . "/api/zsApi.php";
        $param      = "dummy=" . time() . "&inid=" . $this->config->item("order_cpid") .'&'. http_build_query($aInput);
        $resp       = http_post_request($url, $param);
        $aResult    = json_decode($resp,true);

        if($aResult['success']){
            result_echo_json(get_status_code('success'),'',true);
        }

        $this->coupon_model->send_alimtalk_log($aInput,'W');
        result_echo_json(get_status_code('error'), $aResult['msg'], true, 'alert');

    }

    public function coupon_update_proc(){

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
        ,   'coupon_info'   => $this->input->post('coupon_info') //[쿠폰코드]::[쿠폰seq]
        );

        if( $aInput['mode'] == 'update' ){
            $set_rules_array['seq'] = array("field" => "seq", "label" => "쿠폰SEQ", "rules" => "required|".$this->default_set_rules);
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
     * 특가전 목록 (Ajax)
     */
    public function get_coupon_lists_ajax() {
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

        $this->load->view("/coupon/coupon_list_ajax", array(
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



    /**
     * 쿠폰목록 (Ajax)
     */
    public function coupon_list_ajax() {
        ajax_request_check();

        //request
        $req['print_type'] = trim($this->input->post_get('print_type', true));  //출력타입(''|select)

        $inid = $this->config->item('order_cpid');

        //회원쿠폰목록 요청
        $result = get_coupon_list($inid, "issue_type=2");
        $result_obj = json_decode($result);
        $result_list = $result_obj->data;
        $data = array();
        if( !empty($result_list) ) {
            foreach ($result_list as $key => $item) {
                if( $item->cpn_state == "N" ) {
                    continue;
                }

                $data_item = array();
                $data_item['uid'] = $item->cpn_uid;
                $data_item['code'] = $item->cpn_code;
                $data_item['name'] = $item->cpn_name;
                $data_item['kind'] = $item->cpn_kind;
                $data_item['overlap_yn'] = $item->cpn_overlap_use_yn;

                $data_item['kind_txt'] = ($item->cpn_kind == "1") ? $item->cpn_overlap_use_yn == 'Y' ?'중복쿠폰': "상품쿠폰" : "무료배송쿠폰";
                $data_item['price'] = ($item->cpn_price_type == "1") ? number_format($item->cpn_price) . "원" : number_format($item->cpn_price) . "%";
                $data_item['price_max'] = "";
                if( !empty($item->cpn_price_max) ) {
                    $data_item['price_max'] = " (최대 " . number_format($item->cpn_price_max) . "원 할인)";
                }
                $data_item['min_orderprice'] = "";
                if( !empty($item->cpn_min_orderprice) ) {
                    $data_item['min_orderprice'] = "(" . number_format($item->cpn_min_orderprice) . "원 이상 구매)";
                }


                $data_item['name_text'] = "[" . $data_item['kind_txt'] . "] " . $data_item['name'];
                if( $data_item['kind'] == "1" ) {
                    $data_item['name_text'] .= "(" . $data_item['price'] . ")";
                    if( $item->cpn_price_type == "2" ) {
                        $data_item['name_text'] .= $data_item['price_max'];
                    }
                }
                $data_item['name_text'] .= $data_item['min_orderprice'];

                $data[] = $data_item;
            }//end of foreach()
        }//end of if()

        if( $req['print_type'] == "select" ) {
            $html = '';
            $html .= '<select name="coupon_select" class="form-control">';
            $html .= '  <option value="">* 쿠폰선택 *</option>';

            foreach ($data as $key => $item) {

                if( $item['overlap_yn'] == 'Y'){
                    $html .= '<option value="' . $item['uid'] . '" style="color:#d9534f;" >' . $item['name_text'] . '</option>';
                }else{
                    $html .= '<option value="' . $item['uid'] . '" style="color:#337ab7;" >' . $item['name_text'] . '</option>';
                }

            }//end of foreach()

            $html .= '</select>';

            echo $html;
        }
        else {
            echo json_encode_no_slashes($data);
        }
    }//end of coupon_list_ajax()


    /**
     * 회원쿠폰목록 (Ajax)
     */
    public function coupon_member_list_ajax() {
        ajax_request_check();

        //request
        $req['m_num'] = trim($this->input->post_get('m_num', true));
        if( empty($req['m_num']) ) {
            $this->output->set_status_header('403');
            exit;
        }

        //model
        $this->load->model('member_model');

        $member_row = $this->member_model->get_member_row(array('m_num' => $req['m_num']));
        if( empty($member_row) ) {
            $this->output->set_status_header('403');
            exit;
        }

        $inid = $this->config->item('order_cpid');

        //회원쿠폰목록(전체) 요청

/*        $result_obj = json_decode($result);*/
        $result_list = array();//$result_obj->data;

        $data = array();
        if( !empty($result_list) ) {
            foreach ($result_list as $key => $item) {
                if( $item->cpnm_state == "N" ) {
                    continue;
                }

                $data_item = array();
                $data_item['uid'] = $item->cpnm_uid;
                $data_item['code'] = $item->cpnm_coupon_code;
                $data_item['use_able'] = "Y";
                $data_item['state_text'] = "<b>미사용</b>";
                if( $item->cpnm_use == "Y" ) {
                    $data_item['state_text'] = "사용";
                    $data_item['use_able'] = "N";
                }
                else {
                    if( $item->cpnm_expire_datetime < current_datetime() ) {
                        $data_item['state_text'] = "기간만료";
                        $data_item['use_able'] = "N";
                    }
                }

                $data_item['name'] = $item->cpnm_coupon_name;
                $data_item['kind'] = $item->cpnm_coupon_kind;
                $data_item['kind_txt'] = ($item->cpnm_coupon_kind == "1") ? "상품쿠폰" : "무료배송쿠폰";
                $data_item['price'] = ($item->cpnm_coupon_price_type == "1") ? number_format($item->cpnm_coupon_price) . "원" : number_format($item->cpnm_coupon_price) . "%";
                if( $item->cpnm_coupon_kind == "2" ) {
                    $data_item['price'] = "";
                }
                $data_item['price_max'] = "";
                if( !empty($item->cpnm_coupon_price_max) ) {
                    $data_item['price_max'] = "(최대 " . number_format($item->cpnm_coupon_price_max) . "원 할인)";
                }
                $data_item['min_orderprice'] = "";
                if( !empty($item->cpnm_coupon_min_orderprice) ) {
                    $data_item['min_orderprice'] = number_format($item->cpnm_coupon_min_orderprice) . "원 이상 구매";
                }
                $data_item['reg_date'] = date("Y-m-d", strtotime($item->cpnm_reg_datetime));
                $data_item['reg_datetime'] = date("Y-m-d H:i:s", strtotime($item->cpnm_reg_datetime));
                $data_item['use_date'] = ( !empty($item->cpnm_use_datetime) ) ? date("Y-m-d", strtotime($item->cpnm_use_datetime)) : "";
                $data_item['use_datetime'] = ( !empty($item->cpnm_use_datetime) ) ? date("Y-m-d H:i:s", strtotime($item->cpnm_use_datetime)) : "";
                $data_item['expire_datetime'] = date("Y-m-d", strtotime($item->cpnm_expire_datetime));

                $data[] = $data_item;
            }//end of foreach()
        }//end of if()

        //json
        if( is_json_request() ) {
            echo json_encode_no_slashes($data);
        }
        //html
        else {
            $this->load->view("/coupon/coupon_member_list_ajax", array(
                'data'  => $data
            ));
        }
    }//end of coupon_member_list_ajax()

    /**
     * 쿠폰발급 (Ajax)
     */
    public function coupon_issue_ajax() {
        ajax_request_check();

        //request
        $req['uid'] = trim($this->input->post_get('uid', true));
        $req['m_num'] = trim($this->input->post_get('m_num', true));
        if( empty($req['uid']) || empty($req['m_num']) ) {
            $this->output->set_status_header('403');
            exit;
        }

        //model
        $this->load->model('member_model');

        $member_row = $this->member_model->get_member_row(array('m_num' => $req['m_num']));
        if( empty($member_row) ) {
            $this->output->set_status_header('403');
            exit;
        }

        $inid = $this->config->item('order_cpid');

        $result = array();
        echo $result;
    }//end of coupon_issue_ajax()

    /**
     * 회원쿠폰삭제 (Ajax)
     */
    public function coupon_delete_ajax() {
        ajax_request_check();

        //request
        $req['uid'] = trim($this->input->post_get('uid', true));
        $req['m_num'] = trim($this->input->post_get('m_num', true));
        if( empty($req['uid']) || empty($req['m_num']) ) {
            $this->output->set_status_header('403');
            exit;
        }

        //model
        $this->load->model('member_model');

        $member_row = $this->member_model->get_member_row(array('m_num' => $req['m_num']));
        if( empty($member_row) ) {
            $this->output->set_status_header('403');
            exit;
        }

        $inid = $this->config->item('order_cpid');

        $result = array();

        echo $result;
    }//end of coupon_delete_ajax()

}//end of class Coupon