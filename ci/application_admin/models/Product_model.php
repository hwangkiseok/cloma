<?php
/**
 * 상품 관련 모델
 */
class Product_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 상품 목록 추출
     * @param array $query_array : 쿼리배열
     * @param string $start : limit $start, $end
     * @param string $end : limit $start, $end
     * @param bool $is_count : 전체갯수만 추출여부
     * @param bool $DB
     * @return
     */
    public function get_product_list($query_array=array(), $start="", $end="", $is_count=false, $DB=false) {
        //from 절
        $from_query = "from product_tb P ";
        //$from_query .= "left join product_md_tb MD on pmd_product_num = p_num ";

        //where 절
        $where_query = "where 1 = 1 ";
        //해쉬테그 유무
        if( isset($query_array['where']['hash_chk']) && !empty($query_array['where']['hash_chk']) ) {
            $where_query .= "and (p_hash = '' or p_hash is NULL) ";
        }
        //카테고리
        if( isset($query_array['where']['cate']) && !empty($query_array['where']['cate']) ) {
            $where_query .= "and p_category = '" . $this->db->escape_str($query_array['where']['cate']) . "' ";
        }
        //MD카테고리
        if( isset($query_array['where']['md_div']) && !empty($query_array['where']['md_div']) ) {
            $where_query .= "and pmd_division = '" . $this->db->escape_str($query_array['where']['md_div']) . "' ";
        }
        //기간한정
        if( isset($query_array['where']['term_yn']) && !empty($query_array['where']['term_yn']) ) {
            $where_query .= "and p_termlimit_yn = '" . get_yn_value($this->db->escape_str($query_array['where']['term_yn'])) . "' ";
        }
        //기간검색
        if( isset($query_array['where']['date_type']) && !empty($query_array['where']['date_type']) ) {
            $date_field = $query_array['where']['date_type'];

            if ( isset($query_array['where']['date1']) && !empty($query_array['where']['date1']) ) {
                $where_query .= "and left(" . $date_field . ", 8) >= '" . number_only($this->db->escape_str($query_array['where']['date1'])) . "' ";
            }
            if ( isset($query_array['where']['date2']) && !empty($query_array['where']['date2']) ) {
                $where_query .= "and left(" . $date_field . ", 8) <= '" . number_only($this->db->escape_str($query_array['where']['date2'])) . "' ";
            }
        }

        //메인배너 노출상품
        if( isset($query_array['where']['main_banner_view']) && !empty($query_array['where']['main_banner_view']) ) {
            $where_query .= "and p_main_banner_view = '" . $this->db->escape_str($query_array['where']['main_banner_view']) . "' ";
        }

        //메인배너 노출상품
        if( isset($query_array['where']['second_prict_yn']) && !empty($query_array['where']['second_prict_yn']) ) {
            $where_query .= "and p_price_second_yn = '" . $this->db->escape_str($query_array['where']['second_prict_yn']) . "' ";
        }

        //품절제외
        if( isset($query_array['where']['restock_yn']) && !empty($query_array['where']['restock_yn']) ) {
            $where_query .= "and p_stock_state = '" . $this->db->escape_str($query_array['where']['restock_yn']) . "' ";
        }


        if( isset($query_array['where']['p_outside_display_able']) && !empty($query_array['where']['p_outside_display_able']) ) {
            $p_outside_display_able_arr = $this->db->escape_str($query_array['where']['p_outside_display_able']);
            $where_query .= "and p_outside_display_able IN ('".join("','",$p_outside_display_able_arr)."') ";
        }

        //진열상태 (배열)
        if( isset($query_array['where']['display_state']) && !empty($query_array['where']['display_state']) ) {
            //배열일때
            if( is_array($query_array['where']['display_state']) ) {
                $display_state_array = array();
                foreach($query_array['where']['display_state'] as $key => $item) {
                    $display_state_array[] = "p_display_state = '" . $this->db->escape_str($item) . "'";
                }

                $where_query .= "and (".implode(" or ", $display_state_array).") ";
            }
            //배열아닐때
            else {
                $where_query .= "and p_display_state = '" . $this->db->escape_str($query_array['where']['display_state']) . "' ";
            }
        }
        //판매상태 (배열)
        if( isset($query_array['where']['sale_state']) && !empty($query_array['where']['sale_state']) ) {
            //배열일때
            if( is_array($query_array['where']['sale_state']) ) {
                $sale_state_array = array();
                foreach($query_array['where']['sale_state'] as $key => $item) {
                    $sale_state_array[] = "p_sale_state = '" . $this->db->escape_str($item) . "'";
                }

                $where_query .= "and (".implode(" or ", $sale_state_array).") ";
            }
            //배열아닐때
            else {
                $where_query .= "and p_sale_state = '" . $this->db->escape_str($query_array['where']['sale_state']) . "' ";
            }
        }
        //관심(찜) 올리기 사용
        if( isset($query_array['where']['wish_yn']) && !empty($query_array['where']['wish_yn']) ) {
            $where_query .= "and p_wish_raise_yn = '" . $this->db->escape_str($query_array['where']['wish_yn']) . "' ";
        }
        //공유 올리기 사용
        if( isset($query_array['where']['share_yn']) && !empty($query_array['where']['share_yn']) ) {
            $where_query .= "and p_share_raise_yn = '" . $this->db->escape_str($query_array['where']['share_yn']) . "' ";
        }
        //기간한정사용
        if( isset($query_array['where']['term_yn']) && !empty($query_array['where']['term_yn']) ) {
            $where_query .= "and p_termlimit_yn = '" . $this->db->escape_str($query_array['where']['term_yn']) . "' ";
        }
        //기간한정(on) 검색
        if(
            isset($query_array['where']['termdate1']) && !empty($query_array['where']['termdate1']) &&
            isset($query_array['where']['termdate2']) && !empty($query_array['where']['termdate2'])
        ) {
            $termdate1 = number_only($query_array['where']['termdate1']);
            $termdate2 = number_only($query_array['where']['termdate2']);

            //ymd일때
            $datetime1 = substr($termdate1, 0, 8) . "000000";
            $datetime2 = substr($termdate2, 0, 8) . "595959";

            //ymdHis 일때
            if( strlen($termdate1) == 14 ) {
                $datetime1 = $termdate1;
            }
            if( strlen($termdate2) == 14 ) {
                $datetime2 = $termdate2;
            }

            $where_query .= "and p_termlimit_datetime1 <= '" . $datetime1 . "' ";
            $where_query .= "and p_termlimit_datetime2 >= '" . $datetime2 . "' ";
        }
        //기간한정(off) 검색
        if( isset($query_array['where']['termdate2_end']) && !empty($query_array['where']['termdate2_end']) ) {
            $termdate2_end = number_only($query_array['where']['termdate2_end']);

            //Ymd일때
            $datetime = substr($termdate2_end, 0, 8) . "595959";

            //YmdHis일때
            if( strlen($termdate2_end) == 14 ) {
                $datetime = $termdate2_end;
            }

            $where_query .= "and p_termlimit_datetime2 < '" . $datetime . "' ";
        }
        //2차 판매가 여부 검색
        if( isset($query_array['where']['price_second']) && !empty($query_array['where']['price_second']) ) {
            //배열일때
            if( is_array($query_array['where']['price_second']) ) {
                $price_second_array = array();
                foreach($query_array['where']['price_second'] as $key => $item) {
                    $price_second_array[] = "p_price_second_yn = '" . $this->db->escape_str($item) . "'";
                }
                $where_query .= "and (".implode(" or ", $price_second_array).") ";
            }
            //배열아닐때
            else {
                $where_query .= "and p_price_second_yn = '" . $this->db->escape_str($query_array['where']['price_second']) . "' ";
            }
        }
        //3차 판매가 여부 검색
        if( isset($query_array['where']['price_third']) && !empty($query_array['where']['price_third']) ) {
            //배열일때
            if( is_array($query_array['where']['price_third']) ) {
                $price_third_array = array();
                foreach($query_array['where']['price_third'] as $key => $item) {
                    $price_third_array[] = "p_price_third_yn = '" . $this->db->escape_str($item) . "'";
                }
                $where_query .= "and (".implode(" or ", $price_third_array).") ";
            }
            //배열아닐때
            else {
                $where_query .= "and p_price_third_yn = '" . $this->db->escape_str($query_array['where']['price_third']) . "' ";
            }
        }
        //배송조건 (배열)
        if( isset($query_array['where']['p_dlv_type']) && !empty($query_array['where']['p_dlv_type']) ) {
            //배열일때
            if( is_array($query_array['where']['p_dlv_type']) ) {
                $prod_dlv_type_array = array();
                foreach($query_array['where']['p_dlv_type'] as $key => $item) {
                    $prod_dlv_type_array[] = "p_deliveryprice_type = '" . $this->db->escape_str($item) . "'";
                }

                $where_query .= "and (".implode(" or ", $prod_dlv_type_array).") ";
            }
            //배열아닐때
            else {
                $where_query .= "and p_deliveryprice_type = '" . $this->db->escape_str($query_array['where']['p_dlv_type']) . "' ";
            }
        }


        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
        }

        if(isset($query_array['where']['isRestock']) && !empty($query_array['where']['isRestock'])){
            $where_query .= " AND p_restock_cnt > 0 ";
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            //$order_query = "order by p_num desc ";
            $order_query = "order by p_date desc, p_num desc ";
        }

        //limit 절
        $limit_query = "";
        if( $start !== "" && $end !== "" ) {
            $limit_query .= "limit " . $start . ", " . $end . " ";
        }

        //갯수만 추출
        if( $is_count === TRUE ) {
            $query = "select count(*) cnt ";
            $query .= $from_query;
            $query .= $where_query;

            if( !empty($DB) ) {
                return $DB->query($query)->row_array('cnt');
            }
            else {
                return $this->db->query($query)->row_array('cnt');
            }
        }
        //데이터 추출
        else {
            $query = "select *, ( if ( p_termlimit_datetime1 = '' , p_regdatetime, p_termlimit_datetime1 )) p_date ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;

//            zsView($query);
            //echo $query;

            if( !empty($DB) ) {
                return $DB->query($query)->result_array();
            }
            else {
                return $this->db->query($query)->result_array();
            }
        }
    }//end of get_product_list()

    /**
     * 상품 조회
     * @param $p_num    : 상품번호(product_tb.p_num)
     * @param bool $DB  : DB 연결 객체
     * @return mixed
     */
    public function get_product_row($p_num, $DB=false) {
        //DB 연결 객체가 있으면
        if( !empty($DB) ) {
            return $DB->where('p_num', $p_num)->get('product_tb')->row_array();
        }
        //없으면 로컬 DB
        else {
            return $this->db->where('p_num', $p_num)->get('product_tb')->row_array();
        }
    }//end of get_product_row()

    /**
     * 상품 조회
     * @param $p_num    : 상품번호(product_tb.p_num)
     * @param bool $DB  : DB 연결 객체
     * @return mixed
     */
    public function get_product_row_code($p_order_code, $DB=false) {
        //DB 연결 객체가 있으면
        if( !empty($DB) ) {
            return $DB->where('p_order_code', $p_order_code)->get('product_tb')->row_array();
        }
        //없으면 로컬 DB
        else {
            return $this->db->where('p_order_code', $p_order_code)->get('product_tb')->row_array();
        }
    }//end of get_product_row()




    /**
     * 상품 검색
     * @param array $query_data
     * @return bool
     */
    public function get_product_search_row($query_data=array()) {
        $where_array = array();

        if( isset($query_data['p_name']) && !empty($query_data['p_name']) ) {
            $where_array['p_name'] = $query_data['p_name'];
        }

        if( empty($where_array) ) {
            return false;
        }

        return $this->db->where($where_array)->limit(1)->get("product_tb")->row_array();
    }//end of get_product_search()

    /**
     * 상품 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_product($query_data=array()) {
        if(
            !isset($query_data['p_category']) || empty($query_data['p_category']) ||
            !isset($query_data['p_name']) || empty($query_data['p_name']) ||
            !isset($query_data['p_rep_image']) || empty($query_data['p_rep_image']) ||
            !isset($query_data['p_order_code']) || empty($query_data['p_order_code']) ||
            !isset($query_data['p_sale_price']) || empty($query_data['p_sale_price']) ||
            !isset($query_data['p_display_state']) || empty($query_data['p_display_state']) ||
            !isset($query_data['p_sale_state']) || empty($query_data['p_sale_state'])
        ) {
            return false;
        }

        $query_data['p_regdatetime'] = current_datetime();

        if( $this->db->insert("product_tb", $query_data) ) {
            $insert_id = $this->db->insert_id();
            return array('code' => get_status_code('success'), 'id' => $insert_id);
        }
        else {
            return array('code' => get_status_code('error'));
        }
    }//end of insert_product()

    /**
     * 상품 수정
     * @param $p_num
     * @param array $query_data
     * @return bool
     */
    public function update_product($p_num, $query_data=array()) {
        if( empty($p_num) ) {
            return false;
        }

        if( $this->db->where('p_num', $p_num)->update("product_tb", $query_data) ) {
            return true;
        }
        else {
            return false;
        }
    }//end of update_product()

    /**
     * 상품 삭제
     * @param $p_num
     */
    public function delete_product($p_num) {
        //상품 정보
        $row = $this->get_product_row($p_num);

        //대표 이미지 삭제
        file_delete(3, $row['p_rep_image'], DOCROOT);

        //상세 이미지 삭제
        if( !empty($row['p_detail_image']) ) {
            $array = json_decode($row['p_detail_image'], true);

            if( !empty($array) ) {
                foreach($array as $item) {
                    file_delete(2, $item, DOCROOT);
                }
            }
        }


        $date = date('YmdHis');

        $sql = " INSERT INTO product_tb_del
        (act_id,act_date,p_num,p_category,p_cate1,p_cate2,p_cate3,p_name,p_summary,p_detail,p_detail_add,p_rep_image,p_rep_image_add,p_today_image,p_banner_image,p_detail_image,p_order_link,p_order_code,p_short_url,p_app_link_url,p_app_link_url_2,p_supply_price,p_original_price,p_sale_price,p_app_price_yn,p_app_price,p_price_second_yn,p_price_second,p_price_third_yn,p_price_third,p_margin_price,p_discount_rate,p_margin_rate,p_taxation,p_origin,p_manufacturer,p_supplier,p_deliveryprice_type,p_deliveryprice,p_wish_count,p_wish_count_user,p_wish_raise_yn,p_wish_raise_count,p_share_count,p_share_count_user,p_share_raise_yn,p_share_raise_count,p_termlimit_yn,p_termlimit_datetime1,p_termlimit_datetime2,p_display_info,p_view_count,p_view_3day_count,p_view_yesterday_count,p_view_today_count,p_click_count,p_click_yesterday_count,p_click_today_count,p_click_count_week,p_click_count_last_week,p_comment_count,p_review_count,p_order_count,p_order_count_3h,p_order_count_twoday,p_order_count_week,p_order_count_month,p_order_count_twomonth,p_order_count_last_week,p_regdatetime,p_order,p_display_state,p_sale_state,p_stock_state,p_top_desc,p_btm_desc,p_search_cnt,p_usd_price,p_hash,p_option_buy_cnt_view,p_hotdeal_condition_1,p_hotdeal_condition_2,p_main_banner_view,p_restock_cnt,p_tot_order_count,p_outside_display_able,p_suvin_flag,p_easy_admin_code,p_option_use,p_option_depth,p_option_type,p_mod_id)
        SELECT
          '{$_SESSION['session_au_num']}'
        , '{$date}'
        , p_num,p_category,p_cate1,p_cate2,p_cate3,p_name,p_summary,p_detail,p_detail_add,p_rep_image,p_rep_image_add,p_today_image,p_banner_image,p_detail_image,p_order_link,p_order_code,p_short_url,p_app_link_url,p_app_link_url_2,p_supply_price,p_original_price,p_sale_price,p_app_price_yn,p_app_price,p_price_second_yn,p_price_second,p_price_third_yn,p_price_third,p_margin_price,p_discount_rate,p_margin_rate,p_taxation,p_origin,p_manufacturer,p_supplier,p_deliveryprice_type,p_deliveryprice,p_wish_count,p_wish_count_user,p_wish_raise_yn,p_wish_raise_count,p_share_count,p_share_count_user,p_share_raise_yn,p_share_raise_count,p_termlimit_yn,p_termlimit_datetime1,p_termlimit_datetime2,p_display_info,p_view_count,p_view_3day_count,p_view_yesterday_count,p_view_today_count,p_click_count,p_click_yesterday_count,p_click_today_count,p_click_count_week,p_click_count_last_week,p_comment_count,p_review_count,p_order_count,p_order_count_3h,p_order_count_twoday,p_order_count_week,p_order_count_month,p_order_count_twomonth,p_order_count_last_week,p_regdatetime,p_order,p_display_state,p_sale_state,p_stock_state,p_top_desc,p_btm_desc,p_search_cnt,p_usd_price,p_hash,p_option_buy_cnt_view,p_hotdeal_condition_1,p_hotdeal_condition_2,p_main_banner_view,p_restock_cnt,p_tot_order_count,p_outside_display_able,p_suvin_flag,p_easy_admin_code,p_option_use,p_option_depth,p_option_type,p_mod_id
        FROM product_tb
        WHERE p_num = {$p_num}
        ";
        $this->db->query($sql);


        return $this->db->where('p_num', $p_num)->delete('product_tb');
    }//end of delete_product()

    /**
     * 상품 노출순서 수정
     * @param $p_num
     * @param $p_order
     * @return bool
     */
    public function order_update_product($p_num, $p_order) {
        if( empty($p_num) || empty($p_order) ) {
            return false;
        }

        $where_array = array();
        $where_array['p_num'] = $p_num;

        $query_data = array();
        $query_data['p_order'] = $p_order;

        return $this->db->where($where_array)->update('product_tb', $query_data);
    }//end of order_update_product()


    /**
     * 상품 예전 댓글 갯수
     * @param $p_num
     * @param bool $isCount
     * @return bool
     */
    public function get_product_old_comment_list($p_num, $isCount=false) {
        //상품 정보
        $product_row = $this->get_product_row($p_num);

        if( empty($product_row) ) {
            return false;
        }

        //예전 댓글 갯수
        $old_ym = number_only(substr($product_row->p_regdatetime, 0, 6));

        $from_query = "from comment_" . $old_ym . "_tb ";
        $where_query = "where cmt_table='product' and cmt_table_num='" . $product_row->p_num . "' ";

        if( $isCount === true ) {
            $query = "select count(*) cnt ";
            $query .= $from_query;
            $query .= $where_query;
            return $this->db->query($query)->row_array('cnt');
        }
        else {
            $query = "select * ";
            $query .= $from_query;
            $query .= $where_query;
            return $this->db->query($query)->result_array();
        }
    }//end of get_product_old_comment_list()


    /**
     * 상품 예전 댓글 복구
     * @param $p_num
     * @return bool
     */
    public function product_old_comment_restore($p_num) {
        //상품 정보
        $product_row = $this->get_product_row($p_num);

        if( empty($product_row) ) {
            return false;
        }

        $old_ym = number_only(substr($product_row->p_regdatetime, 0, 6));
        $old_comment_tb = "comment_" . $old_ym . "_tb";

        //예전 댓글 목록
        $cmt_list = $this->get_product_old_comment_list($product_row->p_num);

        foreach ($cmt_list as $key => $row) {
            //등록
            $query_data = array();
            $query_data['cmt_num'] = $row->cmt_num;     //예전 댓글번호 그대로 등록함.
            $query_data['cmt_table'] = $row->cmt_table;
            $query_data['cmt_table_num'] = $row->cmt_table_num;
            $query_data['cmt_admin'] = $row->cmt_admin;
            $query_data['cmt_name'] = $row->cmt_name;
            $query_data['cmt_profile_img'] = $row->cmt_profile_img;
            $query_data['cmt_member_num'] = $row->cmt_member_num;
            $query_data['cmt_content'] = $row->cmt_content;
            $query_data['cmt_parent_num'] = $row->cmt_parent_num;
            $query_data['cmt_reply_comment_num'] = $row->cmt_reply_comment_num;
            $query_data['cmt_reply_member_name'] = $row->cmt_reply_member_name;
            $query_data['cmt_reply_member_num'] = $row->cmt_reply_member_num;
            $query_data['cmt_regdatetime'] = $row->cmt_regdatetime;
            $query_data['cmt_best_order'] = $row->cmt_best_order;
            $query_data['cmt_blind'] = $row->cmt_blind;
            $query_data['cmt_blind_memo'] = $row->cmt_blind_memo;
            $query_data['cmt_report_count'] = $row->cmt_report_count;
            $query_data['cmt_display_state'] = $row->cmt_display_state;
            if( $this->db->insert("comment_tb", $query_data) ) {
                //복구된 댓글 삭제
                $this->db->where('cmt_num', $row->cmt_num)->delete($old_comment_tb);
            }
        }//end of foreach()

        return true;
    }//end of product_old_comment_restore()

    /**
     * @date 180515
     * @modify 황기석
     * @desc 재입고알림 푸시발송대상자
     * @param $p_num :: 상품코드
     */
    public function get_restock_push_list($p_num) {

        $sql = "
                SELECT 
                    A.*
                  , B.m_regid
                  , B.m_device_model
                FROM restock_tb A
                INNER JOIN member_tb B ON A.m_num = B.m_num  
                WHERE p_num = ? 
                AND send_flag = 'N' ; 
            ";
        $oResult = $this->db->query($sql,array($p_num));
        $aResult = $oResult->result_array();

        return $aResult;
    }

    /**
     * @date 180515
     * @modify 황기석
     * @desc 재입고알림 초기화
     * @param $p_num :: 상품코드
     */
    public function init_restock_push_data($p_num) {

        { // 상품 > 재입고대상cnt 초기화
            $sql = "UPDATE product_tb SET p_restock_cnt = 0 WHERE p_num = ? ; ";
            $this->db->query($sql,array($p_num));
        }

        { //재입고 > 해당상품 재입고 알림대상자 푸시발송처리
            $sql = "UPDATE restock_tb SET send_flag = 'Y' WHERE p_num = ? ; ";
            $this->db->query($sql,array($p_num));
        }
    }

    /**
     * 상품 리뷰 갯수 업데이트
     * @param $p_num
     * @return mixed
     */
    public function update_prdouct_review_count($p_num) {
        if( empty($p_num) ) {
            return false;
        }

        //리뷰 갯수 (블라인드 제외)
        $sql = "
        SELECT
            COUNT(*) cnt
        FROM review_tb
        WHERE
            re_table_num = '" . $p_num . "'
            AND re_display_state = 'Y' 
            AND re_blind = 'N'
        ";
        $re_row = $this->db->query($sql)->row_array();

        //상품 리뷰 갯수 업데이트
        $sql = "
        UPDATE product_tb
        SET
            p_review_count = '" . $re_row['cnt'] . "'
        WHERE
            p_num = '" . $p_num . "'
        ";
        return $this->db->query($sql);
    }//end update_prdouct_review_count;

}//end of class Product_model