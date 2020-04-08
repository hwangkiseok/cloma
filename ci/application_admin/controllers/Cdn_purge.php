<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CDN Purge 클래스
 */
class Cdn_purge extends A_Controller {

    var $tid_array = array(
        '' => ''
    );

    public function __construct() {
        parent::__construct();
    }

    /**
     * 입력폼
     */
    public function index() {
        $this->_header();

        $this->load->view("/cdn_purge/cdn_purge_index", array(
            'tid_array' => $this->tid_array,
        ));

        $this->_footer();
    }//end of index()

    /**
     * CDN Purge 실행
     */
    public function cdn_purge_proc() {
        //request
        $req['tid'] = $this->input->post("tid", true);
        $req['urls'] = $this->input->post("urls", true);

        //var_dump($req);

        //var_dump(array_values($this->tid_array));

        $replace_arr = array();
        foreach(array_values($this->tid_array) as $item) {
            $replace_arr[] = "http://" . $item;
            $replace_arr[] = "https://" . $item;
        }

        //print_r($replace_arr);

        $domain = $this->tid_array[$req['tid']];

        //var_dump($domain);

        $urls_arr = explode("\n", trim($req['urls']));
        array_filter($urls_arr);


        $result_arr = array();
        $output = '<table border="1" cellpadding="2" cellspacing="0">';
        foreach($urls_arr as $url) {
            //echo $url . "<br>";

            $url = trim(str_replace($replace_arr, "", trim($url)));

            //echo $url . "<br>";

            //var_dump($url, cdn_purge(trim($url), $req['tid'], $domain));

            $result = cdn_purge($url, $req['tid'], $domain);
            if( $result ) {
                $result_str = "성공";
            }
            else {
                $result_str = "실패";
            }

            $output .= '<tr><td>' . $url . '</td><td>' . $result_str . '</td></tr>';

            $result_arr[$url] = $result_str;
        }//endforeach;

        $output .= '</table>';
        $output .= '<br><br><a href="/cdn_purge" class="btn btn-success">입력페이지 이동</a><br>';

        //echo $output;



        $this->_header();

        $this->load->view("/cdn_purge/cdn_purge_proc", array(
            'req'   => $req,
            'result_arr' => $result_arr
        ));

        $this->_footer();
    }//end of purg_proc()

}//end of class Cdn_purge