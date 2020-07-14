<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 1:1문의 관련 컨트롤러
 */
class Board_qna extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('board_qna_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->board_qna_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['cate']            = trim($this->input->post_get('cate', true));
        $req['date_type']       = trim($this->input->post_get('date_type', true));
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));
        $req['ans_yn']          = trim($this->input->post_get('ans_yn', true));
        $req['bq_team']         = trim($this->input->post_get('bq_team', true));
        $req['secret_yn']       = trim($this->input->post_get('secret_yn', true));
        $req['usestate']        = trim($this->input->post_get('usestate', true));
        $req['m_num']           = trim($this->input->post_get('m_num', true));          //회원번호
        $req['rctly']           = trim($this->input->post_get('rctly', true));          //최근것만
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['view_type']       = trim($this->input->post_get('view_type', true));      //simple|null
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));
        $req['init_team']       = trim($this->input->post_get('init_team', true)); // 해당 팀 버튼 클릭시, 팀을 제외한 모든 검색조건 초기화
        $req['none_answer']     = trim($this->input->post_get('none_answer', true)); // 해당팀별 미답변/처리중 버튼 클릭시, 처리해야할 건수이외의 검색조건 초기화

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        return $req;
    }//end of _list_req()

    /**
     * 1:1문의 목록
     */
    public function board_qna_list() {
        //request
        $req = $this->_list_req();

        $this->_header();
        $this->load->view("/board_qna/board_qna_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of board_qna_list()

    /**
     * 1:1문의 목록 (Ajax)
     */
    public function board_qna_list_ajax() {
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
            $query_array['orderby'] = $req['sort_field'] . " " . $req['sort_type'];
        }

        //전체갯수
        $list_count = $this->board_qna_model->get_board_qna_list($query_array, "", "", true);

        if($req['init_team'] == 'Y' || $req['none_answer'] == 'Y') {
            $req['page'] = 1; // 팀/답변/처리중 버튼 클릭은 무조건 페이지 넘버1로 고정
        }

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => "/board_qna/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        if( $req['rctly'] == "Y" ) {

//            if( isset($query_array['orderby']) && !empty($query_array['orderby']) ) {
//                $order_query = "order by " . $query_array['orderby'] . " ";

            $query_array['orderby'] = ' bq_regdatetime ASC ';
            $board_qna_list = $this->board_qna_model->get_board_qna_list($query_array);
        }
        else {
            $board_qna_list = $this->board_qna_model->get_board_qna_list($query_array, $page_result['start'], $page_result['limit']);
        }

        //정렬
        $sort_array = array();
        $sort_array['bq_member_num'] = array("asc", "sorting");
        $sort_array['bq_category'] = array("asc", "sorting");
        $sort_array['bq_secret_yn'] = array("asc", "sorting");
        $sort_array['bq_content'] = array("asc", "sorting");
        $sort_array['bq_answer_yn'] = array("asc", "sorting");
        $sort_array['bq_answer_content'] = array("asc", "sorting");
        $sort_array['bq_adminuser_num'] = array("asc", "sorting");
        $sort_array['bq_regdatetime'] = array("asc", "sorting");
        $sort_array['bq_answerdatetime'] = array("asc", "sorting");
        $sort_array['bq_usestate'] = array("asc", "sorting");
        $sort_array['m_loginid'] = array("asc", "sorting");
        $sort_array['au_name'] = array("asc", "sorting");
        $sort_array['m_order_count'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/board_qna/board_qna_list_ajax", array(
            "req"               => $req,
            "GV"                => $GV,
            "PGV"               => $PGV,
            "sort_array"        => $sort_array,
            "list_count"        => $list_count,
            "list_per_page"     => $req['list_per_page'],
            "page"              => $req['page'],
            "board_qna_list"   => $board_qna_list,
            "pagination"        => $page_result['pagination']

        ));
    }//end of board_qna_list_ajax()

    /**
     * 1:1문의 답글
     */
    public function board_qna_answer_pop() {
        //request
        $req = $this->_list_req();
        $req['bq_num'] = $this->input->post_get('bq_num', true);

        //row
        $board_qna_row = $this->board_qna_model->get_board_qna_row($req['bq_num']);

        if( empty($board_qna_row) ) {
            alert(lang('site_error_empty_data'));
        }

        // 자주 사용하는 문구
        $word_use_list = $this->board_qna_model->get_word_use_list();

        $this->load->view("/board_qna/board_qna_answer_pop", array(
            'req' => $req,
            'board_qna_row' => $board_qna_row,
            'word_use_list' => $word_use_list,
            'list_url' => $this->_get_list_url()
        ));

    }//end of board_qna_answer_pop()

    /**
     * 1:1문의 답글 처리 (Ajax)
     */
    public function board_qna_answer_proc() {
        ajax_request_check();

        $m_name = '관리자';

        //if(isset($_SESSION['GroupMemberId']) == true && in_array($_SESSION['GroupMemberId'], $this->config->item('dev_test')) == true) {
            //request
            $req['bq_num'] = $this->input->post_get('bq_num', true);

            //row
            $board_qna_row = $this->board_qna_model->get_board_qna_row($req['bq_num']);

            if( empty($board_qna_row) ) {
                alert(lang('site_error_empty_data'));
            }

            $this->load->library('form_validation');

            //폼검증 룰 설정
            $set_rules_array = array(
                "bq_num" => array("field" => "bq_num", "label" => "글번호", "rules" => "required|is_natural|".$this->default_set_rules),
                "bq_answer_content" => array("field" => "bq_answer_content", "label" => "답글내용", "rules" => "required")
            );

            $this->form_validation->set_rules($set_rules_array);

            $form_error_array = array();

            //폼 검증 성공시
            if( $this->form_validation->run() === true ) {
                $bq_num = $this->input->post('bq_num', true);
                $bq_answer_content = $this->input->post('bq_answer_content');
                $send_push = $this->input->post('send_push');
                //$push_yn = $this->input->post('push_yn');

                if( empty($form_error_array) ) {
                    $query_data = array();
                    $query_data['bq_answer_yn'] = 'Y';

                    $query_data['bq_answer_content'] = $bq_answer_content;
                    $query_data['bq_answerdatetime'] = current_datetime();
                    $query_data['bq_adminuser_num'] = $_SESSION['session_au_num'];
                    $query_data['bq_last_writer']   = $m_name; // 해당 관리자의 세션 정보 저장(이름)

                    if( $this->board_qna_model->update_board_qna($bq_num, $query_data) ) {

                        // 수동푸시 활성화 또는 첫 답변일때 푸시 발송
                        //if($push_yn == 'Y' || $board_qna_row['bq_answerdatetime'] == '') {

                        // 첫 답변일때 푸시 발송
                        if($board_qna_row['bq_answerdatetime'] == '' || $send_push == 'Y') {

                            //앱 푸시 발송
                            if (!empty($board_qna_row['m_regid'])) {
                                /*
                                $push_data = array();
                                $push_data['title'] = "1:1문의 답변이 등록되었습니다.";
                                $push_data['body']  = "고객님이 문의하신 1:1문의글의 답변을 확인하세요.";
                                $push_data['page']  = "qna";
                                $resp = send_app_push($board_qna_row['m_regid'], $push_data);
                                */

                                $push_data = array();
                                $push_data['title'] = "1:1문의 답변이 등록되었습니다.";
                                $push_data['body']  = "고객센터로 문의하신 사항에 답변이 도착했습니다.";
                                $push_data['page']  = "qna";

                                send_app_push_log($board_qna_row['m_num'], $push_data);

                            }
                        }

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





        /*
        } else {




            //request
            $req['bq_num'] = $this->input->post_get('bq_num', true);

            //row
            $board_qna_row = $this->board_qna_model->get_board_qna_row($req['bq_num']);

            if (empty($board_qna_row)) {
                alert(lang('site_error_empty_data'));
            }

            $this->load->library('form_validation');

            //폼검증 룰 설정
            $set_rules_array = array(
                "bq_num" => array("field" => "bq_num", "label" => "글번호", "rules" => "required|is_natural|" . $this->default_set_rules),
                "bq_answer_content" => array("field" => "bq_answer_content", "label" => "답글내용", "rules" => "required")
            );

            $this->form_validation->set_rules($set_rules_array);

            $form_error_array = array();

            //폼 검증 성공시
            if ($this->form_validation->run() === true) {
                $bq_num = $this->input->post('bq_num', true);
                $bq_happy_talk = $this->input->post('bq_happy_talk', true);

                $bq_answer_content = $this->input->post('bq_answer_content');

                if (empty($form_error_array)) {
                    $query_data = array();
                    $query_data['bq_answer_yn'] = 'Y';
                    $query_data['bq_answer_content'] = $bq_answer_content;
                    $query_data['bq_happy_talk'] = $bq_happy_talk ? $bq_happy_talk : 'N';
                    $query_data['bq_adminuser_num'] = $_SESSION['session_au_num'];
                    $query_data['bq_answerdatetime'] = current_datetime();
                    $query_data['bq_last_writer'] = $m_name; // 해당 관리자의 세션 정보 저장(이름)

                    if ($this->board_qna_model->update_board_qna($bq_num, $query_data)) {
                        //앱 푸시 발송
                        if (!empty($board_qna_row->m_regid)) {
                            $push_data = array();
                            $push_data['title'] = "1:1문의 답변이 등록되었습니다.";
                            $push_data['msg'] = "고객님이 문의하신 1:1문의글의 답변을 확인하세요.";
                            //$push_data['badge'] = "Y";
                            $push_data['tarUrl'] = $this->config->item('site_http') . "/qna/detail/?bq_num=" . $board_qna_row->bq_num;

                            if ($board_qna_row->m_device_model == 'iPad' || $board_qna_row->m_device_model == 'iPhone') {
                                send_app_push_1($board_qna_row->m_regid, $push_data);
                            } else {
                                send_app_push($board_qna_row->m_regid, $push_data);
                            }
                        }

                        result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
                    } else {
                        result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
                    }
                }
            }//end of if(/폼 검증 성공 마침)

            //뷰 출력용 폼 검증 오류메시지 설정
            $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

            result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
        }
        */
    }//end of board_qna_answer_proc()

    /**
     * 1:1문의 삭제 처리 (Ajax)
     */
    public function board_qna_delete_proc() {
        ajax_request_check();

        //request
        $req['bq_num'] = $this->input->post_get('bq_num', true);

        //1:1문의 정보
        $board_qna_row = $this->board_qna_model->get_board_qna_row($req['bq_num']);

        if( empty($board_qna_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //1:1문의 삭제
        $query_data = array();
        $query_data['bq_display_state_2'] = 'N';
        //if( $this->board_qna_model->delete_board_qna($req['bq_num']) ) {
        if( $this->board_qna_model->update_board_qna($req['bq_num'], $query_data) ) {
            result_echo_json(get_status_code('success'), lang('site_delete_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_delete_fail'), true, 'alert');
        }
    }//end of board_qna_delete_proc()


    /**
     * 팀분류
     */
    public function board_qna_team_proc() {
        ajax_request_check();

        //request
        $req['bq_num'] = $this->input->post_get('bq_num', true);
        $req['bq_team'] = $this->input->post_get('bq_team', true);

        //글정보
        $board_qna_row = $this->board_qna_model->get_board_qna_row($req['bq_num']);

        if( empty($board_qna_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //팀 변경


        if( $this->board_qna_model->update_team($req['bq_num'], $req['bq_team']) ) {
            result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
        }
    }//end of board_qna_team_proc()

}//end of class board_qna