<?php
/**
 * 댓글 관련 모델
 */
class Order_model extends A_Model {

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

                $where_query = " AND A.trade_no IN ( '". implode("','", $query_array['where']['tno']) . "' ) " ;

            }else {
                $where_query = " AND A.trade_no = '{$query_array['where']['tno']}' " ;
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

    /**
     * 문의 조회
     * @param $cmt_num
     * @return mixed
     */
    public function get_cancel_order_row($seq) {
        $query = "
                select 
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
                FROM snsform_order_cancel_tb A 
                LEFT JOIN snsform_order_tb B ON A.trade_no = B.trade_no
                LEFT JOIN member_tb C ON C.m_num = B.partner_buyer_id
                WHERE A.seq = '{$seq}';              
        ";


        return $this->db->query($query)->row_array();
    }//end of get_order_row()

    /**
     * 문의 수정
     * @param $seq
     * @param array $query_data
     * @return bool
     */
    public function update_order($seq, $query_data=array()) {
        if( empty($seq) ) {
            return false;
        }

        return $this->db->where('seq', $seq)->update("snsform_order_tb", $query_data);
    }//end of update_comment()

    /**
     * 문의 수정
     * @param $seq
     * @param array $query_data
     * @return bool
     */
    public function update_cancel_order ($seq, $query_data=array()) {
        if( empty($seq) ) {
            return false;
        }

        return $this->db->where('seq', $seq)->update("snsform_order_cancel_tb", $query_data);
    }//end of update_comment()

    /**
     * 문의 삭제
     * @param $seq
     */
    public function delete_order ($seq) {
        return $this->db->where('seq', $seq)->delete('order_tb');
    }//end of delete_order()

}//end of class order_model