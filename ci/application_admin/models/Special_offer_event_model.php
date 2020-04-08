<?php
/**
 * 특가 이벤트 관련 모델
 */
class Special_offer_event_model extends A_Model {

    public $event_num = '14';
    public $event_code = 'event_20180130';

    public function __construct(){
        parent::__construct();
    }//end of __construct()


    public function get_special_offer_join_list($query_array=array(), $start="", $end="", $is_count=false, $DB=false) {


        $event_num = $this->event_num;

        //from 절
        $from_query  = " from event_special_offer_tb ";
        $from_query .= " inner join member_tb mt on mt.m_num = ens_member_num ";
        $from_query .= " LEFT JOIN event_winner_tb ew  ON ens_num = ew.ew_event_active_num AND ew.ew_state IN (2,3) AND ew_event_num = 14 ";

        //where 절
        $where_query = " where 1 = 1 ";
        $order_query = " ORDER BY ens_regdatetime DESC ";


        if($query_array['where']['kfd'] && $query_array['where']['kwd']){
            $where_query .= " AND {$query_array['where']['kfd']} LIKE '%{$query_array['where']['kwd']}%' ";
        }

        if($query_array['where']['ens_week_num'] != ''){
            $where_query .= " AND ens_week_num = '{$query_array['where']['ens_week_num']}' ";
        }

        if($query_array['where']['winner_flag'] != ''){
            $where_query .= $query_array['where']['winner_flag']=='Y'?' AND ew_num IS NOT NULL ':' AND ew_num IS NULL ';
        }

        if($query_array['where']['date1'] != ''){
            $where_query .= " AND left(ens_regdatetime,8) >= '{$query_array['where']['date1']}' ";
        }

        if($query_array['where']['date2'] != ''){
            $where_query .= " AND left(ens_regdatetime,8) <= '{$query_array['where']['date2']}' ";
        }

//
//        if($query_array['where']['kwd']){
//
//            if($query_array['where']['kfd']){ //필드 선택
//                $where_query .= " AND {$query_array['where']['kfd']} LIKE '%{$query_array['where']['kwd']}%' ";
//            }else{
//                $where_query .= " AND ( thema_name LIKE '%{$query_array['where']['kwd']}%' ) ";
//            }
//
//        }
//
//        //order by 절
//        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
//            $order_query = "order by " . $query_array['orderby'] . " ";
//        }
//        else {
//            $order_query = " ORDER BY seq DESC ";
//        }

        //limit 절
        $limit_query = "";
        if( $start !== "" && $end !== "" ) {
            $limit_query .= " limit " . $start . ", " . $end . " ";
        }

        //갯수만 추출
        if( $is_count === TRUE ) {
            $query = "select count(*) cnt ";
            $query .= $from_query;
            $query .= $where_query;

            if( !empty($DB) ) {
                return $DB->query($query)->row('cnt');
            }
            else {
                return $this->db->query($query)->row('cnt');
            }
        }
        //데이터 추출
        else {
            $query = "select * 
                        , DATE_FORMAT(ens_regdatetime, '%Y.%m.%d %H:%i:%s') AS ens_regdatetime_str 
                        , CONCAT( '=\"' , ens_ph, '\"' )    AS ens_ph_str
                        , CASE WHEN (SELECT m_order_count FROM member_tb WHERE ens_ph = m_authno ) > 0
                               THEN (SELECT m_order_count FROM member_tb WHERE ens_ph = m_authno )
                               ELSE 0 END AS order_cnt 
                        , ens_reg_info                      AS ens_reg_info_str
                        , (SELECT e_subject FROM event_tb WHERE e_num = {$event_num} ) as e_subject
                        , CASE WHEN ( SELECT IFNULL(ew_num,0) FROM event_winner_tb ew WHERE ens_num = ew.ew_event_active_num AND ew_state IN (2,3) AND ew_event_num = {$event_num}  ) > 0 THEN '당첨' ELSE '당첨안됨' END AS win_overlap
                        ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;

            //zsView($query);
            //echo $query;

            if( !empty($DB) ) {
                return $DB->query($query)->result();
            }
            else {
                return $this->db->query($query)->result();
            }
        }


    }

