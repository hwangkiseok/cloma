<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 최상위 공통 컨트롤러 (CI_Controller 확장).
 */
class A_Controller extends CI_Controller {

    var $page_link;
    var $page_navi_array = array();
    var $controller_dir;
    var $list_per_page = 20;
    var $default_set_rules = "trim|xss_clean|prep_for_form|strip_tags";

    function __construct() {
        parent::__construct();

        if( !$this->input->is_cli_request() ) {
            session_start();
        }

        //로그인, 메뉴권한 체크 (로그인 컨트롤 제외)
        if( !$this->input->is_cli_request() && $this->uri->segment(1) != "auth" && $this->uri->segment(1) != "api" && $this->uri->segment(1) != "download" ) {
            adminuser_login_check();
        }

        //세션아이디 수동 생성 (접속 후 변경되지 않는 세션아이디, CI 세션아이디는 주기적으로 변경되기 때문에 접속 후 변경되지 않는 세션아이디 필요시 사용함)
        if( !$_SESSION['my_session_id'] ) {
            //$this->session->set_userdata('my_session_id', create_session_id());
            $_SESSION['my_session_id'] = create_session_id();
            //session_write_close();
        }

        //기본 LCRUD 페이지 링크 설정
        $this->page_link = new stdClass();
        $this->controller_dir = "";
        if( $this->uri->segment(1) != $this->router->fetch_class() ) {
            $this->controller_dir .= "/" . $this->uri->segment(1);
        }

        //일반
        $this->page_link->base              = $this->controller_dir . "/" . $this->router->fetch_class();
        $this->page_link->list              = $this->controller_dir . "/" . $this->router->fetch_class() . "/list";
        $this->page_link->list_ajax         = $this->controller_dir . "/" . $this->router->fetch_class() . "/list_ajax";
        $this->page_link->list_excel        = $this->controller_dir . "/" . $this->router->fetch_class() . "/list_excel";
        $this->page_link->insert            = $this->controller_dir . "/" . $this->router->fetch_class() . "/insert";
        $this->page_link->insert_pop        = $this->controller_dir . "/" . $this->router->fetch_class() . "/insert_pop";
        $this->page_link->insert_proc       = $this->controller_dir . "/" . $this->router->fetch_class() . "/insert_proc";
        $this->page_link->answer            = $this->controller_dir . "/" . $this->router->fetch_class() . "/answer";
        $this->page_link->answer_pop        = $this->controller_dir . "/" . $this->router->fetch_class() . "/answer_pop";
        $this->page_link->answer_proc       = $this->controller_dir . "/" . $this->router->fetch_class() . "/answer_proc";
        $this->page_link->update            = $this->controller_dir . "/" . $this->router->fetch_class() . "/update";
        $this->page_link->update_pop        = $this->controller_dir . "/" . $this->router->fetch_class() . "/update_pop";
        $this->page_link->update_pop_dev    = $this->controller_dir . "/" . $this->router->fetch_class() . "/update_pop_dev";
        $this->page_link->update_proc       = $this->controller_dir . "/" . $this->router->fetch_class() . "/update_proc";
        $this->page_link->delete_proc       = $this->controller_dir . "/" . $this->router->fetch_class() . "/delete_proc";
        //공통
        $this->page_link->login             = $this->controller_dir . "/auth/login";
        $this->page_link->login_proc        = $this->controller_dir . "/auth/login_proc";
        $this->page_link->logout            = $this->controller_dir . "/auth/logout";


    }//end of __construct()

    /**
     * 기본 LCRUD 메서드 호출 설정
     * @param $method
     */
    function _remap($method){
        $default_method = $this->router->fetch_class() . "_" . $method;

        if( method_exists($this, $default_method) ) {
            $this->{"{$default_method}"}();
        }
        else if( method_exists($this, $method) ) {
            $this->{"{$method}"}();
        }
        else {
            show_404();
        }
    }//end of _remap()

