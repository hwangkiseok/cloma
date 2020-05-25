<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 알림메시지
 */
class Push extends M_Controller
{

    public function __construct()
    {
        parent::__construct();

    }//end of __construct()

    public function index()
    {
        $aInput = array(
            'seq' => $this->input->get('seq')
        );

        //------------------------------ seq 입장통계
        if(empty($aInput['seq']) == false){
            if( !get_cookie('push_page_'.$aInput['seq']) ) {
                $sql = "UPDATE app_push_tb SET ap_view_cnt = ap_view_cnt + 1 WHERE ap_num = {$aInput['seq']}";
                $this->db->query($sql);
                set_cookie('push_page_' . $aInput['seq'], "Y", get_strtotime_diff("+1 days"));
            }
        }

        $this->load->model('push_model');
        $aPushLists = $this->push_model->get_push_list();

        $options = array('title' => '알림메시지' , 'top_type' => 'back');

        $this->_header($options);

        $this->load->view('/push/index', array(
            'aPushLists'   => $aPushLists
        ) );

        $this->_footer();

    }//end of index()


}//end of class Delivery
