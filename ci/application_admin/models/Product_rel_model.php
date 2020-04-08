<?php
/**
 * 연관상품 관련모델
 */
class Product_rel_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()


    /**
     * 연관상품 목록 추출
     * @param array $query_array : 쿼리배열
     * @param string $start : limit $start, $end
     * @param string $end : limit $start, $end
     * @param bool $is_count : 전체갯수만 추출여부
     * @param bool $DB
     * @return
     */
    public function get_product_rel_list($query_array=array(), $start="", $end="", $is_count=false, $DB=false) {

        //from 절
        $from_query = " from product_tb pt ";
        //$from_query .= "left join product_md_tb MD on pmd_product_num = p_num ";

        //where 절
        $where_query  = " where 1 = 1 ";

        //진열상태 (배열)
        if( isset($query_array['where']['prod_display_state']) && !empty($query_array['where']['prod_display_state']) ) {
            //배열일때
            if( is_array($query_array['where']['prod_display_state']) ) {
                $display_state_array = array();
                foreach($query_array['where']['prod_display_state'] as $key => $item) {
                    $display_state_array[] = "p_display_state = '" . $this->db->escape_str($item) . "'";
                }

                $where_query .= "and (".implode(" or ", $display_state_array).") ";
            }
            //배열아닐때
            else {
                $where_query .= "and p_display_state = '" . $this->db->escape_str($query_array['where']['prod_display_state']) . "' ";
            }
        }
        //판매상태 (배열)
        if( isset($query_array['where']['prod_sale_state']) && !empty($query_array['where']['prod_sale_state']) ) {
            //배열일때
            if( is_array($query_array['where']['prod_sale_state']) ) {
                $sale_state_array = array();
                foreach($query_array['where']['prod_sale_state'] as $key => $item) {
                    $sale_state_array[] = "p_sale_state = '" . $this->db->escape_str($item) . "'";
                }

                $where_query .= "and (".implode(" or ", $sale_state_array).") ";
            }
            //배열아닐때
            else {
                $where_query .= "and p_sale_state = '" . $this->db->escape_str($query_array['where']['prod_sale_state']) . "' ";
            }
        }


        $having_fd    = "";
        $having_query = " HAVING 1 = 1 ";

        $having_query .= " AND ((p_sale_state = 'Y' AND p_stock_state = 'Y') OR ( rel_cnt > 0 )) ";

        //연관상품 여부
        if( isset($query_array['where']['rel_yn']) && !empty($query_array['where']['rel_yn']) ) {
            if( $query_array['where']['rel_yn'] == 'Y' ) {
                $having_query .= "and able_cnt > 0 ";
            }else if( $query_array['where']['rel_yn'] == 'N'){
                $having_query .= "and able_cnt < 1 ";
            }
        }

        //키워드
        if( isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd']) ) {

            if(isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd'])){

                if($query_array['where']['kfd'] == 'p_name'){
                    $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                }else{

                    $having_fd .= ",    ( 	SELECT 
                                            COUNT(*) 
                                            FROM product_relation_tb T1
                                            INNER JOIN product_tb T2 ON T1.c_pnum = T2.p_num
                                            WHERE T1.p_pnum = pt.p_num AND T2.p_name LIKE '%{$query_array['where']['kwd']}%'
                                        ) AS rel_srh_cnt
                    ";

                    $having_query .= " AND rel_srh_cnt > 0  ";
                }

            }else{

                $having_query   .= " AND ( rel_srh_cnt > 0 OR p_name like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ) ";
                $having_fd      .= ",( 	SELECT 
                                        COUNT(*) 
                                        FROM product_relation_tb T1
                                        INNER JOIN product_tb T2 ON T1.c_pnum = T2.p_num
                                        WHERE T1.p_pnum = pt.p_num AND T2.p_name LIKE '%{$query_array['where']['kwd']}%'
                                    ) AS rel_srh_cnt
                ";

            }

        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            //$order_query = "order by able_cnt desc ";
            $order_query = "order by p_num desc, p_regdatetime desc ";
        }

        //limit 절
        $limit_query = "";
        if( $start !== "" && $end !== "" ) {
            $limit_query .= "limit " . $start . ", " . $end . " ";
        }

        //갯수만 추출
        if( $is_count === TRUE ) {

            $query = "SELECT COUNT(*) cnt   
                      FROM (
                          SELECT
                          *
                          , ( SELECT 
                                COUNT(*)
                                FROM product_relation_tb prt
                                INNER JOIN product_tb pt2 ON prt.c_pnum = pt2.p_num
                                WHERE p_pnum = pt.p_num 
                                AND prt.view_yn = 'Y'
                                AND pt2.p_display_state = 'Y' 
                                AND pt2.p_sale_state = 'Y' 
                                AND pt2.p_stock_state = 'Y'
                            ) AS able_cnt
                            , ( SELECT 
                                COUNT(*) 
                                FROM product_relation_tb T1
                                WHERE T1.p_pnum = pt.p_num
                            ) AS rel_cnt
                          {$having_fd} 
                          FROM product_tb pt  
                          {$where_query}
                          {$having_query}
                      ) T
            ";


            if( !empty($DB) ) {
                return $DB->query($query)->row('cnt');
            }
            else {
                return $this->db->query($query)->row('cnt');
            }
        }
        //데이터 추출
        else {
            $query = "select * ";
            $query .= " , ( SELECT 
                            COUNT(*)
                            FROM product_relation_tb prt
                            INNER JOIN product_tb pt2 ON prt.c_pnum = pt2.p_num
                            WHERE p_pnum = pt.p_num 
                            AND prt.view_yn = 'Y'
                            AND pt2.p_display_state = 'Y' 
                            AND pt2.p_sale_state = 'Y' 
                            AND pt2.p_stock_state = 'Y'
                        ) AS able_cnt
                        , ( SELECT 
                            COUNT(*) 
                            FROM product_relation_tb T1
                            WHERE T1.p_pnum = pt.p_num
                        ) AS rel_cnt
            ";
            $query .= $having_fd;
            $query .= $from_query;
            $query .= $where_query;
            $query .= $having_query;
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

    /**
     * 연관상품 조회
     * @param $seq    : 상품번호(product_relation_tb.seq)
     * @param bool $DB  : DB 연결 객체
     * @return mixed
     */
    public function get_product_rel_row($seq, $DB=false) {

    }

    public function get_product_rel_info($p_num , $is_count = false){

        if($is_count == true){//판매중/판매중아님 카운트

            $sql = "  SELECT  
                          SUM( CASE WHEN ( B.p_display_state = 'Y' AND B.p_sale_state = 'Y' AND B.p_stock_state = 'Y' ) OR ( B.p_termlimit_datetime1 > DATE_FORMAT(NOW(),'%Y%m%d%H%i%s') ) THEN 1 ELSE 0 END ) AS able_cnt
                        , SUM( CASE WHEN ( B.p_display_state = 'Y' AND B.p_sale_state = 'Y' AND B.p_stock_state = 'Y' ) OR ( B.p_termlimit_datetime1 > DATE_FORMAT(NOW(),'%Y%m%d%H%i%s') ) THEN 0 ELSE 1 END ) AS disable_cnt   
                      FROM product_relation_tb A
                      INNER JOIN product_tb B ON A.c_pnum = B.p_num
                      WHERE p_pnum = '{$p_num}' 
                      AND view_yn = 'Y' ; 
            ";

            $oResult = $this->db->query($sql);
            $aResult = $oResult->row_array();
            $oResult->free_result();

        }else{//리스트

            $sql = "  SELECT  A.* 
                            , B.p_display_state 
                            , B.p_sale_state 
                            , B.p_stock_state 
                            , B.p_name
                            , B.p_rep_image
                            , B.p_num
                            , B.p_order_code
                            #, CASE WHEN B.p_display_state = 'Y' AND B.p_sale_state = 'Y' AND B.p_stock_state = 'Y' THEN 'Y' ELSE 'N' END AS isAble
                            #, CASE WHEN B.p_termlimit_datetime1 > DATE_FORMAT(NOW(),'%Y%m%d%H%i%s') THEN 'Y' ELSE 'N' END AS reserve_prod
                            , CASE WHEN ( B.p_display_state = 'Y' AND B.p_sale_state = 'Y' AND B.p_stock_state = 'Y' ) OR ( B.p_termlimit_datetime1 > DATE_FORMAT(NOW(),'%Y%m%d%H%i%s') ) THEN 'Y' ELSE 'N' END AS isAble
                            
                            
                      FROM product_relation_tb A
                      INNER JOIN product_tb B ON A.c_pnum = B.p_num
                      WHERE p_pnum = '{$p_num}'
                      ORDER BY view_yn ASC , isAble DESC , A.sort_num , A.seq ; 
            ";

            $oResult = $this->db->query($sql);
            $aResult = $oResult->result_array();
            $oResult->free_result();

        }

        return $aResult;

    }

    public function set_product_sort($arrayParams){

        if(count($arrayParams['p_num_arr']) > 0){

            $sql = " SELECT c_pnum FROM product_relation_tb WHERE p_pnum = '{$arrayParams['p_pnum']}' AND view_yn = 'Y' ; ";
            $oResult = $this->db->query($sql);
            $aResult = $oResult->result_array();

            $tmpPnum = [];
            foreach ($aResult as $row) {
                $tmpPnum[$row['c_pnum']]++;
            }

            foreach ($arrayParams['p_num_arr'] as $v) {
                $tmpPnum[$v]++;
            }

            if(count($tmpPnum) > 12){
                return array('success' => false ,'msg' => '현재 선택된 상품은 12개 이상 연관상품이 지정되게 됩니다. 확인 후 다시 시도해주세요.');
            }

            $chk_arr = array();

            foreach ($arrayParams['p_num_arr'] as $k => $v) { $sort_num = $k+1;

                $sql        = "SELECT COUNT(*) AS cnt FROM product_relation_tb WHERE p_pnum = '{$arrayParams['p_pnum']}' AND c_pnum = '{$v}' ; ";
                $oResult    = $this->db->query($sql);
                $cnt        = $oResult->row_array();

                if($cnt['cnt'] < 1){ //기존에 없던 상품 insert

                    $sql = "  INSERT INTO product_relation_tb
                              SET   p_pnum    = '{$arrayParams['p_pnum']}'
                                ,   c_pnum    = '{$v}'
                                ,   sort_num  = '{$sort_num}'
                                ,   view_yn   = 'Y'
                                ,   reg_date  = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s') ;
                    ";
                    $chk_arr[$v] = 'insert';

                }else{ //기존에 있던 상품 upadte

                    $sql = "  UPDATE product_relation_tb
                              SET   sort_num  = '{$sort_num}'
                                ,   view_yn   = 'Y'
                                ,   mod_date  = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
                              WHERE p_pnum = '{$arrayParams['p_pnum']}' AND c_pnum = '{$v}' ;
                    ";
                    $chk_arr[$v] = 'update';

                }

                $this->db->query($sql);

            }


        }

        return array('success' => true ,'msg' => '');

    }

    public function get_product_sort($arrayParams){

        if(empty($arrayParams['sort']) == true) $arrayParams['sort'] = 'desc';

        if($arrayParams['sort_type'] == 'sum_sales_price'){

            $_09sns = $this->get_db('09sns');

            $sql        = " SELECT IFNULL(SUM(A.op_price * A.op_cnt) , 0 ) AS sum_sales_price , C.p_code
                            FROM smart_product C
                            LEFT JOIN smart_order_product A ON A.op_pcode = C.p_code 
                            LEFT JOIN smart_order B ON A.op_oordernum = B.o_ordernum AND B.o_paystatus = 'Y' AND B.o_canceled = 'N'
                            WHERE C.p_code IN ?
                            GROUP BY C.p_code
                            ORDER BY sum_sales_price {$arrayParams['sort']}
            ";

            $oResult = $_09sns->query($sql,array($arrayParams['p_order_code_arr']));
            $aResult = $oResult->result_array();

        }else if($arrayParams['sort_type'] == 'isAble'){



        }




        return $aResult;

    }
    public function item_drop($seq){

        $sql = "UPDATE product_relation_tb SET view_yn = 'N', sort_num = 9999 , mod_date = DATE_FORMAT(NOW(),'%Y%m%d%H%i%s') WHERE seq = '{$seq}' ; ";
        return $this->db->query($sql);

    }

}//end of class Product_rel_model


