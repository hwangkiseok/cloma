<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 로그인/로그아웃 관련 컨트롤러
 * - 앱 : 카카오, 네이버
 * - 웹 : 카카오 (Kakao.php)
 */
class Auth extends M_Controller {

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
     * 회원등록 (APP)
     * @return mixed
     */
    private function _insert_member($req) {

        $session_sns_site   = $_SESSION['session_sns_site'];
        $session_sns_userid = $_SESSION['session_sns_userid'];

        $nickname = $req['nickname'];

        $tmp_nickname = preg_replace("/\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2}/", "", $nickname);
        $tmp_nickname = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $nickname);

        if( empty($tmp_nickname) ) {
            $tmp_nickname = $req['id'];
        }

        $this->load->helper('string');
        if($tmp_nickname == '' || $tmp_nickname == null ) $tmp_nickname = random_string('alnum', 5);

        //회원 등록
        $query_data = array();
        $query_data['m_key']                = $_SESSION['my_session_id'];
        $query_data['m_division']           = "2";
        $query_data['m_nickname']           = str_replace(array("\"", "'"), array("˝", "´"), $tmp_nickname);
        $query_data['m_sns_site']           = $req['sns_site'];
        $query_data['m_sns_id']             = $req['id'];
        $query_data['m_sns_token']          = $req['access_token'];
        $query_data['m_sns_profile_img']    = $req['profile_image'];
        $query_data['m_sns_nickname']       = str_replace(array("\"", "'"), array("˝", "´"), $tmp_nickname);
        $query_data['m_email']              = $req['email'];
        $query_data['m_adid']               = $req['adid']?$req['adid']:"";
        $query_data['m_join_ip']            = $this->input->ip_address();
        $query_data['m_join_path']          = $this->join_path;
        $query_data['m_state']              = 4;
        $query_data['m_login_ip']           = $this->input->ip_address();
        $query_data['m_app_version']        = $req['app_version'];
        $query_data['m_app_version_code']   = $req['app_version_code']?$req['app_version_code']:"0";
        $query_data['m_device_model']       = $req['device_info'];
        $query_data['m_os_version']         = $req['os_version'];
        $query_data['m_push_yn']            = 'Y';//$req['push'];


        if( $req['sns_site'] == '1' ){

            //연락처
            $phone_arr = explode(' ', $req['phone_number']);
            $phone_number_country = $phone_arr[0];
            if ($phone_arr[0] == '+82') {
                $phone_number = '0' . $phone_arr[1];
                $phone_num_arr = explode('-', $phone_number);
                $phone_number = $phone_num_arr[0] . $phone_num_arr[1] . $phone_num_arr[2];
            } else {
                $phone_number = $phone_arr[1];
            }

            //성별
            $gender = '';
            if($req['gender'] == 'male') $gender = 'M';
            else if($req['gender'] == 'female') $gender = 'F';

            $query_data['m_age_range'] = empty($req['age_range']) == false ? substr($req['age_range'],0,1).'0' : '';
            $query_data['m_gender']    = $gender;
            $query_data['m_authno']    = $phone_number;


//            log_message('A',$req['address']);


            if(empty($req['address']) == false){

                $delivery_addr_1 = json_decode($req['address'],true);

                foreach ($delivery_addr_1 as $key => $val) {

                    if ($val['isDefault'] == true) {

                        $phone_arr1 = '';
                        $phone_arr2 = '';
                        $receiver_phone_number1 = '';
                        $receiver_phone_number2 = '';

                        if (isset($val['receiverPhoneNumber1'])) {
                            $phone_arr1 = explode('-', $val['receiverPhoneNumber1']);
                            $receiver_phone_number1 = $phone_arr1[0] . $phone_arr1[1] . $phone_arr1[2];
                        }

                        if (isset($val['receiverPhoneNumber2'])) {
                            $phone_arr2 = explode('-', $val['receiverPhoneNumber2']);
                            $receiver_phone_number2 = $phone_arr2[0] . $phone_arr2[1] . $phone_arr2[2];
                        }

                        $tmpProfile['name'] = $val['name'];
                        $tmpProfile['addr_type'] = $val['type'];
                        $tmpProfile['addr_post'] = $val['zipCode'];
                        $tmpProfile['addr_post_new'] = $val['zoneNumber'];
                        $tmpProfile['addr1'] = $val['baseAddress'];
                        $tmpProfile['addr2'] = $val['detailAddress'];
                        $tmpProfile['receiver_name'] = $val['receiverName'];
                        $tmpProfile['receiver_phone_number1'] = $receiver_phone_number1;
                        $tmpProfile['receiver_phone_number2'] = $receiver_phone_number2;
                    }

                }

            }

            // 확장 데이터
            $profile_ext = array(
                'sns_id'                => $req['id']
            ,   'sns_site'              => $req['sns_site']
            ,   'nickname'              => str_replace(array("\"", "'"), array("˝", "´"), $tmp_nickname)
            ,   'profile_image'         => $req['profile_image']
            ,   'profile_image_thumb'   => $req['profile_image']
            ,   'email'                 => $req['email']
            ,   'gender'                => $req['gender']
            ,   'birthday'              => $req['birthday']
            ,   'age_range'             => $req['age_range']
            ,   'phone_number_country'  => $phone_number_country
            ,   'phone_number'          => $phone_number
            ,   'name'                  => $tmpProfile['name']
            ,   'addr_type'             => $tmpProfile['addr_type']
            ,   'addr_post'             => $tmpProfile['addr_post']
            ,   'addr_post_new'         => $tmpProfile['addr_post_new']
            ,   'addr1'                 => $tmpProfile['addr1']
            ,   'addr2'                 => $tmpProfile['addr2']
            ,   'receiver_name'         => $tmpProfile['receiver_name']
            ,   'receiver_phone_number1'=> $tmpProfile['receiver_phone_number1']
            ,   'receiver_phone_number2'=> $tmpProfile['receiver_phone_number2']
            ,   'login_path'            => 'app'
            );

//            foreach ($profile_ext as $k => $v) {
//                log_message('A',$k.' ===> '.$v);
//            }

        }


