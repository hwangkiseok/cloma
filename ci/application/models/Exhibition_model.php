<?php
/**
 * 상품 MD 관련 모델
 */
class Exhibition_model extends W_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 기획전 목록 추출
     * @return array
     */
    public function get_exhibition_list(){

        $sql = "SELECT 
                  *
                FROM special_offer_tb sot
                WHERE sot.activate_flag = 'Y'
                AND ( sot.view_type = 'B' OR ( sot.view_type = 'A' AND sot.start_date <= DATE_FORMAT(NOW(),'%Y%m%d') AND sot.end_date >= DATE_FORMAT(NOW(),'%Y%m%d') ) )
                ORDER BY sot.sort_num ASC ;
                
        " ;

        $oResult = $this->db->query($sql);
        $result = $oResult->result_array();

        return $result;

    }


    /**
     * 상품 목록 추출
     * @param array $query_array : 쿼리배열
     * @param string $start : limit $start, $end
     * @param string $end : limit $start, $end
     * @param bool $is_count : 전체갯수만 추출여부
     * @param bool $DB
     * @return
     */
    public function get_exhibition_product_list($arrayParams = array()) {

        $addWhereQueryString = "";

        if($arrayParams['seq']){
            $addWhereQueryString .= " AND sot.seq = '{$arrayParams['seq']}' ";
        }

        $sql = "SELECT 
                  sot.seq
                , sot.thema_name
                , sot.banner_img
                , sot.use_class
                , sot.fold_flag
                , sot.folded
                , sot.bg_color 
                , sot.head_title
                , sot.tag
                , sot.header_title_img 
                , sot.view_type
                , sot.start_date
                , sot.end_date
                , pt.*
                FROM special_offer_tb sot
                INNER JOIN special_offer_product_tb sopt ON sopt.parent_seq = sot.seq
                INNER JOIN product_tb pt on pt.p_num = sopt.p_num
                
                WHERE sot.activate_flag = 'Y'
                AND ( sot.view_type = 'B' OR ( sot.view_type = 'A' AND sot.start_date <= DATE_FORMAT(NOW(),'%Y%m%d') AND sot.end_date >= DATE_FORMAT(NOW(),'%Y%m%d') ) )
                AND pt.p_display_state = 'Y' #진열함 
                AND pt.p_sale_state = 'Y' #판매중
                AND pt.p_stock_state = 'Y' #재고있음
                {$addWhereQueryString}
                ORDER BY sot.sort_num ASC , sopt.sort_num ASC ;
                
        " ;

        $oResult = $this->db->query($sql);
        $tmp_result = $oResult->result_array();

        $g			= -1;
        $special_offer_array = array();

        for ($i=0; $i < count($tmp_result); $i++) {

            $special_offer_sub_array = array();

            $special_offer_sub_array['p_num']	                = $tmp_result[$i]['p_num'];
            $special_offer_sub_array['p_cate1']	                = $tmp_result[$i]['p_cate1'];
            $special_offer_sub_array['p_name']	                = $tmp_result[$i]['p_name'];
            $special_offer_sub_array['p_summary']	            = $tmp_result[$i]['p_summary'];
            $special_offer_sub_array['p_rep_image']		        = $tmp_result[$i]['p_rep_image'];
            $special_offer_sub_array['p_today_image']		    = $tmp_result[$i]['p_today_image'];
            $special_offer_sub_array['p_original_price']	    = $tmp_result[$i]['p_original_price'];
            $special_offer_sub_array['p_sale_price']	        = $tmp_result[$i]['p_sale_price'];
            $special_offer_sub_array['p_discount_rate']	        = $tmp_result[$i]['p_discount_rate'];
            $special_offer_sub_array['p_wish_count']		    = $tmp_result[$i]['p_wish_count'];
            $special_offer_sub_array['p_share_count']		    = $tmp_result[$i]['p_share_count'];
            $special_offer_sub_array['p_deliveryprice_type']    = $tmp_result[$i]['p_deliveryprice_type'];
            $special_offer_sub_array['p_deliveryprice']		    = $tmp_result[$i]['p_deliveryprice'];
            $special_offer_sub_array['p_termlimit_yn']		    = $tmp_result[$i]['p_termlimit_yn'];
            $special_offer_sub_array['p_termlimit_datetime1']	= $tmp_result[$i]['p_termlimit_datetime1'];
            $special_offer_sub_array['p_termlimit_datetime2']   = $tmp_result[$i]['p_termlimit_datetime2'];
            $special_offer_sub_array['p_view_count']		    = $tmp_result[$i]['p_view_count'];
            $special_offer_sub_array['p_click_count']		    = $tmp_result[$i]['p_click_count'];
            $special_offer_sub_array['p_stock_state']		    = $tmp_result[$i]['p_stock_state'];
            $special_offer_sub_array['p_sale_state']		    = $tmp_result[$i]['p_sale_state'];
            $special_offer_sub_array['p_main_banner_view']		= $tmp_result[$i]['p_main_banner_view'];
            $special_offer_sub_array['p_tot_order_count']		= $tmp_result[$i]['p_tot_order_count'];
            $special_offer_sub_array['p_order_code']		    = $tmp_result[$i]['p_order_code'];
            $special_offer_sub_array['p_hash']		            = $tmp_result[$i]['p_hash'];
            $special_offer_sub_array['p_review_count']		    = $tmp_result[$i]['p_review_count'];
            $special_offer_sub_array['p_date']		            = $tmp_result[$i]['p_regdatetime'];

            $special_offer_sub_array['seq']	                    = $tmp_result[$i]['seq'];

            if($group_cd != $tmp_result[$i]['seq']) {
                $g++;
                $special_offer_array[$g] = array();
                $special_offer_array[$g]['thema_name']	= $tmp_result[$i]['thema_name'];
                $special_offer_array[$g]['banner_img']	= $tmp_result[$i]['banner_img'];
                $special_offer_array[$g]['seq']			= $tmp_result[$i]['seq'];

                $special_offer_array[$g]['view_type']	= $tmp_result[$i]['view_type'];
                $special_offer_array[$g]['start_date']	= view_date_format($tmp_result[$i]['start_date']);
                $special_offer_array[$g]['end_date']	= view_date_format($tmp_result[$i]['end_date']);

                $group_cd                               = $tmp_result[$i]['seq'];

                $special_offer_array[$g]['children']			= array();
                array_push($special_offer_array[$g]['children'], $special_offer_sub_array);

            } else {
                array_push($special_offer_array[$g]['children'], $special_offer_sub_array);
            }

        }
        return $special_offer_array;

    }//end of get_special_offer_list()

}//end of class Product_model