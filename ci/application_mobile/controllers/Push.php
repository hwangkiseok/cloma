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

        $this->load->model('push_model');

    }//end of __construct()

    public function index()
    {

        $aInput = array(
                'seq'   => $this->input->get('seq')
            ,   'type'  => $this->input->post_get('type')
        );

        $callApp = false;
        //------------------------------ seq 입장통계
        if(empty($aInput['seq']) == false){
            if( !get_cookie('push_page_'.$aInput['seq']) ) {
                $sql = "UPDATE app_push_tb SET ap_view_cnt = ap_view_cnt + 1 WHERE ap_num = {$aInput['seq']}";
                $this->db->query($sql);
                set_cookie('push_page_' . $aInput['seq'], "Y", get_strtotime_diff("+1 days"));
            }
            $callApp = true;
        }

        if($aInput['type'] == 'info'){

            self::info_list();

        }else if($aInput['type'] == 'product'){

            self::product_list();

        } else{

            if($callApp == true){

                self::product_list($callApp);

            }else{

                $sql     = "SELECT * FROM noti_tb WHERE m_num = '{$_SESSION['session_m_num']}' AND view_flag = 'N'  ;";
                $oResult = $this->db->query($sql);
                $nResult = $oResult->num_rows();

                if($nResult > 0) self::info_list();
                else self::product_list();

            }

        }

    }//end of index()

    public function chk_view(){


        $seq = $this->input->post('seq');

        $sql = "SELECT * FROM noti_tb WHERE seq = '{$seq}'; ";
        $oResult = $this->db->query($sql);
        $aResult = $oResult->row_array();

        $ret = true;
        if(empty($aResult) == false){

            $sql = "UPDATE noti_tb SET view_flag = 'Y' WHERE seq = '{$seq}'; ";
            $ret = $this->db->query($sql);

        }
        echo json_encode(array('success' => $ret , 'msg' => ''));

    }

    private function info_list(){

        $aInfoLists = $this->push_model->get_info_list();
        $options = array('title' => '알림메시지' , 'top_type' => 'back');

        if($this->input->is_ajax_request() == true){

            echo json_encode_no_slashes(array('success' => true , 'msg' => '' , 'data' => $aInfoLists));

        }else{

            $this->_header($options);

            $this->load->view('/push/info_list', array(
                'aInfoLists'   => $aInfoLists
                , 'page'        => 'info'
            ) );

            $this->_footer();

        }
    }

    private function product_list($callApp = false){

        $aPushLists = $this->push_model->get_push_list();

        foreach ($aPushLists as $k => $r) {
            $aPushLists[$k]['ap_list_comment_repl'] = nl2br($aPushLists[$k]['ap_list_comment']);
        }

        $options = array('title' => '알림메시지' , 'top_type' => 'back');

        if($this->input->is_ajax_request() == true){

            echo json_encode_no_slashes(array('success' => true , 'msg' => '' , 'data' => $aPushLists));

        }else{

            $this->_header($options);

            $this->load->view('/push/index', array(
                'aPushLists'   => $aPushLists
            ,   'page'        => 'product'
            ,   'callApp'       => $callApp
            ) );

            $this->_footer();
        }

    }



}//end of class Delivery