        if( !empty($req['reg_id']) )  $query_data['m_regid'] = $req['reg_id'];

        log_message('M', '- APP Member Insert Params --------------------------------------------------------------------------------------');
        foreach ($query_data as $k => $v) {
            log_message('M',$k . ' ==> ' . $v);
        }

        //회원가입
        $this->member_model->insert_member($query_data,$profile_ext);

        $login_result   = $this->member_model->get_login_sns($session_sns_site, $session_sns_userid);
        $member_row     = $login_result['data'];

        ///fcmid 처리
        if( !empty($req['reg_id']) ) {

            $this->load->model('app_device_model');

            if( $login_result['code'] == get_status_code('success') ) {

                $device_row = $this->app_device_model->get_app_device_row(array('dv_regid' => $req['reg_id']));
                //있으면 수정
                if (!empty($device_row)) {
                    $query_data = array();
                    $query_data['dv_member_num'] = $member_row['m_num'];
                    $query_data['dv_push_yn'] = $member_row['m_push_yn'];
                    $query_data['dv_app_version'] = $req['app_version'];
                    $query_data['dv_app_version_code'] = $req['app_version_code'];
                    $query_data['dv_regid'] = $req['reg_id'];


                    $this->app_device_model->update_app_device($device_row['dv_num'], $query_data);
                } //없으면 등록
                else {
                    $query_data = array();
                    $query_data['dv_regid'] = $req['reg_id'];
                    $query_data['dv_member_num'] = $member_row['m_num'];
                    $query_data['dv_push_yn'] = $member_row['m_push_yn'];
                    $query_data['dv_deviceinfo'] = $req['device_info'] . "|" . $req['os_version'];
                    $query_data['dv_useragent'] = $this->agent->agent_string()?$this->agent->agent_string():"";
                    $query_data['dv_app_version'] = $req['app_version'];
                    $query_data['dv_app_version_code'] = $req['app_version_code'];

                    $this->app_device_model->insert_app_device($query_data);
                }

            }

        }//end of if()

        //회원 로그인 세션 생성
        set_login_session($member_row);

