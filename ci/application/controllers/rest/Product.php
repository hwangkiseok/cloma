<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\Rest_Core;

defined('BASEPATH') OR exit('No direct script access allowed');

//To Solve File REST_Controller not found
require APPPATH . 'libraries/RestServer/REST_Controller.php';
require APPPATH . 'libraries/RestServer/Format.php';
require APPPATH . 'libraries/RestServer/Rest_Core.php'; // W_Controller 클래스에서 사용된 메소드이관

/**
 * 상품 관련 컨트롤러
 */
class Product extends REST_Controller
{

    public $core;

    public function __construct()
    {
        parent::__construct();

        //model
        $this->load->model('product_model');
        $this->load->helper('rest');

        $this->core = new Rest_Core(); // Core Class (MyController 코어클래스와 같은역할)

    }

    private function clearRelField($arr, $link_type = ''){

        if(empty($arr[0]['p_num']) == true){ //단일배열

            if(empty($link_type) == false ) $arr['link_type'] = $link_type;
            $arr['p_rep_image'] = json_decode($arr['p_rep_image'],true)[0];
            $arr['p_detail_image'] = json_decode($arr['p_detail_image'],true);

            if(count($arr['p_detail_image']) > 0){
                foreach ($arr['p_detail_image'] as $k => $r) {
                    $arr['p_detail_image'][$k] = $r[0];
                }
            }

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
            unset($arr['seq']);
            unset($arr['p_pnum']);
            unset($arr['c_pnum']);
            unset($arr['view_cnt']);
            unset($arr['click_cnt']);
            unset($arr['order_cnt']);
            unset($arr['sort_num']);
            unset($arr['view_yn']);
            unset($arr['reg_date']);
            unset($arr['mod_date']);
            unset($arr['p_cate1']);
            unset($arr['p_cate2']);
            unset($arr['p_cate3']);
            unset($arr['p_summary']);
            unset($arr['p_detail']);
            unset($arr['p_detail_add']);
            unset($arr['p_detail_image']);
            unset($arr['p_order_code']);
            unset($arr['p_short_url']);
            unset($arr['p_app_link_url']);
            unset($arr['p_app_link_url_2']);
            unset($arr['p_supply_price']);
            unset($arr['p_original_price']);
            unset($arr['p_margin_price']);
            unset($arr['p_discount_rate']);
            unset($arr['p_margin_rate']);
            unset($arr['p_taxation']);
            unset($arr['p_origin']);
            unset($arr['p_manufacturer']);
            unset($arr['p_supplier']);
            unset($arr['p_deliveryprice_type']);
            unset($arr['p_deliveryprice']);
            unset($arr['p_wish_count']);
            unset($arr['p_wish_count_user']);
            unset($arr['p_wish_raise_yn']);
            unset($arr['p_wish_raise_count']);
            unset($arr['p_share_count']);
            unset($arr['p_share_count_user']);
            unset($arr['p_share_raise_yn']);
            unset($arr['p_share_raise_count']);
            unset($arr['p_hash']);
            unset($arr['p_suvin_flag']);
            unset($arr['p_easy_admin_code']);



        }else{ //순차배열

            foreach ($arr as $k => $r) {

                if(empty($link_type) == false ) $arr[$k]['link_type'] = $link_type;
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
                unset($arr[$k]['seq']);
                unset($arr[$k]['p_pnum']);
                unset($arr[$k]['c_pnum']);
                unset($arr[$k]['view_cnt']);
                unset($arr[$k]['click_cnt']);
                unset($arr[$k]['order_cnt']);
                unset($arr[$k]['sort_num']);
                unset($arr[$k]['view_yn']);
                unset($arr[$k]['reg_date']);
                unset($arr[$k]['mod_date']);
                unset($arr[$k]['p_cate1']);
                unset($arr[$k]['p_cate2']);
                unset($arr[$k]['p_cate3']);
                unset($arr[$k]['p_summary']);
                unset($arr[$k]['p_detail']);
                unset($arr[$k]['p_detail_add']);
                unset($arr[$k]['p_detail_image']);
                unset($arr[$k]['p_order_code']);
                unset($arr[$k]['p_short_url']);
                unset($arr[$k]['p_app_link_url']);
                unset($arr[$k]['p_app_link_url_2']);
                unset($arr[$k]['p_supply_price']);
                unset($arr[$k]['p_original_price']);
                unset($arr[$k]['p_margin_price']);
                unset($arr[$k]['p_discount_rate']);
                unset($arr[$k]['p_margin_rate']);
                unset($arr[$k]['p_taxation']);
                unset($arr[$k]['p_origin']);
                unset($arr[$k]['p_manufacturer']);
                unset($arr[$k]['p_supplier']);
                unset($arr[$k]['p_deliveryprice_type']);
                unset($arr[$k]['p_deliveryprice']);
                unset($arr[$k]['p_wish_count']);
                unset($arr[$k]['p_wish_count_user']);
                unset($arr[$k]['p_wish_raise_yn']);
                unset($arr[$k]['p_wish_raise_count']);
                unset($arr[$k]['p_share_count']);
                unset($arr[$k]['p_share_count_user']);
                unset($arr[$k]['p_share_raise_yn']);
                unset($arr[$k]['p_share_raise_count']);
                unset($arr[$k]['p_hash']);
                unset($arr[$k]['p_suvin_flag']);
                unset($arr[$k]['p_easy_admin_code']);
            }

        }

        return $arr;

    }