    /**
     * header
     * @param bool $no_header
     */
    function _header($no_header=false){
        $this->load->view('header', array(
            'no_header' => $no_header
        ));
    }//end of header()

    /**
     * footer
     * @param bool $no_footer
     */
    function _footer($no_footer=false){
        $this->load->view('footer', array(
            'no_footer' => $no_footer
        ));
    }//end of footer()

    /**
     * 페이징
     * @param $param    =>  total_rows      : 전체갯수
     *                      base_url        : URL
     *                      per_page        : 페이지당 출력수
     *                      page            : 현재페이지
     *                      skin            : 스킨(1=관리자(기본값), 2=사용자1)
     *                      page_var_str    : 페이지 변수명(기본값:page)
     *                      ajax            : ajax 요청 여부(true|false)
     * @return array    =>  start           : 시작위치 (목록에서 쿼리문(limit문)에서 사용함)
     *                      limit           : 목록에 출력할 갯수 (목록에서 쿼리문(limit문)에서 사용함)
     *                      pagination      : 페이징 HTML
     */
    function _paging($param=array()){
        $this->load->library('pagination');


        $config['base_url'] = $param['base_url'];
        $config['total_rows'] = $param['total_rows'];
        $config['per_page'] = ($param['per_page']) ? $param['per_page'] : 20;
        $config['num_links'] = 4;
        $config['use_page_numbers'] = true;
        $config['page_query_string'] = true;
        $config['enable_query_strings'] = true;
        $config['query_string_segment'] = ( isset($param['page_var_str']) && !empty($param['page_var_str']) ) ? $param['page_var_str'] : 'page';

        //$page = ($param['page']) ? $param['page'] : 1;
        $page = ($param['page']) ? $param['page'] : 1;
        $total_page = ceil($config['total_rows'] / $config['per_page']);
        if( empty($total_page) ) {
            $total_page = 1;
        }
        if( $page > $total_page ) {
            $page = 1;
        }
        $start = ($page - 1) * $config['per_page'];
        $limit = $config['per_page'];

        if( !isset($param['skin']) ) {
            $param['skin'] = 1;
        }
        $add_class = "";
        if( isset($param['ajax']) && ($param['ajax'] === true) ) {
            $add_class = " ajax";
        }

        //스킨(관리자)
        if( $param['skin'] == 1 ){
            $config['full_tag_open']    = '<ul class="pagination pagination-sm'.$add_class.'">';
            $config['full_tag_close']   = '</ul>';
            $config['first_link']       = '<span aria-hidden="true">&lt;&lt;</span>';
            $config['first_tag_open']   = '<li>';
            $config['first_tag_close']  = '</li>';
            $config['last_link']        = '<span aria-hidden="true">&gt;&gt;</span>';
            $config['last_tag_open']    = '<li>';
            $config['last_tag_close']   = '</li>';
            $config['next_link']        = '<span aria-hidden="true">&gt;</span>';
            $config['next_tag_open']    = '<li>';
            $config['next_tag_close']   = '</li>';
            $config['prev_link']        = '<span aria-hidden="true">&lt;</span>';
            $config['prev_tag_open']    = '<li>';
            $config['prev_tag_close']   = '</li>';
            $config['cur_tag_open']     = '<li class="active"><a href="#none">';
            $config['cur_tag_close']    = '</a></li>';
            $config['num_tag_open']     = '<li>';
            $config['num_tag_close']    = '</li>';
        }

        $this->pagination->initialize($config);
        $pagination = $this->pagination->create_links();

        return array(
            'start'         => $start,
            'limit'         => $limit,
            'pagination'    => $pagination,
            'total_page'    => $total_page
        );
    }//end of _paging()

    /**
     * 목록 페이지 URL 추출 (query_string이 있으면 붙임)
     * @return string
     */
    public function _get_list_url() {
        $list_url = $this->page_link->list;
        if ( $this->input->server('QUERY_STRING') ) {
            $list_url .= "/?" . $this->input->server('QUERY_STRING');
        }

        return $list_url;
    }//end of _get_list_url()

}//end of class A_Controller