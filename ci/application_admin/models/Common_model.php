<?php
/**
 * 공통관리 관련 모델
 */
class Common_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 공통관리 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_common_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from common_tb ";
        $from_query .= "join adminuser_tb on au_num = cm_adminuser_num ";

        //where 절
        $where_query = "where 1 = 1 ";
        //코드
        if( isset($query_array['where']['code']) && !empty($query_array['where']['code']) ) {
            $where_query .= "and cm_code = '" . $this->db->escape_str($query_array['where']['code']) . "' ";
        }
        //상태
        if( isset($query_array['where']['usestate']) && !empty($query_array['where']['usestate']) ) {
            $where_query .= "and cm_usestate = '" . $this->db->escape_str($query_array['where']['usestate']) . "' ";
        }
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
            $order_query = "order by cm_num desc ";
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
    }//end of get_common_list()

    /**
     * 공통코드 정보 추출
     * @param $cm_num
     * @return mixed
     */
    public function get_common_row($cm_num) {
        if( empty($cm_num) ) {
            return false;
        }
        return $this->db->where('cm_num', $cm_num)->get('common_tb')->row();
    }//end of get_common_row()

    /**
     * 공통관리 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_common($query_data=array()) {
        if( !isset($query_data['cm_code']) || empty($query_data['cm_code']) ) {
            return false;
        }

        $query_data['cm_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['cm_datetime'] = current_datetime();

        return $this->db->insert('common_tb', $query_data);
    } //end of insert_common()

    /**
     * 공통관리 수정
     * @param $cm_num
     * @param array $query_data
     * @return bool
     */
    public function update_common($cm_num, $query_data=array()) {
        if( empty($cm_num) ) {
            return false;
        }

        $query_data['cm_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['cm_datetime'] = current_datetime();

        return $this->db->where('cm_num', $cm_num)->update('common_tb', $query_data);
    } //end of update_common()

    /**
     * 공통관리 삭제
     * @param $cm_num
     * @return bool
     */
    public function delete_common($cm_num) {
        if( empty($cm_num) ) {
            return false;
        }

        return $this->db->where('cm_num', $cm_num)->delete('common_tb');
    }//end of delete_common()


    /**
     * 공통관리 삭제
     * @param $cm_num
     * @return bool
     */
    public function getNewIssueCount() {


//        $dream_db = $this->get_db('dreameut');
//        $sql     = "SELECT COUNT(*) AS cnt FROM issueboard_tb WHERE left(ib_regdatetime,8) >= DATE_FORMAT(DATE_ADD(NOW() , INTERVAL -2 day ),'%Y%m%d') ";
//        $oResult = $dream_db->query($sql);
//        $aResult = $oResult->row_array();
//        return $aResult['cnt'];

    }//end of delete_common()


}//end of class Common_model