    private function clearProductField($arr, $link_type = ''){

        if(empty($arr[0]['p_num']) == true){ //단일배열

            if(empty($link_type) == false ) $arr['link_type'] = $link_type;
            $arr['p_rep_image'] = json_decode($arr['p_rep_image'],true)[0];
            $arr['p_detail_image'] = json_decode($arr['p_detail_image'],true);

            if(count($arr['p_detail_image']) > 0){
                foreach ($arr['p_detail_image'] as $k => $r) {
                    $arr['p_detail_image'][$k] = $r[0];
                }
            }

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


        }else{ //순차배열

            foreach ($arr as $k => $r) {

                if(empty($link_type) == false ) $arr[$k]['link_type'] = $link_type;
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
            }

        }

        return $arr;

    }

    /**
    * 삼품상세 정보
    */
    public function detail_get()
    {

        $p_num          = $this->get("p_num");
        $p_code         = $this->get("p_code");
        $view_check     = $this->get("view_check");
        $aInput         = array( 'p_num' => $p_num );
        $aProductInfo   = $this->product_model->get_product_row($aInput);

        if( empty($p_num) ) {

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), "해당상품이 존재하지 않습니다.[empty p_num]", true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code

        }else if( empty($aProductInfo) ) {

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), "해당상품이 존재하지 않습니다.[empty data]", true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code


        }else{

            $aSnsformProductInfo = $this->product_model->get_snsform_product_row($aProductInfo['p_order_code']);

            //상품상세페이지페이지 진입수 ++ (p_view_count) (새로고침 제외)
            if( $view_check == 'Y' ) {
                $query_data = array();
                $query_data['p_view_count'] = (int)($aProductInfo['p_view_count']) + 1;
                $query_data['p_view_today_count'] = (int)($aProductInfo['p_view_today_count']) + 1;
                $query_data['p_view_3day_count'] = (int)($aProductInfo['p_view_3day_count']) + 1;
                $query_data['p_click_count_week'] = (int)($aProductInfo['p_click_count_week']) + 1;

                $this->product_model->update_product($aProductInfo['p_num'], $query_data);
            }

            {//최근 본 상품에 저장

                $recently_view_product_array = array();
                $recently_view_product = $this->core->getRctly();

                if( !empty($recently_view_product) ) {
                    $recently_view_product_array = json_decode($recently_view_product, true);
                    $search_key = array_search($aProductInfo['p_num'], $recently_view_product_array);
                    if( $search_key !== false ) {
                        unset($recently_view_product_array[$search_key]);
                    }

                    foreach($recently_view_product_array as $key => $val ) {
                        if( $val == $aProductInfo['p_num']) {
                            unset($recently_view_product_array[$key]);
                        }
                    }
                }//end of if()

                array_unshift($recently_view_product_array, $aProductInfo['p_num']);

                if( count($recently_view_product_array) > $this->config->item('recently_view_product_max_count') ) {
                    for($i=$this->config->item('recently_view_product_max_count'); $i <= count($recently_view_product_array); $i++ ) {
                        array_pop($recently_view_product_array);
                    }
                }

                $this->core->setRctly(json_encode_no_slashes($recently_view_product_array));

            }

            $isWish = false;
            $isShare = false;

            if ($this->core->isLogin == 'Y') {//찜하기 & 공유 상품여부
                $this->load->model('wish_model');
                $isWish = $this->wish_model->get_wish_row($this->core->aMemberInfo['m_num'],$aProductInfo['p_num']) > 0 ? true : false ;

                $this->load->model('share_model');
                $isShare = $this->share_model->get_share_row($this->core->aMemberInfo['m_num'],$aProductInfo['p_num']) > 0 ? true : false ;
            }

            {//배송정보
                $this->load->model('common_model');
                $aDeliveryInfo = $this->common_model->get_common_code_row(1);
            }

            {//댓글전체수

                $query_array                    = array();
                $query_array['where']['tb']     = 'product';
                $query_array['where']['tb_num'] = $aProductInfo['p_num'];

                $this->load->model('comment_model');
                $nComment = $this->comment_model->get_comment_list($query_array , '' , '' ,true);

            }

            $aOrgRelationProduct    = $this->product_model->getRelationProductLists($aProductInfo['p_num']);
            $aProductInfo           = self::clearProductField($aProductInfo);

            if(empty($aOrgRelationProduct) == false){

                $aRelationProduct       = self::clearRelField($aOrgRelationProduct,'relation');
                $aRelation = array(
                    'title'     => '연관상품'
                ,   'aLists'    => $aRelationProduct
                );

            }else{
                $aRelation = new stdClass();
            }

            if(empty($aOrgRelationProduct) == false){

                $aWithProduct           = self::clearRelField($aOrgRelationProduct,'with');
                $aWith = array(
                    'title'     => '함께 구매한 상품'
                ,   'aLists'    => $aWithProduct
                );

            }else{
                $aWith = new stdClass();
            }

            $this->set_response(
                result_echo_rest_json(get_status_code("success"), "", true, "", "",
                    array(
                        "aProductInfo"          => $aProductInfo
                    ,   "aDeliveryInfo"         => $aDeliveryInfo
                    ,   "isWish"                => $isWish
                    ,   "isShare"               => $isShare
                    ,   "nComment"              => $nComment['cnt']
                    ,   "aOptionList"           => empty($aSnsformProductInfo['option_info']) == true ? array():json_decode($aSnsformProductInfo['option_info'],true)
                    ,   "sSnsformOptionType"    => $aSnsformProductInfo['option_type']
                    ,   "aRelation"             => $aRelation
                    ,   "aWith"                 => $aWith
                    )
                ), REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code;

        }

    }

}

