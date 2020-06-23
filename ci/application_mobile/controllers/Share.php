<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 환불내역
 */
class Share extends M_Controller
{

    public function __construct()
    {
        parent::__construct();

        member_login_check();
        $this->load->model('share_model');

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

        $req                    = $this->_list_req();
        $query_data             =  array();
        $query_data['where']    = $req;
        $list_count             = $this->share_model->get_share_list($query_data, "", "", true);

        $share_prod_list = $this->share_model->get_share_list($query_data);

        foreach( $share_prod_list as $key => $row ) {
            $row['p_rep_image_array'] = json_decode($row['p_rep_image'], true);
            $row['p_display_info_array'] = json_decode($row['p_display_info'], true);
        }//end of foreach()


        if(count($share_prod_list) < 1){
            $recommandProductExt = parent::get_recommand_product(6);
        }

        $options = array('title' => '공유상품' , 'top_type' => 'back');

        $this->_header($options);

        $this->load->view('/share/index', array(
            'req'           => $req,
            'list_count'    => $list_count,
            'share_prod_list' => $share_prod_list,
            'recommandProductExt' => $recommandProductExt
        ) );

        $this->_footer();

    }//end of index()


    /**
     * 공유상품 삭제
     */
    public function share_delete_proc() {
        ajax_request_check();
        $this->load->model('product_model');
        //request
        $req['s_product_num'] = $this->input->post('p_num', true);

        if(is_array($req['s_product_num']) == true){

            foreach ($req['s_product_num'] as $v) {
                $this->share_model->delete_share($_SESSION['session_m_num'], $v);

                $product_row = $this->product_model->get_product_row(array('p_num' => $v));
                $query_data = array();
                $query_data['p_share_count'] = (int)($product_row['p_share_count']) - 1;
                $query_data['p_share_count_user'] = (int)($product_row['p_share_count_user']) - 1;

                $this->product_model->update_product($product_row['p_num'], $query_data);


            }
            result_echo_json(get_status_code('success'), "", true);

        }else{

            //삭제
            if( $this->share_model->delete_share($_SESSION['session_m_num'], $req['s_product_num']) ) {

                $product_row = $this->product_model->get_product_row(array('p_num' => $req['s_product_num']));

                $query_data = array();
                $query_data['p_share_count'] = (int)($product_row['p_share_count']) - 1;
                $query_data['p_share_count_user'] = (int)($product_row['p_share_count_user']) - 1;

                $this->product_model->update_product($product_row['p_num'], $query_data);

                result_echo_json(get_status_code('success'), "", true);
            }
            else {
                result_echo_json(get_status_code('error'), "", true);
            }

        }

    }//end of share_delete_proc

    /**
     * 공유하기
     */
    public function share_upsert_proc() {


        $this->load->model('product_model');

        $req['p_num'] = $this->input->post_get("p_num", true);

        //상품 정보
        $product_row = $this->product_model->get_product_row(array('p_num' => $req['p_num']));


        if( empty($product_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true);
        }

        $query_data = array();

        $query_data['s_member_num'] = $_SESSION['session_m_num'];
        $query_data['s_product_num'] = $product_row['p_num'];

        $result = $this->share_model->insert_share($query_data);

        if( $result['code'] == get_status_code('success') ) {
            $query_data = array();
            $query_data['p_share_count'] = (int)($product_row['p_share_count']) + 1;
            $query_data['p_share_count_user'] = (int)($product_row['p_share_count_user']) + 1;

            $this->product_model->update_product($product_row['p_num'], $query_data);
        }

        $share_tot_cnt = $this->share_model->get_share_count($_SESSION['session_m_num']);

        if ($result['code'] == "000") {
            result_echo_json($result['code'], "", true, "", "", array('share_tot_cnt' => $share_tot_cnt['cnt']));
        } else {
            result_echo_json($result['code'], "이미 공유한 상품입니다.", true, "");
        }

    }//end of share_upsert_proc

}//end of class Refund