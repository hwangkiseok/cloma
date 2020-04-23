<?php
/**
 * 메인상품진열관련 모델
 */
class Main_thema_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 상품 목록 추출
     * @param array $query_array : 쿼리배열
     * @param string $start : limit $start, $end
     * @param string $end : limit $start, $end
     * @param bool $is_count : 전체갯수만 추출여부
     * @param bool $DB
     * @return
     */
    public function get_main_thema_list($query_array=array(), $start="", $end="", $is_count=false, $DB=false) {
        //from 절
        $from_query = "from main_thema_tb ";
        //$from_query .= "left join product_md_tb MD on pmd_product_num = p_num ";

        //where 절
        $where_query = "where 1 = 1 ";


        if($query_array['where']['activate_flag']){
            $where_query .= " AND activate_flag = '{$query_array['where']['activate_flag']}' ";
        }

        if($query_array['where']['kwd']){

            if($query_array['where']['kfd']){ //필드 선택
                $where_query .= " AND {$query_array['where']['kfd']} LIKE '%{$query_array['where']['kwd']}%' ";
            }else{
                $where_query .= " AND ( thema_name LIKE '%{$query_array['where']['kwd']}%' ) ";
            }

        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            $order_query = " ORDER BY seq DESC ";
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
                return $DB->query($query)->row_array('cnt');
            }
            else {
                return $this->db->query($query)->row_array('cnt');
            }
        }
        //데이터 추출
        else {
            $query = "select * ";
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
    }//end of get_product_list()

    public function get_activate_lists(){

        $sql = "  SELECT 
                  * 
                  FROM main_thema_tb
                  WHERE activate_flag = 'Y'
                  AND ( view_type = 'B' OR ( view_type = 'A' AND start_date <= DATE_FORMAT(NOW(),'%Y%m%d') AND end_date >= DATE_FORMAT(NOW(),'%Y%m%d') ) )    
                  
                  ORDER BY sort_num ASC
              ";
        $oResult = $this->db->query($sql);

        $aResult = $oResult->result_array();
        return $aResult;

    }

    public function get_main_thema_info($seq){

        $sql        = "SELECT * FROM main_thema_tb WHERE seq = ? ";
        $oResult    = $this->db->query($sql, array($seq));
        $main_thema_row    = $oResult->row_array();

        $sql        = " SELECT * 
                        FROM main_thema_product_tb sop
                        INNER JOIN product_tb pt ON pt.p_num = sop.p_num 
                        WHERE sop.parent_seq = ? 
                        ORDER BY sop.sort_num ASC ;
        ";
        $oResult    = $this->db->query($sql, array($seq));
        $main_thema_product_lists    = $oResult->result_array();

        $aResult = array(
            'main_thema_row'             => $main_thema_row
        ,   'main_thema_product_lists'   => $main_thema_product_lists
        );

        return $aResult;

    }

    public function get_product_lists(){

        $sql = " SELECT 
                * 
                FROM product_tb 
                WHERE p_sale_state = 'Y' 
                AND ((p_display_state = 'N' AND LEFT(p_termlimit_datetime1,8) >= DATE_FORMAT(NOW(),'%Y%m%d')) OR p_display_state = 'Y' ) ; ";
        $oResult = $this->db->query($sql);
        return $oResult->result_array();

    }

    public function set_sorting($seq_arr) {

        $not_in_arr = array();
        foreach ($seq_arr as $sort_num => $seq) {

            $aBindParams = array($sort_num+1,$seq);
            $sql = " UPDATE main_thema_tb SET sort_num = ? WHERE seq = ? ; " ;
            $this->db->query($sql,$aBindParams);
            $not_in_arr[] = $seq;
        }

        $sql = "UPDATE main_thema_tb SET sort_num = 99999999 WHERE seq NOT IN ? ";
        $this->db->query($sql,array($not_in_arr));

    }

    public function insert_main_thema($arrayParams) {

        $sql = " SELECT IFNULL(MAX(sort_num),0) AS sort_max FROM main_thema_tb ";
        $oResult = $this->db->query($sql);
        $max_sort = $oResult->row_array();

        $sort_num = $max_sort['sort_max']+1;

        $sql = "INSERT INTO main_thema_tb SET
                  thema_name    = ?
                , sort_num      = ?
                , activate_flag = ?
                , view_type     = ?
                , start_date    = ?
                , end_date      = ?
                , display_type  = ?
                , reg_date      = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
        ";

        $aBindParams = array(
              $arrayParams['thema_name']
            , $sort_num
            , $arrayParams['activate_flag']
            , $arrayParams['view_type']
            , $arrayParams['start_date']
            , $arrayParams['end_date']
            , $arrayParams['display_type']
        );

        $this->db->query($sql,$aBindParams);
        $insert_id = $this->db->insert_id();
        $this->upsert_product($arrayParams['p_num_arr'],$insert_id);

    }

    public function update_main_thema($arrayParams) {

        $sql = "UPDATE main_thema_tb SET
                  thema_name    = ?
                , activate_flag = ?
                , view_type     = ?
                , start_date    = ?
                , end_date      = ?
                , display_type  = ?
                , mod_date      = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                
                WHERE seq = ?
                
        ";

        $aBindParams = array(
              $arrayParams['thema_name']
            , $arrayParams['activate_flag']
            , $arrayParams['view_type']
            , $arrayParams['start_date']
            , $arrayParams['end_date']
            , $arrayParams['display_type']
            , $arrayParams['seq']

        );

        $this->db->query($sql,$aBindParams);
        $this->upsert_product($arrayParams['p_num_arr'],$arrayParams['seq']);

    }

    public function set_activate($arrayParams){

        $sql = "
            UPDATE main_thema_tb SET
              activate_flag = ?
            , mod_date      = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
            WHERE seq = ? ;
        ";

        $aBindParams = array($arrayParams['set_flag'],$arrayParams['seq']);

        $bRet = $this->db->query($sql , $aBindParams);
        return $bRet;

    }
    public function delete($arrayParams){

        $aBindParams = array($arrayParams['seq']);

        $sql = " DELETE FROM main_thema_tb WHERE seq = ? ; ";
        $bRet = $this->db->query($sql , $aBindParams);

        $sql = " DELETE FROM main_thema_product_tb WHERE parent_seq = ? ; ";
        $bRet = $this->db->query($sql , $aBindParams);

        return $bRet;

    }

    private function upsert_product($p_num_arr,$insert_id){

        //기존 상품 초기화
        $delete_sql = "DELETE FROM main_thema_product_tb WHERE parent_seq = ? ";
        $this->db->query($delete_sql,array($insert_id));

        foreach ($p_num_arr as $k => $p_num) {

            $sort_num = $k+1;

            $sql = "INSERT INTO main_thema_product_tb
                (parent_seq , p_code , p_num , sort_num , reg_date ) 
                VALUES
                ( ? 
                , ( SELECT p_order_code FROM product_tb WHERE p_num = ? )
                , ?
                , ?
                , DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                ) ;
            ";

            $aBindParams = array($insert_id,$p_num,$p_num,$sort_num);
            $this->db->query($sql,$aBindParams);

        }

    }

}//end of class Product_model