<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\Rest_Core;

defined('BASEPATH') OR exit('No direct script access allowed');

//To Solve File REST_Controller not found
require APPPATH . 'libraries/RestServer/REST_Controller.php';
require APPPATH . 'libraries/RestServer/Format.php';
require APPPATH . 'libraries/RestServer/Rest_Core.php'; // W_Controller 클래스에서 사용된 메소드이관

/**
 * 메인 컨트롤러
 */
class Cart extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('rest');
        $this->core = new Rest_Core(); // Core Class (MyController 코어클래스와 같은역할)

        $this->load->model('cart_model');
        $this->load->model('product_model');

    }//end of __construct()

    /**
     * 찜하기 toggle
     */
    public function upsert_put()
    {

        if(member_login_status() == false){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang("site_error_default"), "", true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code

        }else{

            $req['p_num']           = $this->put("p_num");
            $req['m_num']           = $_SESSION['session_m_num'];
            $req['sess_id']         = create_session_id();
            $req['option_info']     = $this->put("option_info");
            $req['set_referer']     = $this->put("set_referer");
            $req['set_campaign']    = $this->put("set_campaign");
            $option_info_arr        = json_decode($req['option_info'],true);

            $option_arr = array();
            foreach ($option_info_arr as $k => $r) {

                $option_arr[$k] = array(
                        'option_price'  => (int)$r['option_price']
                    ,   'option_count'  => (int)$r['option_count']
                    ,   'option_supply' => (int)$r['option_supply']
                    ,   'option_name'   => $r['option_name']
                    ,   'option_plus'   => $r['option_plus']
                    ,   'option_type'   => 'U'
                );

            }

            //상품 정보
            $product_row = $this->product_model->get_product_row(array('p_num' => $req['p_num']));

            $req['p_order_code']    = $product_row['p_order_code'];

            if ( empty($product_row) ) {

                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), lang('site_error_empty_data'), true, "", "", ""
                    ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code

            }else{

                /*---------------------------- 옵션별 재고체크 */
                $bRet2 = false; //재고수량 체크 value
                $bRet3 = false; //장바구니상품 유무 value
                foreach ($option_arr as $k => $r) {

                    $arrayParams = array(
                        'm_num'             => $_SESSION['session_m_num']
                    ,   'p_num'             => $req['p_num']
                    ,   'p_order_code'      => $req['p_order_code']
                    ,   'sess_id'           => create_session_id()
                    ,   'option_name'       => $r['option_name']
                    ,   'option_info'       => json_encode($r,JSON_UNESCAPED_UNICODE)
                    ,   'set_campaign'      => $req['set_campaign']
                    ,   'set_referer'       => $req['set_referer']
                    );

                    $aOverlapInfo = $this->cart_model->overlapCart($arrayParams);

                    if(empty($aOverlapInfo) ==true){ //insert

                        $bRet2 = self::chk_stock($arrayParams,$r);

                    }else{//update

                        $rev_data = json_decode($aOverlapInfo['option_info'],true);
                        $new_data = array(
                            'option_price'  => $r['option_price']
                        ,   'option_count'  => (int)$r['option_count'] + (int)$rev_data['option_count']
                        ,   'option_supply' => $r['option_supply']
                        ,   'option_name'   => $r['option_name']
                        ,   'option_plus'   => $r['option_plus']
                        ,   'option_type'   => $r['option_type']
                        );

                        $bRet2 = self::chk_stock($arrayParams,$new_data);

                        if($bRet2 == false) $bRet3 = true;

                    }
                    /*---------------------------- 옵션별 재고체크 */

                }

                if($bRet2 == true) {

                    $this->set_response(
                        result_echo_rest_json(get_status_code("error"), "주문하시려는 상품은 재고가 이미 소진된 상품으로 다시 확인 후 구매해 주시기 바랍니다.", "", true, "", "", ""
                        ), REST_Controller::HTTP_OK
                    ); // NOT_FOUND (404) being the HTTP response code

/*
                } else if($bRet3 == true){ // 처리방식 확인 / confirm 후 upsert 처리예정



*/

                }else{

                    foreach ($option_arr as $r) {

                        $arrayParams = array(
                            'm_num'             => $_SESSION['session_m_num']
                        ,   'p_num'             => $req['p_num']
                        ,   'p_order_code'      => $req['p_order_code']
                        ,   'sess_id'           => create_session_id()
                        ,   'option_name'       => $r['option_name']
                        ,   'option_info'       => json_encode($r,JSON_UNESCAPED_UNICODE)
                        ,   'set_campaign'      => $req['set_campaign']
                        ,   'set_referer'       => $req['set_referer']
                        );

                        $aOverlapInfo = $this->cart_model->overlapCart($arrayParams);
                        if(empty($aOverlapInfo) ==true){ //insert

                            $arrayParams['reg_date'] = current_datetime();
                            $ret = $this->cart_model->publicInsert('cart_tb',$arrayParams);

                        }else{//update

                            $arrayParams['mod_date']    = current_datetime();
                            $rev_data                   = json_decode($aOverlapInfo['option_info'],true);
                            $new_data                   = array(
                                'option_price'  => $r['option_price']
                            ,   'option_count'  => (int)$r['option_count'] + (int)$rev_data['option_count']
                            ,   'option_supply' => $r['option_supply']
                            ,   'option_name'   => $r['option_name']
                            ,   'option_plus'   => $r['option_plus']
                            ,   'option_type'   => $r['option_type']
                            );

                            $arrayParams['option_info'] = json_encode($new_data,JSON_UNESCAPED_UNICODE);

                            $ret = $this->cart_model->publicUpdate('cart_tb',$arrayParams, array('cart_id' , $aOverlapInfo['cart_id']));

                        }

                    }

                    if( $ret == true ) {
                        $this->set_response(
                            result_echo_rest_json(get_status_code("success"), "", true , '' , '' , '' ), REST_Controller::HTTP_OK
                        );
                    }
                    else {

                        $this->set_response(
                            result_echo_rest_json(get_status_code("error"), "장바구니 넣기에 실패하였습니다\n새로고침 후 다시 시도해주세요!", "", true, "", "", ""
                            ), REST_Controller::HTTP_OK
                        ); // NOT_FOUND (404) being the HTTP response code

                    }

                }

            }

        }

    }

    private function chk_stock($arrayParams,$data){

        $oSnsformProductInfo = $this->product_model->get_snsform_product_row($arrayParams['p_order_code']);
        $option_info        = json_decode($oSnsformProductInfo['option_info'] , true);

        $ret = false;

        foreach ($option_info as $kk => $rr) {

            $option_name = $rr['option_depth1'];
            if(empty($rr['option_depth2']) == false) $option_name .= ' | '.$rr['option_depth2'];
            if(empty($rr['option_depth3']) == false) $option_name .= ' | '.$rr['option_depth3'];

//            log_message('A','option_name :: '.$option_name .'///'. $data['option_name']);
//            log_message('A','option_count :: '.$data['option_count'] .'///'. $rr['option_count']);

            if($option_name == $data['option_name'] && $data['option_count'] > $rr['option_count']){
                $ret = true;
            }

        }

        return $ret;

    }

}//end of class Cart