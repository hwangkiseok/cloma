<?php
/**
 * 상품옵션 관련 모델
 */
class Product_option_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    public function upsert_option_group($arrayparams){

        $date           = date('YmdHis');

        foreach ($arrayparams as $r) {

            $act_type           = $r['act_type'];
            $option_group_id    = $r['option_group_id'];

            unset($r['act_type']);
            unset($r['option_group_id']);

            if($act_type == 'update'){

                $r['mod_date'] = $date;
                $ret = $this->db->update('product_option_group_tb' , $r , array('option_group_id' => $option_group_id));

            }else{

                $r['reg_date'] = $date;
                $ret = $this->db->insert('product_option_group_tb' , $r );
            }
        }


        return $ret;

    }


    public function upsert_option($arrayparams){

        $date           = date('YmdHis');

        foreach ($arrayparams as $r) {

            $act_type   = $r['act_type'];
            $option_id  = $r['option_id'];

            unset($r['act_type']);
            unset($r['option_id']);

            if($act_type == 'update'){

                $r['mod_date'] = $date;
                $ret = $this->db->update('product_option_tb' , $r , array('option_id' => $option_id));

            }else{

                $r['reg_date'] = $date;
                $ret = $this->db->insert('product_option_tb' , $r );

            }
        }

        return $ret;

    } // end of insert_option




    public function get_option_group_list($arrayParams){

        $addQueryString = '';
        $addOrderString = '';
        if(empty($arrayParams['p_num']) == false) $addQueryString .= " AND p_num = '{$arrayParams['p_num']}' ";
        if(empty($arrayParams['p_num']) == true && empty($arrayParams['option_token']) == false) $addQueryString .= " AND option_token = '{$arrayParams['option_token']}' ";
        if($arrayParams['type'] == 'basic') {
            $table          = 'product_option_tb';
            $addOrderString = ' ORDER BY option_sort ASC ';
        } else {
            $table = 'product_option_group_tb';
        }
        if(empty($addQueryString) == true) return false;

        $sql = "SELECT * FROM {$table} WHERE 1 {$addQueryString} {$addOrderString} ;";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        return $aResult;

    }


    public function get_option_list($arrayParams){

        $addQueryString = '';
        $addOrderString = '';
        if(empty($arrayParams['p_num']) == false) $addQueryString .= " AND p_num = '{$arrayParams['p_num']}' ";
        if(empty($arrayParams['p_num']) == true && empty($arrayParams['option_token']) == false) $addQueryString .= " AND option_token = '{$arrayParams['option_token']}' ";
        if($arrayParams['type'] == 'basic') {
            $table          = 'product_option_tb';
            $addOrderString = ' ORDER BY option_sort ASC ';
        } else {
            $table = 'product_option_group_tb';
        }
        if(empty($addQueryString) == true) return false;

        $sql = "SELECT * FROM {$table} WHERE 1 {$addQueryString} {$addOrderString} ;";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        return $aResult;

    } //end of get_option_list



    public function get_option_group_row($arrayParams){

        $addQueryString = '';
        if(empty($arrayParams['option_group_id']) == false) $addQueryString .= " AND option_group_id = '{$arrayParams['option_group_id']}' ";
        if(empty($addQueryString) == true) return false;

        $sql = "SELECT * FROM product_option_group_tb WHERE 1 {$addQueryString} ;";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        return $aResult;

    } //end of get_option_row

    public function get_option_row($arrayParams){

        $addQueryString = '';

        if(empty($arrayParams['option_id']) == false) $addQueryString .= " AND option_id = '{$arrayParams['option_id']}' ";
        if(empty($arrayParams['option_group_id1']) == false) $addQueryString .= " AND option_group_id1 = '{$arrayParams['option_group_id1']}' ";
        if(empty($arrayParams['option_group_id2']) == false) $addQueryString .= " AND option_group_id2 = '{$arrayParams['option_group_id2']}' ";
        if(empty($arrayParams['option_group_id3']) == false) $addQueryString .= " AND option_group_id3 = '{$arrayParams['option_group_id3']}' ";

        if(empty($addQueryString) == true) return false;

        $sql = "SELECT * FROM product_option_tb WHERE 1 {$addQueryString} ;";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        return $aResult;

    } //end of get_option_row



    public function del_option($arrayParams , $target){

        $addQueryString = '';

        if($target == 'single') {
            if(empty($arrayParams['option_id']) == false) $addQueryString .= " AND option_id = '{$arrayParams['option_id']}' ";
        } else {
            if(empty($arrayParams['option_group_id']) == false) {

                $sql = "DELETE FROM product_option_group_tb WHERE 1 AND option_group_id = '{$arrayParams['option_group_id']}' ";
                $this->db->query($sql);

                $addQueryString .= " AND ( option_group_id1 = '{$arrayParams['option_group_id']}' 
                                        OR option_group_id2 = '{$arrayParams['option_group_id']}' 
                                        OR option_group_id3 = '{$arrayParams['option_group_id']}' 
                )";
            }
        }

        if(empty($addQueryString) == true) return false;

        $sql = "DELETE FROM product_option_tb WHERE 1 {$addQueryString}; ";

        return $this->db->query($sql);

    }//end of del_option

    public function set_mapping_option($arrayParams){

        if(     empty($arrayParams['p_num']) == true
            ||  empty($arrayParams['option_token']) == true
            ||  empty($arrayParams['option_type']) == true
        ) return false;

        if($arrayParams['option_type'] != 'basic'){
            $sql = "UPDATE product_option_group_tb SET p_num = '{$arrayParams['p_num']}' WHERE option_token = '{$arrayParams['option_token']}' ; ";
            $ret = $this->db->query($sql);
        }

        if($ret == true){
            $sql = "UPDATE product_option_tb SET p_num = '{$arrayParams['p_num']}' WHERE option_token = '{$arrayParams['option_token']}' ; ";
            $ret = $this->db->query($sql);

            if($ret == false) log_message('A','set_mapping_option err step2 : '.$sql );
        }else{
            log_message('A','set_mapping_option err step1 : '.$sql );
        }

        return $ret;

    }


}//end of class Product_option_model