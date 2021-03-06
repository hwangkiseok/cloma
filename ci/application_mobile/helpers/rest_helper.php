<?php

/**
 * @date 180326
 * @modify 옵션별데이터 ajax
 */
function _option_graph($p_order_code){

    $CI =& get_instance();
    $CI->load->model("product_model");

    $aInput = array('p_code' => $p_order_code);

    if (empty($aInput['p_code'])) {
        $rResult = array('success' => false , 'msg' => '필수입력정보 없음[pcode]' , 'data' => '');
        echo json_encode_no_slashes($rResult);
        exit;
    }

    /**
     * @date 190208
     * @modify 황기석
     * @desc curl 처리 -> db conn으로 변경
     */
    if (0) {
        $url = $CI->config->item("order_site_http") . "/api/zsApi.php";
        $param = "mode=getBuyOptionInfo&p_code={$aInput['p_code']}";
        $jResult = http_post_request($url, $param);
        $aResult =json_decode($jResult,true);

        $rResult = array('success' => true , 'msg' => '' , 'data' => $aResult);
    } else {
        $aResult = $CI->product_model->getBuyOptionInfo($aInput);
        $rResult = array('success' => true , 'msg' => '' , 'data' => $aResult);
    }

    return $rResult;
}

/**
 * 추천상품 목록 배열 리턴
 * @param $prod_list
 * @param $wish_list_result
 * @param string $arrival_source
 * @return array
 */
function _getRecommendProductListFormat($prod_list, $wish_list_result, $arrival_source="") {

    $pd_list = array();

    foreach ($prod_list as $key => $val) {
        $pd_list[$key]['p_num'] = $val->p_num;
        $pd_list[$key]['p_discount_rate'] = ceil($val->p_discount_rate) !="0"?ceil($val->p_discount_rate) . "%" : "핫딜가";
        if ( $val->p_deliveryprice_type != '1' ) {
            $pd_list[$key]['delivery_text'] = get_config_item_text($val->p_deliveryprice_type, 'product_deliveryprice_type','false');
        } else {
            $pd_list[$key]['delivery_text'] = "";
        }

        $pd_list[$key]['p_name'] = $val->p_name;
        $pd_list[$key]['p_sale_price'] = number_format($val->p_sale_price)."원";
        $pd_list[$key]['p_original_price'] = ($val->p_original_price != "0") ? number_format($val->p_original_price) . "원" : "";
        $pd_list[$key]['p_today_image'] = IMG_HTTP . $val->p_today_image;

        if ( is_array($wish_list_result) ) {
            if ( in_array($val->p_num, $wish_list_result) ) {
                $pd_list[$key]['p_like_chk'] = "1";
            } else {
                $pd_list[$key]['p_like_chk'] = "0";
            }
        } else {
            $pd_list[$key]['p_like_chk'] = "0";
        }

        if ( $val->p_stock_state == 'N' ) {
            $pd_list[$key]['p_soldout_chk'] = "1";
        } else {
            $pd_list[$key]['p_soldout_chk'] = "0";
        }

        $pd_list[$key]['arrival_source'] = $arrival_source;
        $pd_list[$key]['etc_data'] = "test";

    }

    return $pd_list;
}//end _getProductListItemFormat;