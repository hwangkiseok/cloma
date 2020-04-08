<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 회원 관련 컨트롤러
 */
class Member extends A_Controller {

    var $list_per_page = 20;
    var $default_set_rules = "trim|xss_clean|prep_for_form|strip_tags";

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('member_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->member_list();
    }//end of index()

    private function _list_req() {
        $req = array();
        $req['kfd']             = trim($this->input->post_get('kfd', true));
        $req['kwd']             = trim($this->input->post_get('kwd', true));
        $req['date_type']       = trim($this->input->post_get('date_type', true));
        $req['date1']           = trim($this->input->post_get('date1', true));
        $req['date2']           = trim($this->input->post_get('date2', true));
        $req['state']           = trim($this->input->post_get('state', true));
        $req['j_path']          = trim($this->input->post_get('j_path', true));
        $req['sort_field']      = trim($this->input->get_post('sort_field', true));     //정렬필드
        $req['sort_type']       = trim($this->input->get_post('sort_type', true));      //정렬구분(asc, desc)
        $req['page']            = trim($this->input->post_get('page', true));
        $req['list_per_page']   = trim($this->input->post_get('list_per_page', true));
        $req['ph_yn']           = trim($this->input->post_get('ph_yn', true));          //휴대폰번호 유무
        $req['rejoin_yn']       = trim($this->input->post_get('rejoin_yn', true));      //재가입


        if( empty($req['page']) ) {
            $req['page'] = 1;
        }
        if( empty($req['list_per_page']) ) {
            $req['list_per_page'] = 20;
        }

        return $req;
    }//end of _list_req()

    /**
     * 회원 목록
     */
    public function member_list() {
        //request
        $req = $this->_list_req();

        $this->_header();

        $this->load->view('/member/member_list', array(
            'req'           => $req,
            'list_per_page' => $this->list_per_page
        ));

        $this->_footer();
    }//end of member_list()

    /**
     * 회원 목록 (Ajax)
     */
    public function member_list_ajax() {
        ajax_request_check(true);

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

        //전체수
        $list_count = $this->member_model->get_member_list($query_array, "", "", true);

        //페이징
        $page_result = $this->_paging(array(
            "total_rows"    => $list_count['cnt'],
            "base_url"      => "/member/list_ajax/?" . $PGV,
            "per_page"      => $req['list_per_page'],
            "page"          => $req['page'],
            "ajax"          => true
        ));

        //페이지번호 보정
        if( $req['page'] > $page_result['total_page'] ) {
            $req['page'] = $page_result['total_page'];
        }

        //목록
        $member_list = $this->member_model->get_member_list($query_array, $page_result['start'], $page_result['limit']);

        //상태별 회원수
        $member_count_array = array();
        //$member_count_array['total'] = $this->member_model->get_member_list("", "", "", true);

        unset($query_array['where']['state']);
        unset($query_array['where']['date1']);
        unset($query_array['where']['date2']);


        $member_count_array['total'] = $this->member_model->get_member_list($query_array, "", "", true);
        foreach( $this->config->item('member_state') as $key => $text ) {
            $query_array['where']['state'] = $key;
            //$member_count_array['state'][$key] = $this->member_model->get_member_list(array('where'=>array('state'=>$key)), "", "", true);
            $member_count_array['state'][$key] = $this->member_model->get_member_list($query_array, "", "", true);
            $member_count_array['state_per'][$key] = ($member_count_array['total']['cnt'] > 0) ? number_format($member_count_array['state'][$key]['cnt'] / $member_count_array['total']['cnt'] * 100, 2) : "0.00";
        }
        //임시회원 수
        //$sql = "select count(*) cnt from member_tb where m_state = '1' and m_loginid = ''";
        //$member_count_array['state']['99'] = $this->db->query($sql)->row('cnt');
        //$member_count_array['state_per']['99'] = number_format($member_count_array['state']['99'] / $member_count_array['state']['1'] * 100, 2);    //정상회원수 대비
        $query_array['where']['state'] = "99";
        $member_count_array['state']['99'] = $this->member_model->get_member_list($query_array, "", "", true);
        $member_count_array['state_per']['99'] = ($member_count_array['total']['cnt']) ? number_format($member_count_array['state']['99']['cnt'] / $member_count_array['total']['cnt'] * 100, 2) : "0.00";    //정상회원수 대비

        //정렬
        $sort_array = array();
        $sort_array['m_division'] = array("asc", "sorting");
        $sort_array['m_loginid'] = array("asc", "sorting");
        $sort_array['m_sns_site'] = array("asc", "sorting");
        $sort_array['m_join_path'] = array("asc", "sorting");
        $sort_array['m_logindatetime'] = array("asc", "sorting");
        $sort_array['m_regdatetime'] = array("asc", "sorting");
        $sort_array['m_state'] = array("asc", "sorting");
        $sort_array['m_order_count'] = array("asc", "sorting");
        $sort_array['m_email'] = array("asc", "sorting");
        $sort_array['m_nickname'] = array("asc", "sorting");

        $sort_array[$req['sort_field']][0] = ($req['sort_type'] == "asc") ? "desc" : "asc";
        $sort_array[$req['sort_field']][1] = ($req['sort_type'] == "asc") ? "sorting_asc" : "sorting_desc";

        $this->load->view('/member/member_list_ajax', array(
            'req'                   => $req,
            'GV'                    => $GV,
            'PGV'                   => $PGV,
            'sort_array'            => $sort_array,
            'member_count_array'    => $member_count_array,
            'list_count'            => $list_count,
            'list_per_page'         => $req['list_per_page'],
            'page'                  => $req['page'],
            'member_list'           => $member_list,
            'pagination'            => $page_result['pagination']
        ));
    }//end of member_list_ajax()

