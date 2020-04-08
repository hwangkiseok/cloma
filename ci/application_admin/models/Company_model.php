<?php
/**
 * 외부광고 관련 모델
 */
class Company_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 외부광고 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_company_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from company_tb ";
        $from_query .= "join adminuser_tb on au_num = co_adminuser_num ";

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
            $order_query = "order by co_num desc ";
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
    }//end of get_company_list()

    /**
     * 외부광고 조회
     * @param $co_num
     * @return mixed
     */
    public function get_company_row($co_num) {
        return $this->db->where('co_num', $co_num)->get('company_tb')->row();
    }//end of get_company_row()

    /**
     * 외부광고 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_company($query_data=array()) {
        if(
            !isset($query_data['co_name']) || empty($query_data['co_name']) ||
            !isset($query_data['co_loginid']) || empty($query_data['co_loginid'])
        ) {
            return false;
        }

        $query_data['co_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['co_regdatetime'] = current_datetime();

        return $this->db->insert("company_tb", $query_data);
    }//end of insert_company()

    /**
     * 외부광고 수정
     * @param $co_num
     * @param array $query_data
     * @return bool
     */
    public function update_company($co_num, $query_data=array()) {
        if( empty($co_num) ) {
            return false;
        }
        if(
            !isset($query_data['co_name']) || empty($query_data['co_name']) ||
            !isset($query_data['co_loginid']) || empty($query_data['co_loginid'])
        ) {
            return false;
        }

        $query_data['co_adminuser_num'] = $_SESSION['session_au_num'];

        return $this->db->where('co_num', $co_num)->update("company_tb", $query_data);
    }//end of update_company()

    /**
     * 외부광고 삭제
     * @param $co_num
     */
    public function delete_company($co_num) {
        return $this->db->where('co_num', $co_num)->delete('company_tb');
    }//end of delete_company()

}//end of class Company_model