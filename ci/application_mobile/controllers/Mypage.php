<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 교환/반품
 */
class Mypage extends M_Controller
{

    public function __construct()
    {
        parent::__construct();

        member_login_check();

    }//end of __construct()

    public function index()
    {

        $options = array('title' => '개인정보변경' , 'top_type' => 'back');

        $aMemberInfo = $this->_get_member_info();

        $this->_header($options);

        $this->load->view('/mypage/index', array( 'aMemberInfo' => $aMemberInfo ) );

        $this->_footer();

    }//end of index()

    public function mypage_update_proc(){

        ajax_request_check();

        $this->load->library('form_validation');

        //폼검증 룰 설정
        $set_rules_array = array(
            "age_range" => array("field" => "age_range", "label" => "연령대", "rules" => "numeric|".$this->default_set_rules),
            "gender" => array("field" => "gender", "label" => "성별", "rules" => "in_list[Y,N]|".$this->default_set_rules),
            "m_nickname" => array("field" => "m_nickname", "label" => "닉네임", "rules" => "required|".$this->default_set_rules),
        );

        $this->form_validation->set_rules($set_rules_array);

        $form_error_array = array();

        //이미지 업로드
        if( isset($_FILES['m_sns_profile_img']['name']) && !empty($_FILES['m_sns_profile_img']['name']) ) {

            $prof_image_path_web = $this->config->item('profile_file_path_web') . "/" . date("Y") . "/" . date("md");
            $prof_image_path = $this->config->item('profile_file_path') . "/" . date("Y") . "/" . date("md");
            create_directory($prof_image_path);

            $config = array();
            $config['upload_path'] = $prof_image_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = '5000';
            $config['encrypt_name'] = true;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ( $this->upload->do_upload('m_sns_profile_img') ) {
                $prof_image_data_array = $this->upload->data();
                $prof_image = $prof_image_path_web . "/" . $prof_image_data_array['file_name'];
            }
            else {
                $form_error_array['m_sns_profile_img'] = strip_tags($this->upload->display_errors());
            }//end of if()

        }

        if(empty($form_error_array) == true){

            $query_data     =  array();
            if(empty($prof_image) == false) $query_data['m_sns_profile_img'] = $prof_image;
            if(empty($this->input->post('age_range')) == false) $query_data['m_age_range']       = $this->input->post('age_range');
            if(empty($this->input->post('gender')) == false) $query_data['m_gender']          = $this->input->post('gender');
            if(empty($this->input->post('m_nickname')) == false) $query_data['m_nickname']        = $this->input->post('m_nickname');

            if(empty($query_data) == false){

                $this->load->model('mypage_model');
                $this->mypage_model->publicUpdate('member_tb',$query_data, array('m_num' , $_SESSION['session_m_num']));
                result_echo_json(get_status_code('success'), lang("site_update_success") , true);

            }else{

                result_echo_json(get_status_code('success'), lang("site_update_success") , true);

            }

        }

        //뷰 출력용 폼 검증 오류메시지 설정
        $form_error_array = set_form_error_from_rules($set_rules_array, $form_error_array);
        result_echo_json(get_status_code('error'), "", true, "", $form_error_array);


    }

    public function mypage_withdraw(){

        $member_withdraw_list = $this->config->item('member_withdraw');
        $this->load->view('/mypage/withdraw', array( 'member_withdraw_list' => $member_withdraw_list ) );

    }

    public function mypage_withdraw_proc(){

        $this->load->model('member_model');

        $aInput = array(
                'm_num'             => $_SESSION['session_m_num']
            ,   'withdraw_reason'   => $this->input->post('withdraw_reason')
        );

        if(empty($aInput['withdraw_reason']) == true){
            echo json_encode_no_slashes(array('msg' => '탈퇴사유는 필수 입력사항입니다.' , 'success' => false ));
            exit;
        }

        $aMemberInfo = $this->member_model->get_member_row($aInput);

        if(empty($aMemberInfo) == false){

            {/* ----------------  현재 구매 진행중인 상품 확인 */
                $sql = "SELECT A.* , B.after_status_cd 
                        FROM snsform_order_tb A
                        LEFT JOIN snsform_order_cancel_tb B ON A.trade_no = B.trade_no 
                        WHERE partner_buyer_id = '{$aMemberInfo['m_num']}' 
                        AND status_cd <= '65';
                ";
                $oResult = $this->db->query($sql);
                $aResult = $oResult->result_array();

                $living_cnt = 0;
                if(count($aResult) > 0){
                    foreach ($aResult as $k => $r) {
                        if($r['status_cd'] == 65){ //배송완료
                            if($r['after_status_cd'] == 66 || $r['after_status_cd'] == 67 || $r['after_status_cd'] == 68){ //취소정보중 취소중 / 교환중 / 반품중
                                $living_cnt++;
                            }
                        }else{
                            $living_cnt++;
                        }
                    }
                }

                if($living_cnt > 0){
                    echo json_encode_no_slashes(array('msg' => '' , 'success' => false , 'data' => $living_cnt ));
                    exit;
                }
            }/*----------------  현재 구매 진행중인 상품 확인 */

            $aDrawData = array(
                'mwl_member_num'            => $aMemberInfo['m_num']
            ,   'mwl_nickname'              => $aMemberInfo['m_nickname']
            ,   'mwl_sns_site'              => $aMemberInfo['m_sns_site']
            ,   'mwl_sns_id'                => $aMemberInfo['m_sns_id']
            ,   'mwl_app_version'           => $aMemberInfo['m_app_version']
            ,   'mwl_app_version_code'      => $aMemberInfo['m_app_version_code']
            ,   'mwl_device_model'          => $aMemberInfo['m_device_model']
            ,   'mwl_os_version'            => $aMemberInfo['m_os_version']
            ,   'mwl_join_ip'               => $aMemberInfo['m_join_ip']
            ,   'mwl_joindatetime'          => $aMemberInfo['m_regdatetime']
            ,   'mwl_login_ip'              => $aMemberInfo['m_login_ip']
            ,   'mwl_logindatetime'         => $aMemberInfo['m_logindatetime']
            ,   'mwl_wish_count'            => $aMemberInfo['m_wish_count']
            ,   'mwl_comment_count'         => $aMemberInfo['m_comment_count']
            ,   'mwl_order_count'           => $aMemberInfo['m_order_count']
            ,   'mwl_push_yn'               => $aMemberInfo['m_push_yn']
            ,   'mwl_reason'                => $aInput['withdraw_reason']
            ,   'mwl_reason_etc'            => ''
            ,   'mwl_reg_ip'                => $this->input->ip_address()
            ,   'mwl_admin_yn'              => $aMemberInfo['m_admin_yn']
            ,   'mwl_regdatetime'           => current_datetime()
            );

            $aRet = $this->member_model->withdraw_member($aMemberInfo['m_num'] , $aDrawData);

            if($aMemberInfo['m_sns_site'] == 1) unlink_kakao($aMemberInfo['m_sns_id']);

            if($aRet['success'] == true){
                session_destroy();
                echo json_encode_no_slashes(array('msg' => "" , 'success' => true ));
                exit;
            }else{
                echo json_encode_no_slashes(array('msg' => "탈퇴 진행 중 문제가 발생하였습니다.\n잠시 후 다시시도해주세요" , 'success' => false ));
                exit;
            }

        }

    }

}//end of class Exchange