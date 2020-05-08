<?php
/**
 * 회원 관련 모델
 */
class Member_model extends W_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()


    public function get_member_row_app($arrayParams){

        if(empty($arrayParams['m_num']) == true || empty($arrayParams['m_key']) == true) return false;

        $addQueryString = "";
        $addQueryString .= " AND m_num = '{$arrayParams['m_num']}' ";
        $addQueryString .= " AND m_key = '{$arrayParams['m_key']}' ";

        $sql = "SELECT 
                * 
                FROM member_tb
                WHERE 1
                {$addQueryString}
        ";

        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();
        return $aResult;

    }
    /**
     * 회원 조회
     * @param array $query_data (m_num | m_key | hp)
     * @return mixed
     */
    public function get_member_row($arrayParams=array()) {

        $addQueryString = "";

        if(empty($arrayParams['m_num']) == false) $addQueryString .= " AND m_num = '{$arrayParams['m_num']}' ";
        if(empty($arrayParams['m_key']) == false) $addQueryString .= " AND m_key = '{$arrayParams['m_key']}' ";
        if(empty($arrayParams['hp']) == false) $addQueryString .= " AND ( m_authno = '{$arrayParams['hp']}' OR m_order_phone = '{$arrayParams['hp']}' ) ";
        if(empty($arrayParams['m_sns_site']) == false) $addQueryString .= " AND m_sns_site = '{$arrayParams['m_sns_site']}' ";
        if(empty($arrayParams['m_sns_id']) == false) $addQueryString .= " AND m_sns_id = '{$arrayParams['m_sns_id']}' ";
        if(empty($arrayParams['m_nickname']) == false) $addQueryString .= " AND m_nickname = '{$arrayParams['m_nickname']}' ";
        if(empty($arrayParams['m_state']) == false) $addQueryString .= " AND m_state = '{$arrayParams['m_state']}' ";

        if(empty($addQueryString) == true ) return false;

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
     * SNS 로그인 체크
     * @param $m_sns_site
     * @param $m_sns_id
     * @return bool
     */
    public function get_login_sns($m_sns_site, $m_sns_id) {

        if( empty($m_sns_site) || empty($m_sns_id) ) {
            return array('code' => get_status_code('error'), 'message' => lang("site_error_empty_id"), 'data' => array());
        }

        $where_array = array();
        $where_array['m_sns_site'] = $m_sns_site;
        $where_array['m_sns_id'] = $m_sns_id;

        //회원정보
        $member_row = $this->db->where($where_array)->get('member_tb')->row_array();

        if( $member_row['m_state'] == '1' || $member_row['m_state'] == '4' ) {
            $ret = array('code' => get_status_code('success'), 'message' => '', 'data' => $member_row);
        }
        else if( $member_row['m_state'] == '2' ) {
            $ret = array('code' => get_status_code('error'), 'message' => "정책위반으로 서비스 이용이 정지되었으며,\n제재기간 동안에는 서비스를 이용하실 수 없습니다.", 'data' => $member_row);
        }
        else if( $member_row['m_state'] == '3' ) {
            $where_array = array();
            $where_array['m_num'] = $member_row['m_num'];
            $ret = array('code' => get_status_code('success'), 'message' => '', 'data' => $member_row);
        }
        else {
            $ret = array('code' => get_status_code('error'), 'message' => "회원정보가 없습니다.\n잠시 후 다시 시도해 주세요.", 'data' => array());
        }

        return $ret;

    }//end of get_login_sns()


    /**
     * 회원 추가
     * @param array $query_data
     * @return bool
     */
    public function insert_member($query_data=array() , $profile_ext = array()) {

        if(
            !isset($query_data['m_key']) || empty($query_data['m_key']) ||
            !isset($query_data['m_division']) || empty($query_data['m_division'])
        ) {
            return false;
        }

        if(
            !isset($query_data['m_sns_site']) || empty($query_data['m_sns_site']) ||
            !isset($query_data['m_sns_id']) || empty($query_data['m_sns_id'])
        ) {
            return false;
        }

        $query_data['m_regdatetime'] = current_datetime();

        if(empty($query_data['m_tag'])){
            $is_ios = false;
            if( in_array($query_data['m_device_model'], array("iPad", "iPhone", "iPod touch")) ) {
                $is_ios = true;
            }

            if( is_app_1() || $is_ios ) {
                $query_data['m_tag'] = 'appstore';
            }
            else {
                $query_data['m_tag'] = 'googleplay';
            }
        }

        log_message('A',json_encode($query_data,JSON_UNESCAPED_UNICODE));

        if( $this->db->insert('member_tb', $query_data) ) {

            $m_num      = $this->db->insert_id();
            $member_row = $this->get_member_row(array('m_num' => $m_num));

            if($query_data['m_sns_site'] == '1' && empty($profile_ext) == false){
                $profile_ext['m_num']       = $m_num;
                $profile_ext['reg_date']    = current_datetime();

                $this->db->insert('member_ext_tb', $profile_ext);
            }

            total_stat("join_total");

            return array('code' => get_status_code('success'), 'message' => '', 'data' => $member_row);
        }
        else {

            return array('code' => get_status_code('error'), 'message' => "회원 가입에 실패했습니다.\n확인 후 다시 시도해 주세요.", 'data' => array());
        }
    }//end of insert_member()

    public function set_user_activate($m_num){

        $sql  ="UPDATE member_tb SET m_state = '1' WHERE m_num = '{$m_num}' ";
        if( $bRet = $this->db->query($sql) ) {
            return $bRet;
        }
        else {
            return false;
        }
    }

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

        $addSetQueryString = array();
        foreach ($query_data as $k => $v) {
            $addSetQueryString[] = " `{$k}` = '{$v}' ";
        }
        $addSetQueryString = implode(',',$addSetQueryString);

        $sql = "UPDATE member_tb SET {$addSetQueryString} WHERE m_num = '{$m_num}' ";
        $bRet = $this->db->query($sql);

        return $bRet;

    }//end of update_member()

    public function get_member_view($m_num) {

        if( empty($m_num) ) {
            return false;
        }

        $where_array = array();
        $where_array['m_num'] = $m_num;
        $row = $this->db->where($where_array)->get('member_tb')->row_array();

        return $row->rctlyViewPdt;


    }//end of get_member_view()


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

    public function getBankName($bank_code)
    {
        $sns09db = $this->load->database("09sns", true);
        $query = $sns09db->get_where('nicepay_bank_code', array('bank_code' => $bank_code));
        $row = $query->row();

        return $row;
    }

    public function withdraw_member($m_num , $draw_data = array()){

        if( empty($m_num) ) {
            return false;
        }

        if( $this->publicInsert('member_withdraw_log_tb',$draw_data) == true ){ //로그 기록 성공

            $sql = "DELETE FROM member_ext_tb WHERE m_num = '{$m_num}';  ";
            $this->db->query($sql);

            $sql = "DELETE FROM member_tb WHERE m_num = '{$m_num}';  ";
            $bRet = $this->db->query($sql);

            return array('success' => $bRet , 'msg' => '');

        } else {

            return array('success' => false , 'msg' => '');

        }

    }

}//end of class Member_model