<?php
/**
 * 회원 관련 모델
 */
class Kakao_member_model extends A_Model
{

    public function __construct()
    {
        parent::__construct();
    }//end of __construct()

    /**
     * 회원 목록 추출
     * @param array $query_array : 쿼리배열
     * @param string $start : limit $start, $end
     * @param string $end : limit $start, $end
     * @param bool $is_count : 전체갯수만 추출여부
     */
    public function get_kakao_member_list($query_array = array(), $start = "", $end = "", $is_count = false)
    {
        //from 절
        $from_query = "from kakao_friend_tb ";

        //where 절
        $where_query = "where 1 = 1 ";
        //상태
        if (isset($query_array['where']['state']) && !empty($query_array['where']['state'])) {
            $where_query .= "and friend_flag = '" . $this->db->escape_str($query_array['where']['state']) . "' ";
        }
        //날짜
        if (isset($query_array['where']['date_type']) && !empty($query_array['where']['date_type'])) {
            if (isset($query_array['where']['date1']) && !empty($query_array['where']['date1'])) {
                $where_query .= "and left(" . $query_array['where']['date_type'] . ", 8) >= '" . number_only($this->db->escape_str($query_array['where']['date1'])) . "' ";
            }
            if (isset($query_array['where']['date2']) && !empty($query_array['where']['date2'])) {
                $where_query .= "and left(" . $query_array['where']['date_type'] . ", 8) <= '" . number_only($this->db->escape_str($query_array['where']['date2'])) . "' ";
            }
        }
        //검색구분, 검색어
        if (
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            if ($query_array['where']['kfd'] == 'phone_number') {
                $hp_1 = number_only($this->db->escape_str($query_array['where']['kwd']));
                $where_query .= " and phone_number LIKE  '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
            } else {
                $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
            }
        }

        //order by 절
        if (isset($query_array['orderby']) && !empty($query_array['orderby'])) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        } else {
            $order_query = "order by seq desc ";
        }

        //limit 절
        $limit_query = "";
        if ($start !== "" && $end !== "") {
            $limit_query .= "limit " . $start . ", " . $end . " ";
        }

        //갯수만 추출
        if ($is_count === true) {
            $query = "select count(*) cnt ";
            $query .= $from_query;
            $query .= $where_query;

            return $this->db->query($query)->row_array();
        } //데이터 추출
        else {
            $query = "select * 
            {$from_query}
            {$where_query}
            {$order_query}
            {$limit_query}";

            return $this->db->query($query)->result_array();
        }
    }//end of get_member_list()

}