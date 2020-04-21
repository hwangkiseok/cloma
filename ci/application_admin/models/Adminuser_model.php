<?php
/**
 * 관리자계정 관련 모델
 */
class Adminuser_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 로그인 체크
     * @param $id
     * @param $pw
     */
    public function get_adminuser_login($id, $pw) {
        $query = "select * ";
        $query .= "from adminuser_tb ";
        $query .= "where au_loginid = '" . $this->db->escape_str($id) . "' ";
        $query .= "and au_password = password('" . $this->db->escape_str($pw) . "') ";

        return $this->db->query($query)->row();
    }//end of get_adminuser_login()

    public function get_adminuser_login_id($id) {
        $query = "select * ";
        $query .= "from adminuser_tb ";
        $query .= "where au_loginid = '" . $this->db->escape_str($id) . "' ";
        //$query .= "and adm_usestate = 'Y' ";

        return $this->db->query($query)->row();
    }//end of get_adminuser_login()

    /**
     * 관리자계정 단일항목 추출
     * @param $au_num
     */
    public function get_adminuser_row($au_num) {
        return $this->db->where("au_num", $au_num)->get("adminuser_tb")->row();
    }//end of get_adminuser_row()

    /**
     * 관리자계정 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_adminuser_list($query_array=array(), $start="", $end="", $is_count=FALSE) {
        //from 절
        $from_query = "from adminuser_tb ";

        //where 절
        $where_query = "where 1 = 1 ";
        //검색어
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
            $order_query = "order by au_num desc ";
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

            return $this->db->query($query)->result();
        }
    }//end of get_adminuser_list()

    /**
     * 관리자 계정 업데이트 (공통)
     * @param $au_num
     * @param array $query_data
     * @return bool
     */
    public function update_adminuser($au_num, $query_data=array()) {
        if( empty($au_num ) ) {
            return false;
        }

        if( !empty($query_data['au_password']) ) {
            $this->db->set("au_password", "password('".$query_data['au_password']."')", FALSE);
            unset($query_data['au_password']);
        }

        $this->db->where("au_num", $au_num);
        return $this->db->update("adminuser_tb", $query_data);
    }//end of update_adminuser()

    /**
     * 관리자 계정 등록 (공통)
     * @param array $query_data
     * @return bool
     */
    public function insert_adminuser($query_data=array()) {
        if(
            !isset($query_data['au_level']) || empty($query_data['au_level']) ||
            !isset($query_data['au_loginid']) || empty($query_data['au_loginid']) ||
            !isset($query_data['au_password']) || empty($query_data['au_password']) ||
            !isset($query_data['au_name']) || empty($query_data['au_name'])
        ) {
            return false;
        }

        $au_password = $query_data['au_password'];
        unset($query_data['au_password']);

        $query_data['au_regdate'] = current_date();

        $this->db->set("au_password", "password('" . $au_password . "')", FALSE);
        return $this->db->insert("adminuser_tb", $query_data);
    }//end of insert_adminuser()

    /**
     * 관리자 계정 삭제
     * @param $au_num
     * @return bool
     */
    public function delete_adminuser($au_num) {
        if( empty($au_num ) ) {
            return false;
        }

        $this->db->where("au_num", $au_num);
        return $this->db->delete("adminuser_tb");
    }//end of delete_adminuser()

}//end of class Adminuser_model