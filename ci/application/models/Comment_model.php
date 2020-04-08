<?php
/**
 * 댓글 관련 모델
 */
class Comment_model extends W_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 댓글 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_comment_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절

        $from_query = "from comment_tb ";
        $from_query .= "left join member_tb on m_num = cmt_member_num ";

        //where 절
        $where_query = "where cmt_display_state = 'Y' ";
        //자신이 쓴 글은 항상 보이게
        if ($_SESSION['session_m_num']) {
            $where_query .= "and (cmt_blind = 'N' or cmt_member_num = '" . $_SESSION['session_m_num'] . "') ";
        } else {
            $where_query .= "and cmt_blind = 'N' ";
        }

        //회원
        if( isset($query_array['where']['m_num']) && !empty($query_array['where']['m_num']) ) {
            $where_query .= "and cmt_member_num = '" . $this->db->escape_str($query_array['where']['m_num']) . "' ";
        }
        //테이블
        if( isset($query_array['where']['tb']) && !empty($query_array['where']['tb']) ) {
            $where_query .= "and cmt_table = '" . $this->db->escape_str($query_array['where']['tb']) . "' ";
        }
        //테이블 번호
        if( isset($query_array['where']['tb_num']) && !empty($query_array['where']['tb_num']) ) {
            $where_query .= "and cmt_table_num = '" . $this->db->escape_str($query_array['where']['tb_num']) . "' ";
        }
        //베스트
        if( isset($query_array['where']['best']) && $query_array['where']['best'] == "Y" ) {
            $where_query .= "and cmt_best_order != 0 ";
        }

        if( $query_array['where']['is_product']  == true ){
            $where_query .= "and cmt_parent_num = 0 ";
        }

        //order by 절
        if( isset($query_array['where']['best']) && $query_array['where']['best'] == "Y" ) {
            $order_query = "order by sort+0 asc, cmt_best_order=0 asc, cmt_best_order asc, cmt_num desc ";
        }
        else if( isset($query_array['where']['my']) && $query_array['where']['my'] == "Y"/* && zsDebug() */) {
            $order_query = "order by cmt_regdatetime desc, cmt_num asc ";
        }
        else {
            if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
                $order_query = "order by " . $query_array['orderby'] . " ";
            }
            else {
                $order_query = "order by cmt_regdatetime asc, cmt_num asc ";
            }
        }

        //limit 절
        if($query_array['where']['my'] == 'Y') {

            $ago_date = date("Ymd", strtotime("-6 month")) . "000000";
            $where_query .= " and cmt_regdatetime > '" . $ago_date . "' ";

            $from_query .= "left join product_tb on cmt_table_num = p_num and cmt_table = 'product' ";
            $from_query .= "left join event_tb on cmt_table_num = e_num and cmt_table = 'event' ";

        } else {
            $limit_query = "";
            if( $start !== "" && $end !== "" ) {
                $limit_query .= "limit " . $start . ", " . $end . " ";
            }
        }

        //갯수만 추출
        if( $is_count === true ) {
            $query = "select count(*) cnt ";
            $query .= $from_query;
            $query .= $where_query;

            return $this->db->query($query)->row_array('cnt');
        }
        //데이터 추출
        else {
            if ($query_array['where']['my'] == 'Y') {
                $addField = "
                , product_tb.p_sale_state
                , event_tb.e_proc_state
                ";
            }

            $query = "select 
                        comment_tb.*
                    ,   member_tb.m_nickname 
                    ,   member_tb.m_profile_img 
                    ,   member_tb.m_sns_profile_img
                    ,   member_tb.m_sns_nickname 
                    ,   member_tb.m_email 
                    {$addField}
                    {$from_query}
                    {$where_query}
                    {$order_query}
                    {$limit_query}
            ";

            $cmt_list = $this->db->query($query)->result_array();

            foreach( $cmt_list as $k => $r ) {
                //작성자명 설정
                $cmt_list[$k]['cmt_name'] = get_member_name(array('cmt_name' => $r['cmt_name'], 'm_nickname' => $r['m_nickname'], 'm_loginid' => $r['m_loginid'], 'm_email' => $r['m_email']));
            }//endforeach;
            return $cmt_list;
        }
    }//end of get_comment_list()

    /**
     * 댓글 조회 (노출중인 댓글만)
     * @param $cmt_num
     * @param $cmt_member_num
     * @return mixed
     */
    public function get_comment_row($cmt_num, $cmt_member_num="") {
        $query = "select comment_tb.*, member_tb.m_nickname , member_tb.m_regid ";
        $query .= "from comment_tb ";
        $query .= "left join member_tb on m_num = cmt_member_num ";
        $query .= "where cmt_num = '" . $this->db->escape_str($cmt_num) . "' ";
        $query .= "and cmt_display_state = 'Y' ";
        if( !empty($cmt_member_num) ) {
            $query .= "and cmt_member_num = '" . $this->db->escape_str($cmt_member_num) . "' ";
        }
        $query .= "limit 1 ";

        return $this->db->query($query)->row_array();
    }//end of get_comment_row()

    /**
     * 댓글 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_comment($query_data=array()) {
        if( !isset($query_data['cmt_content']) || empty($query_data['cmt_content']) ) {
            return false;
        }

        $query_data['cmt_regdatetime'] = current_datetime();

        return $this->db->insert("comment_tb", $query_data);
    }//end of update_comment()

    /**
     * 댓글 수정
     * @param $cmt_num
     * @param array $query_data
     * @return bool
     */
    public function update_comment($cmt_num, $query_data=array()) {
        if( empty($cmt_num) ) {
            return false;
        }

        return $this->db->where('cmt_num', $cmt_num)->update("comment_tb", $query_data);
    }//end of update_comment()

    /**
     * 댓글 삭제
     * @param $cmt_num
     * @param $cmt_member_num
     * @return bool
     */
    public function delete_comment($cmt_num, $cmt_member_num) {
        if( empty($cmt_num) || empty($cmt_member_num) ) {
            return false;
        }

        $where_array = array();
        $where_array['cmt_num'] = $cmt_num;
        $where_array['cmt_member_num'] = $cmt_member_num;

        return $this->db->where($where_array)->delete('comment_tb');
    }//end of delete_comment()

}//end of class Comment_model