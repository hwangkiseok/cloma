<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/RestServer/Apidoc.php'; // Excute Api Library

/**
 * Class DocsExec
 * @description : Apidoc 문서화시작 컨트롤러
 */
class DocsExec extends W_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->doc_version = $this->config->item('api_doc_version');
    }

    public function index()
    {
        // 생성권한 체크
        $this->apidoc = new Apidoc();
        $this->apidoc->run(APPPATH . 'controllers/rest', APPPATH . 'views/apidoc/v' . $this->doc_version);
    }

}