    /**
     * 회원 수정
     */
    public function member_update() {
        //request
        $req['m_num'] = $this->input->post_get('m_num', true);
        $req['hp'] = $this->input->post_get('hp', true);            //휴대폰번호
        $req['m_key'] = $this->input->post_get('m_key', true);
        $req['pop'] = $this->input->post_get('pop', true);          //팝업여부

        //row
        $member_row = $this->member_model->get_member_row(array('m_num' => $req['m_num'], 'hp' => $req['hp'], 'm_key' => $req['m_key']));

        if( empty($member_row) ) {
            alert(lang('site_error_empty_data'));
        }

        if( !empty($req['pop']) ) {
            $this->_header(true);
        }
        else {
            $this->_header();
        }

        $viewFile = "/member/member_update";

        $this->load->view($viewFile, array(
            "req"           => $req,
            "member_row"    => $member_row,
            "list_url"      => $this->_get_list_url()
        ));

        if( !empty($req['pop']) ) {
            $this->_footer(true);
        }
        else {
            $this->_footer();
        }
    }//end of member_update()

    /**
     * 회원 수정 처리 (Ajax)
     */
    public function member_update_proc() {
        ajax_request_check();

        //reqeust
        $req['m_num'] = $this->input->post_get('m_num', true);

        //회원 정보
        $member_row = $this->member_model->get_member_row(array('m_num' => $req['m_num']));

        if( empty($member_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
              "m_num" => array("field" => "m_num", "label" => "회원번호", "rules" => "required|is_natural|".$this->default_set_rules)
            , "m_admin_yn" => array("field" => "m_admin_yn", "label" => "관리자 회원", "rules" => "required|in_list[Y,N]|".$this->default_set_rules)
            , "m_state" => array("field" => "m_state", "label" => "회원상태", "rules" => "required|in_list[".get_config_item_keys_string("member_state")."]|".$this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $m_num = $this->input->post('m_num', true);
            $m_admin_yn = $this->input->post('m_admin_yn', true);
            $m_state = $this->input->post('m_state', true);

            if( empty($form_error_array) ) {
                //수정
                $query_data = array();
                $query_data['m_admin_yn'] = get_yn_value($m_admin_yn);
                $query_data['m_state'] = $m_state;
                if( $query_data['m_state'] == '2' ) {
                    $query_data['m_procdatetime'] = current_datetime();
                }
                else if( $query_data['m_state'] == '3' ) {
                    $query_data['m_deldatetime'] = current_datetime();

                    $m_key = $member_row->m_key;

                    $ex_key = explode("_",$m_key);
                    if($ex_key['1']){
                        $add_num = $ex_key['1']+1;
                        $edit_key = $ex_key['0']."_".$add_num;
                    }else{
                        $edit_key = $m_key."_1";
                    }

                    $query_data['m_login_chk'] = "Y";
                    $query_data['m_key'] = $edit_key;
                    $query_data['m_authno'] = "";
                }

                if( $this->member_model->update_member($m_num, $query_data) ) {
                    if( $query_data['m_state'] == '3' ) {
                        total_stat("join_del");
                    }

                    //관리자 회원으로 설정
                    if( $query_data['m_admin_yn'] == 'Y' ) {
                        $this->member_model->admin_member_insert($m_num);
                    }
                    //관리자 회원에서 제외
                    else {
                        $this->member_model->admin_member_delete($m_num);
                    }

                    result_echo_json(get_status_code('success'), lang('site_update_success'), true, 'alert');
                }
                else {
                    result_echo_json(get_status_code('error'), lang('site_update_fail'), true, 'alert');
                }
            }
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of member_update_proc()

    /**
     * 회원 인증번호 삭제 (ajax)
     */
    public function member_auth_delete() {
        ajax_request_check();

        //request
        $req['m_num'] = $this->input->post_get("m_num", true);

        if( empty($req['m_num']) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_id'), true, 'alert');
        }

        //회원정보
        $member_row = $this->member_model->get_member_row(array('m_num' => $req['m_num']));
        if( empty($member_row) ) {
            result_echo_json(get_status_code('error'), lang('site_error_empty_data'), true, 'alert');
        }

        //인증번호 삭제
        $query = "
            update member_tb
            set
                m_authno = ''
            where
                m_num = '" . $member_row->m_num . "'
        ";
        if( $this->db->query($query) ) {
            result_echo_json(get_status_code('success'), '', true, '');
        }
        else {
            result_echo_json(get_status_code('error'), lang('site_error_db'), true, 'alert');
        }
    }//end of member_auth_delete()

    /**
     * 회원태그 통계 (ajax)
     */
    public function member_tag_stat_ajax() {
        //request
        $req = $this->_list_req();

        $data = $this->member_model->member_tag_stat($req);

        $this->load->view("/member/member_tag_stat_ajax", array(
            'data' => $data
        ));
    }//end of member_tag_stat_ajax()

}//end of class member