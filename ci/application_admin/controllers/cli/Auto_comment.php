<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 자동댓글
 */
class auto_comment extends A_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {

        $curr_datetime = current_datetime();

        $sql = "    SELECT ACT.* 
                    FROM auto_cmt_tb ACT 
                    INNER JOIN product_tb PT ON ACT.p_num = PT.p_num
                    WHERE ACT.proc_flag = 'N'
                    AND DATE_FORMAT(DATE_ADD(PT.p_termlimit_datetime1 , INTERVAL + ACT.reg_min MINUTE) , '%Y-%m-%d %H:%i:%s') <= NOW() ;  
        ";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        $aInsertParams = array();
        foreach ($aResult as $r) {
            $aInsertParams[] = "('product','{$r['p_num']}','{$r['reg_name']}','{$r['auto_cmt_cont']}','{$curr_datetime}')";
        }

        $sInsertParams = implode(',',$aInsertParams);

        $sql = " INSERT INTO comment_tb (cmt_table , cmt_table_num, cmt_name , cmt_content , cmt_regdatetime) VALUES {$sInsertParams} ";
        $ret = $this->db->query($sql);

        if($ret == true){
            $sql = "UPDATE auto_cmt_tb ACT 
                    INNER JOIN product_tb PT ON ACT.p_num = PT.p_num
                    SET proc_flag = 'Y' , proc_date = '{$curr_datetime}'
                    WHERE ACT.proc_flag = 'N'
                    AND DATE_FORMAT(DATE_ADD(PT.p_termlimit_datetime1 , INTERVAL + ACT.reg_min MINUTE) , '%Y-%m-%d %H:%i:%s') <= NOW() ; ";
            $this->db->query($sql);
        }

    }//end of index()

}//end of class Product_sns_count