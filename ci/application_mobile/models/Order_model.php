<?php
/**
 * 찜 관련 모델
 */
class order_model extends M_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_order_cancel_list($query_array=array(), $start="", $end="", $is_count=false) {

        //from 절
        $from_query  = " from snsform_order_cancel_tb A ";
        $from_query .= " LEFT JOIN snsform_order_tb B ON A.trade_no = B.trade_no ";
        $from_query .= " LEFT JOIN member_tb C ON C.m_num = B.partner_buyer_id ";

        //where 절
        $where_query = "where 1 = 1 ";

        //날짜검색1
        if( isset($query_array['where']['date1']) && !empty($query_array['where']['date1']) ) {
            $where_query .= "and {$query_array['where']['dateType']} >= '" . number_only($this->db->escape_str($query_array['where']['date1'])) . "000000' ";
        }
        //날짜검색2
        if( isset($query_array['where']['date2']) && !empty($query_array['where']['date2']) ) {
            $where_query .= "and {$query_array['where']['dateType']} <= '" . number_only($this->db->escape_str($query_array['where']['date2'])) . "235959' ";
        }


        if( isset($query_array['where']['tno']) && !empty($query_array['where']['tno']) ) {

            if(is_array($query_array['where']['tno']) == true){

                $where_query .= " AND A.trade_no IN ( '". implode("','", $query_array['where']['tno']) . "' ) " ;

            }else {
                $where_query .= " AND A.trade_no = '{$query_array['where']['tno']}' " ;

            }
        }

        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            if( $query_array['where']['kfd'] == 'all' ) {

                $where_query .= " AND ( ";
                $where_query .= " C.m_nickname like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= " OR A.account_holder like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= " ) ";

            }  else {

                $where_query .= " and {$query_array['where']['kfd']} like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%'  ";

            }
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        } else {
            $order_query = "order by A.reg_date desc ";
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
            return $this->db->query($query)->row_array('cnt');
        }
        //데이터 추출
        else {
            $query = "select 
              A.*
              ,B.item_name
              ,B.partner_buyer_id
              ,B.buyer_id
              ,B.payway_cd
              ,B.status_cd
              ,B.buy_amt
              ,B.delivery_amt
              ,B.delivery_amt
              ,B.register_date
              ,B.option_list
              ,C.m_num
              ,C.m_nickname
            {$from_query}
            {$where_query}
            {$order_query}
            {$limit_query}
            ";
            return $this->db->query($query)->result_array();
        }
    }//end of get_order_list()


//주문상태(status_cd) / 60:주문대기 61:입금확인중 62:신규주문 63:배송준비중 64:배송중 65:배송완료 66:취소관리 67:교환관리 68:반품관리
//2차주문상태(after_status_cd) /
    public function get_basket_info($m_trade_no){

        $sql = "SELECT 
                   B.*
                  ,A.item_name
                  ,A.item_no
                  ,A.partner_buyer_id
                  ,A.buyer_id
                  ,A.payway_cd
                  ,A.status_cd
                  ,A.buy_amt
                  ,A.delivery_amt
                  ,A.register_date
                  ,A.option_list
                  ,C.p_today_image
                  ,C.p_name 

                    , A.trade_no 
                    , A.receiver_zip
                    , A.receiver_name                                                                                                                                                                     
                    , A.receiver_tel                                                                                                                                                                      
                    , A.receiver_zip                                                                                                                                                                      
                    , A.receiver_addr1                                                                                                                                                                     
                    , A.receiver_addr2                                                                                                                                                                     
                    , A.aorder_memo
                    , A.order_memo
                    , A.complete_push_yn
                    , A.delivery_push_yn
                    
                FROM snsform_order_tb A
                LEFT JOIN snsform_order_cancel_tb B ON A.trade_no = B.trade_no
                LEFT JOIN product_tb C ON A.item_no = C.p_order_code  
                WHERE A.m_trade_no = '{$m_trade_no}' 
        ";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        return $aResult;

    }

    public function get_order_info($trade_no){

        if( !isset($trade_no) || empty($trade_no) ){
            return false;
        }

        $sql = "SELECT 
                    B.*
                    ,A.item_name
                    ,A.item_no
                    ,A.partner_buyer_id
                    ,A.buyer_id
                    ,A.payway_cd
                    ,A.status_cd
                    ,A.buy_amt
                    ,A.delivery_amt
                    ,A.register_date
                    ,A.option_list
                    ,A.m_trade_no
                    ,C.p_today_image
                    ,C.p_name
                    , A.trade_no
                    , A.check_date
                    , A.buyer_name                                                                                                                                                                     
                    , A.buyer_hhp
                    , A.basket_yn
                    
                     
                    , A.receiver_name                                                                                                                                                                     
                    , A.receiver_tel                                                                                                                                                                      
                    , A.receiver_zip                                                                                                                                                                      
                    , A.receiver_addr1                                                                                                                                                                     
                    , A.receiver_addr2                                                                                                                                                                     
                    , A.aorder_memo
                    , A.order_memo
                    , A.complete_push_yn
                    , A.delivery_push_yn

                FROM snsform_order_tb A
                LEFT JOIN snsform_order_cancel_tb B ON A.trade_no = B.trade_no
                LEFT JOIN product_tb C ON A.item_no = C.p_order_code  
                WHERE A.trade_no = '{$trade_no}' 
        ";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        $aResult['tot_buy_cnt'] = 0;
        $aResult['cart_del_amt'] = 0;
        if(empty($aResult['m_trade_no']) == false){
            $sql = "SELECT COUNT(*) AS cnt FROM snsform_order_tb WHERE m_trade_no = '{$aResult['m_trade_no']}' AND item_no <> '0000000000'; ";
            $oSubResult = $this->db->query($sql);
            $aSubResult = $oSubResult->row_array();
            $aResult['tot_buy_cnt'] = $aSubResult['cnt'];

            $sql = "SELECT delivery_amt FROM snsform_order_tb WHERE m_trade_no = '{$aResult['m_trade_no']}' AND item_no = '0000000000'; ";
            $oSubResult = $this->db->query($sql);
            $aSubResult = $oSubResult->row_array();
            $aResult['cart_del_amt'] = $aSubResult['delivery_amt'];
        }

        return $aResult;

    }

    public function upsert_cancel_order($query_data){

        if( !isset($query_data['trade_no']) || empty($query_data['trade_no']) ) {
            return false;
        }

        $sql = "SELECT * FROM snsform_order_cancel_tb WHERE trade_no = '{$query_data['trade_no']}';";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        if(empty($aResult) == true){
            $query_data['reg_date'] = current_datetime();
            $bRet =  $this->db->insert("snsform_order_cancel_tb", $query_data);
        }else{
            $trade_no = $query_data['trade_no'];
            unset($query_data['trade_no']);
            unset($query_data['after_status_cd']);
            $query_data['mod_date'] = current_datetime();

            $bRet =  $this->db->update("snsform_order_cancel_tb", $query_data , array('trade_no' => $trade_no));
        }


        return $bRet;


    }
    public function get_last_order(){

        $sql = "    SELECT * 
                    FROM snsform_order_tb
                    WHERE partner_buyer_id = '{$_SESSION['session_m_num']}'
                    ORDER BY register_date DESC
                    LIMIT 1;
        ";

        $aResult = $this->db->query($sql)->row_array();

        return $aResult;

    }

}//end of class Wish_model