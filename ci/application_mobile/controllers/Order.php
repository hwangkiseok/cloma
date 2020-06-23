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

    public function order_cancel(){

        //TODO :: cancel type 확인하여 입장가능한 값인지 구분 / 함수로 처리하여 전역사용가능하도록...

        $aInput = array(
            'trade_no'      => $this->input->get('tn')
        ,   'cancel_type'   => $this->input->get('t') // form_status_cd
        );

        if($aInput['cancel_type'] == 66) $aInput['tit_str'] = '취소';
        else if($aInput['cancel_type'] == 67) $aInput['tit_str'] = '교환';
        else if($aInput['cancel_type'] == 68) $aInput['tit_str'] = '반품';

        $this->load->model('order_model');
        $aOrderInfo         = $this->order_model->get_order_info($aInput['trade_no']);
        $aSnsformOrderInfo  = getSnsformDeliveryInfo($aInput['trade_no']);
        $last_info          = self::chk_last_order($aInput['trade_no']);

        $options = array('title' => '주문 '.$aInput['tit_str'] , 'top_type' => 'back');
        $this->_header($options);

        $this->load->view('/order/cancel', array(
            'aOrderInfo'        => $aOrderInfo
        ,   'trade_no'          => $aInput['trade_no']
        ,   'cancel_type'       => $aInput['cancel_type']
        ,   'tit_str'           => $aInput['tit_str']
        ,   'aSnsformOrderInfo' => $aSnsformOrderInfo
        ,   'aLastOrderInfo'    => $last_info
        ) );

        $this->_footer();

    }

    private function able_req($to_cd , $from_cd){

        //60:주문대기 61:입금확인중 62:신규주문 63:배송준비중 64:배송중 65:배송완료 66:취소관리 67:교환관리 68:반품관리

        $b = false;
        if($to_cd <= 62 && $from_cd == 66) $b = true;
        else if($to_cd == 65 && ($from_cd == 67 || $from_cd == 68)) $b = true;

        return $b;

    }

    public function cancel_proc_v2(){

        member_login_check();

        $vacc_rules = '';
        $refund_rules = '';
        
        if( $this->input->post('payway_cd') == 3 && $this->input->post('status_cd') > 61 ){
            $vacc_rules = "|required";
        };

        if( $this->input->post('t') == '67' || $this->input->post('t') == '68' ){
            $refund_rules = '|required';
        } 
        
        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "trade_no" => array("field" => "trade_no", "label" => "주문번호", "rules" => "required|".$this->default_set_rules)
        ,   "cancel_gubun" => array("field" => "cancel_gubun", "label" => "취소사유", "rules" => "required|".$this->default_set_rules)
        ,   "cancel_reason" => array("field" => "cancel_reason", "label" => "상세사유", "rules" => "required|".$this->default_set_rules)
        ,   "account_holder" => array("field" => "account_holder", "label" => "환불 예금주", "rules" => $this->default_set_rules.$vacc_rules)
        ,   "account_bank" => array("field" => "account_bank", "label" => "환불 은행", "rules" => $this->default_set_rules.$vacc_rules)
        ,   "account_no" => array("field" => "account_no", "label" => "환불 계좌", "rules" => "numeric|".$this->default_set_rules.$vacc_rules)

        ,   "del_type" => array("field" => "del_type", "label" => "반송방법", "rules" => $this->default_set_rules.$refund_rules)
        ,   "refund_receiver_name" => array("field" => "refund_receiver_name", "label" => "반송자 이름", "rules" => $this->default_set_rules.$refund_rules)
        ,   "refund_receiver_tel" => array("field" => "refund_receiver_tel", "label" => "반송자 연락처", "rules" => $this->default_set_rules.$refund_rules)
        ,   "refund_receiver_zip" => array("field" => "refund_receiver_zip", "label" => "반송 우편번호", "rules" => $this->default_set_rules.$refund_rules)
        ,   "refund_receiver_addr1" => array("field" => "refund_receiver_addr1", "label" => "반송 주소", "rules" => $this->default_set_rules.$refund_rules)
        ,   "refund_receiver_addr2" => array("field" => "refund_receiver_addr2", "label" => "반송 주소 상세", "rules" => $this->default_set_rules.$refund_rules)


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

            $refund_receiver_name  = $this->input->post('refund_receiver_name', true);
            $refund_receiver_tel   = $this->input->post('refund_receiver_tel', true);
            $refund_receiver_zip   = $this->input->post('refund_receiver_zip', true);
            $refund_receiver_addr1 = $this->input->post('refund_receiver_addr1', true);
            $refund_receiver_addr2 = $this->input->post('refund_receiver_addr2', true);

            $del_type = $this->input->post('del_type', true);

            $after_status_cd   = $this->input->post('t', true);
            $payway_cd         = $this->input->post('payway_cd', true);
            $status_cd         = $this->input->post('status_cd', true);

            { //validation

                $bAbleCode = self::able_req($status_cd,$after_status_cd);
                if($bAbleCode == false) $form_error_array['able_code'] = '변경불가능 상태코드입니다.'; //변경할 수 없는 상태

                $aOrderInfo = $this->order_model->get_order_info($trade_no);
                if(empty($aOrderInfo) == true ) $form_error_array['empty_order'] = '주문정보 없습니다.';

                $isRefundView = false;
                if(in_array($aOrderInfo['payway_cd'] ,$this->config->item('refund_view_cd')) == true) $isRefundView = true; //무통장입금 / 가상계좌
                if( substr(number_only($aOrderInfo['register_date']) , 0 ,6) < date('Ym') && $aOrderInfo['payway_cd'] == 5 ) $isRefundView = true; //익월 휴대폰 결제

                if(($after_status_cd == 66 || $after_status_cd == 68) && $isRefundView == true && $aOrderInfo['status_cd'] > 61){
                    if(     empty($account_holder) == true
                        ||  empty($account_bank) == true
                        ||  empty($account_no) == true
                    ){
                        $form_error_array['empty_refund_acc'] = '환불정보 없습니다.';
                    }
                }

                if( ( $after_status_cd == 67 || $after_status_cd == 68 ) && $del_type == 'request'){

                    if(     empty($refund_receiver_name) == true
                        ||  empty($refund_receiver_tel) == true
                        ||  empty($refund_receiver_zip) == true
                        ||  empty($refund_receiver_addr1) == true
                        ||  empty($refund_receiver_addr2) == true
                    ){
                        $form_error_array['empty_receiver_data'] = '반품회수 주소정보가 없습니다.';
                    }

                }

            }

            if( empty($form_error_array) ) {

                $query_data = array();
                $query_data['trade_no']        = $trade_no;
                $query_data['cancel_gubun']    = $cancel_gubun;
                $query_data['cancel_reason']   = $cancel_reason;
                $query_data['account_holder']  = $account_holder;
                $query_data['account_bank']    = $account_bank;
                $query_data['account_no']      = $account_no;

                $query_data['del_type']         = $del_type;

                if(($after_status_cd == 66 || $after_status_cd == 68) && $isRefundView == true && $aOrderInfo['status_cd'] > 61){
                    $query_data['account_holder'] = $account_holder;
                    $query_data['account_bank'] = $account_bank;
                    $query_data['account_no'] = $account_no;
                }

                if( ( $after_status_cd == 67 || $after_status_cd == 68 ) && $del_type == 'request') {
                    $query_data['receiver_name'] = $refund_receiver_name;
                    $query_data['receiver_tel'] = $refund_receiver_tel;
                    $query_data['receiver_zip'] = $refund_receiver_zip;
                    $query_data['receiver_addr1'] = $refund_receiver_addr1;
                    $query_data['receiver_addr2'] = $refund_receiver_addr2;
                }

                $query_data['m_num']           = $_SESSION['session_m_num'];
                $query_data['after_status_cd'] = $after_status_cd;

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

                    $aOrderInfo = $this->order_model->get_order_info($trade_no);

                    if( substr($aOrderInfo['register_date'], 0, 7) == date('Y-m') && empty($aOrderInfo['m_trade_no']) == true ){

                        $aInput = array( 'tno' => $trade_no , 'act_type' => 'CE' );
                        $resp = getSnsformOrderCancel($aInput);

                        $this->chk_last_cart_order($trade_no,$aInput['act_type']);

                    }else{

                        $resp['sRtnCode'] = '001';

                    };

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

                    //json_encode($resp,JSON_UNESCAPED_UNICODE)
                    result_echo_json(get_status_code('error'), '신청실패[API]', true, 'alert' , array('data' => json_encode($resp,JSON_UNESCAPED_UNICODE)));

                }

            }

        }//end of if(/폼 검증 성공 마침)

        $msg_arr = array();
        foreach ($form_error_array as $k => $v) {
            $msg_arr[] = $v;
        }

        $msg_str = implode("\n",$msg_arr);

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), $msg_str, true, "alert", $form_error_array);

    }

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

                    $aOrderInfo = $this->order_model->get_order_info($trade_no);

