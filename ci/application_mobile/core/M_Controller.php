<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 최상위 공통 컨트롤러 (CI_Controller 확장).
 */
class M_Controller extends CI_Controller {

    var $page_link;
    var $page_navi_array = array();
    var $controller_dir;
    var $list_per_page = 20;
    var $default_set_rules = "trim|xss_clean|prep_for_form|strip_tags";
    var $isLogin = "N";
    var $isApp = "N";
    var $isApp_ios = "N";
    var $isAuthNo = "N";
    var $isAuthNo_chk = "N";
    var $market_url = "";
    var $aMemberInfo = array();
    public $en_ak = '';

    function __construct() {
        parent::__construct();

        //CLI 요청일때 세션 라이브러리 사용안함
        if( !is_cli() ) {
            header('P3P: CP="NOI CURa ADMa DEVa TAIa OUR DELa BUS IND PHY ONL UNI COM NAV INT DEM PRE"');
            session_start();
        }

//        if(1){ //benchmark
//            $this->load->library('Profiler');
//            $this->output->enable_profiler(TRUE);
//        }

        //세션아이디 수동 생성 (접속 후 변경되지 않는 세션아이디, CI 세션아이디는 주기적으로 변경되기 때문에 접속 후 변경되지 않는 세션아이디 필요시 사용함)
        if( !$_SESSION['my_session_id'] ) {
            $_SESSION['my_session_id'] = create_session_id();
        }

        //장바구니 쿠키id 생성
        if( !get_cookie("cookie_cart_id") ) {
            set_cookie("cookie_cart_id", "c_" . create_session_id(), get_strtotime_diff("+7 days"));
        }

        //기본 LCRUD 페이지 링크 설정
        $this->page_link = new stdClass();
        $this->controller_dir = "";
        if( $this->uri->segment(1) && ($this->uri->segment(1) != $this->router->fetch_class()) ) {
            $this->controller_dir .= "/" . $this->uri->segment(1);
        }

        if (member_login_status()) {
            $this->isLogin = 'Y';
            $this->aMemberInfo = self::_get_member_info();
        }



        //일반 클래스
        $this->page_link->base              = $this->controller_dir . "/" . $this->router->fetch_class();
        $this->page_link->list              = $this->controller_dir . "/" . $this->router->fetch_class() . "/list";
        $this->page_link->list_ajax         = $this->controller_dir . "/" . $this->router->fetch_class() . "/list_ajax";
        $this->page_link->detail            = $this->controller_dir . "/" . $this->router->fetch_class() . "/detail";
        $this->page_link->insert            = $this->controller_dir . "/" . $this->router->fetch_class() . "/insert";
        $this->page_link->insert_pop        = $this->controller_dir . "/" . $this->router->fetch_class() . "/insert_pop";
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

        self::app_login();

    }//end of __construct()

