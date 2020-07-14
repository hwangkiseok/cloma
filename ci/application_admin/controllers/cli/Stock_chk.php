<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define("LIMIT_COUNT", 10);

/**
 * 재고 체크
 */
class Stock_chk extends A_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('product_model');
    }

    public function index() {

        $sql = "SELECT 
                    sct.* 
                ,	spt.option_info
                FROM `stock_chk_tb` sct
                INNER JOIN `snsform_product_tb` spt ON spt.item_no = sct.p_order_code
                WHERE sct.proc_yn = 'Y'
                AND sct.del_yn = 'N' ;
        ";

        $oResult    = $this->db->query($sql);
        $aList      = $oResult->result_array();

        foreach ($aList as $r) {

            $option_info = json_decode($r['option_info'],true);
            $res = false;

            foreach ($option_info as $rr) {
                if( $rr['option_count'] <= LIMIT_COUNT ) $res = true; 
            }
            
            if($res == true){

                $aInput = array(
                    'proc_yn'   => 'N'
                ,   'issue_yn'  => 'Y'
                ,   'proc_date' => date('YmdHis')
                ,   'mod_id'    => 9999999
                ,   'mod_date'  => date('YmdHis')
                );

                $this->product_model->publicUpdate('stock_chk_tb' , $aInput , array('p_num',$r['p_num']));

            }else{

                if($r['issue_yn'] == 'Y'){
                    $aInput = array(
                        'issue_yn'  => 'N'
                    ,   'mod_id'    => 9999999
                    ,   'mod_date'  => date('YmdHis')
                    );

                    $this->product_model->publicUpdate('stock_chk_tb' , $aInput , array('p_num',$r['p_num']));
                }
            }

        }

    }//end of index()

}//end of class Stock_chk