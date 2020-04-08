<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\Rest_Core;

defined('BASEPATH') OR exit('No direct script access allowed');

//To Solve File REST_Controller not found
require APPPATH . 'libraries/RestServer/REST_Controller.php';
require APPPATH . 'libraries/RestServer/Format.php';
require APPPATH . 'libraries/RestServer/Rest_Core.php'; // W_Controller 클래스에서 사용된 메소드이관

/**
 * 앱관련 컨트롤러
 */
class App extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('rest');
        $this->core = new Rest_Core(); // Core Class (MyController 코어클래스와 같은역할)

    }//end of __construct()

    public function splash_get()
    {

        $sql            = " SELECT 
                                aps_image
                            ,   aps_termlimit1
                            ,   aps_termlimit2
                            ,   CONCAT('#',aps_bg_color) AS aps_bg_color
                            FROM app_splash_tb 
                            WHERE aps_termlimit1 <= DATE_FORMAT(NOW(), '%Y%m%d%H%i%s') 
                            AND aps_termlimit2 >= DATE_FORMAT(NOW(), '%Y%m%d%H%i%s')
                            AND aps_state = '1'
                            AND aps_usestate = 'Y' 
                            ORDER BY aps_regdatetime DESC 
                            LIMIT 1 ; 
        ";
        $oResult        = $this->db->query($sql);
        $aSplashInfo    = $oResult->row_array();

        if(empty($aSplashInfo) == true){

            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang('site_error_empty_data'), true , '' , '' , ''
                ), REST_Controller::HTTP_OK
            );

        }else{

            $this->set_response(
                result_echo_rest_json(get_status_code("success"), "", true , '' , '' , array(
                    'aSplashInfo'       => $aSplashInfo
                )), REST_Controller::HTTP_OK
            );

        }

    }//end of splash_get()

}//end of class App

