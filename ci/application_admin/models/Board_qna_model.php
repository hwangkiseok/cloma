<?php
/**
 * 1:1문의 관련 모델
 */
class Board_qna_model extends A_Model {

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
        $from_query .= "left join adminuser_tb on au_num = bq_adminuser_num ";

        //where 절
        $where_query = "where bq_display_state_2 = 'Y' ";

        if ($query_array['where']['none_answer'] == 'Y') { // 미답변/처리중 버튼 클릭시
            if($query_array['where']['ans_yn'] == 'P') {
                $where_query .= "and bq_flag = 'P' and bq_team = '" . $query_array['where']['bq_team'] . "' ";
            } else {
                $where_query .= "and bq_answer_yn = 'N' and bq_display_state_1 = 'Y' and bq_team = '" . $query_array['where']['bq_team'] . "' ";
            }

        } elseif ($query_array['where']['init_team'] == 'Y') { // 팀버튼 클릭시
            $where_query .= " and bq_team = '" . $query_array['where']['bq_team'] . "' ";
        } else {
            //회원
            if (isset($query_array['where']['m_num']) && !empty($query_array['where']['m_num'])) {
                $where_query .= "and bq_member_num = '" . $this->db->escape_str($query_array['where']['m_num']) . "' ";
            }
            //관리자
            if (isset($query_array['where']['au_num']) && !empty($query_array['where']['au_num'])) {
                $where_query .= "and bq_adminuser_num = '" . $this->db->escape_str($query_array['where']['au_num']) . "' ";
            }
            //답변자
            if (isset($query_array['where']['bq_team']) && !empty($query_array['where']['bq_team'])) {
                $where_query .= "and bq_team = '" . $this->db->escape_str($query_array['where']['bq_team']) . "' ";
            }
            //문의유형
            if (isset($query_array['where']['ans_yn']) && !empty($query_array['where']['ans_yn'])) {

                if ($query_array['where']['ans_yn'] == 'P') {
                    $where_query .= "and bq_flag = '" . $this->db->escape_str($query_array['where']['ans_yn']) . "' ";
                } else {
                    $where_query .= "and bq_answer_yn = '" . $this->db->escape_str($query_array['where']['ans_yn']) . "' ";

                    //미답변만 출력일때 고객이 삭제한 문의글은 제외함(190114/김홍주)
                    if ($query_array['where']['ans_yn'] == "N") {
                        $where_query .= "and bq_display_state_1 = 'Y' ";
                    }
                }


            }
            //문의유형
            if (isset($query_array['where']['cate']) && !empty($query_array['where']['cate'])) {
                $where_query .= "and bq_category = '" . $this->db->escape_str($query_array['where']['cate']) . "' ";
            }
            //노출여부
            if (isset($query_array['where']['usestate']) && !empty($query_array['where']['usestate'])) {
                $where_query .= "and bq_display_state_1 = '" . $this->db->escape_str($query_array['where']['usestate']) . "' ";
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

            //키워드
            if (
                isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
                isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
            ) {
                if ($query_array['where']['kfd'] == 'all') {
                    $where_query .= "and (bq_content like '%" . $query_array['where']['kwd'] . "%' or m_nickname like '%" . $query_array['where']['kwd'] . "%' or bq_member_num like '%" . $query_array['where']['kwd'] . "%') ";
                } else {
                    $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                }
            }
        }

        /*
        //미답변 갯수 (회원이 삭제한 문의는 제외함[190114/김홍주])
        if(!zsDebug()) {
            if (isset($query_array['where']['none_answer']) && !empty($query_array['where']['none_answer'])) {
                if ($query_array['where']['none_answer'] == 'P') {
                    $where_query .= "and bq_flag = 'P'";
                } else {
                    $where_query .= "and bq_answer_yn = 'N' and bq_display_state_1 = 'Y' ";
                }
            }
        }
        */

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
            $query = "select board_qna_tb.*, member_tb.m_num , member_tb.m_nickname , member_tb.m_key, member_tb.m_order_count , member_tb.m_authno , adminuser_tb.au_loginid, adminuser_tb.au_name ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;
            return $this->db->query($query)->result_array();
        }
    }//end of get_board_qna_list()

    /**
     * 게시물 조회
     * @param $bq_num
     * @return mixed
     */
    public function get_board_qna_row($bq_num) {
        $query = "select ";
        $query .= "board_qna_tb.* ";
        $query .= ", member_tb.m_num, member_tb.m_nickname, member_tb.m_regid, member_tb.m_device_model ";
        $query .= ", adminuser_tb.au_loginid, adminuser_tb.au_name ";
        $query .= "from board_qna_tb ";
        $query .= "left join member_tb on m_num = bq_member_num ";
        $query .= "left join adminuser_tb on au_num = bq_adminuser_num ";
        $query .= "where bq_num = '" . $this->db->escape_str($bq_num) . "' ";
        $query .= "limit 1 ";
        return $this->db->query($query)->row_array();
    }//end of get_board_qna_row()

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

        return $this->db->where('bq_num', $bq_num)->update("board_qna_tb", $query_data);
    }//end of update_board_qna()


    /**
     * 팀 수정
     * @param $bq_num
     * @param $bq_team
     * @return bool
     */
    public function update_team($bq_num, $bq_team) {
        if( empty($bq_num) && empty($bq_team)) {
            return false;
        }

        return $this->db->where('bq_num', $bq_num)->update("board_qna_tb", array('bq_team' => $bq_team));
    }//end of update_team()





    /**
     * 게시물 삭제
     * @param $bq_num
     */
    public function delete_board_qna($bq_num) {
        return $this->db->where('bq_num', $bq_num)->delete('board_qna_tb');
    }//end of delete_board_qna()


    /**
     * 문구 목록 추출
     */
    public function get_word_use_list() {
        //from 절
        $from_query = "from word_use_tb ";

        //where 절
        $where_query = "where wd_usestate = 'Y' and wd_use = 'board_qna' ";

        //order by 절
        $order_query = "order by wd_num asc ";

        //limit 절
        $limit_query = "limit 100";

        $query = "select * ";
        $query .= $from_query;
        $query .= $where_query;
        $query .= $order_query;
        $query .= $limit_query;

        //echo $query;
        //print_r($query_array);
        return $this->db->query($query)->result();
    }//end of get_word_use_list()

}//end of class Board_qna_model