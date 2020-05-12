<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'third_party/apidoc/Autoloader.php';

/**
 * Class Apidoc
 * @draft : dhkim 20170717
 * @description : 문서화 시작 라이브러리, 해당 third_party(apidoc)를 호출
 */
class Apidoc
{
    //public $twig;
    //public $config;
    //private $data = array();

    public function __construct()
    {
        session_start();
        Apidoc_Autoloader::register();
        $this->CI = &get_instance();
        $this->title = "옷쟁이들 :: API Docs";
        $this->logo = "<img src='https://www.cloma.co.kr/images/favicon/32_32.png' width='20' />";
    }

    /**
     * CI Apidoc
     *
     * @param string $path_dir => /data/{APP_USER}/ci/application/controllers/rest
     * @param string $output_dir => /data/{APP_USER}/ci/application/views/rest/apidoc/v버전
     */
    public function run($path_dir = '', $output_dir = '')
    {
        $autos = glob($path_dir.'/*.php');
        $classes = array();

        if (!empty($autos) && is_array($autos)) {

            foreach ($autos as $key => $val) {

                $class_name = array_pop(explode('/', $val));
                $class_name = substr($class_name, 0, strrpos($class_name, ".")); // 확장자제거

                if ($class_name == 'Docs') { // Docs Class만 읽게끔
                    $classes[] = $class_name;
                    include_once($val);
                }
            }
        }
        /*
        if (!$output_dir) {
            $output_dir  = $path_dir . '/apidoc';
        }
        */
        // 버전관리
        $view_dir = explode('/', $output_dir, -1);
        $view_dir = implode('/' , $view_dir);
        $version_dir = array();
        $sub_dir = opendir($view_dir);
        while ($entry = readdir($sub_dir)) {
            if($entry != '.' && $entry != '..') {
                if (is_dir($view_dir . '/' . $entry) && glob($view_dir . '/v*')) {
                    $version_dir[$entry] = $entry;
                }
            }
        }
        closedir($sub_dir);
        sort($version_dir);

        $output_file = 'api.html'; // defaults to index.html


        try {
            $builder = new Crada\Apidoc\Builder($classes, $output_dir, $this->title, $output_file, '', $version_dir, $this->logo);
            $builder->generate();

        } catch (Exception $e) {
            echo 'There was an error generating the documentation: ', $e->getMessage();
        }

        echo 'OK';
    }
}