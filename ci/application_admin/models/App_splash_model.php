<?php
/**
 * APP 스플래시 관련 모델
 */
class App_splash_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * APP 스플래시 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_app_splash_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from app_splash_tb ";

        //where 절
        $where_query = "where 1 = 1 ";
        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = "order by aps_num desc ";
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
    }//end of get_app_splash_list()

    /**
     * APP 스플래시 조회
     * @param $aps_num
     * @return mixed
     */
    public function get_app_splash_row($aps_num) {
        return $this->db->where('aps_num', $aps_num)->get('app_splash_tb')->row();
    }//end of get_app_splash_row()

    /**
     * APP 스플래시 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_app_splash($query_data=array()) {
        if(
            !isset($query_data['aps_image']) || empty($query_data['aps_image']) ||
            !isset($query_data['aps_usestate']) || empty($query_data['aps_usestate'])
        ) {
            return false;
        }

        $query_data['aps_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['aps_regdatetime'] = current_datetime();

        return $this->db->insert("app_splash_tb", $query_data);
    }//end of insert_app_splash()

    /**
     * APP 스플래시 수정
     * @param $aps_num
     * @param array $query_data
     * @return bool
     */
    public function update_app_splash($aps_num, $query_data=array()) {
        if( empty($aps_num) ) {
            return false;
        }

        $query_data['aps_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['aps_regdatetime'] = current_datetime();

        return $this->db->where('aps_num', $aps_num)->update("app_splash_tb", $query_data);
    }//end of update_app_splash()

    /**
     * APP 스플래시 정보 수정
     * @param $aps_num
     * @param array $query_data
     * @return bool
     */
    public function update_app_splash_info($aps_num, $query_data=array()) {
        if( empty($aps_num) ) {
            return false;
        }

        $query_data['aps_adminuser_num'] = $_SESSION['session_au_num'];

        return $this->db->where('aps_num', $aps_num)->update("app_splash_tb", $query_data);
    }//end of update_app_splash_info()


    /**
     * 이미지 삭제
     * @param $aps_num
     * @return mixed
     */
    public function image_delete_app_splash($aps_num, $field="") {
        if( empty($field) ) {
            $field = 'aps_image';
        }

        return $this->db->where('aps_num', $aps_num)->update("app_splash_tb", array($field => ""));
    }//end of image_delete_app_splash()


    /**
     * APP 스플래시 삭제
     * @param $aps_num
     */
    public function delete_app_splash($aps_num) {
        return $this->db->where('aps_num', $aps_num)->delete('app_splash_tb');
    }//end of delete_app_splash()

}//end of class App_splash_model