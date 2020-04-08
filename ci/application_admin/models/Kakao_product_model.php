<?php
/**
 * 카카오광고 상품 관련 모델
 */
class Kakao_product_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 카카오광고 상품 목록 추출
     * @param array $query_array : 쿼리배열
     * @param string $start : limit $start, $end
     * @param string $end : limit $start, $end
     * @param bool $is_count : 전체갯수만 추출여부
     * @return
     */
    public function get_kakao_product_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "
            from kakao_product_tb
                join product_tb on p_num = kp_product_num
        ";

        //where 절
        $where_query = "where 1 = 1 ";
        //노출상태 (배열)
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
        //해쉬테그 유무
        if( isset($query_array['where']['prod_hash_chk']) && !empty($query_array['where']['prod_hash_chk']) ) {
            $where_query .= "and (p_hash = '' or p_hash is NULL) ";
        }
        //2차판매가 여부
        if( isset($query_array['where']['prod_second_price_yn']) && !empty($query_array['where']['prod_second_price_yn']) ) {
            $where_query .= "and p_price_second_yn = '" . $this->db->escape_str($query_array['where']['prod_second_price_yn']) . "' ";
        }
        //품절제외
        if( isset($query_array['where']['prod_restock_yn']) && !empty($query_array['where']['prod_restock_yn']) ) {
            $where_query .= "and p_stock_state = '" . $this->db->escape_str($query_array['where']['prod_restock_yn']) . "' ";
        }
        //진열상태 (배열)
        if( isset($query_array['where']['prod_display_state']) && !empty($query_array['where']['prod_display_state']) ) {
            //배열일때
            if( is_array($query_array['where']['prod_display_state']) ) {
                $display_state_array = array();
                foreach($query_array['where']['prod_display_state'] as $key => $item) {
                    $display_state_array[] = "p_display_state = '" . $this->db->escape_str($item) . "'";
                }

                $where_query .= "and (".implode(" or ", $display_state_array).") ";
            }
            //배열아닐때
            else {
                $where_query .= "and p_display_state = '" . $this->db->escape_str($query_array['where']['prod_display_state']) . "' ";
            }
        }
        //판매상태 (배열)
        if( isset($query_array['where']['prod_sale_state']) && !empty($query_array['where']['prod_sale_state']) ) {
            //배열일때
            if( is_array($query_array['where']['prod_sale_state']) ) {
                $sale_state_array = array();
                foreach($query_array['where']['prod_sale_state'] as $key => $item) {
                    $sale_state_array[] = "p_sale_state = '" . $this->db->escape_str($item) . "'";
                }

                $where_query .= "and (".implode(" or ", $sale_state_array).") ";
            }
            //배열아닐때
            else {
                $where_query .= "and p_sale_state = '" . $this->db->escape_str($query_array['where']['prod_sale_state']) . "' ";
            }
        }
        //상품구분
        if( isset($query_array['where']['prod_type']) && !empty($query_array['where']['prod_type']) ) {
            //배열일때
            if( is_array($query_array['where']['prod_type']) ) {
                $where_query .= "and kp_prod_type in ('".implode("','", $query_array['where']['prod_type'])."') ";
            }
            //배열아닐때
            else {
                $where_query .= "and kp_prod_type = '" . $this->db->escape_str($query_array['where']['prod_type']) . "' ";
            }
        }

        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = "order by kp_click_count desc, kp_num desc ";
        }

        //limit 절
        $limit_query = "";
        if( $start !== "" && $end !== "" ) {
            $limit_query .= "limit " . $start . ", " . $end . " ";
        }

        //갯수만 추출
        if( $is_count === true ) {
            $query = "select count(*) cnt ";
            $query .= $from_query;
            $query .= $where_query;

            //echo $query;
            return $this->db->query($query)->row('cnt');
        }
        //데이터 추출
        else {
            $query = "select * ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;

            //echo $query;
            return $this->db->query($query)->result();
        }
    }//end of get_kakao_product_list()

    /**
     * 카카오광고 상품 조회
     * @param $kp_num   : 일련번호
     * @param bool $DB  : DB 연결 객체
     * @return mixed
     */
    public function get_kakao_product_row($kp_num) {
        $sql = "
            select *
            from kakao_product_tb
                join product_tb on p_num = kp_product_num
            where kp_num = '" . $this->db->escape_str($kp_num) . "'
        ";
        return $this->db->query($sql)->row();
    }//end of get_kakao_product_row()

    /**
     * 카카오광고 상품 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_kakao_product($query_data=array()) {
        if(
            !isset($query_data['kp_product_num']) || empty($query_data['kp_product_num']) ||
            !isset($query_data['kp_display_state']) || empty($query_data['kp_display_state'])
        ) {
            return false;
        }

        $query_data['kp_regdatetime'] = current_datetime();

        if( $this->db->insert("kakao_product_tb", $query_data) ) {
            $insert_id = $this->db->insert_id();
            return array('code' => get_status_code('success'), 'id' => $insert_id);
        }
        else {
            return array('code' => get_status_code('error'));
        }
    }//end of insert_kakao_product()

    /**
     * 카카오광고 상품 수정
     * @param $kp_num
     * @param array $query_data
     * @return bool
     */
    public function update_kakao_product($kp_num, $query_data=array()) {
        if( empty($kp_num) ) {
            return false;
        }

        if( $this->db->where('kp_num', $kp_num)->update("kakao_product_tb", $query_data) ) {
            return true;
        }
        else {
            return false;
        }
    }//end of update_kakao_product()

    /**
     * 카카오광고 상품 삭제
     * @param $kp_num
     */
    public function delete_kakao_product($kp_num) {
        return $this->db->where('kp_num', $kp_num)->delete('kakao_product_tb');
    }//end of delete_kakao_product()

}//end of class Kakao_product_model