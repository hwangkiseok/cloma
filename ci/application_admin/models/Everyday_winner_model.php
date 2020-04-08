<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 매일응모 당첨 배송정보 관련 컨트롤러
 */
class Everyday_winner_model extends A_Model {

    public function __construct() {
        parent::__construct();
    }//end of __construct()

    /**
     * 매일응모 당첨 배송정보 구하기
     * @param array $query_data
     * @return bool
     */
    public function get_everyday_winner_row($query_data=array()) {
        $where_array = array();
        if( isset($query_data['edw_everyday_num']) && !empty($query_data['edw_everyday_num']) ) {
            $where_array['edw_everyday_num'] = $query_data['edw_everyday_num'];
        }
        if( isset($query_data['edw_member_num']) && !empty($query_data['edw_member_num']) ) {
            $where_array['edw_member_num'] = $query_data['edw_member_num'];
        }
        if( isset($query_data['edw_num']) && !empty($query_data['edw_num']) ) {
            $where_array['edw_num'] = $query_data['edw_num'];
        }

        if( empty($where_array) ) {
            return false;
        }

        return $this->db->where($where_array)->get("everyday_winner_tb")->row();
    }//end of get_everyday_winner_row()

    /**
     * 매일응모 당첨 배송정보 수정
     * @param $edw_num
     * @param array $query_data
     * @return bool
     */
    public function update_everyday_winner($edw_num, $query_data=array()) {
        if( empty($edw_num) || empty($query_data) ) {
            return false;
        }

        return $this->db->where("edw_num", $edw_num)->update("everyday_winner_tb", $query_data);
    }//end of update_everyday_winner()

}//end of class Everyday_winner_model