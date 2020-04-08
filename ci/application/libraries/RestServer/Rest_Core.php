<?php
namespace Restserver\Libraries;

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Class Rest_Core
 * @package Restserver\Libraries
 * W_Controller 역할 클래스
 */
class Rest_Core  {

    public $CI;
    public $doc_version;
    public $page_link;
    public $controller_dir;
    public $list_per_page = 20;
    public $isLogin = 'N';
    public $aMemberInfo = array();

    function __construct(){

        $this->CI = &get_instance();
        $this->CI->load->model('member_model');
        $this->doc_version = '1';

        $this->login();

    }

    private function login(){

        $headers = apache_request_headers();

        foreach ($headers as $header => $value) {

            if ($header == 'm_num') $m_num = $value;
            if ($header == 'm_key') $m_key = $value;
            if ($header == 'adid') $adid = $value;
            if ($header == 'fcm_id') $regid = $value;
            if ($header == 'key') $key = $value;  // API KEY

        }

        if ($m_num && $m_key) { //네이티브 세션제어

            $aInput = array(
                'm_num' => $m_num
            ,   'm_key' => $m_key
            );

            $member_row = $this->CI->member_model->get_member_row($aInput);
            if (empty($member_row) == false) {
                set_login_session($member_row);
                $this->aMemberInfo = $member_row;
                $this->isLogin = 'Y';

                {//회원정보가 있는경우 fcmid 와 adid 체크 후 update
                    if(empty($regid) == false || empty($adid) == false){
                        $aInput2 = array();
                        if(empty($adid) ==false) $aInput2['m_adid'] = $adid;
                        if(empty($regid) ==false) $aInput2['m_regid'] = $regid;
                        $this->CI->member_model->update_member($member_row['m_num'] , $aInput2);
                    }
                }

            }

        }

    }

    public function _get_member_info(){
        $aMemberInfo = $this->CI->member_model->get_member_row(array('m_num' => $_SESSION['session_m_num']));
        return $aMemberInfo;
    }

    public function getRctly() {
        $aMemberInfo = $this->_get_member_info();
        return $aMemberInfo['rctlyViewPdt'];
    }

    public function setRctly($rctlydata){ //네이티브 최근본상품 db 저장.

        if($_SESSION['session_m_num']){
            $query_data = array();
            if ( !empty($rctlydata) ) $query_data['rctlyViewPdt'] = $rctlydata;
            $this->CI->member_model->update_member($_SESSION['session_m_num'], $query_data);
        }

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
//        if( $page > $total_page ) {
//            $page = 1;
//        }
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

        return array(
            'start'         => $start,
            'limit'         => $limit,
            'total_page'    => $total_page
        );
    }//end of _paging()



}