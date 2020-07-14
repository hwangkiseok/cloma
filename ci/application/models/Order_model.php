<?php
/**
 * 주문정보 관련
 */
class Order_model extends W_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    public function getOrderInfo($arrayParams){

        $whereQueryString = '';

        if( empty($arrayParams['m_num']) == false ){
            $whereQueryString .= " AND partner_buyer_id = '{$arrayParams['m_num']}' ";
        }

        if( empty($arrayParams['p_order_code']) == false ){
            $whereQueryString .= " AND item_no = '{$arrayParams['p_order_code']}' ";
        }

        $sql = "SELECT * FROM snsform_order_tb WHERE 1 {$whereQueryString} ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        return $aResult;

    }

}//end of class Offer_model