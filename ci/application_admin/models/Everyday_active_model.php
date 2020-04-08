<?php
/**
 * 매일응모 참여 관련 모델
 */
class Everyday_active_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 매일응모 참여 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_everyday_active_list($query_array=array(), $start="", $end="", $is_count=false) {
        $cur_ym = date("Ym", time());
        $everyday_active_tb = "everyday_active_tb";

        if( isset($query_array['where']['ym']) && !empty($query_array['where']['ym']) ) {
            $ym = $query_array['where']['ym'];

            //테이블 확인
            $everyday_active_tb = "everyday_active_" . $ym . "_tb";
            if( !$this->table_check($everyday_active_tb) ) {
                if( $is_count === true ) {
                    return 0;
                }
                else {
                    return array();
                }
            }
        }//end of if()

        //from 절
        $from_query = "from " . $everyday_active_tb . " ";
        $from_query .= "join everyday_tb on ed_num = eda_everyday_num ";
        $from_query .= "join product_tb on p_num = ed_product_num ";
        $from_query .= "join member_tb on m_num = eda_member_num ";

        //where 절
        $where_query = "where 1 = 1 ";
        //진행상태
        if( isset($query_array['where']['usestate']) && !empty($query_array['where']['usestate']) ) {
            $where_query .= "and ed_usestate = '" . $this->db->escape_str($query_array['where']['usestate']) . "' ";
        }
        //노출여부
        if( isset($query_array['where']['displaystate']) && !empty($query_array['where']['displaystate']) ) {
            $where_query .= "and ed_displaystate = '" . $this->db->escape_str($query_array['where']['displaystate']) . "' ";
        }
        //당첨여부
        if( isset($query_array['where']['win_yn']) && !empty($query_array['where']['win_yn']) ) {
            $where_query .= "and eda_winner_yn = '" . $this->db->escape_str($query_array['where']['win_yn']) . "' ";
        }
        //날짜검색
        if( isset($query_array['where']['dateType']) && !empty($query_array['where']['dateType']) ) {
            if( isset($query_array['where']['date1']) && !empty($query_array['where']['date1']) ) {
                $where_query .= "and left(" . $query_array['where']['dateType'] . ", 8) >= '" . number_only($this->db->escape_str($query_array['where']['date1'])) . "' ";
            }
            if( isset($query_array['where']['date2']) && !empty($query_array['where']['date2']) ) {
                $where_query .= "and left(" . $query_array['where']['dateType'] . ", 8) <= '" . number_only($this->db->escape_str($query_array['where']['date2'])) . "' ";
            }
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
            $order_query = "order by eda_num desc ";
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
    }//end of get_everyday_active_list()

    /**
     * 매일응모 참여 조회
     * @param $eda_num
     * @return mixed
     */
    public function get_everyday_active_row($eda_num) {
        return $this->db->where('eda_num', $eda_num)->get('everyday_active_tb')->row();
    }//end of get_everyday_active_row()

    /**
     * 매일응모 참여 삭제
     * @param $eda_num
     * @return
     */
    public function delete_everyday_active($eda_num) {
        return $this->db->where('eda_num', $eda_num)->delete('everyday_active_tb');
    }//end of delete_everyday_active()

}//end of class Everyday_active_model