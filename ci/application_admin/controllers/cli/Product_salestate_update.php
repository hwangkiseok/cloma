<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 상품 판매상태 업데이트 (cron)
 */
class Product_salestate_update extends A_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->model('product_model');

        //찜수 올리기
        $query_data = array();
        $query_data['where']['wish_yn'] = 'Y';
        $query_data['where']['display_state'] = 'Y';
        $wish_product_list = $this->product_model->get_product_list($query_data);

        foreach ( $wish_product_list as $key => $row ) {
            $query_data = array();
            $query_data['p_wish_count'] = $row->p_wish_count + $row->p_wish_raise_count;
            $this->product_model->update_product($row->p_num, $query_data);
        }//end of foreach()

        //공유수 올리기
        $query_data = array();
        $query_data['where']['share_yn'] = 'Y';
        $query_data['where']['display_state'] = 'Y';
        $share_product_list = $this->product_model->get_product_list($query_data);

        foreach ( $share_product_list as $key => $row ) {
            $query_data = array();
            $query_data['p_share_count'] = $row->p_share_count + $row->p_share_raise_count;
            $this->product_model->update_product($row->p_num, $query_data);
        }//end of foreach()
    }//end of index()

}//end of class Product_sns_count