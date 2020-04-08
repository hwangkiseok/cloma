<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 장바구니
 */
class Cart extends M_Controller
{

    public function __construct()
    {
        parent::__construct();
        member_login_check();

        $this->load->model('cart_model');

    }//end of __construct()

    private function _list_req() {
        $req = array();
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
            $req['list_per_page'] = 5;
        }

        return $req;
    }//end of _list_req()

    private function clearCartData(){
        $this->cart_model->clearCartData();
    }

    public function index()
    {
        $this->load->model('order_model');

        self::clearCartData();

        $req                    = $this->_list_req();
        $query_data             =  array();
        $query_data['where']    = $req;
        $list_count             = $this->cart_model->get_cart_list($query_data, "", "", true);
//
//        //페이징
//        $page_result = $this->_paging(array(
//            "total_rows"    => $list_count['cnt'],
//            "per_page"      => $req['list_per_page'],
//            "page"          => $req['page'],
//            "ajax"          => true
//        ));
//
//        $cart_list              = $this->cart_model->get_cart_list($query_data, $page_result['start'], $page_result['limit']);
        $cart_list              = $this->cart_model->get_cart_list($query_data);
        $aLastOrder             = $this->order_model->get_last_order();

        $aInput = array(
                'shop_id'           => $this->config->item('form_api_id')
            ,   'a_session_id'      => create_session_id()
            ,   'a_code'            => $this->config->item('order_code')
            ,   'a_campaign'        => '' //api에서 처리
            ,   'a_referer'         => '' //api에서 처리
            ,   'partner_buyer_id'  => $_SESSION['session_m_num']
            ,   'toggle_header'     => 'N'
        );

        $options = array('title' => '장바구니' , 'top_type' => 'back');

        $this->_header($options);

        $this->load->view('/cart/index', array(
            'req'               => $req,
            'list_count'        => $list_count,
            'cart_prod_list'    => $cart_list,
            'aInput'            => $aInput,
            'target_url'        => $this->config->item('prefix_cart_url'),
            'target_url2'       => $this->config->item('prefix_order_url'),
            'aLastOrder'        => $aLastOrder
        ) );

        $this->_footer();

    }//end of index()

    public function cart_insert_proc(){

        ajax_request_check();

        $this->load->model('product_model');

        $aInput = array(
                'item_no'       => $this->input->post('item_no')
            ,   'option_info'   => $this->input->post('option_info')
            ,   'set_referer'   => $this->input->post('set_referer')
            ,   'set_campaign'  => $this->input->post('set_campaign')
            ,   'buy_count'     => $this->input->post('buy_count')
        );

        $aProductInfo   = $this->product_model->get_product_row(array('p_order_code' => $aInput['item_no']));
        $option_info    = json_decode($aInput['option_info'],true);

        foreach ($option_info as $r) {

            $arrayParams = array(
                'm_num'             => $_SESSION['session_m_num']
            ,   'p_num'             => $aProductInfo['p_num']
            ,   'p_order_code'      => $aProductInfo['p_order_code']
            ,   'sess_id'           => $_SESSION['my_session_id']
            ,   'option_name'       => $r['option_name']
            ,   'option_info'       => json_encode($r,JSON_UNESCAPED_UNICODE)
            ,   'set_campaign'      => $_SESSION['_set_referer']
            ,   'set_referer'       => $_SESSION['_set_campaign']
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
            result_echo_json(get_status_code('success'), '', true);
        }
        else {
            result_echo_json(get_status_code('error'), "장바구니 넣기에 실패하였습니다\n새로고침 후 다시 시도해주세요!", true);
        }

    }

    public function cart_delete_proc(){

        $cart_id = $this->input->post('cart_id');

        if(is_array($cart_id)){
            foreach ($cart_id as $v) {
                $this->cart_model->delete_cart($v);
            }
        }else{
            $this->cart_model->delete_cart($cart_id);
        }
        result_echo_json(get_status_code('success'), '', true);
    }


    public function cart_save_proc(){

        $this->load->model('product_model');

        $aCartLists = $this->input->post('data');

        foreach ($aCartLists as $k => $r) {

            $aCartInfo    = $this->cart_model->get_cart_row($r);
            $aProductInfo = $this->product_model->get_product_row(array('p_order_code' => $aCartInfo['p_order_code']));

            $option_info_arr    = json_decode($aCartInfo['option_info'],true);

            if($option_info_arr['option_count'] != $r['cart_count']){
                $option_info_arr['option_count'] = (int)$r['cart_count'];
                $option_info_json = json_encode($option_info_arr,JSON_UNESCAPED_UNICODE);

                if( self::chk_stock($aCartInfo ,$option_info_arr) == true ) {//재고량 오버
                    result_echo_json(get_status_code('error'), "{$aProductInfo['p_name']} {$option_info_arr['option_name']} 상품의 재고가 부족합니다 !", true);
                    exit;
                }else{
                    $this->cart_model->publicUpdate('cart_tb',array('option_info' => $option_info_json) , array('cart_id',$r['cart_id']));
                };

            }

        }

        result_echo_json(get_status_code('success'), '', true);

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
