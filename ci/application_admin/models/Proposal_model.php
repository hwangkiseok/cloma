<?php
/**
 * 배너 관련 모델
 */
class Proposal_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 배너 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_proposal_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from proposal_file_tb ";

        //where 절
        $where_query = "where 1 = 1 ";

        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = "order by pf_num desc ";
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

            return $this->db->query($query)->row_array();
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
    }//end of get_banner_list()

    /**
     * 배너 조회
     * @param $bn_num
     * @return mixed
     */
    public function get_proposal_row($pf_num) {
        return $this->db->where('pf_num', $pf_num)->get('proposal_file_tb')->row_array();
    }//end of get_banner_row()

    /**
     * 배너 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_proposal($query_data=array()) {
        if( !isset($query_data['pf_name']) || empty($query_data['pf_name']) ) {
            return false;
        }

        $query_data['reg_date'] = current_datetime();
        $query_data['pf_adminuser_num'] = $_SESSION['session_au_num'];

        return $this->db->insert("proposal_file_tb", $query_data);
    }//end of insert_banner()

    /**
     * 배너 수정
     * @param $bn_num
     * @param array $query_data
     * @return bool
     */
    public function update_proposal($pf_num, $query_data=array()) {
        if( empty($pf_num) ) {
            return false;
        }

        if( isset($query_data['mod_date']) && !empty($query_data['mod_date']) ) {
            $query_data['mod_date'] = current_datetime();
        }

        $query_data['pf_adminuser_num'] = $_SESSION['session_au_num'];

        return $this->db->where('pf_num', $pf_num)->update("proposal_file_tb", $query_data);
    }//end of update_banner()

    /**
     * 배너 삭제
     * @param $bn_num
     */
    public function delete_proposal($pf_num) {
        return $this->db->where('pf_num', $pf_num)->delete('proposal_file_tb');
    }//end of delete_banner()

}//end of class banner_model