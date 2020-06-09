<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 최상위 공통 컨트롤러 (CI_Controller 확장).
 */
class W_Controller extends CI_Controller {

    var $page_link;
    var $controller_dir;
    var $list_per_page = 20;
    var $default_set_rules = "trim|xss_clean|prep_for_form|strip_tags";
    var $isLogin = "N";
    var $isApp = "N";
    var $isApp_ios = "N";

    function __construct() {
        parent::__construct();

        $headers = apache_request_headers();
        if( strpos($headers['Authorization'], $this->config->item('kakao_app_key')['admin']) === false ) {
        //카카오 api 중 req시 text를 담아 넘기는 경우 header 에러발생
            if( !$this->input->is_cli_request() ) {
                //CLI 요청일때 세션 라이브러리 사용안함
                header('P3P: CP="NOI CURa ADMa DEVa TAIa OUR DELa BUS IND PHY ONL UNI COM NAV INT DEM PRE"');
                session_start();
            }

        }
        if( !$_SESSION['my_session_id'] ) {
            $_SESSION['my_session_id'] = create_session_id();
        }

        //기본 LCRUD 페이지 링크 설정
        $this->page_link = new stdClass();
        $this->controller_dir = "";
        if( $this->uri->segment(1) && ($this->uri->segment(1) != $this->router->fetch_class()) ) {
            $this->controller_dir .= "/" . $this->uri->segment(1);
        }

        //일반 클래스
        $this->page_link->base              = $this->controller_dir . "/" . $this->router->fetch_class();
        $this->page_link->list              = $this->controller_dir . "/" . $this->router->fetch_class() . "/list";
        $this->page_link->list_ajax         = $this->controller_dir . "/" . $this->router->fetch_class() . "/list_ajax";
        $this->page_link->detail            = $this->controller_dir . "/" . $this->router->fetch_class() . "/detail";
        $this->page_link->insert            = $this->controller_dir . "/" . $this->router->fetch_class() . "/insert";
        $this->page_link->insert_proc       = $this->controller_dir . "/" . $this->router->fetch_class() . "/insert_proc";
        $this->page_link->update            = $this->controller_dir . "/" . $this->router->fetch_class() . "/update";
        $this->page_link->update_proc       = $this->controller_dir . "/" . $this->router->fetch_class() . "/update_proc";
        $this->page_link->delete_proc       = $this->controller_dir . "/" . $this->router->fetch_class() . "/delete_proc";
        //공통
        $this->page_link->join              = $this->controller_dir . "/auth/join";
        $this->page_link->join_proc         = $this->controller_dir . "/auth/join_proc";
        $this->page_link->login             = $this->controller_dir . "/auth/login";
        $this->page_link->login_proc        = $this->controller_dir . "/auth/login_proc";
        $this->page_link->logout            = $this->controller_dir . "/auth/logout";


    }//end of __construct()

    /**
     * 기본 LCRUD 메서드 호출 설정
     * @param $method
     */
    function _remap($method){
        $default_method = $this->router->class . "_" . $method;

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
     * @param array|string $options
     */
    function _header($options=""){

        //넘겨받은 값이 true, false일때
        if( $options === true || $options === false ) {
            $no_header = $options;

            $options = array();
            $options['no_header'] = $no_header;
        }
        else {
            $no_header = (isset($options['no_header'])) ? $options['no_header'] : false;
        }

        $top_type = ( isset($options['top_type']) && !empty($options['top_type']) ) ? $options['top_type'] : 'menu';        //menu|back|search

        //공통
        $this->load->view('/header', array(
            'options'               => $options,
            'no_header'             => $no_header,
            'isLogin'               => $this->isLogin,
            'isApp'                 => $this->isApp,
            'isApp_ios'                 => $this->isApp_ios,
            'isAuthNo'              => $this->isAuthNo,
            'isAuthNo_chk'              => $this->isAuthNo_chk,
            'market_url'            => $this->market_url
        ));

        if( !$no_header ) {
            //menu|back|pop
            $this->load->view('/header_' . $top_type, array(
                'options'               => $options,
                'no_header'             => $no_header,
                'top_type'              => $top_type,
                'kwd'                   => $this->input->post_get('kwd', true)
            ));
        }
    }//end of header()

    /**
     * footer
     * @param array|string $options
     */
    function _footer($options=""){

        //넘겨받은 값이 true|false 일때
        if( $options === true || $options === false ) {
            $no_footer = $options;
            $options = array();
            $options['no_footer'] = $no_footer;
        }
        else {
            $no_footer = (isset($options['no_footer'])) ? $options['no_footer'] : false;
        }

        //top_type 기본값 : menu
        if ( !isset($options['top_type']) || empty($options['top_type']) ) {
            $top_type = "menu";
        }
        else {
            $top_type = $options['top_type'];
        }

        $sort_fix_yn = "N";


        $this->load->view('footer', array(
            'options'       => $options,
            'no_footer'     => $no_footer,
            'top_type'      => $top_type,
            'sort_fix_yn'   => $sort_fix_yn
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
     *                      sort            : 정렬(reverse|'')
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

        $page = ($param['page']) ? $param['page'] : 1;
        $total_page = ceil($config['total_rows'] / $config['per_page']);
        if( empty($total_page) ) {
            $total_page = 1;
        }
        if( $page > $total_page ) {
            $page = 1;
        }
        $limit = $config['per_page'];
        if( isset($param['sort']) && $param['sort'] == 'reverse' ) {
            //$start = ($total_page - $page) * $config['per_page'];
            $start = $config['total_rows'] - ($page * $config['per_page']);
            if( $start < 0 ) {
                $limit = $config['per_page'] - abs($start);
                $start = 0;
            }
        }
        else {
            $start = ($page - 1) * $config['per_page'];
        }

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

    public function _get_member_info(){
        $this->load->model('member_model');
        $aMemberInfo = $this->member_model->get_member_row(array('m_num' => $_SESSION['session_m_num']));
        return $aMemberInfo;
    }

    public function rctlyview_in($rctlydata){ //네이티브 최근본상품 db 저장.
        if($_SESSION['session_m_num']){
            $query_data = array();
            if ( !empty($rctlydata) ) {
                $query_data['rctlyViewPdt'] = $rctlydata;
            }
            $this->member_model->update_member($_SESSION['session_m_num'], $query_data);
        }
    }

    public function get_rctlyview() {
        $r_data = $this->member_model->get_member_view($_SESSION['session_m_num']);
        return $r_data;
    }
    /**
     * 코멘트 view 파일 load
     * @params @typo :: product, event , my
     * @params @num :: p_num, e_num, ''
     */

    public function ext_comment($type, $num = ''){

        $this->load->model('comment_model');

        if($type == 'my'){
            $aInput['where'] = array(
                'm_num'        => $num
            );
            $file_name = 'comment_my';
        }else{
            $aInput['where'] = array(
                'tb'        => $type
            ,   'tb_num'    => $num
            );
            $file_name = 'comment_default';
        }

        $aCommentLists = $this->comment_model->get_comment_list($aInput);
        $aExtData = array('aCommentLists' => $aCommentLists);
        $aExtView = array (
            'comment_view' => $this->load->view('/comment/'.$file_name, $aExtData, TRUE)
        );

        return $aExtView;

    }

}//end of class W_Controller