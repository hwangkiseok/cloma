<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 1:1문의
 */
class Notice extends M_Controller
{

    var $back_url = "/";

    public function __construct()
    {
        parent::__construct();

        //model
        $this->load->model('board_help_model');

    }//end of __construct()

    private function _list_req() {
        $req = array();
        $req['cate']            = trim($this->input->post_get('cate', true));
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['sort_field']      = trim($this->input->post_get('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->post_get('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));

        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 5;
        }

        return $req;
    }//end of _list_req()

    public function index()
    {

        //request
        $req = $this->_list_req();

        //최상위 노출
        $query_data =  array();
        $query_data['where'] = $req;
        $query_data['where']['div'] = 1;
        $query_data['where']['top_yn'] = 'Y';
        $query_data['where']['usestate'] = 'Y';


        $top_list = $this->board_help_model->get_board_help_list($query_data);


        $query_data =  array();
        $query_data['where'] = $req;
        $query_data['where']['div'] = 1;
        $query_data['where']['top_yn'] = 'N';
        $query_data['where']['usestate'] = 'Y';

        //전체갯수
        $list_count = $this->board_help_model->get_board_help_list($query_data, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => $this->page_link->list_ajax,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        $notice_list = $this->board_help_model->get_board_help_list($query_data, $page_result['start'], $page_result['limit']);

        $options = array('title' => '공지사항' , 'top_type' => 'back');
        $this->_header($options);

        $this->load->view('/notice/index', array(
            'req'           => $req,
            'back_url'      => $this->back_url,
            'notice_list'      => $notice_list,
            'top_list'      => $top_list,
        ));

        $this->_footer();

    }//end of index()

}//end of class Qna