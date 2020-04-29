<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 카카오 API 관련 컨트롤러
 */
class Api extends W_Controller
{

    public function __construct()
    {
        parent::__construct();

    }//end of __construct()

    public function index()
    {
        show_404();
    }//end of index()

    /**
     * @date 200429
     * @modify 황기석
     * @desc 옷쟁이 채널 추가/차단 callback
     */
    public function changeChannel(){
        
        $callback_data = file_get_contents( 'php://input' );

        if(empty($callback_data) == false){

            $aInput = json_decode($callback_data,true);
//------------ sample
//event ==> blocked
//id ==> 1274364410
//id_type ==> open_id
//plus_friend_public_id ==> _ISxgbxb
//plus_friend_uuid ==> @옷쟁이들
//timestamp ==> 1588138497000
//updated_at ==> 2020-04-29T05:34:57Z

            if($aInput['event' == 'blocked']){ //차단

            }else{ //추가

            }

        }

    }

    /**
     * @date 200429
     * @modify 황기석
     * @desc 카카오 연결끊기 callback
     */
    public function leave(){

        $aInput = array(
                'referrer_type' => $this->input->post('referrer_type')
            ,   'sns_id'        => $this->input->post('user_id')
        );

        if($aInput['referrer_type'] == 'UNLINK_FROM_APPS'){

            $sql = "SELECT * FROM member_tb WHERE m_sns_id = '{$aInput['sns_id']}'; ";
            $oResult = $this->db->query($sql);
            $aMemberInfo = $oResult->row_array();

            if(empty($aMemberInfo) == false){

                $this->load->model('member_model');

                //탈퇴 로그 저장
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
                ,   'mwl_reason'                => 0
                ,   'mwl_reason_etc'            => '카카오 사용자 연결끊기'
                ,   'mwl_reg_ip'                => $this->input->ip_address()
                ,   'mwl_admin_yn'              => $aMemberInfo['m_admin_yn']
                ,   'mwl_regdatetime'           => current_datetime()
                );

                $this->member_model->withdraw_member($aMemberInfo['m_num'] , $aDrawData);

                unlink_kakao($aMemberInfo['m_sns_id']);

            }

        }

    }

}//end of class Api


