<?php

use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\Rest_Core;

defined('BASEPATH') OR exit('No direct script access allowed');

//To Solve File REST_Controller not found
require APPPATH . 'libraries/RestServer/REST_Controller.php';
require APPPATH . 'libraries/RestServer/Format.php';
require APPPATH . 'libraries/RestServer/Rest_Core.php'; // W_Controller 클래스에서 사용된 메소드이관

/**
 * 상품 관련 컨트롤러
 */
class Common extends REST_Controller
{

    public $core;

    public function __construct()
    {
        parent::__construct();

        $this->core = new Rest_Core(); // Core Class (MyController 코어클래스와 같은역할)

    }

    /**
     * 리퍼러 카운팅
     */
    public function referer_put()
    {

        $ref        = $this->put('ref', true);
        $curr_date  = current_date();

        if( empty($ref) == true ){
            $this->set_response(
                result_echo_rest_json(get_status_code("error"), lang('site_error_empty_data'), true, "", "", ""
                ), REST_Controller::HTTP_OK
            ); // NOT_FOUND (404) being the HTTP response code
        }else{

            $res    = $this->db->field_exists($ref,'ref_tb');
            $tb_res = true;

            if($res == false){
                $sql    = "ALTER TABLE `ref_tb` ADD `{$ref}` INT(9) NOT NULL DEFAULT '0'; ";
                $tb_res = $this->db->query($sql);
            }

            if($tb_res == true){

                $sql    = "SELECT * FROM ref_tb WHERE ref_date = '{$curr_date}'; ";
                $isData = $this->db->query($sql)->num_rows();

                if($isData > 0) $sql = "UPDATE `ref_tb` SET `{$ref}` = `{$ref}` + 1 WHERE ref_date = '{$curr_date}'; ";
                else $sql = "INSERT INTO ref_tb (ref_date,`{$ref}`) VALUES ('{$curr_date}','1'); ";

                $this->db->query($sql);

                $this->set_response(
                    result_echo_rest_json(get_status_code("success"), '', true, "", "", ""
                    ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code

            }else{
                $this->set_response(
                    result_echo_rest_json(get_status_code("error"), lang('site_error_unknown'), true, "", "", ""
                    ), REST_Controller::HTTP_OK
                ); // NOT_FOUND (404) being the HTTP response code
            }

        }//end of if()

    }

}

