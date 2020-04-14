<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\Rest_Core;

defined('BASEPATH') OR exit('No direct script access allowed');

//To Solve File REST_Controller not found
require APPPATH . 'libraries/RestServer/REST_Controller.php';
require APPPATH . 'libraries/RestServer/Format.php';
require APPPATH . 'libraries/RestServer/Rest_Core.php'; // W_Controller 클래스에서 사용된 메소드이관

/**
 * 팝업 관련 컨트롤러
 */
class Popup extends REST_Controller
{

    public $core;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('rest');
        $this->core = new Rest_Core(); // Core Class (MyController 코어클래스와 같은역할)

        //model
        $this->load->model('popup_model');

    }

    /**
     * 팝업 리스트
     */
    public function list_get()
    {

        $exec       = $this->get('exec');
        $apo_uid    = $this->get('apo_uid');

        if(empty($exec) == true){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang('site_error_required'), true , '' , '' , ''
                ), REST_Controller::HTTP_OK
            );

        }else{

            $aTodayPopup = $this->popup_model->getTodayPopup($exec , $apo_uid);

            if(empty($aTodayPopup) == false){

                $update_seq  = array();
                foreach ($aTodayPopup as $r)  $update_seq[] = $r['apo_num'];
                $this->popup_model->setDisplayCount($update_seq);

            }

            $this->load->model('product_model');

            $aClosePopup_1 = get_recently_product(2,true);
            $aClosePopup_2 = $this->product_model->get_close_product( array('not_in' => $aClosePopup_1 ));
            $aClosePopup = array();
            $aClosePopup = array_merge($aClosePopup_1,$aClosePopup_2);
            $aClosePopup = $this->core->clearProductField($aClosePopup, array('campaign' => 'close_popup'));

            //마지막 등록된 팝업의 정보로 팝업사이즈와 버튼액션 결정
            //sort가 최신순으로 처리가되어 있어 0번배열로 처리
            $apo_size_type = $aTodayPopup[0]['apo_size_type'];
            $apo_btn_type = $aTodayPopup[0]['apo_btn_type'];

            $aTodayPopup = self::clearPopupData($aTodayPopup, array('campaign' => 'popup'));

            if(empty($aTodayPopup) == true) $aTodayPopup = array();

            $this->set_response(
                result_echo_rest_json(get_status_code("success"), "", true , '' , '' , array(
                        'aPopupList'       => $aTodayPopup
                    ,   'aClosePopup'      => $aClosePopup
                    ,   'popup_size_type'  => $apo_size_type
                    ,   'popup_btn_type'   => $apo_btn_type
                )), REST_Controller::HTTP_OK
            );


        }

    }

    /**
     * 클릭/클로즈 체크
     */
    public function click_view_put()
    {

        $type       = $this->put('type'); //이미지 클릭 | 닫기
        $apo_num    = $this->put('apo_num');

        if(empty($type) == true || empty($apo_num) == true){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang('site_error_required').' :: params', true , '' , '' , ''
                ), REST_Controller::HTTP_OK
            );

        }else{

            $aPopupInfo = $this->popup_model->get_app_popup_row(array('apo_num' => $apo_num ) );

            if(empty($aPopupInfo) == true){

                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), lang('site_error_required').' :: row', true , '' , '' , ''
                    ), REST_Controller::HTTP_OK
                );

            }else{

                if($type == 'click'){ //이미지 클릭
                    $ret = $this->popup_model->publicUpdate('app_popup_tb',array('apo_click_count' => (int)$aPopupInfo['apo_click_count']+1),array('apo_num' , $apo_num ));
                }else if($type == 'view'){ //노출시
                    $ret = $this->popup_model->publicUpdate('app_popup_tb',array('apo_view_count' => (int)$aPopupInfo['apo_view_count']+1),array('apo_num' , $apo_num ));
                }

                if($ret == true){
                    $this->set_response(
                        result_echo_rest_json(get_status_code("success"), '' , true , '' , '' , ''
                        ), REST_Controller::HTTP_OK
                    );
                }else{
                    $this->set_response(
                        result_echo_rest_json(get_status_code("error"), lang("site_error_default"), true , '' , '' , ''
                        ), REST_Controller::HTTP_OK
                    );
                }

            }

        }

    }

    public function close_pop_view_get()
    {
        total_stat('end_popup');

        $this->set_response(
            result_echo_rest_json(get_status_code("success"), '' , true , '' , '' , ''
            ), REST_Controller::HTTP_OK
        );

    }


    private function clearPopupData($aPopup , $add_f = array()){

        if(empty($aPopup['apo_num']) == true){ //다중배열

            foreach ($aPopup as $k => $r) {

                if(empty($add_f) == false ) {
                    foreach ($add_f as $kk => $vv) $aPopup[$k][$kk] = $vv;
                }

                unset($aPopup[$k]['apo_position']);
                unset($aPopup[$k]['apo_subject']);
                unset($aPopup[$k]['apo_termlimit_yn']);
                unset($aPopup[$k]['apo_termlimit_datetime1']);
                unset($aPopup[$k]['apo_termlimit_datetime2']);
                unset($aPopup[$k]['apo_regdatetime']);
                unset($aPopup[$k]['apo_click_count']);
                unset($aPopup[$k]['apo_close_count']);
                unset($aPopup[$k]['apo_display_yn']);
                unset($aPopup[$k]['apo_view_page']);
                unset($aPopup[$k]['apo_view_target']);
                unset($aPopup[$k]['apo_expire_day']);
                unset($aPopup[$k]['apo_size_type']);
                unset($aPopup[$k]['apo_btn_type']);
                unset($aPopup[$k]['apo_back_close_yn']);
                unset($aPopup[$k]['apo_os_type']);
                unset($aPopup[$k]['apo_display_count']);
                unset($aPopup[$k]['apo_view_count']);

            }

        }else{//단일

            if(empty($add_f) == false ) {
                foreach ($add_f as $kk => $vv) $aPopup[$kk] = $vv;
            }

            unset($aPopup['apo_position']);
            unset($aPopup['apo_subject']);
            unset($aPopup['apo_termlimit_yn']);
            unset($aPopup['apo_termlimit_datetime1']);
            unset($aPopup['apo_termlimit_datetime2']);
            unset($aPopup['apo_regdatetime']);
            unset($aPopup['apo_click_count']);
            unset($aPopup['apo_close_count']);
            unset($aPopup['apo_display_yn']);
            unset($aPopup['apo_view_page']);
            unset($aPopup['apo_view_target']);
            unset($aPopup['apo_expire_day']);
            unset($aPopup['apo_size_type']);
            unset($aPopup['apo_btn_type']);
            unset($aPopup['apo_back_close_yn']);
            unset($aPopup['apo_os_type']);
            unset($aPopup['apo_display_count']);
            unset($aPopup['apo_view_count']);

        }

        return $aPopup;

    }

}

