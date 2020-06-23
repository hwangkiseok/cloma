<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\Rest_Core;

defined('BASEPATH') OR exit('No direct script access allowed');

//To Solve File REST_Controller not found
require APPPATH . 'libraries/RestServer/REST_Controller.php';
require APPPATH . 'libraries/RestServer/Format.php';
require APPPATH . 'libraries/RestServer/Rest_Core.php'; // W_Controller 클래스에서 사용된 메소드이관

/**
 * 메인 컨트롤러
 */
class Main extends REST_Controller
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
            $arr['p_discount_rate'] = floor($arr['p_discount_rate']);

//            unset($arr['p_banner_image']);
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
//            unset($arr['p_discount_rate']);
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
                $arr[$k]['p_rep_image']     = json_decode($arr[$k]['p_rep_image'],true)[0];
                $arr[$k]['p_discount_rate'] = floor($arr[$k]['p_discount_rate']);
//                unset($arr[$k]['p_banner_image']);
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
//                unset($arr[$k]['p_discount_rate']);
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
    private function clearExhibitionField($arr, $link_type = ''){

        foreach ($arr as $k => $r) {

            if(empty($link_type) == false ) $arr[$k]['link_type'] = $link_type;
            //$arr[$k]['children'] = self::clearProductField($arr[$k]['children'] , array('campaign' => 'exhibition'));
            unset($arr[$k]['children']);

            unset($arr[$k]['tag']);
            unset($arr[$k]['sort_num']);
            unset($arr[$k]['activate_flag']);
            unset($arr[$k]['view_type']);
            unset($arr[$k]['header_title_img']);
            unset($arr[$k]['use_class']);
            unset($arr[$k]['fold_flag']);
            unset($arr[$k]['folded']);
            unset($arr[$k]['bg_color']);
            unset($arr[$k]['head_title']);
            unset($arr[$k]['reg_date']);
            unset($arr[$k]['mod_date']);
            unset($arr[$k]['start_date']);
            unset($arr[$k]['end_date']);

        }

        return $arr;

    }
    private function changeTypeProduct($arr , $link_type = ""){


        $ret_data = array();

        if(empty($arr[0]['p_num']) == true){ //단일배열

            $ret_data['thema_name']  = $arr['p_name'];
            $ret_data['link_type']  = $link_type;
            $ret_data['banner_img'] = json_decode($arr['p_rep_image'],true)[0];
            $ret_data['seq'] = $arr['p_num'];
            $ret_data['children'] = array();


        }else{ //순차배열

            foreach ($arr as $k => $r) {

                $ret_data[$k]['thema_name']     = $arr[$k]['p_name'];
                $ret_data[$k]['banner_img']     = json_decode($arr[$k]['p_rep_image'],true)[0];
                $ret_data[$k]['seq']            = $arr[$k]['p_num'];
                $ret_data[$k]['children']       = array();
                $ret_data[$k]['link_type']      = $link_type;
            }

        }

        return $ret_data;

    }

    /**
     * 메인
     */
    public function index_get()
    {

        $top15_type = $this->get('top15_type',true);

        $this->load->model('product_model');
        $this->load->model('exhibition_model');

        $aRollingBanner = $this->exhibition_model->get_exhibition_product_list();

        $notin = array();


        {// 세로롤링
            $aInput = array();
            $aInput['where']['md_div']      =  '2';
            $aInput['where']['sale_state']  =  'Y';
            $aInput['where']['stock_state'] =  'Y';
            $aInput['orderby']              = ' pmd_order ASC ';
            $aVerticalProductList = $this->product_model->get_product_list($aInput) ;

            shuffle($aVerticalProductList);


//            세로롤링 상품은 아래 영역에 중복으로 처리되도록 처리 ( 이경림과장 요청사항 - 200616 )
//            if(empty($aVerticalProductList) == false){
//                foreach ($aVerticalProductList as $r) {
//                    $notin[] = $r['p_num'];
//                }
//            }

        }

        $fix_cnt = 15;

        if($top15_type == 1 || $top15_type == ''){ // top30_top 상품은 최대 15개

            //강제 노출
            $aInput = array();
            $aInput['where']['md_div']      =  '1';
            $aInput['where']['sale_state']  =  'Y';
            $aInput['where']['stock_state'] =  'Y';
            $aInput['where']['not_pnum']    =  $notin;
            $aInput['orderby']              = ' pmd_order ASC ';
            $addProductList = $this->product_model->get_product_list($aInput , 0 , $fix_cnt) ;

            if(empty($addProductList) == false){
                foreach ($addProductList as $r) {
                    $notin[] = $r['p_num'];
                }
            }

            if(count($addProductList) >= 15){

                $aTopTheme = $addProductList;

            }else{
                $e_limit    = $fix_cnt - count($addProductList);
                $aInput     = array('not_pnum'  => $notin);

                $aTop10Lists = $this->product_model->get_main_product($aInput, 0 , $e_limit);
                $aTopTheme  = array_merge($addProductList,$aTop10Lists);
            }
            foreach ($aTopTheme as $r)  $notin[] = $r['p_num'];

        }else if($top15_type == 2){ // 총 주문수 / 진입 수

            $aInput = array();
            $aInput['where']['sale_state']  =  'Y';
            $aInput['where']['stock_state'] =  'Y';
            $aInput['orderby']              = ' p_order_count DESC , p_view_count DESC ';
            $aTopTheme = $this->product_model->get_product_list($aInput , 0 , $fix_cnt) ;

            foreach ($aTopTheme as $r)  $notin[] = $r['p_num'];

        }else if($top15_type == 3){ //최근 2주간 잘판린 상품

            $addProductList = get_recently_product('',true);
            if(empty($addProductList) == false){
                foreach ($addProductList as $r) {
                    $notin[] = $r['p_num'];
                }
            }

            $e_limit    = $fix_cnt - count($addProductList);

            $aTop10Lists  = array();

            if($e_limit > 0){

                $aInput = array();
                $aInput['where']['sale_state']  =  'Y';
                $aInput['where']['stock_state'] =  'Y';
                $aInput['where']['not_pnum']    =  $notin;
                $aInput['orderby']              = ' p_termlimit_datetime1 DESC ';
                $aTop10Lists_temp = $this->product_model->get_product_list($aInput , 0 , 50) ;

                shuffle($aTop10Lists_temp);

                for ($i = 0; $i <  $e_limit; $i++) {
                    $aTop10Lists[] = $aTop10Lists_temp[$i];
                }

            }

            $aTopTheme  = array_merge($addProductList,$aTop10Lists);

            foreach ($aTopTheme as $r)  $notin[] = $r['p_num'];

        }

        { // top30_bottom 상품은 최대 15개

            //정책에 의한 노출
            $aInput = array('not_pnum'  => $notin);
            $aTopTheme2 = $this->product_model->get_main_product($aInput,0,$fix_cnt);
            foreach ($aTopTheme2 as $r)  $notin[] = $r['p_num'];

            if( count($aTopTheme2) < $fix_cnt ) { //모자르는 경우 채워넣기

                $addCnt =  (int)$fix_cnt - count($aTopTheme2);

                $aInput = array(
                    'main_best' => 'Y'
                ,   'where'     => array('not_pnum'  => $notin)
                );

                $aAddTopTheme2 = $this->product_model->get_product_list($aInput, 0 , $addCnt);

                foreach ($aAddTopTheme2 as $r)  $notin[] = $r['p_num'];

                $aTopTheme2 = array_merge($aTopTheme2,$aAddTopTheme2);

            }

        }

        $aMainThemaResult   = $this->product_model->get_main_thema(array('not_in' => $notin));
        $notin              = $aMainThemaResult['not_in'];
        $aMainThemaList     = $aMainThemaResult['list'];

        { // 테마 - 카테고리 별 신상품 ( 카테고리별 20개 씩 )

            $this->load->model('category_md_model');
            $aInput = array();
            $aInput['where']['division'] = 4;
            $aInput['where']['state'] = 'Y';
            $aCategory = $this->category_md_model->get_category_md_list($aInput);

            $aTmpTheme3 = array();

            foreach ($aCategory as $r) {

                $aInput = array();
                $aInput['where']['sale_state']  =  'Y';
                $aInput['where']['stock_state'] =  'Y';
                $aInput['where']['ctgr']        =  $r['cmd_name'];
                $aInput['where']['not_pnum']    =  $notin;
                $aInput['orderby']              = ' p_termlimit_datetime1 DESC ';
                $tmp_result = $this->product_model->get_product_list($aInput, 0 , 20 ) ;

                $aTmpTheme3 = array_merge($aTmpTheme3,$tmp_result);

            }

        }
        
        $aRollingBanner = self::clearExhibitionField($aRollingBanner,'exhibition');

        if(empty($aTopTheme) == false) $aTopTheme = self::clearProductField($aTopTheme , array('campaign' => 'top30_t'));
        else $aTopTheme = array();
        if(empty($aTopTheme2) == false) $aTopTheme2 = self::clearProductField($aTopTheme2 , array('campaign' => 'top30_b'));
        else $aTopTheme2 = array();
        if(empty($aTmpTheme3) == false) $aTmpTheme3 = self::clearProductField($aTmpTheme3 , array('campaign' => 'thema_new_ctgr'));
        else $aTmpTheme3 = array();

        if(empty($aVerticalProductList) == false) $aVerticalProductList = self::clearProductField($aVerticalProductList , array('campaign' => 'Vertical'));
        else $aVerticalProductList = array();

        $aTheme2 = array(
            'title' => ''
        ,   'view_type' => 'A'
        ,   'aLists' => $aTopTheme2
        );

        $aTheme3 = array(
            'title' => '카테고리별 신상품'
        ,   'view_type' => 'C'
        ,   'aLists' => $aTmpTheme3
        );

        $aVertical = array(
            'title' => '오늘만 이 가격'
        ,   'aLists' => $aVerticalProductList
        );

        $aTheme = array();

        if(count($aMainThemaList) > 1){

            foreach ($aMainThemaList as $k => $r) {

                if($k == 1) $aTheme[] = $aTheme2;

                $aRowList = self::clearProductField($r['main_thema_product_lists'], array('campaign' => 'main_thema'));

                $aTheme[] = array(
                    'title'     => $r['main_thema_row']['thema_name']
                ,   'view_type' => $r['main_thema_row']['display_type']
                ,   'aLists'    => $aRowList
                );

            }

        }else {

            if(count($aMainThemaList) == 1){
                foreach ($aMainThemaList as $k => $r) {

                    $aRowList = self::clearProductField($r['main_thema_product_lists'], array('campaign' => 'main_thema'));

                    $aTheme[] = array(
                        'title'     => $r['main_thema_row']['thema_name']
                    ,   'view_type' => $r['main_thema_row']['display_type']
                    ,   'aLists'    => $aRowList
                    );

                }
            }

            $aTheme[] = $aTheme2;

        }

        $aTheme[] = $aTheme3;

        $this->set_response(
            result_echo_rest_json(get_status_code("success"), "", true, "", "",

                array(  'aRollingBanner'=> $aRollingBanner // 상단 롤링배너
                ,   'aTopTheme'     => array ( array( 'title' => '' , 'aLists' => $aTopTheme) )        //상단 상품 리스트
                ,   'aTheme'        => $aTheme //테마 리스트
                ,   'aVertical'     => $aVertical //세로롤링배너
                )

            ), REST_Controller::HTTP_OK
        ); // OK (200) being the HTTP response code;


    }

    /**
     * 메인
     * @desc 200423 변경

    public function index_bak_get()
    {

        $top15_type = $this->get('top15_type',true);

        $this->load->model('product_model');
        $this->load->model('exhibition_model');

        $aRollingBanner = $this->exhibition_model->get_exhibition_product_list();

        $notin = array();

        $fix_cnt = 15;

        if($top15_type == 1 || $top15_type == ''){ // top30_top 상품은 최대 15개

            //강제 노출
            $aInput = array();
            $aInput['where']['md_div']      =  '1';
            $aInput['where']['sale_state']  =  'Y';
            $aInput['where']['stock_state'] =  'Y';
            $aInput['orderby']              = ' pmd_order ASC ';
            $addProductList = $this->product_model->get_product_list($aInput) ;

            if(empty($addProductList) == false){
                foreach ($addProductList as $r) {
                    $notin[] = $r['p_num'];
                }
            }

            $e_limit    = $fix_cnt - count($addProductList);
            $aInput     = array('not_pnum'  => $notin);

            $aTop10Lists = $this->product_model->get_main_product($aInput, 0 , $e_limit);
            $aTopTheme  = array_merge($addProductList,$aTop10Lists);

            foreach ($aTopTheme as $r)  $notin[] = $r['p_num'];

        }else if($top15_type == 2){ //최근 본상품 + 마진높은 상품

            $addProductList = get_recently_product('',true);
            if(empty($addProductList) == false){
                foreach ($addProductList as $r) {
                    $notin[] = $r['p_num'];
                }
            }

            $e_limit    = $fix_cnt - count($addProductList);

            $aTop10Lists  = array();

            if($e_limit > 0){

                $aInput = array();
                $aInput['where']['sale_state']  =  'Y';
                $aInput['where']['stock_state'] =  'Y';
                $aInput['where']['not_pnum']    =  $notin;
                $aInput['orderby']              = ' p_margin_price DESC ';
                $aTop10Lists = $this->product_model->get_product_list($aInput , 0 , $e_limit) ;

            }

            $aTopTheme  = array_merge($addProductList,$aTop10Lists);

            foreach ($aTopTheme as $r)  $notin[] = $r['p_num'];

        }else if($top15_type == 3){ //최근 2주간 잘판린 상품

            $aInput = array();
            $aInput['where']['sale_state']  =  'Y';
            $aInput['where']['stock_state'] =  'Y';
            $aInput['orderby']              = ' p_order_count_week+p_order_count_last_week DESC ';
            $aTopTheme = $this->product_model->get_product_list($aInput , 0 , $fix_cnt) ;

            foreach ($aTopTheme as $r)  $notin[] = $r['p_num'];

        }

        { // top30_bottom 상품은 최대 15개

            //정책에 의한 노출
            $aInput = array('not_pnum'  => $notin);
            $aTopTheme2 = $this->product_model->get_main_product($aInput,0,$fix_cnt);
            foreach ($aTopTheme2 as $r)  $notin[] = $r['p_num'];

            if( count($aTopTheme2) < $fix_cnt ) { //모자르는 경우 채워넣기

                $addCnt =  (int)$fix_cnt - count($aTopTheme2);

                $aInput = array(
                    'main_best' => 'Y'
                ,   'where'     => array('not_pnum'  => $notin)
                );

                $aAddTopTheme2 = $this->product_model->get_product_list($aInput, 0 , $addCnt);

                foreach ($aAddTopTheme2 as $r)  $notin[] = $r['p_num'];

                $aTopTheme2 = array_merge($aTopTheme2,$aAddTopTheme2);

            }

        }

        { // 테마 - 편하고 이쁜 밴딩팬츠 맛집~

            //강제 노출
            $aInput = array();
            $aInput['where']['md_div']      =  '3';
            $aInput['where']['sale_state']  =  'Y';
            $aInput['where']['stock_state'] =  'Y';
            $aInput['where']['not_pnum']    =  $notin;
            $aInput['orderby']              = ' pmd_order ASC ';
            $aTmpTheme2_1 = $this->product_model->get_product_list($aInput, 0 , 8) ;

            foreach ($aTmpTheme2_1 as $r) {
                $notin[] = $r['p_num'];
            }

        }

        { // 테마 - 코디걱정 없는 상하의세트!

            //강제 노출
            $aInput = array();
            $aInput['where']['md_div']      =  '4';
            $aInput['where']['sale_state']  =  'Y';
            $aInput['where']['stock_state'] =  'Y';
            $aInput['where']['not_pnum']    =  $notin;
            $aInput['orderby']              = ' pmd_order ASC ';
            $aTmpTheme2_2 = $this->product_model->get_product_list($aInput, 0 , 8) ;

            foreach ($aTmpTheme2_2 as $r) {
                $notin[] = $r['p_num'];
            }

        }



        { // 테마 - 카테고리 별 신상품 ( 카테고리별 20개 씩 )

            $this->load->model('category_md_model');
            $aInput = array();
            $aInput['where']['division'] = 4;
            $aInput['where']['state'] = 'Y';
            $aCategory = $this->category_md_model->get_category_md_list($aInput);

            $aTmpTheme3 = array();

            foreach ($aCategory as $r) {

                $aInput = array();
                $aInput['where']['sale_state']  =  'Y';
                $aInput['where']['stock_state'] =  'Y';
                $aInput['where']['ctgr']        =  $r['cmd_name'];
                $aInput['where']['not_pnum']    =  $notin;
                $aInput['orderby']              = ' p_termlimit_datetime1 DESC ';
                $tmp_result = $this->product_model->get_product_list($aInput, 0 , 20 ) ;

                $aTmpTheme3 = array_merge($tmp_result,$aTmpTheme3);

            }

        }

        {// adid / fcmid 저장

            $adid   = $this->get('adid');
            $fcm_id = $this->get('fcm_id');

            if(( empty($adid) == false || empty($fcm_id) == false ) && empty($_SESSION['session_m_num']) == false ) {

                $this->load->model('member_model');

                if(empty($adid) == false) $aInput['m_adid'] = $adid;
                if(empty($fcm_id) == false) $aInput['m_reg'] = $fcm_id;

                $this->member_model->update_member($_SESSION['session_m_num'] , $aInput);

            }

        }

        $aRollingBanner = self::clearExhibitionField($aRollingBanner,'exhibition');

        if(empty($aTopTheme) == false) $aTopTheme = self::clearProductField($aTopTheme , array('campaign' => 'top30_t'));
        else $aTopTheme = array();
        if(empty($aTopTheme2) == false) $aTopTheme2 = self::clearProductField($aTopTheme2 , array('campaign' => 'top30_b'));
        else $aTopTheme2 = array();
        if(empty($aTmpTheme3) == false) $aTmpTheme3 = self::clearProductField($aTmpTheme3 , array('campaign' => 'thema_new_ctgr'));
        else $aTmpTheme3 = array();

        if(empty($aTmpTheme2_1) == false) $aTmpTheme2_1 = self::clearProductField($aTmpTheme2_1 , array('campaign' => ''));
        else $aTmpTheme2_1 = array();
        if(empty($aTmpTheme2_2) == false) $aTmpTheme2_2 = self::clearProductField($aTmpTheme2_2 , array('campaign' => ''));
        else $aTmpTheme2_2 = array();

        $aTheme2_1 = array(
            'title' => '편하고 이쁜 밴딩팬츠 맛집~'
        ,   'view_type' => 'B'
        ,   'aLists' => $aTmpTheme2_1
        );

        $aTheme2_2 = array(
            'title' => '코디걱정 없는 상하의세트!'
        ,   'view_type' => 'B'
        ,   'aLists' => $aTmpTheme2_2
        );

        $aTheme2 = array(
            'title' => ''
        ,   'view_type' => 'A'
        ,   'aLists' => $aTopTheme2
        );

        $aTheme3 = array(
            'title' => '카테고리별 신상품'
        ,   'view_type' => 'C'
        ,   'aLists' => $aTmpTheme3
        );

        $this->set_response(
            result_echo_rest_json(get_status_code("success"), "", true, "", "",

            array(  'aRollingBanner'=> $aRollingBanner // 상단 롤링배너
                ,   'aTopTheme'     => array ( array( 'title' => '' , 'aLists' => $aTopTheme) )        //상단 상품 리스트
                ,   'aTheme'        => array ( $aTheme2_1, $aTheme2 , $aTheme2_2 ,  $aTheme3 ) //테마 리스트
            )

            ), REST_Controller::HTTP_OK
        ); // OK (200) being the HTTP response code;

    }//end of index()
     */

    public function best_get(){

        $req = array();
        $req['best_code']       = $this->get('best_code');
        $req['list_per_page']   = $this->get('list_per_page')?$this->get('list_per_page'):50;
        $req['page']            = $this->get('page')?$this->get('page'):1;

        if(empty($req['best_code']) == true){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), "필수입력 값 누락 [ best_code ]", true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code;

        }else{

            $chk_arr = array();

            $query_data             = array();
            $query_data['where']    = $req;


            $query_data['where']['sale_state']  = 'Y';
            $query_data['where']['stock_state'] = 'Y';

            $this->load->model('product_model');

            if($req['best_code'] == 'today' ) $query_data['orderby'] = ' p_order_count_twoday DESC , p_date DESC , p_num DESC ';
            else if($req['best_code'] == 'week' ) $query_data['orderby'] = ' p_order_count_week DESC , p_date DESC , p_num DESC ';
            else if($req['best_code'] == 'month' ) $query_data['orderby'] = ' p_order_count_month DESC , p_date DESC , p_num DESC ';

            //전체갯수
            //$list_count = $this->product_model->get_product_list($query_data, 0, 100, true);
            $list_count['cnt'] = 50;

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

            if($isEnd == false){
                $aProductList = $this->product_model->get_product_list( $query_data , $page_result['start'], $page_result['limit'] );
                $aProductList = self::clearProductField($aProductList, array('campaign' => 'best'));
            }

            if(empty($aProductList) == true){

                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), "상품정보가 없습니다.", true, "", "", "" ), REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;

            }else{
                $this->set_response(
                    result_echo_rest_json(get_status_code("success"), "", true, "", "",
                        array(  'aProductList'  => $aProductList // 상품 리스트
                            ,   'req'           => $req
                            ,   'tot_cnt'       => $list_count['cnt']
                        )
                    ), REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;
            }
        }
    }

    public function fashion_get(){

        $req                    = array();
        $req['ctgr_code']       = $this->get('ctgr_code');
        $req['sort_type']       = $this->get('sort_type');
        $req['list_per_page']   = $this->get('list_per_page')?$this->get('list_per_page'):50;
        $req['page']            = $this->get('page')?$this->get('page'):1;

        if(empty($req['ctgr_code']) == true){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), "필수입력 값 누락 [ ctgr_code ]", true, "", "", "" ), REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code;

        }else{

            $this->load->model('category_md_model');
            $aCategoryInfo = $this->category_md_model->get_category_md_row( array('cmd_num' => $req['ctgr_code']) );
            $req['cmd_name'] = $aCategoryInfo['cmd_product_cate'];

            if(empty($aCategoryInfo) == true){
                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), "필수입력 값 누락 [ ctgr_row ]", true, "", "", "" ), REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;
            }else{

                $this->load->model('product_model');

                $query_data             =  array();
                $query_data['where']    = $req;
                $query_data['where']['sale_state']  = 'Y';
                $query_data['where']['stock_state'] = 'Y';
                $query_data['where']['ctgr'] = $aCategoryInfo['cmd_product_cate'];

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

                if($isEnd == false){

                    if($req['sort_type'] == '' || $req['sort_type'] == 'new_desc'){
                        $query_data['orderby'] = ' p_termlimit_datetime1 DESC '; //신상품부터
                    }else if($req['sort_type'] == 'ingi_desc'){
                        $query_data['orderby'] = ' p_view_3day_count DESC , p_order_count DESC ';
                    }else if($req['sort_type'] == 'discount_desc'){
                        $query_data['orderby'] = ' p_discount_rate DESC ';
                    }else if($req['sort_type'] == 'lowprice_desc'){
                        $query_data['orderby'] = ' p_sale_price ASC ';
                    }else{
                        $query_data['orderby'] = ' p_termlimit_datetime1 DESC '; //신상품부터
                    }

                    $aProductList = $this->product_model->get_product_list( $query_data, $page_result['start'], $page_result['limit'] );
                    $aProductList = self::clearProductField($aProductList, array('campaign' => 'fashion'));

                }

                if(empty($aProductList) == true){
                    $this->set_response(
                        result_echo_rest_json(get_status_code("error"), "상품정보가 없습니다.", true, "", "", ""
                        ), REST_Controller::HTTP_OK
                    ); // OK (200) being the HTTP response code;
                }else{
                    $this->set_response(
                        result_echo_rest_json(get_status_code("success"), "", true, "", "",
                            array(  'aProductList'  => $aProductList // 상품 리스트
                                ,   'req'           => $req
                                ,   'tot_cnt'       => $list_count['cnt']
                            )
                        ), REST_Controller::HTTP_OK
                    ); // OK (200) being the HTTP response code;
                }
            }

        }

    }

}//end of class Main