    private function app_login(){

        $headers = apache_request_headers();

        foreach ($headers as $header => $value) {
            if ($header == 'm_num') $m_num = $value;
            if ($header == 'm_key') $m_key = $value;
            if ($header == 'is_app') $is_app = $value;
        }

        if(is_app() || $is_app == 'Y') {

            $this->load->library('encryption');
            $this->load->model('member_model');

            if ($m_num && $m_key) { //네이티브 세션제어

                $aInput = array(
                    'm_num' => $m_num
                ,   'm_key' => $m_key
                );

                $member_row = $this->member_model->get_member_row($aInput);

                if (empty($member_row) == false) {
                    set_login_session($member_row);
                    //로그인 유지 쿠키
                    $auto_login_enc = $this->encryption->encrypt(time() . "|" . $member_row['m_sns_site'] . "|" . $member_row['m_sns_id']);
                    set_cookie('cookie_sal', $auto_login_enc, get_strtotime_diff("+1 years"));

                    $this->aMemberInfo = $member_row;
                    $this->isLogin = 'Y';
                    $this->en_ak = $auto_login_enc;
                }

            }else{ // 서브페이지에서 세션시간보다 오래 켜져 로그인이 풀리는경우 쿠키데이터로 대체하여 로그인 처리

                if ($this->isLogin != 'Y') {

                    $cookie_sal     = get_cookie('cookie_sal');
                    $fetch_class    = $this->router->class;
                    $fetch_method   = $this->router->method;

                    if(empty($cookie_sal) == false){

                        $this->en_ak = $cookie_sal;

                        $sSnsData = $this->encryption->decrypt($cookie_sal);
                        $aSnsData = explode('|',$sSnsData);
//                        $aSnsData[1]; //sns_site
//                        $aSnsData[2]; //sns_id

                        $member_row = $this->member_model->get_member_row(array('m_sns_site' => $aSnsData[1], 'm_sns_id' => $aSnsData[2]));

                        if (empty($member_row) == false) {
                            log_message('A','---------- 서브페이지에서 세션시간보다 오래 켜져 로그인이 풀리는경우 쿠키데이터로 대체하여 로그인 처리 : '.$sSnsData.' :: '.$fetch_class.' // '.$fetch_method);
                            set_login_session($member_row);
                            $this->aMemberInfo = $member_row;
                            $this->isLogin = 'Y';
                        }else{
                            log_message('A','---------- 서브페이지에서 세션시간보다 오래 켜져 로그인이 풀리는경우 쿠키데이터로 대체하여 로그인 처리 : 쿠키있으나 회원정보 없음 :: '.$sSnsData.' :: '.$fetch_class.' // '.$fetch_method);
                        }

                    }else{

                        if($fetch_class != 'product') log_message('A','---------- 서브페이지에서 세션시간보다 오래 켜져 로그인이 풀리는경우 쿠키데이터로 대체하여 로그인 처리 : 쿠키없음 :: '.$fetch_class.' // '.$fetch_method);

                    }

                }

            }

            if($this->isLogin != 'Y') { //위 방법으로 로그인이 안되는 경우 넘겨지는 파라메터가 있는지 확인 후 로그인 처리

                $fetch_class    = $this->router->class;
                $fetch_method   = $this->router->method;
                $loc_en_ak = $this->input->get('en_ak');

                if(empty($loc_en_ak) == false){

                    $aSnsData = $this->encryption->decrypt($loc_en_ak);
                    $this->en_ak = $loc_en_ak;
                    log_message('A','---------- 위 방법으로 로그인이 안되는 경우 넘겨지는 파라메터가 있는지 확인 후 로그인 처리 :: '.$aSnsData.' :: '.$fetch_class.' // '.$fetch_method);

                    $aSnsData = explode('|', $sSnsData);
    //              $aSnsData[1]; //sns_site
    //              $aSnsData[2]; //sns_id
                    $member_row = $this->member_model->get_member_row(array('m_sns_site' => $aSnsData[1], 'm_sns_id' => $aSnsData[2]));

                    if (empty($member_row) == false) {
                        set_login_session($member_row);
                        $this->aMemberInfo = $member_row;
                        $this->isLogin = 'Y';
                    }

                }

            }

        }

    }

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

        $lnb_menu = '';

        $aMemberInfo = $this->_get_member_info();

        if(is_app() == false){

            $aRecentlyProduct = get_recently_product(1);
            $aRecentlyProduct = array_shift($aRecentlyProduct);
            if($top_type == 'menu'){
                $lnb_menu = $this->load->view('/lnb', array( 'aMemberInfo' => $aMemberInfo , 'aRecentlyProduct' => $aRecentlyProduct ) , true );
    //            $rnb_menu = $this->load->view('/rnb', array( ) , true );
            }

            $depth1_nav = $depth2_nav = $depth3_nav = $depth4_nav = array();

            $aMenu      = $this->setMenu();
            $depth1_nav = $aMenu[0];
            $depth2_nav = $aMenu[1];
            $depth3_nav = $aMenu[2];
            //$depth4_nav = $aMenu[3];

        }

        //상품 정보가 있을때 : content 정보 수정
        $meta_content_array = array();
        $meta_content_array['title'] = $this->config->item("site_name_kr");
        $meta_content_array['og_title'] = $this->config->item("site_name_kr");


