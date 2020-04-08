<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 댓글
 */
class Comment extends M_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('comment_model');

        //로그인, 메뉴권한 체크 (로그인 컨트롤 제외)
        if( $this->uri->segment(2) != "list_ajax" ) {
            member_login_check();
        }

    }//end of __construct()

    public function index()
    {

        $aMemberInfo = $this->_get_member_info();

        $aInput['where'] = array('m_num' => $_SESSION['session_m_num']);
        $nCommentLists = $this->comment_model->get_comment_list($aInput , '' , '' , true);

        $ext_comment = $this->ext_comment('my',$aMemberInfo['m_num']);

        $options = array('title' => '댓글' , 'top_type' => 'back');

        $this->_header($options);
        $this->load->view('/comment/index', array( 'ext_comment' => $ext_comment , 'nCommentLists' => $nCommentLists ) );
        $this->_footer();

    }//end of index()

    public function comment_list_ajax()
    {

        $cmt_num = $this->input->post('cmt_num');
        $type = $this->input->post('type');
        $page = $this->input->post('page');
        $append = $this->input->post('append')=='false'?false:true;

        $s = ($page-1) * $this->config->item('comment_limit');
        $ext_comment = $this->ext_comment($type , $cmt_num , $s , '' , $append);

        echo $ext_comment['comment_view'];

    }//end of index()

    /*
     * 댓글 저장 proc
     * */
    public function comment_insert_proc(){
        ajax_request_check();
        member_login_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "cmt_table" => array("field" => "cmt_table", "label" => "댓글구분", "rules" => "required|in_list[" . get_config_item_keys_string('comment_table') ."]|" . $this->default_set_rules),
            "cmt_table_num" => array("field" => "cmt_table_num", "label" => "댓글대상", "rules" => "required|is_natural|" . $this->default_set_rules),
            "cmt_content" => array("field" => "cmt_content", "label" => "댓글내용", "rules" => "required|max_length[1000]|" . $this->default_set_rules),
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $cmt_table = $this->input->post('cmt_table', true);
            $cmt_table_num = $this->input->post('cmt_table_num', true);
            $cmt_content = $this->input->post('cmt_content', true);

            //회원정보
            $member_row = $this->_get_member_info();

            //금칙어 목록 배열
            $this->load->model('banned_words_model');
            $banned_words_array = $this->banned_words_model->get_banned_words_array();

            if( !empty($banned_words_array) ) {
                $banned_words_str = implode("|", $banned_words_array);

                if( preg_match("/" . $banned_words_str . "/i", $cmt_content) ) {
                    $form_error_array['cmt_content'] = "댓글내용에 금칙어가 있습니다.";
                }
            }

            if( empty($form_error_array) ) {
                //등록
                $query_data = array();
                if( $member_row['m_admin_yn'] == 'Y' ) {
                    $query_data['cmt_admin']        = 'Y';
                    $query_data['cmt_member_num']   = $_SESSION['session_m_num'];
                    $query_data['cmt_name']         = $this->config->item('comment_admin_name');
                }
                else {
                    $query_data['cmt_member_num'] = $member_row['m_num'];
                    if( !empty($member_row['m_nickname']) ) {
                        $query_data['cmt_name'] = $member_row['m_nickname'];
                    }
                }
                $query_data['cmt_table']        = $cmt_table;
                $query_data['cmt_table_num']    = $cmt_table_num;
                $query_data['cmt_content']      = $cmt_content;

                if( $this->comment_model->insert_comment($query_data) ) {
                    total_stat("comment");

                    //댓글 대상 댓글수 ++
                    $query = str_replace("#TB_NUM#", $cmt_table_num, $this->config->item($cmt_table, "comment_table_count_update_query"));
                    $this->db->query($query);

                    //회원 댓글수++
                    $this->member_model->publicUpdate('member_tb', array('m_comment_count' => (int)$member_row['m_comment_count']+1 ), array('m_num',$member_row['m_num']));

                    $query_data                     = array();
                    $query_data['where']['tb']        = $cmt_table;
                    $query_data['where']['tb_num']    = $cmt_table_num;

                    $tot_cnt = $this->comment_model->get_comment_list($query_data,'','',true);

                    result_echo_json(get_status_code('success'), "문의가 등록되었습니다.", true, "alert" , array() , array('tot_cnt' => $tot_cnt));

                }
                else {
                    result_echo_json(get_status_code('error'), "문의 등록에 실패했습니다.", true, "alert");
                }
            }//end of if()
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);

    }//end of comment_insert_proc();

    /*
     * 댓글 삭제 proc
     * */
    public function comment_delete_proc(){
        ajax_request_check();
        member_login_check();

        //request
        $req['cmt_num'] = $this->input->post_get('cmt_num', true);

        //댓글 조회
        $comment_row = $this->comment_model->get_comment_row($req['cmt_num'], $_SESSION['session_m_num']);

        if( empty($comment_row) ) {
            if( $this->input->is_ajax_request() ) {
                header("HTTP/1.1 500 " . rawurlencode(lang('site_error_default')));
                exit;
            }
            else {
                alert(lang('site_error_default'));
            }
        }

        $member_row = $this->_get_member_info();

        //댓글 삭제
        if( $this->comment_model->delete_comment($req['cmt_num'], $_SESSION['session_m_num']) ) {
            //댓글수--
            $query = str_replace("#TB_NUM#", $comment_row['cmt_table_num'], $this->config->item($comment_row['cmt_table'], "comment_table_count_delete_query"));
            $this->db->query($query);

            //회원 댓글수--
            $this->member_model->publicUpdate('member_tb', array('m_comment_count' => (int)$member_row['m_comment_count']-1 ), array('m_num',$member_row['m_num']));

            $query_data                     = array();
            $query_data['where']['tb']        = $comment_row['cmt_table'];
            $query_data['where']['tb_num']    = $comment_row['cmt_table_num'];
            $tot_cnt = $this->comment_model->get_comment_list($query_data,'','',true);

            result_echo_json(get_status_code('success'), lang("site_delete_success"), true, "alert", array() , array('tot_cnt' => $tot_cnt));
        }
        else {
            result_echo_json(get_status_code('error'), lang("site_delete_fail"), true, "alert");
        }

    }//end of comment_delete_proc();


}//end of class Comment