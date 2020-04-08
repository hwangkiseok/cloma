<?php
/**
 * 댓글 관련 모델
 */
class Review_model extends A_Model {

    public function __construct(){
        parent::__construct();
    }//end of __construct()

    /**
     * 리뷰 목록 추출
     * @param array $query_array    : 쿼리배열
     * @param string $start         : limit $start, $end
     * @param string $end           : limit $start, $end
     * @param bool $is_count        : 전체갯수만 추출여부
     */
    public function get_review_list($query_array=array(), $start="", $end="", $is_count=false) {

        //from 절
        $from_query = " from review_tb ";
        $from_query .= "left join member_tb on m_num = re_member_num ";

        //where 절
        $where_query = "where 1 = 1 ";
        //구분 데이터 번호
        if( isset($query_array['where']['tb_num']) && !empty($query_array['where']['tb_num']) ) {
            $where_query .= "and re_table_num = '" . $this->db->escape_str($query_array['where']['tb_num']) . "' ";
        }
        //블라인드
        if( isset($query_array['where']['blind']) && !empty($query_array['where']['blind']) ) {
            $where_query .= "and re_blind = '" . $this->db->escape_str($query_array['where']['blind']) . "' ";
        }
        //관리글
        if( isset($query_array['where']['admin']) && !empty($query_array['where']['admin']) ) {
            $where_query .= "and re_admin = '" . $this->db->escape_str($query_array['where']['admin']) . "' ";
        }
        //회원
        if( isset($query_array['where']['m_num']) && !empty($query_array['where']['m_num']) ) {
            $where_query .= "and re_member_num = '" . $this->db->escape_str($query_array['where']['m_num']) . "' ";
        }
        //노출여부
        if( isset($query_array['where']['state']) && !empty($query_array['where']['state']) ) {
            $where_query .= "and re_display_state = '" . $this->db->escape_str($query_array['where']['state']) . "' ";
        }


        //추천상태
        if( isset($query_array['where']['grade']) && !empty($query_array['where']['grade']) ) {
            $where_query .= "and re_grade = '" . $this->db->escape_str($query_array['where']['grade']) . "' ";
        }
        //메인노출여부
        if( isset($query_array['where']['main_view']) && !empty($query_array['where']['main_view']) ) {
            $where_query .= "and re_recommend = '" . $this->db->escape_str($query_array['where']['main_view']) . "' ";
        }

        //이미지여부
        if( isset($query_array['where']['img_yn']) && !empty($query_array['where']['img_yn']) ) {

            if($query_array['where']['img_yn'] == 'Y'){
                $where_query .= "and re_img <> '' AND re_img <> '[]' AND re_img IS NOT NULL ";
            }else{
                $where_query .= "and ( re_img = '' OR re_img = '[]' OR re_img IS NULL ) ";
            }

        }

        //적립금 지급여부
        if( isset($query_array['where']['reward_yn']) && !empty($query_array['where']['reward_yn']) ) {
            if($query_array['where']['reward_yn'] == 'N') {
                $where_query .= "and re_admin != 'Y' and re_reward = '" . $this->db->escape_str($query_array['where']['reward_yn']) . "' and re_regdatetime > '20190219163000' ";
            } else {
                $where_query .= "and re_reward = '" . $this->db->escape_str($query_array['where']['reward_yn']) . "' ";
            }

        }

        //적립금 타입
        if( isset($query_array['where']['reward_type']) && !empty($query_array['where']['reward_type']) ) {
            $where_query .= "and re_reward_type = '" . $this->db->escape_str($query_array['where']['reward_type']) . "' ";
        }

        //zsView($query_array);

        if($query_array['where']['dateType'] == 're_winner_date'){

            //날짜검색1
            if( isset($query_array['where']['date1']) && !empty($query_array['where']['date1']) ) {
                $where_query .= "and re_winner_date >= LEFT('" . number_only($this->db->escape_str($query_array['where']['date1'])) . "',6) ";
            }
            //날짜검색2
            if( isset($query_array['where']['date2']) && !empty($query_array['where']['date2']) ) {
                $where_query .= "and re_winner_date <= LEFT('" . number_only($this->db->escape_str($query_array['where']['date2'])) . "',6) ";
            }

        }else{
            //날짜검색1
            if( isset($query_array['where']['date1']) && !empty($query_array['where']['date1']) ) {
                $where_query .= "and left(re_regdatetime, 8) >= '" . number_only($this->db->escape_str($query_array['where']['date1'])) . "' ";
            }
            //날짜검색2
            if( isset($query_array['where']['date2']) && !empty($query_array['where']['date2']) ) {
                $where_query .= "and left(re_regdatetime, 8) <= '" . number_only($this->db->escape_str($query_array['where']['date2'])) . "' ";
            }
        }
        //키워드
        if(
            isset($query_array['where']['kfd']) && !empty($query_array['where']['kfd']) &&
            isset($query_array['where']['kwd']) && !empty($query_array['where']['kwd'])
        ) {
            if( $query_array['where']['kfd'] == 'all' ) {
                $where_query .= "and (re_content like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= "or re_name like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
                $where_query .= "or p_name like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%') ";
            }
            else {
                $where_query .= "and " . $query_array['where']['kfd'] . " like '%" . $this->db->escape_str($query_array['where']['kwd']) . "%' ";
            }
        }

        // 메모유무
        if (!empty($query_array['where']['memo_yn'])) {

            if($query_array['where']['memo_yn'] == 'Y') {
                $where_query .= "and re_blind_memo <> '' ";
            } else if($query_array['where']['memo_yn'] == 'N') {
                $where_query .= "and re_blind_memo = '' ";
            }
        }

        // CS처리 필요/불필요
        if (!empty($query_array['where']['cs_help_yn'])) {
            $where_query .= "and re_cs_help_yn = '" . $this->db->escape_str($query_array['where']['cs_help_yn']) . "' ";
        }

        //order by 절
        if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
            $order_query = "order by " . $query_array['orderby'] . " ";
        }
        else {
            //$order_query = "order by cmt_regdatetime desc, cmt_num desc ";
            $order_query = "order by re_regdatetime desc, re_num asc ";
            //$order_query = "order by cmt_num desc ";
        }

