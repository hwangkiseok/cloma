<?php
class Dev extends A_Controller {

    function __construct() {
        parent::__construct();

        $allow_ip_array = array("182.221.169.37", "115.94.91.204");

        if( array_search($this->input->ip_address(), $allow_ip_array) === false ) {
            exit;
        }
    }//end of __construct()


    /**
     * 상품 카테고리 일괄 수정
     */
    function product_cate_update_batch() {
        exit;

        $query = "
            select *
            from product_tb
            where p_cate1 = ''
            order by p_num asc
        ";
        $list = $this->db->query($query)->result();

        $no = 0;
        foreach ($list as $key => $row) {
            if( empty($row->p_order_code) ) {
                continue;
            }

            $cate_info = json_decode(get_product_info($row->p_order_code, "cate1:cate2:cate3"), true);

            if( !empty($cate_info['cate1']) ) {
                $query = "
                    update product_tb
                    set
                        p_cate1 = '" . addslashes($cate_info['cate1']) . "'
                        , p_cate2 = '" . addslashes($cate_info['cate2']) . "'
                        , p_cate3 = '" . addslashes($cate_info['cate3']) . "'
                    where
                        p_num = '" . $row->p_num . "'
                ";
                $this->db->query($query);
            }

            $no++;

            echo $no . "<br />";
        }//endforeach;

        echo "완료";
    }//end of product_cate_update_batch()


    function category_info() {
        //$cate_list = json_decode(get_category_info(1), true);
        //var_dump($cate_list);

        //$cate_list = json_decode(get_category_info(2), true);
        //var_dump($cate_list);
        //
        //$cate_list = json_decode(get_category_info(3), true);
        //var_dump($cate_list);
        //
        $cate_list = json_decode(get_category_info(), true);
        var_dump($cate_list);


    }

}//end of class Dev