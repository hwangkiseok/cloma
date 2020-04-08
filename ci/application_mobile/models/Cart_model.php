<?php
/**
 * 장바구니 관련 모델
 */
class Cart_model extends M_Model {

    public $m_num;

    public function __construct(){
        parent::__construct();
        $this->m_num = $_SESSION['session_m_num'];
    }//end of __construct()


    /**
     * 장바구니 목록 추출
     * @param array $query_data    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_cart_list($query_data=array(), $start="", $end="", $is_count=false) {

        //from 절
        $from_query = "from cart_tb ct ";
        $from_query .= "join product_tb pt on pt.p_num = ct.p_num ";

        //where 절
        $where_query = "where 1 AND ct.m_num = '" . $this->m_num . "' ";

        //키워드
        if(
            isset($query_data['where']['kfd']) && !empty($query_data['where']['kfd']) &&
            isset($query_data['where']['kwd']) && !empty($query_data['where']['kwd'])
        ) {
            $where_query .= "and " . $query_data['where']['kfd'] . " like '%" . $this->db->escape_str($query_data['where']['kwd']) . "%' ";
        }
        //제외상품
        if( isset($query_data['where']['not_pnum']) && !empty($query_data['where']['not_pnum']) ) {
            if( is_array($query_data['where']['not_pnum']) ) {
                $not_pnum = implode(',', $query_data['where']['not_pnum']);
            }
            else {
                $not_pnum = $query_data['where']['not_pnum'];
            }

            if($not_pnum) {
                $where_query .= " AND ct.p_num NOT IN ({$not_pnum}) ";
            }
        }
        //재고확인
        if( isset($query_data['where']['p_stock_state']) && !empty($query_data['where']['p_stock_state']) ) {
            $where_query .= "and pt.p_stock_state = '" . $this->db->escape_str($query_data['where']['p_stock_state']) . "' ";
        }

        //order by 절
        if( isset($query_data['orderby']) && !empty($query_data['orderby']) ) {
            $order_query = "order by " . $query_data['orderby'] . " ";
        }
        else {
            $order_query = "order by ct.p_order_code ,  ct.reg_date desc ";
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

            return $this->db->query($query)->row_array('cnt');
        }
        //데이터 추출
        else {
            $query = "select * ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;

            return $this->db->query($query)->result_array();
        }
    }//end of get_wish_list()


    public function overlapCart($arrayParams){

        if(
                empty($arrayParams['p_order_code']) == true
            ||  empty($arrayParams['option_name']) == true
            ||  empty($arrayParams['m_num']) == true
        ){
            return false;
        }

        $sql        = "SELECT 
                       * 
                       FROM cart_tb 
                       WHERE p_order_code = '{$arrayParams['p_order_code']}' 
                       AND option_name = '{$arrayParams['option_name']}'
                       AND m_num = '{$arrayParams['m_num']}' 
       ";
        $aResult    = $this->db->query($sql)->row_array();
        return $aResult;

    }


    public function get_cart_row($arrayParams){

        $whereQueryString = "";

        if(empty($arrayParams['cart_id']) == false) $whereQueryString .= " AND cart_id = '{$arrayParams['cart_id']}' ";

        $sql = "SELECT * FROM cart_tb WHERE 1 {$whereQueryString} ";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        return $aResult;

    }

    public function clearCartData(){

//        $where_query = "where p_display_state = 'Y' ";
//        $where_query .= "and p_sale_state = 'Y' ";

        $sql = "    SELECT 
                        ct.*
                    ,   pt.p_display_state
                    ,   pt.p_stock_state
                    ,   pt.p_sale_state
                    FROM cart_tb ct 
                    INNER JOIN product_tb pt ON ct.p_order_code = pt.p_order_code 
                    WHERE ct.m_num = '{$this->m_num}'
                    GROUP BY ct.p_num
        ";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        foreach ($aResult as $r) {
            if($r['p_display_state'] == 'N'){
                $sql = "DELETE FROM cart_tb WHERE p_num = '{$r['p_num']}' AND m_num = '{$this->m_num}' ";
                $this->db->query($sql);
            }
        }

    }

    public function delete_cart($cart_id){
        $sql = "DELETE FROM cart_tb WHERE cart_id = '{$cart_id}' ";
        $this->db->query($sql);
    }
}//end of class Cart_model