        //limit 절
        $limit_query = "";
        if( $start !== "" && $end !== "" ) {
            $limit_query .= "limit " . $start . ", " . $end . " ";
        }

        if(!empty($query_array['best_review'])) {
            if ($query_array['best_review'] == 'Y') {
                $where_query .= " and re_recommend = 'Y'";
                $order_query = " order by re_sort asc ";
            } else {
                $where_query .= " and re_recommend = 'N'";
            }
        }

        //갯수만 추출
        if( $is_count === true ) {
            $query = "select count(*) cnt ";
            $query .= $from_query;
            $query .= " LEFT JOIN product_tb ON p_num = re_table_num AND re_table = 'review' ";
            $query .= $where_query;

            return $this->db->query($query)->row('cnt');
        }
        //데이터 추출 table_num_name
        else {
            $query = "select review_tb.*, member_tb.m_key, member_tb.m_nickname , member_tb.m_order_count , member_tb.m_authno ";
            $query .= ", (SELECT p_name FROM product_tb WHERE p_num = re_table_num ) AS table_num_name";
            $query .= $from_query;
            $query .= " LEFT JOIN product_tb ON p_num = re_table_num AND re_table = 'review' ";
            $query .= $where_query;
            $query .= $order_query;
            $query .= $limit_query;
            //zsView($query);

            if(zsDebug()) {
                //echo $query;
            }

            return $this->db->query($query)->result();
        }
    }//end of get_comment_list()

    /**
     * 리뷰 조회
     * @param $re_num
     * @return mixed
     */
    public function get_review_row($re_num) {
        $query = "select review_tb.*, member_tb.m_nickname , member_tb.m_authno , member_tb.m_order_phone , member_tb.m_num ";
        $query .= ", (SELECT p_name FROM product_tb WHERE p_num = re_table_num ) AS table_num_name ";
        $query .= ", DATE_FORMAT(re_regdatetime,'%Y%m') AS winner_date ";
        $query .= "from review_tb ";
        $query .= "left join member_tb on m_num = re_member_num ";
        $query .= "where re_num = '" . $this->db->escape_str($re_num) . "' ";
        $query .= "limit 1 ";
        return $this->db->query($query)->row();
    }//end of get_comment_row()

    /**
     * 리뷰 수정
     * @param $re_num
     * @param array $query_data
     * @return bool
     */
    public function update_review($re_num, $query_data=array()) {
        if( empty($re_num) ) {
            return false;
        }

        return $this->db->where('re_num', $re_num)->update("review_tb", $query_data);
    }//end of update_comment()

    /**
     * 리뷰에 상품상세 노출여부 변경시 re_sort 재배열처리
     * @param $re_table_num
     * @return void
     */
    public function setReArrange($re_table_num) {
        if( empty($re_table_num) ) {
            return false;
        }

        $sql = "SELECT * FROM review_tb WHERE re_table_num = '{$re_table_num}' AND re_recommend = 'Y' ORDER BY re_regdatetime DESC ; ";
        $aLists = $this->db->query($sql)->result_array();

        foreach ($aLists as $key => $row) {
            $sort   = $key+1;
            $sql    = "UPDATE review_tb SET re_sort = '{$sort}' WHERE re_num = '{$row['re_num']}' ; ";
            $this->db->query($sql);
        }
    }//end of update_comment()





    /**
     * 댓글 등록
     * @param array $query_data
     * @return bool
     */
    public function insert_review($query_data=array()) {
        if(
            !isset($query_data['re_table']) || empty($query_data['re_table']) ||
            !isset($query_data['re_table_num']) || empty($query_data['re_table_num']) ||
            !isset($query_data['re_content']) || empty($query_data['re_content'])
        ) {
            return false;
        }

        if( $query_data['re_grade'] == 'A' ){
            $query_data['re_blind'] = 'N';
        }else{
            $query_data['re_blind'] = 'Y';
        }

        if( $query_data['re_grade'] == 'A' && $query_data['re_img'] != ''){
            $query_data['re_recommend'] = 'Y';
        }

        $query_data['re_regdatetime'] = current_datetime();

        if($this->db->insert("review_tb", $query_data)){
            $sql = "UPDATE product_tb SET p_review_count = p_review_count + 1 WHERE p_num = '{$query_data['re_table_num']}' ; ";
            $this->db->query($sql);
            return true;
        }else{
            return false;
        }
    }//end of insert_comment()


    public function delete($re_num,$p_num){
        $sql = "DELETE FROM review_tb WHERE re_num = ? ; ";
        $bResult = $this->db->query($sql,array($re_num));
        if($bResult){
            $sql = " UPDATE product_tb SET p_review_count = p_review_count - 1 WHERE p_num = '{$p_num}' ; ";
            $this->db->query($sql);
        }

        return $bResult;
    }

    /**
     * 리뷰 적립금 지급또는 삭제시 re_reward 업데이트용
     */
    public function updateReviewReward($re_num, $flag, $reward_type) {
        if( empty($re_num) ) {
            return false;
        }

        return $this->db->where('re_num', $re_num)->update("review_tb", array('re_reward' => $flag, 're_reward_type' => $reward_type));
    }

    /**
     * 리뷰 적립금 지급시 기지급 체크
     */
    public function chkReviewReward($re_num) {
        $query = "select * from review_tb where re_num = '" . $re_num . "'";
        $row = $this->db->query($query)->row();

        return $row;
    }

    public function setBestReviewSort($param)
    {
        $sort_index = $param['index']; // 순서바뀜을 하는 대상의 바뀐 행의 index번호

        if($param['type'] == 'up') {
            $re_sort = $sort_index + 1;
        } elseif($param['type'] == 'down') {
            $re_sort = $sort_index - 1;
        }

        // 순서바뀜을 당하는 행의 sort 번호 업데이트
        $query = $query = "update review_tb set re_sort = '" . $re_sort . "' where re_table_num = '" . $param['table_id'] . "' and re_sort = '" . $sort_index . "' ";
        $this->db->query($query);

        unset($query);

        // 순서바뀜을 하는 행의 sort 번호 업데이트
        $re_sort = $sort_index;
        $query = "update review_tb set re_sort = '" . $re_sort . "' where re_num = '" . $param['id'] . "' ";
        $this->db->query($query);

        if($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function updateReviewSort($param)
    {
        $query = "select * from review_tb where re_num = '" . $param['re_num'] . "' ";
        $row = $this->db->query($query)->row();

        $table_num = $row->re_table_num;

        if($param['setFlag'] == 'Y') { // 베스트 리뷰

            // 현재 re_sort 최대값을 가져와서 +1하여 업데이트함

            unset($query);
            $query = "select max(re_sort) as max_re_sort from review_tb where re_table_num = '" . $table_num. "' and re_recommend = 'Y' ";
            $row = $this->db->query($query)->row();

            $max_re_sort = $row->max_re_sort;

            if (empty($max_re_sort) || $max_re_sort < 1) {
                $max_re_sort = 1;
            } else {
                $max_re_sort = $max_re_sort + 1;
            }

            unset($query);
            $query = "update review_tb set re_sort = '" . $max_re_sort . "' where re_num = '" . $param['re_num'] . "' ";
            $this->db->query($query);

        } else if($param['setFlag'] == 'N') { // 베스트 리뷰 비활성

            // 비활성 대상의 re_sort값을 0으로 업데이트하고, 나머지(비활성대상의 re_sort보다 큰 값들) 베스트리뷰글의 re_sort -1 함.

            unset($query);
            $query = "update review_tb set re_sort = '0' where re_num = '" . $param['re_num'] . "' ";
            $this->db->query($query);

            $re_sort = $row->re_sort;

            unset($query);
            $query = "select * from review_tb where re_sort > '" . $re_sort . "' and re_table_num = '" . $table_num. "' and re_recommend = 'Y' ";
            $rows = $this->db->query($query)->result();

            if(count($rows) > 0) {
                unset($query);

                foreach($rows as $k => $v) {
                    $query = "update review_tb set re_sort = re_sort - 1 where re_num = '" . $v->re_num . "' ";
                    $this->db->query($query);
                }
            }
        }
    }

}//end of class Comment_model