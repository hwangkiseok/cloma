<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 기본 정보들
 */
class Common extends M_Controller
{

    public $simple;

    public function __construct()
    {
        parent::__construct();
        $this->simple = $this->input->get('simple');

    }//end of __construct()

    public function Privacy()
    {

        $sql = "SELECT * FROM board_help_tb WHERE bh_division = '4' ";
        $text = $this->db->query($sql)->row_array();

        $options = array("top_type" => "back", "title" => "개인정보취급방침");

        if($this->simple != 'Y') $this->_header($options);

        $this->load->view('/common/privacy', array( 'text' => $text, 'simple' => $this->simple) );

        if($this->simple != 'Y') $this->_footer();

    }//end of Privacy()

    public function TermOfUs()
    {

        $options = array("top_type" => "back", "title" => "이용약관");

        $sql = "SELECT * FROM board_help_tb WHERE bh_division = '3' ";
        $text = $this->db->query($sql)->row_array();

        if($this->simple != 'Y') $this->_header($options);

        $this->load->view('/common/termofus', array( 'text' => $text, 'simple' => $this->simple ) );

        if($this->simple != 'Y') $this->_footer();

    }//end of TermOfUs()

    public function UseEventNoti()
    {

        $options = array("top_type" => "back", "title" => "이벤트/쇼핑정보수신동의");

        $sql = "SELECT * FROM board_help_tb WHERE bh_division = '7' ";
        $text = $this->db->query($sql)->row_array();

        if($this->simple != 'Y') $this->_header($options);

        $this->load->view('/common/use_event_noti', array( 'text' => $text, 'simple' => $this->simple ) );

        if($this->simple != 'Y') $this->_footer();

    }//end of UseEventNoti()


    public function srh_addr()
    {

        $this->load->view('/common/srh_addr');

    }//end of Privacy()

}//end of class Comment