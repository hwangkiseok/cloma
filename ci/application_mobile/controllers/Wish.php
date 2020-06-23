<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY찜
 */
class Wish extends M_Controller
{

    public function __construct()
    {
        parent::__construct();

        member_login_check();

        //model
        $this->load->model('wish_model');

    }//end of __construct()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['sort_field']      = trim($this->input->post_get('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->post_get('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 5;
        }

        return $req;
    }//end of _list_req()

    public function index()
    {
        $this->wish_list();

    }//end of index()

    private function wish_list()
    {
        $req                    = $this->_list_req();
        $query_data             =  array();
        $query_data['where']    = $req;
        $list_count             = $this->wish_model->get_wish_list($query_data, "", "", true);

        $options = array('title' => '찜한상품' , 'top_type' => 'back');

        $this->_header($options);

        $wish_prod_list = $this->wish_model->get_wish_list($query_data);

        foreach( $wish_prod_list as $key => $row ) {
            $row['p_rep_image_array'] = json_decode($row['p_rep_image'], true);
            $row['p_display_info_array'] = json_decode($row['p_display_info'], true);
        }//end of foreach()

        if(count($wish_prod_list) < 1){
            $recommandProductExt = parent::get_recommand_product(6);
        }


        $this->load->view("/wish/wish_list", array(
            'req'           => $req,
            'list_count'    => $list_count,
            'wish_prod_list' => $wish_prod_list,
            'recommandProductExt' => $recommandProductExt
        ));

        $this->_footer($options);

    }//end of wish_list

    /**
     * 찜한상품 삭제
     */
    public function wish_delete_proc() {
        ajax_request_check();


        $this->load->model('product_model');

        //request
        $req['w_product_num'] = $this->input->post('p_num', true);

        if(is_array($req['w_product_num']) == true){

            foreach ($req['w_product_num'] as $v) {
                $this->wish_model->delete_wish($_SESSION['session_m_num'], $v);

                $product_row = $this->product_model->get_product_row(array('p_num' => $v));

                $query_data = array();
                $query_data['p_wish_count'] = (int)($product_row['p_wish_count']) - 1;
                $query_data['p_wish_count_user'] = (int)($product_row['p_wish_count_user']) - 1;

                $this->product_model->update_product($product_row['p_num'], $query_data);

            }
            result_echo_json(get_status_code('success'), "", true);

        }else{

            //삭제
            if( $this->wish_model->delete_wish($_SESSION['session_m_num'], $req['w_product_num']) ) {

                $product_row = $this->product_model->get_product_row(array('p_num' => $req['w_product_num']));

                $query_data = array();
                $query_data['p_wish_count'] = (int)($product_row['p_wish_count']) - 1;
                $query_data['p_wish_count_user'] = (int)($product_row['p_wish_count_user']) - 1;

                $this->product_model->update_product($product_row['p_num'], $query_data);


                result_echo_json(get_status_code('success'), "", true);
            }
            else {
                result_echo_json(get_status_code('error'), "", true);
            }

        }

    }//end of wish_delete_proc


    /**
     * 찜하기
     */
    public function wish_upsert_proc() {


        $this->load->model('product_model');

        $req['p_num'] = $this->input->post_get("p_num", true);

        //상품 정보
        $product_row = $this->product_model->get_product_row(array('p_num' => $req['p_num']));



        if( empty($product_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true);
        }

        $query_data = array();

        $query_data['w_member_num'] = $_SESSION['session_m_num'];
        $query_data['w_product_num'] = $product_row['p_num'];

        $result = $this->wish_model->insert_wish($query_data);

        if( $result['code'] == get_status_code('success') ) {
            $query_data = array();
            $query_data['p_wish_count'] = (int)($product_row['p_wish_count']) + 1;
            $query_data['p_wish_count_user'] = (int)($product_row['p_wish_count_user']) + 1;

            $this->product_model->update_product($product_row['p_num'], $query_data);
        }

        $wish_tot_cnt = $this->wish_model->get_wish_count($_SESSION['session_m_num']);

        if ($result['code'] == "000") {
            result_echo_json($result['code'], "", true, "", "", array('wish_tot_cnt' => $wish_tot_cnt['cnt']));
        } else {
            result_echo_json($result['code'], "이미 찜한 상품입니다.", true, "");
        }

    }//end of wish_upsert_proc

}//end of class Wish