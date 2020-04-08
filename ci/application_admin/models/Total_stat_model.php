<?php
/**
 * 전체통계 관련 모델
 */
class Total_stat_model extends A_Model {

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
    public function get_total_stat_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from total_stat_tb ";

        //where 절
        $where_query = "where 1 = 1 ";
        //년월검색
        if(
            isset($query_array['where']['year']) && !empty($query_array['where']['year']) &&
            isset($query_array['where']['month']) && !empty($query_array['where']['month'])
        ) {
            $where_query .= "and left(t_date, 6) = '" . $this->db->escape_str($query_array['where']['year'] . $query_array['where']['month']) . "' ";
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = "order by t_date desc ";
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
    }//end of get_total_stat_list()

    /**
     * 해당 날짜의 통계 정보 추출
     * @param $date
     * @return bool
     */
    public function get_total_stat_row($date) {
        if( empty($date) ) {
            return false;
        }

        return $this->db->where("t_date", $date)->get("total_stat_tb")->row();
    }//end of get_total_stat_row()

    /**
     * 해당 날짜 통계 업데이트(없으면 추가)
     * @param $date
     * @param $fd
     * @return bool
     */
    public function update_total_stat($date, $fd) {
        if( empty($date) || empty($fd) ) {
            return false;
        }

        //해당 날짜의 통계가 있는지
        $row = $this->get_total_stat_row($date);

        $field = "t_" . $fd;

        if( !empty($row) ) {
            //수정
            $query = "update total_stat_tb set ";
            $query .= $field . " = " . $field . " + 1 ";
            $query .= "where t_date = '" . $this->db->escape_str($date) ."'  ";
            return $this->db->query($query);
        }
        else {
            //등록
            $query = "insert into total_stat_tb set ";
            $query .= "t_date = '" . $this->db->escape_str($date) ."', ";
            $query .= $field . " = " . $field . " + 1 ";
            return $this->db->query($query);
        }
    }//end of insert_total_stat()

}//end of class Total_stat_model