<?php
/**
 */
class Cron_model extends A_Model
{

    public function __construct()
    {
        parent::__construct();

        $this->curr_time = date('Hi');
        $this->curr_time_i = date('i');
        $this->curr_time_h = date('H');

    }//end of __construct()


    /**
     * @date 180207
     * @modify 황기석
     * @desc 현재 시간에 실행할 cron select
     * @param 현재 시간:분
     */
    public function get_cron()
    {

        $sql = "SELECT * FROM cron_tb WHERE ( cron_time = '{$this->curr_time}' AND use_flag = 'Y' ) OR ( `continue` = 'Y' AND use_flag = 'Y' ) ORDER BY sort ASC; ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        return $aResult;

    } //end of function get_cron

    /**
     * @date 200302
     * @modify 황기석
     * @desc 메인페이지 상품노출 proc
     */
    public function proc_main_product($debug)
    {

        $chk_time = array( '0800' , '1200' , '1600' , '2000');

        if(in_array($this->curr_time,$chk_time) == true) { //초기화 후 재 등록

            $new_cnt = 3;
            $best_cnt = 27;

            {//신상품 3개
                $sql = "SELECT * 
                            , 'new' AS gubun 
                        FROM product_tb 
                        WHERE p_display_state = 'Y' 
                        AND p_sale_state = 'Y' 
                        AND p_stock_state = 'Y' 
                        ORDER BY p_termlimit_datetime1 DESC 
                        LIMIT {$new_cnt}; 
                ";
                $oResult = $this->db->query($sql);
                $aProductList_New = $oResult->result_array();
            }

            { //최근 30분동안 가장 잘팔린 상위 7개 상품

                //신규상품 제외
                $not_in_arr = array();
                foreach ($aProductList_New as $r) {
                    $not_in_arr[] = $r['p_order_code'];
                }
                $not_in = "'" . implode("','", $not_in_arr) . "'";

                //전체상품 대상 상위 12개
                $sql = " SELECT   item_no
                                , COUNT(*) AS cnt
                         FROM `snsform_order_tb`
                         WHERE register_date >= DATE_FORMAT(DATE_ADD(NOW() , INTERVAL -30 MINUTE) , '%Y-%m-%d %H:%i:%s')

                         GROUP BY item_no
                         ORDER BY cnt DESC ";


                $oResult = $this->db->query($sql);
                $aBestOrderLists = $oResult->result_array();

                $p_order_code_arr = array();
                foreach ($aBestOrderLists as $r) {
                    $p_order_code_arr[] = $r['item_no'];
                }

                $p_order_code = "'" . implode("','", $p_order_code_arr) . "'";

                $sql = " SELECT * , 'best' AS gubun FROM product_tb WHERE p_order_code IN ({$p_order_code}) AND p_display_state = 'Y' AND p_sale_state = 'Y' AND p_stock_state = 'Y' ";
                $oResult = $this->db->query($sql);
                $aProductList_Best = $oResult->result_array();

                //판매 상품 수량이 셋팅값보다 적은 경우 최근 일주일판매 상품중 잘팔린 상품으로 추가 상품 선정
                if(count($aProductList_Best) < $best_cnt){

                    $p_order_code_arr = array_merge($p_order_code_arr,$not_in_arr);
                    $p_order_code = "'" . implode("','", $p_order_code_arr) . "'";

                    $add_product_cnt = (int)$best_cnt - (int)count($aProductList_Best);

                    $sql = " SELECT 
                                * 
                             , 'best' AS gubun 
                             FROM product_tb 
                             WHERE p_order_code NOT IN ({$p_order_code}) 
                             AND p_display_state = 'Y' 
                             AND p_sale_state = 'Y' 
                             AND p_stock_state = 'Y'
                             ORDER BY p_order_count_week DESC
                             LIMIT {$add_product_cnt};
                    ";
                    $oResult = $this->db->query($sql);
                    $aProductList_Best2 = $oResult->result_array();

                }

                if(empty($aProductList_Best2) == false){
                    $aProductList_Best = array_merge($aProductList_Best,$aProductList_Best2);
                }

            }

            { // params && insert && update

                $aProductList   = array_merge($aProductList_New, $aProductList_Best);
                $curr_datetime  = current_datetime();

                //기준상품 off
                $sql = "UPDATE main_product_tb SET use_flag = 'N' WHERE use_flag = 'Y'; ";
                $this->db->query($sql);

                foreach ($aProductList as $k => $r) {

                    $arrayParams = array(
                            'p_num'         => $r['p_num']
                        ,   'p_order_code'  => $r['p_order_code']
                        ,   'gubun'         => $r['gubun']
                        ,   'use_flag'      => 'Y'
                        ,   'sort'          => (int)$k + 1
                        ,   'reg_date'      => $curr_datetime
                    );

                    $this->publicInsert('main_product_tb',$arrayParams);

                }

            }

        }else{ //고정된 메인상품의 판매량 정리

            $sql = " SELECT * 
                     FROM main_product_tb
                     WHERE use_flag = 'Y'; 
            ";
            $oResult = $this->db->query($sql);
            $aResult = $oResult->result_array();

            $p_order_code_arr = array();
            $p_num_arr = array();
            foreach ($aResult as $r) {
                $p_order_code_arr[] = $r['p_order_code'];
                $p_num_arr[$r['p_order_code']] = $r['p_num'];
            }

            $p_order_code = "'" . implode("','", $p_order_code_arr) . "'";

            $sql = "SELECT * , COUNT(*) AS cnt 
                    FROM snsform_order_tb 
                    WHERE item_no IN ({$p_order_code})
                    AND register_date >= DATE_FORMAT(DATE_ADD(NOW() , INTERVAL -30 MINUTE) , '%Y-%m-%d %H:%i:%s')  
                    GROUP BY item_no 
                    ORDER BY cnt DESC ; 
            ";
            $oResult_2 = $this->db->query($sql);
            $aResult_2 = $oResult_2->result_array();

            $aResult_3 = array();
            foreach ($aResult_2 as $k => $r) {
                $r['no'] = (int)$k+1;
                $aResult_3[$r['item_no']] = $r;
            }

            //기준상품 off
            $sql = "UPDATE main_product_tb SET use_flag = 'N' WHERE use_flag = 'Y'; ";
            $this->db->query($sql);

            $empty_no       = (int)count($aResult_3)+1;
            $curr_datetime  = current_datetime();

            foreach ($aResult as $k => $r) {

                if(empty($aResult_3[$r['p_order_code']]) == true){ //판매량없음

                    $arrayParams = array(
                        'p_num'         => $p_num_arr[$r['p_order_code']]
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'sort'          => $empty_no
                    ,   'use_flag'      => 'Y'
                    ,   'reg_date'      => $curr_datetime
                    );

                    $empty_no++;

                }else{

                    $sub_r = $aResult_3[$r['p_order_code']];
                    $arrayParams = array(
                            'p_num'         => $p_num_arr[$r['p_order_code']]
                        ,   'p_order_code'  => $r['p_order_code']
                        ,   'gubun'         => $r['gubun']
                        ,   'sort'          => $sub_r['no']
                        ,   'use_flag'      => 'Y'
                        ,   'reg_date'      => $curr_datetime
                    );

                }

                $this->publicInsert('main_product_tb',$arrayParams);

            }

        }

    }


