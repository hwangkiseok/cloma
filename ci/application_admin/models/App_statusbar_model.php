<?php
/**
 * APP 상태바 관련 모델
 */
class App_statusbar_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * APP 상태바 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_app_statusbar_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from app_statusbar_tb ";

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
            $order_query = "order by asb_num desc ";
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
    }//end of get_app_statusbar_list()

    /**
     * APP 상태바 조회
     * @param $asb_num
     * @return mixed
     */
    public function get_app_statusbar_row($asb_num) {
        return $this->db->where('asb_num', $asb_num)->get('app_statusbar_tb')->row();
    }//end of get_app_statusbar_row()

    /**
     * APP 상태바 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_app_statusbar($query_data=array()) {
        if(
            !isset($query_data['asb_color']) || empty($query_data['asb_color']) ||
            !isset($query_data['asb_usestate']) || empty($query_data['asb_usestate'])
        ) {
            return false;
        }

        $query_data['asb_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['asb_regdatetime'] = current_datetime();

        return $this->db->insert("app_statusbar_tb", $query_data);
    }//end of insert_app_statusbar()

    /**
     * APP 상태바 수정
     * @param $asb_num
     * @param array $query_data
     * @return bool
     */
    public function update_app_statusbar($asb_num, $query_data=array()) {
        if( empty($asb_num) ) {
            return false;
        }

        $query_data['asb_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['asb_regdatetime'] = current_datetime();

        return $this->db->where('asb_num', $asb_num)->update("app_statusbar_tb", $query_data);
    }//end of update_app_statusbar()

    /**
     * APP 상태바 정보 수정
     * @param $asb_num
     * @param array $query_data
     * @return bool
     */
    public function update_app_statusbar_info($asb_num, $query_data=array()) {
        if( empty($asb_num) ) {
            return false;
        }

        $query_data['asb_adminuser_num'] = $_SESSION['session_au_num'];

        return $this->db->where('asb_num', $asb_num)->update("app_statusbar_tb", $query_data);
    }//end of update_app_statusbar_info()


    /**
     * 이미지 삭제
     * @param $asb_num
     * @return mixed
     */
    public function image_delete_app_statusbar($asb_num, $field="") {
        if( empty($field) ) {
            $field = 'asb_color';
        }

        return $this->db->where('asb_num', $asb_num)->update("app_statusbar_tb", array($field => ""));
    }//end of image_delete_app_statusbar()


    /**
     * APP 상태바 삭제
     * @param $asb_num
     */
    public function delete_app_statusbar($asb_num) {
        return $this->db->where('asb_num', $asb_num)->delete('app_statusbar_tb');
    }//end of delete_app_statusbar()

}//end of class App_statusbar_model