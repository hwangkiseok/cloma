<?php
/**
 * 찜 관련 모델
 */
class Share_model extends W_Model {

    var $m_num = "";

    public function __construct(){
        parent::__construct();

        $this->m_num = $_SESSION['session_m_num'];
    }//end of __construct()

    /**
     * 찜한상품 목록 추출
     * @param array $query_data    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_share_list($query_data=array(), $start="", $end="", $is_count=false) {

        //from 절
        $from_query = "from share_tb ";
        $from_query .= "join product_tb on p_num = s_product_num ";

        //where 절
        $where_query = "where p_display_state = 'Y' ";
        $where_query .= "and p_sale_state = 'Y' ";
        $where_query .= "and s_member_num = '" . $this->m_num . "' ";

        //키워드
        if(
            isset($query_data['where']['kfd']) && !empty($query_data['where']['kfd']) &&
            isset($query_data['where']['kwd']) && !empty($query_data['where']['kwd'])
        ) {
            $where_query .= "and " . $query_data['where']['kfd'] . " like '%" . $this->db->escape_str($query_data['where']['kwd']) . "%' ";
        }
        //제외상품
        if( isset($query_data['where']['not_pnum']) && !empty($query_data['where']['not_pnum']) ) {
            if( is_array($query_data['where']['not_pnum']) ) {
                $not_pnum = implode(',', $query_data['where']['not_pnum']);
            }
            else {
                $not_pnum = $query_data['where']['not_pnum'];
            }

            if($not_pnum) {
                $where_query .= " AND p_num NOT IN ({$not_pnum}) ";
            }
        }
        //재고확인
        if( isset($query_data['where']['p_stock_state']) && !empty($query_data['where']['p_stock_state']) ) {
            $where_query .= "and p_stock_state = '" . $this->db->escape_str($query_data['where']['p_stock_state']) . "' ";
        }

        //order by 절
        if( isset($query_data['orderby']) && !empty($query_data['orderby']) ) {
            $order_query = "order by " . $query_data['orderby'] . " ";
        }
        else {
            $order_query = "order by s_regdatetime desc ";
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

            return $this->db->query($query)->result_array();
        }
    }//end of get_share_list()


    /**
     * 찜 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_share($query_data=array()) {
        if(
            !isset($query_data['s_member_num']) || empty($query_data['s_member_num']) ||
            !isset($query_data['s_product_num']) || empty($query_data['s_product_num'])
        ) {
            return false;
        }

        $row = $this->db->where($query_data)->get('share_tb')->row_array();

        if( !empty($row) ) {
            return array('code' => get_status_code('overlap'));
        }

        $query_data['s_regdatetime'] = current_datetime();

        if( $this->db->insert('share_tb', $query_data) ) {
            return array('code' => get_status_code('success'));
        }
        else {
            return array('code' => get_status_code('error'));
        }
    }//end of insert_share()

    /**
     * 해당 회원,상품 찜 정보
     * @param $s_member_num
     * @param $s_product_num
     * @return mixed
     */
    public function get_share_row($s_member_num, $s_product_num) {
        $where_array = array();
        $where_array['s_member_num'] = $s_member_num;
        $where_array['s_product_num'] = $s_product_num;

        return $this->db->where($where_array)->get('share_tb')->row_array();
    }//end of get_share_row()

    /**
     * 해당 회원 찜 갯수 추출
     * @param $s_member_num
     * @return mixed
     */
    public function get_share_count($s_member_num) {
        if( empty($s_member_num) ) {
            return false;
        }

        $query = "select count(*) as cnt ";
        $query .= "from share_tb ";
        $query .= "join product_tb on p_num = s_product_num ";
        $query .= "where s_member_num = '" . $this->db->escape_str($s_member_num) . "' ";
        $query .= "and p_display_state = 'Y' ";
        $query .= "and p_sale_state = 'Y'  ";

        return $this->db->query($query)->row_array('cnt');
    }//end of get_share_count()

    /**
     * 찜한상품 삭제
     * @param $s_member_num
     * @param $s_product_num
     * @return mixed
     */
    public function delete_share($s_member_num, $s_product_num) {
        $where_array = array();
        $where_array['s_member_num'] = $s_member_num;
        $where_array['s_product_num'] = $s_product_num;

        return $this->db->where($where_array)->delete('share_tb');

    }//end of delete_share()

}//end of class share_model