<?php
/**
 * 카테고리 MD 관련 모델
 */
class Category_md_model extends W_Model {

    public function __construct(){
        parent::__construct();
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
        $from_query = "from category_md_tb ";

        //where 절
        $where_query = "where cmd_state = 'Y' ";
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

            return $this->db->query($query)->row_array('cnt');
        }
        //데이터 추출
        else {

            $query = "select * ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;

            //log_message('A',$query);

            return $this->db->query($query)->result_array();
        }
    }//end of get_category_md_list()

    /**
     * 카테고리 MD 항목 추출
     * @param array $where_data : 조건검색배열
     * @return bool|array
     */
    public function get_category_md_row($where_data=array()) {


        if( !isset($where_data['cmd_num']) && empty($where_data['cmd_num']) ) {
            return false;
        }

        $sWhereQueryString = "AND cmd_num = '".$this->db->escape_str($where_data['cmd_num'])."' ";

        $query = "
            select *
            from category_md_tb
            WHERE 1 " . $sWhereQueryString . "
            limit 1
        ";

        return $this->db->query($query)->row_array();

    }//end of get_category_md_row()

}//end of class Category_md_model