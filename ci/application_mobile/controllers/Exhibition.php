<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 기획전
 */
class Exhibition extends M_Controller
{

    public function __construct()
    {
        parent::__construct();

    }//end of __construct()

    public function index()
    {

        $this->load->model('exhibition_model');
        $aExhibition = $this->exhibition_model->get_exhibition_list();

        $this->_header();

        $this->load->view('/exhibition/index', array(
            'aExhibition' => $aExhibition
        ) );

        $this->_footer();

    }//end of index()

    public function exhibition_list()
    {

        $aInput = array(
            'seq' => $this->input->get('seq')
        );

        $this->load->model('exhibition_model');
        $aExhibitionList = $this->exhibition_model->get_exhibition_product_list($aInput);
        $aExhibitionList = array_shift($aExhibitionList);

        $this->_header();

        $this->load->view('/exhibition/list', array(
            'aExhibitionList' => $aExhibitionList
        ) );

        $this->_footer();

    }//end of index()



}//end of class Recently