    /**
     * @date 200302
     * @modify 황기석
     * @desc 메인페이지 상품노출 proc
     */
    public function proc_main_product_v2($debug)
    {

        $chk_time_area0 = array('08','12','16','20');
        $chk_min0 = '30';

        $new_cnt = 3;
        $best_cnt = 27;
        $tot_cnt = $new_cnt + $best_cnt;

        if(in_array($this->curr_time_h,$chk_time_area0) == true && $chk_min0 > $this->curr_time_i) { //초기화 후 재 등록

            log_message('A','proc_main_product_v2 :: 초기화 후 재등록');

            {//신상품 3개
                $sql = " SELECT 
                            * 
                         ,  'new' AS gubun 
                         FROM product_tb 
                         WHERE p_display_state = 'Y' 
                         AND p_sale_state = 'Y' 
                         AND p_stock_state = 'Y' 
                         ORDER BY p_termlimit_datetime1 DESC 
                         LIMIT {$new_cnt}; 
                 ";
                $oResult = $this->db->query($sql);
                $aProductList_New = $oResult->result_array();
            }

            { //최근 30분동안 가장 잘팔린 상위 12개 상품

                //신규상품 제외
                $not_in_arr = array();
                foreach ($aProductList_New as $r) {
                    $not_in_arr[] = $r['p_order_code'];
                }
                $not_in = "'" . implode("','", $not_in_arr) . "'";

                //전체상품 대상 상위 27개
                $sql = "SELECT 
                            B.p_order_code 
                        ,   B.p_num
                        ,   'best' AS gubun 
                        ,   COUNT(*) AS cnt 
                        #,   COUNT(*) * B.p_margin_price AS chk_point
                        ,   COUNT(*) AS chk_point
                        FROM snsform_order_tb A
                        INNER JOIN product_tb B ON B.p_order_code = A.item_no AND A.register_date >= DATE_FORMAT(DATE_ADD(NOW() , INTERVAL -1 DAY) , '%Y-%m-%d 00:00:00')
                        WHERE 1
                        AND B.p_order_code NOT IN ({$not_in})
                        AND B.p_display_state = 'Y' 
                        AND B.p_sale_state = 'Y' 
                        AND B.p_stock_state = 'Y' 
                        GROUP BY B.p_order_code
                        ORDER BY chk_point DESC
                        LIMIT {$best_cnt} ;
                ";

                $oResult = $this->db->query($sql);
                $aProductList_Best = $oResult->result_array();

            }

            { // params && insert && update

                $aProductList   = array_merge($aProductList_New, $aProductList_Best);
                $curr_datetime  = current_datetime();

                //기준상품 off
                $sql = "UPDATE main_product_tb SET use_flag = 'N' WHERE use_flag = 'Y'; ";
                $this->db->query($sql);

                foreach ($aProductList as $k => $r) {

                    $arrayParams = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'use_flag'      => 'Y'
                    ,   'sort'          => (int)$k + 1
                    ,   'reg_date'      => $curr_datetime
                    );

                    $this->publicInsert('main_product_tb',$arrayParams);

                }

            }

        } else {

            $chk_time_area1 = array('08','12','16','20');
            $chk_min1 = '30';

            if($this->curr_time_i >= $chk_min1 && in_array($this->curr_time_h,$chk_time_area1) == true  ){

                log_message('A','proc_main_product_v2 :: chk_time > 30분 ~ 00분');

                /**
                 * @desc
                 * 신상 1/3/5
                 * 베스트 2/4/6/7/8/9/10 ~ 순서
                 */
                {//신상

                    $sql = "SELECT 
                                #T.cnt * T.p_margin_price AS chk_point
                                T.cnt AS chk_point
                            ,	T.cnt
                            ,	T.p_order_code
                            ,	T.p_num
                            ,	T.gubun
                            FROM (
                                SELECT 
                                    A.p_order_code
                                ,   A.p_num
                                ,   A.gubun 
                                ,   C.p_margin_price
                                ,   COUNT(B.seq) AS cnt 
                                FROM main_product_tb A
                                INNER JOIN product_tb C ON A.p_num = C.p_num
                                LEFT JOIN snsform_order_tb B ON A.p_order_code = B.item_no AND B.register_date >= DATE_FORMAT(DATE_ADD(NOW() , INTERVAL -1 DAY) , '%Y-%m-%d 00:00:00')
                                WHERE A.gubun = 'new'
                                AND A.use_flag= 'Y'
                                GROUP BY A.p_order_code
                            ) T
                            LEFT JOIN snsform_product_tb A ON A.item_no = T.p_order_code
                            ORDER BY chk_point DESC
                    ";

                    $oResult = $this->db->query($sql);
                    $aNewList = $oResult->result_array();

                }

                {//베스트

                    //신규상품 제외
                    $not_in_arr = array();
                    foreach ($aNewList as $r) {
                        $not_in_arr[] = $r['p_order_code'];
                    }
                    $not_in = "'" . implode("','", $not_in_arr) . "'";

                    $sql = "SELECT 
                                B.p_order_code 
                            ,   B.p_num
                            ,   'best' AS gubun 
                            ,   COUNT(*) AS cnt 
                            #,   COUNT(*) * B.p_margin_price AS chk_point 
                            ,   COUNT(*) AS chk_point
                            FROM snsform_order_tb A
                            INNER JOIN product_tb B ON B.p_order_code = A.item_no AND A.register_date >= DATE_FORMAT(DATE_ADD(NOW() , INTERVAL -1 DAY) , '%Y-%m-%d 00:00:00')
                            WHERE 1
                            AND B.p_order_code NOT IN ({$not_in})
                            AND B.p_display_state = 'Y' 
                            AND B.p_sale_state = 'Y' 
                            AND B.p_stock_state = 'Y' 
                            GROUP BY B.p_order_code
                            ORDER BY chk_point DESC
                            LIMIT {$best_cnt} ;
                    ";
                    $oResult = $this->db->query($sql);
                    $aBestList = $oResult->result_array();

                }

                foreach ($aNewList as $k => $r) {

                    $sort = 0;
                    if($k == 0) $sort = 1;
                    else if($k == 1) $sort = 3;
                    else if($k == 2) $sort = 5;

                    $insert_data[] = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'sort'          => $sort
                    );

                }

                foreach ($aBestList as $k => $r) {

                    $sort = 0;
                    if($k == 0) $sort = 2;
                    else if($k == 1) $sort = 4;
                    else if($k == 2) $sort = 6;
                    else $sort = $k+4;

                    $insert_data[] = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'sort'          => $sort
                    );

                }

