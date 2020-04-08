<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 메인 컨트롤러
 */
class Mypage extends W_Controller
{

    public function __construct()
    {
        parent::__construct();

    }//end of __construct()

    /**
     * 교환/반품
     */
    public function exchange()
    {

        $this->_header();

        $this->load->view('/mypage/exchange', array( ) );

        $this->_footer();

    }//end of index()

}//end of class Main