<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\Rest_Core;

defined('BASEPATH') OR exit('No direct script access allowed');

//To Solve File REST_Controller not found
require APPPATH . 'libraries/RestServer/REST_Controller.php';
require APPPATH . 'libraries/RestServer/Format.php';
require APPPATH . 'libraries/RestServer/Rest_Core.php'; // W_Controller 클래스에서 사용된 메소드이관

/**
 * 게시판 컨트롤러
 */
class Board extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('rest');
        $this->core = new Rest_Core(); // Core Class (MyController 코어클래스와 같은역할)

    }//end of __construct()

    /**
     * 공지사항
     */
    public function notice_get()
    {

        $req                    = array();
        $req['page']            = $this->get('page');
        $req['list_per_page']   = $this->get('list_per_page');

        if( empty($req['page']) ) $req['page'] = 1;
        if( empty($req['list_per_page']) ) $req['list_per_page'] = 999;

        $this->load->model('board_help_model');

        $query_data =  array();
        $query_data['where'] = $req;
        $query_data['where']['div'] = 1;
        $query_data['where']['usestate'] = 'Y';

        //전체갯수
        $list_count = $this->board_help_model->get_board_help_list($query_data, "", "", true);

        //페이징
        $page_result = $this->core->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        $aTmpNoticeList = $this->board_help_model->get_board_help_list($query_data, $page_result['start'], $page_result['limit']);

        $aNoticeList = array();
        foreach ($aTmpNoticeList as $k => $r) {
            $aNoticeList[$k]['bh_subject']         = $r['bh_subject'];
            $aNoticeList[$k]['bh_content']         = $r['bh_content'];
            $aNoticeList[$k]['bh_regdatetime']     =  view_date_format($r['bh_regdatetime'],4);
        }

        if(empty($aNoticeList) == true){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang('site_error_empty_data'), true, "", "", "" ), REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code;

        }else{

            $this->set_response(
                result_echo_rest_json(get_status_code("success"), "", true, "", "",
                    array(
                        'aList'   => $aNoticeList
                    ,   'req'     => $req
                    )
                ), REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code;

        }

    }//end of index()

    /**
     * 자주하는 질문
     */
    public function faq_get()
    {

        $req                    = array();
        $req['page']            = $this->get('page');
        $req['list_per_page']   = $this->get('list_per_page');

        $req['app_search_text'] = $this->get('search_text');
        $req['ctgr']            = $this->get('ctgr');

        if( empty($req['page']) ) $req['page'] = 1;
        if( empty($req['list_per_page']) ) $req['list_per_page'] = 999;

        $this->load->model('board_help_model');

        $query_data =  array();
        $query_data['where'] = $req;
        $query_data['where']['div'] = 2;
        $query_data['where']['usestate'] = 'Y';
        if(empty($req['ctgr']) == false) $query_data['where']['cate'] = $req['ctgr'];
        if(empty($req['app_search_text']) == false) $query_data['where']['app_search_text'] = $req['app_search_text'];


        //전체갯수
        $list_count = $this->board_help_model->get_board_help_list($query_data, "", "", true);

        //페이징
        $page_result = $this->core->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        $aTmpFaqList = $this->board_help_model->get_board_help_list($query_data, $page_result['start'], $page_result['limit']);
        $aFaqList = array();
        foreach ($aTmpFaqList as $k => $r) {
            $aFaqList[$k]['bh_subject']         = $r['bh_subject'];
            $aFaqList[$k]['bh_content']         = $r['bh_content'];
            $aFaqList[$k]['bh_regdatetime']     =  view_date_format($r['bh_regdatetime'],4);
            $aFaqList[$k]['bh_category_str']    = $this->config->item($r['bh_category'],'faq_category');
        }

        if(empty($aFaqList) == true){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang('site_error_empty_data'), true, "", "", ""), REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code;

        }else{

            $this->set_response(
                result_echo_rest_json(get_status_code("success"), "", true, "", "",
                    array(
                        'aList' => $aFaqList
                    ,   'req'   => $req
                    )
                ), REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code;

        }

    }//end of index()

}//end of class Board