                $curr_datetime  = current_datetime();

                //기준상품 off
                $sql = "UPDATE main_product_tb SET use_flag = 'N' WHERE use_flag = 'Y'; ";
                $this->db->query($sql);

                foreach ($insert_data as $r) {

                    $arrayParams = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'use_flag'      => 'Y'
                    ,   'sort'          => $r['sort']
                    ,   'reg_date'      => $curr_datetime
                    );

                    $this->publicInsert('main_product_tb',$arrayParams);

                }

            }else{

                log_message('A','proc_main_product_v2 :: else all ');

                /**
                 * @desc
                 * 마진 정책에 의한 1-30위
                 */
                $sql = "SELECT 
                            B.p_order_code 
                        ,   B.p_num
                        ,   'best' AS gubun 
                        ,   COUNT(*) AS cnt 
                        #,   COUNT(*) * B.p_supply_price AS chk_point
                        ,   COUNT(*) AS chk_point
                        FROM snsform_order_tb A 
                        INNER JOIN product_tb B ON B.p_order_code = A.item_no AND A.register_date >= DATE_FORMAT(DATE_ADD(NOW() , INTERVAL -1 DAY) , '%Y-%m-%d 00:00:00')
                        WHERE 1
                        AND B.p_display_state = 'Y' 
                        AND B.p_sale_state = 'Y' 
                        AND B.p_stock_state = 'Y' 
                        GROUP BY B.p_order_code
                        ORDER BY chk_point DESC
                        LIMIT {$tot_cnt} ;
                ";

                $oResult = $this->db->query($sql);
                $aMainList = $oResult->result_array();

                foreach ($aMainList as $k => $r) {

                    $insert_data[] = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'sort'          => $k+1
                    );

                }

                $curr_datetime  = current_datetime();

                if(count($insert_data) > 0){ //변경할 데이터가 있는 경우 기존상품 off
                    $sql = "UPDATE main_product_tb SET use_flag = 'N' WHERE use_flag = 'Y'; ";
                    $this->db->query($sql);
                }

                foreach ($insert_data as $r) {

                    $arrayParams = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'use_flag'      => 'Y'
                    ,   'sort'          => $r['sort']
                    ,   'reg_date'      => $curr_datetime
                    );

                    $this->publicInsert('main_product_tb',$arrayParams);

                }

            }

        }

    }


    /**
     * @date 200302
     * @modify 황기석
     * @desc 메인페이지 상품노출 proc
     */
    public function proc_main_product_v3($debug)
    {

        $chk_time_area0 = array('08','12','16','20');
        $chk_min0 = '30';

        $new_cnt = 3;
        $best_cnt = 27;
        $tot_cnt = $new_cnt + $best_cnt;

        if(in_array($this->curr_time_h,$chk_time_area0) == true && $chk_min0 > $this->curr_time_i) { //초기화 후 재 등록

            log_message('A','proc_main_product_v3 :: 초기화 후 재등록');

            {//신상품 3개
                $sql = " SELECT 
                            p_order_code 
                         ,  p_num
                         ,  'new' AS gubun 
                         FROM product_tb 
                         WHERE p_display_state = 'Y' 
                         AND p_sale_state = 'Y' 
                         AND p_stock_state = 'Y' 
                         ORDER BY p_termlimit_datetime1 DESC 
                         LIMIT {$new_cnt}; 
                 ";
                $oResult = $this->db->query($sql);
                $aProductList_New = $oResult->result_array();
            }

            { //최근 30분동안 가장 잘팔린 상위 12개 상품

                //신규상품 제외
                $not_in_arr = array();
                foreach ($aProductList_New as $r) {
                    $not_in_arr[] = $r['p_order_code'];
                }
                $not_in = "'" . implode("','", $not_in_arr) . "'";

                //전체상품 대상 상위 27개
                $sql = "SELECT 
                            B.p_order_code 
                        ,   B.p_num
                        ,   'best' AS gubun 
                        FROM product_tb B
                        WHERE 1
                        AND B.p_order_code NOT IN ({$not_in})
                        AND B.p_display_state = 'Y' 
                        AND B.p_sale_state = 'Y' 
                        AND B.p_stock_state = 'Y' 
                        ORDER BY B.p_order_count_twoday DESC , B.p_view_today_count DESC
                        LIMIT {$best_cnt} ;
                ";

                $oResult = $this->db->query($sql);
                $aProductList_Best = $oResult->result_array();

            }

            { // params && insert && update

                $aProductList   = array_merge($aProductList_New, $aProductList_Best);
                $curr_datetime  = current_datetime();

                //기준상품 off
                $sql = "UPDATE main_product_tb SET use_flag = 'N' WHERE use_flag = 'Y'; ";
                $this->db->query($sql);

                foreach ($aProductList as $k => $r) {

                    $arrayParams = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'use_flag'      => 'Y'
                    ,   'sort'          => (int)$k + 1
                    ,   'reg_date'      => $curr_datetime
                    );

                    $this->publicInsert('main_product_tb',$arrayParams);

                }

            }

        } else {

            $chk_time_area1 = array('08','12','16','20');
            $chk_min1 = '30';

            if($this->curr_time_i >= $chk_min1 && in_array($this->curr_time_h,$chk_time_area1) == true  ){

                log_message('A','proc_main_product_v2 :: chk_time > 30분 ~ 00분');

                /**
                 * @desc
                 * 신상 1/3/5
                 * 베스트 2/4/6/7/8/9/10 ~ 순서
                 */
                {//신상

                    $sql = "SELECT 
                                A.p_order_code
                            ,   A.p_num
                            ,   A.gubun 
                            FROM main_product_tb A
                            INNER JOIN product_tb C ON A.p_num = C.p_num
                            WHERE A.gubun = 'new'
                            AND A.use_flag= 'Y'
                            ORDER BY C.p_order_count_twoday DESC , C.p_view_today_count DESC
                    ";

                    $oResult = $this->db->query($sql);
                    $aNewList = $oResult->result_array();

                }

                {//베스트

                    //신규상품 제외
                    $not_in_arr = array();
                    foreach ($aNewList as $r) {
                        $not_in_arr[] = $r['p_order_code'];
                    }
                    $not_in = "'" . implode("','", $not_in_arr) . "'";

                    $sql = "SELECT 
                                B.p_order_code 
                            ,   B.p_num
                            ,   'best' AS gubun 
                            FROM product_tb B
                            WHERE 1
                            AND B.p_order_code NOT IN ({$not_in})
                            AND B.p_display_state = 'Y' 
                            AND B.p_sale_state = 'Y' 
                            AND B.p_stock_state = 'Y' 
                            ORDER BY B.p_order_count_twoday DESC , B.p_view_today_count DESC
                            LIMIT {$best_cnt} ;
                    ";
                    $oResult = $this->db->query($sql);
                    $aBestList = $oResult->result_array();

                }

                foreach ($aNewList as $k => $r) {

                    $sort = 0;
                    if($k == 0) $sort = 1;
                    else if($k == 1) $sort = 3;
                    else if($k == 2) $sort = 5;

                    $insert_data[] = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'sort'          => $sort
                    );

                }

                foreach ($aBestList as $k => $r) {

                    $sort = 0;
                    if($k == 0) $sort = 2;
                    else if($k == 1) $sort = 4;
                    else if($k == 2) $sort = 6;
                    else $sort = $k+4;

                    $insert_data[] = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'sort'          => $sort
                    );

                }

                $curr_datetime  = current_datetime();

                //기준상품 off
                $sql = "UPDATE main_product_tb SET use_flag = 'N' WHERE use_flag = 'Y'; ";
                $this->db->query($sql);

                foreach ($insert_data as $r) {

                    $arrayParams = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'use_flag'      => 'Y'
                    ,   'sort'          => $r['sort']
                    ,   'reg_date'      => $curr_datetime
                    );

                    $this->publicInsert('main_product_tb',$arrayParams);

                }

            }else{

                log_message('A','proc_main_product_v2 :: else all ');

                /**
                 * @desc
                 * 마진 정책에 의한 1-30위
                 */
                $sql = "SELECT 
                            B.p_order_code 
                        ,   B.p_num
                        ,   'best' AS gubun 
                        FROM product_tb B
                        WHERE 1
                        AND B.p_display_state = 'Y' 
                        AND B.p_sale_state = 'Y' 
                        AND B.p_stock_state = 'Y' 
                        ORDER BY B.p_order_count_twoday DESC , B.p_view_today_count DESC
                        LIMIT {$tot_cnt} ;
                ";

                $oResult = $this->db->query($sql);
                $aMainList = $oResult->result_array();

                foreach ($aMainList as $k => $r) {

                    $insert_data[] = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'sort'          => $k+1
                    );

                }

                $curr_datetime  = current_datetime();

                if(count($insert_data) > 0){ //변경할 데이터가 있는 경우 기존상품 off
                    $sql = "UPDATE main_product_tb SET use_flag = 'N' WHERE use_flag = 'Y'; ";
                    $this->db->query($sql);
                }

                foreach ($insert_data as $r) {

                    $arrayParams = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'use_flag'      => 'Y'
                    ,   'sort'          => $r['sort']
                    ,   'reg_date'      => $curr_datetime
                    );

                    $this->publicInsert('main_product_tb',$arrayParams);

                }

            }

        }

    }


    /**
     * @date 200701
     * @modify 황기석
     * @desc 메인페이지 상품노출 proc
     * @add 1시간단위 정책 추가
     */
    public function proc_main_product_v4($debug)
    {

        $chk_time_area0 = array('08','12','16','20');

        $new_cnt = 3;
        $best_cnt = 27;
        $tot_cnt = $new_cnt + $best_cnt;

        if(in_array($this->curr_time_h,$chk_time_area0) == true) { //초기화 후 재 등록

            log_message('A','proc_main_product_v4 :: 기준시간');

            {//신상품 3개
                $sql = " SELECT 
                            p_order_code 
                         ,  p_num
                         ,  'new' AS gubun 
                         FROM product_tb 
                         WHERE p_display_state = 'Y' 
                         AND p_sale_state = 'Y' 
                         AND p_stock_state = 'Y' 
                         ORDER BY p_termlimit_datetime1 DESC 
                         LIMIT {$new_cnt}; 
                 ";
                $oResult = $this->db->query($sql);
                $aProductList_New = $oResult->result_array();
            }

            { //최근 30분동안 가장 잘팔린 상위 12개 상품

                //신규상품 제외
                $not_in_arr = array();
                foreach ($aProductList_New as $r) {
                    $not_in_arr[] = $r['p_order_code'];
                }
                $not_in = "'" . implode("','", $not_in_arr) . "'";

                //전체상품 대상 상위 27개
                $sql = "SELECT 
                            B.p_order_code 
                        ,   B.p_num
                        ,   'best' AS gubun 
                        FROM product_tb B
                        WHERE 1
                        AND B.p_order_code NOT IN ({$not_in})
                        AND B.p_display_state = 'Y' 
                        AND B.p_sale_state = 'Y' 
                        AND B.p_stock_state = 'Y' 
                        ORDER BY B.p_order_count_twoday DESC , B.p_view_today_count DESC
                        LIMIT {$best_cnt} ;
                ";

                $oResult = $this->db->query($sql);
                $aProductList_Best = $oResult->result_array();

            }

            { // params && insert && update

                $aProductList   = array_merge($aProductList_New, $aProductList_Best);
                $curr_datetime  = current_datetime();

                //기준상품 off
                $sql = "UPDATE main_product_tb SET use_flag = 'N' WHERE use_flag = 'Y'; ";
                $this->db->query($sql);

                foreach ($aProductList as $k => $r) {

                    $arrayParams = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'use_flag'      => 'Y'
                    ,   'sort'          => (int)$k + 1
                    ,   'reg_date'      => $curr_datetime
                    );

                    $this->publicInsert('main_product_tb',$arrayParams);

                }

            }

        } else {

            $chk_time_area1 = array('09','13','17','21');

            if(in_array($this->curr_time_h,$chk_time_area1) == true  ){

                log_message('A','proc_main_product_v4 :: 기존시간 +1시간');

                /**
                 * @desc
                 * 신상 1/3/5
                 * 베스트 2/4/6/7/8/9/10 ~ 순서
                 */
                {//신상

                    $sql = "SELECT 
                                A.p_order_code
                            ,   A.p_num
                            ,   A.gubun 
                            FROM main_product_tb A
                            INNER JOIN product_tb C ON A.p_num = C.p_num
                            WHERE A.gubun = 'new'
                            AND A.use_flag= 'Y'
                            ORDER BY C.p_order_count_twoday DESC , C.p_view_today_count DESC
                    ";

                    $oResult = $this->db->query($sql);
                    $aNewList = $oResult->result_array();

                }

                {//베스트

                    //신규상품 제외
                    $not_in_arr = array();
                    foreach ($aNewList as $r) {
                        $not_in_arr[] = $r['p_order_code'];
                    }
                    $not_in = "'" . implode("','", $not_in_arr) . "'";

                    $sql = "SELECT 
                                B.p_order_code 
                            ,   B.p_num
                            ,   'best' AS gubun 
                            FROM product_tb B
                            WHERE 1
                            AND B.p_order_code NOT IN ({$not_in})
                            AND B.p_display_state = 'Y' 
                            AND B.p_sale_state = 'Y' 
                            AND B.p_stock_state = 'Y' 
                            ORDER BY B.p_order_count_twoday DESC , B.p_view_today_count DESC
                            LIMIT {$best_cnt} ;
                    ";
                    $oResult = $this->db->query($sql);
                    $aBestList = $oResult->result_array();

                }

                foreach ($aNewList as $k => $r) {

                    $sort = 0;
                    if($k == 0) $sort = 1;
                    else if($k == 1) $sort = 3;
                    else if($k == 2) $sort = 5;

                    $insert_data[] = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'sort'          => $sort
                    );

                }

                foreach ($aBestList as $k => $r) {

                    $sort = 0;
                    if($k == 0) $sort = 2;
                    else if($k == 1) $sort = 4;
                    else if($k == 2) $sort = 6;
                    else $sort = $k+4;

                    $insert_data[] = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'sort'          => $sort
                    );

                }

                $curr_datetime  = current_datetime();

                //기준상품 off
                $sql = "UPDATE main_product_tb SET use_flag = 'N' WHERE use_flag = 'Y'; ";
                $this->db->query($sql);

                foreach ($insert_data as $r) {

                    $arrayParams = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'use_flag'      => 'Y'
                    ,   'sort'          => $r['sort']
                    ,   'reg_date'      => $curr_datetime
                    );

                    $this->publicInsert('main_product_tb',$arrayParams);

                }

            }else{

                log_message('A','proc_main_product_v4 :: 기준시간 +3 +4 else All');

                /**
                 * @desc
                 * 마진 정책에 의한 1-30위
                 */
                $sql = "SELECT 
                            B.p_order_code 
                        ,   B.p_num
                        ,   'best' AS gubun 
                        FROM product_tb B
                        WHERE 1
                        AND B.p_display_state = 'Y' 
                        AND B.p_sale_state = 'Y' 
                        AND B.p_stock_state = 'Y' 
                        ORDER BY B.p_order_count_twoday DESC , B.p_view_today_count DESC
                        LIMIT {$tot_cnt} ;
                ";

                $oResult = $this->db->query($sql);
                $aMainList = $oResult->result_array();

                foreach ($aMainList as $k => $r) {

                    $insert_data[] = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'sort'          => $k+1
                    );

                }

                $curr_datetime  = current_datetime();

                if(count($insert_data) > 0){ //변경할 데이터가 있는 경우 기존상품 off
                    $sql = "UPDATE main_product_tb SET use_flag = 'N' WHERE use_flag = 'Y'; ";
                    $this->db->query($sql);
                }

                foreach ($insert_data as $r) {

                    $arrayParams = array(
                        'p_num'         => $r['p_num']
                    ,   'p_order_code'  => $r['p_order_code']
                    ,   'gubun'         => $r['gubun']
                    ,   'use_flag'      => 'Y'
                    ,   'sort'          => $r['sort']
                    ,   'reg_date'      => $curr_datetime
                    );

                    $this->publicInsert('main_product_tb',$arrayParams);

                }

            }

        }

    }


    public function product_static_month_order($debug){

        $sql = " SELECT   
                    item_no 
                ,   COUNT(*) AS cnt 
                FROM `snsform_order_tb` 
                WHERE register_date >= DATE_FORMAT(DATE_ADD(NOW() , INTERVAL -30 DAY) , '%Y-%m-%d 00:00:00')
                AND item_no <> '0000000000'
                GROUP BY item_no;
        ";

        $oResult         = $this->db->query($sql);
        $aBestOrderLists = $oResult->result_array();

        foreach ($aBestOrderLists as $r) {
            $sql = "UPDATE product_tb SET p_order_count_month = '{$r['cnt']}' WHERE p_order_code = '{$r['item_no']}';   ";
            $this->db->query($sql);
        }

        $sql = " SELECT   
                    item_no 
                ,   COUNT(*) AS cnt 
                FROM `snsform_order_tb` 
                WHERE register_date >= DATE_FORMAT(DATE_ADD(NOW() , INTERVAL -60 DAY) , '%Y-%m-%d 00:00:00')
                AND item_no <> '0000000000'
                GROUP BY item_no;
        ";

        $oResult         = $this->db->query($sql);
        $aBestOrderLists = $oResult->result_array();

        foreach ($aBestOrderLists as $r) {
            $sql = "UPDATE product_tb SET p_order_count_twomonth = '{$r['cnt']}' WHERE p_order_code = '{$r['item_no']}';   ";
            $this->db->query($sql);
        }


    }

    public function product_static_week_order($debug){

        $sql = "UPDATE product_tb SET p_order_count_last_week = p_order_count_week WHERE p_order_count_week > 0 ; ";
        $this->db->query($sql);
        $sql = "UPDATE product_tb SET p_order_count_week = 0 WHERE p_order_count_week > 0 ; ";
        $this->db->query($sql);

    }

    public function product_static_yesterday_order($debug){

        $sql = "UPDATE product_tb SET p_order_count_twoday = 0 ; ";
        $this->db->query($sql);

        $sql = " SELECT
                    item_no
                ,   COUNT(*) AS cnt
                FROM `snsform_order_tb`
                WHERE register_date >= DATE_FORMAT(DATE_ADD(NOW() , INTERVAL -1 DAY) , '%Y-%m-%d 00:00:00')
                AND item_no <> '0000000000'
                GROUP BY item_no;
        ";

        $oResult         = $this->db->query($sql);
        $aBestOrderLists = $oResult->result_array();

        foreach ($aBestOrderLists as $r) {
            $sql = "UPDATE product_tb SET p_order_count_twoday = '{$r['cnt']}' WHERE p_order_code = '{$r['item_no']}';   ";
            $this->db->query($sql);
        }

    }
    public function  get_sending_push_target($debug){

        $sql     = "SELECT  
                        A.*
                    ,   B.p_name 
                    FROM snsform_order_tb A
                    INNER JOIN product_tb B ON A.item_no = B.p_order_code
                    WHERE delivery_push_yn = 'N' 
                    AND status_cd = '64' 
                    AND invoice_no <> '000'
                    AND item_no <> '0000000000'
                    AND req_push_cnt <= 2; 
        ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->result_array();

        return $aResult;
    }

    public function product_static_view($debug){

        $query = "
            update product_tb
            set p_view_3day_count = p_view_yesterday_count + p_view_today_count
            WHERE p_view_yesterday_count > 0 OR p_view_today_count > 0
        ";
        $this->db->query($query);

        $query = "
            update product_tb
            set p_view_yesterday_count = p_view_today_count
            WHERE p_view_today_count > 0
        ";
        $this->db->query($query);

        $query = "
            update product_tb
            set p_view_today_count = '0'
        ";
        $this->db->query($query);

    }

}// end of Class