    public function get_special_offer_event(){

        $event_num = $this->event_num;

        $sql = " SELECT 
                 * 
                 , (SELECT gift_name FROM event_gift_code_tb WHERE seq = ostb.gift_code_seq) AS gift_name
                 , (SELECT 
                      COUNT(*)
                      FROM event_gift_code_tb gctb
                      INNER JOIN event_gift_tb gtb ON gtb.eg_event_gift = gctb.gift_code AND  gtb.eg_event_num = ?
                      WHERE gctb.seq = ostb.gift_code_seq AND gtb.eg_state = 1 
                  ) AS quantity_left
                 FROM event_special_offer_setting_tb ostb ; ";
        $oResult = $this->db->query($sql,array($event_num));
        $aResult = $oResult->result_array();

        return $aResult;

    }

    public function get_special_offer_event_row($seq){


        $event_num = $this->event_num;


        $sql = " SELECT 
                   *  
                 , (SELECT gift_name FROM event_gift_code_tb WHERE seq = ostb.gift_code_seq) AS gift_name
                 , (SELECT 
                      COUNT(*)
                      FROM event_gift_code_tb gctb
                      INNER JOIN event_gift_tb gtb ON gtb.eg_event_gift = gctb.gift_code AND  gtb.eg_event_num = ?
                      WHERE gctb.seq = ostb.gift_code_seq
                  ) AS quantity_left
                 FROM event_special_offer_setting_tb ostb
                 WHERE ostb.seq = ? ; 
         ";
        $oResult = $this->db->query($sql,array($event_num,$seq));
        $aResult = $oResult->row_array();

        return $aResult;

    }


    public function update_special_offer_gfit($arrayParams){

        $aBindParams = array(
            $arrayParams['gift_img']
        ,   $arrayParams['gift_img']
        ,   $arrayParams['confirm_img']
        ,   $arrayParams['confirm_img']
        ,   $arrayParams['gift_img_sm']
        ,   $arrayParams['gift_img_sm']
        ,   $arrayParams['confirm_msg']
        ,   $arrayParams['confirm_url']
        ,   $arrayParams['winner_limit']
        ,   $arrayParams['gift_code_seq']
        ,   $arrayParams['alimtalk_url']

        ,   $arrayParams['confirm_url_ios']
        ,   $arrayParams['confirm_url_top_tap']
        ,   $arrayParams['confirm_img_ios']
        ,   $arrayParams['confirm_img_ios']
        ,   $arrayParams['confirm_msg_ios']
        ,   $arrayParams['confirm_url_top_tap_ios']
        ,   $arrayParams['seq']
        );



        $sql = "UPDATE event_special_offer_setting_tb SET
                  gift_img    = CASE WHEN ? = '' THEN gift_img ELSE ? END
                , confirm_img = CASE WHEN ? = '' THEN confirm_img ELSE ? END
                , gift_img_sm = CASE WHEN ? = '' THEN gift_img_sm ELSE ? END
                , confirm_msg = ?
                , confirm_url = ?
                , winner_limit = ?
                , gift_code_seq = ?
                , alimtalk_url = ?
                
                , confirm_url_ios = ?
                , confirm_url_top_tap = ?
                
                , confirm_img_ios = CASE WHEN ? = '' THEN confirm_img_ios ELSE ? END
                , confirm_msg_ios = ?
                , confirm_url_top_tap_ios = ?
                WHERE seq     = ? ;
        ";

        $this->db->query($sql , $aBindParams);

    }

    public function get_gift_list(){

        $event_code = $this->event_code;

        $sql = " SELECT * FROM event_gift_code_tb WHERE event_code = ? ; ";

        $oResult = $this->db->query($sql, array($event_code));
        $aResult = $oResult->result_array();

        return $aResult;

    }

}//end of class Product_model