        if(
                $this->router->fetch_class() == 'product'
            &&  $this->router->fetch_method() == 'detail'
            &&  empty($options['aProductInfo']) == false
        ){

            $thumb_img = json_decode($options['aProductInfo']['p_rep_image'],true)[0];

            $meta_content_array['og_image'] = $thumb_img.'?t='.time();
            $meta_content_array['description'] = $options['aProductInfo']['p_summary'];
            $meta_content_array['url'] = $this->config->item('default_http').'/product/detail/'.$options['aProductInfo']['p_num'];

        }else{

            $meta_content_array['og_image'] = $this->config->item("site_thumb");
            $meta_content_array['description'] =  $this->config->item("site_description");
            $meta_content_array['url'] = $this->config->item('default_http');

        }



        //타이틀
        $title = "";
        if( isset($options['title']) && !empty($options['title']) ) {
            $title = $options['title'];
        }

        //공통
        $this->load->view('/header', array(
            'options'               => $options,
            'no_header'             => $no_header,
            'isLogin'               => $this->isLogin,
            'meta_content_array'    => $meta_content_array,
            'en_ak'                 => $this->en_ak
        ));

        if( is_app() ) $no_header = true;

        if( !$no_header ) {
            //menu|back|pop
            $this->load->view('/header_' . $top_type, array(
                'options'               => $options,
                'title'                 => $title,
                'no_header'             => $no_header,
                'top_type'              => $top_type,
                'lnb_menu'              => $lnb_menu,
//                'rnb_menu'              => $rnb_menu,
                'depth1_nav'            => $depth1_nav,
                'depth2_nav'            => $depth2_nav,
                'depth3_nav'            => $depth3_nav,
                'depth4_nav'            => $depth4_nav,
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

        $fetch_class = strtolower($this->router->fetch_class());

        $btm_fix_menu_yn = "N";

        $aRecentlyProduct = array();
        if($fetch_class == 'main' || $fetch_class == 'exhibition'){
            $btm_fix_menu_yn = 'Y';
//            $aRecentlyProduct = get_recently_product(1);
//            $aRecentlyProduct = array_shift($aRecentlyProduct);
        }
        if( !$no_footer ) {
            $this->load->view('footer', array(
                'options' => $options,
                'no_footer' => $no_footer,
                'top_type' => $top_type,
                'btm_fix_menu_yn' => $btm_fix_menu_yn,
                'aRecentlyProduct' => $aRecentlyProduct

            ));
        }
    }//end of footer()


    public function setMenu(){

        $fetch_class = strtolower($this->router->fetch_class());
        $fetch_method = strtolower($this->router->fetch_method());

        if($fetch_class == 'main' || $fetch_class == 'exhibition'){

            $depth1_nav = array(

                array(
                    'name'  => '홈'
                ,   'url'   => '/'
                ,   'active' => $fetch_class == 'main' && $fetch_method=='index' ? 'active' : ''
                )
            ,   array(
                    'name'  => '패션'
                ,   'url'   => '/Fashion'
                ,   'active' => $fetch_class == 'main' && $fetch_method=='fashion' ? 'active' : ''
                )
            ,   array(
                    'name'  => '베스트'
                ,   'url'   => '/Best'
                ,   'active' => $fetch_class == 'main' && $fetch_method=='best' ? 'active' : ''
                )
            ,   array(
                    'name'  => '기획전'
                ,   'url'   => '/Exhibition'
                ,   'active' =>$fetch_class=='exhibition' ? 'active' : ''
                )

            );

            if($fetch_method == 'fashion'){

                $depth3_nav = array(
                    array(
                        'name'  => '아웃터'
                    ,   'url'   => '#none'
                    ,   'ctgr_code' => 2
                    )
                ,   array(
                        'name'  => '원피스'
                    ,   'url'   => '#none'
                    ,   'ctgr_code' => 5
                    )
                ,   array(
                        'name'  => '상의'
                    ,   'url'   => '#none'
                    ,   'ctgr_code' => 3
                    )
                ,   array(
                        'name'  => '하의'
                    ,   'url'   => '#none'
                    ,   'ctgr_code' => 4
                    )
                ,   array(
                        'name'  => '홈웨어'
                    ,   'url'   => '#none'
                    ,   'ctgr_code' => 6
                    )
                ,   array(
                        'name'  => '패션잡화'
                    ,   'url'   => '#none'
                    ,   'ctgr_code' => 1
                    )

                );

            }

            if($fetch_method == 'best'){

                $depth3_nav = array(
                    array(
                        'name'  => 'Today베스트'
                    ,   'url'   => '#none'
                    ,   'best_code' => 'today'
                    )
                ,   array(
                        'name'  => '주간 베스트'
                    ,   'url'   => '#none'
                    ,   'best_code' => 'week'
                    )
                ,   array(
                        'name'  => '월간 베스트'
                    ,   'url'   => '#none'
                    ,   'best_code' => 'month'
                    )

                );

            }


            $in_arr = array( 'index' , 'fashion' , 'best');

            if(in_array( $fetch_method , $in_arr) == true){

                $main_sort_type = $this->input->get('sort_type');
                $main_view_type = $this->input->get('view_type');

                $depth4_nav = array(

                    array(
                            'name'      => '최신순'
                        ,   'param'     => 'recently'
                            //,   'active'    => $main_sort_type=='recently' ? 'active' : ''
                        ,   'active'    => 'active'
                        )
                    ,array(
                            'name'      => '낮은가격순'
                        ,   'param'     => 'l_p'
                        ,   'active'    => $main_sort_type=='l_p' ? 'active' : ''
                        )
                    ,array(
                            'name'      => '높은가격순'
                        ,   'param'     => 'h_p'
                        ,   'active'    => $main_sort_type=='h_p' ? 'active' : ''
                        )
                    ,array(
                            'name'      => '인기순'
                        ,   'param'     => 'ingi'
                        ,   'active'    => $main_sort_type=='ingi' ? 'active' : ''
                        )
                    ,array(
                            'name'      => 'icon'
                        ,   'param'     => 'view_type'
                        ,   'active'    => $main_view_type=='1' ? '2' : '1'
                        ,   'use_flag'  => $fetch_method != 'index' ? 'Y' : 'N'
                        )

                );

            }

        }

        $in_arr = array( 'notice' , 'faq');

        if(in_array($fetch_class , $in_arr)){

            $depth2_nav = array(
                array(
                    'name'  => '공지사항'
                ,   'url'   => '/Notice'
                ,   'active' => $fetch_class=='notice' ? 'active' : ''
                )
            ,   array(
                    'name'  => '자주하는 질문'
                ,   'url'   => '/Faq'
                ,   'active' => $fetch_class=='faq' ? 'active' : ''
                )
            );

        }

        $in_arr = array( 'wish' , 'comment' , 'cart' , 'delivery' , 'setting' , 'qna' , 'share');

        if(in_array($fetch_class , $in_arr)){

            $depth2_nav = array(
                array(
                    'name'  => '주문조회'
                ,   'url'   => '/delivery/'
                ,   'active' => $fetch_class=='delivery' ? 'active' : ''
                )
            ,   array(
                    'name'  => '장바구니'
                ,   'url'   => '/cart/'
                ,   'active' => $fetch_class=='cart' ? 'active' : ''
                )
            ,   array(
                    'name'  => '찜한상품'
                ,   'url'   => '/wish/'
                ,   'active' => $fetch_class=='wish' ? 'active' : ''
                )
            ,   array(
                    'name'  => '공유상품'
                ,   'url'   => '/share/'
                ,   'active' =>$fetch_class=='share' ? 'active' : ''
                )
            ,   array(
                    'name'  => '1:1문의'
                ,   'url'   => '/qna/'
                ,   'active' =>$fetch_class=='qna' ? 'active' : ''
                )
            ,   array(
                    'name'  => '댓글'
                ,   'url'   => '/comment/'
                ,   'active' =>$fetch_class=='comment' ? 'active' : ''
                )
//            ,   array(
//                    'name'  => '알림메시지'
//                ,   'url'   => '/push/'
//                ,   'active' =>$fetch_class=='push' ? 'active' : ''
//                )
            ,   array(
                    'name'  => '환경설정'
                ,   'url'   => '/setting/'
                ,   'active' => $fetch_class=='setting' ? 'active' : ''
                )

            );

        }

        return array($depth1_nav , $depth2_nav , $depth3_nav , $depth4_nav );

    }

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


    public function getRctly() {
        $aMemberInfo = $this->_get_member_info();
        return $aMemberInfo['rctlyViewPdt'];
    }

    public function setRctly($rctlydata){ //네이티브 최근본상품 db 저장.

        if($_SESSION['session_m_num']){
            $this->load->model('member_model');
            $query_data = array();
            if ( !empty($rctlydata) ) $query_data['rctlyViewPdt'] = $rctlydata;
            $this->member_model->update_member($_SESSION['session_m_num'], $query_data);
        }

    }

    /**
     * 코멘트 view 파일 load
     * @params @typo :: product, event , my
     * @params @num :: p_num, e_num, ''
     */

    public function ext_comment($type, $num = '' , $s = 0 , $l = 0 , $isAppend = false){

        $this->load->model('comment_model');

        if($l == 0) $l = $this->config->item('comment_limit');

        if($type == 'my'){
            $aInput['where'] = array(
                'm_num' => $num
            ,   'my'    => 'Y'
            );
            $file_name = 'comment_my';
        }else{
            $aInput['where'] = array(
                    'tb'        => $type
                ,   'tb_num'    => $num
            );
            $file_name = 'comment_default';
        }


        $nCommentLists = $this->comment_model->get_comment_list($aInput , '' , '' ,true);


        $aInput['orderby'] = 'cmt_regdatetime DESC';

        $aCommentLists = $this->comment_model->get_comment_list($aInput , $s , $l);

        $isNoMore = $l >= $nCommentLists ? true : false ;
        if($isNoMore == false) $isNoMore = $l > count($aCommentLists) ? true : false ;

        $aExtData = array(
              'aCommentLists'   => $aCommentLists
            , 'aInput'          => $aInput['where']
            , 'isAppend'        => $isAppend
            , 'isNoMore'        => $isNoMore
        );

        $aExtView = array (
            'comment_view' => $this->load->view('/comment/'.$file_name, $aExtData, TRUE)
        );

        return $aExtView;

    }

    public function _get_member_info(){
        $this->load->model('member_model');
        $aMemberInfo = $this->member_model->get_member_row(array('m_num' => $_SESSION['session_m_num']));
        return $aMemberInfo;
    }

    public function clearProductField($arr, $link_type = ''){

        if(empty($arr[0]['p_num']) == true){ //단일배열

            if(empty($link_type) == false ) $arr['link_type'] = $link_type;
            $arr['p_rep_image'] = json_decode($arr['p_rep_image'],true)[0];
            $arr['p_discount_rate'] = floor($arr['p_discount_rate']);

//            unset($arr['p_banner_image']);
            unset($arr['p_category']);
            unset($arr['p_order_link']);
            unset($arr['p_app_price_yn']);
            unset($arr['p_app_price']);
            unset($arr['p_price_second_yn']);
            unset($arr['p_price_second']);
            unset($arr['p_price_third_yn']);
            unset($arr['p_price_third']);
            unset($arr['p_hotdeal_condition_1']);
            unset($arr['p_hotdeal_condition_2']);
            unset($arr['total_margin']);
            unset($arr['total_margin_twoday']);
            unset($arr['p_display_info']);
            unset($arr['p_termlimit_yn']);
            unset($arr['p_termlimit_datetime1']);
            unset($arr['p_termlimit_datetime2']);
            unset($arr['p_view_count']);
            unset($arr['p_view_3day_count']);
            unset($arr['p_view_yesterday_count']);
            unset($arr['p_view_today_count']);
            unset($arr['p_click_count']);
            unset($arr['p_click_yesterday_count']);
            unset($arr['p_click_today_count']);
            unset($arr['p_click_count_week']);
            unset($arr['p_click_count_last_week']);
            unset($arr['p_comment_count']);
            unset($arr['p_review_count']);
            unset($arr['p_order_count']);
            unset($arr['p_order_count_3h']);
            unset($arr['p_order_count_twoday']);
            unset($arr['p_order_count_week']);
            unset($arr['p_order_count_month']);
            unset($arr['p_order_count_twomonth']);
            unset($arr['p_order_count_last_week']);
            unset($arr['p_regdatetime']);
            unset($arr['p_order']);
            unset($arr['p_display_state']);
            unset($arr['p_sale_state']);
            unset($arr['p_stock_state']);
            unset($arr['p_top_desc']);
            unset($arr['p_btm_desc']);
            unset($arr['p_search_cnt']);
            unset($arr['p_usd_price']);
            unset($arr['p_option_buy_cnt_view']);
            unset($arr['p_main_banner_view']);
            unset($arr['p_restock_cnt']);
            unset($arr['p_tot_order_count']);
            unset($arr['p_outside_display_able']);
            unset($arr['p_rep_image_add']);

        }else{ //순차배열

            foreach ($arr as $k => $r) {

                if(empty($link_type) == false ) $arr[$k]['link_type'] = $link_type;
                $arr[$k]['p_rep_image'] = json_decode($r['p_rep_image'],true)[0];
                $arr[$k]['p_discount_rate'] = floor($arr[$k]['p_discount_rate']);

//                unset($arr[$k]['p_banner_image']);
                unset($arr[$k]['p_category']);
                unset($arr[$k]['p_order_link']);
                unset($arr[$k]['p_app_price_yn']);
                unset($arr[$k]['p_app_price']);
                unset($arr[$k]['p_price_second_yn']);
                unset($arr[$k]['p_price_second']);
                unset($arr[$k]['p_price_third_yn']);
                unset($arr[$k]['p_price_third']);
                unset($arr[$k]['p_hotdeal_condition_1']);
                unset($arr[$k]['p_hotdeal_condition_2']);
                unset($arr[$k]['total_margin']);
                unset($arr[$k]['total_margin_twoday']);
                unset($arr[$k]['p_display_info']);
                unset($arr[$k]['p_termlimit_yn']);
                unset($arr[$k]['p_termlimit_datetime1']);
                unset($arr[$k]['p_termlimit_datetime2']);
                unset($arr[$k]['p_view_count']);
                unset($arr[$k]['p_view_3day_count']);
                unset($arr[$k]['p_view_yesterday_count']);
                unset($arr[$k]['p_view_today_count']);
                unset($arr[$k]['p_click_count']);
                unset($arr[$k]['p_click_yesterday_count']);
                unset($arr[$k]['p_click_today_count']);
                unset($arr[$k]['p_click_count_week']);
                unset($arr[$k]['p_click_count_last_week']);
                unset($arr[$k]['p_comment_count']);
                unset($arr[$k]['p_review_count']);
                unset($arr[$k]['p_order_count']);
                unset($arr[$k]['p_order_count_3h']);
                unset($arr[$k]['p_order_count_twoday']);
                unset($arr[$k]['p_order_count_week']);
                unset($arr[$k]['p_order_count_month']);
                unset($arr[$k]['p_order_count_twomonth']);
                unset($arr[$k]['p_order_count_last_week']);
                unset($arr[$k]['p_regdatetime']);
                unset($arr[$k]['p_order']);
                unset($arr[$k]['p_display_state']);
                unset($arr[$k]['p_sale_state']);
                unset($arr[$k]['p_stock_state']);
                unset($arr[$k]['p_top_desc']);
                unset($arr[$k]['p_btm_desc']);
                unset($arr[$k]['p_search_cnt']);
                unset($arr[$k]['p_usd_price']);
                unset($arr[$k]['p_option_buy_cnt_view']);
                unset($arr[$k]['p_main_banner_view']);
                unset($arr[$k]['p_restock_cnt']);
                unset($arr[$k]['p_tot_order_count']);
                unset($arr[$k]['p_outside_display_able']);
                unset($arr[$k]['p_rep_image_add']);

            }

        }

        return $arr;

    }

}//end of class M_Controller
