<?php
/**
 * 상품 관련 모델
 */
class Product_model extends W_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 상품 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_product_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from product_tb ";

        //where 절
        $where_query = "where p_display_state = 'Y' ";

        //카테고리
        if( isset($query_array['where']['cate']) && !empty($query_array['where']['cate']) ) {
            $where_query .= "and p_category = '" . $this->db->escape_str($query_array['where']['cate']) . "' ";
        }
        //MD카테고리
        if( isset($query_array['where']['md_div']) && !empty($query_array['where']['md_div']) ) {
            $from_query .= "join product_md_tb on pmd_product_num = p_num ";
            $where_query .= "and pmd_division = '" . $this->db->escape_str($query_array['where']['md_div']) . "' ";
        }
        //등록일
        if( isset($query_array['where']['date1']) && !empty($query_array['where']['date1']) ) {
            $where_query .= "and left(p_regdatetime, 8) >= '" . number_only($this->db->escape_str($query_array['where']['date1'])) . "' ";
        }
        if( isset($query_array['where']['date2']) && !empty($query_array['where']['date2']) ) {
            $where_query .= "and left(p_regdatetime, 8) <= '" . number_only($this->db->escape_str($query_array['where']['date2'])) . "' ";
        }
        //판매상태 (배열)
        if( isset($query_array['where']['sale_state']) && !empty($query_array['where']['sale_state']) ) {
            $where_query .= "and p_sale_state = '" . $this->db->escape_str($query_array['where']['sale_state']) . "' ";
        }
        //재고상태
        if( isset($query_array['where']['stock_state']) && !empty($query_array['where']['stock_state']) ) {
            $where_query .= "and p_stock_state = '" . $this->db->escape_str($query_array['where']['stock_state']) . "' ";
        }
        //배너이미지 있는것만
        if( isset($query_array['where']['banner_image']) && !empty($query_array['where']['banner_image']) ) {
            $where_query .= "and p_banner_image != '' ";
        }
        //메인배너 노출상품
        if( isset($query_array['where']['main_banner_view']) && !empty($query_array['where']['main_banner_view']) ) {
            $where_query .= "and p_main_banner_view = '" . $this->db->escape_str($query_array['where']['main_banner_view']) . "' ";
        }
        //제외 상품 (복수, 단일)
        if( isset($query_array['where']['not_pnum']) && !empty($query_array['where']['not_pnum']) ) {
            if( is_array($query_array['where']['not_pnum']) ) {
                $where_query .= "and p_num not in (" . implode(",", $query_array['where']['not_pnum']) . ") ";
            }
            else {
                $where_query .= "and p_num != '" . $this->db->escape_str($query_array['where']['not_pnum']) . "' ";
            }
        }
        //검색타입
        if( isset($query_array['where']['sch_type']) && !empty($query_array['where']['sch_type']) ) {
            //무료배송
            if( $query_array['where']['sch_type'] == "freeShip" ) {
                $where_query .= "and p_deliveryprice_type = '3' ";
            }
            //1만원대
            else if( $query_array['where']['sch_type'] == "price1" ) {
                $where_query .= "and p_sale_price < 20000 ";
                if( !isset($query_array['orderby']) || empty($query_array['orderby']) ) {
                    $query_array['orderby'] = "p_sale_price asc";
                }
            }
        }
        if($query_array['where']['auto_complete'] == 'Y'){
            if( isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd']) ) {
                $where_query .= "and ( p_name like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' OR p_hash like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ) ";
            }
        }else{

            //키워드
            if(
                isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
                isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
            ) {
                $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
            }

        }

        if(isset($query_array['where']['ctgr']) && !empty($query_array['where']['ctgr'])){
            $where_query .= " AND FIND_IN_SET(p_cate1, '{$query_array['where']['ctgr']}') ";
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . ", p_num desc ";
        }
        else {
            $order_query = "order by p_sale_state asc, p_date desc, p_num desc ";
        }

        //초기화 후 다시 셋팅
        if($query_array['main_best'] == 'Y'){

            $where_query = '';
            $order_query = '';

            $where_query .= " WHERE p_display_state = 'Y' AND p_sale_state = 'Y' AND p_stock_state = 'Y' ";

            //제외 상품 (복수, 단일)
            if( isset($query_array['where']['not_pnum']) && !empty($query_array['where']['not_pnum']) ) {
                if( is_array($query_array['where']['not_pnum']) ) {
                    $where_query .= "and p_num not in (" . implode(",", $query_array['where']['not_pnum']) . ") ";
                }
                else {
                    $where_query .= "and p_num != '" . $this->db->escape_str($query_array['where']['not_pnum']) . "' ";
                }
            }

            $order_query .= " ORDER BY p_order_count_twoday DESC , p_date DESC ";

        }

        //limit 절
        $limit_query = "";
        if( $start !== "" && $end !== "" ) {
            $limit_query .= "limit " . $start . ", " . $end . " ";
        }

        //갯수만 추출
        if( $is_count === true ) {
            $query = "SELECT COUNT(*) AS cnt
            {$from_query}
            {$where_query} 
            ";

            return $this->db->query($query)->row_array('cnt');

        }
        //데이터 추출
        else {

            if($_SESSION['session_m_num']) {
                $reviewQueryString = " AND ( re_blind = 'N' OR re_member_num = '".$_SESSION['session_m_num']."' ) ";
            }else{
                $reviewQueryString = " AND re_blind = 'N' ";
            }

            $query = "select 
                          p_num 
                        , p_category
                        , p_cate1
                        , p_name
                        , p_summary
                        , p_rep_image
                        , p_today_image
                        , p_banner_image
                        , p_order_link
                        , p_original_price
                        , p_sale_price
                        , p_app_price_yn
                        , p_app_price
                        , p_price_second_yn
                        , p_price_second
                        , p_price_third_yn
                        , p_price_third
                        , p_discount_rate
                        , p_wish_count
                        , p_share_count
                        , p_deliveryprice_type
                        , p_deliveryprice
                        , p_termlimit_yn
                        , p_termlimit_datetime1
                        , p_termlimit_datetime2
                        , p_display_info
                        , p_view_count
                        , p_click_count
                        , p_stock_state
                        , p_sale_state
                        , p_hotdeal_condition_1
                        , p_hotdeal_condition_2
                        , p_main_banner_view
                        , p_tot_order_count
                        , p_order_code
                        , p_hash
                        , (SELECT COUNT(*) FROM review_tb WHERE re_table_num = p_num {$reviewQueryString} AND re_display_state = 'Y') AS p_review_count
                        , ( if ( p_termlimit_datetime1 = '' , p_regdatetime, p_termlimit_datetime1 )) p_date 
                        , ( p_margin_price * p_order_count_twomonth ) as total_margin 
                        , ( p_margin_price * p_order_count_twoday ) as total_margin_twoday
                {$from_query}  
                {$where_query}
                {$order_query}
                {$limit_query}
            ";

            return $this->db->query($query)->result_array();
        }
    }//end of get_product_list()


    /**
     * 상품 목록
     * @param $query : 쿼리(select * from product_tb 형식의 full query)
     */
    public function get_product_list_from_query($query) {
        if( empty($query) ) {
            return false;
        }

        $product_list = $this->db->query($query)->result();

        foreach ($product_list as $key => $row) {
            $row = $this->_unset_security_field($row);
        }

        return $product_list;
    }//end of get_product_list_from_query()


    /**
     * 앱 닫기시 노출 상품 2개
     */
    public function get_close_product($arrayParams) {

        $whereQueryString = '';

        $tot_cnt = 4;

        if(empty($arrayParams['not_in']) == false){

            $not_in = array();

            if(is_array($arrayParams['not_in']) == true){
                foreach ($arrayParams['not_in'] as $r)  $not_in[] = $r['p_num'];
                $whereQueryString .= " AND p_num NOT IN (". implode(',',$not_in) .")";
                $limit_cnt = $tot_cnt - count($arrayParams['not_in']);
            }else{
                $whereQueryString .= " AND p_num <> '{$arrayParams['not_in']}' ";
                $limit_cnt = $tot_cnt - 1;
            }

        }else{

            $limit_cnt = $tot_cnt;

        }

        $sql = "SELECT 
                * 
                FROM (
                    SELECT 
                    *
                    FROM product_tb
                    WHERE p_display_state = 'Y'
                    AND p_sale_state = 'Y'
                    AND p_stock_state = 'Y' 
                    {$whereQueryString}
                    ORDER BY p_order_count_twoday DESC
                    LIMIT 20
                ) T
                ORDER BY T.p_margin_price DESC 
                LIMIT {$limit_cnt};
        ";

        $oResult = $this->db->query($sql);
        $product_list = $oResult->result_array();

        return $product_list;
    }//end of get_close_product()

    /**
     * 상품 조회
     * @param array
     * @return mixed
     */
    public function get_product_row($arrayParams) {

        if( empty($arrayParams['p_num']) == true && empty($arrayParams['p_code']) == true ) {
            return false;
        }

        $addQueryString = "";

        $where_array = array();
        if(empty($arrayParams['p_num']) == false) $addQueryString .= " AND p_num = '{$arrayParams['p_num']}' ";
        if(empty($arrayParams['p_code']) == false) $addQueryString .= " AND p_code = '{$arrayParams['p_code']}' ";

        if(empty($arrayParams['p_display_state']) == true) $addQueryString .= " AND p_display_state = 'Y' ";
        else $addQueryString .= " AND p_display_state = '{$where_array['p_display_state']}' ";

        if(empty($arrayParams['p_sale_state']) == true)  $addQueryString .= " AND p_sale_state = 'Y' ";
        else $addQueryString .= " AND p_sale_state = '{$where_array['p_sale_state']}' ";

        if(empty($arrayParams['p_stock_state']) == true) $addQueryString .= " AND p_stock_state = 'Y' ";
        else $addQueryString .= " AND p_stock_state = '{$where_array['p_stock_state']}' ";

        $sql = "SELECT 
                * 
                FROM product_tb
                WHERE 1
                {$addQueryString}
        ";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        //사용자단에는 필요없는 필드 삭제함
        //$aResult = $this->_unset_security_field($aResult);

        return $aResult;
    }//end of get_product_row()

    public function get_snsform_product_row($p_order_code){

        $sql = "SELECT * FROM snsform_product_tb WHERE item_no = '{$p_order_code}' ";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        return $aResult;

    }

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

        return $this->db->where('p_num', $p_num)->update('product_tb', $query_data);
    }//end of update_product()

    /**
     * 사용자단에는 필요없는 필드 삭제함
     * @param $row
     */
    private function _unset_security_field($row) {
        if( empty($row) ) {
            return false;
        }

        unset($row['p_supply_price']);
        unset($row['p_margin_price']);
        unset($row['p_margin_rate']);
        unset($row['p_taxation']);
        unset($row['p_supplier']);
        unset($row['p_wish_count_user']);
        unset($row['p_wish_raise_yn']);
        unset($row['p_wish_raise_count']);
        unset($row['p_share_count_user']);
        unset($row['p_share_raise_yn']);
        unset($row['p_share_raise_count']);

        return $row;
    }//end of _unset_security_field()

    public function getRelationProductLists($p_num){

        $sql = "SELECT 
                * 
                FROM product_relation_tb A
                INNER JOIN product_tb B ON A.c_pnum = p_num 
                WHERE A.p_pnum        = '{$p_num}'
                AND A.view_yn         = 'Y'  
                AND B.p_sale_state    = 'Y'
                AND B.p_display_state = 'Y'
                AND B.p_stock_state   = 'Y'
                ORDER BY A.sort_num ASC
                LIMIT 0 , 12 ; 
        ";
        $oRelationProductLists = $this->db->query($sql);
        $aRelationProductLists = $oRelationProductLists->result_array();

        return $aRelationProductLists;

    }

    public function rel_product_count($p_pnum,$c_pnum = 0,$fd,$target){

        if($target == 'parent'){
            $sql = "UPDATE product_relation_tb A
                    INNER JOIN product_tb B ON A.c_pnum = p_num
                    SET {$fd}_cnt = {$fd}_cnt + 1 
                    WHERE A.p_pnum = '{$p_pnum}' 
                    AND A.view_yn         = 'Y'  
                    AND B.p_sale_state    = 'Y' 
                    AND B.p_display_state = 'Y'
                    AND B.p_stock_state   = 'Y' ; 
            ";
            $this->db->query($sql);
        }else if($target == 'child'){
            $sql = "UPDATE product_relation_tb A
                    SET {$fd}_cnt = {$fd}_cnt + 1 
                    WHERE A.c_pnum = '{$c_pnum}' 
                    AND A.p_pnum = '{$p_pnum}'
            ";
            $this->db->query($sql);
        }
    }

    public function get_main_product($arrayParams , $s = '' , $e = ''){

        $addQueryString = '';
        if(empty($arrayParams['not_pnum']) == false){
            if(is_array($arrayParams['not_pnum']) == true){
                $not_in_num = "'" . implode("','",$arrayParams['not_pnum']) . "'";
            }else{
                $not_in_num = "{$arrayParams['not_pnum']}";
            }
            $addQueryString .= " AND B.p_num NOT IN ({$not_in_num})";
        }

        //limit 절
        $limit_query = "";
        if( $s !== "" && $e !== "" ) {
            $limit_query .= "limit " . $s . ", " . $e . " ";
        }

        $sql = "SELECT 
                B.* 
                FROM main_product_tb A
                INNER JOIN product_tb B ON A.p_num = B.p_num
                WHERE A.use_flag = 'Y'
                AND B.p_sale_state    = 'Y' 
                AND B.p_display_state = 'Y'
                AND B.p_stock_state   = 'Y'
                {$addQueryString}
                ORDER BY A.sort ASC
                {$limit_query} ; 
        ";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        foreach ($aResult as $k => $r) $aResult[$k] = $this->_unset_security_field($r);

        return $aResult;

    }

    public function get_main_thema($arrayParams){

        $sql        = "SELECT 
                            seq
                       ,    thema_name
                       ,    display_type 
                       FROM main_thema_tb 
                       WHERE 1
                       AND activate_flag = 'Y'
                       AND ( view_type = 'B' OR ( view_type = 'A' AND start_date <= DATE_FORMAT(NOW(),'%Y%m%d') AND end_date >= DATE_FORMAT(NOW(),'%Y%m%d') ) )
                       ORDER BY sort_num ASC ; 
        ";
        $oResult        = $this->db->query($sql);
        $main_thema_list = $oResult->result_array();

        $aResult = array();

        foreach ($main_thema_list as $row) {

            $where_query = '';
            if(empty($arrayParams['not_in']) == false){
                if( is_array($arrayParams['not_in']) == true ) {
                    $where_query .= " AND sop.p_num NOT IN (" . implode(",", $arrayParams['not_in']) . ") ";
                } else {
                    $where_query .= " AND sop.p_num != '" . $this->db->escape_str($arrayParams['not_in']) . "' ";
                }
            }

            $sql        = " SELECT * 
                            FROM main_thema_product_tb sop
                            INNER JOIN product_tb pt ON pt.p_num = sop.p_num 
                            WHERE sop.parent_seq = '{$row['seq']}' 
                            {$where_query}
                            ORDER BY sop.sort_num ASC ;
            ";

            $oResult                    = $this->db->query($sql);
            $main_thema_product_lists   = $oResult->result_array();

            $aResult[] = array(
                'main_thema_row'             => $row
            ,   'main_thema_product_lists'   => $main_thema_product_lists
            );

            foreach ($main_thema_product_lists as $r) {
                $arrayParams['not_in'][] = $r['p_num'];
            }

        }

        return array('list' => $aResult , 'not_in' => $arrayParams['not_in']);

    }

}//end of class Product_model