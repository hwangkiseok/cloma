<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends A_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {

        //상품 통계
        $query = "select p_category, p_display_state, p_sale_state, count(*) as cnt from product_tb group by p_category, p_display_state, p_sale_state";
        $product_stat_list = $this->db->query($query)->result();

        $product_count_array = array();
        $product_totalcount_array = array();
        foreach ( $product_stat_list as $key => $row ) {
            $product_count_array[$row->p_category][0] += $row->cnt;
            $product_count_array[$row->p_category][1][$row->p_display_state] += $row->cnt;
            $product_count_array[$row->p_category][2][$row->p_sale_state] += $row->cnt;

            $product_totalcount_array[0] += $row->cnt;
            $product_totalcount_array[1][$row->p_display_state] += $row->cnt;
            $product_totalcount_array[2][$row->p_sale_state] += $row->cnt;
        }//end of foreach()



        $this->load->model('common_model');
        $issue_cnt = $this->common_model->getNewIssueCount();



        $this->_header();

        $this->load->view("/main", array(
            "product_count_array"       => $product_count_array,
            "product_totalcount_array"  => $product_totalcount_array,
            "issue_cnt"                 => $issue_cnt
        ));

        $this->_footer();
    }//end of index()

}//end of class Main