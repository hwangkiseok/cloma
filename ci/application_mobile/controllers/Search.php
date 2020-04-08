<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 검색결과
 */
class Search extends M_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('product_model');

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
            $req['list_per_page'] = 10;
        }

        return $req;
    }//end of _list_req()

    public function index()
    {

        $req = $this->_list_req();

        $query_data = array();
        $query_data['where'] = $req;

        //전체갯수
        $list_count = $this->product_model->get_product_list($query_data, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => $this->page_link->list_ajax,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        $aProductLists      = $this->product_model->get_product_list($query_data,$page_result['start'], $page_result['limit']);

        $options = array('title' => '검색' , 'top_type' => 'back');

        $this->_header($options);

        $this->load->view('/search/index', array(
            'req'               => $req,
            'list_count'        => $list_count,
            'total_page'        => $page_result['total_page'],
            'aProductLists'     => $aProductLists
        ));

        $this->_footer();

    }//end of index()


    public function search_list_ajax(){

        ajax_request_check();

        $req = $this->_list_req();

        $query_data = array();
        $query_data['where'] = $req;

        //전체갯수
        $list_count = $this->product_model->get_product_list($query_data, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => $this->page_link->list_ajax,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        $aProductLists      = $this->product_model->get_product_list($query_data,$page_result['start'], $page_result['limit']);

        $this->load->view('/search/ajax_list', array(
            'req'               => $req,
            'list_count'        => $list_count,
            'total_page'        => $page_result['total_page'],
            'aProductLists'     => $aProductLists
        ));

    }

}//end of class Search
