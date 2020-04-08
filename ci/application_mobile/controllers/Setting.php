<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 환경설정
 */
class Setting extends M_Controller
{

    public function __construct()
    {
        parent::__construct();
        member_login_check();
    }//end of __construct()

    public function index()
    {

        $aMemberInfo = $this->_get_member_info();

        $sql = "SELECT * FROM app_version_tb WHERE av_os_type = 1 ORDER BY av_regdatetime DESC LIMIT 1; ";
        $aVersionInfo = $this->db->query($sql)->row_array();

        $options = array('title' => '환경설정' , 'top_type' => 'back');
        $this->_header($options);

        $this->load->view('/setting/index', array('aMemberInfo' => $aMemberInfo , 'aVersionInfo' => $aVersionInfo) );

        $this->_footer();

    }//end of index()

    public function toggle_push(){

        $aInput = array(
                'm_num' => $_SESSION['session_m_num']
            ,   'flag'  => $this->input->post('f')
        );

        $this->load->model('push_model');

        if( $this->push_model->setUsePush($aInput) == true ){
            result_echo_json(get_status_code('success'), "", true, "alert");
        }else{
            result_echo_json(get_status_code('error'), "", true, "alert");
        }

    }

}//end of class Setting