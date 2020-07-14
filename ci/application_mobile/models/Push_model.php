<?php
/**
 * 푸시 관련 모델
 */
class push_model extends M_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()


    public function setUsePush($arrayParams){

        $sql = "UPDATE member_tb SET m_push_yn = '{$arrayParams['flag']}' WHERE m_num = '{$arrayParams['m_num']}'; ";
        $bResult = $this->db->query($sql);

        return $bResult;

    }

    public function setUseShoppingPush($arrayParams){

        $sql = "UPDATE member_tb SET m_shopping_push_yn = '{$arrayParams['flag']}' WHERE m_num = '{$arrayParams['m_num']}'; ";
        log_message('A',$sql);
        $bResult = $this->db->query($sql);

        return $bResult;

    }


    /**
     * 게시물 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_push_list($query_array=array(), $start="", $end="", $is_count=false) {
        //from 절
        $from_query  = " from app_push_tb A ";
        $from_query .= " left join product_tb B ON A.ap_pnum = B.p_num ";

        //where 절
        if(zsDebug()){
            //$where_query = " where 1 ";
            $where_query = " where ap_state = 3 AND ap_display_state = 'Y' ";
        }else{
            $where_query = " where ap_state = 3 AND ap_display_state = 'Y' ";
        }


        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = "order by ap_reserve_datetime desc ";
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
            $query = "select * , DATE_FORMAT(A.ap_regdatetime,'%Y.%m.%d') AS reg_date_str ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;

            return $this->db->query($query)->result_array();
        }
    }//end of get_board_qna_list()


    public function get_info_list(){

        $sql        = "SELECT * , DATE_FORMAT(reg_date,'%Y.%m.%d') AS reg_date_str FROM noti_tb WHERE m_num = '{$_SESSION['session_m_num']}' AND view_flag = 'N' ORDER BY reg_date DESC; ";
        $oResult    = $this->db->query($sql);
        $aResult_N  = $oResult->result_array();

        $sql        = "SELECT * , DATE_FORMAT(reg_date,'%Y.%m.%d') AS reg_date_str FROM noti_tb WHERE m_num = '{$_SESSION['session_m_num']}' AND view_flag = 'Y' ORDER BY reg_date DESC LIMIT 20; ";
        $oResult    = $this->db->query($sql);
        $aResult_Y  = $oResult->result_array();

        return array_merge($aResult_N,$aResult_Y);

    }


}//end of class push_model