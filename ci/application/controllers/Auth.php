<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 로그인/로그아웃 관련 컨트롤러
 * - 앱 : 카카오, 네이버
 * - 웹 : 카카오 (Kakao.php)
 */
class Auth extends W_Controller {

    var $join_path;

    public function __construct() {
        parent::__construct();

        //model
        $this->load->model('member_model');

        //library
        $this->load->library('encryption');
        $this->load->library('user_agent');

        //APP 접근일때
        if ( $this->agent->is_mobile() ) {
            $this->join_path = "2";
        }
        else {
            $this->join_path = "3";
        }

    }//end of __construct()

    /**
     * 가입 확인 페이지
     */
    public function join_web() {

        //유효 키값 비교
        $session_sns_site = $_SESSION['session_sns_site'];
        $session_sns_userid = $_SESSION['session_sns_userid'];
        $session_return_url = $_SESSION['session_return_url'];

        if($session_sns_site == '' || $session_sns_userid == ''){ //오류
            alert(lang("site_error_empty_id"), $this->config->item("error_url"));
        }

        $where_data['m_sns_site'] = $session_sns_site;
        $where_data['m_sns_id'] = $session_sns_userid;
        $member_row = $this->member_model->get_member_row($where_data);

        if(empty($member_row)){ // 오류
            alert(lang("site_error_empty_id"), $this->config->item("error_url"));
        }

        $this->_header(true);
        $this->load->view('/auth/join', array( 'member_row' => $member_row , 'mode' => 'web' , 'return_url' => urlencode($session_return_url?$session_return_url:'/') ));
        $this->_footer(true);

    }

    /**
     * 가입 완료처리 (Ajax)
     */
    public function join_web_proc() {

        $m_num      = $this->input->post('m_num');
        $return_url = $this->input->post('return_url');

        //넘어온 회원번호가 없을때
        if( empty($m_num) ) {
            result_echo_json(get_status_code("error"), lang("site_error_invalid_id"), true, "alert", "", "", $this->config->item("error_url"));
        }

        /* m_state 변경 회원대기(4) -> 정상(1) */
        $bRet = $this->member_model->set_user_activate($m_num);

        if( $this->_login_proc() ) {
            result_echo_json(get_status_code('success'), "", true, "", "", "", urldecode($return_url) );
        } else {
            result_echo_json(get_status_code('error'), lang("site_error_unknown"), true, "alert");
        }

    }

    /**
     * 로그인 처리 (APP)
     * @return bool
     */
    private function _login_proc() {

        $sns_site = $_SESSION['session_sns_site'];
        $sns_userid = $_SESSION['session_sns_userid'];

        if( empty($sns_userid) ) {
            alert(lang("site_error_empty_id"), $this->config->item("error_url"));
        }

        //로그인 시도
        $login_result = $this->member_model->get_login_sns($sns_site, $sns_userid);

        if( $login_result['code'] == get_status_code('success') ) {
            //회원정보
            $member_row = $login_result['data'];

            //회원 로그인 세션 생성
            set_login_session($member_row);

            $query_data = array();
            $query_data['m_login_ip']       = $this->input->ip_address();
            $query_data['m_logindatetime']  = current_datetime();

            //me2.do
            if( !empty($member_row) ) {
                //로그인시 태그 입력
                $tag = trim($this->input->post_get("backUrl", true));

                //가입태그가 없을때만
                if( $member_row->m_tag == "" ) {
                    //$query_data['m_tag'] = get_join_tag($tag, $query_data['m_device_model']);
                }

                //30일 동안 로그인이 없었을때 만료재가입으로 변경
                if( !empty($member_row['m_logindatetime']) && $member_row['m_logindatetime'] < date("YmdHis", strtotime("-30 days")) ) {
                    $m_memo_rejoin = "#[재가입]" . $member_row['m_regdatetime'] . ":" . $member_row['m_tag'] . "#";
                    $query_data['m_memo'] = ( !empty($member_row['m_memo']) ) ? $member_row['m_memo'] . "\n" . $m_memo_rejoin : $m_memo_rejoin;
                    $query_data['m_regdatetime'] = current_datetime();
                    $query_data['m_rejoin_yn'] = "Y";

                    //가입태그 업데이트
                    //$query_data['m_tag'] = get_join_tag($tag, $query_data['m_device_model']);
                }//endif;

                $this->member_model->update_member($member_row['m_num'], $query_data);
            }//endif;


            //업데이트된 회원정보
            $member_row = $login_result['data'];

            $auto_login_enc =  $this->encryption->encrypt(current_mstime() . "|||2|" . $member_row->m_sns_site . "|" . $member_row->m_sns_id);
            set_cookie('cookie_sal', $auto_login_enc, get_strtotime_diff("+1 years"));

            return true;
        }
        else {
            return false;
        }
    }//end of _login_proc()


