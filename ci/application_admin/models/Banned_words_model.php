<?php
/**
 * 금칙어 관련 모델
 */
class Banned_words_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 금칙어 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_banned_words_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from banned_words_tb ";

        //where 절
        $where_query = "where 1 = 1 ";
        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            if( isset($query_array['where']['no_like']) && $query_array['where']['no_like'] == "Y" ) {
                $where_query .= "and " . $query_array['where']['kfd'] . " = '" . $this->db->escape_str($query_array['where']['kwd']) . "' ";
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
            $order_query = "order by bw_num desc ";
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
            $query = "select * ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;

            //echo $query;

            return $this->db->query($query)->result();
        }
    }//end of get_banned_words_list()

    /**
     * 금칙어 조회
     * @param array $query_data
     * @return mixed
     */
    public function get_banned_words_row($query_data=array()) {
        $where_array = array();
        if( isset($query_data['bw_num']) && !empty($query_data['bw_num']) ) {
            $where_array['bw_num'] = $query_data['bw_num'];
        }
        if( isset($query_data['bw_word']) && !empty($query_data['bw_word']) ) {
            $where_array['bw_word'] = $query_data['bw_word'];
        }

        if( empty($where_array) ) {
            return false;
        }

        return $this->db->where($where_array)->get('banned_words_tb')->row();
    }//end of get_banned_words_row()

    /**
     * 금칙어 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_banned_words($query_data=array()) {
        if( !isset($query_data['bw_word']) || empty($query_data['bw_word']) ) {
            return false;
        }

        $word_row = $this->get_banned_words_row(array('bw_word' => $query_data['bw_word']));
        if( !empty($word_row) ) {
            return $this->get_result(get_status_code("error"), "이미 등록된 금칙어입니다.");
        }

        if( $this->db->insert("banned_words_tb", $query_data) ) {
            return $this->get_result(get_status_code("success"), "");
        }
        else {
            return $this->get_result(get_status_code("error"), "");
        }
    }//end of insert_banned_words()

    /**
     * 금칙어 삭제
     * @param $bw_num
     */
    public function delete_banned_words($bw_num) {
        return $this->db->where('bw_num', $bw_num)->delete('banned_words_tb');
    }//end of delete_banned_words()

}//end of class Banned_words_model