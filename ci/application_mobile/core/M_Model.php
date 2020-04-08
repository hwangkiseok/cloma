<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 최상위 공통 모델 (CI_Model 확장).
 */
class M_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 테이블 존재 여부 체크
     * @param $tb
     * @return bool
     */
    public function table_check($tb) {
        $query = "show tables like '" . $tb . "'";
        $row = $this->db->query($query)->row();

        if( !empty($row) ) {
            return true;
        }
        else {
            return false;
        }
    }//end of table_check()

    /**
     * 테이블 생성 (스키마만)
     * @param $new_tb
     * @param $old_tb
     * @return bool
     */
    public function create_table_like($new_tb, $old_tb) {
        if( $this->table_check($new_tb) ) {
            return true;
        }

        $query = "create table " . $new_tb . " like " . $old_tb;
        return $this->db->query($query);
    }//end of create_table_like()


    /**
     * DB 연결
     * @param string $db_group
     * @return mixed
     */
    public function get_db($db_group="") {
        if( !empty($db_group) ) {
            if ( $this->load->database($db_group, true) ) {
                return $this->load->database($db_group, true);
            }
        }

        return $this->load->database("db", true);
    }//end of get_db()

    public function publicInsert($table_name, $query_data){

        if( $this->db->insert($table_name, $query_data) ) {
            return $this->db->insert_id();
        }else{
            return false;
        }

    }

    public function publicUpdate($table_name, $query_data , $seq_arr){

        if( $this->db->update($table_name, $query_data , array($seq_arr[0] => $seq_arr[1]) ) ) {
            return true;
        }else{
            return false;
        }

    }

}//end of class MY_Model