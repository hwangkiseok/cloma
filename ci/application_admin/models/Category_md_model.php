<?php
/**
 * 카테고리 MD 관련 모델
 */
class Category_md_model extends A_Model {

    var $category_md_tb;

    public function __construct(){
        parent::__construct();

        $this->category_md_tb = "category_md_tb";

    }//end of __construct()

    /**
     * 카테고리 MD 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_category_md_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절

        //if( in_array($_SESSION['GroupMemberId'], array('zeus721', 'reddrink')) || $_COOKIE['cki_category_md_ver'] == "2" ){
        //if( $_COOKIE['cki_category_md_ver'] == "2" ){
        //    $from_query = "from category_md_tb_test ";
        //}else{
        //    $from_query = "from category_md_tb ";
        //}

        $from_query = "from " . $this->category_md_tb . " ";

        //where 절
        $where_query = "where 1 = 1 ";
        //구분
        if( isset($query_array['where']['division']) && !empty($query_array['where']['division']) ) {
            $where_query .= "and cmd_division = '" . $this->db->escape_str($query_array['where']['division']) . "' ";
        }
        //상태
        if( isset($query_array['where']['state']) && !empty($query_array['where']['state']) ) {
            $where_query .= "and cmd_state = '" . $this->db->escape_str($query_array['where']['state']) . "' ";
        }
        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            if( $query_array['where']['kfd'] == "all" ) {
                $where_query .= "and ( ";
                $where_query .= "   cmd_name like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= "   or cmd_product_cate like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= ") ";
            }
            else {
                $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
            }
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = "order by cmd_order asc, cmd_num asc ";
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
    }//end of get_category_md_list()

    /**
     * 카테고리 MD 항목 추출
     * @param array $where_data : 조건검색배열
     * @return bool
     */
    public function get_category_md_row($where_data=array()) {
        $where = "";
        if( isset($where_data['cmd_num']) && !empty($where_data['cmd_num']) ) {
            $where .= "cmd_num = '".$this->db->escape_str($where_data['cmd_num'])."' and ";
        }
        else {
            return false;
        }

        if( !empty($where) ) {
            $where = "where " . substr($where, 0, -4);
        }

        $query = "
            select *
            from " . $this->category_md_tb . "
            " . $where . "
            limit 1
        ";
        return $this->db->query($query)->row();
    }//end of get_category_md_row()

    /**
     * 카테고리 MD 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_category_md($query_data=array()) {
        if(
            //!isset($query_data['cmd_division']) || empty($query_data['cmd_division']) ||
             !isset($query_data['cmd_name']) || empty($query_data['cmd_name'])

        ) {
            return false;
        }

        //해당 카테고리의 마지막 항목 정보
        $query = "select max(cmd_order) as last_order from " . $this->category_md_tb . " where cmd_state = 'Y'";
        $last_order = $this->db->query($query)->row("last_order");
        if( empty($last_order) ) {
            $last_order = 0;
        }
        $query_data['cmd_order'] = $last_order + 1;
        $query_data['cmd_regdatetime'] = current_datetime();


        return $this->db->insert($this->category_md_tb, $query_data);
    }//end of insert_category_md()

    /**
     * 카테고리 MD 수정
     * @param array $query_data
     * @return bool
     */
    public function update_category_md($cmd_num, $query_data=array()) {
        if(empty($cmd_num) || empty($query_data) ) {
            return false;
        }

        return $this->db->where("cmd_num", $cmd_num)->update($this->category_md_tb, $query_data);
    }//end of update_category_md()

    /**
     * 카테고리 MD 순서 수정
     * @param $pmd_division
     * @param $pmd_product_num
     * @param $pmd_order
     * @return bool
     */
    public function order_update_category_md($cmd_num, $cmd_order) {
        if( empty($cmd_num) || empty($cmd_order) ) {
            return false;
        }

        $where_array = array();
        $where_array['cmd_num'] = $cmd_num;

        $query_data = array();
        $query_data['cmd_order'] = $cmd_order;

        return $this->db->where($where_array)->update($this->category_md_tb, $query_data);
    }//end of order_update_category_md()

    /**
     * 카테고리 MD 삭제
     * @param $cmd_num
     * @return mixed
     */
    public function delete_category_md($cmd_num) {
        if( empty($cmd_num) ) {
            return false;
        }

        return $this->db->where("cmd_num", $cmd_num)->delete($this->category_md_tb);
    }//end of delete_category_md()

}//end of class Category_md_model