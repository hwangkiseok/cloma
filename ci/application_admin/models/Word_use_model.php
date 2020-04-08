<?php
/**
 * 자주 사용하는 문구 관련 모델
 */
class Word_use_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 자주 사용하는 문구 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_word_use_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from word_use_tb ";

        //where 절
        $where_query = "where wd_usestate = 'Y' ";

        //사용처
        if(isset($query_array['where']['wd_use']) && !empty($query_array['where']['wd_use']) ) {
            $where_query .= "and wd_use = '" . $this->db->escape_str($query_array['where']['wd_use']) . "' ";
        }

        if(isset($query_array['where']['wd_view']) && !empty($query_array['where']['wd_view']) ) {
            $where_query .= "and wd_view = '" . $this->db->escape_str($query_array['where']['wd_view']) . "' ";
        }

        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            if($query_array['where']['kfd'] == 'all') {
                $where_query .= "and (wd_subject like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' or ";
                $where_query .= " wd_content like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' )";
            } else {
                $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
            }

        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = "order by wd_num desc ";
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
            //print_r($query_array);
            return $this->db->query($query)->result();
        }
    }//end of get_word_use_list()

    /**
     * 자주 사용하는 문구 조회
     * @param array $query_data
     * @return mixed
     */
    public function get_word_use_row($query_data=array()) {
        $where_array = array();
        if( isset($query_data['wd_num']) && !empty($query_data['wd_num']) ) {
            $where_array['wd_num'] = $query_data['wd_num'];
        }

        if( empty($where_array) ) {
            return false;
        }

        return $this->db->where($where_array)->get('word_use_tb')->row();
    }//end of get_word_use_row()

    /**
     * 자주 사용하는 문구 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_word_use($query_data=array()) {
        if( !isset($query_data['wd_subject']) || empty($query_data['wd_subject']) ) {
            return false;
        }

        if( !isset($query_data['wd_content']) || empty($query_data['wd_content']) ) {
            return false;
        }

        if( !isset($_SESSION['GroupMemberNum']) || empty($_SESSION['GroupMemberNum']) ) {
            return false;
        } else {
            $query_data['wd_au_num'] = $_SESSION['GroupMemberNum'];
        }

        if( !isset($query_data['wd_use']) || empty($query_data['wd_use']) ) {
            return false;
        }

        if( $this->db->insert("word_use_tb", $query_data) ) {
            return $this->get_result(get_status_code("success"), "");
        }
        else {
            return $this->get_result(get_status_code("error"), "");
        }
    }//end of insert_word_use()


    /**
     * 자주 사용하는 문구 수정
     * @param $wd_num_num
     * @param array $query_data
     * @return bool
     */
    public function update_word_use($wd_num, $query_data=array()) {
        if( empty($wd_num) ) {
            return false;
        }

        if( !isset($query_data['wd_use']) || empty($query_data['wd_use']) ) {
            return false;
        }

        if( !isset($query_data['wd_subject']) || empty($query_data['wd_subject']) ) {
            return false;
        }

        if( !isset($query_data['wd_content']) || empty($query_data['wd_content']) ) {
            return false;
        }



        return $this->db->where('wd_num', $wd_num)->update("word_use_tb", $query_data);

    }//end of update_banner()


    /**
     * 자주 사용하는 문구 삭제
     * @param $wd_num
     */
    public function delete_word_use($wd_num) {
        $query_data['wd_usestate'] = 'N';
        return $this->db->where('wd_num', $wd_num)->update("word_use_tb", $query_data);
    }//end of delete_word_use()

}//end of class word_use_model