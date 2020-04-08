<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 네이버 SmartEditor 관련 컨트롤러 (파일업로드)
 */
class Smarteditor extends A_Controller {

    /**
     * 파일업로드
     */
    public function file_upload() {
        $type = $this->uri->segment(3);
        if( $type == "html5" ) {
            $this->_file_upload_html5();
        }
        else {
            $this->_file_upload_html();
        }
    }//end of file_upload()

    /**
     * html5 지원 파일 업로드
     */
    private function _file_upload_html5() {
        $sFileInfo = '';
        $headers = array();

        foreach($_SERVER as $k => $v) {
            if(substr($k, 0, 9) == "HTTP_FILE") {
                $k = substr(strtolower($k), 5);
                $headers[$k] = $v;
            }
        }

        $file = new stdClass;
        $file->name = str_replace("\0", "", rawurldecode($headers['file_name']));
        $file->size = $headers['file_size'];
        $file->content = file_get_contents("php://input");

        $filename_ext = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
        $allow_file = array("jpg", "png", "gif");

        if( !in_array($filename_ext, $allow_file) ) {
            echo "NOTALLOW_" . $file->name;
        }
        else {
            //디렉터리 설정, 생성
            $uploadDirWeb = $this->config->item('smarteditor_file_path_web') . "/" . date("Y") . "/" . date("md") . "/";
            $uploadDir = $this->config->item('smarteditor_file_path') . "/" . date("Y") . "/" . date("md") . "/";
            create_directory($uploadDir);

            $newFileName = create_session_id() . "." . $filename_ext;
            //$newPath = $uploadDir.iconv("utf-8", "cp949", $file->name);
            $newPath = $uploadDir.$newFileName;

            if(file_put_contents($newPath, $file->content)) {
                $sFileInfo .= "&bNewLine=true";
                $sFileInfo .= "&sFileName=".$file->name;
                $sFileInfo .= "&sFileURL=".$uploadDirWeb.$newFileName;
                //$sFileInfo .= "&sFileURL=".$this->config->item('default_http').$uploadDirWeb.$newFileName;
            }


            echo $sFileInfo;
        }
    }//end of _file_upload_html5()

    /**
     * html5 미지원 파일 업로드
     */
    private function _file_upload_html () {
        $url = $_REQUEST["callback"].'?callback_func='.$_REQUEST["callback_func"];
        $bSuccessUpload = is_uploaded_file($_FILES['Filedata']['tmp_name']);

        // SUCCESSFUL
        if($bSuccessUpload) {
            $tmp_name = $_FILES['Filedata']['tmp_name'];
            $name = $_FILES['Filedata']['name'];

            //$filename_ext = strtolower(array_pop(explode('.',$name)));
            $filename_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allow_file = array("jpg", "png", "gif");

            if(!in_array($filename_ext, $allow_file)) {
                $url .= '&errstr='.$name;
            }
            else {
                $uploadDirWeb = $this->config->item('smarteditor_file_path_web') . "/" . date("Y") . "/" . date("md") . "/";
                $uploadDir = $this->config->item('smarteditor_file_path') . "/" . date("Y") . "/" . date("md") . "/";
                create_directory($uploadDir);

                $newFileName = create_session_id().".".$filename_ext;
                //$newPath = $uploadDir.urlencode($_FILES['Filedata']['name']);
                $newPath = $uploadDir.$newFileName;

                @move_uploaded_file($tmp_name, $newPath);

                $url .= "&bNewLine=true";
                //$url .= "&sFileName=".urlencode(urlencode($name));
                $url .= "&sFileName=".$newFileName;
                //$url .= "&sFileURL=".$uploadDirWeb.urlencode(urlencode($name));
                $url .= "&sFileURL=".$uploadDirWeb.$newFileName;
            }
        }
        // FAILED
        else {
            $url .= '&errstr=error';
        }

        header('Location: '. $url);
    }//end of _file_upload_html()

}//end of class File_upload


/*

// smarteditor html5 sample
$sFileInfo = '';
$headers = array();

foreach($_SERVER as $k => $v) {
    if(substr($k, 0, 9) == "HTTP_FILE") {
        $k = substr(strtolower($k), 5);
        $headers[$k] = $v;
    }
}

$file = new stdClass;
$file->name = str_replace("\0", "", rawurldecode($headers['file_name']));
$file->size = $headers['file_size'];
$file->content = file_get_contents("php://input");

$filename_ext = strtolower(array_pop(explode('.',$file->name)));
$allow_file = array("jpg", "png", "bmp", "gif");

if(!in_array($filename_ext, $allow_file)) {
    echo "NOTALLOW_".$file->name;
} else {
    $uploadDir = '../../upload/';
    if(!is_dir($uploadDir)){
        mkdir($uploadDir, 0777);
    }

    $newPath = $uploadDir.iconv("utf-8", "cp949", $file->name);

    if(file_put_contents($newPath, $file->content)) {
        $sFileInfo .= "&bNewLine=true";
        $sFileInfo .= "&sFileName=".$file->name;
        $sFileInfo .= "&sFileURL=upload/".$file->name;
    }

    echo $sFileInfo;
}



// smarteditor sample
// default redirection
$url = $_REQUEST["callback"].'?callback_func='.$_REQUEST["callback_func"];
$bSuccessUpload = is_uploaded_file($_FILES['Filedata']['tmp_name']);

// SUCCESSFUL
if(bSuccessUpload) {
    $tmp_name = $_FILES['Filedata']['tmp_name'];
    $name = $_FILES['Filedata']['name'];

    $filename_ext = strtolower(array_pop(explode('.',$name)));
    $allow_file = array("jpg", "png", "bmp", "gif");

    if(!in_array($filename_ext, $allow_file)) {
        $url .= '&errstr='.$name;
    } else {
        $uploadDir = '../../upload/';
        if(!is_dir($uploadDir)){
            mkdir($uploadDir, 0777);
        }

        $newPath = $uploadDir.urlencode($_FILES['Filedata']['name']);

        @move_uploaded_file($tmp_name, $newPath);

        $url .= "&bNewLine=true";
        $url .= "&sFileName=".urlencode(urlencode($name));
        $url .= "&sFileURL=upload/".urlencode(urlencode($name));
    }
}
// FAILED
else {
    $url .= '&errstr=error';
}

header('Location: '. $url);
*/