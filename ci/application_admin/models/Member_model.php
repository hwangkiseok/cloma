<?php
/**
 * 회원 관련 모델
 */
class Member_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 회원 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_member_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from member_tb ";

        //where 절
        $where_query = "where 1 = 1 ";
        //상태
        if( isset($query_array['where']['state']) && !empty($query_array['where']['state']) ) {
            //임시회원
            if( $query_array['where']['state'] == "99" ) {
                $where_query .= "and m_state = '1' and m_sns_id = '' ";
            }
            //정상회원
            else if( $query_array['where']['state'] == "1" ) {
                $where_query .= "and m_state = '1' and m_sns_id != '' ";
            }
            else {
                $where_query .= "and m_state = '" . $this->db->escape_str($query_array['where']['state']) . "' ";
            }
        }
        //가입경로
        if( isset($query_array['where']['j_path']) && !empty($query_array['where']['j_path']) ) {
            $where_query .= "and m_join_path = '" . $this->db->escape_str($query_array['where']['j_path']) . "' ";
        }
        //휴대폰
        if( isset($query_array['where']['ph_yn']) && !empty($query_array['where']['ph_yn']) ) {
            if( $query_array['where']['ph_yn'] == 'Y' ) {
                $where_query .= "and (m_authno <> '' OR m_order_phone <> '') ";
            }
            else if( $query_array['where']['ph_yn'] == 'N' ) {
                $where_query .= "and (m_authno = '' AND m_order_phone = '') ";
            }
        }
        //재가입
        if( isset($query_array['where']['rejoin_yn']) && !empty($query_array['where']['rejoin_yn']) ) {
            $where_query .= "and m_rejoin_yn = '" . $this->db->escape_str($query_array['where']['rejoin_yn']) . "' ";
        }
        //관리자 회원
        if( isset($query_array['where']['admin_yn']) && !empty($query_array['where']['admin_yn']) ) {
            $where_query .= "and m_admin_yn = '" . $this->db->escape_str($query_array['where']['admin_yn']) . "' ";
        }
        //날짜
        if( isset($query_array['where']['date_type']) && !empty($query_array['where']['date_type']) ) {
            if( isset($query_array['where']['date1']) && !empty($query_array['where']['date1']) ) {
                $where_query .= "and left(" . $query_array['where']['date_type'] . ", 8) >= '" . number_only($this->db->escape_str($query_array['where']['date1'])) . "' ";
            }
            if( isset($query_array['where']['date2']) && !empty($query_array['where']['date2']) ) {
                $where_query .= "and left(" . $query_array['where']['date_type'] . ", 8) <= '" . number_only($this->db->escape_str($query_array['where']['date2'])) . "' ";
            }
        }
        //검색구분, 검색어
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            //아이디/이메일/닉네임 검색일때
            if( $query_array['where']['kfd'] == "loginid" ) {
                $where_query .= "and ( ";
                $where_query .= " m_nickname like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= " or m_email like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= ") ";
            }
            //휴대폰번호 검색일때
            else if($query_array['where']['kfd'] == 'm_authno'){
                $hp_1 = number_only($this->db->escape_str($query_array['where']['kwd']));
                $hp_2 = get_hp_hyphen($this->db->escape_str($query_array['where']['kwd']));
                $where_query .= " and ( m_authno in ('" . $hp_1 . "', '" . $hp_2 . "') or m_order_phone in ('" . $hp_1 . "', '" . $hp_2 . "') )";
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
            $order_query = "order by m_num desc ";
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

            return $this->db->query($query)->row_array();
        }
        //데이터 추출
        else {
            $query = "select * 
            {$from_query}
            {$where_query}
            {$order_query}
            {$limit_query}";



            return $this->db->query($query)->result_array();
        }
    }//end of get_member_list()

    /**
     * 회원 조회
     * @param array $query_data (m_num | m_key | hp)
     * @return mixed
     */
    public function get_member_row($arrayParams=array()) {

        if( empty($arrayParams['m_num']) == true && empty($arrayParams['m_key']) == true ) {
            return false;
        }

        $addQueryString = "";
        if(empty($arrayParams['m_num']) == false) $addQueryString .= " AND m_num = '{$arrayParams['m_num']}' ";
        if(empty($arrayParams['m_key']) == false) $addQueryString .= " AND m_key = '{$arrayParams['m_key']}' ";
        if(empty($arrayParams['hp']) == false) $addQueryString .= " AND ( m_authno = '{$arrayParams['hp']}' OR m_order_phone = '{$arrayParams['hp']}' ) ";

        $sql = "SELECT 
                * 
                FROM member_tb
                WHERE 1
                {$addQueryString}
        ";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        return $aResult;

    }//end of get_member_row()

    /**
     * 회원 수정
     * @param $m_num
     * @param array $query_data
     * @return bool
     */
    public function update_member($m_num, $query_data=array()) {
        if( empty($m_num) ) {
            return false;
        }

        return $this->db->where('m_num', $m_num)->update("member_tb", $query_data);
    }//end of update_member()


    /**
     * 회원 태그 통계 데이터
     * @param array $query_data
     */
    public function member_tag_stat($query_data=array()) {
        //select m_tag, count(*) cnt from member_tb where left(m_regdatetime,8) >= '20180809' and left(m_regdatetime,8) <= '20180810' group by m_tag order by cnt desc;

        $add_where_sql = "";
        if(
            isset($query_data['date_type']) && !empty($query_data['date_type'])
            && isset($query_data['date1']) && !empty($query_data['date1'])
            && isset($query_data['date2']) && !empty($query_data['date2'])
        ) {
            $add_where_sql .= " and left(" . $query_data['date_type'] . ",8) >= '" . number_only($query_data['date1']) . "' ";
            $add_where_sql .= " and left(" . $query_data['date_type'] . ",8) <= '" . number_only($query_data['date2']) . "' ";
        }

        if( empty($add_where_sql) ) {
            return false;
        }

        $sql = "
            select m_tag, count(*) cnt
            from member_tb
            where 1
            " . $add_where_sql . "
            group by m_tag
            order by cnt desc, m_tag asc
        ";
        //echo $sql;

        return $this->db->query($sql)->result_array();
    }//end of member_tag_stat()

    /**
     * 관리자 회원 목록 추출
     */
    public function get_admin_member_list($select_fd="") {
        $select_fd = (isset($select_fd) && !empty($select_fd)) ? $select_fd : " * ";

        $sql = "
            select " . $select_fd . "
            from admin_member_tb
            join member_tb on m_num = adm_member_num
        ";

        return $this->db->query($sql)->result_array();
    }//end get_admin_member_list;

    /**
     * 관리자 회원 추가
     * @param $m_num
     */
    public function admin_member_insert($m_num) {
        $sql = "select * from admin_member_tb where adm_member_num = '" . $this->db->escape_str($m_num) . "'";
        $adm_row = $this->db->query($sql)->row_array();

        //이미 있으면 true 리턴
        if( !empty($adm_row) ) {
            return true;
        }

        $sql = "
            insert into admin_member_tb
            set
                adm_member_num = '" . $this->db->escape_str($m_num) . "'
        ";

        return $this->db->query($sql);
    }//end admin_member_insert;

    /**
     * 관리자 회원 삭제
     * @param $m_num
     */
    public function admin_member_delete($m_num) {
        $sql = "
            delete from admin_member_tb
            where adm_member_num = '" . $this->db->escape_str($m_num) . "'
        ";
        return $this->db->query($sql);
    }//end admin_member_delete;

}//end of class Member_model