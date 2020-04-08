<?php
/**
 * 이벤트 기프티콘 관련 모델
 */
class Event_gift_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 이벤트 기프티콘 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_event_gift_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from event_gift_tb ";
        $from_query .= "left join event_tb on e_num = eg_event_num ";
        $from_query .= "left join member_tb on m_num = eg_member_num ";

        //where 절
        $where_query = "where 1 = 1 ";
        //이벤트
        if( isset($query_array['where']['event_num']) && !empty($query_array['where']['event_num']) ) {
            $where_query .= "and eg_event_num = '" . $this->db->escape_str($query_array['where']['eg_event_num']) . "' ";
        }
        //이벤트년월
        if( isset($query_array['where']['event_ym']) && !empty($query_array['where']['event_ym']) ) {
            $where_query .= "and eg_event_ym = '" . $this->db->escape_str($query_array['where']['event_ym']) . "' ";
        }
        //상태
        if( isset($query_array['where']['state']) && !empty($query_array['where']['state']) ) {
            $where_query .= "and eg_state = '" . $this->db->escape_str($query_array['where']['state']) . "' ";
        }
        //이벤트seq
        if( isset($query_array['where']['eg_event_num']) && !empty($query_array['where']['eg_event_num']) ) {
            $where_query .= "and eg_event_num = '" . $this->db->escape_str($query_array['where']['eg_event_num']) . "' ";
        }
        //키워드
        if($query_array['where']['kfd'] == 'eg_event_ph'){

            $where_query .= "and {$query_array['where']['kfd']} like '%{$query_array['where']['kwd']}%' ";


        }else if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            //$where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
            $where_query .= "and ( ";
            $where_query .= "   eg_gift like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
            $where_query .= "   or m_loginid like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
            $where_query .= ") ";
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = "order by eg_num desc ";
        }

        //group by 절
        if( isset($query_array['groupby']) && !empty($query_array['groupby']) ) {
            $order_query = "group by " . $query_array['groupby'] . " ";
        }

        //limit 절
        $limit_query = "";
        if( $start !== "" && $end !== "" ) {
            $limit_query .= "limit " . $start . ", " . $end . " ";
        }


        //갯수만 추출
        if( $is_count === true ) {
            $query = "select count(*) cnt ";
            $query .= $from_query;
            $query .= $where_query;

            return $this->db->query($query)->row('cnt');
        }
        //데이터 추출
        else {
            $query = "select event_gift_tb.*, e_subject, m_loginid , e_code , (select gift_name from event_gift_code_tb a where eg_event_gift =  a.gift_code) as gift_name ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;
            //echo $query;
            //zsView($query);

            return $this->db->query($query)->result();
        }
    }//end of get_event_gift_list()

    /**
     * 이벤트 기프티콘 조회
     * @param $e_num
     * @return mixed
     */
    public function get_event_gift_row($query_data=array()) {
        if( 
            !isset($query_data['eg_num']) && empty($query_data['eg_num']) &&
            !isset($query_data['eg_gift']) && empty($query_data['eg_gift'])
        ) {
            return false;
        }

        $where_query = "where 1 = 1 ";
        if( isset($query_data['eg_num']) && !empty($query_data['eg_num']) ) {
            $where_query .= "and eg_num = '" . $this->db->escape_str($query_data['eg_num']) . "' ";
        }
        if( isset($query_data['eg_gift']) && !empty($query_data['eg_gift']) ) {
            $where_query .= "and eg_gift = '" . $this->db->escape_str($query_data['eg_gift']) . "' ";
        }
        if( isset($query_data['not_eg_num']) && !empty($query_data['not_eg_num']) ) {
            $where_query .= "and eg_num != '" . $this->db->escape_str($query_data['not_eg_num']) . "' ";
        }
        if( isset($query_data['not_eg_gift']) && !empty($query_data['not_eg_gift']) ) {
            $where_query .= "and eg_gift != '" . $this->db->escape_str($query_data['not_eg_gift']) . "' ";
        }

        $query = "
            select event_gift_tb.*, e_subject, m_loginid
            from event_gift_tb
                left join event_tb on e_num = eg_event_num
                left join member_tb on m_num = eg_member_num
            " . $where_query . "
        ";

        return $this->db->query($query)->row();
    }//end of get_event_gift_row()

    /**
     * 이벤트 기프티콘 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_event_gift($query_data=array()) {
        if(
            !isset($query_data['eg_event_num']) || empty($query_data['eg_event_num']) ||
            !isset($query_data['eg_gift']) || empty($query_data['eg_gift']) ||
            !isset($query_data['eg_gift_file']) || empty($query_data['eg_gift_file'])
        ) {
            return false;
        }

        $query_data['eg_regdatetime'] = current_datetime();
        $query_data['eg_state'] = "1";

        return $this->db->insert("event_gift_tb", $query_data);
    }//end of insert_event_gift()

    /**
     * 이벤트 기프티콘 수정
     * @param $e_num
     * @param array $query_data
     * @param bool $strict
     * @return bool
     */
    public function update_event_gift($eg_num, $query_data=array()) {
        if(
            empty($eg_num) ||
            !isset($query_data['eg_event_num']) || empty($query_data['eg_event_num'])
        ) {
            return false;
        }

        return $this->db->where('eg_num', $eg_num)->update("event_gift_tb", $query_data);
    }//end of update_event_gift()

    /**
     * 이벤트 기프티콘 삭제
     * @param $e_num
     */
    public function delete_event_gift($eg_num) {
        return $this->db->where('eg_num', $eg_num)->delete('event_gift_tb');
    }//end of delete_event_gift()

}//end of class event_gift_model