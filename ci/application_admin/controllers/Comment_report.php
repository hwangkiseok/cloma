<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *댓글신고신고 관련 컨트롤러
 */
class Comment_report extends A_Controller {

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('comment_report_model');
        $this->load->model('comment_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->comment_report_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['cmt_num']         = trim($this->input->post_get('cmt_num', true));
        $req['m_num']           = trim($this->input->post_get('m_num', true));
        $req['sort_field']      = trim($this->input->post_get('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->post_get('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        return $req;
    }//end of _list_req()

    /**
     * 댓글신고 목록
     */
    public function comment_report_list() {
        //request
        $req = $this->_list_req();
        $req['pop'] = trim($this->input->post_get("pop", true));    //팝업여부(팝업일때 header, footer 필요)

        if( !empty($req['pop']) ) {
            $this->_header(true);
        }
        else {
            $this->_header();
        }

        $this->load->view("/comment_report/comment_report_list", array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        if( !empty($req['pop']) ) {
            $this->_footer(true);
        }
        else {
            $this->_footer();
        }
    }//end of comment_report_list()

    /**
     *댓글신고 목록 (Ajax)
     */
    public function comment_report_list_ajax() {
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
        $list_count = $this->comment_report_model->get_comment_report_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count,
            "base_url"      => "/comment_report/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //목록
        $comment_report_list = $this->comment_report_model->get_comment_report_list($query_array, $page_result['start'], $page_result['limit']);

        foreach ( $comment_report_list as $key => $row) {
            //댓글대상글 제목
            $table_num_row = $this->comment_model->get_table_data_row($row->cmt_table, $row->cmt_table_num, "select " . $this->config->item($row->cmt_table, "comment_table_num_name") . " as name");
            $row->table_num_name = $table_num_row->name;

            //작성자
            if( $row->cmt_admin == "Y" ) {
                $row->name = $row->cmt_name;
            }
            else {
                $row->name = $row->comment_m_nickname;
            }
        }//end of foreach()

        //정렬
        $sort_array = array();
        $sort_array['cmt_table'] = array("asc", "sorting");
        $sort_array['cmt_content'] = array("asc", "sorting");
        $sort_array['cmt_name'] = array("asc", "sorting");
        $sort_array['cmt_blind'] = array("asc", "sorting");
        $sort_array['cmt_blind_memo'] = array("asc", "sorting");
        $sort_array['cmt_display_state'] = array("asc", "sorting");
        $sort_array['cmt_regdatetime'] = array("asc", "sorting");
        $sort_array['rp_comment_num'] = array("asc", "sorting");
        $sort_array['rp_member_num'] = array("asc", "sorting");
        $sort_array['rp_reason'] = array("asc", "sorting");
        $sort_array['rp_regdatetime'] = array("asc", "sorting");
        $sort_array['RM.m_nickname'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view("/comment_report/comment_report_list_ajax", array(
            "req"                   => $req,
            "GV"                    => $GV,
            "PGV"                   => $PGV,
            "sort_array"            => $sort_array,
            "list_count"            => $list_count,
            "list_per_page"         => $req['list_per_page'],
            "page"                  => $req['page'],
            "comment_report_list"   => $comment_report_list,
            "pagination"            => $page_result['pagination']
        ));
    }//end of comment_report_list_ajax()

}//end of class Comment_report