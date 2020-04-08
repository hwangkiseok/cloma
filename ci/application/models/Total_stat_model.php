<?php
/**
 * 전체통계 관련 모델
 */
class Total_stat_model extends W_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 해당 날짜의 통계 정보 추출
     * @param $date
     * @return bool
     */
    public function get_total_stat_row($date) {
        if( empty($date) ) {
            return false;
        }

        return $this->db->where("t_date", $date)->get("total_stat_tb")->row();
    }//end of get_total_stat_row()

    /**
     * 해당 날짜 통계 업데이트(없으면 추가)
     * @param $date
     * @param $fd
     * @return bool
     */
    public function update_total_stat($date, $fd) {
        if( empty($date) || empty($fd) ) {
            return false;
        }

        $field = "t_" . $fd;
        $query = "
                insert into total_stat_tb set
                  {$field} = {$field}+1
                  ,t_date = '" . $this->db->escape_str($date) . "'
                ON DUPLICATE KEY 
                UPDATE 
                  {$field} = {$field}+1 
    
            ";
        return $this->db->query($query);

    }//end of insert_total_stat()


    public function get_count($date , $field){

        $sql = "SELECT {$field} WHERE t_date = {$date}; ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();
        return $aResult[$field];

    }

}//end of class Total_stat_model