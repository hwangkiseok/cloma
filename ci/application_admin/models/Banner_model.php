<?php
/**
 * 배너 관련 모델
 */
class Banner_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 배너 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_banner_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from banner_tb ";
        $from_query .= "join adminuser_tb on au_num = bn_adminuser_num ";

        //where 절
        $where_query = "where 1 = 1 ";
        //배너종류
        if( isset($query_array['where']['div']) && !empty($query_array['where']['div']) ) {
            $where_query .= "and bn_division = '" . $this->db->escape_str($query_array['where']['div']) . "' ";
        }
        //노출여부
        if( isset($query_array['where']['usestate']) && !empty($query_array['where']['usestate']) ) {
            $where_query .= "and bn_usestate = '" . $this->db->escape_str($query_array['where']['usestate']) . "' ";
        }
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
            $order_query = "order by bn_num desc ";
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
    }//end of get_banner_list()

    /**
     * 배너 조회
     * @param $bn_num
     * @return mixed
     */
    public function get_banner_row($bn_num) {
        return $this->db->where('bn_num', $bn_num)->get('banner_tb')->row();
    }//end of get_banner_row()

    /**
     * 배너 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_banner($query_data=array()) {
        if(
            !isset($query_data['bn_division']) || empty($query_data['bn_division']) ||
            !isset($query_data['bn_subject']) || empty($query_data['bn_subject']) ||
            !isset($query_data['bn_image']) || empty($query_data['bn_image']) ||
            !isset($query_data['bn_usestate']) || empty($query_data['bn_usestate'])
        ) {
            return false;
        }

        if( !isset($query_data['bn_termlimit_yn']) || empty($query_data['bn_termlimit_yn']) ) {
            $query_data['bn_termlimit_yn'] = 'N';
        }

        if( isset($query_data['bn_termlimit_datetime1']) && !empty($query_data['bn_termlimit_datetime1']) ) {
            $query_data['bn_termlimit_datetime1'] = number_only($query_data['bn_termlimit_datetime1']) . "000000";
        }
        if( isset($query_data['bn_termlimit_datetime2']) && !empty($query_data['bn_termlimit_datetime2']) ) {
            $query_data['bn_termlimit_datetime2'] = number_only($query_data['bn_termlimit_datetime2']) . "235959";
        }

        //bn_order
        $last_order = $this->get_last_order_row($query_data['bn_division']);

        $query_data['bn_order'] = (int)($last_order) + 1;
        $query_data['bn_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['bn_regdatetime'] = current_datetime();

        return $this->db->insert("banner_tb", $query_data);
    }//end of insert_banner()

    /**
     * 배너 수정
     * @param $bn_num
     * @param array $query_data
     * @return bool
     */
    public function update_banner($bn_num, $query_data=array()) {
        if( empty($bn_num) ) {
            return false;
        }

        if( !isset($query_data['bn_termlimit_yn']) || empty($query_data['bn_termlimit_yn']) ) {
            $query_data['bn_termlimit_yn'] = 'N';
        }

        if( isset($query_data['bn_termlimit_datetime1']) && !empty($query_data['bn_termlimit_datetime1']) ) {
            $query_data['bn_termlimit_datetime1'] = number_only($query_data['bn_termlimit_datetime1']) . "000000";
        }
        if( isset($query_data['bn_termlimit_datetime2']) && !empty($query_data['bn_termlimit_datetime2']) ) {
            $query_data['bn_termlimit_datetime2'] = number_only($query_data['bn_termlimit_datetime2']) . "235959";
        }

        $query_data['bn_adminuser_num'] = $_SESSION['session_au_num'];

        return $this->db->where('bn_num', $bn_num)->update("banner_tb", $query_data);
    }//end of update_banner()

    /**
     * 배너 삭제
     * @param $bn_num
     */
    public function delete_banner($bn_num) {
        return $this->db->where('bn_num', $bn_num)->delete('banner_tb');
    }//end of delete_banner()

    /**
     * 해당 카테고리 마지막 ROW 추출
     * @param $bn_division
     * @return mixed
     */
    public function get_last_order_row($bn_division) {
        $query = "select * ";
        $query .= "from banner_tb ";
        $query .= "where bn_division = '" . $this->db->escape_str($bn_division) . "' ";
        $query .= "order by bn_order desc ";
        $query .= "limit 1 ";

        return $this->db->query($query)->row();
    }//end of get_last_order_row()

    /**
     * 배너 순서 수정
     * @param $bn_num
     * @param $bn_order
     * @return bool
     */
    public function order_update_banner($bn_num, $bn_order) {
        if ( empty($bn_num) ) {
            return false;
        }

        $query_data = array();
        $query_data['bn_order'] = $bn_order;

        return $this->db->where('bn_num', $bn_num)->update('banner_tb', $query_data);
    }//end of order_update_banner()

}//end of class banner_model