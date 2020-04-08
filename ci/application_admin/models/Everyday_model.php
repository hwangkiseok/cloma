<?php
/**
 * 매일응모 관련 모델
 */
class Everyday_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 매일응모 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_everyday_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query = "from everyday_tb ";
        $from_query .= "join product_tb on p_num = ed_product_num ";
        $from_query .= "join adminuser_tb on au_num = ed_adminuser_num ";

        //where 절
        $where_query = "where 1 = 1 ";
        //상품번호
        if( isset($query_array['where']['pnum']) && !empty($query_array['where']['pnum']) ) {
            $where_query .= "and ed_product_num = '" . $this->db->escape_str($query_array['where']['pnum']) . "' ";
        }
        //노출상태
        if( isset($query_array['where']['displaystate']) && !empty($query_array['where']['displaystate']) ) {
            $where_query .= "and ed_displaystate = '" . $this->db->escape_str($query_array['where']['displaystate']) . "' ";
        }
        //활성상태
        if( isset($query_array['where']['usestate']) && !empty($query_array['where']['usestate']) ) {
            $where_query .= "and ed_usestate = '" . $this->db->escape_str($query_array['where']['usestate']) . "' ";
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
            $order_query = "order by ed_num desc ";
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
    }//end of get_product_list()

    /**
     * 매일응모 항목 추출
     * @param array $where_data : 조건검색배열 (ed_num | ed_product_num, ed_usestate, ed_displaystate)
     * @return bool
     */
    public function get_everyday_row($where_data=array()) {
        $where = "";
        if( isset($where_data['ed_num']) && !empty($where_data['ed_num']) ) {
            $where .= "ed_num = '".$this->db->escape_str($where_data['ed_num'])."' and ";
        }
        if( isset($where_data['ed_product_num']) && !empty($where_data['ed_product_num']) ) {
            $where .= "ed_product_num = '".$this->db->escape_str($where_data['ed_product_num'])."' and ";

            if( isset($where_data['ed_usestate']) && !empty($where_data['ed_usestate']) ) {
                $where .= "ed_usestate = '".$this->db->escape_str($where_data['ed_usestate'])."' and ";
            }
            if( isset($where_data['ed_displaystate']) && !empty($where_data['ed_displaystate']) ) {
                $where .= "ed_displaystate = '".$this->db->escape_str($where_data['ed_displaystate'])."' and ";
            }
        }
        if( isset($where_data['not_ed_num']) && !empty($where_data['not_ed_num']) ) {
            $where .= "ed_num != '".$this->db->escape_str($where_data['not_ed_num'])."' and ";
        }

        if( empty($where) ) {
            return false;
        }

        if( !empty($where) ) {
            $where = "where " . substr($where, 0, -4);
        }

        $query = "select * ";
        $query .= "from everyday_tb ";
        $query .= "join product_tb on p_num = ed_product_num ";
        $query .= "join adminuser_tb on au_num = ed_adminuser_num ";
        $query .= $where;
        $query .= "limit 1";

        return $this->db->query($query)->row();
    }//end of get_everyday_row()

    /**
     * 매일응모 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_everyday($query_data=array()) {
        if(
            !isset($query_data['ed_product_num']) || empty($query_data['ed_product_num']) ||
            !isset($query_data['ed_winner_count']) || empty($query_data['ed_winner_count'])
        ) {
            return false;
        }

        //중복체크 (활성/노출된 같은 상품)
        $ed_row = $this->get_everyday_row(array(
            "ed_product_num" => $query_data['ed_product_num'],
            "ed_usestate" => 'Y',
            "ed_displaystate" => 'Y'
        ));

        if( !empty($ed_row) ) {
            return $this->get_result(get_status_code('error'), "이미 등록된 상품입니다.");
        }

        $query_data['ed_adminuser_num'] = $_SESSION['session_au_num'];
        $query_data['ed_startdatetime'] = current_datetime();
        $query_data['ed_regdatetime'] = current_datetime();

        //insert
        if( $this->db->insert("everyday_tb", $query_data) ) {
            return $this->get_result(get_status_code('success'));
        }
        else {
            return $this->get_result(get_status_code('error'));
        }
    }//end of insert_everyday()

    /**
     * 매일응모 수정
     * @param $ed_num
     * @param array $query_data
     * @param bool $strict
     * @return bool
     */
    public function update_everyday($ed_num, $query_data=array(), $strict=true) {
        if( empty($ed_num) ) {
            return false;
        }

        if( $strict === true ) {
            if(
                //!isset($ed_num['ed_product_num']) || empty($query_data['ed_product_num']) ||
                !isset($query_data['ed_winner_count']) || empty($query_data['ed_winner_count'])
            ) {
                return false;
            }
        }

        ////중복체크
        //$ed_row = $this->get_everyday_row(array(
        //    "ed_product_num" => $query_data['ed_product_num'],
        //    "ed_usestate" => 'Y',
        //    "ed_displaystate" => 'Y',
        //    "not_ed_num" => $ed_num
        //));
        //
        //if( !empty($ed_row) ) {
        //    return $this->get_result(get_status_code('error'), "이미 등록된 상품입니다.");
        //}

        $query_data['ed_adminuser_num'] = $_SESSION['session_au_num'];

        //update
        if ( $this->db->where("ed_num", $ed_num)->update("everyday_tb", $query_data) ) {
            return $this->get_result(get_status_code('success'));
        }
        else {
            return $this->get_result(get_status_code('error'));
        }
    }//end of insert_everyday()

    /**
     * 매일응모 삭제
     * @param $ed_num
     * @return bool
     */
    public function delete_everyday($ed_num) {
        if ( empty($ed_num) || empty($ed_num) ) {
            return false;
        }

        $where_array = array();
        $where_array['ed_num'] = $ed_num;

        return $this->db->where($where_array)->delete("everyday_tb");
    }//end of delete_everyday()

}//end of class Everyday_model