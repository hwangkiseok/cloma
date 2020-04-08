<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\Rest_Core;

defined('BASEPATH') OR exit('No direct script access allowed');

//To Solve File REST_Controller not found
require APPPATH . 'libraries/RestServer/REST_Controller.php';
require APPPATH . 'libraries/RestServer/Format.php';
require APPPATH . 'libraries/RestServer/Rest_Core.php'; // W_Controller 클래스에서 사용된 메소드이관

/**
 * 상품 관련 컨트롤러
 */
class Comment extends REST_Controller
{

    public $core;

    public function __construct()
    {
        parent::__construct();

        //model
        $this->load->model('product_model');
        $this->load->model('comment_model');
        $this->load->model('member_model');
        $this->load->helper('rest');

        $this->core = new Rest_Core(); // Core Class (MyController 코어클래스와 같은역할)

    }

    /**
     * 댓글리스트
     */
    public function list_get()
    {

        $cmt_table      = $this->get('tb_table', true);
        $cmt_table_num  = $this->get('tb_table_num', true);
        $req['list_per_page']   = $this->get('list_per_page')?$this->get('list_per_page'):50;
        $req['page']            = $this->get('page')?$this->get('page'):1;

        if( $cmt_table != 'my' && (empty($cmt_table) == true ||  empty($cmt_table_num) == true ) ){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang('site_error_required'), true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code

        } else {

            if($cmt_table == 'my'){
                $aInput['where'] = array(
                    'm_num'        => $_SESSION['session_m_num']
                );
            }else{
                $aInput['where'] = array(
                    'tb'        => $cmt_table
                ,   'tb_num'    => $cmt_table_num
                );
            }

            //전체갯수
            $list_count = $this->comment_model->get_comment_list($aInput, "", "", true);

            //페이징
            $page_result = $this->core->_paging(array(
                "total_rows"    => $list_count['cnt'],
                "per_page"      => $req['list_per_page'],
                "page"          => $req['page'],
                "ajax"          => true
            ));

            $isEnd = false;
            if($req['list_per_page']*($req['page']-1) >= $list_count['cnt'] ){
                $isEnd = true;
            }

            if($isEnd == false){
                $aInput['orderby'] = 'cmt_regdatetime DESC';
                $aCommentLists = $this->comment_model->get_comment_list($aInput , $page_result['start'], $page_result['limit']);
            }

            if($cmt_table == 'product'){//댓글전체수

                $query_array                    = array();
                $query_array['where']['tb']     = $cmt_table;
                $query_array['where']['tb_num'] = $cmt_table_num;

                $this->load->model('comment_model');
                $nComment = $this->comment_model->get_comment_list($query_array , '' , '' ,true);
            }


            if (empty($aCommentLists) == false) {
                $this->set_response(

                    result_echo_rest_json(get_status_code("success"), "", true, "", "",
                        array(
                                "aCommentLists"  => $aCommentLists
                            ,   "nComment"      => $nComment['cnt']
                            ,   "isEnd" => $isEnd
                        )
                    ), REST_Controller::HTTP_OK
                ); // OK (200) being the HTTP response code;

            } else {

                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), "댓글이 존재하지 않습니다.", true, "", "", "" ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code

            }

        }

    }

    /**
     * 댓글등록
     */
    public function insert_put()
    {

        $cmt_table      = $this->put('cmt_table', true);
        $cmt_table_num  = $this->put('cmt_table_num', true);
        $cmt_content    = $this->put('cmt_content', true);

        //회원정보
        $member_row = $this->core->_get_member_info();

        //금칙어 목록 배열
        $this->load->model('banned_words_model');
        $banned_words_array = $this->banned_words_model->get_banned_words_array();

        if( !empty($banned_words_array) ) {
            $banned_words_str = implode("|", $banned_words_array);
            if( preg_match("/" . $banned_words_str . "/i", $cmt_content) )  $bBannedWord = true;
        }

        if(     empty($cmt_table) == true
            ||  empty($cmt_table_num) == true
            ||  empty($cmt_content) == true
        ){
            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang('site_error_required'), true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code
        }else if($bBannedWord) {
            $this->set_response(
                result_echo_rest_json(get_status_code("error"), '댓글내용에 금칙어가 있습니다.', true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code
        }else{

            //등록
            $query_data = array();
//            if( $member_row['m_admin_yn'] == 'Y' ) {
//                $query_data['cmt_admin']        = 'Y';
//                $query_data['cmt_member_num']   = $_SESSION['session_m_num'];
//                $query_data['cmt_name']         = $this->config->item('comment_admin_name');
//            }
//            else {
            $query_data['cmt_member_num'] = $member_row['m_num'];
            if( !empty($member_row['m_nickname']) ) {
                $query_data['cmt_name'] = $member_row['m_nickname'];
            }
//            }
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

                $this->set_response(
                    result_echo_rest_json(get_status_code("success"), '문의가 등록되었습니다.', true, "", "", ""
                    ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code

            }
            else {
                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), '문의가 실패했습니다.', true, "", "", ""
                    ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code
            }
        }//end of if()

    }

    /*
     * 댓글 삭제 proc
     * */
    public function delete_delete(){

        //request
        $req['cmt_num'] = $this->delete('cmt_num', true);

        //댓글 조회
        $comment_row = $this->comment_model->get_comment_row($req['cmt_num'], $_SESSION['session_m_num']);


        if( empty($comment_row) ) {

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang('site_error_empty_data'), true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code

        }else{

            $member_row = $this->core->_get_member_info();

            //댓글 삭제
            if( $this->comment_model->delete_comment($req['cmt_num'], $_SESSION['session_m_num']) ) {
                //댓글수--
                $query = str_replace("#TB_NUM#", $comment_row->cmt_table_num, $this->config->item($comment_row->cmt_table, "comment_table_count_delete_query"));
                $this->db->query($query);

                //회원 댓글수--
                $this->member_model->publicUpdate('member_tb', array('m_comment_count' => (int)$member_row['m_comment_count']-1 ), array('m_num',$member_row['m_num']));

                $this->set_response(
                    result_echo_rest_json(get_status_code("success"), lang("site_delete_success"), true, "", "", ""
                    ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code

            } else {
                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), lang("site_delete_fail"), true, "", "", ""
                    ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code

            }

        }

    }//end of comment_delete_proc();

}

