<?php
/**
 * 회원 관련 모델
 */
class Member_model extends M_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

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

//        if(
//            !isset($query_data['m_sns_site']) || empty($query_data['m_sns_site']) ||
//            !isset($query_data['m_sns_id']) || empty($query_data['m_sns_id'])
//        ) {
//            return false;
//        }

        if(empty($query_data['m_login_pw']) == false){
            $login_pw = $query_data['m_login_pw'];
            $this->db->set("m_login_pw", "password('" . $login_pw . "')", FALSE);
            unset($query_data['m_login_pw']);
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

        if( $this->db->insert('member_tb', $query_data) ) {
            $m_num = $this->db->insert_id();
            $member_row = $this->get_member_row(array('m_num' => $m_num));

            $profile_ext['m_num']       = $m_num;
            $profile_ext['reg_date']    = current_date();

            $this->db->insert('member_ext_tb', $profile_ext);

            total_stat("join_total");

            return array('code' => get_status_code('success'), 'message' => '', 'data' => $member_row);
        }
        else {

            return array('code' => get_status_code('error'), 'message' => "회원 가입에 실패했습니다.\n확인 후 다시 시도해 주세요.", 'data' => array());
        }
    }//end of insert_member()

    public function set_user_activate($m_num){

        $sql  ="UPDATE member_tb SET m_state = '1' WHERE m_num = '{$m_num}' ";
        log_message('A',$sql);
        if( $bRet = $this->db->query($sql) ) {
            return $bRet;
        }
        else {
            return false;
        }
    }

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


    public function withdraw_member($m_num , $draw_data = array()){

        if( empty($m_num) ) {
            return false;
        }

        if( $this->publicInsert('member_withdraw_log_tb',$draw_data) == true ){ //로그 기록 성공

            $sql = "DELETE FROM member_tb WHERE m_num = '{$m_num}';  ";
            $bRet = $this->db->query($sql);

            return array('success' => $bRet , 'msg' => '');

        } else {

            return array('success' => false , 'msg' => '');

        }

    }


    /**
     * 로그인 체크
     * @param $id
     * @param $pw
     */
    public function get_user_login($id, $pw) {

        $query = "select * ";
        $query .= "from member_tb ";
        $query .= "where m_login_id = '" . $this->db->escape_str($id) . "' ";
        $query .= "and m_login_pw = password('" . $this->db->escape_str($pw) . "') ";

        return $this->db->query($query)->row_array();
    }//end of get_adminuser_login()


}//end of class Member_model