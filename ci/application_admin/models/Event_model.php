<?php
/**
 * 이벤트 관련 모델
 */
class Event_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 이벤트 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_event_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from event_tb ";
        $from_query .= "join adminuser_tb on au_num = e_adminuser_num ";

        //where 절
        $where_query = "where 1 = 1 ";
        //종류
        if( isset($query_array['where']['div']) && !empty($query_array['where']['div']) ) {
            $where_query .= "and e_division = '" . $this->db->escape_str($query_array['where']['div']) . "' ";
        }
        //진행여부
        if( isset($query_array['where']['pro_state']) && !empty($query_array['where']['pro_state']) ) {
            $where_query .= "and e_proc_state = '" . $this->db->escape_str($query_array['where']['pro_state']) . "' ";
        }
        //노출여부
        if( isset($query_array['where']['dis_state']) && !empty($query_array['where']['dis_state']) ) {
            $where_query .= "and e_display_state = '" . $this->db->escape_str($query_array['where']['dis_state']) . "' ";
        }
        //이벤트코드
        if( isset($query_array['where']['e_code']) && !empty($query_array['where']['e_code']) ) {
            $where_query .= "and e_code = '" . $this->db->escape_str($query_array['where']['e_code']) . "' ";
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
            $order_query = "order by e_num desc ";
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
    }//end of get_event_list()

    /**
     * 이벤트 조회
     * @param $e_num
     * @return mixed
     */
    public function get_event_row($e_num) {
        return $this->db->where('e_num', $e_num)->get('event_tb')->row();
    }//end of get_event_row()

    /**
     * 이벤트 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_event($query_data=array()) {
        if(
            !isset($query_data['e_subject']) || empty($query_data['e_subject']) ||
            !isset($query_data['e_content']) || empty($query_data['e_content']) ||
            !isset($query_data['e_proc_state']) || empty($query_data['e_proc_state']) ||
            !isset($query_data['e_display_state']) || empty($query_data['e_display_state'])
        ) {
            return false;
        }

        if( !isset($query_data['e_termlimit_yn']) || empty($query_data['e_termlimit_yn']) ) {
            $query_data['e_termlimit_yn'] = 'N';
        }

        if( isset($query_data['e_termlimit_datetime1']) && !empty($query_data['e_termlimit_datetime1']) ) {
            $query_data['e_termlimit_datetime1'] = number_only($query_data['e_termlimit_datetime1']) . "000000";
        }
        if( isset($query_data['e_termlimit_datetime2']) && !empty($query_data['e_termlimit_datetime2']) ) {
            $query_data['e_termlimit_datetime2'] = number_only($query_data['e_termlimit_datetime2']) . "235959";
        }

        $query_data['e_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['e_regdatetime'] = current_datetime();

        return $this->db->insert("event_tb", $query_data);
    }//end of insert_event()

    /**
     * 이벤트 수정
     * @param $e_num
     * @param array $query_data
     * @param bool $strict
     * @return bool
     */
    public function update_event($e_num, $query_data=array(), $strict=true) {
        if( empty($e_num) ) {
            return false;
        }

        if( $strict === true ) {
            if(
                !isset($query_data['e_subject']) || empty($query_data['e_subject']) ||
                !isset($query_data['e_content_type']) || empty($query_data['e_content_type']) ||
                //!isset($query_data['e_content']) || empty($query_data['e_content']) ||
                !isset($query_data['e_proc_state']) || empty($query_data['e_proc_state']) ||
                !isset($query_data['e_display_state']) || empty($query_data['e_display_state'])
            ) {
                return false;
            }

            if( !isset($query_data['e_termlimit_yn']) || empty($query_data['e_termlimit_yn']) ) {
                $query_data['e_termlimit_yn'] = 'N';
            }

            if( isset($query_data['e_termlimit_datetime1']) && !empty($query_data['e_termlimit_datetime1']) ) {
                $query_data['e_termlimit_datetime1'] = number_only($query_data['e_termlimit_datetime1']) . "000000";
            }
            if( isset($query_data['e_termlimit_datetime2']) && !empty($query_data['e_termlimit_datetime2']) ) {
                $query_data['e_termlimit_datetime2'] = number_only($query_data['e_termlimit_datetime2']) . "235959";
            }
        }//end of if()

        $query_data['e_adminuser_num'] = $_SESSION['session_au_num'];

        //print_r($query_data);exit;
        return $this->db->where('e_num', $e_num)->update("event_tb", $query_data);
    }//end of update_event()

    /**
     * 이벤트 삭제
     * @param $e_num
     */
    public function delete_event($e_num) {
        return $this->db->where('e_num', $e_num)->delete('event_tb');
    }//end of delete_event()

    public function getEventGiftCodeLists($param){

        $addQueryString = '';

        if($param == 'event_20180911' || $param == 'event_20190304'){
            if(0){
                $addQueryString .= " AND gift_ym = '201903' ";
            }else{
                $addQueryString .= " AND gift_ym = DATE_FORMAT(NOW(),'%Y%m') ";
            }
        }

        $sql = "SELECT * FROM event_gift_code_tb WHERE event_code = '{$param}' {$addQueryString} ORDER BY `sort` ; ";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        return $aResult;

    }

}//end of class event_model