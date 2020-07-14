<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *  주문 관련 컨트롤러
 */
class Order extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('order_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->order_cancel_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['dateType']        = trim($this->input->post_get('dateType', true));
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));

        $req['form_status_cd']  = trim($this->input->post_get('form_status_cd', true));
        $req['after_form_status_cd'] = trim($this->input->post_get('after_form_status_cd', true));


        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        return $req;
    }//end of _list_req()

    /**
     * 댓글 목록
     */
    public function order_cancel_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view("/order/cancel_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();

    }//end of order_list()

    /**
     * 댓글 목록 데이터 (ajax)
     */
    public function order_cancel_list_ajax() {
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
        $list_count = $this->order_model->get_order_cancel_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => "/order/cancel_list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
            //"sort"          => "reverse"
        ));

        $cancel_list = $this->order_model->get_order_cancel_list($query_array, $page_result['start'], $page_result['limit']);

        //정렬
//        $sort_array = array();
//        $sort_array['cmt_table'] = array("asc", "sorting");
//
//        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
//        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/order/cancel_list_ajax", array(
            "req"           => $req,
            "GV"            => $GV,
            "PGV"           => $PGV,
//            "sort_array"    => $sort_array,
            "list_count"    => $list_count,
            "list_per_page" => $req['list_per_page'],
            "page"          => $req['page'],
            "cancel_list"    => $cancel_list,
            "pagination"    => $page_result['pagination']
        ));
    }//end order_list_ajax;

    public function order_cancel_pop() {

        $aInput['seq']  = $this->input->get('seq');
        $aOrderInfo     = $this->order_model->get_cancel_order_row($aInput['seq']);

//        $view_file = '/order/cancel_pop';
        $view_file = '/order/cancel_pop_v2';

        $this->load->view($view_file, array(
            "aInput"      => $aInput,
            "aOrderInfo"  => $aOrderInfo
        ));

    }

    public function order_exchange_pop() {

        $aInput['seq']  = $this->input->get('seq');
        $aOrderInfo     = $this->order_model->get_cancel_order_row($aInput['seq']);

        $this->load->view("/order/exchange_pop", array(
            "aInput"      => $aInput,
            "aOrderInfo"  => $aOrderInfo
        ));

    }


    public function order_proc_flag() {
        ajax_request_check();

        //request
        $req['seq'] = $this->input->post_get('seq', true);
        $req['proc_flag'] = $this->input->post_get('proc_flag', true);

        //글정보
        $order_row = $this->order_model->get_cancel_order_row($req['seq']);

        if( empty($order_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        // 참고처리
        $query_data['proc_flag'] = $req['proc_flag'];
        if( $req['proc_flag'] == "Y" ) {
            $query_data['proc_date'] = current_datetime();
        }
        else {
            $query_data['proc_date'] = "";
        }

        if( $this->order_model->update_cancel_order($req['seq'],$query_data) ) {
            result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
        }

    }//end of offer_proc_flag()


    public function update_cancel_proc_v2(){


        ajax_request_check();

        $this->load->library('form_validation');

        $addSetRule = '';
        if($this->input->post('after_status_cd') == 67 && $this->input->post('proc_flag') == 'Y'){
            //$addSetRule = '|required';
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "seq"                       => array("field" => "seq", "label" => "seq", "rules" => "required|numeric|".$this->default_set_rules)
        ,   "exchange_delivery"         => array("field" => "exchange_delivery", "label" => "택배사", "rules" => "numeric|".$this->default_set_rules.$addSetRule)
        ,   "exchange_delivery_no"      => array("field" => "exchange_delivery_no", "label" => "송장번호", "rules" => "numeric|".$this->default_set_rules.$addSetRule)
        ,   "bigo"                      => array("field" => "bigo", "label" => "비고", "rules" => $this->default_set_rules)
        ,   "proc_flag"                 => array("field" => "proc_flag", "label" => "처리여부", "rules" => "in_list[Y,N]|".$this->default_set_rules)


        ,   "account_holder"            => array("field" => "account_holder", "label" => "환불-예금주", "rules" => $this->default_set_rules)
        ,   "account_bank"              => array("field" => "account_bank", "label" => "환불-은행", "rules" => $this->default_set_rules)
        ,   "account_no"                => array("field" => "account_no", "label" => "환불-계좌", "rules" => $this->default_set_rules)
        ,   "exchange_del_price"        => array("field" => "exchange_del_price", "label" => "비고", "rules" => "in_list[".get_config_item_keys_string("exchange_del_price")."]|".$this->default_set_rules)

        );

        $this->form_validation->set_rules($set_rules_array);
        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {

            $seq                    = $this->input->post('seq', true);
            $exchange_delivery      = $this->input->post('exchange_delivery', true);
            $exchange_delivery_no   = $this->input->post('exchange_delivery_no', true);
            $bigo                   = $this->input->post('bigo', true);
            $proc_flag              = $this->input->post('proc_flag', true);
            $after_status_cd        = $this->input->post('after_status_cd',true);

            $account_holder         = $this->input->post('account_holder',true);
            $account_bank           = $this->input->post('account_bank',true);
            $account_no             = $this->input->post('account_no',true);
            $exchange_del_price     = $this->input->post('exchange_del_price',true);

            if( empty($form_error_array) ) {

                $query_data = array();
                $query_data['exchange_delivery']    = $exchange_delivery;
                $query_data['exchange_delivery_no'] = $exchange_delivery_no;
                $query_data['bigo']                 = $bigo;
                $query_data['proc_flag']            = $proc_flag;

                $query_data['account_holder']       = $account_holder;
                $query_data['account_bank']         = $account_bank;
                $query_data['account_no']           = $account_no;
                $query_data['exchange_del_price']   = $exchange_del_price;

                if($proc_flag == 'Y') $query_data['proc_date'] = current_datetime();
                else if($proc_flag == 'N') $query_data['proc_date'] = "";

                if($after_status_cd == '66' && $proc_flag == 'Y') $query_data['after_status_cd'] = 166;
                else if($after_status_cd == '166' && $proc_flag == 'N') $query_data['after_status_cd'] = 66;

                if($after_status_cd == '67' && $proc_flag == 'Y') $query_data['after_status_cd'] = 167;
                else if($after_status_cd == '167' && $proc_flag == 'N') $query_data['after_status_cd'] = 67;

                if($after_status_cd == '68' && $proc_flag == 'Y') $query_data['after_status_cd'] = 168;
                else if($after_status_cd == '168' && $proc_flag == 'N') $query_data['after_status_cd'] = 68;

                if( $this->order_model->update_cancel_order($seq,$query_data) ) {
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

    }

    public function order_update_cancel_proc() {

        ajax_request_check();

        $this->load->library('form_validation');

        $addSetRule = '';
        if($this->input->post('after_status_cd') == 67 && $this->input->post('proc_flag') == 'Y'){
            $addSetRule = '|required';
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "seq"                       => array("field" => "seq", "label" => "seq", "rules" => "required|numeric|".$this->default_set_rules)
        ,   "exchange_delivery"         => array("field" => "exchange_delivery", "label" => "택배사", "rules" => "numeric|".$this->default_set_rules.$addSetRule)
        ,   "exchange_delivery_no"      => array("field" => "exchange_delivery_no", "label" => "송장번호", "rules" => "numeric|".$this->default_set_rules.$addSetRule)
        ,   "bigo"                      => array("field" => "bigo", "label" => "비고", "rules" => $this->default_set_rules)
        ,   "proc_flag"                 => array("field" => "proc_flag", "label" => "처리여부", "rules" => "in_list[Y,N]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);
        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {

            $seq                    = $this->input->post('seq', true);
            $exchange_delivery      = $this->input->post('exchange_delivery', true);
            $exchange_delivery_no   = $this->input->post('exchange_delivery_no', true);
            $bigo                   = $this->input->post('bigo', true);
            $proc_flag              = $this->input->post('proc_flag', true);
            $after_status_cd        = $this->input->post('after_status_cd',true);

            if( empty($form_error_array) ) {

                $query_data = array();
                $query_data['exchange_delivery'] = $exchange_delivery;
                $query_data['exchange_delivery_no'] = $exchange_delivery_no;
                $query_data['bigo'] = $bigo;
                $query_data['proc_flag'] = $proc_flag;
                if($proc_flag == 'Y') $query_data['proc_date'] = current_datetime();
                else if($proc_flag == 'N') $query_data['proc_date'] = "";

                if($after_status_cd == '66' && $proc_flag == 'Y') $query_data['after_status_cd'] = 166;
                else if($after_status_cd == '166' && $proc_flag == 'N') $query_data['after_status_cd'] = 66;

                if($after_status_cd == '67' && $proc_flag == 'Y') $query_data['after_status_cd'] = 167;
                else if($after_status_cd == '167' && $proc_flag == 'N') $query_data['after_status_cd'] = 67;

                if($after_status_cd == '68' && $proc_flag == 'Y') $query_data['after_status_cd'] = 168;
                else if($after_status_cd == '168' && $proc_flag == 'N') $query_data['after_status_cd'] = 68;

                if( $this->order_model->update_cancel_order($seq,$query_data) ) {
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

    }

}//end of class order