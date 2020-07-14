<?php
/**
 * APP 팝업 관리 모델
 */
class App_popup_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * APP 팝업 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_app_popup_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from app_popup_tb b ";

        //where 절
        $where_query = "where 1 = 1 ";
        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
        }


        if( $query_array['where']['notice'] == 'Y' ) {
            $where_query .= " AND apo_content_type = '3' ";
        }else{
            $where_query .= " AND apo_content_type IN ('1','2') ";
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = "order by apo_num desc ";
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

    }//end of get_app_popup_list()


    /**
     * APP 팝업 조회
     * @param $apo_num
     * @return mixed
     */
    public function get_app_popup_row($apo_num) {
        return $this->db->where('apo_num', $apo_num)->get('app_popup_tb')->row();
    }//end of get_app_popup_row()


    /**
     * APP 팝업 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_app_popup($query_data=array()) {
        if(
            !isset($query_data['apo_os_type']) || empty($query_data['apo_os_type']) ||
            !isset($query_data['apo_size_type']) || empty($query_data['apo_size_type']) ||
            !isset($query_data['apo_subject']) || empty($query_data['apo_subject'])
        ) {
            return false;
        }

        $query_data['apo_regdatetime'] = current_datetime();

        return $this->db->insert("app_popup_tb", $query_data);
    }//end of insert_app_popup()


    /**
     * APP 팝업 수정
     * @param $apo_num
     * @param array $query_data
     * @return bool
     */
    public function update_app_popup($apo_num, $query_data=array()) {

        if( empty($apo_num) ) {
            return false;
        }

        if(
            !isset($query_data['apo_os_type']) || empty($query_data['apo_os_type']) ||
            !isset($query_data['apo_size_type']) || empty($query_data['apo_size_type']) ||
            !isset($query_data['apo_subject']) || empty($query_data['apo_subject'])
        ) {
            return false;
        }

        return $this->db->where('apo_num', $apo_num)->update("app_popup_tb", $query_data);
    }//end of update_app_popup()


    /**
     * APP 팝업 삭제
     * @param $apo_num
     */
    public function delete_app_popup($apo_num) {
        return $this->db->where('apo_num', $apo_num)->delete('app_popup_tb');
    }//end of delete_app_popup()

    /**
     * APP 팝업 정보 수정
     * @param $apo_num
     * @param array $query_data
     * @return bool
     */
    public function update_app_popup_nonchk($apo_num, $query_data=array()) {
        if( empty($apo_num) ) {
            return false;
        }

        return $this->db->where('apo_num', $apo_num)->update("app_popup_tb", $query_data);
    }//end of update_app_push_info()

}//end of class App_popup_model