//                    @TODO 휴대폰 결제취소 제한처리
//                    @TODO 당월, 단일주문만 취소가능

                    if( substr($aOrderInfo['register_date'], 0, 7) == date('Y-m') && empty($aOrderInfo['m_trade_no']) == true ){

                        $aInput = array( 'tno' => $trade_no , 'act_type' => 'CE' );
                        $resp = getSnsformOrderCancel($aInput);

                        $this->chk_last_cart_order($trade_no,$aInput['act_type']);

                    }else{

                        $resp['sRtnCode'] = '001';

                    };

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

                    //json_encode($resp,JSON_UNESCAPED_UNICODE)
                    result_echo_json(get_status_code('error'), '신청실패[API]', true, 'alert' , array('data' => json_encode($resp,JSON_UNESCAPED_UNICODE)));

                }

            }

        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);


        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);

    } // end of order_cancel_proc


    private function chk_last_order($trade_no){

        $sql = "SELECT * FROM snsform_order_tb WHERE trade_no = '{$trade_no}'; ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        $aResult2 = array();
        if( empty($aResult['m_trade_no']) == false && $aResult['cart_yn'] == 'Y' ) { //장바구니결제인경우

            $sql = "SELECT 
                    *
                    FROM snsform_order_tb 
                    WHERE m_trade_no = '{$aResult['m_trade_no']}' 
                    AND trade_no <> '{$trade_no}' 
                    AND status_cd < 66 ; 
            ";
            $oResult2 = $this->db->query($sql);
            $aResult2 = $oResult2->result_array();

        }

        $ret = array(
                'isLast' => count($aResult2) == 1 ? true : false
            ,   'data'   => $aResult2
        );

        return $ret; //장바구니 배송비 주문만 있는 경우

    }

    /**
     * @date 200310
     * @modify 황기석
     * @desc 장바구니 결제이고 취소한 주문이 장바구니배송비주문을 제외하고 마지막인 경우 장바구니배송비도 취소
     */
    private function chk_last_cart_order($trade_no,$act_type){

        $last_info = self::chk_last_order($trade_no);

        if($last_info['isLast'] == true && empty($last_info['data']) == false){
            $aResult2   = array_shift($last_info['data']);
            $aInput     = array( 'tno' => $aResult2['trade_no'] , 'act_type' => $act_type );
            $resp       = getSnsformOrderCancel($aInput);
        }

        /*
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
        */

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
            $nOrderInfo = count($aOrderInfo);
            $aSnsformOrderInfo = getSnsformDeliveryInfo($aInput['trade_no']);
            $buy_type = 'single';
        }else{//장바구니구매
            $aOrderInfo = $this->order_model->get_basket_info($aInput['m_trade_no']);
            $nOrderInfo = count($aOrderInfo);
            $aSnsformOrderInfo = getSnsformDeliveryInfo($aOrderInfo[0]['trade_no']);
            $buy_type = 'cart';
        }

        if($aOrderInfo[0]['complete_push_yn'] == 'N'){

            $nOrderInfo = (int)$nOrderInfo - 1 ;

            if(mb_strlen($aOrderInfo[0]['p_name']) > 5) $p_name = mb_substr($aOrderInfo[0]['p_name'], 0, 5, 'utf-8')."...";
            else $p_name = $aOrderInfo[0]['p_name'];

            $push_data  = array();
            if($buy_type == 'cart') $push_data['title'] = "[{$p_name}] 외 {$nOrderInfo}건의 결제가 완료되었습니다.";
            else $push_data['title'] = "[{$p_name}]의 결제가 완료되었습니다.";;
            $push_data['body']  = "최대한 빠르게 배송해 드릴게요!";
            $push_data['page']  = "delivery";

            $resp = send_app_push_log($aOrderInfo[0]['partner_buyer_id'], $push_data);

            if( $resp['success'] == true ){
                if($buy_type == 'cart') $sql = "UPDATE snsform_order_tb SET complete_push_yn = 'Y' WHERE m_trade_no = '{$aInput['m_trade_no']}'; ";
                else $sql = "UPDATE snsform_order_tb SET complete_push_yn = 'Y' WHERE trade_no = '{$aInput['trade_no']}'; ";
                $this->db->query($sql);
            }

        }

        $options = array('title' => '주문완료' , 'top_type' => 'back');
        $this->_header($options);

        $this->load->view('/order/complete', array(
                'aInput'            => $aInput
            ,   'aOrderInfo'        => $aOrderInfo
            ,   'aSnsformOrderInfo' => $aSnsformOrderInfo
            ,   'tno'               => $aInput['trade_no']?$aInput['trade_no']:$aInput['m_trade_no']
            ,   'buy_type'          => $buy_type
        ));

        $this->_footer();
    }

} // end of order