        return $member_row;

    }//end of _insert_member()


    /**
     * 로그인 처리 (앱에서 SNS로그인 완료시 요청함)
     */
    public function login() {

        //request
        $req = array(
             'id'			    => $this->input->post_get('id')                  //필수
            ,'nickname'		    => $this->input->post_get('nickname')            //필수
            ,'email'			=> $this->input->post_get('email')               //필수 x
            ,'profile_image'	=> $this->input->post_get('profile_image')       //필수 x
            ,'access_token'		=> $this->input->post_get('sns_site') == "4" ? "" : $this->input->post_get('access_token')        //필수 x
            ,'fcm_id'		    => $this->input->post_get('fcm_id')              //필수
            ,'device_info'		=> $this->input->post_get('device_info')         //필수
            ,'os_version'		=> $this->input->post_get('os_version')          //필수
            ,'app_version'		=> $this->input->post_get('app_version')         //필수
            ,'app_version_code'	=> $this->input->post_get('app_version_code')    //필수
            ,'sns_site'		    => $this->input->post_get('sns_site')            //필수
            ,'adid'             => $this->input->post_get('adid')                //필수 x
            ,'reg_id'           => $this->input->post_get('fcm_id')              //필수 x

            ,'age_range'        => $this->input->post_get('age_range')           //필수 x
            ,'birthday'         => $this->input->post_get('birthday')            //필수 x
            ,'gender'           => $this->input->post_get('gender')              //필수 x
            ,'phone_number'     => $this->input->post_get('phone_number')        //필수 x
            ,'address'          => $this->input->post_get('address')             //필수 x

        );

        if(empty($req['id']) == true){
            result_echo_json(get_status_code('error'), "회원 가입에 실패했습니다.[empty id]", true, "alert");
            exit;
        }

        foreach ($req as $k => $v) {
            $req[$k] = AES_Decode($v);
        }

        if( $req['sns_site'] == '4' ){
            $req['access_token'] = '';
        }

        if($req['device_info'] == "iPhone") $this->join_path = "4";
        else $this->join_path = "1";

        $_SESSION['session_join_path']  = $this->join_path;
        $_SESSION['session_sns_site']   = $req['sns_site'];
        $_SESSION['session_sns_userid'] = $req['id'];
        $_SESSION['session_adid']       = $req['adid'];

        $query_data = array();
        $query_data['m_sns_site']   = $req['sns_site'];
        $query_data['m_sns_id']     = $req['id'];

        $member_row = $this->member_model->get_member_row($query_data);

        if( !empty($member_row) ) {

            $member_row = $this->_login_proc($req);

            if( $member_row['m_num'] == "" ) {
                result_echo_json(get_status_code('error'), "로그인에 실패했습니다.", true, "alert" , json_encode_no_slashes($_SESSION) );
                exit;
            }

            $member_row['auth_type'] = 'login';

        }
        //회원정보가 없으면 => 가입 페이지로 이동
        else {
            //일단 회원을 등록함.
            $member_row = $this->_insert_member($req);

            if($member_row['m_num'] == ""){
                //echo "<script>alert('회원 가입에 실패했습니다.\\n확인 후 다시 시도해 주세요.');</script>";
                result_echo_json(get_status_code('error'), "회원 가입에 실패했습니다.1", true, "alert");
                exit;
            }

            $member_row['auth_type'] = 'join';

        }

        //푸쉬및다이나믹 링크로 들어 왔을 경우 분기 작업.
//        if( !empty($req['backUrl']) ) {
//
//            $_SESSION['app_back_url'] = $req['backUrl'];
//
//            //상품상세로 이동
//            if( strpos($req['backUrl'], "/product") !== false ) {
//                preg_match_all("@/product/detail/(.*)/@", $req['backUrl'], $chk_url, PREG_SET_ORDER);
//
//                if ($chk_url['0']['1']) {
//                    $p_num = $chk_url['0']['1'];
//                }else if(preg_match_all("@/product/detail/(.*)\?@", $req['backUrl'], $chk_url, PREG_SET_ORDER)){
//                    $p_num = $chk_url['0']['1'];
//                } else {
//                    preg_match_all("@/product/detail/(.*)@", $req['backUrl'], $chk_url, PREG_SET_ORDER);
//                    $p_num = $chk_url['0']['1'];
//
//                }
//                $member_row->webview_chk = "1";
//                $member_row->backUrl = $req['backUrl'];
//                $member_row->p_num = $p_num;
//            }
//            //메인웹뷰로 띄우기 : 푸쉬페이지, 카카오광고상품, 카카오스토리광고상품, 구글단축url, bitly단축url
//            else if(strpos($req['backUrl'], "/event/list") !== false || strpos($req['backUrl'], "/push") !== false || strpos($req['backUrl'], "/kakao_product") !== false || strpos($req['backUrl'], "/kstory_product") !== false || (strpos($req['backUrl'], "https://goo.gl") !== false || strpos($req['backUrl'], "http://bit.ly") !== false)) {
//                $member_row->webview_chk = "2";
//                $member_row->backUrl = $req['backUrl'] . "&newapp=1";
//                $member_row->p_num = "";
//                $_SESSION['rct_view_push'] = 'Y';
//            }
//            //메인페이지로 이동
//            else if( strpos($req['backUrl'], "/main") !== false ) {
//                $member_row->webview_chk = "0";
//                $member_row->backUrl = "";
//            }
//            //웹뷰새창 : 이벤트페이지로이동
//            else {
//                $member_row->webview_chk = "3";
//                $member_row->backUrl = $req['backUrl'];
//                $member_row->p_num = "";
//            }
//
//            /////////리퍼러 체크
//
//
//            if(strpos($req['backUrl'], "ref_site") !== false) {
//
//                if (strpos($req['backUrl'], "&") !== false) {
//                    preg_match_all("@ref_site=(.+?)&@", $req['backUrl'], $chk_url, PREG_SET_ORDER);
//                } else {
//                    preg_match_all("@ref_site=(.*)@", $req['backUrl'], $chk_url, PREG_SET_ORDER);
//                }
//
//                if($chk_url['0']['1']){
//                    $member_row->ref_site = $chk_url['0']['1'];
//                    $member_row->ref_kwd = "";
//                }
//
//            }
//
//        }else{
//            //$this->session->unset_userdata("app_back_url");
//            unset($_SESSION['app_back_url']);
//            //session_write_close();
//            $member_row->webview_chk = "0";
//            $member_row->backUrl = "";
//            $member_row->ref_site = "";
//            $member_row->ref_kwd = "";
//        }




        result_echo_json(get_status_code("success"), "", true, "", "", $member_row);

        exit;
    }//end of login()



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

        $this->_header();
        $this->load->view('/auth/join', array( 'member_row' => $member_row , 'return_url' => urlencode($session_return_url?$session_return_url:'/') ));
        $this->_footer();

    }

    /**
     * 가입 확인 페이지
     */ 
    public function join() {

        $this->_header();
        $this->load->view('/auth/join_2');
//        $this->_footer();

    }
    public function join_proc_2() {
        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
                "kor_name"  => array("field" => "kor_name", "label" => "성명", "rules" => "required|" . $this->default_set_rules)
            ,   "login_id"  => array("field" => "login_id", "label" => "로그인아이디", "rules" => "required|" . $this->default_set_rules)
            ,   "login_pw"  => array("field" => "login_pw", "label" => "로그인비밀번호", "rules" => "required|" . $this->default_set_rules)
            ,   "cell_tel"  => array("field" => "cell_tel", "label" => "연락처", "rules" => "required|numeric|" . $this->default_set_rules)
            ,   "gender"    => array("field" => "gender", "label" => "성별", "rules" => $this->default_set_rules)
            ,   "age_range" => array("field" => "age_range", "label" => "나이대", "rules" => $this->default_set_rules)
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === true ) {

            $aInput = array(
                'm_key'         => create_session_id()
            ,   'm_nickname'    => $this->input->post('kor_name')
            ,   'm_login_id'    => $this->input->post('login_id')
            ,   'm_login_pw'    => $this->input->post('login_pw')
            ,   'm_authno'      => $this->input->post('cell_tel')
            ,   'm_division'    => 1
            ,   'm_sns_site'    => 0
            ,   'm_age_range'   => $this->input->post('age_range')
            ,   'm_gender'      => $this->input->post('gender')
            ,   'm_push_yn'     => $this->input->post('accept3') == 'on' ? 'Y' : 'N'
//            ,   'zip_code'      => $this->input->post('zip_code')
//            ,   'addr1'         => $this->input->post('addr1')
//            ,   'addr2'         => $this->input->post('addr2')
//            ,   'birth_m'       => $this->input->post('birth_m')
//            ,   'birth_d'       => $this->input->post('birth_d')

            );

            $insert_result = $this->member_model->insert_member($aInput);

            if ( $insert_result['code'] == get_status_code('success') ) {
                result_echo_json(get_status_code("success"),'',true);
            } else {
                result_echo_json(get_status_code("error"), lang("site_error_db"), true, "alert", "", "", $this->config->item("error_url"));
            }

        }

        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);
        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);

    }

    public function overlap_id(){

        $login_id = $this->input->post('m_login_id');

        $sql = "SELECT * FROM member_tb WHERE m_login_id = '{$login_id}'; ";
        $oResult = $this->db->query($sql);
        $cnt = $oResult->num_rows();
        if($cnt > 0){
            result_echo_json(get_status_code('error'));
        }else{
            result_echo_json(get_status_code('success'));
        }

    }

    public function chk_login(){

        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "login_id"   => array("field" => "login_id", "label" => "아이디", "rules" => "trim|required|max_length[30]|xss_clean|prep_for_form|strip_tags"),
            "login_pw"   => array("field" => "login_pw", "label" => "비밀번호", "rules" => "trim|required|max_length[30]|xss_clean|prep_for_form|strip_tags"),
        );

        $this->form_validation->set_rules($set_rules_array);

        //$form_error_array = array();

        //폼 검증 성공시
        if( $this->form_validation->run() === TRUE ) {
            $id = $this->input->post("login_id", TRUE);
            $pw = $this->input->post("login_pw", TRUE);

            //model
            $user_row = $this->member_model->get_user_login($id, $pw);

            if ( empty($user_row) ) {
                result_echo_json(get_status_code('fail'), "계정정보가 없습니다.", TRUE, "alert");
            }

            //회원 로그인 세션 생성
            set_login_session($user_row);

            $query_data = array();
            $query_data['m_login_ip']       = $this->input->ip_address();
            $query_data['m_logindatetime']  = current_datetime();

            $this->member_model->publicUpdate('member_tb' , $query_data , array('m_num',$user_row['m_num']));


            result_echo_json(get_status_code('success'), "", TRUE);
        }

        result_echo_json(get_status_code('error'), "", TRUE);

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

    public function join_proc() {

        $m_num = $_SESSION['session_m_num'];
        $m_key = $_SESSION['session_m_key'];

        //SNS userid 세션이 없을때
        if( empty($m_num) || empty($m_key) ) {
            result_echo_json(get_status_code("error"), lang("site_error_invalid_id"), true, "alert", "", "");
        }

        $where_data = array();
        $where_data['m_num'] = $m_num;
        $where_data['m_key'] = $m_key;
        $member_row = $this->member_model->get_member_row($where_data);

        if( empty($member_row) ) {
            result_echo_json(get_status_code('error'), lang("site_error_invalid_id"), true, "alert", array('data' => json_encode($where_data,JSON_UNESCAPED_UNICODE)), "");
        }

        //기기정보에 회원번호 업데이트
//        if( !empty($regid) ) {
//            $device_row = $this->app_device_model->get_app_device_row(array('dv_regid' =>$regid));
//
//            if( !empty($device_row) ) {
//                $query_data = array();
//                $query_data["dv_member_num"] = $member_row->m_num;
//                $this->app_device_model->update_app_device($device_row->dv_num, $query_data);
//            }
//        }

        /* m_state 변경 회원대기(4) -> 정상(1) */
        $bRet = $this->member_model->set_user_activate($member_row['m_num']);

        if( $this->_login_proc() ) {
            result_echo_json(get_status_code('success'), "", true, "", "", "", $_POST['return_url']);
        }
        else {
            result_echo_json(get_status_code('error'), lang("site_error_unknown"), true, "alert");
        }
    }//end of join_proc()

    /**
     * 로그인 처리 (APP)
     * @return bool
     */
    private function _login_proc($req = array()) {

        $sns_site   = $_SESSION['session_sns_site']?$_SESSION['session_sns_site']:$_SESSION['session_m_sns_site'];
        $sns_userid = $_SESSION['session_sns_userid']?$_SESSION['session_sns_userid']:$_SESSION['session_m_sns_id'];

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

            if(empty($req['app_version']) == false) $query_data['m_app_version'] = $req['app_version'];
            if(empty($req['app_version_code']) == false) $query_data['m_app_version_code'] = $req['app_version_code'];
            if(empty($req['reg_id']) == false) $query_data['m_regid'] = $req['reg_id'];
            if(empty($req['adid']) == false) $query_data['m_adid'] = $req['adid'];

            $this->member_model->publicUpdate('member_tb' , $query_data , array('m_num',$member_row['m_num']));

            return $member_row;
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

}//end of class Auth