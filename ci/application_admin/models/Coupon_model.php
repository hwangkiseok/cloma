<?php
/**
 * 특가 관련 모델
 */
class Coupon_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()



    /**
     * 쿠폰 목록 추출
     * @param array $query_array : 쿼리배열
     * @param string $start : limit $start, $end
     * @param string $end : limit $start, $end
     * @param bool $is_count : 전체갯수만 추출여부
     * @param bool $DB
     * @return
     */

    public function get_coupon_lists($query_array=array(), $start="", $end="", $is_count=false, $DB=false){

        //from 절
        $from_query = " from coupon_info_tb cit ";

        //where 절
        $where_query = " where 1 = 1 ";

//        if($query_array['where']['activate_flag']){
//            $where_query .= " AND activate_flag = '{$query_array['where']['activate_flag']}' ";
//        }
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

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = " ORDER BY seq DESC ";
        }

        if( isset($query_array['where']['issue_type']) && !empty($query_array['where']['issue_type']) ) {
            $where_query .= " AND issue_type = '{$query_array['where']['issue_type']}' ";
        }

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
            $query = "select 
                        *
                      , ( SELECT COUNT(*) FROM coupon_list_tb WHERE p_seq = cit.seq ) AS coupon_cnt 
                      , ( SELECT coupon_name FROM coupon_list_tb WHERE p_seq = cit.seq LIMIT 1 ) AS coupon_name
                      , ( SELECT coupon_code FROM coupon_list_tb WHERE p_seq = cit.seq LIMIT 1 ) AS coupon_code
            ";
            $query .= $from_query;
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;

            //echo $query;

            if( !empty($DB) ) {
                return $DB->query($query)->result_array();
            }
            else {
                return $this->db->query($query)->result_array();
            }
        }

    }


    public function get_coupon_info_row($arrayParams){

        $sql = "SELECT 
                  *
                , ( SELECT GROUP_CONCAT(coupon_seq) FROM coupon_list_tb WHERE p_seq = cit.seq ) AS coupon_seq_str
                FROM coupon_info_tb cit
                WHERE seq = ? 
        ";
        $oResult = $this->db->query($sql,array($arrayParams['seq']));
        $aResult = $oResult->row_array();

        return $aResult;

    }

    public function getConditionAlimtalkProc(){

        $_db_push_09 = $this->get_db('db_push_09');
        $sql = " SELECT mass_alimtalk FROM process_tb ";
        $oResult = $_db_push_09->query($sql);
        $aResult = $oResult->row_array();

        return  $aResult['mass_alimtalk'];
    }

    public function setBlockAlimtalk($arrayParams){

        $_db_push_09 = $this->get_db('db_push_09');

        $sql = "SELECT count(*) as cnt FROM smart_block_alimtalk WHERE use_flag <> 'D' AND p_seq = '{$arrayParams['seq']}' AND inid = '{$arrayParams['inid']}'  ";
        $oResult = $_db_push_09->query($sql);
        $aResult = $oResult->row_array();
        $oResult->free_result();

        if($aResult['cnt'] > 0){
            $result = array('success' =>false , 'msg' => '이미 발송 중인 알림톡이 있습니다.', 'data' => '');
            return $result;
        }

        $bigo = urldecode($arrayParams['contents']);

        $sql = "INSERT INTO smart_block_alimtalk SET
                inid        = '{$arrayParams['inid']}'
             ,  start_date  = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
             ,  p_seq       = '{$arrayParams['seq']}'
             ,  need_params = '{$arrayParams['code']}'
             ,  tpl_code    = '{$arrayParams['tpl_code']}'
             ,  target      = '{$arrayParams['send_type']}'
             ,  bigo        = '{$bigo}' 
        ";

        $bRet = $_db_push_09->query($sql);

        $result = array('success' =>$bRet , 'msg' => '', 'data' => '');
        return $result;

    }



    public function set_coupon_activate($arrayParams){

        $sql = "UPDATE coupon_info_tb SET use_flag = ? , mod_date = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s') WHERE seq = ? ; ";

        $aBindParams = array($arrayParams['set_flag'],$arrayParams['seq']);
        $bRet = $this->db->query($sql,$aBindParams);
        return $bRet;

    }

    public function coupon_insert($arrayParams){

        $this->db->trans_begin(); //트랜잭션 시작

        $sql = "INSERT INTO coupon_info_tb SET
                  start_date = ?
                , end_date = ?
                , issue_type = ?
                , reg_date = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s') ;
        ";
        $aBindParams = array($arrayParams['start_date'],$arrayParams['end_date'],$arrayParams['issue_type']);
        $this->db->query($sql,$aBindParams);

        $sql = "SELECT LAST_INSERT_ID() AS last_seq";
        $o_last_row = $this->db->query($sql);
        $last_row = $o_last_row->row_array();

        $arrayParams['p_seq'] = $last_row['last_seq'];

        $this->upsert_conpon_lists($arrayParams);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $bRet = false;
        } else {
            $this->db->trans_commit();
            $bRet = true;
        }//트랜잭션 종료


        return $bRet;
    }

    public function coupon_update($arrayParams){

        $this->db->trans_begin(); //트랜잭션 시작

        $sql = "UPDATE coupon_info_tb SET
                  start_date = ?
                , end_date = ?
                , mod_date = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                WHERE seq = ? ;
        ";
        $aBindParams = array($arrayParams['start_date'],$arrayParams['end_date'],$arrayParams['seq']);
        $this->db->query($sql,$aBindParams);

        $arrayParams['p_seq'] = $arrayParams['seq'];

        $this->upsert_conpon_lists($arrayParams);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $bRet = false;
        } else {
            $this->db->trans_commit();
            $bRet = true;
        }//트랜잭션 종료

        return $bRet;
    }



    public function delete_coupon($arrayParams){


        $sql = "DELETE FROM coupon_list_tb WHERE p_seq = ? ";
        $this->db->query($sql,array($arrayParams['seq']));

        $sql = "DELETE FROM coupon_info_tb WHERE seq = ? ";
        $ret = $this->db->query($sql,array($arrayParams['seq']));

        return $ret;

    }



    public function send_alimtalk_log($arrayParams,$flag){

        $sql = "UPDATE coupon_info_tb SET
                  alimtalk_flag = ?
                , alimtalk_send_date = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                WHERE seq = ? ;
        ";
        $aBindParams = array($flag,$arrayParams['seq']);
        $ret = $this->db->query($sql,$aBindParams);

        return $ret;

    }


    private function upsert_conpon_lists($arrayParams){

        if( $arrayParams['mode'] == 'update'){
            $sql = " DELETE FROM coupon_list_tb WHERE p_seq = ? ; ";
            $this->db->query($sql,array($arrayParams['p_seq']));
        }

        $aCouponInfo = $arrayParams['coupon_info'];

        foreach ($aCouponInfo as $v) {
            $val_arr = explode('::',$v);

            $cpn_code = $val_arr[0];
            $cpn_uid = $val_arr[1];
            $cpn_name = $val_arr[2];
            $cpn_issue_type = $val_arr[3];

            $sql = "INSERT INTO coupon_list_tb SET 
                      p_seq       = ?
                    , coupon_code = ?
                    , coupon_seq  = ?
                    , coupon_name = ?
                    , coupon_issue_type = ?
            ";

            $aBindParams = array($arrayParams['p_seq'],$cpn_code,$cpn_uid,$cpn_name,$cpn_issue_type);
            $this->db->query($sql,$aBindParams);

        }

    }

}//end of class Product_model