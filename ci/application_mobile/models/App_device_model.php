<?php
/**
 * APP 단말기 관련 모델
 */
class App_device_model extends M_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * APP 단말 정보
     * @param array $query_data
     * @return mixed
     */
    public function get_app_device_row($query_data=array()) {
        $where_array = array();
        if( isset($query_data["dv_regid"]) && !empty($query_data["dv_regid"]) ) {
            $where_array["dv_regid"] = $query_data["dv_regid"];
        }
        if( isset($query_data["dv_member_num"]) && !empty($query_data["dv_member_num"]) ) {
            $where_array["dv_member_num"] = $query_data["dv_member_num"];
        }

        if( empty($where_array) ) {
            return false;
        }

        $this->db->from("app_device_tb");
        $this->db->where($where_array);
        $this->db->order_by("dv_num", "desc");
        $this->db->limit(1);
        $query = $this->db->get()->row_array();

        return $query;

    }//end of get_app_device_row()

    /**
     * APP 단말 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_app_device($query_data=array()) {
        if( !isset($query_data['dv_regid']) || empty($query_data['dv_regid']) ) {
            return false;
        }

        $query_data['dv_regdatetime'] = current_datetime();

        //중복체크
        $row = $this->get_app_device_row(array('dv_regid' => $query_data['dv_regid']));
        if( !empty($row) ) {
            $this->update_app_device($row->dv_num, $query_data);
            return true;
        }

        if( $this->db->insert("app_device_tb", $query_data) ) {
            return true;
        }
        else {
            return false;
        }
    }//end of insert_app_device()

    /**
     * APP 단말 수정
     * @param $dv_num
     * @param array $query_data
     * @return bool
     */
    public function update_app_device($dv_num, $query_data=array()) {
        if( empty($dv_num) ) {
            return false;
        }

        if( $this->db->where('dv_num', $dv_num)->update("app_device_tb", $query_data) ) {
            //푸시 DB에 수정
//            $db_push = $this->get_db("db_push");
//            $db_push->where('dv_num', $dv_num)->update("app_device_tb", $query_data);

            return true;
        }
        else {
            return false;
        }
    }//end of update_app_device()

    /**
     * APP 단말 삭제 (회원탈퇴시)
     * @param string $dv_regid
     * @param string $dv_member_num
     * @return bool
     */
    public function delete_app_device($dv_regid="", $dv_member_num="") {
        if( empty($dv_regid) && empty($dv_member_num) ) {
            return false;
        }

        $where_query = "";
        if( !empty($dv_regid) ) {
            $where_query .= " dv_regid = '" . $dv_regid . "' or ";
        }
        if( !empty($dv_member_num) ) {
            $where_query .= " dv_member_num = '" . $dv_member_num . "' or ";
        }

        $query = "delete from app_device_tb ";
        $query .= "where " . substr($where_query, 0, -3) . " ";
        if( $this->db->query($query) ) {
            //푸시 DB에 삭제
//            $db_push = $this->get_db("db_push");
//            $db_push->query($query);

            return true;
        }
        else {
            return false;
        }
    }//end of delete_app_device()

}//end of class App_device_model