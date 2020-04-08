<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package CKEditor 컨트롤러
 */
class Ckeditor extends A_Controller
{
    /**
     * 에디터 이미지 업로더
     */
    function uploads()
    {
        header("Content-Type: text/json; charset=utf-8");
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");

        if($_FILES["upload"]["size"] > 0) {

            // 파일이름. 확장자 구하기
            $file_name = pathinfo($_FILES['upload']['name'], PATHINFO_FILENAME);
            $file_ext = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));

            $savefilename = $file_name . "_" . current_mstime() . "." . $file_ext;

            $uploadpath_web = $this->config->item('smarteditor_file_path_web') . "/" . date("Y") . "/" . date("md") . "/"; // Web Url
            $uploadpath_sys = $this->config->item('smarteditor_file_path') . "/" . date("Y") . "/" . date("md") . "/"; // System Directory

            // 해당 디렉토리 생성
            create_directory($uploadpath_sys);

            //php 파일업로드하는 부분
            if($file_ext == "jpg" or $file_ext == "gif" or $file_ext == "png") {
                if(@move_uploaded_file($_FILES['upload']['tmp_name'], $uploadpath_sys . $savefilename)) {
                    $uploadfile = $savefilename;
                }
            } else {
                // error
            }

        } else {
            exit;
        }

        $json_array = array(
            'filename' =>  $uploadfile,
            'uploaded' => 1,
            //'url' => $this->config->item("site_img_http") . $uploadpath_web . $uploadfile
            'url' => $uploadpath_web . $uploadfile
        );

        echo json_encode($json_array);
    }

}