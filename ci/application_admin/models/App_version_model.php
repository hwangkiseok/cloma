<?php
/**
 * APP 버전 관련 모델
 */
class App_version_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * APP 버전 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_app_version_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from app_version_tb ";
        $from_query .= "join adminuser_tb on au_num = av_adminuser_num ";

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
            $order_query = "order by av_num desc ";
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
    }//end of get_app_version_list()

    /**
     * APP 버전 조회
     * @param $av_num
     * @return mixed
     */
    public function get_app_version_row($av_num) {
        return $this->db->where('av_num', $av_num)->get('app_version_tb')->row();
    }//end of get_app_version_row()


    /**
     * 최신 버전코드 추출
     * @return mixed
     */
    public function get_last_app_version_row() {
        return $this->db->order_by('av_version_code', 'desc')->limit(1, 0)->get('app_version_tb')->row();
    }//end of get_last_app_version_row()

    /**
     * APP 버전 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_app_version($query_data=array()) {
        if(
            !isset($query_data['av_version']) || empty($query_data['av_version']) ||
            !isset($query_data['av_version_code']) || empty($query_data['av_version_code']) ||
            !isset($query_data['av_offer_type']) || empty($query_data['av_offer_type']) ||
            !isset($query_data['av_os_type']) || empty($query_data['av_os_type'])
        ) {
            return false;
        }

        //$last_row = $this->get_last_app_version_row();
        //if( !empty($last_row) ) {
        //    $version_code = (int)($last_row->av_version_code) + 1;
        //}
        //else {
        //    $version_code = 1;
        //}

        //$query_data['av_version_code'] = $version_code;
        $query_data['av_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['av_regdatetime'] = current_datetime();

        return $this->db->insert("app_version_tb", $query_data);
    }//end of insert_app_version()

    /**
     * APP 버전 수정
     * @param $av_num
     * @param array $query_data
     * @return bool
     */
    public function update_app_version($av_num, $query_data=array()) {
        if( empty($av_num) ) {
            return false;
        }
        if(
            !isset($query_data['av_version']) || empty($query_data['av_version']) ||
            !isset($query_data['av_offer_type']) || empty($query_data['av_offer_type']) ||
            !isset($query_data['av_os_type']) || empty($query_data['av_os_type'])
        ) {
            return false;
        }

        $query_data['av_adminuser_num'] = $_SESSION['session_au_num'];

        return $this->db->where('av_num', $av_num)->update("app_version_tb", $query_data);
    }//end of update_app_version()

    /**
     * APP 버전 삭제
     * @param $av_num
     */
    public function delete_app_version($av_num) {
        return $this->db->where('av_num', $av_num)->delete('app_version_tb');
    }//end of delete_app_version()

}//end of class App_version_model