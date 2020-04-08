<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 최근본상품
 */
class Recently extends M_Controller
{

    public function __construct()
    {
        parent::__construct();

    }//end of __construct()

    public function index()
    {

        $aRecentlyProduct = get_recently_product();

        $options = array('title' => '최근 본 상품' , 'top_type' => 'back');

        $this->_header($options);

        $this->load->view('/recently/index', array(
            'aRecentlyProduct' => $aRecentlyProduct
        ) );

        $this->_footer();

    }//end of index()

}//end of class Recently