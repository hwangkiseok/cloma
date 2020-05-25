<?php
/**
 * 상품옵션 관련 모델
 */
class Product_option_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    public function insert_option($arrayparams){

        $insert_query = array();

        $date = date('YmdHis');

        foreach ($arrayparams as $r) {
            $insert_query[] = "('{$r['p_num']}','{$r['option_name1']}','{$r['option_name2']}','{$r['option_name3']}','{$r['img_url']}'
            ,'{$r['use_img']}','{$r['sale_price']}','{$r['org_price']}','{$r['supply_price']}' ,'{$r['stock']}','{$r['add']}','{$date}')";
        }

        $insert_query_str = join(',',$insert_query);

        $sql = "INSERT INTO product_option_tb (p_num , option_1, option_2, option_3, option_img, option_use_img
                ,option_sale_price,option_supply_price,option_org_price,option_stock,option_add,reg_date ) VALUES
                {$insert_query_str};
        ";

        zsView($sql);

    }

}//end of class Product_model