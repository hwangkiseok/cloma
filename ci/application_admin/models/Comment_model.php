<?php
/**
 * 댓글 관련 모델
 */
class Comment_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_comment_list($query_array=array(), $start="", $end="", $is_count=false) {
        $comment_tb = "comment_tb";

        //from 절
        $from_query = " from " . $comment_tb . " ";
	    $from_query .= "left join member_tb on member_tb.m_num = comment_tb.cmt_member_num ";
	    $from_query .= "left join product_tb on product_tb.p_num = comment_tb.cmt_table_num ";

        //where 절
        $where_query = "where 1 = 1 ";

        //구분
        if( isset($query_array['where']['tb']) && !empty($query_array['where']['tb']) ) {
            $where_query .= "and comment_tb.cmt_table = '" . $this->db->escape_str($query_array['where']['tb']) . "' ";
        }
        //구분 데이터 번호
        if( isset($query_array['where']['tb_num']) && !empty($query_array['where']['tb_num']) ) {
            $where_query .= "and comment_tb.cmt_table_num = '" . $this->db->escape_str($query_array['where']['tb_num']) . "' ";
        }
        //답댓글유무
        if( isset($query_array['where']['reply_cnt']) && !empty($query_array['where']['reply_cnt']) ) {
            //있음 (답댓글도 같이 출력)
            if( $query_array['where']['reply_cnt'] == "Y" ) {
                $where_query .= "and comment_tb.cmt_answertime <> '' ";
            }
            //없음
            else if( $query_array['where']['reply_cnt'] == "N" ) {
                $where_query .= "and cmt_answertime = '' ";
            }
        }
        //블라인드
        if( isset($query_array['where']['blind']) && !empty($query_array['where']['blind']) ) {
            $where_query .= "and comment_tb.cmt_blind = '" . $this->db->escape_str($query_array['where']['blind']) . "' ";
        }
        //관리글
        if( isset($query_array['where']['admin']) && !empty($query_array['where']['admin']) ) {
            $where_query .= "and comment_tb.cmt_admin = '" . $this->db->escape_str($query_array['where']['admin']) . "' ";
        }
        //회원
        if( isset($query_array['where']['m_num']) && !empty($query_array['where']['m_num']) ) {
            $where_query .= "and comment_tb.cmt_member_num = '" . $this->db->escape_str($query_array['where']['m_num']) . "' ";
        }
        //노출여부
        if( isset($query_array['where']['state']) && !empty($query_array['where']['state']) ) {
            $where_query .= "and comment_tb.cmt_display_state = '" . $this->db->escape_str($query_array['where']['state']) . "' ";
        }
        //날짜검색1
        if( isset($query_array['where']['date1']) && !empty($query_array['where']['date1']) ) {
            $where_query .= "and left(comment_tb.cmt_regdatetime, 8) >= '" . number_only($this->db->escape_str($query_array['where']['date1'])) . "' ";
        }
        //날짜검색2
        if( isset($query_array['where']['date2']) && !empty($query_array['where']['date2']) ) {
            $where_query .= "and left(comment_tb.cmt_regdatetime, 8) <= '" . number_only($this->db->escape_str($query_array['where']['date2'])) . "' ";
        }
        //메인인 경우 EVENT 댓글 제외
        if( isset($query_array['where']['main']) && !empty($query_array['where']['main']) ) {
            $where_query .= "and comment_tb.cmt_table = 'product' ";
        }

