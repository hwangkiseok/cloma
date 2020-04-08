<?php
/**
 * 상품 MD 관련 모델
 */
class Product_md_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 상품 MD 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_product_md_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from product_md_tb ";
        $from_query .= "join product_tb on p_num = pmd_product_num ";

        //where 절
        $where_query = "where 1 = 1 ";
        //상품번호
        if( isset($query_array['where']['pnum']) && !empty($query_array['where']['pnum']) ) {
            $where_query .= "and pmd_product_num = '" . $this->db->escape_str($query_array['where']['pnum']) . "' ";
        }
        //카테고리
        if( isset($query_array['where']['cate']) && !empty($query_array['where']['cate']) ) {
            $where_query .= "and p_category = '" . $this->db->escape_str($query_array['where']['cate']) . "' ";
        }
        //MD카테고리
        if( isset($query_array['where']['md_div']) && !empty($query_array['where']['md_div']) ) {
            $where_query .= "and pmd_division = '" . $this->db->escape_str($query_array['where']['md_div']) . "' ";
        }
        //등록일
        if( isset($query_array['where']['date1']) && !empty($query_array['where']['date1']) ) {
            $where_query .= "and left(p_regdatetime, 8) >= '" . number_only($this->db->escape_str($query_array['where']['date1'])) . "' ";
        }
        if( isset($query_array['where']['date2']) && !empty($query_array['where']['date2']) ) {
            $where_query .= "and left(p_regdatetime, 8) <= '" . number_only($this->db->escape_str($query_array['where']['date2'])) . "' ";
        }
        //진열상태 (배열)
        if( isset($query_array['where']['display_state']) && !empty($query_array['where']['display_state']) ) {
            $display_state_array = array();
            foreach($query_array['where']['display_state'] as $key => $item) {
                $display_state_array[] = "p_display_state = '" . $this->db->escape_str($item) . "'";
            }

            $where_query .= "and (".implode(" or ", $display_state_array).") ";
        }
        //판매상태 (배열)
        if( isset($query_array['where']['sale_state']) && !empty($query_array['where']['sale_state']) ) {
            $sale_state_array = array();
            foreach($query_array['where']['sale_state'] as $key => $item) {
                $sale_state_array[] = "p_sale_state = '" . $this->db->escape_str($item) . "'";
            }

            $where_query .= "and (".implode(" or ", $sale_state_array).") ";
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
            $order_query = "order by p_num desc ";
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

            //echo $query;

            return $this->db->query($query)->result_array();
        }
    }//end of get_product_list()

    ///**
    // * 상품 MD 목록
    // * @param array $query_data
    // * @return bool
    // */
    //public function get_product_md_list($query_data=array(), $start="", $end="", $is_count=false, $include_product=false) {
    //    if( empty($query_data['pmd_division']) && empty($query_data['pmd_product_num']) ) {
    //        return false;
    //    }
    //
    //    $where_array = array();
    //    if( isset($query_data['pmd_division']) && !empty($query_data['pmd_division']) ) {
    //        $where_array['pmd_division'] = $query_data['pmd_division'];
    //    }
    //    if( isset($query_data['pmd_product_num']) && !empty($query_data['pmd_product_num']) ) {
    //        $where_array['pmd_product_num'] = $query_data['pmd_product_num'];
    //    }
    //
    //
    //    //return $this->db->where($where_array)->get('product_md_tb')->result_array();
    //
    //    $query = "";
    //}//end of get_product_md_list_simple()

    ///**
    // * @return bool
    // */
    //public function get_product_md_list_simple($query_data=array()) {
    //    if( empty($query_data['pmd_division']) && empty($query_data['pmd_product_num']) ) {
    //        return false;
    //    }
    //
    //    $where_array = array();
    //    if( isset($query_data['pmd_division']) && !empty($query_data['pmd_division']) ) {
    //        $where_array['pmd_division'] = $query_data['pmd_division'];
    //    }
    //    if( isset($query_data['pmd_product_num']) && !empty($query_data['pmd_product_num']) ) {
    //        $where_array['pmd_product_num'] = $query_data['pmd_product_num'];
    //    }
    //
    //    return $this->db->where($where_array)->get('product_md_tb')->result_array();
    //}//end of get_product_md_list_simple()

    /**
     * 상품 MD 항목 추출
     * @param array $where_data : 조건검색배열
     * @return bool
     */
    public function get_product_md_row($where_data=array()) {
        $where = "";
        if( isset($where_data['pmd_num']) && !empty($where_data['pmd_num']) ) {
            $where .= "pmd_num = '".$this->db->escape_str($where_data['pmd_num'])."' and ";
        }
        else if( isset($where_data['pmd_division']) && !empty($where_data['pmd_division']) && isset($where_data['pmd_product_num']) && !empty($where_data['pmd_product_num']) ) {
            $where .= "pmd_division = '".$this->db->escape_str($where_data['pmd_division'])."' and ";
            $where .= "pmd_product_num = '".$this->db->escape_str($where_data['pmd_product_num'])."' and ";
        }
        else {
            return false;
        }

        if( !empty($where) ) {
            $where = "where " . substr($where, 0, -4);
        }

        $query = "select * ";
        $query .= "from product_md_tb ";
        $query .= $where;
        $query .= "limit 1";

        return $this->db->query($query)->row_array();
    }//end of get_product_md_row()

    /**
     * 상품 MD 등록 (공통)
     * @param array $query_data
     * @return bool
     */
    public function insert_product_md($query_data=array()) {
        if(
            !isset($query_data['pmd_division']) || empty($query_data['pmd_division']) ||
            !isset($query_data['pmd_product_num']) || empty($query_data['pmd_product_num'])
        ) {
            return false;
        }

        //중복체크
        $md_row = $this->get_product_md_row(array(
            "pmd_division" => $query_data['pmd_division'],
            "pmd_product_num" => $query_data['pmd_product_num']
        ));

        if( !empty($md_row) ) {
            return true;
        }

        //해당 카테고리의 마지막 항목 정보
        $last_row = $this->get_last_order_row($query_data['pmd_division']);
        $query_data['pmd_order'] = $last_row->pmd_order + 1;

        return $this->db->insert("product_md_tb", $query_data);
    }//end of insert_product_md()

    /**
     * 상품 MD 순서 수정
     * @param $pmd_division
     * @param $pmd_product_num
     * @param $pmd_order
     * @return bool
     */
    public function order_update_product_md($pmd_division, $pmd_product_num, $pmd_order) {
        if( empty($pmd_division) || empty($pmd_product_num) ) {
            return false;
        }

        $where_array = array();
        $where_array['pmd_division'] = $pmd_division;
        $where_array['pmd_product_num'] = $pmd_product_num;

        $query_data = array();
        $query_data['pmd_order'] = $pmd_order;

        return $this->db->where($where_array)->update('product_md_tb', $query_data);
    }//end of order_update_product_md()


    /**
     * 해당 상품의 MD 삭제 (초기화)
     * @param $pmd_product_num
     * @return mixed
     */
    public function delete_product_md_in_product($pmd_product_num) {
        return $this->db->where('pmd_product_num', $pmd_product_num)->delete('product_md_tb');
    }//end of delete_product_md_in_product()

    /**
     * 상품 MD 삭제
     * @param $pmd_division
     * @param $pmd_product_num
     * @return bool
     */
    public function delete_product_md($pmd_division, $pmd_product_num) {
        if ( empty($pmd_division) || empty($pmd_product_num) ) {
            return false;
        }

        $where_array = array();
        $where_array['pmd_division'] = $pmd_division;
        $where_array['pmd_product_num'] = $pmd_product_num;

        return $this->db->where($where_array)->delete('product_md_tb');
    }//end of delete_product_md()

    /**
     * 해당 카테고리 마지막 ROW 추출
     * @param $pmd_division
     * @return mixed
     */
    public function get_last_order_row($pmd_division) {
        $query = "select * ";
        $query .= "from product_md_tb ";
        $query .= "where pmd_division = '" . $this->db->escape_str($pmd_division) . "' ";
        $query .= "order by pmd_order desc ";
        $query .= "limit 1 ";

        return $this->db->query($query)->row_array();
    }//end of get_last_order_row()

}//end of class Product_md_model