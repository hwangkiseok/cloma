<?php
/**
 * 댓글 관련 모델
 */
class offer_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_offer_list($query_array=array(), $start="", $end="", $is_count=false) {

        //from 절
        $from_query = " from offer_tb ";

        //where 절
        $where_query = "where 1 = 1 ";

        //날짜검색1
        if( isset($query_array['where']['date1']) && !empty($query_array['where']['date1']) ) {
            $where_query .= "and offer_tb.reg_date >= '" . number_only($this->db->escape_str($query_array['where']['date1'])) . "000000' ";
        }
        //날짜검색2
        if( isset($query_array['where']['date2']) && !empty($query_array['where']['date2']) ) {
            $where_query .= "and offer_tb.reg_date <= '" . number_only($this->db->escape_str($query_array['where']['date2'])) . "235959' ";
        }

        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            if( $query_array['where']['kfd'] == 'all' ) {

                $where_query .= " AND ( ";
                $where_query .= " offer_tb.user_name like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= " OR offer_tb.user_hp like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= " OR offer_tb.user_email like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= " OR offer_tb.content like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= " ) ";

            }  else {

                $where_query .= " and {$query_array['where']['kfd']} like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%'  ";

            }
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        } else {
            $order_query = "order by offer_tb.reg_date desc ";
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
              *
              , (SELECT m_nickname FROM member_tb WHERE member_tb.m_num = offer_tb.m_num) AS m_nickname
            {$from_query}
            {$where_query}
            {$order_query}
            {$limit_query}
            ";

            return $this->db->query($query)->result_array();
        }
    }//end of get_offer_list()

    /**
     * 문의 조회
     * @param $cmt_num
     * @return mixed
     */
    public function get_offer_row($seq) {
        $query = "select offer_tb.*, member_tb.m_nickname ";
        $query .= "from offer_tb ";
        $query .= "left join member_tb on member_tb.m_num = offer_tb.m_num ";
        $query .= "where seq = '" . $this->db->escape_str($seq) . "' ";
        $query .= "limit 1 ";
        return $this->db->query($query)->row_array();
    }//end of get_offer_row()

    /**
     * 문의 수정
     * @param $seq
     * @param array $query_data
     * @return bool
     */
    public function update_offer ($seq, $query_data=array()) {
        if( empty($seq) ) {
            return false;
        }

        return $this->db->where('seq', $seq)->update("offer_tb", $query_data);
    }//end of update_comment()

    /**
     * 문의 삭제
     * @param $seq
     */
    public function delete_offer ($seq) {
        return $this->db->where('seq', $seq)->delete('offer_tb');
    }//end of delete_offer()

}//end of class offer_model