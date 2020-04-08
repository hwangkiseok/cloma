<?php
/**
 * 공통관리 관련 모델
 */
class Common_model extends M_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 공통코드 정보 추출
     * @param $cm_num
     * @return mixed
     */
    public function get_common_row($cm_num) {
        if( empty($cm_num) ) {
            return false;
        }

        $where_array = array();
        $where_array['cm_num'] = $cm_num;
        $where_array['cm_usestate'] = 'Y';

        return $this->db->where('cm_num', $cm_num)->get('common_tb')->row_array();
    }//end of get_common_row()

    /**
     * 해당구분의 공통코드 정보 추출
     * @param $cm_code
     * @return mixed
     */
    public function get_common_code_row($cm_code) {
        if( empty($cm_code) ) {
            return false;
        }

        $where_array = array();
        $where_array['cm_code'] = $cm_code;
        $where_array['cm_usestate'] = 'Y';

        return $this->db->where($where_array)->order_by('cm_datetime', 'desc')->limit(1)->get('common_tb')->row_array();
    }//end of get_common_row()

}//end of class Common_model