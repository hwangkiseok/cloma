<?php
/**
 * SNS FORM API 관련 모델
 */
class Snsform_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /* 지난 주문인지 확인 */
    public function bOverlapOrder($trade_no){

        $sql = "SELECT * FROM snsform_order_tb WHERE trade_no = '{$trade_no}' ;";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        if(empty($aResult) == true){
            return false;
        } else {
            return true;
        }

    } //end of bOverlapOrder

    /* 지난 주문인지 확인 */
    public function bOverlapProduct($item_no){

        $sql = "SELECT * FROM snsform_product_tb WHERE item_no = '{$item_no}' ;";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        if(empty($aResult) == true){
            return false;
        } else {
            return true;
        }

    } //end of bOverlapOrder


}//end of class Api_model