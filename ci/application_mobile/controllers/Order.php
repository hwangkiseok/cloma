<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 주문 관련 컨트롤러
 */
class Order extends M_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');


    }
    public function index()
    {

        member_login_check();

        {//마지막 주문 가져오기
            $aLastOrder             = $this->order_model->get_last_order();
        }

        $aInput = array(
                'item_no'               => $this->input->post('item_no')
            ,   'option_info'           => $this->input->post('a_cart_yn') == 'Y' ? $this->input->post('a_basket_info') : $this->input->post('option_info')
            ,   'a_campaign'            => $this->input->post('set_referer')
            ,   'a_referer'             => $this->input->post('set_campaign')
            ,   'partner_buyer_id'      => $_SESSION['session_m_num']
            ,   'a_code'                => $this->config->item('order_code')
            ,   'toggle_header'         => 'N'
            ,   'cart_yn'               => $this->input->post('a_cart_yn')
        );

        $option_info_arr = json_decode($aInput['option_info'],true);
        $tot_cnt = 0;

        if($aInput['cart_yn'] == 'Y'){

            $tmp_option_info = array();

            if(empty($aInput['item_no']) == true) $aInput['item_no'] = $option_info_arr[0]['item_no'];

            $i = 0;
            foreach ($option_info_arr[0]['option_info'] as $r) {
                $tot_cnt += (int)$r['option_count'];

                $tmp_option_info[] = array(
                        'option_price' => (int)$r['option_price']
                    ,   'option_count' => (int)$r['option_count']
                    ,   'option_supply' => (int)$r['option_supply']
                    ,   'option_name' => $r['option_name']
                    ,   'option_plus' => 'N'
                    ,   'option_seller_supply' => 0
                );

            }

            $aInput['option_info'] = json_encode($tmp_option_info,JSON_UNESCAPED_UNICODE);

        }else{

            foreach ($option_info_arr as $r) {
                $tot_cnt += (int)$r['option_count'];
            }

        }

        $aInput['buy_count'] = $tot_cnt;

        $target_url = $this->config->item('prefix_order_url');
        $options = array('title' => '주문서' , 'top_type' => 'back');
        $this->_header($options);

        $this->load->view('/order/index', array(
            'aInput'       => $aInput
        ,   'target_url'   => $target_url
        ,   'aLastOrder'   => $aLastOrder
        ));

        $this->_footer();

    }//end of index()

    public function order_cancel_proc()
    {

        member_login_check();

        $vacc_rules = '';

        if( $this->input->post('payway_cd') == 3 && $this->input->post('status_cd') > 61 ){
            $vacc_rules = "|required";
        };

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
                "trade_no" => array("field" => "trade_no", "label" => "주문번호", "rules" => "required|".$this->default_set_rules)
            ,   "cancel_gubun" => array("field" => "cancel_gubun", "label" => "취소사유", "rules" => "required|".$this->default_set_rules)
            ,   "cancel_reason" => array("field" => "cancel_reason", "label" => "상세사유", "rules" => "required|".$this->default_set_rules)
            ,   "account_holder" => array("field" => "account_holder", "label" => "환불 예금주", "rules" => $this->default_set_rules.$vacc_rules)
            ,   "account_bank" => array("field" => "account_bank", "label" => "환불 은행", "rules" => $this->default_set_rules.$vacc_rules)
            ,   "account_no" => array("field" => "account_no", "label" => "환불 계좌", "rules" => "numeric|".$this->default_set_rules.$vacc_rules)
            ,   "t" => array("field" => "t", "label" => "환불타입", "rules" => "required|numeric|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $trade_no           = $this->input->post('trade_no', true);
            $cancel_gubun       = $this->input->post('cancel_gubun', true);
            $cancel_reason      = $this->input->post('cancel_reason', true);
            $account_holder     = $this->input->post('account_holder', true);
            $account_bank       = $this->input->post('account_bank', true);
            $account_no         = $this->input->post('account_no', true);
            $after_status_cd    = $this->input->post('t', true);

            $payway_cd         = $this->input->post('payway_cd', true);
            $status_cd         = $this->input->post('status_cd', true);


            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['trade_no']        = $trade_no;
                $query_data['cancel_gubun']    = $cancel_gubun;
                $query_data['cancel_reason']   = $cancel_reason;
                $query_data['account_holder']  = $account_holder;
                $query_data['account_bank']    = $account_bank;
                $query_data['account_no']      = $account_no;
                $query_data['m_num']           = $_SESSION['session_m_num'];
                $query_data['after_status_cd']      = $after_status_cd;

                if($status_cd == 61 & $payway_cd == 3) { //발주전 취소 처리 (실제 취소처리)

                    //무통장-입금전
                    $aInput = array( 'tno' => $trade_no , 'act_type' => 'CB' );
                    $resp = getSnsformOrderCancel($aInput);

                    $this->chk_last_cart_order($trade_no,$aInput['act_type']);

                }else if(in_array($payway_cd, array(1,2)) == true && $status_cd < 63){ //발주전 취소 처리 (실제 취소처리)

                    //카드, 계좌이체
                    $aInput = array( 'tno' => $trade_no , 'act_type' => 'CE' );
                    $resp = getSnsformOrderCancel($aInput);

                    $this->chk_last_cart_order($trade_no,$aInput['act_type']);

                }else if(in_array($payway_cd, array(5)) == true && $status_cd < 63){ //발주전 취소 처리 (실제 취소처리)
//                    @TODO 휴대폰 결제취소 제한처리
//                    @TODO 당월, 단일주문만 취소가능

                    //휴대폰
                    $aInput = array( 'tno' => $trade_no , 'act_type' => 'CE' );
                    $resp = getSnsformOrderCancel($aInput);

                    $this->chk_last_cart_order($trade_no,$aInput['act_type']);

                }else{ //발주후 취소 요청
                    $resp['sRtnCode'] = '001';
                }

                if($resp['sRtnCode'] == '001'){

                    if( $this->order_model->upsert_cancel_order($query_data) ) {
                        result_echo_json(get_status_code('success'), '신청완료', true, 'alert');
                    }
                    else {
                        result_echo_json(get_status_code('error'), '신청실패[DB]', true, 'alert');
                    }

                }else{

                    result_echo_json(get_status_code('error'), '신청실패[API]'.json_encode($resp,JSON_UNESCAPED_UNICODE), true, 'alert' , array('data' => json_encode($resp,JSON_UNESCAPED_UNICODE)));

                }

            }

        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);


        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);

    } // end of order_cancel_proc


    /**
     * @date 200310
     * @modify 황기석
     * @desc 장바구니 결제이고 취소한 주문이 장바구니배송비주문을 제외하고 마지막인 경우 장바구니배송비도 취소
     */
    private function chk_last_cart_order($trade_no,$act_type){


        $sql = "SELECT * FROM snsform_order_tb WHERE trade_no = '{$trade_no}'; ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        if( empty($aResult['m_trade_no']) == false && $aResult['cart_yn'] == 'Y' ){ //장바구니결제인경우

            $sql = "SELECT 
                    *
                    FROM snsform_order_tb 
                    WHERE m_trade_no = '{$aResult['m_trade_no']}' 
                    AND trade_no <> '{$trade_no}' 
                    AND status_cd < 66 ; 
            ";
            $oResult2 = $this->db->query($sql);
            $aResult2 = $oResult2->result_array();

            if( count($aResult2) == 1 ){ //장바구니 배송비 주문만 있는 경우
                $aResult2   = array_shift($aResult2);
                $aInput     = array( 'tno' => $aResult2['trade_no'] , 'act_type' => $act_type );
                $resp       = getSnsformOrderCancel($aInput);
            }

        }

    }

    public function cart()
    {

        member_login_check();

        {//마지막 주문 가져오기
            $aLastOrder             = $this->order_model->get_last_order();
        }

        $aInput = array(
            'shop_id'               => $this->input->post('shop_id')
        //,   'a_session_id'          => create_session_id() //$this->input->post('a_session_id')
        ,   'a_buyer_name'          => $this->input->post('a_buyer_name')
        ,   'a_buyer_hhp'           => $this->input->post('a_buyer_hhp')
        ,   'a_receiver_name'       => $this->input->post('a_receiver_name')
        ,   'a_receiver_hhp'        => $this->input->post('a_receiver_hhp')
        ,   'a_receiver_zip'        => $this->input->post('a_receiver_zip')
        ,   'a_receiver_addr1'      => $this->input->post('a_receiver_addr1')
        ,   'a_receiver_addr2'      => $this->input->post('a_receiver_addr2')

        ,   'option_info'           => $this->input->post('a_basket_info')
        ,   'a_campaign'            => $this->input->post('a_campaign')
        ,   'a_referer'             => $this->input->post('a_referer')
        ,   'partner_buyer_id'      => $_SESSION['session_m_num']
        ,   'a_code'                => $this->config->item('order_code')
        ,   'cart_yn'               => $this->input->post('a_cart_yn')
        ,   'a_payway_cd'           => $this->config->item('a_payway_cd')
        ,   'toggle_header'         => 'N'
        );

        $target_url = $this->config->item('prefix_cart_url');
        $options = array('title' => '주문서' , 'top_type' => 'back');
        $this->_header($options);

        $this->load->view('/order/cart', array(
            'aInput'       => $aInput
        ,   'target_url'   => $target_url
        ,   'aLastOrder'   => $aLastOrder
        ));

        $this->_footer();

    }
    
    public function order_complete(){

        $aInput = array(
                'trade_no'      => $this->input->get('trade_no')
            ,   'm_trade_no'    => $this->input->get('m_trade_no')
        );

        if(empty($aInput['trade_no']) == false){ //단품구매
            $aOrderInfo[] = $this->order_model->get_order_info($aInput['trade_no']);
            $aSnsformOrderInfo = getSnsformDeliveryInfo($aInput['trade_no']);

        }else{//장바구니구매
            $aOrderInfo = $this->order_model->get_basket_info($aInput['m_trade_no']);
            $aSnsformOrderInfo = getSnsformDeliveryInfo($aOrderInfo[0]['trade_no']);
        }

        $options = array('title' => '주문완료' , 'top_type' => 'back');
        $this->_header($options);

        $this->load->view('/order/complete', array(
                'aInput'            => $aInput
            ,   'aOrderInfo'        => $aOrderInfo
            ,   'aSnsformOrderInfo' => $aSnsformOrderInfo
            ,   'tno'               => $aInput['trade_no']?$aInput['trade_no']:$aInput['m_trade_no']
        ));

        $this->_footer();
    }
    
} // end of order