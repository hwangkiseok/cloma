<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 댓글 관련 컨트롤러
 */
class Comment extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('comment_model');
        $this->load->helper('url');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->comment_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['ym']              = trim($this->input->post_get('ym', true));
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['tb']              = trim($this->input->post_get('tb', true));
        $req['tb_num']          = trim($this->input->post_get('tb_num', true));
        $req['m_num']           = trim($this->input->post_get('m_num', true));          //회원번호
        $req['p_name']          = trim($this->input->post_get('p_name', true));          //회원번호
        $req['blind']           = trim($this->input->post_get('blind', true));
        $req['admin']           = trim($this->input->post_get('admin', true));
        $req['state']           = trim($this->input->post_get('state', true));
        $req['rctly']           = trim($this->input->post_get('rctly', true));          //최근것만
        $req['sort_field']      = trim($this->input->post_get('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->post_get('sort_type', true));      //정렬구분(asc, desc)
        $req['view_type']       = trim($this->input->post_get('view_type', true));      //simple|null
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));
        $req['main']            = trim($this->input->post_get('main', true));
        $req['member_only']     = trim($this->input->post_get('member_only', true));    //회원것만 출력여부(Y|null)
        $req['cmt_gubun']       = trim($this->input->post_get('cmt_gubun', true));
        $req['cmt_flag']        = trim($this->input->post_get('cmt_flag', true));
        $req['reply_cnt']       = trim($this->input->post_get('reply_cnt', true));      //답댓글유무(Y|N|'')
        $req['pop']             = trim($this->input->post_get("pop", true));
        $req['dev'] = $this->input->post_get('dev', true);

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 1;
        }

        return $req;
    }//end of _list_req()

    /**
     * 댓글 추가 정보 설정
     * @param $row
     * @return array
     */
    private function _get_comment_row_info($row) {
        if( empty($row) ) {
            return array();
        }

        //작성자
        if( $row->cmt_admin == "Y" ) {
            $row->name = $row->cmt_name;
        }
        else {
            if($row->m_nickname){
                $row->name = $row->m_nickname;
            }else{
                $row->name = '[닉네임없음]';
            }

        }

        //댓글내용에 reply 회원명 처리
        if( !empty($row->cmt_reply_member_name) ) {
            $name_array = explode("::", $row->cmt_reply_member_name);

            foreach ($name_array as $key => $item) {
                $name = str_replace(":", "", $item);
                $row->cmt_content = str_replace($name, "<em>" . $name ."</em>", $row->cmt_content);
            }
        }

        //답댓글 갯수 (쿼리가 느림!!!!!!, 현재사용안해서 주석처리함, 190114/김홍주) (사용하려면 갯수만 따로 필드를 빼서 답댓글 등록시마다 업데이트하는 방법을 사용해야 할 듯)
        //$query = "select count(*) as cnt from comment_tb ";
        //$query .= "where cmt_display_state = 'Y' ";
        //$query .= "and cmt_blind = 'N' ";
        //$query .= "and cmt_reply_comment_num like '%:" . $row->cmt_num . ":%' ";
        //$reply_count = $this->db->query($query)->row('cnt');
        //$row->reply_count = $reply_count;
        $row->reply_count = 0;

        //zsView($query);

        return $row;
    }//end of _get_comment_row_info()


    /**
     * 댓글 목록
     */
    public function comment_list() {
        //request
        $req = $this->_list_req();
        $req['pop'] = trim($this->input->post_get("pop", true));    //팝업여부(팝업일때 header, footer 필요)

        if( !empty($req['pop']) ) {
            $this->_header(true);
        }
        else {
            $this->_header();
        }

        $this->load->view("/comment/comment_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        if( !empty($req['pop']) ) {
            $this->_footer(true);
        }
        else {
            $this->_footer();
        }
    }//end of comment_list()

    /**
     * 댓글 목록 데이터 (ajax)
     */
    public function comment_list_ajax() {

        ajax_request_check(true);

        //request
        $req = $this->_list_req();

        $pgv_array = $req;
        unset($pgv_array['page']);

        $gv_array = $pgv_array;
        $gv_array['page'] = $req['page'];

        $PGV = http_build_query($pgv_array);
        $GV = http_build_query($gv_array);

        //쿼리 배열
        $query_array =  array();
        $query_array['where'] = $req;
        if( !empty($req['sort_field']) && !empty($req['sort_type']) ) {
            //상품상세 & 대상글제목로 정렬
            if( $req['sort_field'] == "target_subject" && $req['tb'] == "product" ) {
                $query_array['orderby'] = "product_tb.p_name " . $req['sort_type'];
            }
            else {
                $query_array['orderby'] = $req['sort_field'] . " " . $req['sort_type'];
            }
        }

        //전체갯수
        $list_count = $this->comment_model->get_comment_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => "/comment/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
            //"sort"          => "reverse"
        ));

        $comment_list = $this->comment_model->get_comment_list($query_array, $page_result['start'], $page_result['limit']);

        foreach ($comment_list as $key => $row) {
            if(empty($row['cmt_answertime']) == false){
                $comment_list[$key]['delay_time'] = dayDiff_second($row['cmt_regdatetime'],$row['cmt_answertime']);
            }else{
                $comment_list[$key]['delay_time'] = dayDiff_second($row['cmt_regdatetime'],date('YmdHis'));
            }
        }

        //정렬
        $sort_array = array();
        $sort_array['cmt_table'] = array("asc", "sorting");
        $sort_array['cmt_admin'] = array("asc", "sorting");
        $sort_array['cmt_name'] = array("asc", "sorting");
        $sort_array['cmt_member_num'] = array("asc", "sorting");
        $sort_array['cmt_content'] = array("asc", "sorting");
        $sort_array['cmt_gubun'] = array("asc", "sorting");
        $sort_array['cmt_regdatetime'] = array("asc", "sorting");
        $sort_array['cmt_blind'] = array("asc", "sorting");
        $sort_array['cmt_blind_memo'] = array("asc", "sorting");
        $sort_array['cmt_best_order'] = array("asc", "sorting");
        $sort_array['cmt_report_count'] = array("asc", "sorting");
        $sort_array['cmt_display_state'] = array("asc", "sorting");
        $sort_array['m_nickname'] = array("asc", "sorting");
        $sort_array['m_order_count'] = array("asc", "sorting");
        $sort_array['target_subject'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/comment/comment_list_ajax", array(
            "req"           => $req,
            "GV"            => $GV,
            "PGV"           => $PGV,
            "sort_array"    => $sort_array,
            "list_count"    => $list_count,
            "list_per_page" => $req['list_per_page'],
            "page"          => $req['page'],
            "comment_list"  => $comment_list,
            "pagination"    => $page_result['pagination']
        ));
    }//end comment_list_ajax;

    /**
     * 댓글 등록
     */
    public function comment_insert_pop() {
        //request
        $req = $this->_list_req();

        $word_use_list = $profile_array = array();


        $this->load->view("/comment/comment_insert_pop", array(
            'req'           => $req,
            'list_url'      => $this->_get_list_url(),
            'profile_array' => $profile_array,
            'word_use_list' => $word_use_list
        ));
    }//end of comment_insert_pop()

    /**
     * 댓글 등록 처리 (Ajax)
     */
    public function comment_insert_proc() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "cmt_table"         => array("field" => "cmt_table", "label" => "구분", "rules" => "required|in_list[" . get_config_item_keys_string('comment_table') ."]|".$this->default_set_rules),
            "cmt_table_num"     => array("field" => "cmt_table_num", "label" => "댓글 대상", "rules" => "required|is_natural|".$this->default_set_rules),
            "cmt_name"          => array("field" => "cmt_name", "label" => "작성자명", "rules" => "required|".$this->default_set_rules),
            "cmt_profile_img"   => array("field" => "cmt_profile_img", "label" => "프로필이미지", "rules" => $this->default_set_rules),
            "cmt_display_state" => array("field" => "cmt_display_state", "label" => "노출여부", "rules" => "required|in_list[" . get_config_item_keys_string('comment_display_state') ."]|".$this->default_set_rules),
            "cmt_content"       => array("field" => "cmt_content", "label" => "내용", "rules" => "required|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $cmt_table          = trim($this->input->post("cmt_table", true));
            $cmt_table_num      = trim($this->input->post("cmt_table_num", true));
            $cmt_name           = trim($this->input->post("cmt_name", true));
            $cmt_profile_img    = trim($this->input->post("cmt_profile_img", true));
            $cmt_display_state  = trim($this->input->post("cmt_display_state", true));
            $cmt_content        = trim($this->input->post("cmt_content", true));

            if( empty($form_error_array) ) {
                $query_data = array();
                $query_data['cmt_table'] = $cmt_table;
                $query_data['cmt_table_num'] = $cmt_table_num;
                $query_data['cmt_admin'] = "Y";
                $query_data['cmt_name'] = $cmt_name;
                $query_data['cmt_profile_img'] = $cmt_profile_img;
                $query_data['cmt_display_state'] = $cmt_display_state;
                $query_data['cmt_content'] = $cmt_content;

                if( $this->comment_model->insert_comment($query_data) ) {
                    //댓글 갯수 업데이트
                    $this->comment_model->update_table_data_count($cmt_table, $cmt_table_num, "insert");

                    result_echo_json(get_status_code('success'), lang('site_insert_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_insert_fail'), true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of comment_insert_proc()

    /**
     * 댓글 수정
     */
    public function comment_update_pop() {
        //request
        $req = $this->_list_req();
        $req['cmt_num'] = trim($this->input->post_get("cmt_num", true));

        //댓글 정보
        $comment_row = $this->comment_model->get_comment_row($req['cmt_num']);
        if( empty($comment_row) ) {
            result_echo_json(get_status_code("error"), lang("site_error_empty_data"), true, "alert");
        }

        //댓글대상글 제목
        $table_num_row = $this->comment_model->get_table_data_row($comment_row['cmt_table'], $comment_row['cmt_table_num'], "select " . $this->config->item($comment_row['cmt_table'], "comment_table_num_name") . " as name");
        $comment_row['table_num_name'] = $table_num_row['name'];

        $profile_array = array();
        // 자주 사용하는 문구
        $word_use_list = $this->comment_model->get_word_use_list();

        $this->load->view("/comment/comment_update_pop", array(
            'req'           => $req,
            'list_url'      => $this->_get_list_url(),
            'comment_row'   => $comment_row,
            'profile_array' => $profile_array,
            'word_use_list' => $word_use_list
        ));
    }//end of comment_insert_pop()

    /**
     * 참고(노출)여부 변경 토글 추가 dhkim 20190411
     */
    public function comment_blind_proc() {
        ajax_request_check();

        //request
        $req['cmt_num'] = $this->input->post_get('cmt_num', true);
        $req['cmt_blind'] = $this->input->post_get('cmt_blind', true);

        //글정보
        $comment_row = $this->comment_model->get_comment_row($req['cmt_num']);

        if( empty($comment_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        // 참고처리
        $query_data['cmt_blind'] = $req['cmt_blind'];
        if( $req['cmt_blind'] == "Y" ) {
            $query_data['cmt_blind_regdatetime'] = current_datetime();
        }
        else {
            $query_data['cmt_blind_regdatetime'] = "";
        }

        if( $this->comment_model->update_comment($req['cmt_num'], $query_data) ) {
            result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
        }

    }//end of board_qna_team_proc()


    /**
     * 댓글 수정 처리 (Ajax)
     */
    public function comment_update_proc() {
        ajax_request_check();

        //request
        $req['cmt_num'] = trim($this->input->post_get("cmt_num", true));

        //댓글 정보
        $comment_row = $this->comment_model->get_comment_row($req['cmt_num']);

        if( empty($comment_row) ) {
            result_echo_json(get_status_code("error"), lang("site_error_empty_data"), true, "alert");
        }

        $this->load->library('form_validation');

        $cmt_name_set_rules = $this->default_set_rules;
        $cmt_content_set_rules = $this->default_set_rules;
        if( $comment_row['cmt_admin'] == "Y" ) {
            $cmt_name_set_rules .= "|required";
            $cmt_content_set_rules .= "|required";
        }

        //폼검증 룰 설정
        $set_rules_array = array(
            "cmt_num"           => array("field" => "cmt_num", "label" => "댓글번호", "rules" => "required|is_natural|" . $this->default_set_rules),
            "cmt_name"          => array("field" => "cmt_name", "label" => "작성자명", "rules" => $cmt_name_set_rules),
            "cmt_profile_img"   => array("field" => "cmt_profile_img", "label" => "프로필이미지", "rules" => $this->default_set_rules),
            "cmt_blind"         => array("field" => "cmt_blind", "label" => "참고", "rules" => "required|in_list[" . get_config_item_keys_string('comment_blind') ."]|".$this->default_set_rules),/*블라인드*/
            "cmt_blind_memo"    => array("field" => "cmt_blind_memo", "label" => "참고 메모", "rules" => "max_length[200]" . $this->default_set_rules),/*블라인드*/
            "cmt_display_state" => array("field" => "cmt_display_state", "label" => "노출여부", "rules" => "required|in_list[" . get_config_item_keys_string('comment_display_state') ."]|".$this->default_set_rules),
            "cmt_content"       => array("field" => "cmt_content", "label" => "내용", "rules" => $cmt_content_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $cmt_num            = trim($this->input->post("cmt_num", true));
            $cmt_name           = trim($this->input->post("cmt_name", true));
            $cmt_profile_img    = trim($this->input->post("cmt_profile_img", true));
            $cmt_blind          = trim($this->input->post("cmt_blind", true));
            $cmt_blind_memo     = trim($this->input->post("cmt_blind_memo", true));
            $cmt_display_state  = trim($this->input->post("cmt_display_state", true));
            $cmt_content        = trim($this->input->post("cmt_content", true));


            if( empty($form_error_array) ) {
                $query_data = array();
                if( $comment_row['cmt_admin'] == "Y" ) {
                    $query_data['cmt_name'] = $cmt_name;
                    $query_data['cmt_profile_img'] = $cmt_profile_img;
                }
                $query_data['cmt_blind'] = $cmt_blind;
                $query_data['cmt_blind_memo'] = $cmt_blind_memo;
                $query_data['cmt_display_state'] = $cmt_display_state;
                if( $comment_row['cmt_admin'] == "Y" ) {
                    $query_data['cmt_content'] = $cmt_content;
                }
                if( $cmt_blind == "Y" ) {
                    $query_data['cmt_blind_regdatetime'] = current_datetime();
                }
                else {
                    $query_data['cmt_blind_regdatetime'] = "";
                }

                if( $this->comment_model->update_comment($cmt_num, $query_data) ) {
                    result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of comment_update_proc()

    /**
     * 댓글 구분별 데이터 목록
     */
    public function comment_table_data_list_ajax() {
        ajax_request_check();

        //request
        $req['tb'] = trim($this->input->post_get("tb", true));
        $req['kwd'] = trim($this->input->post_get("kwd", true));
        $req['page'] = trim($this->input->post_get("page", true));

        if( empty($req['tb']) ) {
            exit;
        }

        $pgv_array = $req;
        unset($pgv_array['page']);

        $gv_array = $pgv_array;
        $gv_array['page'] = $req['page'];

        $PGV = http_build_query($pgv_array);
        $GV = http_build_query($gv_array);        
        
        //테이블 확인
        $table = $req['tb'] . "_tb";
        $query = "show tables like '" . $table . "'";
        $table_row = $this->db->query($query)->row();

        if( empty($table_row) ) {
            exit;
        }

        //쿼리 배열
        $query_array =  array();
        $query_array['tb'] = $req['tb'];
        $query_array['where'] = $req;

        //전체갯수
        $list_count = $this->comment_model->get_table_data_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/comment/table_data_list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        //$table_data_list = $this->comment_model->get_table_data_list($query_array, $page_result['start'], $page_result['limit']);
        $table_data_list = $this->comment_model->get_table_data_list($query_array);

        $this->load->view("/comment/comment_table_" . $req['tb'], array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "table_data_list"   => $table_data_list,
            "pagination"        => $page_result['pagination']
        ));
    }//end of comment_table_data_list_ajax()

    /**
     * 댓글 삭제 처리 (Ajax)
     */
    public function comment_delete_proc() {
        ajax_request_check();

        //request
        $req['cmt_num'] = trim($this->input->post_get('cmt_num', true));

        //댓글 정보
        $comment_row = $this->comment_model->get_comment_row($req['cmt_num']);

        if( empty($comment_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //댓글 삭제
        if( $this->comment_model->delete_comment($req['cmt_num']) ) {
            //댓글 갯수 업데이트
            $this->comment_model->update_table_data_count($comment_row->cmt_table, $comment_row->cmt_table_num, "delete");

            if( !empty($comment_row->cmt_parent_num) ) {
                //답댓글 갯수 업데이트
                $this->comment_model->update_comment_reply_count($comment_row->cmt_parent_num);
            }

            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of comment_delete_proc()

    /**
     * 베스트 노출순서 수정 (Ajax)
     */
    public function comment_best_order_proc() {
        ajax_request_check();

        $data = $this->input->post('data', true);       //배열 ([cmt_num] => order 형식)
        
        if( empty($data) ) {
            result_echo_json(get_status_code('error'), lang('site_no_data'), true, 'alert');
        }

        foreach( $data as $key => $value ) {
            $cmt_num = $key;
            $cmt_best_order = $value;

            $this->comment_model->best_order_update_comment($cmt_num, $cmt_best_order);
        }//end of foreach()

        result_echo_json(get_status_code("success"), lang("site_update_success"), true, "alert");
    }//end of product_md_order_proc()

    /**
     * 답댓글 등록 팝업
     */
    public function comment_reply_pop() {
        //request
        $req['cmt_num'] = trim($this->input->post_get('cmt_num', true));

        //조회
        $comment_row = $this->comment_model->get_comment_row($req['cmt_num']);
        if( empty($comment_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, "alert");
        }

        //회원정보
        $this->load->model('member_model');
        $member_row = $this->member_model->get_member_row(array('m_num' => $comment_row['cmt_member_num']));
        if( !empty($member_row) ) {
            $comment_row['cmt_member_num'] = $member_row['m_num'];
            $comment_row['cmt_name'] = $member_row['m_nickname'];
        }

        $word_use_list = $this->comment_model->get_word_use_list();

        $this->load->view('/comment/comment_reply_pop', array(
            'req'           => $req,
            'comment_row'   => $comment_row,
            'member_row'    => $member_row,
            'word_use_list'  => $word_use_list
        ));
    }//end of comment_reply_pop()

    /**
     * 답댓글 등록 처리 (ajax)
     */
    public function comment_reply_proc() {
        ajax_request_check();

        //request
        $req['cmt_num'] = trim($this->input->post_get('cmt_num', true));
        $req['cmt_flag'] = trim($this->input->post_get('cmt_flag', true));
        $req['send_push'] = trim($this->input->post_get('send_push', true));
        $req['cmt_content'] = $this->input->post_get('cmt_content', true);

        //조회
        $comment_row = $this->comment_model->get_comment_row($req['cmt_num']);
        if( empty($comment_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, "alert");
        }

        //회원정보
        $this->load->model('member_model');
        $member_row = $this->member_model->get_member_row(array('m_num' => $comment_row->cmt_member_num));

        //회원정보가 있을때
        if( !empty($member_row) ) {
            $comment_row['cmt_member_num'] = $member_row['m_num'];
            $comment_row['cmt_name'] = $member_row['m_nickname'];
        }

        //등록
        $query_data                     = array();
        $query_data['cmt_answer']       = $req['cmt_content'];
        $query_data['cmt_answertime']   = current_datetime();
        $query_data['cmt_table_num']    = $comment_row['cmt_table_num'];
        $seq                            = $comment_row['cmt_num'];

        if( $this->comment_model->update_comment($seq , $query_data) == false ) {
            result_echo_json(get_status_code('success'), lang('site_insert_fail'), true, 'alert');
        }

        if(empty($comment_row['cmt_answertime']) == true) {

            if($comment_row['cmt_table'] == 'product'){
                $sql = "SELECT * FROM product_tb WHERE p_num = '{$comment_row['cmt_table_num']}';";
                $oResult = $this->db->query($sql);
                $aResult = $oResult->row_array();
                $title_str = $aResult['p_name'];
            }

            if(mb_strlen($title_str) > 5) $title_str = mb_substr($title_str, 0, 5, 'utf-8')."...";
            else $title_str = $title_str;

            $push_data = array();
            $push_data['title'] = "[{$title_str}] 상품에 새로운 대댓글이 달렸습니다.";
            $push_data['body']  = "";
            $push_data['page']  = "comment";

            send_app_push_log($comment_row['cmt_member_num'], $push_data);

        }


        result_echo_json(get_status_code('success'), lang('site_insert_success'), true, 'alert');

    }//end of comment_reply_proc()


    public function _send_push($member_row)
    {
        //푸시 발송
        $push_data = array();
        $push_data['title'] = "문의에 대한 답변이 등록되었습니다.";
        $push_data['msg'] = "고객님의 문의에 답변을 확인하세요.";

        $push_data['tarUrl'] = $this->config->item('site_http') . "/comment/list/?my=Y";

        if( $member_row->m_device_model == 'iPad' || $member_row->m_device_model == 'iPhone' ){
            send_app_push_1($member_row->m_regid, $push_data);
        }else{
            send_app_push($member_row->m_regid, $push_data);
        }
    }
}//end of class Comment