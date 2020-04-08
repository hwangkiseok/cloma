<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 상품 관련 컨트롤러
 */
class Product_desc extends A_Controller {

    public function __construct() {
        parent::__construct();
        show_404();
        exit;
        //model
        $this->load->model('Product_desc_model');
    }//end of __construct()

    /**
     * index
     */
    public function index() {
        $this->product_desc_list();
    }//end of index()

    /**
     * 상품 목록
     */
    public function product_desc_list() {
        $this->_header();
        $this->load->view("/product_desc/product_desc_list");
        $this->_footer();
    }//end of product_list()

    public function ImgUpload(){

        $this->load->library('form_validation');

        $this->form_validation->set_rules('TEXT_FILE1', '이미지 파일', 'required');
        $this->form_validation->set_rules('gubun', '구분', 'required');

        if ($this->form_validation->run() == FALSE)
        {
            result_echo_json(get_status_code('error'),strip_tags($this->form_validation->error_string()), true, 'alert');
        }

        $this->db->trans_begin();

        $aInput = array(    'IMG_NAME'      => $this->input->post('TEXT_FILE1')
                        ,   'GUBUN'         => $this->input->post('gubun')
                        ,   'img_path_web'  => $this->config->item('product_desc_image_path_web')
                        ,   'img_path'      => $this->config->item('product_desc_image_path')
                        ,   'REG_DATE'      => date('Ymd')
        );

        create_directory($aInput['img_path'].'/'.date('Ymd').'/');
        $i = 0;

        foreach ($_FILES as $key => $value) {

            if(!$_FILES[$key]['error']){

                $config['upload_path']          = "{$aInput['img_path']}/{$aInput['REG_DATE']}"; //.'/'.date('Ymd').'/';
                $config['max_size']             = $this->config->item('upload_total_max_size');
                $config['allowed_types']        = 'jpg|jpeg|gif|png|bmp';
                $config['encrypt_name'] 		= true;

                $this->load->library('upload',$config);
                $this->upload->initialize($config);

                if (!$this->upload->do_upload($key)){ // 실패

                    $error =  $this->upload->display_errors();
                    result_echo_json(get_status_code('error'),strip_tags($error), true, 'alert');

                }else{

                    $imgData[$i] 	= $this->upload->data();
                    $aFiles[$i]		= array (	'ORG_NAME'		=> $imgData[$i]['orig_name']
                                            ,	'ENC_NAME'		=> $imgData[$i]['file_name']
                                            ,	'FILE_TYPE'		=> $imgData[$i]['file_type']
                                            ,	'FILE_SIZE'		=> $imgData[$i]['file_size']
                                            ,	'FILE_GUBUN'	=> $aInput['GUBUN']
                                            ,   'URL'           => "{$aInput['img_path_web']}/{$aInput['REG_DATE']}/{$imgData[$i]['file_name']}"
                    );

                    $i++;

                }

            }

        }

        $this->Product_desc_model->img_upsert($aFiles);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            result_echo_json(get_status_code('error'),lang('이미지업로드 중 문제가 발생하였습니다. 다시 시도해주세요 !'), true, 'alert');
        } else {
            $this->db->trans_commit();
            result_echo_json(get_status_code('success'),'이미지 등록완료 !', true, 'alert');
        }

    }

    public function product_desc_list_ajax(){

        ajax_request_check(true);
        $product_list = $this->Product_desc_model->getLists();
        result_echo_json(get_status_code('success'),'', true, 'alert','',$product_list);

    }


    public function img_default(){

        ajax_request_check(true);
        $aInput = array(    'p_desc'        => $this->input->post('p_desc')
                        ,   'default_flag'  => $this->input->post('default_flag')
        );
        $bRet = $this->Product_desc_model->img_default($aInput);
        result_echo_json(get_status_code('success'),'기본설정 변경완료 !'.$bRet, true, 'alert','');

    }

    public function img_check(){

        ajax_request_check(true);
        $aInput = array(    'p_desc'    => $this->input->post('p_desc')
                        ,   'open_flag' => $this->input->post('open_flag')
        );
        $bRet = $this->Product_desc_model->img_chk($aInput);
        result_echo_json(get_status_code('success'),'오픈상태 변경완료 !'.$bRet, true, 'alert','');

    }

    public function img_delete(){

        ajax_request_check(true);
        $aInput = array(    'p_desc'  => $this->input->post('p_desc')
                        ,   'path'    => $this->input->post('path')
        );
        $bRet = $this->Product_desc_model->img_del($aInput['p_desc']);
        if($bRet) file_delete('1',$aInput['path'],DOCROOT);
        result_echo_json(get_status_code('success'),'이미지 삭제 성공 !'.$bRet, true, 'alert','');

    }

}//end of class Product