    /**
     * 고유 임시 닉네임 생성
     * @param int $len
     * @return string
     */
    private function _get_unique_nickname($len=7) {
        $nickname = get_random_string($len);

        $query_data = array();
        $query_data['m_nickname'] = $nickname;
        $member_row = $this->member_model->get_member_row($query_data);

        if( !empty($member_row) ) {
            $this->_get_unique_nickname();
        }

        return $nickname;
    }//end of _get_unique_nickname()

    /**
     * 로그아웃 (APP/웹)
     * @param bool $exec_only
     */
    public function logout($exec_only=false) {
        //자동로그인 쿠키 삭제
        delete_cookie('cookie_sal');
        $this->_logout_web($exec_only);
    }//end of logout()

    /**
     * 로그아웃 (웹)
     */
    private function _logout_web($exec_only=false) {

        if( $_SESSION['kakao_access_token'] ) { //카카오 로그아웃
            $this->load->library('Snoopy');

            $url = "https://kapi.kakao.com/v1/user/logout";
            $this->snoopy->rawheaders["Authorization"] = "Bearer " . $_SESSION['kakao_access_token'];
            $this->snoopy->fetch($url);
            $result = json_decode($this->snoopy->results);

        }//end of if()
        else if( $_SESSION['naver_access_token'] ){ //네이버 로그아웃


        }
        else if( $_SESSION['facebook_access_token'] ) { //페이스북 로그아웃

        }

        //TODO:네이버, 페이스북 로그아웃 처리 필요

        //$this->session->sess_destroy();
        session_destroy();

        if( !$exec_only ) {
            redirect('/');
        }
    }//end of _logout_web()

    /**
     * 회원탈퇴 팝업 (APP)
     */
    public function withdraw_pop() {
        if( !member_login_status() ) {
            alert("");
        }

        $this->load->view("/auth/withdraw_pop", array());
    }//end of withdraw_pop()

