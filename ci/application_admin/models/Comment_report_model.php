<?php
/**
 * 댓글신고 관련 모델
 */
class Comment_report_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 댓글신고 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_comment_report_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from comment_report_tb CR ";
        $from_query .= "join comment_tb C on cmt_num = rp_comment_num ";
        $from_query .= "left join member_tb CM on CM.m_num = cmt_member_num ";
        $from_query .= "left join member_tb RM on RM.m_num = rp_member_num ";

        //where 절
        $where_query = "where 1 = 1 ";
        //댓글
        if( isset($query_array['where']['cmt_num']) && !empty($query_array['where']['cmt_num']) ) {
            $where_query .= "and rp_comment_num = '" . $this->db->escape_str($query_array['where']['cmt_num']) . "' ";
        }
        //회원
        if( isset($query_array['where']['m_num']) && !empty($query_array['where']['m_num']) ) {
            $where_query .= "and rp_member_num = '" . $this->db->escape_str($query_array['where']['m_num']) . "' ";
        }
        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            if( $query_array['where']['kfd'] == 'all' ) {
                $where_query .= "and (";
                $where_query .= "rp_reason like '%" . $query_array['where']['kwd'] . "%' ";
                $where_query .= "or cmt_content like '%" . $query_array['where']['kwd'] . "%' ";
                $where_query .= "or CM.m_nickname like '%" . $query_array['where']['kwd'] . "%' ";
                $where_query .= "or RM.m_nickname like '%" . $query_array['where']['kwd'] . "%' ";
                $where_query .= ") ";
            }
            else {
                $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
            }
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = "order by rp_num desc ";
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

            return $this->db->query($query)->row('cnt');
        }
        //데이터 추출
        else {
            $query = "select CR.*, C.*, CM.m_num comment_m_num, CM.m_nickname comment_m_nickname, RM.m_num report_m_num, RM.m_nickname report_m_nickname ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;

            //echo $query;

            return $this->db->query($query)->result();
        }
    }//end of get_comment_report_list()

    /**
     *댓글신고 조회
     * @param $rp_num
     * @return mixed
     */
    public function get_comment_report_row($rp_num) {
        $query = "select CR.*, C.*, CM.m_nickname comment_m_nickname, RM.m_nickname report_m_nickname ";
        $query .= "from comment_report_tb CR ";
        $query .= "join comment_tb C on cmt_num = rp_comment_num ";
        $query .= "left join member_tb CM on m_num = cmt_member_num ";
        $query .= "left join member_tb RM on m_num = rp_member_num ";
        $query .= "left join member_tb on m_num = rp_member_num ";
        $query .= "where rp_num = '" . $this->db->escape_str($rp_num) . "' ";
        $query .= "limit 1 ";
        return $this->db->query($query)->row();
    }//end of get_report_row()

    /**
     *댓글신고 삭제
     * @param $rp_num
     */
    public function delete_comment_report ($rp_num) {
        return $this->db->where('rp_num', $rp_num)->delete('comment_report_tb');
    }//end of delete_comment_report()

}//end of class Comment_report_model