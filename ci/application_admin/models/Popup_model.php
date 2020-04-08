<?php
/**
 * 팝업 관련 모델
 */
class Popup_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 팝업 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_popup_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from popup_tb ";
        $from_query .= "join adminuser_tb on au_num = pu_adminuser_num ";

        //where 절
        $where_query = "where 1 = 1 ";
        //게시판구분
        if( isset($query_array['where']['div']) && !empty($query_array['where']['div']) ) {
            $where_query .= "and pu_division = '" . $this->db->escape_str($query_array['where']['div']) . "' ";
        }
        //노출여부
        if( isset($query_array['where']['usestate']) && !empty($query_array['where']['usestate']) ) {
            $where_query .= "and pu_usestate = '" . $this->db->escape_str($query_array['where']['usestate']) . "' ";
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
            $order_query = "order by pu_num desc ";
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
    }//end of get_popup_list()

    /**
     * 팝업 조회
     * @param $pu_num
     * @return mixed
     */
    public function get_popup_row($pu_num) {
        return $this->db->where('pu_num', $pu_num)->get('popup_tb')->row();
    }//end of get_popup_row()

    /**
     * 팝업 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_popup($query_data=array()) {
        if(
            !isset($query_data['pu_division']) || empty($query_data['pu_division']) ||
            !isset($query_data['pu_content']) || empty($query_data['pu_content']) ||
            !isset($query_data['pu_usestate']) || empty($query_data['pu_usestate'])
        ) {
            return false;
        }

        if( !isset($query_data['pu_termlimit_yn']) || empty($query_data['pu_termlimit_yn']) ) {
            $query_data['pu_termlimit_yn'] = 'N';
        }

        if( isset($query_data['pu_termlimit_datetime1']) && !empty($query_data['pu_termlimit_datetime1']) ) {
            $query_data['pu_termlimit_datetime1'] = number_only($query_data['pu_termlimit_datetime1']) . "000000";
        }
        if( isset($query_data['pu_termlimit_datetime2']) && !empty($query_data['pu_termlimit_datetime2']) ) {
            $query_data['pu_termlimit_datetime2'] = number_only($query_data['pu_termlimit_datetime2']) . "235959";
        }

        $query_data['pu_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['pu_regdatetime'] = current_datetime();

        return $this->db->insert("popup_tb", $query_data);
    }//end of insert_popup()

    /**
     * 팝업 수정
     * @param $pu_num
     * @param array $query_data
     * @return bool
     */
    public function update_popup($pu_num, $query_data=array()) {
        if( empty($pu_num) ) {
            return false;
        }

        if( !isset($query_data['pu_termlimit_yn']) || empty($query_data['pu_termlimit_yn']) ) {
            $query_data['pu_termlimit_yn'] = 'N';
        }

        if( isset($query_data['pu_termlimit_datetime1']) && !empty($query_data['pu_termlimit_datetime1']) ) {
            $query_data['pu_termlimit_datetime1'] = number_only($query_data['pu_termlimit_datetime1']) . "000000";
        }
        if( isset($query_data['pu_termlimit_datetime2']) && !empty($query_data['pu_termlimit_datetime2']) ) {
            $query_data['pu_termlimit_datetime2'] = number_only($query_data['pu_termlimit_datetime2']) . "235959";
        }

        $query_data['pu_adminuser_num'] = $_SESSION['session_au_num'];

        return $this->db->where('pu_num', $pu_num)->update("popup_tb", $query_data);
    }//end of update_popup()

    /**
     * 팝업 삭제
     * @param $pu_num
     */
    public function delete_popup($pu_num) {
        return $this->db->where('pu_num', $pu_num)->delete('popup_tb');
    }//end of delete_popup()

}//end of class Popup_model