        //글 상태
        if( isset($query_array['where']['cmt_flag']) && !empty($query_array['where']['cmt_flag']) ) {
            $where_query .= "and (comment_tb.cmt_flag = '" . $this->db->escape_str($query_array['where']['cmt_flag']) . "' ";
            $where_query .= "and comment_tb.cmt_admin = 'N') and comment_tb.cmt_regdatetime >= '20190101000000' ";
        }

        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            if( $query_array['where']['kfd'] == 'all' ) {

                $where_query .= "and ( product_tb.p_name like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= "or comment_tb.cmt_content like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= "or comment_tb.cmt_name like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= "or comment_tb.cmt_member_num like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ) ";

            }  else {

                if($query_array['where']['kfd'] == 'cmt_content') {
                    $where_query .= " and comment_tb.cmt_content like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%'  ";
                } elseif($query_array['where']['kfd'] == 'cmt_name') {
                    $where_query .= "and comment_tb.cmt_name like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%'  ";
                } elseif($query_array['where']['kfd'] == 'p_name') {
                    $where_query .= "and product_tb.p_name like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                } elseif($query_array['where']['kfd'] == 'cmt_member_num') {
                    $where_query .= "and comment_tb.cmt_member_num like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                }
            }
        }




        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_array = explode(" ", trim($query_array['orderby']));

            if( $order_array[0] == "cmt_best_order" ) {
                $order_query = "order by " . $order_array[0] . "=0 asc, " . $order_array[0] . " " . $order_array[1] . " ";
            } else {
                $order_query = "order by " . $query_array['orderby'] . " ";
            }
        } else {
            $order_query = "order by comment_tb.cmt_regdatetime desc ";
        }

        //limit 절
        $limit_query = "";
        if( $start !== "" && $end !== "" ) {
            $limit_query .= "limit " . $start . ", " . $end . " ";
        }

        //$where_query .= " and comment_tb.cmt_admin = 'N' ";

        //갯수만 추출
        if( $is_count === true ) {

            $query = "select count(*) cnt ";
            $query .= $from_query;
            $query .= $where_query;

//            if(zsDebug()) zsView($query);exit;
            return $this->db->query($query)->row_array('cnt');
        }
        //데이터 추출
        else {
            $query = "select 
              {$comment_tb}.*
            , member_tb.m_key
            , member_tb.m_nickname 
            , member_tb.m_order_count 
            , member_tb.m_authno
            , CASE WHEN comment_tb.cmt_table = 'product' THEN (SELECT p_name FROM product_tb WHERE p_num = comment_tb.cmt_table_num)
                   ELSE (SELECT e_subject FROM event_tb WHERE e_num = comment_tb.cmt_table_num) END AS table_num_name
            {$from_query}
            {$where_query}
            {$order_query}
            {$limit_query}
            ";

            return $this->db->query($query)->result_array();
        }
    }//end of get_comment_list()

    /**
     * 해당 회원 댓글 목록
     * @param array $query_array
     * @param string $start
     * @param string $end
     * @param bool $is_count
     * @return array
     */
    public function get_comment_list_member($query_array=array(), $start="", $end="", $is_count=false) {
        if( !isset($query_array['where']['m_num']) || empty($query_array['where']['m_num']) ) {
            return array();
        }

        $m_num = $query_array['where']['m_num'];

        //limit 절
        $limit_query = "";
        if( $start !== "" && $end !== "" ) {
            $limit_query .= "limit " . $start . ", " . $end . " ";
        }

        //갯수만 추출
        if( $is_count === true ) {
            $query = "
                select count(*) cnt
                from comment_tb
                where cmt_member_num = '" . $query_array['m_num'] . "'
            ";
            //var_dump($query);

            return $this->db->query($query)->row('cnt');
        }
        //데이터 추출
        else {
            $query = "
                select 
                    *
                    , if (cmt_parent_num != 0, concat(cmt_parent_num, '_', cmt_num), concat(cmt_num, '_', cmt_parent_num)) as sort
                    , (SELECT cmt_regdatetime FROM comment_tb cmt_table WHERE TB.cmt_num = cmt_table.cmt_parent_num ORDER BY cmt_table.cmt_regdatetime ASC LIMIT 1 ) as parent_cmt_regdatetime
                from
                (
                    select
                        comment_tb.*
                        , member_tb.m_key
                        , member_tb.m_loginid
                        , member_tb.m_nickname
                        , 'Y' as my
                    from comment_tb
                        join member_tb on m_num = cmt_member_num 
                    where cmt_member_num = '" . $m_num . "'
                    
                    union all
                    
                    select
                        *
                        , '' m_key
                        , '' m_loginid
                        , '' m_nickname
                        , 'N' as my
                    from comment_tb
                    where cmt_parent_num in (select cmt_num from comment_tb where cmt_member_num = '" . $m_num . "')
                ) TB
                order by sort+0 desc
                " . $limit_query . "
            ";

            return $this->db->query($query)->result();
        }
    }//end of get_comment_list_member()

    /**
     * 댓글 조회
     * @param $cmt_num
     * @return mixed
     */
    public function get_comment_row($cmt_num) {
        $query = "select comment_tb.*, member_tb.m_nickname ";
        $query .= "from comment_tb ";
        $query .= "left join member_tb on m_num = cmt_member_num ";
        $query .= "where cmt_num = '" . $this->db->escape_str($cmt_num) . "' ";
        $query .= "limit 1 ";
        return $this->db->query($query)->row_array();
    }//end of get_comment_row()

    /**
     * 댓글 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_comment($query_data=array()) {
        if(
            !isset($query_data['cmt_table']) || empty($query_data['cmt_table']) ||
            !isset($query_data['cmt_table_num']) || empty($query_data['cmt_table_num']) ||
            !isset($query_data['cmt_content']) || empty($query_data['cmt_content'])
        ) {
            return false;
        }


        $query_data['cmt_regdatetime'] = current_datetime();

        return $this->db->insert("comment_tb", $query_data);
    }//end of insert_comment()

    /**
     * 댓글 수정
     * @param $cmt_num
     * @param array $query_data
     * @return bool
     */
    public function update_comment ($cmt_num, $query_data=array()) {
        if( empty($cmt_num) ) {
            return false;
        }

        return $this->db->where('cmt_num', $cmt_num)->update("comment_tb", $query_data);
    }//end of update_comment()

    /**
     * 상태 count
     * @param $flag
     * @return int
     */
    public function get_flag_count($flag) {
        $sql = "
            select count(*) cnt
            from comment_tb
            where 
              cmt_admin = 'N' and 
              cmt_flag = '" . $this->db->escape_str($flag) . "'
                cmt_table = 'product' 
        ";
        $reply_count = $this->db->query($sql)->row('cnt');

        if( empty($reply_count) ) {
            $reply_count = 0;
        }

        return $reply_count;


    }//end of get_flag_count()

    /**
     * 댓글 삭제
     * @param $cmt_num
     */
    public function delete_comment ($cmt_num) {
        return $this->db->where('cmt_num', $cmt_num)->delete('comment_tb');
    }//end of delete_comment()

    /**
     * 해당 테이블 데이터의 갯수 업데이트
     * @param $tb
     * @param $tb_num
     * @param string $act
     * @return bool
     */
    function update_table_data_count($tb, $tb_num, $act="insert") {
        if( empty($tb) || empty($tb_num) ) {
            return false;
        }
        
        $table_name = $this->config->item($tb, "comment_table_name");

        //테이블 존재 확인
        $query = "show tables like '" . $table_name . "'";
        $table_check = $this->db->query($query)->row();

        if( empty($table_check) ) {
            return false;
        }

        if( $act == "delete" ) {
            $update_query = str_replace("#TB_NUM#", $tb_num, $this->config->item($tb, "comment_table_count_delete_query"));
        }
        else {
            $update_query = str_replace("#TB_NUM#", $tb_num, $this->config->item($tb, "comment_table_count_update_query"));
        }

        return $this->db->query($update_query);
    }//end of update_table_data_count()

    /**
     * 테이블 데이터 정보 추출
     * @param $tb
     * @param $num
     * @param string $select_query
     * @return bool
     */
    function get_table_data_row($tb, $num, $select_query="") {
        if( empty($tb) || empty($num) ) {
            return false;
        }

        $table_query = $this->config->item($tb, "comment_table_query");
        $table_initial = $this->config->item($tb, "comment_table_initial");

        if( !empty($select_query) ) {
            $query = $select_query . " ";
        }
        else {
            $query = "select * ";
        }
        $query .= $table_query['from'] . " ";
        if( $tb == "everyday" ) {
            $query .= "where " . $table_initial . "num = '" . $num . "' ";
        }
        else {
            $query .= $table_query['where'] . " ";
            $query .= "and " . $table_initial . "num = '" . $num . "' ";
        }
        $query .= "limit 1";

        return $this->db->query($query)->row_array();
    }//end of get_table_data_row()

    /**
     * 문구 목록 추출
     */
    public function get_word_use_list() {
        //from 절
        $from_query = "from word_use_tb ";

        //where 절
        $where_query = "where wd_usestate = 'Y' and wd_use = 'comment' ";

        //order by 절
        $order_query = "order by wd_num asc ";

        //limit 절
        $limit_query = "limit 100";

        $query = "select * ";
        $query .= $from_query;
        $query .= $where_query;
        $query .= $order_query;
        $query .= $limit_query;

        //echo $query;
        //print_r($query_array);
        return $this->db->query($query)->result();
    }//end of get_word_use_list()


}//end of class Comment_model