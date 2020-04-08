<?php
/**
 * 이벤트 참여 관련 모델
 */
class Event_active_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 이벤트 참여 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_event_active_list($query_array=array(), $start="", $end="", $is_count=false) {
        $ym = date("Ym", time());

        if( isset($query_array['where']['ym']) && !empty($query_array['where']['ym']) ) {
            $ym = $query_array['where']['ym'];
        }

        //테이블 확인
        $event_active_tb = "event_active_" . $ym . "_tb";
        if( !$this->table_check($event_active_tb) ) {
            if( $is_count === true ) {
                return 0;
            }
            else {
                return array();
            }
        }
        
        //where 절
        $where_query = "where 1 = 1 ";
        //종류
        if( isset($query_array['where']['div']) && !empty($query_array['where']['div']) ) {
            $where_query .= "and e_division = '" . $this->db->escape_str($query_array['where']['div']) . "' ";
        }
        //진행여부
        if( isset($query_array['where']['pro_state']) && !empty($query_array['where']['pro_state']) ) {
            $where_query .= "and e_proc_state = '" . $this->db->escape_str($query_array['where']['pro_state']) . "' ";
        }
        //노출여부
        if( isset($query_array['where']['dis_state']) && !empty($query_array['where']['dis_state']) ) {
            $where_query .= "and e_display_state = '" . $this->db->escape_str($query_array['where']['dis_state']) . "' ";
        }
        //회원아이디
        if( isset($query_array['where']['m_id']) && !empty($query_array['where']['m_id']) ) {
            $where_query .= "and m_loginid = '" . $this->db->escape_str($query_array['where']['m_id']) . "' ";
        }
        //회원번호
        if( isset($query_array['where']['m_num']) && !empty($query_array['where']['m_num']) ) {
            $where_query .= "and ea_member_num = '" . $this->db->escape_str($query_array['where']['m_num']) . "' ";
        }
        //달성종류
        if( isset($query_array['where']['ew_type']) && !empty($query_array['where']['ew_type']) ) {
            if( $query_array['where']['ew_type'] == "all" ) {
                $where_query .= "and ew_type != '' ";
            }
            else {
                $where_query .= "and ew_type = '" . $this->db->escape_str($query_array['where']['ew_type']) . "' ";
            }
        }
        //년월검색
        if(
            isset($query_array['where']['year']) && !empty($query_array['where']['year']) &&
            isset($query_array['where']['month']) && !empty($query_array['where']['month'])
        ) {
            $where_query .= "and left(ea_regdatetime, 6) = '" . number_only($this->db->escape_str($query_array['where']['year'] . $query_array['where']['month'])) . "' ";
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
            if( $query_array['where']['kfd'] == "ew_contact" ) {
                $where_query .= "and replace(" . $query_array['where']['kfd'] . ", '-', '') like '%" . $this->db->escape_str(str_replace("-", "", $query_array['where']['kwd'])) . "%' ";
            }
            else if( $query_array['where']['kfd'] == "ea_month_count" || $query_array['where']['kfd'] == "ea_accrue_count" ) {
                $where_query .= "and " . $query_array['where']['kfd'] . " = '" . $this->db->escape_str($query_array['where']['kwd']) . "' ";
            }
            else if( $query_array['where']['kfd'] == "m_loginid") {
                $tmp_kwd = $this->db->escape_str($query_array['where']['kwd']);
                $where_query .= "and ( m_loginid like '%{$tmp_kwd}%' OR m_authno like '%{$tmp_kwd}%' ) ";
            }
            else {
                $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
            }
        }

        //group by 절
        $groupby_query = "";
        if( isset($query_array['where']['grp_yn']) && !empty($query_array['where']['grp_yn']) ) {
            $groupby_query .= "group by ea_member_num ";
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $orderby_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $orderby_query = "order by ea_regdatetime desc, ea_num desc ";
        }

        //limit 절
        $limit_query = "";
        if( $start !== "" && $end !== "" ) {
            $limit_query .= "limit " . $start . ", " . $end . " ";
        }

        //from 절
        $from_query = "";

        //달성검색일때
        if( isset($query_array['where']['ew_type']) && !empty($query_array['where']['ew_type']) ) {
            $from_query = "from event_winner_tb ";
            $from_query .= "join " . $event_active_tb . " on ea_num = ew_event_active_num and left(ew_regdatetime, 6)='" . $ym . "' ";
            $from_query .= "join event_tb on e_num = ea_event_num ";
            $from_query .= "join member_tb on m_num = ea_member_num ";
        }
        //달성검색 아닐때
        else {
            if( isset($query_array['where']['grp_yn']) && !empty($query_array['where']['grp_yn']) ) {
                $from_query .= "
                from (
                    select * from " . $event_active_tb . "
                    join event_tb on e_num = ea_event_num
                    join member_tb on m_num = ea_member_num
                    " . $where_query . "
                    order by ea_num desc
                ) TB
            ";
            }
            else {
                $from_query .= "from " . $event_active_tb . " ";
                $from_query .= "join event_tb on e_num = ea_event_num ";
                $from_query .= "join member_tb on m_num = ea_member_num ";
            }
        }

