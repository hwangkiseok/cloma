<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 주문배송 조회
 */
class Delivery extends M_Controller
{

    public function __construct()
    {
        parent::__construct();
        member_login_check();




    }//end of __construct()
    private function _list_req(){

        $req = array();
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));
        $req['date_type']       = trim($this->input->post_get('date_type', true));

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 10;
        }
        if( empty($req['date_type']) ) {
            $req['date_type'] = '1m';
        }

        return $req;

    }

    public function index_old()
    {

        $req = $this->_list_req();

        $options = array('title' => '주문조회' , 'top_type' => 'back');

        $this->_header($options);

        if($req['date_type'] == '6m'){
            $set_date_e = date("Ymd", strtotime('-31 days'));
            $set_date_s = date("Ymd", strtotime('-180 days'));
        }else if($req['date_type'] == '12m'){
            $set_date_e = date("Ymd", strtotime('-181 days'));
            $set_date_s = date("Ymd", strtotime('-365 days'));
        }else{
            $set_date_e = current_date();
            $set_date_s = date("Ymd", strtotime('-30 days'));
        }

        //20200329 이후 주문건만 검색
        if($set_date_e <= 20200329) $set_date_e = 20200330;
        if($set_date_s <= 20200329) $set_date_s = 20200329;

        $aInput = array(
                's_date'        => $set_date_s
            ,   'e_date'        => $set_date_e
            ,   'status_cd'     => ''
            ,   'list_start'    => ($req['page']-1)*$req['list_per_page']
            ,   'list_end'      => ($req['page']-1)*$req['list_per_page']+$req['list_per_page']
            ,   'buyer_id'      => $_SESSION['session_m_num']
        );

        $delivery_list = getSnsformDeliveryLists($aInput);

        $tno_arr = array();

        $ret = array();

        foreach ($delivery_list as $k => $r) {
            if( strpos($delivery_list[$k]['img_url'],'https') === false ) {
                $delivery_list[$k]['img_url'] = $this->config->item('snsform_prefix').$delivery_list[$k]['img_url'];
            }
            $tno_arr[] = $r['trade_no'];

            $sql        = "SELECT p_num FROM product_tb WHERE p_order_code = '{$r['item_no']}';";
            $oResult    = $this->db->query($sql);
            $delivery_list[$k]['p_num'] = $oResult->row_array()['p_num'];

        }

        $this->load->model('order_model');
        $aTmpOrderCancelLists = $this->order_model->get_order_cancel_list(array('where' => array('tno' => $tno_arr )));

        $aOrderCancelLists = array();
        foreach ($aTmpOrderCancelLists as $r) {
            $aOrderCancelLists[$r['trade_no']] = $r;
        }

        $this->load->view('/delivery/index', array('delivery_list' => $delivery_list , 'aOrderCancelLists' => $aOrderCancelLists ) );

        $this->_footer();

    }//end of index()

    public function index()
    {

        $req = $this->_list_req();

        $options = array('title' => '주문조회' , 'top_type' => 'back');

        $this->_header($options);

        if($req['date_type'] == '6m'){
            $set_date_e = date("Ymd", strtotime('-31 days'));
            $set_date_s = date("Ymd", strtotime('-180 days'));
        }else if($req['date_type'] == '12m'){
            $set_date_e = date("Ymd", strtotime('-181 days'));
            $set_date_s = date("Ymd", strtotime('-365 days'));
        }else{
            $set_date_e = current_date();
            $set_date_s = date("Ymd", strtotime('-30 days'));
        }

        //20200329 이후 주문건만 검색
        if($set_date_e <= 20200329) $set_date_e = 20200330;
        if($set_date_s <= 20200329) $set_date_s = 20200329;

        $aInput = array(
            's_date'        => $set_date_s
        ,   'e_date'        => $set_date_e
        ,   'status_cd'     => ''
        ,   'list_start'    => ($req['page']-1)*$req['list_per_page']
        ,   'list_end'      => ($req['page']-1)*$req['list_per_page']+$req['list_per_page']
        ,   'buyer_id'      => $_SESSION['session_m_num']
        );

        $delivery_list = getSnsformDeliveryLists($aInput);


        if(zsDebug()){
//            zsView($delivery_list);
        }

        $tno_arr = array();
        $ret = array();

        foreach ($delivery_list as $k => $r) {
            if( strpos($delivery_list[$k]['img_url'],'https') === false ) {
                $delivery_list[$k]['img_url'] = $this->config->item('snsform_prefix').$delivery_list[$k]['img_url'];
            }
            $tno_arr[] = $r['trade_no'];

            $sql = "SELECT 
                        A.m_trade_no
                    ,   B.p_num 
                    FROM snsform_order_tb A 
                    INNER JOIN product_tb B ON A.item_no = B.p_order_code
                    WHERE A.trade_no = '{$r['trade_no']}';
            ";
            $oResult = $this->db->query($sql);
            $aResult = $oResult->row_array();
            $delivery_list[$k]['p_num']      = $aResult['p_num'];
            $delivery_list[$k]['m_trade_no'] = $aResult['m_trade_no'];

            if(empty($aResult['m_trade_no']) == false){
                $ret[$aResult['m_trade_no']][] = $delivery_list[$k];
            }else{
                $ret[$r['trade_no']][] = $delivery_list[$k];
            }

        }

        $this->load->model('order_model');
        $aTmpOrderCancelLists = $this->order_model->get_order_cancel_list(array('where' => array('tno' => $tno_arr )));

        $aOrderCancelLists = array();
        foreach ($aTmpOrderCancelLists as $r) {
            $aOrderCancelLists[$r['trade_no']] = $r;
        }

        $view_file = '/delivery/index';

        $this->load->view($view_file, array('delivery_list' => $ret , 'aOrderCancelLists' => $aOrderCancelLists ) );

        $this->_footer();

    }

    public function delivery_list_ajax(){

        ajax_request_check();

        $req = $this->_list_req();

        if($req['date_type'] == '6m'){
            $set_date_e = date("Ymd", strtotime('-31 days'));
            $set_date_s = date("Ymd", strtotime('-180 days'));
        }else if($req['date_type'] == '12m'){
            $set_date_e = date("Ymd", strtotime('-181 days'));
            $set_date_s = date("Ymd", strtotime('-365 days'));
        }else{
            $set_date_e = current_date();
            $set_date_s = date("Ymd", strtotime('-30 days'));
        }

        $aInput = array(
            's_date'        => $set_date_s
        ,   'e_date'        => $set_date_e
        ,   'status_cd'     => ''
        ,   'list_start'    => ($req['page']-1)*$req['list_per_page']
        ,   'list_end'      => ($req['page']-1)*$req['list_per_page']+$req['list_per_page']
        ,   'buyer_id'      => $_SESSION['session_m_num']
        );

        $delivery_list = getSnsformDeliveryLists($aInput);



        $tno_arr = array();
        $ret = array();
        foreach ($delivery_list as $k => $r) {
            if( strpos($delivery_list[$k]['img_url'],'https') === false ) {
                $delivery_list[$k]['img_url'] = $this->config->item('snsform_prefix').$delivery_list[$k]['img_url'];
            }
            $tno_arr[] = $r['trade_no'];

//            $sql        = "SELECT p_num FROM product_tb WHERE p_order_code = '{$r['item_no']}';";
//            $oResult    = $this->db->query($sql);
//            $delivery_list[$k]['p_num'] = $oResult->row_array()['p_num'];

            $sql = "SELECT 
                        A.m_trade_no
                    ,   B.p_num 
                    FROM snsform_order_tb A 
                    INNER JOIN product_tb B ON A.item_no = B.p_order_code
                    WHERE A.trade_no = '{$r['trade_no']}';
            ";
            $oResult = $this->db->query($sql);
            $aResult = $oResult->row_array();
            $delivery_list[$k]['p_num']      = $aResult['p_num'];
            $delivery_list[$k]['m_trade_no'] = $aResult['m_trade_no'];

            if(empty($aResult['m_trade_no']) == false){
                $ret[$aResult['m_trade_no']][] = $delivery_list[$k];
            }else{
                $ret[$r['trade_no']][] = $delivery_list[$k];
            }

        }

        //zsView($delivery_list);
        $this->load->model('order_model');
        $aTmpOrderCancelLists = $this->order_model->get_order_cancel_list(array('where' => array('tno' => $tno_arr )));

        $aOrderCancelLists = array();
        foreach ($aTmpOrderCancelLists as $r) {
            $aOrderCancelLists[$r['trade_no']] = $r;
        }

        $this->load->view('/delivery/ajax_list', array('delivery_list' => $ret , 'aOrderCancelLists' => $aOrderCancelLists ) );

    }


    public function delivery_detail(){

        $tn = $this->input->get('tn');

        $aSnsformOrderInfo = getSnsformDeliveryInfo($tn);



        $this->load->model('order_model');
        $aOrderInfo = $this->order_model->get_order_info($tn);

        $options = array('title' => '주문상세' , 'top_type' => 'back');
        $this->_header($options);

        $this->load->view('/delivery/detail', array('aOrderInfo' => $aOrderInfo ,'aSnsformOrderInfo' => $aSnsformOrderInfo, 'tn' => $tn ) );

        $this->_footer();

    }


    public function delivery_outside_detail(){

        $aInput = array(
                'company'       => $this->input->get('company')
            ,   'invoice_no'    => $this->input->get('invoice_no')
        );

        if($aInput['company'] == 'CJ대한통운'){
            $aInput['prefix_url'] = 'http://nplus.doortodoor.co.kr/web/detail.jsp?slipno=';
        }

        $options = array('title' => '배송조회 상세' , 'top_type' => 'back');
        $this->_header($options);

        $this->load->view('/delivery/outside_detail', array( 'aInput' => $aInput , 'delivery_row' => $delivery_row ) );

        $this->_footer();

    }

    public function delivery_cancel(){

        $aInput = array(
            'trade_no'      => $this->input->get('tn')
        ,   'cancel_type'   => $this->input->get('t') // form_status_cd
        );

        $this->load->model('order_model');
        $aOrderInfo = $this->order_model->get_order_info($aInput['trade_no']);
        $aSnsformOrderInfo = getSnsformDeliveryInfo($aInput['trade_no']);

        $this->load->view('/delivery/cancel', array(
            'aOrderInfo'    => $aOrderInfo
        ,   'trade_no'      => $aInput['trade_no']
        ,   'cancel_type'   => $aInput['cancel_type']
        ,   'aSnsformOrderInfo' => $aSnsformOrderInfo
        ) );

    }

}//end of class Delivery
