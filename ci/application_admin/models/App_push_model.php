<?php
/**
 * APP 푸시 관련 모델
 */
class App_push_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * APP 푸시 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_app_push_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from app_push_tb b ";
        $from_query .= "join adminuser_tb on au_num = ap_adminuser_num ";

        //where 절
        $where_query = "where 1 = 1 ";
        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
        }

        //당일푸시
        if( $query_array['where']['rctly'] == 'Y' ) {
            $where_query .= " and LEFT(ap_reserve_datetime,8) = DATE_FORMAT(NOW(), '%Y%m%d') AND ap_state <> 3 ";
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = "order by ap_num desc ";
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
            $query .= ", (SELECT IFNULL(SUM(apv_cnt),0) FROM app_push_product_view_tb a WHERE a.ap_num = b.ap_num) as ap_product_click_cnt ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;

            //echo $query;

            return $this->db->query($query)->result();
        }
    }//end of get_app_push_list()

    /**
     * APP 푸시 조회
     * @param $ap_num
     * @return mixed
     */
    public function get_app_push_row($ap_num) {
        return $this->db->where('ap_num', $ap_num)->get('app_push_tb')->row_array();
    }//end of get_app_push_row()

    /**
     * APP 예약 카운터
     * @param $ap_num
     * @return mixed
     */
    public function get_tot_reserve_push_cnt() {

        $sql = " 
             SELECT COUNT(*) AS cnt 
             FROM app_push_tb 
             WHERE 1 
             AND LEFT(ap_reserve_datetime,8) >= DATE_FORMAT(NOW(), '%Y%m%d') 
             AND ap_state <> 3 
             AND ap_display_state = 'Y' ; 
         ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();
        return $aResult['cnt'];

    }//end of get_tot_reserve_push_cnt()


    /**
     * APP 푸시 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_app_push($query_data=array()) {
        if(
            !isset($query_data['ap_os_type']) || empty($query_data['ap_os_type']) ||
            !isset($query_data['ap_subject']) || empty($query_data['ap_subject']) ||
            !isset($query_data['ap_message']) || empty($query_data['ap_message'])
//            !isset($query_data['ap_noti_type']) || empty($query_data['ap_noti_type'])
        ) {
            return false;
        }

        $query_data['ap_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['ap_regdatetime'] = current_datetime();

        if( !isset($query_data['ap_state']) || empty($query_data['ap_state']) ) {
            $query_data['ap_state'] = 1;
        }

        return $this->db->insert("app_push_tb", $query_data);
    }//end of insert_app_push()

    /**
     * APP 푸시 수정
     * @param $ap_num
     * @param array $query_data
     * @return bool
     */
    public function update_app_push($ap_num, $query_data=array()) {
        if( empty($ap_num) ) {
            return false;
        }
        if(
            !isset($query_data['ap_os_type']) || empty($query_data['ap_os_type']) ||
            !isset($query_data['ap_subject']) || empty($query_data['ap_subject']) ||
            !isset($query_data['ap_message']) || empty($query_data['ap_message']) /*||
            !isset($query_data['ap_noti_type']) || empty($query_data['ap_noti_type']) ||
            !isset($query_data['ap_state']) || empty($query_data['ap_state'])
            */
        ) {
            return false;
        }

        $query_data['ap_adminuser_num'] = $_SESSION['session_au_num'];

        

        return $this->db->where('ap_num', $ap_num)->update("app_push_tb", $query_data);
    }//end of update_app_push()

    /**
     * APP 푸시 정보 수정
     * @param $ap_num
     * @param array $query_data
     * @return bool
     */
    public function update_app_push_info($ap_num, $query_data=array()) {
        if( empty($ap_num) ) {
            return false;
        }

        $query_data['ap_adminuser_num'] = $_SESSION['session_au_num'];

        return $this->db->where('ap_num', $ap_num)->update("app_push_tb", $query_data);
    }//end of update_app_push_info()


    /**
     * 이미지 삭제
     * @param $ap_num
     * @return mixed
     */
    public function image_delete_app_push($ap_num, $field="") {
        if( empty($field) ) {
            $field = 'ap_image';
        }

        return $this->db->where('ap_num', $ap_num)->update("app_push_tb", array($field => ""));
    }//end of image_delete_app_push()


    /**
     * APP 푸시 삭제
     * @param $ap_num
     */
    public function delete_app_push($ap_num) {
        return $this->db->where('ap_num', $ap_num)->delete('app_push_tb');
    }//end of delete_app_push()

}//end of class App_push_model

