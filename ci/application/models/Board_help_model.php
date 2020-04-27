<?php
/**
 * 게시물 관련 모델
 */
class Board_help_model extends W_Model {

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
    public function get_board_help_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from board_help_tb ";

        //where 절
        $where_query = "where 1 = 1 ";
        //게시판구분
        if( isset($query_array['where']['div']) && !empty($query_array['where']['div']) ) {
            $where_query .= "and bh_division = '" . $this->db->escape_str($query_array['where']['div']) . "' ";
        }
        //분류
        if( isset($query_array['where']['cate']) && !empty($query_array['where']['cate']) ) {
            $where_query .= "and bh_category = '" . $this->db->escape_str($query_array['where']['cate']) . "' ";
        }
        //최상단노출
        if( isset($query_array['where']['top_yn']) && !empty($query_array['where']['top_yn']) ) {
            $where_query .= "and bh_top_yn = '" . $this->db->escape_str($query_array['where']['top_yn']) . "' ";
        }
        //노출여부
        if( isset($query_array['where']['usestate']) && !empty($query_array['where']['usestate']) ) {
            $where_query .= "and bh_usestate = '" . $this->db->escape_str($query_array['where']['usestate']) . "' ";
        }
        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
        }

        //앱에서 검색 :: 전체검색
        if( isset($query_array['where']['app_search_text']) && !empty($query_array['where']['app_search_text']) ) {
            $where_query .= "and ( 
                    bh_subject like '%" . $this->db->escape_str($query_array['where']['app_search_text']) . "%' 
                OR  bh_content like '%" . $this->db->escape_str($query_array['where']['app_search_text']) . "%' 
            )
            ";
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = "order by bh_num desc ";
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
    }//end of get_board_help_list()

    /**
     * 게시물 조회
     * @param $bh_num
     * @return mixed
     */
    public function get_board_help_row($bh_num) {
        return $this->db->where('bh_num', $bh_num)->get('board_help_tb')->row_array();
    }//end of get_board_help_row()

    /**
     * 게시물 조회 (최근 1건)
     * @param array $query_data
     * @return mixed
     */
    public function get_board_help_last_row($query_data=array()) {
        $where_array = array();
        if( isset($query_data['bh_division']) && !empty($query_data['bh_division']) ) {
            $where_array['bh_division'] = $query_data['bh_division'];
        }
        if( isset($query_data['bh_top_yn']) && !empty($query_data['bh_top_yn']) ) {
            $where_array['bh_top_yn'] = $query_data['bh_top_yn'];
        }
        if( isset($query_data['bh_usestate']) && !empty($query_data['bh_usestate']) ) {
            $where_array['bh_usestate'] = $query_data['bh_usestate'];
        }

        if( empty($where_array) ) {
            return false;
        }

        return $this->db->where($where_array)->order_by('bh_num', 'DESC')->get('board_help_tb', 1)->row_array();
    }//end of get_board_help_last_row()

    /**
     * 게시물 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_board_help($query_data=array()) {
        if(
            !isset($query_data['bh_division']) || empty($query_data['bh_division']) ||
            !isset($query_data['bh_subject']) || empty($query_data['bh_subject']) ||
            !isset($query_data['bh_content']) || empty($query_data['bh_content'])
        ) {
            return false;
        }

        if( !isset($query_data['bh_top_yn']) && empty($query_data['bh_top_yn']) ) {
            $query_data['bh_top_yn'] = 'N';
        }

        $query_data['bh_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['bh_regdatetime'] = current_datetime();

        return $this->db->insert("board_help_tb", $query_data);
    }//end of insert_board_help()

    /**
     * 게시물 수정
     * @param $bh_num
     * @param array $query_data
     * @return bool
     */
    public function update_board_help($bh_num, $query_data=array()) {
        if( empty($bh_num) ) {
            return false;
        }

        return $this->db->where('bh_num', $bh_num)->update("board_help_tb", $query_data);
    }//end of update_board_help()

    /**
     * 게시물 삭제
     * @param $bh_num
     */
    public function delete_board_help($bh_num) {
        return $this->db->where('bh_num', $bh_num)->delete('board_help_tb');
    }//end of delete_board_help()

}//end of class Board_help_model