        //갯수만 추출
        if( $is_count === true ) {
            if( isset($query_array['where']['grp_yn']) && !empty($query_array['where']['grp_yn']) ) {
                $query = "select count(*) as cnt from ";
                $query .= "(select * ";
                $query .= $from_query;
                $query .= $where_query;
                $query .= $groupby_query;
                $query .= ") TB ";
            }
            else {
                $query = "select count(*) cnt ";
                $query .= $from_query;
                $query .= $where_query;
            }

            //echo $query . "<br>\n";

            return $this->db->query($query)->row_array('cnt');
        }
        //데이터 추출
        else {
            $query = "select * ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $groupby_query;
            $query .= $orderby_query;
            $query .= $limit_query;

            //zsView($query);

            //echo $query . "<br>\n";

            return $this->db->query($query)->result_array();
        }
    }//end of get_event_active_list()

    /**
     * @date 171016
     * @author 황기석
     * @desc 이벤트에 중복자가 있는지
     * @return boolean
     */
    function overlapWinner($arrayParams){

        $whereQueryString = '';
        if(empty($arrayParams['initInfo']['regist_winner_item']) == false){
            $whereQueryString .= " AND ew_event_gift = '{$arrayParams['initInfo']['regist_winner_item']}' ";
        }

        //리뷰 당첨 이벤트 중복인 경우 :: 당월만 체크
        if($arrayParams['initInfo']['chk_review'] == 'Y'){
            $whereQueryString .= " AND ew_regdatetime >= DATE_FORMAT(NOW(),'%Y%m01000000') ";
        }

        $sql            = "SELECT * FROM event_winner_tb WHERE ew_event_num = ? AND ew_contact = ? {$whereQueryString} ; ";

        $aBindParams    = array($arrayParams['initInfo']['e_num'],$arrayParams['setData']['cel_tel']);
        $oResult        = $this->db->query($sql,$aBindParams);
        $nResult        = $oResult->num_rows();

        if($nResult > 0){ //중복
            return true;
        }else{
            return false;
        }

    }//end of overlapWinner()

    function setCancelWinner($arrayParams){

        for ($i = 0; $i <  count($arrayParams['setData']); $i++) {
            $row = $arrayParams['setData'][$i];

            if(empty($row['m_num']) == true || empty($arrayParams['initInfo']['regist_winner_item']) == true || empty($arrayParams['initInfo']['e_num']) == true ){
                continue;
            }

            $sql = "DELETE FROM event_winner_tb
                    WHERE ew_member_num = '{$row['m_num']}' 
                    AND ew_event_gift = '{$arrayParams['initInfo']['regist_winner_item']}' 
                    AND ew_event_num = '{$arrayParams['initInfo']['e_num']}'
                    ORDER BY ew_num DESC 
                    LIMIT 1; 
            ";

            $this->db->query($sql);

        }

    }

    function setWinner($arrayParams) {

        $aValue = array();

        for ($i = 0; $i <  count($arrayParams['setData']); $i++) { $row = $arrayParams['setData'][$i];

            $m_num = $row['m_num']?$row['m_num']:'0';

            if($row['is_overlap'] == false) $aValue[] = "('0','0','당첨','{$arrayParams['initInfo']['e_num']}','{$m_num}','0','{$row['cel_tel']}','N','','{$arrayParams['initInfo']['reg_date']}','','2','{$arrayParams['initInfo']['regist_winner_item']}')";
        }

        if(count($aValue) > 0){

            $this->db->trans_begin();

            $strValue = implode(',',$aValue);
            $strValue = $strValue.';';

            $sql = "INSERT INTO event_winner_tb (
                  ew_type
                , ew_type_detail
                , ew_type_text
                , ew_event_num
                , ew_member_num
                , ew_event_active_num
                , ew_contact
                , ew_view_yn
                , ew_gift
                , ew_regdatetime
                , ew_updatetime
                , ew_state
                , ew_event_gift
            ) VALUES {$strValue}";

            $this->db->query($sql);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aResult = array('success' => false , 'msg' => '당첨자를 등록하는 중 문제가 발생하였습니다.[TransRollback OK]');
            } else {
                $this->db->trans_commit();
                $aResult = array('success' => true , 'msg' => '당첨자 등록완료');
            }

        }else{
            $aResult = array('success' => false , 'msg' => '등록할 당첨자가 없습니다.[DB - NoData]');
        }

        return $aResult;

    }//end of setWinner()


    public function get_event_winner_cnt($arrayParams){

        $sql = "
                SELECT 
                count(*) AS cnt 
                FROM event_tb ET
                INNER JOIN event_winner_tb EWT ON EWT.ew_event_num = ET.e_num
                WHERE ET.e_code = '{$arrayParams['where']['div']}' ;      
        ";

        $oRet = $this->db->query($sql);
        $aRet = $oRet->row_array();

        return $aRet['cnt'];

    }

    public function get_event_viewer_cnt($arrayParams){

        $sql = "
                SELECT 
                count(*) AS cnt 
                FROM event_tb ET
                INNER JOIN event_gift_tb EGT ON EGT.eg_event_num = ET.e_num
                WHERE ET.e_code = '{$arrayParams['where']['div']}' AND eg_view = 'Y' ;      
        ";

        $oRet = $this->db->query($sql);
        $aRet = $oRet->row_array();

        return $aRet['cnt'];

    }

}//end of class Event_active_model
