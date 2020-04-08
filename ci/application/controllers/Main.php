<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 메인 컨트롤러
 */
class Main extends W_Controller
{

    public function __construct()
    {
        parent::__construct();

    }//end of __construct()

    /**
     * 메인
     */
    public function index()
    {
        $this->_header();

        $this->load->view('/main/main', array( ) );

        $this->_footer();

    }//end of index()

}//end of class Main