    /**
     * 회원탈퇴처리 (회원삭제함) (APP) (ajax)
     */
    public function withdraw_proc() {
        ajax_request_check();
        member_login_check(true);

        //model
        $this->load->model('member_withdraw_log_model');

        $member_row = $this->_get_member_info();

        $this->load->library('form_validation');

        //$mwl_reason_etc_set_rules = $this->default_set_rules;
        //if( $this->input->post('mwl_reason', true) == 99 ) {
        //    $mwl_reason_etc_set_rules .= "|required";
        //}

        //폼검증 룰 설정
        $set_rules_array = array(
            "mwl_reason" => array("field" => "mwl_reason", "label" => "사유", "rules" => "required|in_list[" . get_config_item_keys_string('member_withdraw_reason') ."]|" . $this->default_set_rules),
            //"mwl_reason_etc" => array("field" => "mwl_reason_etc", "label" => "사유입력", "rules" => $mwl_reason_etc_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {
            $mwl_reason = $this->input->post('mwl_reason', true);
            $mwl_reason_etc = $this->input->post('mwl_reason_etc', true);

            if( empty($form_error_array) ) {
                //탈퇴처리
                if( $this->member_model->withdraw_member($member_row->m_num) ) {

                    //쿠키, 세션 삭제

                    delete_cookie('cookie_sal');
                    total_stat('join_del');
                    //$this->session->sess_destroy();
                    session_destroy();

                    //탈퇴 로그 저장
                    $query_data = array();
                    $query_data['mwl_member_num'] = $member_row->m_num;
                    $query_data['mwl_loginid'] = $member_row->m_loginid;
                    $query_data['mwl_nickname'] = $member_row->m_nickname;
                    $query_data['mwl_sns_site'] = $member_row->m_sns_site;
                    $query_data['mwl_sns_id'] = $member_row->m_sns_id;
                    $query_data['mwl_app_version'] = $member_row->m_app_version;
                    $query_data['mwl_app_version_code'] = $member_row->m_app_version_code;
                    $query_data['mwl_device_model'] = $member_row->m_device_model;
                    $query_data['mwl_os_version'] = $member_row->m_os_version;
                    $query_data['mwl_join_ip'] = $member_row->m_join_ip;
                    $query_data['mwl_joindatetime'] = $member_row->m_regdatetime;
                    $query_data['mwl_login_ip'] = $member_row->m_login_ip;
                    $query_data['mwl_logindatetime'] = $member_row->m_logindatetime;
                    $query_data['mwl_wish_count'] = $member_row->m_wish_count;
                    $query_data['mwl_comment_count'] = $member_row->m_comment_count;
                    $query_data['mwl_order_count'] = $member_row->m_order_count;
                    $query_data['mwl_first_push_yn'] = $member_row->m_first_push_yn;
                    $query_data['mwl_push_yn'] = $member_row->m_push_yn;
                    $query_data['mwl_reason'] = (!empty($mwl_reason)) ? $mwl_reason : "";
                    $query_data['mwl_reason_etc'] = (!empty($mwl_reason_etc)) ? $mwl_reason_etc : "";
                    $query_data['mwl_reg_ip'] = $this->input->ip_address();
                    $this->member_withdraw_log_model->insert_member_withdraw_log($query_data);

                    result_echo_json(get_status_code('success'), lang('site_member_withdraw_success'), true, 'alert',array(),array(),"");
                }
                else {
                    log_message('ZS','err :: withdraw_proc :: 탈퇴 DB 오류');

                    result_echo_json(get_status_code('error'), lang('site_error_db'), true, 'alert');
                }
            }//end of if()
        }//end of if(/폼 검증 성공 마침)

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);

        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);
    }//end of withdraw_proc()

    /**
     * 회원탈퇴 (APP/웹)
     */
    public function withdraw() {
        if( is_app() ) {
            //회원탈퇴 완료 (앱링크해제)
            $this->_withdraw_app();
        }
        else {
            //회원탈퇴 처리
            $this->_withdraw_web();
        }
    }//end of withdraw()

    /**
     * 회원탈퇴 (웹)
     */
    private function _withdraw_web() {
        member_login_check();
        ajax_request_check();

        $m_num = $_SESSION['session_m_num'];

        //회원정보
        $query_data = array();
        $query_data['m_num'] = $m_num;
        $member_row = $this->member_model->get_member_row($query_data);

        if( empty($member_row) ) {
            result_echo_json(get_status_code("error"), lang("site_error_noauth"), true, "alert");
        }

        //탈퇴
        $query_data = array();
        $query_data['m_deldatetime'] = current_datetime();
        $query_data['m_push_yn'] = "N";
        $query_data['m_state'] = "3";
        if( $this->member_model->update_member($m_num, $query_data) ) {

            //적립금 삭제
            $this->_deletePointMember($member_row->m_key);

            //로그아웃
            $this->logout(true);

            total_stat("join_del");

            result_echo_json(get_status_code("success"), lang("site_withdraw_complete"), true, "alert");
        }
        else {
            result_echo_json(get_status_code("error"), lang("site_error_unknown"), true, "alert");
        }
    }//end of _withdraw_web()

    /**
     * SMS 인증 팝업
     */
    public function sms_auth_pop() {
        $this->load->view("/auth/sms_auth_pop", array());
    }//end of sms_auth_pop()

    /**
     * SMS 인증 (ajax)
     */
    public function sms_auth() {
        ajax_request_check();

        //회원만
        member_login_check();

        //허용 mode
        $allow_mode_array = array("req", "retry", "cert");

        //request
        $req['mode'] = $this->input->post_get('mode', true);            //req=요청, retry=재요청, cert=확인
        $req['ph'] = $this->input->post_get('ph', true);                //휴대폰번호(요청시)
        $req['no'] = $this->input->post_get('no', true);                //인증번호(확인시)
        $req['no_save'] = $this->input->post_get('no_save', true);      //회원정보에저장하지않음(Y=저장안함|''/null=저장함)
        $req['pass_chk'] = $this->input->post_get('pass_chk', true);      //회원인증페이지에서 처리되었는지 체크 Y
        $req['before_chk'] = $this->input->post_get('before_chk', true);      //Y 일경우는 m_auth_no 가 아닌 m_auth_no_chk 필드에 업데이트

        if( !in_array($req['mode'], $allow_mode_array) ) {
            result_echo_json(get_status_code('error'), lang("site_error_default"), true, "alert");
        }
        if( ($req['mode'] == "req" || $req['mode'] == "retry") && empty($req['ph']) ) {
            result_echo_json(get_status_code('error'), lang("site_error_empty_id"), true, "alert");
        }
        if( ($req['mode'] == "req" || $req['mode'] == "retry") && strlen(number_only($req['ph'])) < 10 ) {
            result_echo_json(get_status_code('error'), "올바르지 않은 휴대폰번호입니다.\n확인 후 다시 시도해 주세요.", true, "alert");
        }
        if( $req['mode'] == "cert" && empty($req['no']) ) {
            result_echo_json(get_status_code('error'), lang("site_error_empty_id"), true, "alert");
        }

        //회원정보
        $member_row = $this->_get_member_info();


        //이미 인증했으면
        if( !empty($member_row['m_authno']) ) {
            result_echo_json(get_status_code('error'), "이미 본인인증하셨습니다.", true, "alert", "", array('reload' => "Y"));
        }

        $data = array();

        //인증번호 요청
        if( $req['mode'] == "req" ) {
            //이미 본인인증에 사용된 휴대폰번호일때
            $query = "select * from member_tb where m_authno = '" . number_only($req['ph']) . "' and m_division = '2'";
            $member_auth_row = $this->db->query($query)->row_array();

            if (!empty($member_auth_row) && $req['pass_chk'] =="") {
                result_echo_json(get_status_code('error'), "이미 인증된 휴대폰번호입니다.", true, "alert", "", array('reload' => "Y", 'm_info' => $member_auth_row));
            }else if(!empty($member_auth_row) && $req['pass_chk'] == "Y"){

                $data = sms_auth_req($this->config->item("order_cpid"), $member_row->m_key, $req['ph']);

                $data = array();
                $data['status'] = "000";
                $data['message'] = "";
                $data['before_chk'] = "Y";

                if( !empty($add_data) ) {
                    $data = array_merge($data, $add_data);
                }

                echo json_encode_no_slashes($data);
                exit;

            }else{

                $data = sms_auth_req($this->config->item("order_cpid"), $member_row->m_key, $req['ph']);
                echo $data;
                exit;

            }


            $data = sms_auth_req($this->config->item("order_cpid"), $member_row->m_key, $req['ph']);
            echo $data;
            exit;
        }
        //재전송 요청
        else if( $req['mode'] == "retry" ) {
            //이미 본인인증에 사용된 휴대폰번호일때
            $query = "select * from member_tb where m_authno = '" . number_only($req['ph']) . "' and m_division = '2'";
            $member_auth_row = $this->db->query($query)->row();
            if( !empty($member_auth_row) ) {
                result_echo_json(get_status_code('error'), "이미 인증된 휴대폰번호입니다.", true, "alert", "", array('reload' => "Y"));
            }

            $data = sms_auth_req($this->config->item("order_cpid"), $member_row->m_key, $req['ph'], true);
            echo $data;
            exit;
        }
        //인증번호 확인
        else if( $req['mode'] == "cert" ) {
            $data = sms_auth_cert($this->config->item("order_cpid"), $member_row->m_key, $req['no']);
            $data_json = json_decode($data);

            //성공이면 회원정보 업데이트
            if( $data_json->status == get_status_code('success') && !empty($data_json->ph) ) {
                if( !empty($req['no_save']) ) {
                    result_echo_json(get_status_code('success'), "정상적으로 인증되었습니다.", true, "alert");
                }
                if($req['before_chk'] == "Y") {
                    $query = "update member_tb set m_authno_chk = '" . $data_json->ph . "' where m_num = '" . $member_row->m_num . "'";
                    if ($this->db->query($query)) {

                        result_echo_json(get_status_code('success'), "정상적으로 인증되었습니다.", true, "alert");
                    } else {
                        result_echo_json(get_status_code('error'), lang("site_error_db"), true, "alert");
                    }
                }else{
                    $query = "update member_tb set m_authno = '" . $data_json->ph . "' where m_num = '" . $member_row->m_num . "'";
                    if ($this->db->query($query)) {
                        //출석체크 당첨 연락처 업데이트 정보가 있으면
                        if ($_SESSION['session_ew_cmd'] == "update" && $_SESSION['session_ew_num']) {
                            $query = "
                            update event_winner_tb
                            set
                                ew_contact = '" . $data_json->ph . "'
                                , ew_updatetime = '" . current_datetime() . "'
                            where ew_num = '" . $_SESSION['session_ew_num'] . "'
                        ";
                            $this->db->query($query);

                            $_SESSION['session_ew_cmd'];
                            $_SESSION['session_ew_num'];
                        }

                        result_echo_json(get_status_code('success'), "정상적으로 인증되었습니다.", true, "alert");
                    } else {
                        result_echo_json(get_status_code('error'), lang("site_error_db"), true, "alert");
                    }


                }
            }
            else {
                $msg = $data_json->message;
                if( empty($msg) ) {
                    $msg = lang("site_error_unknown");
                }
                result_echo_json(get_status_code('error'), $msg, true, "alert");
            }
        }//endif;

        echo $data;
    }//end of sms_auth()


    /**
     * SMS 인증 (비회원) 팝업
     */
    function sms_auth_nologin_pop() {
        //request
        $req['ret_call'] = $this->input->post_get("ret_call", true);
        $req['silent'] = $this->input->post_get("silent", true);

        //$this->_header(true);

        $this->load->view("/auth/sms_auth_nologin_pop", array(
            'req'   => $req
        ));

        //$this->_footer(true);

    }//end of sms_auth_nologin_pop()

    /**
     * SMS 비회원 인증 (ajax)
     */
    function sms_auth_nologin() {
        ajax_request_check();

        //허용 mode
        $allow_mode_array = array("req", "retry", "cert");

        ////하루 최대 허용 횟수
        //$day_max_limit = 5;

        //request
        $req['mode'] = $this->input->post_get('mode', true);        //req=요청, retry=재요청, cert=확인
        $req['ph'] = $this->input->post_get('ph', true);            //휴대폰번호(요청시)
        $req['no'] = $this->input->post_get('no', true);            //인증번호(확인시)
        $req['silent'] = $this->input->post_get('silent', true);    //메시지출력없음(Y|null)

        if( !in_array($req['mode'], $allow_mode_array) ) {
            result_echo_json(get_status_code('error'), lang("site_error_default"), true, "alert");
        }
        if( ($req['mode'] == "req" || $req['mode'] == "retry") && empty($req['ph']) ) {
            result_echo_json(get_status_code('error'), lang("site_error_empty_id"), true, "alert");
        }
        if( ($req['mode'] == "req" || $req['mode'] == "retry") && strlen(number_only($req['ph'])) < 10 ) {
            result_echo_json(get_status_code('error'), "올바르지 않은 휴대폰번호입니다.\n확인 후 다시 시도해 주세요.", true, "alert");
        }
        if( $req['mode'] == "cert" && empty($req['no']) ) {
            result_echo_json(get_status_code('error'), lang("site_error_empty_id"), true, "alert");
        }

        //if( get_cookie("cki_nologin_sms_cnt") >= $day_max_limit ) {
        //    result_echo_json(get_status_code('error'), "1일 최대 " . $day_max_limit . "회까지만 요청이 가능합니다.", true, "alert", "", array('reload' => "Y"));
        //}

        $cur_sms_cnt = get_cookie("cki_nologin_sms_cnt");
        if( empty($cur_sms_cnt) ) {
            $cur_sms_cnt = 0;
        }
        $cur_sms_cnt++;
        set_cookie("cki_nologin_sms_cnt", $cur_sms_cnt, get_strtotime_diff("+1 days"));

        $data = array();

        //인증번호 요청
        if( $req['mode'] == "req" ) {
            $data = sms_auth_req($this->config->item("order_cpid"), "", $req['ph']);
            echo $data;
            exit;
        }
        //재전송 요청
        else if( $req['mode'] == "retry" ) {
            $data = sms_auth_req($this->config->item("order_cpid"), "", $req['ph'], true);
            echo $data;
            exit;
        }
        //인증번호 확인
        else if( $req['mode'] == "cert" ) {
            $data = sms_auth_cert($this->config->item("order_cpid"), "", $req['no']);
            $data_json = json_decode($data);

            //성공이면
            if( $data_json->status == get_status_code('success') && !empty($data_json->ph) ) {
                if( empty($req['silent']) ) {
                    result_echo_json(get_status_code('success'), "정상적으로 인증되었습니다.", true, "alert");
                }
                else {
                    result_echo_json(get_status_code('success'), "", true);
                }
            }
            else {
                result_echo_json(get_status_code('error'), lang("site_error_unknown"), true, "alert");
            }
        }

        echo $data;
    }//end of sms_auth_nologin()

}//end of class Auth