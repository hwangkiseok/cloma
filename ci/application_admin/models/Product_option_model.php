<?php
/**
 * 상품옵션 관련 모델
 */
class Product_option_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    public function upsert_option($arrayparams){

        $insert_query   = array();
        $date           = date('YmdHis');

        foreach ($arrayparams as $r) {

            if($r['act_type'] == 'update'){

                $sql = "UPDATE product_option_tb
                        SET
                          option_1              = '{$r['option_1']}' 
                        , option_2              = '{$r['option_2']}'
                        , option_3              = '{$r['option_3']}'
                        , option_img            = '{$r['option_img']}'
                        , option_use_img        = '{$r['option_use_img']}'
                        , option_sale_price     = '{$r['option_sale_price']}'
                        , option_supply_price   = '{$r['option_supply_price']}'   
                        , option_org_price      = '{$r['option_org_price']}'
                        , option_stock          = '{$r['option_stock']}'
                        , option_add            = '{$r['option_add']}'
                        , option_sort           = '{$r['option_sort']}'
                        , mod_date              = '{$date}'
                        WHERE option_id = '{$r['option_id']}'; 
                ";
                $this->db->query($sql);

            }else{
                $insert_query[] = "('{$r['p_num']}','{$r['option_1']}','{$r['option_2']}','{$r['option_3']}','{$r['option_img']}','{$r['option_use_img']}','{$r['option_sale_price']}','{$r['option_org_price']}','{$r['option_supply_price']}' ,'{$r['option_stock']}','{$r['option_add']}','{$r['option_sort']}','{$date}')";
            }
        }

        $insert_query_str = join(',',$insert_query);

        $sql = "INSERT INTO product_option_tb (p_num , option_1, option_2, option_3, option_img, option_use_img ,option_sale_price,option_supply_price,option_org_price,option_stock,option_add,option_sort,reg_date ) 
                VALUES {$insert_query_str};
        ";


        return $this->db->query($sql);

    } // end of insert_option


    public function get_option_list($p_num){

        $sql = "SELECT * FROM product_option_tb WHERE p_num = '{$p_num}' ORDER BY option_sort ASC;";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        return $aResult;

    }

    public function get_option_row($option_id){

        $sql = "SELECT * FROM product_option_tb WHERE option_id = '{$option_id}' ;";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        return $aResult;

    }


    public function del_option($arrayParams){

        $addQueryString = '';



        if(empty($arrayParams['option_id']) == false){
            $addQueryString = " AND option_id = '{$arrayParams['option_id']}' ";
        }


        if(empty($addQueryString) == true) return false;

        $sql = "DELETE FROM product_option_tb WHERE 1 {$addQueryString}; ";

        return $this->db->query($sql);

    }


}//end of class Product_option_model