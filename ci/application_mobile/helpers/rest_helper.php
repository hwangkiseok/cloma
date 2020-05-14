<?php

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