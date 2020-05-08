<?php
namespace Restserver\Libraries;

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Class Rest_Core
 * @package Restserver\Libraries
 * W_Controller 역할 클래스
 */
class Rest_Core  {

    public $CI;
    public $doc_version;
    public $page_link;
    public $controller_dir;
    public $list_per_page = 20;
    public $isLogin = 'N';
    public $aMemberInfo = array();

    function __construct(){

        $this->CI = &get_instance();
        $this->CI->load->model('member_model');
        $this->doc_version = '1';

        if( !$_SESSION['my_session_id'] ) {
            $_SESSION['my_session_id'] = create_session_id();
        }

        $this->login();

    }

    private function login(){

        $headers = apache_request_headers();

        foreach ($headers as $header => $value) {

            if ($header == 'm_num') $m_num = $value;
            if ($header == 'm_key') $m_key = $value;
            if ($header == 'adid') $adid = $value;
            if ($header == 'fcm_id') $regid = $value;
            if ($header == 'key') $key = $value;  // API KEY

        }

        if ($m_num && $m_key) { //네이티브 세션제어

            $aInput = array(
                'm_num' => $m_num
            ,   'm_key' => $m_key
            );

            $member_row = $this->CI->member_model->get_member_row_app($aInput);
            if (empty($member_row) == false) {
                set_login_session($member_row);
                $this->aMemberInfo = $member_row;
                $this->isLogin = 'Y';

                {//회원정보가 있는경우 fcmid 와 adid 체크 후 update
                    if(empty($regid) == false || empty($adid) == false){
                        $aInput2 = array();
                        if(empty($adid) ==false) $aInput2['m_adid'] = $adid;
                        if(empty($regid) ==false) $aInput2['m_regid'] = $regid;
                        $this->CI->member_model->update_member($member_row['m_num'] , $aInput2);
                    }
                }

            }

        }

    }

    public function _get_member_info(){
        $aMemberInfo = $this->CI->member_model->get_member_row(array('m_num' => $_SESSION['session_m_num']));
        return $aMemberInfo;
    }

    public function getRctly() {
        $aMemberInfo = $this->_get_member_info();
        return $aMemberInfo['rctlyViewPdt'];
    }

    public function setRctly($rctlydata){ //네이티브 최근본상품 db 저장.

        if($_SESSION['session_m_num']){
            $query_data = array();
            if ( !empty($rctlydata) ) $query_data['rctlyViewPdt'] = $rctlydata;
            $this->CI->member_model->update_member($_SESSION['session_m_num'], $query_data);
        }

    }

    /**
     * 페이징
     * @param $param    =>  total_rows      : 전체갯수
     *                      base_url        : URL
     *                      per_page        : 페이지당 출력수
     *                      page            : 현재페이지
     *                      skin            : 스킨(1=관리자(기본값), 2=사용자1)
     *                      page_var_str    : 페이지 변수명(기본값:page)
     *                      ajax            : ajax 요청 여부(true|false)
     *                      sort            : 정렬(reverse|'')
     * @return array    =>  start           : 시작위치 (목록에서 쿼리문(limit문)에서 사용함)
     *                      limit           : 목록에 출력할 갯수 (목록에서 쿼리문(limit문)에서 사용함)
     *                      pagination      : 페이징 HTML
     */
    function _paging($param=array()){

        $config['base_url'] = $param['base_url'];
        $config['total_rows'] = $param['total_rows'];
        $config['per_page'] = ($param['per_page']) ? $param['per_page'] : 20;
        $config['num_links'] = 4;
        $config['use_page_numbers'] = true;
        $config['page_query_string'] = true;
        $config['enable_query_strings'] = true;
        $config['query_string_segment'] = ( isset($param['page_var_str']) && !empty($param['page_var_str']) ) ? $param['page_var_str'] : 'page';

        $page = ($param['page']) ? $param['page'] : 1;
        $total_page = ceil($config['total_rows'] / $config['per_page']);
        if( empty($total_page) ) {
            $total_page = 1;
        }
//        if( $page > $total_page ) {
//            $page = 1;
//        }
        $limit = $config['per_page'];
        if( isset($param['sort']) && $param['sort'] == 'reverse' ) {
            //$start = ($total_page - $page) * $config['per_page'];
            $start = $config['total_rows'] - ($page * $config['per_page']);
            if( $start < 0 ) {
                $limit = $config['per_page'] - abs($start);
                $start = 0;
            }
        }
        else {
            $start = ($page - 1) * $config['per_page'];
        }

        return array(
            'start'         => $start,
            'limit'         => $limit,
            'total_page'    => $total_page
        );
    }//end of _paging()

    public function clearProductField($arr, $add_f = array()){

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
            unset($arr['p_taxation']);
            unset($arr['p_supplier']);
            unset($arr['p_wish_count_user']);
            unset($arr['p_wish_raise_yn']);
            unset($arr['p_wish_raise_count']);
            unset($arr['p_share_count_user']);
            unset($arr['p_share_raise_yn']);
            unset($arr['p_share_raise_count']);
            unset($arr['p_easy_admin_code']);

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
                unset($arr[$k]['p_taxation']);
                unset($arr[$k]['p_supplier']);
                unset($arr[$k]['p_wish_count_user']);
                unset($arr[$k]['p_wish_raise_yn']);
                unset($arr[$k]['p_wish_raise_count']);
                unset($arr[$k]['p_share_count_user']);
                unset($arr[$k]['p_share_raise_yn']);
                unset($arr[$k]['p_share_raise_count']);
                unset($arr[$k]['p_easy_admin_code']);

            }

        }

        return $arr;

    }

}