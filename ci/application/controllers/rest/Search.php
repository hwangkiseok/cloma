<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\Rest_Core;

defined('BASEPATH') OR exit('No direct script access allowed');

//To Solve File REST_Controller not found
require APPPATH . 'libraries/RestServer/REST_Controller.php';
require APPPATH . 'libraries/RestServer/Format.php';
require APPPATH . 'libraries/RestServer/Rest_Core.php'; // W_Controller 클래스에서 사용된 메소드이관

/**
 * 검색 컨트롤러
 */
class Search extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('rest');
        $this->core = new Rest_Core(); // Core Class (MyController 코어클래스와 같은역할)

    }//end of __construct()

    private function clearProductField($arr, $add_f = array()){

        if(empty($arr[0]['p_num']) == true){ //단일배열

            if(empty($add_f) == false ) {
                foreach ($add_f as $kk => $vv) $arr[$kk] = $vv;
            }
            $arr['p_rep_image'] = json_decode($arr['p_rep_image'],true)[0];

            unset($arr['p_banner_image']);
            unset($arr['p_category']);
            unset($arr['p_order_link']);
            unset($arr['p_app_price_yn']);
            unset($arr['p_app_price']);
            unset($arr['p_price_second_yn']);
            unset($arr['p_price_second']);
            unset($arr['p_price_third_yn']);
            unset($arr['p_price_third']);
            unset($arr['p_hotdeal_condition_1']);
            unset($arr['p_hotdeal_condition_2']);
            unset($arr['total_margin']);
            unset($arr['total_margin_twoday']);
            unset($arr['p_display_info']);
            unset($arr['p_termlimit_yn']);
            unset($arr['p_termlimit_datetime1']);
            unset($arr['p_termlimit_datetime2']);
            unset($arr['p_view_count']);
            unset($arr['p_view_3day_count']);
            unset($arr['p_view_yesterday_count']);
            unset($arr['p_view_today_count']);
            unset($arr['p_click_count']);
            unset($arr['p_click_yesterday_count']);
            unset($arr['p_click_today_count']);
            unset($arr['p_click_count_week']);
            unset($arr['p_click_count_last_week']);
            unset($arr['p_comment_count']);
            unset($arr['p_review_count']);
            unset($arr['p_order_count']);
            unset($arr['p_order_count_3h']);
            unset($arr['p_order_count_twoday']);
            unset($arr['p_order_count_week']);
            unset($arr['p_order_count_month']);
            unset($arr['p_order_count_twomonth']);
            unset($arr['p_order_count_last_week']);
            unset($arr['p_regdatetime']);
            unset($arr['p_order']);
            unset($arr['p_display_state']);
            unset($arr['p_sale_state']);
            unset($arr['p_stock_state']);
            unset($arr['p_top_desc']);
            unset($arr['p_btm_desc']);
            unset($arr['p_search_cnt']);
            unset($arr['p_usd_price']);
            unset($arr['p_option_buy_cnt_view']);
            unset($arr['p_main_banner_view']);
            unset($arr['p_restock_cnt']);
            unset($arr['p_tot_order_count']);
            unset($arr['p_outside_display_able']);
            unset($arr['p_rep_image_add']);
            unset($arr['p_detail']);
            unset($arr['p_discount_rate']);
            unset($arr['p_wish_count']);
            unset($arr['p_share_count']);
            unset($arr['p_deliveryprice_type']);
            unset($arr['p_deliveryprice']);
            unset($arr['p_order_code']);
            unset($arr['p_hash']);
            unset($arr['p_date']);
            unset($arr['p_detail_image']);
            unset($arr['p_detail_add']);
            unset($arr['p_cate2']);
            unset($arr['p_cate3']);
            unset($arr['p_detail_image']);
            unset($arr['p_short_url']);
            unset($arr['p_app_link_url']);
            unset($arr['p_app_link_url_2']);
            unset($arr['p_origin']);
            unset($arr['p_manufacturer']);
            unset($arr['p_suvin_flag']);




        }else{ //순차배열

            foreach ($arr as $k => $r) {

                if(empty($add_f) == false ) {
                    foreach ($add_f as $kk => $vv) $arr[$k][$kk] = $vv;
                }
                $arr[$k]['p_rep_image'] = json_decode($r['p_rep_image'],true)[0];

                unset($arr[$k]['p_banner_image']);
                unset($arr[$k]['p_category']);
                unset($arr[$k]['p_order_link']);
                unset($arr[$k]['p_app_price_yn']);
                unset($arr[$k]['p_app_price']);
                unset($arr[$k]['p_price_second_yn']);
                unset($arr[$k]['p_price_second']);
                unset($arr[$k]['p_price_third_yn']);
                unset($arr[$k]['p_price_third']);
                unset($arr[$k]['p_hotdeal_condition_1']);
                unset($arr[$k]['p_hotdeal_condition_2']);
                unset($arr[$k]['total_margin']);
                unset($arr[$k]['total_margin_twoday']);
                unset($arr[$k]['p_display_info']);
                unset($arr[$k]['p_termlimit_yn']);
                unset($arr[$k]['p_termlimit_datetime1']);
                unset($arr[$k]['p_termlimit_datetime2']);
                unset($arr[$k]['p_view_count']);
                unset($arr[$k]['p_view_3day_count']);
                unset($arr[$k]['p_view_yesterday_count']);
                unset($arr[$k]['p_view_today_count']);
                unset($arr[$k]['p_click_count']);
                unset($arr[$k]['p_click_yesterday_count']);
                unset($arr[$k]['p_click_today_count']);
                unset($arr[$k]['p_click_count_week']);
                unset($arr[$k]['p_click_count_last_week']);
                unset($arr[$k]['p_comment_count']);
                unset($arr[$k]['p_review_count']);
                unset($arr[$k]['p_order_count']);
                unset($arr[$k]['p_order_count_3h']);
                unset($arr[$k]['p_order_count_twoday']);
                unset($arr[$k]['p_order_count_week']);
                unset($arr[$k]['p_order_count_month']);
                unset($arr[$k]['p_order_count_twomonth']);
                unset($arr[$k]['p_order_count_last_week']);
                unset($arr[$k]['p_regdatetime']);
                unset($arr[$k]['p_order']);
                unset($arr[$k]['p_display_state']);
                unset($arr[$k]['p_sale_state']);
                unset($arr[$k]['p_stock_state']);
                unset($arr[$k]['p_top_desc']);
                unset($arr[$k]['p_btm_desc']);
                unset($arr[$k]['p_search_cnt']);
                unset($arr[$k]['p_usd_price']);
                unset($arr[$k]['p_option_buy_cnt_view']);
                unset($arr[$k]['p_main_banner_view']);
                unset($arr[$k]['p_restock_cnt']);
                unset($arr[$k]['p_tot_order_count']);
                unset($arr[$k]['p_outside_display_able']);
                unset($arr[$k]['p_rep_image_add']);
                unset($arr[$k]['p_detail']);
                unset($arr[$k]['p_discount_rate']);
                unset($arr[$k]['p_wish_count']);
                unset($arr[$k]['p_share_count']);
                unset($arr[$k]['p_deliveryprice_type']);
                unset($arr[$k]['p_deliveryprice']);
                unset($arr[$k]['p_order_code']);
                unset($arr[$k]['p_hash']);
                unset($arr[$k]['p_date']);
                unset($arr[$k]['p_detail_image']);
                unset($arr[$k]['p_detail_add']);
                unset($arr[$k]['p_cate2']);
                unset($arr[$k]['p_cate3']);
                unset($arr[$k]['p_detail_image']);
                unset($arr[$k]['p_short_url']);
                unset($arr[$k]['p_app_link_url']);
                unset($arr[$k]['p_app_link_url_2']);
                unset($arr[$k]['p_origin']);
                unset($arr[$k]['p_manufacturer']);
                unset($arr[$k]['p_suvin_flag']);

            }

        }

        return $arr;

    }


    /**
     * 검색
     */
    public function index_get()
    {

        $req['kwd']             = $this->get('kwd');
        $req['kfd']             = 'p_name';
        $req['list_per_page']   = $this->get('list_per_page')?$this->get('list_per_page'):50;
        $req['page']            = $this->get('page')?$this->get('page'):1;

        $this->load->model('product_model');

        $query_data             =  array();
        $query_data['where']    = $req;
        $query_data['where']['sale_state']    = 'Y';
        $query_data['where']['stock_state']   = 'Y';

        //전체갯수
        $list_count = $this->product_model->get_product_list($query_data, "", "", true);

        //페이징
        $page_result = $this->core->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        $isEnd = false;
        if($req['list_per_page']*($req['page']-1) >= $list_count['cnt'] ){
            $isEnd = true;
        }

        $aProductList = $this->product_model->get_product_list($query_data , $page_result['start'], $page_result['limit']);
        $aProductList = self::clearProductField($aProductList , array('campaign' => 'search'));


        if(empty($aProductList) == true){

            $this->set_response(
                result_echo_rest_json(get_status_code("success"), "", true, "", "", ""
                ) , REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code;

        }else{

            $this->set_response(
                result_echo_rest_json(get_status_code("success"), "", true, "", "",
                    array(  'aProductList'  => $aProductList // 상품 리스트
                    ,   'tot_cnt'       => $list_count['cnt']
                    ,   "isEnd"         => $isEnd
                    ,   'req'           => $req
                    )
                ), REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code;

        }


    }//end of index()

}//end of class search