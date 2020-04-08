<?php
/**
 * 1:1문의 관련 모델
 */
class Board_qna_model extends M_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 게시물 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_board_qna_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from board_qna_tb ";
        $from_query .= "left join member_tb on m_num = bq_member_num ";

        //where 절
        $where_query = "where bq_display_state_1 = 'Y' ";
        //회원
        if( isset($query_array['where']['m_num']) && !empty($query_array['where']['m_num']) ) {
            $where_query .= "and bq_member_num = '" . $this->db->escape_str($query_array['where']['m_num']) . "' ";
        }
        //문의유형
        if( isset($query_array['where']['ans_yn']) && !empty($query_array['where']['ans_yn']) ) {
            $where_query .= "and bq_answer_yn = '" . $this->db->escape_str($query_array['where']['ans_yn']) . "' ";
        }
        //문의유형
        if( isset($query_array['where']['cate']) && !empty($query_array['where']['cate']) ) {
            $where_query .= "and bq_category = '" . $this->db->escape_str($query_array['where']['cate']) . "' ";
        }
        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            if( $query_array['where']['kfd'] == 'all' ) {
                $where_query .= "and (bq_content like '%" . $query_array['where']['kwd'] . "%' ) ";
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
            $order_query = "order by bq_num desc ";
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
            $query = "select board_qna_tb.*, member_tb.m_nickname ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;

            //echo $query;

            return $this->db->query($query)->result_array();
        }
    }//end of get_board_qna_list()

    /**
     * 게시물 조회
     * @param $bq_num
     * @param $bq_member_num
     * @return mixed
     */
    public function get_board_qna_row_array($bq_num, $bq_member_num) {
        $query = "select board_qna_tb.*, member_tb.m_loginid ";
        $query .= "from board_qna_tb ";
        $query .= "left join member_tb on m_num = bq_member_num ";
        $query .= "where bq_num = '" . $this->db->escape_str($bq_num) . "' ";
        $query .= "and bq_member_num = '" . $this->db->escape_str($bq_member_num) . "' ";
        $query .= "limit 1 ";
        return $this->db->query($query)->row_array();
    }//end of get_board_qna_row_array()

    /**
     * 게시물 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_board_qna($query_data=array()) {
        if(
            !isset($query_data['bq_member_num']) || empty($query_data['bq_member_num']) ||
            !isset($query_data['bq_content']) || empty($query_data['bq_content'])
        ) {
            return false;
        }

        $query_data['bq_content'] = addslashes($query_data['bq_content']);
        $query_data['bq_regdatetime'] = current_datetime();

        return $this->db->insert("board_qna_tb", $query_data);
    }//end of update_board_qna()

    /**
     * 게시물 수정
     * @param $bq_num
     * @param array $query_data
     * @return bool
     */
    public function update_board_qna($bq_num, $query_data=array()) {
        if( empty($bq_num) ) {
            return false;
        }

        $query_data['bq_content'] = addslashes($query_data['bq_content']);

        return $this->db->where('bq_num', $bq_num)->update("board_qna_tb", $query_data);
    }//end of update_board_qna()

    /**
     * 게시물 삭제
     * @param $bq_num
     */
    public function delete_board_qna($bq_num) {
        return $this->db->where('bq_num', $bq_num)->delete('board_qna_tb');
    }//end of delete_board_qna()


    /**
     * 1:1문의 중복글 체크
     * @param array $query_data
     * @return bool
     */
    public function get_overlapl_board_qna($query_data=array()) {
        if( empty($query_data) ) {
            return false;
        }
        //회원번호, 카테고리, 내용 입력 체크
        if(
            !isset($query_data['bq_member_num']) || empty($query_data['bq_member_num'])
            || !isset($query_data['bq_category']) || empty($query_data['bq_category'])
            || !isset($query_data['bq_content']) || empty($query_data['bq_content'])
        ) {
            return false;
        }

        //1분
        $check_time = date("YmdHis", time() - 60);

        $query_data['bq_content'] = addslashes($query_data['bq_content']);

        //1분이내 같은 회원, 카테고리, 내용일때 중복글임.
        $sql = "
            select count(*) cnt
            from board_qna_tb
            where
                bq_member_num = '" . $this->db->escape_str($query_data['bq_member_num']) . "'
                and bq_category = '" . $this->db->escape_str($query_data['bq_category']) . "'
                and bq_content = '" . $query_data['bq_content'] . "'
                and bq_display_state_1 = 'Y'
                and bq_regdatetime >= " . $check_time . "
        ";

        $cnt = $this->db->query($sql)->row_array('cnt');
        //db에서 체크
        if( $cnt['cnt'] > 0 ) {
            return true;
        }
        else {
            return false;
        }
    }//end get_overlapl_board_qna;

    public function getFaqList($param)
    {
        $this->db->select()
            ->from('board_help_tb')
            ->where_in('bh_num', $param);

        $query = $this->db->get();
        $rows = $query->result_array();

        return $rows;
    }

}//end of class Board_qna_model